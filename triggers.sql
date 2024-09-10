DELIMITER //

CREATE TRIGGER handle_transaction
AFTER INSERT ON Transaction
FOR EACH ROW
BEGIN
    DECLARE atm_balance DECIMAL(10, 2);

    -- Get the current ATM balance
    SELECT ATM_Amount INTO atm_balance
    FROM ATM_Machine
    WHERE ATM_Id = NEW.T_ATM_Id;

    -- Ensure atm_balance is not NULL
    IF atm_balance IS NOT NULL THEN
        -- Handle Deposit
        IF NEW.Transaction_type = 'Deposit' THEN
            -- Update the ATM balance after deposit
            SET atm_balance = atm_balance + NEW.Transaction_amount;

            -- Update the ATM balance in ATM_Machine table
            UPDATE ATM_Machine
            SET ATM_Amount = atm_balance
            WHERE ATM_Id = NEW.T_ATM_Id;
        
        -- Handle Successful Withdrawal
        ELSEIF NEW.Transaction_type = 'Withdrawal' AND NEW.Transaction_status = 'Success' THEN
            -- Update the ATM balance after withdrawal
            SET atm_balance = atm_balance - NEW.Transaction_amount;

            -- Update the ATM balance in ATM_Machine table
            UPDATE ATM_Machine
            SET ATM_Amount = atm_balance
            WHERE ATM_Id = NEW.T_ATM_Id;
        END IF;
    ELSE
        -- Handle NULL atm_balance case
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ATM balance is NULL';
    END IF;
END //

DELIMITER ;


DELIMITER//
-- Create a trigger to update ATM amount if it's less than 1000
CREATE TRIGGER update_atm_amount
AFTER UPDATE ON atm_machine
FOR EACH ROW
BEGIN
DECLARE A int;
SELECT ATM_Id INTO A where NEW.ATM_Balace<1000;
    IF NEW.ATM_Balance < 1000 THEN
        CALL func(A);
    END IF;
END;

DELIMITER;
