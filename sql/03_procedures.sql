-- Run as BANKDB

-- Clean up existing procedures if any
BEGIN
    EXECUTE IMMEDIATE 'DROP PROCEDURE SP_AUTHENTICATE_USER';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/
BEGIN
    EXECUTE IMMEDIATE 'DROP PROCEDURE SP_REGISTER_CUSTOMER';
EXCEPTION WHEN OTHERS THEN NULL;
END;
/

-- Stored procedure to authenticate both Employees and Customers
CREATE OR REPLACE PROCEDURE SP_AUTHENTICATE_USER (
    p_username IN VARCHAR2,
    p_password IN VARCHAR2,
    p_user_role IN VARCHAR2,
    p_user_id OUT VARCHAR2,
    p_full_name OUT VARCHAR2,
    p_email OUT VARCHAR2,
    p_status OUT VARCHAR2
) AS
BEGIN
    IF p_user_role = 'EMPLOYEE' THEN
        SELECT TO_CHAR(USER_ID), FULL_NAME, EMAIL, 'ACTIVE'
        INTO p_user_id, p_full_name, p_email, p_status
        FROM BANK_USERS
        WHERE (USERNAME = p_username OR EMAIL = p_username)
          AND PASSWORD_HASH = p_password;
    ELSIF p_user_role = 'CUSTOMER' THEN
        SELECT CUSTOMER_ID, FIRST_NAME || ' ' || LAST_NAME, EMAIL, STATUS
        INTO p_user_id, p_full_name, p_email, p_status
        FROM CUSTOMERS
        WHERE (CUSTOMER_ID = p_username OR EMAIL = p_username)
          AND PASSWORD_HASH = p_password;
    ELSE
        p_user_id := NULL;
        p_full_name := NULL;
        p_email := NULL;
        p_status := NULL;
    END IF;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        p_user_id := NULL;
        p_full_name := NULL;
        p_email := NULL;
        p_status := NULL;
END;
/

-- Stored procedure to register a new Customer
CREATE OR REPLACE PROCEDURE SP_REGISTER_CUSTOMER (
    p_first_name IN VARCHAR2,
    p_last_name IN VARCHAR2,
    p_email IN VARCHAR2,
    p_phone IN VARCHAR2,
    p_address IN VARCHAR2,
    p_dob IN DATE,
    p_nid IN VARCHAR2,
    p_password IN VARCHAR2,
    p_customer_id OUT VARCHAR2
) AS
BEGIN
    INSERT INTO CUSTOMERS (FIRST_NAME, LAST_NAME, EMAIL, PHONE, ADDRESS, DOB, NID, PASSWORD_HASH)
    VALUES (p_first_name, p_last_name, p_email, p_phone, p_address, p_dob, p_nid, p_password)
    RETURNING CUSTOMER_ID INTO p_customer_id;
END;
/
