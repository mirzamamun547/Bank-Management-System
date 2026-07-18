<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create the support_tickets table
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ticket_type', 30);   // COMPLAINT, FEEDBACK, REQUEST
            $table->string('subject', 200);
            $table->text('message');
            $table->string('priority', 20)->default('Medium'); // Low, Medium, High
            $table->string('status', 20)->default('Open');     // Open, In Review, Resolved, Closed
            $table->text('admin_response')->nullable();
            $table->timestamps();
        });

        // ─────────────────────────────────────────────────────────────
        // 2. PL/SQL STORED PROCEDURE: SUBMIT_SUPPORT_TICKET
        //    Inserts a ticket AND auto-generates a notification
        //    for the user confirming submission.
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("
            CREATE OR REPLACE PROCEDURE SUBMIT_SUPPORT_TICKET (
                p_user_id     IN NUMBER,
                p_ticket_type IN VARCHAR2,
                p_subject     IN VARCHAR2,
                p_message     IN VARCHAR2,
                p_priority    IN VARCHAR2
            ) AS
                v_ticket_id NUMBER;
                v_user_name VARCHAR2(200);
            BEGIN
                -- Get user's full name
                SELECT first_name || ' ' || last_name
                INTO   v_user_name
                FROM   USERS
                WHERE  id = p_user_id;

                -- Insert the support ticket
                INSERT INTO support_tickets (
                    user_id, ticket_type, subject, message,
                    priority, status, created_at, updated_at
                ) VALUES (
                    p_user_id, p_ticket_type, p_subject, p_message,
                    p_priority, 'Open', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                )
                RETURNING id INTO v_ticket_id;

                -- Auto-notify the user about successful submission
                INSERT INTO notifications (user_id, message, is_read, created_at, updated_at)
                VALUES (
                    p_user_id,
                    'Your support ticket #' || v_ticket_id || ' (' || p_ticket_type || ': ' || p_subject || ') has been submitted successfully. Our team will review it shortly.',
                    0,
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                );

            EXCEPTION
                WHEN NO_DATA_FOUND THEN
                    RAISE_APPLICATION_ERROR(-20040, 'User not found.');
                WHEN OTHERS THEN
                    RAISE_APPLICATION_ERROR(-20041, 'Failed to submit support ticket: ' || SQLERRM);
            END;
        ");

        // ─────────────────────────────────────────────────────────────
        // 3. PL/SQL TRIGGER: TRG_TICKET_STATUS_NOTIFY
        //    Fires when admin updates ticket status → auto-notifies user
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("
            CREATE OR REPLACE TRIGGER TRG_TICKET_STATUS_NOTIFY
            AFTER UPDATE OF status ON support_tickets
            FOR EACH ROW
            DECLARE
                v_msg VARCHAR2(4000);
            BEGIN
                IF :OLD.status != :NEW.status THEN
                    v_msg := 'Your support ticket #' || :NEW.id || ' status has been updated from ' || :OLD.status || ' to ' || :NEW.status || '.';

                    IF :NEW.admin_response IS NOT NULL AND (:OLD.admin_response IS NULL OR :OLD.admin_response != :NEW.admin_response) THEN
                        v_msg := v_msg || ' Admin Response: ' || :NEW.admin_response;
                    END IF;

                    INSERT INTO notifications (user_id, message, is_read, created_at, updated_at)
                    VALUES (
                        :NEW.user_id,
                        v_msg,
                        0,
                        CURRENT_TIMESTAMP,
                        CURRENT_TIMESTAMP
                    );
                END IF;
            END;
        ");
    }

    public function down(): void
    {
        // Drop trigger
        try { DB::unprepared("DROP TRIGGER TRG_TICKET_STATUS_NOTIFY"); } catch (\Exception $e) {}

        // Drop procedure
        try { DB::unprepared("DROP PROCEDURE SUBMIT_SUPPORT_TICKET"); } catch (\Exception $e) {}

        // Drop table
        Schema::dropIfExists('support_tickets');
    }
};
