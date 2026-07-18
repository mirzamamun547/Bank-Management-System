<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. BRANCHES table
        Schema::create('branches', function (Blueprint $table) {
            $table->id('branch_id');
            $table->string('branch_name', 100)->unique();
            $table->string('location', 255);
            $table->string('manager_employee_id', 20)->nullable();
            $table->string('status', 20)->default('ACTIVE');
            $table->timestamps();
        });

        // 2. SYSTEM_SETTINGS table
        Schema::create('system_settings', function (Blueprint $table) {
            $table->string('settings_key', 50)->primary();
            $table->string('settings_value', 100);
            $table->timestamps();
        });

        // Default settings
        DB::table('system_settings')->insert([
            ['settings_key' => 'INTEREST_RATE',   'settings_value' => '5.0',  'created_at' => now(), 'updated_at' => now()],
            ['settings_key' => 'LOAN_INTEREST',   'settings_value' => '8.5',  'created_at' => now(), 'updated_at' => now()],
            ['settings_key' => 'OTP_EXPIRY',      'settings_value' => '10',   'created_at' => now(), 'updated_at' => now()],
            ['settings_key' => 'MINIMUM_BALANCE', 'settings_value' => '500',  'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Add branch_id FK columns
        Schema::table('USERS', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable();
        });
        Schema::table('accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable();
        });

        // ─────────────────────────────────────────────────────────────
        // 4. SEQUENCES
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("CREATE SEQUENCE SEQ_BRANCH     START WITH 1 INCREMENT BY 1 NOCACHE");
        DB::unprepared("CREATE SEQUENCE SEQ_AUDIT      START WITH 1 INCREMENT BY 1 NOCACHE");
        DB::unprepared("CREATE SEQUENCE SEQ_NOTIFICATION START WITH 1 INCREMENT BY 1 NOCACHE");

        // ─────────────────────────────────────────────────────────────
        // 5. VIEWS
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("
            CREATE OR REPLACE VIEW ADMIN_DASHBOARD_VIEW AS
            SELECT
                (SELECT COUNT(*) FROM USERS WHERE UPPER(role) = 'CUSTOMER')  AS total_customers,
                (SELECT COUNT(*) FROM USERS WHERE UPPER(role) = 'EMPLOYEE')  AS total_employees,
                (SELECT COUNT(*) FROM branches)                               AS total_branches,
                (SELECT COUNT(*) FROM accounts)                               AS total_accounts,
                (SELECT COUNT(*) FROM loans)                                  AS total_loans,
                (SELECT NVL(SUM(balance), 0) FROM accounts)                  AS total_deposits,
                (SELECT COUNT(*) FROM transactions)                           AS total_transactions
            FROM DUAL
        ");

        DB::unprepared("
            CREATE OR REPLACE VIEW ACTIVE_ACCOUNTS_VIEW AS
            SELECT * FROM accounts WHERE UPPER(status) = 'ACTIVE'
        ");

        DB::unprepared("
            CREATE OR REPLACE VIEW BRANCH_SUMMARY_VIEW AS
            SELECT
                b.branch_id,
                b.branch_name,
                COUNT(a.id)          AS total_accounts,
                NVL(SUM(a.balance), 0) AS total_balance
            FROM branches b
            LEFT JOIN accounts a ON a.branch_id = b.branch_id
            GROUP BY b.branch_id, b.branch_name
        ");

        DB::unprepared("
            CREATE OR REPLACE VIEW MONTHLY_TRANSACTION_VIEW AS
            SELECT
                TO_CHAR(created_at, 'YYYY-MM') AS month,
                transaction_type,
                SUM(amount)  AS total_amount,
                COUNT(*)     AS total_count
            FROM transactions
            GROUP BY TO_CHAR(created_at, 'YYYY-MM'), transaction_type
        ");

        // ─────────────────────────────────────────────────────────────
        // 6. TRIGGERS
        // ─────────────────────────────────────────────────────────────

        // Prevent negative / below-minimum balance
        DB::unprepared("
            CREATE OR REPLACE TRIGGER TRG_PREVENT_NEG_BALANCE
            BEFORE UPDATE OF balance ON accounts
            FOR EACH ROW
            DECLARE
                v_min NUMBER;
            BEGIN
                BEGIN
                    SELECT TO_NUMBER(settings_value) INTO v_min
                    FROM system_settings WHERE settings_key = 'MINIMUM_BALANCE';
                EXCEPTION
                    WHEN NO_DATA_FOUND THEN v_min := 500;
                END;
                IF :NEW.balance < v_min THEN
                    RAISE_APPLICATION_ERROR(-20005,
                        'Balance cannot drop below minimum required balance of ' || v_min);
                END IF;
            END;
        ");

        // Audit trigger – logs every INSERT / UPDATE / DELETE on USERS
        DB::unprepared("
            CREATE OR REPLACE TRIGGER TRG_USERS_AUDIT
            AFTER INSERT OR UPDATE OR DELETE ON USERS
            FOR EACH ROW
            DECLARE
                v_action  VARCHAR2(50);
                v_details VARCHAR2(4000);
            BEGIN
                IF INSERTING THEN
                    v_action  := 'INSERT';
                    v_details := 'User created: ' || :NEW.customer_id || ', Role: ' || :NEW.role;
                ELSIF UPDATING THEN
                    v_action  := 'UPDATE';
                    v_details := 'User ' || :OLD.customer_id
                                 || ' status changed from ' || :OLD.status
                                 || ' to ' || :NEW.status;
                ELSIF DELETING THEN
                    v_action  := 'DELETE';
                    v_details := 'User deleted: ' || :OLD.customer_id;
                END IF;

                INSERT INTO audit_log (table_name, action, performed_by, details, created_at, updated_at)
                VALUES ('USERS', v_action, 'SYSTEM', v_details, SYSDATE, SYSDATE);
            END;
        ");

        // ─────────────────────────────────────────────────────────────
        // 7. STANDALONE FUNCTIONS
        // ─────────────────────────────────────────────────────────────

        DB::unprepared("
            CREATE OR REPLACE FUNCTION GET_TOTAL_DEPOSITS
            RETURN NUMBER IS
                v_sum NUMBER;
            BEGIN
                SELECT NVL(SUM(balance), 0) INTO v_sum FROM accounts;
                RETURN v_sum;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE FUNCTION GET_TOTAL_CUSTOMERS
            RETURN NUMBER IS
                v_count NUMBER;
            BEGIN
                SELECT COUNT(*) INTO v_count FROM USERS WHERE UPPER(role) = 'CUSTOMER';
                RETURN v_count;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE FUNCTION GET_BRANCH_BALANCE (p_branch_id IN NUMBER)
            RETURN NUMBER IS
                v_sum NUMBER;
            BEGIN
                SELECT NVL(SUM(balance), 0) INTO v_sum
                FROM accounts WHERE branch_id = p_branch_id;
                RETURN v_sum;
            END;
        ");

        // ─────────────────────────────────────────────────────────────
        // 8. STANDALONE PROCEDURES
        // ─────────────────────────────────────────────────────────────

        // Employee management
        DB::unprepared("
            CREATE OR REPLACE PROCEDURE ADD_EMPLOYEE (
                p_first_name  IN VARCHAR2,
                p_last_name   IN VARCHAR2,
                p_email       IN VARCHAR2,
                p_phone       IN VARCHAR2,
                p_address     IN VARCHAR2,
                p_nid         IN VARCHAR2,
                p_dob         IN DATE,
                p_password    IN VARCHAR2,
                p_branch_id   IN NUMBER
            ) AS
                v_customer_id VARCHAR2(20);
            BEGIN
                v_customer_id := 'EMP' || CUSTOMER_ID_SEQ.NEXTVAL;
                INSERT INTO USERS (
                    customer_id, first_name, last_name, email, phone,
                    address, dob, nid, password, role, status, branch_id
                ) VALUES (
                    v_customer_id, p_first_name, p_last_name, p_email, p_phone,
                    p_address, p_dob, p_nid, p_password, 'EMPLOYEE', 'ACTIVE', p_branch_id
                );
            EXCEPTION
                WHEN DUP_VAL_ON_INDEX THEN
                    RAISE_APPLICATION_ERROR(-20001, 'Duplicate email or employee ID.');
                WHEN OTHERS THEN
                    RAISE_APPLICATION_ERROR(-20002, 'Error adding employee: ' || SQLERRM);
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE PROCEDURE UPDATE_EMPLOYEE (
                p_emp_id    IN NUMBER,
                p_first_name IN VARCHAR2,
                p_last_name  IN VARCHAR2,
                p_email      IN VARCHAR2,
                p_phone      IN VARCHAR2,
                p_address    IN VARCHAR2,
                p_status     IN VARCHAR2
            ) AS
            BEGIN
                UPDATE USERS SET
                    first_name  = p_first_name,
                    last_name   = p_last_name,
                    email       = p_email,
                    phone       = p_phone,
                    address     = p_address,
                    status      = p_status,
                    updated_at  = CURRENT_TIMESTAMP
                WHERE id = p_emp_id AND UPPER(role) = 'EMPLOYEE';

                IF SQL%ROWCOUNT = 0 THEN
                    RAISE_APPLICATION_ERROR(-20003, 'Employee not found.');
                END IF;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE PROCEDURE DELETE_EMPLOYEE (
                p_emp_id IN NUMBER
            ) AS
            BEGIN
                DELETE FROM USERS WHERE id = p_emp_id AND UPPER(role) = 'EMPLOYEE';
                IF SQL%ROWCOUNT = 0 THEN
                    RAISE_APPLICATION_ERROR(-20004, 'Employee not found.');
                END IF;
            END;
        ");

        // Branch management
        DB::unprepared("
            CREATE OR REPLACE PROCEDURE ADD_BRANCH (
                p_branch_name         IN VARCHAR2,
                p_location            IN VARCHAR2,
                p_manager_employee_id IN VARCHAR2
            ) AS
            BEGIN
                INSERT INTO branches (
                    branch_id, branch_name, location,
                    manager_employee_id, status, created_at, updated_at
                ) VALUES (
                    SEQ_BRANCH.NEXTVAL, p_branch_name, p_location,
                    p_manager_employee_id, 'ACTIVE', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                );
            EXCEPTION
                WHEN DUP_VAL_ON_INDEX THEN
                    RAISE_APPLICATION_ERROR(-20020, 'Branch name already exists.');
                WHEN OTHERS THEN
                    RAISE_APPLICATION_ERROR(-20021, 'Error adding branch: ' || SQLERRM);
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE PROCEDURE UPDATE_BRANCH (
                p_branch_id           IN NUMBER,
                p_branch_name         IN VARCHAR2,
                p_location            IN VARCHAR2,
                p_manager_employee_id IN VARCHAR2,
                p_status              IN VARCHAR2
            ) AS
            BEGIN
                UPDATE branches SET
                    branch_name         = p_branch_name,
                    location            = p_location,
                    manager_employee_id = p_manager_employee_id,
                    status              = p_status,
                    updated_at          = CURRENT_TIMESTAMP
                WHERE branch_id = p_branch_id;

                IF SQL%ROWCOUNT = 0 THEN
                    RAISE_APPLICATION_ERROR(-20022, 'Branch not found.');
                END IF;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE PROCEDURE DELETE_BRANCH (
                p_branch_id IN NUMBER
            ) AS
            BEGIN
                DELETE FROM branches WHERE branch_id = p_branch_id;
                IF SQL%ROWCOUNT = 0 THEN
                    RAISE_APPLICATION_ERROR(-20023, 'Branch not found.');
                END IF;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE PROCEDURE ASSIGN_MANAGER (
                p_branch_id           IN NUMBER,
                p_manager_employee_id IN VARCHAR2
            ) AS
            BEGIN
                UPDATE branches SET
                    manager_employee_id = p_manager_employee_id,
                    updated_at          = CURRENT_TIMESTAMP
                WHERE branch_id = p_branch_id;

                IF SQL%ROWCOUNT = 0 THEN
                    RAISE_APPLICATION_ERROR(-20024, 'Branch not found.');
                END IF;
            END;
        ");

        // Loan management – with SAVEPOINT, CURSOR, Exception handling
        DB::unprepared("
            CREATE OR REPLACE PROCEDURE APPROVE_LOAN (
                p_loan_id IN NUMBER
            ) AS
                v_user_id    NUMBER;
                v_amount     NUMBER;
                v_loan_type  VARCHAR2(50);
                v_account_id NUMBER;

                -- Cursor: loop through pending notifications for this user
                CURSOR c_pending_loans (p_uid NUMBER) IS
                    SELECT id FROM loans
                    WHERE user_id = p_uid AND UPPER(status) = 'PENDING';
            BEGIN
                SAVEPOINT before_approval;

                -- Fetch loan details
                SELECT user_id, amount, loan_type
                INTO   v_user_id, v_amount, v_loan_type
                FROM   loans
                WHERE  id = p_loan_id AND UPPER(status) = 'PENDING';

                -- Approve the loan
                UPDATE loans SET
                    status     = 'Active',
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = p_loan_id;

                -- Disburse to active account
                SELECT id INTO v_account_id
                FROM   accounts
                WHERE  user_id = v_user_id
                  AND  UPPER(status) = 'ACTIVE'
                  AND  ROWNUM = 1;

                UPDATE accounts SET
                    balance    = balance + v_amount,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = v_account_id;

                -- Record transaction
                INSERT INTO transactions (
                    account_id, transaction_type, amount,
                    reference, description, created_at, updated_at
                ) VALUES (
                    v_account_id, 'LOAN_DISBURSEMENT', v_amount,
                    'LN-' || p_loan_id,
                    'Loan approved: ' || v_loan_type,
                    CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                );

                -- Notify customer
                INSERT INTO notifications (user_id, message, is_read, created_at, updated_at)
                VALUES (
                    v_user_id,
                    'Your ' || v_loan_type || ' loan of $' || v_amount || ' has been approved.',
                    0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                );

            EXCEPTION
                WHEN NO_DATA_FOUND THEN
                    ROLLBACK TO before_approval;
                    RAISE_APPLICATION_ERROR(-20010,
                        'Pending loan or active account not found.');
                WHEN OTHERS THEN
                    ROLLBACK TO before_approval;
                    RAISE_APPLICATION_ERROR(-20011,
                        'Loan approval failed: ' || SQLERRM);
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE PROCEDURE REJECT_LOAN (
                p_loan_id IN NUMBER
            ) AS
                v_user_id   NUMBER;
                v_loan_type VARCHAR2(50);
            BEGIN
                SELECT user_id, loan_type
                INTO   v_user_id, v_loan_type
                FROM   loans
                WHERE  id = p_loan_id AND UPPER(status) = 'PENDING';

                UPDATE loans SET
                    status     = 'Rejected',
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = p_loan_id;

                -- Notify customer of rejection
                INSERT INTO notifications (user_id, message, is_read, created_at, updated_at)
                VALUES (
                    v_user_id,
                    'Your ' || v_loan_type || ' loan application has been rejected.',
                    0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                );

            EXCEPTION
                WHEN NO_DATA_FOUND THEN
                    RAISE_APPLICATION_ERROR(-20012, 'Pending loan not found.');
                WHEN OTHERS THEN
                    RAISE_APPLICATION_ERROR(-20013, 'Loan rejection failed: ' || SQLERRM);
            END;
        ");

        // Customer management
        DB::unprepared("
            CREATE OR REPLACE PROCEDURE UPDATE_CUSTOMER (
                p_customer_id IN VARCHAR2,
                p_status      IN VARCHAR2
            ) AS
            BEGIN
                UPDATE USERS SET
                    status     = p_status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE customer_id = p_customer_id AND UPPER(role) = 'CUSTOMER';

                IF SQL%ROWCOUNT = 0 THEN
                    RAISE_APPLICATION_ERROR(-20030, 'Customer not found.');
                END IF;
            END;
        ");

        DB::unprepared("
            CREATE OR REPLACE PROCEDURE DELETE_CUSTOMER (
                p_customer_id IN VARCHAR2
            ) AS
            BEGIN
                DELETE FROM USERS
                WHERE customer_id = p_customer_id AND UPPER(role) = 'CUSTOMER';

                IF SQL%ROWCOUNT = 0 THEN
                    RAISE_APPLICATION_ERROR(-20031, 'Customer not found.');
                END IF;
            END;
        ");
    }

    public function down(): void
    {
        // Drop standalone procedures
        foreach ([
            'ADD_EMPLOYEE', 'UPDATE_EMPLOYEE', 'DELETE_EMPLOYEE',
            'ADD_BRANCH',   'UPDATE_BRANCH',   'DELETE_BRANCH', 'ASSIGN_MANAGER',
            'APPROVE_LOAN', 'REJECT_LOAN',
            'UPDATE_CUSTOMER', 'DELETE_CUSTOMER',
        ] as $proc) {
            try { DB::unprepared("DROP PROCEDURE {$proc}"); } catch (\Exception $e) {}
        }

        // Drop standalone functions
        foreach (['GET_TOTAL_DEPOSITS', 'GET_TOTAL_CUSTOMERS', 'GET_BRANCH_BALANCE'] as $fn) {
            try { DB::unprepared("DROP FUNCTION {$fn}"); } catch (\Exception $e) {}
        }

        // Drop triggers
        foreach (['TRG_USERS_AUDIT', 'TRG_PREVENT_NEG_BALANCE'] as $trg) {
            try { DB::unprepared("DROP TRIGGER {$trg}"); } catch (\Exception $e) {}
        }

        // Drop views
        foreach ([
            'MONTHLY_TRANSACTION_VIEW', 'BRANCH_SUMMARY_VIEW',
            'ACTIVE_ACCOUNTS_VIEW',     'ADMIN_DASHBOARD_VIEW',
        ] as $view) {
            try { DB::unprepared("DROP VIEW {$view}"); } catch (\Exception $e) {}
        }

        // Drop sequences
        foreach (['SEQ_BRANCH', 'SEQ_AUDIT', 'SEQ_NOTIFICATION'] as $seq) {
            try { DB::unprepared("DROP SEQUENCE {$seq}"); } catch (\Exception $e) {}
        }

        Schema::table('accounts', fn (Blueprint $t) => $t->dropColumn('branch_id'));
        Schema::table('USERS',    fn (Blueprint $t) => $t->dropColumn('branch_id'));

        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('branches');
    }
};
