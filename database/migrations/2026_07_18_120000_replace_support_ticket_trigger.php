<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Recreate trigger without OF clause, and compare CLOB using DBMS_LOB.compare
        DB::unprepared("
            CREATE OR REPLACE TRIGGER TRG_TICKET_STATUS_NOTIFY
            AFTER UPDATE ON support_tickets
            FOR EACH ROW
            DECLARE
                v_msg VARCHAR2(4000);
                v_changed NUMBER := 0;
            BEGIN
                -- Check if status changed
                IF :OLD.status != :NEW.status THEN
                    v_changed := 1;
                END IF;

                -- Check if admin_response changed (safely handling nulls and CLOB comparison)
                IF (:OLD.admin_response IS NULL AND :NEW.admin_response IS NOT NULL) OR
                   (:OLD.admin_response IS NOT NULL AND :NEW.admin_response IS NULL) OR
                   (:OLD.admin_response IS NOT NULL AND :NEW.admin_response IS NOT NULL AND DBMS_LOB.COMPARE(:OLD.admin_response, :NEW.admin_response) != 0) THEN
                    v_changed := 1;
                END IF;

                IF v_changed = 1 THEN
                    v_msg := 'Update on Ticket #' || :NEW.id || ' [' || :NEW.subject || ']. Status: ' || :NEW.status || '.';
                    IF :NEW.admin_response IS NOT NULL THEN
                        -- Cast CLOB to VARCHAR2 for notification string insertion
                        v_msg := v_msg || ' Response: ' || DBMS_LOB.SUBSTR(:NEW.admin_response, 1000, 1);
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
        // Revert to original trigger
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
};
