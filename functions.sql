DELIMITER //

CREATE PROCEDURE ChangeCardPIN(
    IN card_number BIGINT,
    IN new_pin INT
)
BEGIN
    -- Check if the card exists
    DECLARE card_count INT DEFAULT 0;
    SELECT COUNT(*) INTO card_count FROM Card WHERE Card_No = card_number;
    
    IF card_count > 0 THEN
        -- Update the PIN
        UPDATE Card SET Pin = new_pin WHERE Card_No = card_number;
        
        SELECT 'Success' AS Message;
    ELSE
        SELECT 'Card does not exist' AS Message;
    END IF;
END //

DELIMITER ;
 
 DELIMITER //
CREATE FUNCTION CheckBalance(p_cardNo BIGINT) RETURNS DECIMAL(16, 2)
BEGIN
    DECLARE v_balance DECIMAL(16, 2);
    SELECT Card_Balance INTO v_balance
    FROM Card
    WHERE Card_No = p_cardNo;
    RETURN v_balance;
END //
DELIMITER ;

CREATE VIEW Transaction_Sender_Receiver_View AS
SELECT 
    T.Transaction_Id,
    T.Transaction_type,
    T.Transaction_date,
    T.Transaction_time,
    T.Transaction_amount,
    T.Transaction_status,
    CASE 
        WHEN TP_Sender.Participant_Role = 'Sender' THEN TP_Sender.Participant_C_Id
        ELSE NULL
    END AS sender_id,
    CASE 
        WHEN TP_Receiver.Participant_Role = 'Receiver' THEN TP_Receiver.Participant_C_Id
        ELSE NULL
    END AS receiver_id
FROM 
    Transaction T
LEFT JOIN 
    Transaction_Participant TP_Sender ON T.Transaction_Id = TP_Sender.Transaction_Id AND TP_Sender.Participant_Role = 'Sender'
LEFT JOIN 
    Transaction_Participant TP_Receiver ON T.Transaction_Id = TP_Receiver.Transaction_Id AND TP_Receiver.Participant_Role = 'Receiver';



DELIMITER //

CREATE PROCEDURE MakeDepositAndUpdateBalance(
    IN p_card_number BIGINT,
    IN p_deposit_amount DECIMAL(16, 2),
    IN p_atm_name VARCHAR(10)
)
BEGIN
    DECLARE v_customer_id VARCHAR(10);
    DECLARE v_transaction_id VARCHAR(10);
    DECLARE v_atm_id VARCHAR(10);
    
    -- Get ATM ID from ATM name
    SELECT ATM_Id INTO v_atm_id
    FROM ATM_Machine
    WHERE ATM_Name = p_atm_name
    LIMIT 1;

    -- Update the card balance
    UPDATE Card
    SET Card_Balance = Card_Balance + p_deposit_amount
    WHERE Card_No = p_card_number;
    
    -- Insert into Transaction table
    SET v_transaction_id = CONCAT('T', LPAD(FLOOR(RAND() * 1000000), 6, '0')); -- Generating a random transaction ID
    INSERT INTO Transaction (Transaction_Id, Transaction_type, Transaction_date, Transaction_time, Transaction_amount, Transaction_status, T_ATM_Id)
    VALUES (v_transaction_id, 'Deposit', CURDATE(), CURTIME(), p_deposit_amount, 'Success', v_atm_id);
    
    -- Find the customer ID associated with the card
    SELECT C_Id INTO v_customer_id
    FROM Customer_Card
    WHERE Card_No = p_card_number
    LIMIT 1;
    
    -- Insert into Transaction_Participant table
    INSERT INTO Transaction_Participant (Transaction_Id, Participant_C_Id, Participant_Role)
    VALUES (v_transaction_id, v_customer_id, 'Sender');
    
END //

DELIMITER ;


DELIMITER //

