<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::unprepared("
    CREATE OR REPLACE PROCEDURE PAY_LOAN_EMI (
        p_loan_id IN NUMBER,
        p_account_id IN NUMBER,
        p_emi_amount IN NUMBER
    ) AS
        v_balance NUMBER;
        v_remaining_amount NUMBER;
        v_user_id NUMBER;
    BEGIN
        SELECT balance INTO v_balance FROM accounts WHERE id = p_account_id FOR UPDATE;
        IF v_balance < p_emi_amount THEN
            RAISE_APPLICATION_ERROR(-20002, 'Insufficient balance in selected account.');
        END IF;
        
        UPDATE accounts SET balance = balance - p_emi_amount WHERE id = p_account_id;
        
        UPDATE loans SET remaining_amount = GREATEST(0, remaining_amount - p_emi_amount),
                         next_due_date = ADD_MONTHS(next_due_date, 1),
                         updated_at = CURRENT_TIMESTAMP
        WHERE id = p_loan_id RETURNING remaining_amount, user_id INTO v_remaining_amount, v_user_id;
        
        IF v_remaining_amount = 0 THEN
            UPDATE loans SET status = 'Closed' WHERE id = p_loan_id;
        END IF;
        
        INSERT INTO transactions (
            account_id,
            transaction_type,
            amount,
            description,
            created_at,
            updated_at
        ) VALUES (
            p_account_id,
            'LOAN_PAYMENT',
            p_emi_amount,
            'EMI Payment for Loan ID: ' || p_loan_id,
            CURRENT_TIMESTAMP,
            CURRENT_TIMESTAMP
        );
        
        INSERT INTO audit_log (table_name, action, performed_by, details, created_at, updated_at)
        VALUES ('loans', 'UPDATE', 'SYSTEM', 'EMI Payment of ' || p_emi_amount || ' for Loan ID: ' || p_loan_id || '. Remaining: ' || v_remaining_amount, SYSDATE, SYSDATE);
        
        INSERT INTO notifications (
            user_id,
            message,
            is_read,
            created_at,
            updated_at
        ) VALUES (
            v_user_id,
            'Loan payment of $' || TO_CHAR(p_emi_amount, 'FM999999990.00') || ' processed. Remaining balance: $' || TO_CHAR(v_remaining_amount, 'FM999999990.00'),
            0,
            CURRENT_TIMESTAMP,
            CURRENT_TIMESTAMP
        );
        
        COMMIT;
    END;
");

echo "Procedure PAY_LOAN_EMI updated successfully.\n";
