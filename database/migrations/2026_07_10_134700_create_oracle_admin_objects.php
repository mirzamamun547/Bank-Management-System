<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create BRANCHES table
        Schema::create('branches', function (Blueprint $table) {
            $table->id('branch_id'); // maps to Oracle NUMBER
            $table->string('branch_name', 100)->unique();
            $table->string('location', 255);
            $table->string('manager_employee_id', 20)->nullable();
            $table->string('status', 20)->default('ACTIVE');
            $table->timestamps();
        });

        // 2. Create SYSTEM_SETTINGS table
        Schema::create('system_settings', function (Blueprint $table) {
            $table->string('settings_key', 50)->primary();
            $table->string('settings_value', 100);
            $table->timestamps();
        });

        // Seed default settings
        DB::table('system_settings')->insert([
            ['settings_key' => 'INTEREST_RATE', 'settings_value' => '5.0', 'created_at' => now(), 'updated_at' => now()],
            ['settings_key' => 'LOAN_INTEREST', 'settings_value' => '8.5', 'created_at' => now(), 'updated_at' => now()],
            ['settings_key' => 'OTP_EXPIRY', 'settings_value' => '10', 'created_at' => now(), 'updated_at' => now()],
            ['settings_key' => 'MINIMUM_BALANCE', 'settings_value' => '500', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Add branch_id to USERS and ACCOUNTS tables
        Schema::table('USERS', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable();
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable();
        });

        // 4. Create Sequence for Branches
        DB::unprepared("
            CREATE SEQUENCE SEQ_BRANCH 
            START WITH 1 
            INCREMENT BY 1 
            NOCACHE
        ");

        // 5. Create Oracle Views
        DB::unprepared("
            CREATE OR REPLACE VIEW ADMIN_DASHBOARD_VIEW AS
            SELECT
                (SELECT COUNT(*) FROM USERS WHERE role = 'CUSTOMER') as total_customers,
                (SELECT COUNT(*) FROM USERS WHERE role = 'EMPLOYEE') as total_employees,
                (SELECT COUNT(*) FROM branches) as total_branches,
                (SELECT COUNT(*) FROM accounts) as total_accounts,
                (SELECT COUNT(*) FROM loans) as total_loans,
                (SELECT NVL(SUM(balance), 0) FROM accounts) as total_deposits,
                (SELECT COUNT(*) FROM transactions) as total_transactions
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
                COUNT(a.id) as total_accounts,
                NVL(SUM(a.balance), 0) as total_balance
            FROM branches b
            LEFT JOIN accounts a ON a.branch_id = b.branch_id
            GROUP BY b.branch_id, b.branch_name
        ");

        DB::unprepared("
            CREATE OR REPLACE VIEW MONTHLY_TRANSACTION_VIEW AS
            SELECT 
                TO_CHAR(created_at, 'YYYY-MM') as month,
                transaction_type,
                SUM(amount) as total_amount,
                COUNT(*) as total_count
            FROM transactions
            GROUP BY TO_CHAR(created_at, 'YYYY-MM'), transaction_type
        ");

        // 6. Create Prevent Negative Balance Trigger
        DB::unprepared("
            CREATE OR REPLACE TRIGGER TRG_PREVENT_NEG_BALANCE
            BEFORE UPDATE OF balance ON accounts
            FOR EACH ROW
            DECLARE
                v_min_balance NUMBER;
            BEGIN
                BEGIN
                    SELECT TO_NUMBER(settings_value) INTO v_min_balance 
                    FROM system_settings 
                    WHERE settings_key = 'MINIMUM_BALANCE';
                EXCEPTION
                    WHEN NO_DATA_FOUND THEN
                        v_min_balance := 500;
                END;
                IF :NEW.balance < v_min_balance THEN
                    raise_application_error(-20005, 'Balance cannot drop below the minimum required balance of ' || v_min_balance);
                END IF;
            END;
        ");

        // 7. Create Users Audit Trigger
        DB::unprepared("
            CREATE OR REPLACE TRIGGER TRG_USERS_AUDIT
            AFTER INSERT OR UPDATE OR DELETE ON USERS
            FOR EACH ROW
            DECLARE
                v_action VARCHAR2(50);
                v_details VARCHAR2(4000);
                v_performed_by VARCHAR2(100) := 'SYSTEM';
            BEGIN
                IF INSERTING THEN
                    v_action := 'INSERT';
                    v_details := 'User created with CUSTOMER_ID: ' || :NEW.customer_id || ', Role: ' || :NEW.role;
                ELSIF UPDATING THEN
                    v_action := 'UPDATE';
                    v_details := 'User CUSTOMER_ID: ' || :OLD.customer_id || ' updated. Status changed from ' || :OLD.status || ' to ' || :NEW.status;
                ELSIF DELETING THEN
                    v_action := 'DELETE';
                    v_details := 'User deleted with CUSTOMER_ID: ' || :OLD.customer_id;
                END IF;

                INSERT INTO audit_log (
                    table_name, action, performed_by, details, created_at, updated_at
                ) VALUES (
                    'USERS', v_action, v_performed_by, v_details, SYSDATE, SYSDATE
                );
            END;
        ");

        // 8. Create Unified Package Spec
        DB::unprepared("
            CREATE OR REPLACE PACKAGE BANK_ADMIN_PKG AS
                PROCEDURE ADD_EMPLOYEE(
                    p_first_name IN VARCHAR2,
                    p_last_name IN VARCHAR2,
                    p_email IN VARCHAR2,
                    p_phone IN VARCHAR2,
                    p_address IN VARCHAR2,
                    p_nid IN VARCHAR2,
                    p_dob IN DATE,
                    p_password IN VARCHAR2,
                    p_branch_id IN NUMBER
                );
                PROCEDURE UPDATE_EMPLOYEE(
                    p_emp_id IN NUMBER,
                    p_first_name IN VARCHAR2,
                    p_last_name IN VARCHAR2,
                    p_email IN VARCHAR2,
                    p_phone IN VARCHAR2,
                    p_address IN VARCHAR2,
                    p_status IN VARCHAR2
                );
                PROCEDURE DELETE_EMPLOYEE(
                    p_emp_id IN NUMBER
                );
                PROCEDURE ADD_BRANCH(
                    p_branch_name IN VARCHAR2,
                    p_location IN VARCHAR2,
                    p_manager_employee_id IN VARCHAR2
                );
                PROCEDURE UPDATE_BRANCH(
                    p_branch_id IN NUMBER,
                    p_branch_name IN VARCHAR2,
                    p_location IN VARCHAR2,
                    p_manager_employee_id IN VARCHAR2,
                    p_status IN VARCHAR2
                );
                PROCEDURE DELETE_BRANCH(
                    p_branch_id IN NUMBER
                );
                PROCEDURE APPROVE_LOAN(
                    p_loan_id IN NUMBER
                );
                PROCEDURE REJECT_LOAN(
                    p_loan_id IN NUMBER
                );
                FUNCTION GET_TOTAL_DEPOSITS RETURN NUMBER;
                FUNCTION GET_TOTAL_CUSTOMERS RETURN NUMBER;
                FUNCTION GET_BRANCH_BALANCE(p_branch_id IN NUMBER) RETURN NUMBER;
            END BANK_ADMIN_PKG;
        ");

        // 9. Create Unified Package Body
        DB::unprepared("
            CREATE OR REPLACE PACKAGE BODY BANK_ADMIN_PKG AS
                PROCEDURE ADD_EMPLOYEE(
                    p_first_name IN VARCHAR2,
                    p_last_name IN VARCHAR2,
                    p_email IN VARCHAR2,
                    p_phone IN VARCHAR2,
                    p_address IN VARCHAR2,
                    p_nid IN VARCHAR2,
                    p_dob IN DATE,
                    p_password IN VARCHAR2,
                    p_branch_id IN NUMBER
                ) AS
                    v_customer_id VARCHAR2(20);
                BEGIN
                    v_customer_id := 'EMP' || CUSTOMER_ID_SEQ.NEXTVAL;
                    INSERT INTO USERS (
                        customer_id, first_name, last_name, email, phone, address, dob, nid, password, role, status, branch_id
                    ) VALUES (
                        v_customer_id, p_first_name, p_last_name, p_email, p_phone, p_address, p_dob, p_nid, p_password, 'EMPLOYEE', 'ACTIVE', p_branch_id
                    );
                EXCEPTION
                    WHEN DUP_VAL_ON_INDEX THEN
                        raise_application_error(-20001, 'Duplicate email or employee ID.');
                    WHEN OTHERS THEN
                        raise_application_error(-20002, 'An error occurred while adding the employee: ' || SQLERRM);
                END ADD_EMPLOYEE;

                PROCEDURE UPDATE_EMPLOYEE(
                    p_emp_id IN NUMBER,
                    p_first_name IN VARCHAR2,
                    p_last_name IN VARCHAR2,
                    p_email IN VARCHAR2,
                    p_phone IN VARCHAR2,
                    p_address IN VARCHAR2,
                    p_status IN VARCHAR2
                ) AS
                BEGIN
                    UPDATE USERS SET
                        first_name = p_first_name,
                        last_name = p_last_name,
                        email = p_email,
                        phone = p_phone,
                        address = p_address,
                        status = p_status,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = p_emp_id AND role = 'EMPLOYEE';
                    IF SQL%ROWCOUNT = 0 THEN
                        raise_application_error(-20003, 'Employee not found.');
                    END IF;
                END UPDATE_EMPLOYEE;

                PROCEDURE DELETE_EMPLOYEE(
                    p_emp_id IN NUMBER
                ) AS
                BEGIN
                    DELETE FROM USERS WHERE id = p_emp_id AND role = 'EMPLOYEE';
                    IF SQL%ROWCOUNT = 0 THEN
                        raise_application_error(-20004, 'Employee not found.');
                    END IF;
                END DELETE_EMPLOYEE;

                PROCEDURE ADD_BRANCH(
                    p_branch_name IN VARCHAR2,
                    p_location IN VARCHAR2,
                    p_manager_employee_id IN VARCHAR2
                ) AS
                BEGIN
                    INSERT INTO branches (
                        branch_id, branch_name, location, manager_employee_id, status, created_at, updated_at
                    ) VALUES (
                        SEQ_BRANCH.NEXTVAL, p_branch_name, p_location, p_manager_employee_id, 'ACTIVE', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                    );
                END ADD_BRANCH;

                PROCEDURE UPDATE_BRANCH(
                    p_branch_id IN NUMBER,
                    p_branch_name IN VARCHAR2,
                    p_location IN VARCHAR2,
                    p_manager_employee_id IN VARCHAR2,
                    p_status IN VARCHAR2
                ) AS
                BEGIN
                    UPDATE branches SET
                        branch_name = p_branch_name,
                        location = p_location,
                        manager_employee_id = p_manager_employee_id,
                        status = p_status,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE branch_id = p_branch_id;
                END UPDATE_BRANCH;

                PROCEDURE DELETE_BRANCH(
                    p_branch_id IN NUMBER
                ) AS
                BEGIN
                    DELETE FROM branches WHERE branch_id = p_branch_id;
                END DELETE_BRANCH;

                PROCEDURE APPROVE_LOAN(
                    p_loan_id IN NUMBER
                ) AS
                    v_user_id NUMBER;
                    v_amount NUMBER;
                    v_duration NUMBER;
                    v_loan_type VARCHAR2(50);
                    v_account_id NUMBER;
                BEGIN
                    SAVEPOINT before_approval;
                    
                    SELECT user_id, amount, duration_months, loan_type 
                    INTO v_user_id, v_amount, v_duration, v_loan_type
                    FROM loans 
                    WHERE id = p_loan_id AND status = 'Pending';
                    
                    UPDATE loans SET 
                        status = 'Approved',
                        updated_at = CURRENT_TIMESTAMP 
                    WHERE id = p_loan_id;
                    
                    SELECT id INTO v_account_id 
                    FROM accounts 
                    WHERE user_id = v_user_id AND status = 'Active' AND ROWNUM = 1;
                    
                    UPDATE accounts SET 
                        balance = balance + v_amount,
                        updated_at = CURRENT_TIMESTAMP 
                    WHERE id = v_account_id;
                    
                    INSERT INTO transactions (
                        account_id, transaction_type, amount, reference, description, created_at, updated_at
                    ) VALUES (
                        v_account_id, 'LOAN_DISBURSEMENT', v_amount, 'LN-' || p_loan_id, 'Loan approved and disbursed: ' || v_loan_type, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                    );
                    
                    INSERT INTO notifications (
                        user_id, message, is_read, created_at, updated_at
                    ) VALUES (
                        v_user_id, 'Your loan of amount ' || v_amount || ' for ' || v_loan_type || ' has been approved.', 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                    );
                    
                EXCEPTION
                    WHEN NO_DATA_FOUND THEN
                        ROLLBACK TO before_approval;
                        raise_application_error(-20010, 'Pending loan application or active account not found.');
                    WHEN OTHERS THEN
                        ROLLBACK TO before_approval;
                        raise_application_error(-20011, 'Failed to approve loan: ' || SQLERRM);
                END APPROVE_LOAN;

                PROCEDURE REJECT_LOAN(
                    p_loan_id IN NUMBER
                ) AS
                BEGIN
                    UPDATE loans SET
                        status = 'Rejected',
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = p_loan_id AND status = 'Pending';
                    
                    IF SQL%ROWCOUNT = 0 THEN
                        raise_application_error(-20012, 'Pending loan application not found.');
                    END IF;
                END REJECT_LOAN;

                FUNCTION GET_TOTAL_DEPOSITS RETURN NUMBER AS
                    v_sum NUMBER;
                BEGIN
                    SELECT SUM(balance) INTO v_sum FROM accounts;
                    RETURN NVL(v_sum, 0);
                END GET_TOTAL_DEPOSITS;

                FUNCTION GET_TOTAL_CUSTOMERS RETURN NUMBER AS
                    v_count NUMBER;
                BEGIN
                    SELECT COUNT(*) INTO v_count FROM USERS WHERE role = 'CUSTOMER';
                    RETURN v_count;
                END GET_TOTAL_CUSTOMERS;

                FUNCTION GET_BRANCH_BALANCE(p_branch_id IN NUMBER) RETURN NUMBER AS
                    v_sum NUMBER;
                BEGIN
                    SELECT SUM(balance) INTO v_sum FROM accounts WHERE branch_id = p_branch_id;
                    RETURN NVL(v_sum, 0);
                END GET_BRANCH_BALANCE;
            END BANK_ADMIN_PKG;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PACKAGE BODY BANK_ADMIN_PKG");
        DB::unprepared("DROP PACKAGE BANK_ADMIN_PKG");
        DB::unprepared("DROP TRIGGER TRG_USERS_AUDIT");
        DB::unprepared("DROP TRIGGER TRG_PREVENT_NEG_BALANCE");
        DB::unprepared("DROP VIEW MONTHLY_TRANSACTION_VIEW");
        DB::unprepared("DROP VIEW BRANCH_SUMMARY_VIEW");
        DB::unprepared("DROP VIEW ACTIVE_ACCOUNTS_VIEW");
        DB::unprepared("DROP VIEW ADMIN_DASHBOARD_VIEW");
        DB::unprepared("DROP SEQUENCE SEQ_BRANCH");

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });

        Schema::table('USERS', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });

        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('branches');
    }
};