CREATE PROCEDURE MakeWithdrawalAndUpdateBalance(
    IN p_card_number BIGINT,
    IN p_withdrawal_amount DECIMAL(16, 2),
    IN p_atm_name VARCHAR(10)
)
BEGIN
    DECLARE v_customer_id VARCHAR(10);
    DECLARE v_transaction_id VARCHAR(10);
    DECLARE v_atm_id VARCHAR(10);
    DECLARE v_current_balance DECIMAL(16, 2);
    DECLARE v_atm_balance DECIMAL(10, 2);
    DECLARE v_transaction_status VARCHAR(10);

    -- Get ATM ID and ATM balance from ATM name
    SELECT ATM_Id, ATM_Amount INTO v_atm_id, v_atm_balance
    FROM ATM_Machine
    WHERE ATM_Name = p_atm_name
    LIMIT 1;

    -- Get current balance of the card
    SELECT Card_Balance INTO v_current_balance
    FROM Card
    WHERE Card_No = p_card_number;

    -- Check if withdrawal amount is less than or equal to current balance and ATM balance
    IF v_current_balance >= p_withdrawal_amount AND v_atm_balance >= p_withdrawal_amount THEN
        -- Update the card balance
        UPDATE Card
        SET Card_Balance = Card_Balance - p_withdrawal_amount
        WHERE Card_No = p_card_number;

        -- Set transaction status to success
        SET v_transaction_status = 'Success';
    ELSE
        -- Set transaction status to failure
        SET v_transaction_status = 'Failure';
    END IF;

    -- Insert into Transaction table
    SET v_transaction_id = CONCAT('T', LPAD(FLOOR(RAND() * 1000000), 6, '0')); -- Generating a random transaction ID
    INSERT INTO `Transaction` (Transaction_Id, Transaction_type, Transaction_date, Transaction_time, Transaction_amount, Transaction_status, T_ATM_Id)
    VALUES (v_transaction_id, 'Withdrawal', CURDATE(), CURTIME(), p_withdrawal_amount, v_transaction_status, v_atm_id);

    -- Find the customer ID associated with the card
    SELECT C_Id INTO v_customer_id
    FROM Customer_Card
    WHERE Card_No = p_card_number
    LIMIT 1;

    -- Insert into Transaction_Participant table
    INSERT INTO Transaction_Participant (Transaction_Id, Participant_C_Id, Participant_Role)
    VALUES (v_transaction_id, v_customer_id, 'Sender');
    
    
END //

DELIMITER ;


DELIMITER //

CREATE PROCEDURE GetLatestTransactionSenderReceiver()
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE transId VARCHAR(10);
    DECLARE transType VARCHAR(10);
    DECLARE transDate DATE;
    DECLARE transTime TIME;
    DECLARE transAmount DECIMAL(10, 2);
    DECLARE transStatus VARCHAR(10);
    DECLARE atmLocation VARCHAR(80);
    DECLARE customerCardNumber VARCHAR(20);
    DECLARE atmId VARCHAR(10);
    DECLARE cardBalance DECIMAL(16, 2);
    DECLARE customerName VARCHAR(60);
    DECLARE bankName VARCHAR(10);

    -- Cursor to fetch the latest transaction details with additional fields
    DECLARE cur CURSOR FOR
        SELECT 
            T.Transaction_Id,
            T.Transaction_type,
            T.Transaction_date,
            T.Transaction_time,
            T.Transaction_amount,
            T.Transaction_status,
            A.ATM_Add AS atm_location,
            CONCAT('XXXXXXXXXXXX', SUBSTRING(CAST(C.Card_No AS CHAR(16)), LENGTH(CAST(C.Card_No AS CHAR(16))) - 3, 4)) AS customer_card_number,
            T.T_ATM_Id AS atm_id,
            C.Card_Balance AS card_balance,
            CONCAT(Cust.F_name, ' ', IFNULL(Cust.M_name, ''), ' ', Cust.L_name) AS customer_name,
            B.Bank_Name AS bank_name
        FROM 
            Transaction T
        LEFT JOIN 
            Transaction_Participant TP_Sender ON T.Transaction_Id = TP_Sender.Transaction_Id AND TP_Sender.Participant_Role = 'Sender'
        LEFT JOIN 
            Transaction_Participant TP_Receiver ON T.Transaction_Id = TP_Receiver.Transaction_Id AND TP_Receiver.Participant_Role = 'Receiver'
        LEFT JOIN 
            ATM_Machine A ON T.T_ATM_Id = A.ATM_Id
        LEFT JOIN 
            Bank B ON A.ATM_Bank_Id = B.Bank_Id
        LEFT JOIN 
            Customer_Card CC ON TP_Sender.Participant_C_Id = CC.C_Id
        LEFT JOIN 
            Card C ON CC.Card_No = C.Card_No
        LEFT JOIN 
            Customer Cust ON TP_Sender.Participant_C_Id = Cust.C_Id
        ORDER BY 
            T.Transaction_date DESC, 
            T.Transaction_time DESC
        LIMIT 1;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cur;
    FETCH cur INTO transId, transType, transDate, transTime, transAmount, transStatus, atmLocation, customerCardNumber, atmId, cardBalance, customerName, bankName;

    IF NOT done THEN
        -- Output the fetched record
        SELECT 
            transId AS Transaction_Id,
            transType AS Transaction_type,
            transDate AS Transaction_date,
            transTime AS Transaction_time,
            transAmount AS Transaction_amount,
            transStatus AS Transaction_status,
            atmLocation AS ATM_Location,
            customerCardNumber AS Customer_Card_Number,
            atmId AS ATM_Id,
            cardBalance AS Card_Balance,
            customerName AS Customer_Name,
            bankName AS Bank_Name;
    ELSE
        -- Add debug statement to see if the cursor is actually executing
        SELECT 'No transactions found' AS Debug_Info;
    END IF;

    CLOSE cur;
