-- Run as BANKDB

-- Seed initial Employee account
INSERT INTO BANK_USERS (USERNAME, PASSWORD_HASH, FULL_NAME, EMAIL, USER_ROLE)
VALUES ('emp001', 'password123', 'John Doe (Employee)', 'john.doe@bank.com', 'EMPLOYEE');

-- Seed initial Customer account (direct table insert)
INSERT INTO CUSTOMERS (CUSTOMER_ID, FIRST_NAME, LAST_NAME, EMAIL, PHONE, ADDRESS, DOB, NID, PASSWORD_HASH, STATUS)
VALUES ('CUST-000001', 'Jane', 'Smith', 'jane.smith@gmail.com', '555-0199', '123 Bank St, New York', TO_DATE('1990-05-15', 'YYYY-MM-DD'), 'NID-9988776655', 'password123', 'ACTIVE');

COMMIT;
