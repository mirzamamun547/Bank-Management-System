<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. FIND_ACTIVE_ACCOUNT
        DB::unprepared("
            CREATE OR REPLACE FUNCTION FIND_ACTIVE_ACCOUNT(
                p_account_number IN VARCHAR2
            ) RETURN SYS_REFCURSOR IS
                v_cursor SYS_REFCURSOR;
            BEGIN
                OPEN v_cursor FOR
                    SELECT 
                        a.id AS account_id,
                        a.account_number,
                        a.balance,
                        a.status,
                        u.id AS user_id,
                        u.customer_id,
                        u.first_name || ' ' || u.last_name AS full_name,
                        u.nid
                    FROM accounts a
                    JOIN USERS u ON a.user_id = u.id
                    WHERE a.account_number = p_account_number
                      AND UPPER(a.status)  = 'ACTIVE'
                      AND UPPER(u.status)  = 'ACTIVE'
                      AND UPPER(u.role)    = 'CUSTOMER';
                
                RETURN v_cursor;
            END;
        ");

        // 2. DO_DEPOSIT
        DB::unprepared("
            CREATE OR REPLACE PROCEDURE DO_DEPOSIT(
                p_account_number IN VARCHAR2,
                p_amount         IN NUMBER,
                p_performed_by   IN VARCHAR2
            )
            IS
                v_account_id  accounts.id%TYPE;
                v_user_id     accounts.user_id%TYPE;
                v_new_balance accounts.balance%TYPE;
            BEGIN
                SELECT a.id, a.user_id
                INTO   v_account_id, v_user_id
                FROM   accounts a
                WHERE  a.account_number = p_account_number
                AND    UPPER(a.status)  = 'ACTIVE';

                UPDATE accounts
                SET    balance    = balance + p_amount,
                       updated_at = CURRENT_TIMESTAMP
                WHERE  account_number = p_account_number;
              
                SELECT balance INTO v_new_balance
                FROM   accounts
                WHERE  account_number = p_account_number;

                INSERT INTO transactions (account_id, transaction_type, amount, description, created_at, updated_at)
                VALUES (
                    v_account_id,
                    'DEPOSIT',
                    p_amount,
                    'Cash deposit processed by bank employee.',
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                );
               
                INSERT INTO notifications (user_id, message, is_read, created_at, updated_at)
                VALUES (
                    v_user_id,
                    'Deposit Successful! Amount: $' || TO_CHAR(p_amount, 'FM999999990.00') ||
                    ' | New Balance: $' || TO_CHAR(v_new_balance, 'FM999999990.00'),
                    0,
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                );

                INSERT INTO audit_log (table_name, action, performed_by, details, created_at, updated_at)
                VALUES (
                    'accounts',
                    'DEPOSIT',
                    p_performed_by,
                    'Deposited $' || TO_CHAR(p_amount, 'FM999999990.00') ||
                    ' to account ' || p_account_number,
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                );

                COMMIT;

            EXCEPTION
                WHEN NO_DATA_FOUND THEN
                    RAISE_APPLICATION_ERROR(-20004, 'Active account not found.');
                WHEN OTHERS THEN
                    ROLLBACK;
                    RAISE_APPLICATION_ERROR(-20005, 'DO_DEPOSIT Error: ' || SQLERRM);
            END;
        ");

        // 3. DO_WITHDRAW
        DB::unprepared("
            CREATE OR REPLACE PROCEDURE DO_WITHDRAW(
                p_account_number IN VARCHAR2,
                p_amount         IN NUMBER,
                p_performed_by   IN VARCHAR2
            )
            IS
                v_account_id  accounts.id%TYPE;
                v_user_id     accounts.user_id%TYPE;
                v_balance     accounts.balance%TYPE;
                v_new_balance accounts.balance%TYPE;
            BEGIN
                SELECT a.id, a.user_id, a.balance
                INTO   v_account_id, v_user_id, v_balance
                FROM   accounts a
                WHERE  a.account_number = p_account_number
                AND    UPPER(a.status)  = 'ACTIVE';

                IF v_balance < p_amount THEN
                    RAISE_APPLICATION_ERROR(-20006, 'Insufficient funds for withdrawal.');
                END IF;

                UPDATE accounts
                SET    balance    = balance - p_amount,
                       updated_at = CURRENT_TIMESTAMP
                WHERE  account_number = p_account_number;
              
                SELECT balance INTO v_new_balance
                FROM   accounts
                WHERE  account_number = p_account_number;

                INSERT INTO transactions (account_id, transaction_type, amount, description, created_at, updated_at)
                VALUES (
                    v_account_id,
                    'WITHDRAWAL',
                    p_amount,
                    'Cash withdrawal processed by bank employee.',
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                );
               
                INSERT INTO notifications (user_id, message, is_read, created_at, updated_at)
                VALUES (
                    v_user_id,
                    'Withdrawal Successful! Amount: $' || TO_CHAR(p_amount, 'FM999999990.00') ||
                    ' | New Balance: $' || TO_CHAR(v_new_balance, 'FM999999990.00'),
                    0,
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                );

                INSERT INTO audit_log (table_name, action, performed_by, details, created_at, updated_at)
                VALUES (
                    'accounts',
                    'WITHDRAWAL',
                    p_performed_by,
                    'Withdrew $' || TO_CHAR(p_amount, 'FM999999990.00') ||
                    ' from account ' || p_account_number,
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                );

                COMMIT;

            EXCEPTION
                WHEN NO_DATA_FOUND THEN
                    RAISE_APPLICATION_ERROR(-20004, 'Active account not found.');
                WHEN OTHERS THEN
                    ROLLBACK;
                    RAISE_APPLICATION_ERROR(-20007, 'DO_WITHDRAW Error: ' || SQLERRM);
            END;
        ");

        // 4. DO_TRANSFER
        DB::unprepared("
            CREATE OR REPLACE PROCEDURE DO_TRANSFER(
                p_from_account IN VARCHAR2,
                p_to_account   IN VARCHAR2,
                p_amount       IN NUMBER,
                p_performed_by IN VARCHAR2
            )
            IS
                v_from_id       accounts.id%TYPE;
                v_from_user_id  accounts.user_id%TYPE;
                v_from_balance  accounts.balance%TYPE;
                v_to_id         accounts.id%TYPE;
                v_to_user_id    accounts.user_id%TYPE;
            BEGIN
                -- Validate Sender
                SELECT a.id, a.user_id, a.balance
                INTO   v_from_id, v_from_user_id, v_from_balance
                FROM   accounts a
                WHERE  a.account_number = p_from_account
                AND    UPPER(a.status)  = 'ACTIVE';

                IF v_from_balance < p_amount THEN
                    RAISE_APPLICATION_ERROR(-20006, 'Insufficient funds for transfer.');
                END IF;

                -- Validate Receiver
                SELECT a.id, a.user_id
                INTO   v_to_id, v_to_user_id
                FROM   accounts a
                WHERE  a.account_number = p_to_account
                AND    UPPER(a.status)  = 'ACTIVE';

                -- Deduct from sender
                UPDATE accounts
                SET    balance    = balance - p_amount,
                       updated_at = CURRENT_TIMESTAMP
                WHERE  account_number = p_from_account;

                -- Add to receiver
                UPDATE accounts
                SET    balance    = balance + p_amount,
                       updated_at = CURRENT_TIMESTAMP
                WHERE  account_number = p_to_account;

                -- Transactions
                INSERT INTO transactions (account_id, transaction_type, amount, description, created_at, updated_at)
                VALUES (v_from_id, 'TRANSFER_OUT', p_amount, 'Transfer to ' || p_to_account, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

                INSERT INTO transactions (account_id, transaction_type, amount, description, created_at, updated_at)
                VALUES (v_to_id, 'TRANSFER_IN', p_amount, 'Transfer from ' || p_from_account, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
               
                -- Notifications
                INSERT INTO notifications (user_id, message, is_read, created_at, updated_at)
                VALUES (v_from_user_id, 'You transferred $' || TO_CHAR(p_amount, 'FM999999990.00') || ' to ' || p_to_account, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

                INSERT INTO notifications (user_id, message, is_read, created_at, updated_at)
                VALUES (v_to_user_id, 'You received $' || TO_CHAR(p_amount, 'FM999999990.00') || ' from ' || p_from_account, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

                INSERT INTO audit_log (table_name, action, performed_by, details, created_at, updated_at)
                VALUES (
                    'accounts',
                    'TRANSFER',
                    p_performed_by,
                    'Transferred $' || TO_CHAR(p_amount, 'FM999999990.00') || ' from ' || p_from_account || ' to ' || p_to_account,
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                );

                COMMIT;
            EXCEPTION
                WHEN NO_DATA_FOUND THEN
                    RAISE_APPLICATION_ERROR(-20004, 'One or both accounts are invalid/inactive.');
                WHEN OTHERS THEN
                    ROLLBACK;
                    RAISE_APPLICATION_ERROR(-20008, 'DO_TRANSFER Error: ' || SQLERRM);
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP FUNCTION FIND_ACTIVE_ACCOUNT");
        DB::unprepared("DROP PROCEDURE DO_DEPOSIT");
        DB::unprepared("DROP PROCEDURE DO_WITHDRAW");
        DB::unprepared("DROP PROCEDURE DO_TRANSFER");
    }
};
