

CREATE OR REPLACE PROCEDURE CREATE_CUSTOMER (
    p_first_name   IN  VARCHAR2,
    p_last_name    IN  VARCHAR2,
    p_email        IN  VARCHAR2,
    p_phone        IN  VARCHAR2,
    p_address      IN  VARCHAR2,
    p_dob          IN  DATE,
    p_nid          IN  VARCHAR2,
    p_password     IN  VARCHAR2,
    p_role         IN  VARCHAR2 DEFAULT 'CUSTOMER',
    p_status       IN  VARCHAR2 DEFAULT 'ACTIVE',
    o_customer_id  OUT VARCHAR2
) AS
    v_cust_seq NUMBER;
BEGIN
    
    SELECT CUSTOMER_ID_SEQ.NEXTVAL INTO v_cust_seq FROM DUAL;
    o_customer_id := 'CUST-' || v_cust_seq;


    INSERT INTO USERS (
        CUSTOMER_ID,
        FIRST_NAME,
        LAST_NAME,
        FULL_NAME,
        EMAIL,
        PHONE,
        ADDRESS,
        DOB,
        NID,
        PASSWORD,
        ROLE,
        STATUS
    ) VALUES (
        o_customer_id,
        p_first_name,
        p_last_name,
        p_first_name || ' ' || p_last_name,
        p_email,
        p_phone,
        p_address,
        p_dob,
        p_nid,
        p_password,
        p_role,
        p_status
    );

    
    COMMIT;

EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20001, 'Error creating customer: ' || SQLERRM);
END CREATE_CUSTOMER;
/
EXIT;