END //

DELIMITER ;


DELIMITER //

CREATE PROCEDURE MakeTransferAndUpdateBalance(
    IN p_sender_card_number BIGINT,
    IN p_receiver_account_number BIGINT,
    IN p_transfer_amount DECIMAL(16, 2),
    IN p_atm_name VARCHAR(10)
)
BEGIN
    DECLARE v_sender_customer_id VARCHAR(10);
    DECLARE v_receiver_customer_id VARCHAR(10);
    DECLARE v_transaction_id VARCHAR(10);
    DECLARE v_atm_id VARCHAR(10);
    DECLARE v_atm_balance DECIMAL(10, 2);
    DECLARE v_sender_current_balance DECIMAL(16, 2);
    DECLARE v_receiver_current_balance DECIMAL(16, 2);
    DECLARE v_transaction_status VARCHAR(10);

    -- Get ATM ID and ATM balance from ATM name
    SELECT ATM_Id, ATM_Amount INTO v_atm_id, v_atm_balance
    FROM ATM_Machine
    WHERE ATM_Name = p_atm_name
    LIMIT 1;

    -- Get current balance of the sender's card
    SELECT Card_Balance INTO v_sender_current_balance
    FROM Card
    WHERE Card_No = p_sender_card_number;

    -- Get current balance of the receiver's account
    SELECT Card_Balance INTO v_receiver_current_balance
    FROM Card
    JOIN Account ON Card.Card_No = Account.Acc_no
    WHERE Account.Acc_no = p_receiver_account_number;

    -- Check if sender has sufficient balance and ATM has sufficient funds
    IF v_sender_current_balance >= p_transfer_amount AND v_atm_balance >= p_transfer_amount THEN
        -- Generate a transaction ID
        SET v_transaction_id = CONCAT('T', LPAD(FLOOR(RAND() * 1000000), 6, '0'));

        -- Update sender's card balance
        UPDATE Card
        SET Card_Balance = Card_Balance - p_transfer_amount
        WHERE Card_No = p_sender_card_number;

        -- Update receiver's account balance
        UPDATE Card
        SET Card_Balance = Card_Balance + p_transfer_amount
        WHERE Card_No = p_receiver_account_number;

        -- Set transaction status to success
        SET v_transaction_status = 'Success';
    ELSE
        -- Set transaction status to failure
        SET v_transaction_status = 'Failure';
    END IF;

    -- Insert into Transaction table for sender and receiver
    INSERT INTO `Transaction` (Transaction_Id, Transaction_type, Transaction_date, Transaction_time, Transaction_amount, Transaction_status, T_ATM_Id)
    VALUES (v_transaction_id, 'Transfer', CURDATE(), CURTIME(), p_transfer_amount, v_transaction_status, v_atm_id);

    -- Insert into Transaction_Participant table for sender
    SELECT C_Id INTO v_sender_customer_id
    FROM Customer_Card
    WHERE Card_No = p_sender_card_number
    LIMIT 1;

    INSERT INTO Transaction_Participant (Transaction_Id, Participant_C_Id, Participant_Role)
    VALUES (v_transaction_id, v_sender_customer_id, 'Sender');

    -- Insert into Transaction_Participant table for receiver
    SELECT C_Id INTO v_receiver_customer_id
    FROM Account
    JOIN Customer ON Account.A_C_Id = Customer.C_Id
    WHERE Account.Acc_no = p_receiver_account_number
    LIMIT 1;

    INSERT INTO Transaction_Participant (Transaction_Id, Participant_C_Id, Participant_Role)
    VALUES (v_transaction_id, v_receiver_customer_id, 'Receiver');
END //

DELIMITER ;

DELIMITER  //
CREATE PROCEDURE func(x IN INT)
BEGIN
 UPDATE ATM_Machine SET ATM_Balance='3000' WHERE ATM_Id=x;
 END
 //
 DELIMITER;