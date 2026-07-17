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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('USERS')->onDelete('cascade');
            $table->string('loan_type');
            $table->decimal('amount', 15, 2);
            $table->integer('duration_months');
            $table->string('purpose')->nullable();
            $table->string('status')->default('Pending');
            $table->decimal('monthly_installment', 15, 2)->default(0.00);
            $table->decimal('remaining_amount', 15, 2)->default(0.00);
            $table->date('next_due_date')->nullable();
            $table->timestamps();
        });

        // Compile APPLY_LOAN procedure
        DB::unprepared("
            CREATE OR REPLACE PROCEDURE APPLY_LOAN (
                p_user_id IN NUMBER,
                p_loan_type IN VARCHAR2,
                p_amount IN NUMBER,
                p_duration_months IN NUMBER,
                p_purpose IN VARCHAR2
            ) AS
                v_installment NUMBER;
                v_loan_interest NUMBER;
            BEGIN
                BEGIN
                    SELECT TO_NUMBER(settings_value) INTO v_loan_interest
                    FROM system_settings WHERE settings_key = 'LOAN_INTEREST';
                EXCEPTION
                    WHEN NO_DATA_FOUND THEN v_loan_interest := 8.5;
                END;

                v_installment := (p_amount * (1 + (v_loan_interest / 100))) / p_duration_months;
                
                INSERT INTO loans (
                    user_id,
                    loan_type,
                    amount,
                    duration_months,
                    purpose,
                    status,
                    monthly_installment,
                    remaining_amount,
                    next_due_date,
                    created_at,
                    updated_at
                ) VALUES (
                    p_user_id,
                    p_loan_type,
                    p_amount,
                    p_duration_months,
                    p_purpose,
                    'Pending',
                    v_installment,
                    p_amount,
                    ADD_MONTHS(SYSDATE, 1),
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                );
                COMMIT;
            END;
        ");

        // Compile PAY_LOAN_EMI procedure
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE PAY_LOAN_EMI");
        DB::unprepared("DROP PROCEDURE APPLY_LOAN");
        Schema::dropIfExists('loans');
    }
};
