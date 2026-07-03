CREATE OR REPLACE VIEW PENDING_LOANS AS
SELECT l.id AS loan_id, l.amount, l.loan_type, l.duration_months, l.purpose, l.created_at,
       u.id AS user_id, u.full_name, u.email, u.phone
FROM loans l
JOIN USERS u ON l.user_id = u.id
WHERE l.status = 'Pending';
/

CREATE OR REPLACE FUNCTION CHECK_LOAN_ELIGIBILITY(p_user_id IN NUMBER) RETURN VARCHAR2 IS
    v_active_accounts NUMBER;
    v_active_loans NUMBER;
BEGIN
    SELECT COUNT(*) INTO v_active_accounts FROM accounts WHERE user_id = p_user_id AND status = 'Active';
    IF v_active_accounts = 0 THEN
        RETURN 'NOT_ELIGIBLE: No active accounts found.';
    END IF;
    
    SELECT COUNT(*) INTO v_active_loans FROM loans WHERE user_id = p_user_id AND status IN ('Active', 'Pending');
    IF v_active_loans > 0 THEN
        RETURN 'NOT_ELIGIBLE: User already has an active or pending loan.';
    END IF;

    RETURN 'ELIGIBLE';
END;
/

CREATE OR REPLACE PROCEDURE APPROVE_LOAN(
    p_loan_id IN NUMBER,
    p_action IN VARCHAR2, 
    p_performed_by IN VARCHAR2
) IS
    v_status VARCHAR2(20);
    v_user_id NUMBER;
    v_amount NUMBER;
    
    CURSOR c_loans IS
        SELECT user_id, amount
        FROM loans
        WHERE id = p_loan_id AND status = 'Pending';
BEGIN
    IF p_action = 'APPROVE' THEN
        v_status := 'Active';
    ELSIF p_action = 'REJECT' THEN
        v_status := 'Rejected';
    ELSE
        RAISE_APPLICATION_ERROR(-20001, 'Invalid action specified. Must be APPROVE or REJECT.');
    END IF;

    OPEN c_loans;
    FETCH c_loans INTO v_user_id, v_amount;
    IF c_loans%NOTFOUND THEN
        CLOSE c_loans;
        RAISE_APPLICATION_ERROR(-20002, 'Loan not found or not in pending state.');
    END IF;
    CLOSE c_loans;

    UPDATE loans 
    SET status = v_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_loan_id;

    -- Notification
    INSERT INTO notifications (user_id, message, is_read, created_at, updated_at)
    VALUES (
        v_user_id,
        'Your loan application for $' || TO_CHAR(v_amount, 'FM999999990.00') || ' has been ' || v_status || '.',
        0,
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP
    );

    -- Audit Log
    INSERT INTO audit_log (table_name, action, performed_by, details, created_at, updated_at)
    VALUES (
        'loans',
        p_action || '_LOAN',
        p_performed_by,
        'Loan ID ' || p_loan_id || ' ' || v_status || ' for $' || v_amount,
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP
    );
    
    COMMIT;
END;
/
EXIT;
