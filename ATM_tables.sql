
CREATE TABLE Bank (
    Bank_Id VARCHAR(10) PRIMARY KEY,
    IFSC_Code VARCHAR(15),
    Bank_Add VARCHAR(80),
    Bank_Name VARCHAR(30),
    B_ATM_Id VARCHAR(10)
);


CREATE TABLE ATM_Machine (
    ATM_Id VARCHAR(10) PRIMARY KEY,
    ATM_Name VARCHAR(10),
    ATM_Add VARCHAR(80),
    ATM_Bank_Id VARCHAR(10),
    ATM_Branch VARCHAR(10),
    ATM_Amount DECIMAL(10, 2),
    FOREIGN KEY (ATM_Bank_Id ) REFERENCES Bank (Bank_Id)
);

CREATE TABLE Card (
    Card_No BIGINT PRIMARY KEY,
    Card_Bank_id VARCHAR(10),
    Card_CVV BIGINT,
    Card_ExpiryDate DATE,
    Card_Balance DECIMAL(16, 2),
    Card_Type VARCHAR(10),
    Pin INT,
    FOREIGN KEY (Card_Bank_id) REFERENCES Bank(Bank_Id)
);

CREATE TABLE Branch (
    Branch_Id VARCHAR(15) PRIMARY KEY,
    Branch_Name VARCHAR(15),
    Branch_loc VARCHAR(15),
    B_Bank_Id VARCHAR(10),
    FOREIGN KEY (B_Bank_Id) REFERENCES Bank(Bank_Id)
);

CREATE TABLE Customer (
    C_Id VARCHAR(10) PRIMARY KEY,
    C_add VARCHAR(80),
    F_name VARCHAR(20),
    M_name VARCHAR(20),
    L_name VARCHAR(20),
    C_phone BIGINT
);
CREATE TABLE Customer_Card (
    C_Id VARCHAR(10),
    Card_No BIGINT,
    PRIMARY KEY (C_Id, Card_No),
    FOREIGN KEY (C_Id) REFERENCES Customer(C_Id),
    FOREIGN KEY (Card_No) REFERENCES Card(Card_No)
);

CREATE TABLE Transaction (
    Transaction_Id VARCHAR(10) PRIMARY KEY,
    Transaction_type VARCHAR(10),
    Transaction_date DATE,
    Transaction_time TIME,
    Transaction_amount DECIMAL(10, 2),
    Transaction_status VARCHAR(10),
    T_ATM_Id VARCHAR(10),
    FOREIGN KEY (T_ATM_Id) REFERENCES ATM_Machine (ATM_Id)
);

CREATE TABLE Transaction_Participant (
    Transaction_Id VARCHAR(10),
    Participant_C_Id VARCHAR(10),
    Participant_Role VARCHAR(10), -- Add a column to denote participant role
    FOREIGN KEY (Transaction_Id) REFERENCES Transaction (Transaction_Id),
    FOREIGN KEY (Participant_C_Id) REFERENCES Customer (C_Id),
    CHECK (Participant_Role IN ('Sender', 'Receiver')) -- Constraint to ensure valid roles
);


CREATE TABLE Account (
    Acc_no BIGINT PRIMARY KEY,
    Acc_type VARCHAR(10),
    A_C_Id VARCHAR(10),
    FOREIGN KEY (A_C_Id) REFERENCES Customer(C_Id)
);

CREATE TABLE CurrentA (
    Acc_no BIGINT PRIMARY KEY,
    FOREIGN KEY (Acc_no) REFERENCES Account(Acc_no)
);

CREATE TABLE Savings (
    Acc_no BIGINT PRIMARY KEY,
    FOREIGN KEY (Acc_no) REFERENCES Account(Acc_no)
);

-- Insert values into Bank table
INSERT INTO Bank (Bank_Id, IFSC_Code, Bank_Add, Bank_Name, B_ATM_Id) VALUES
('B001', 'IFSC001', '123 Main Street', 'SBI', 'ATM001'),
('B002', 'IFSC002', '456 Elm Street', 'HDFC', 'ATM002'),
('B003', 'IFSC003', '789 Oak Street', 'Canara', 'ATM003'),
('B004', 'IFSC004', '101 Pine Street', 'Indian Bank', 'ATM004'),
('B005', 'IFSC005', '202 Maple Street', 'BOB', 'ATM005'),
('B006', 'IFSC006', '303 Oak Street', 'BOI', 'ATM006'),
('B007', 'IFSC007', '404 Walnut Street', 'ICICI', 'ATM007'),
('B008', 'IFSC008', '505 Cherry Street', 'Karur vysya ', 'ATM008'),
('B009', 'IFSC009', '606 Pineapple Street', 'IOB', 'ATM009'),
('B010', 'IFSC010', '707 Banana Street', 'Kotak Mahendra', 'ATM010');

-- Insert values into ATM_Machine table
INSERT INTO ATM_Machine (ATM_Id, ATM_Name, ATM_Add, ATM_Bank_Id, ATM_Branch, ATM_Amount) VALUES
('ATM001', 'ATM 1', '123 Main Street', 'B001', 'Branch 1', 10000.00),
('ATM002', 'ATM 2', '456 Elm Street', 'B002', 'Branch 2', 15000.00),
('ATM003', 'ATM 3', '789 Oak Street', 'B003', 'Branch 3', 20000.00),
('ATM004', 'ATM 4', '101 Pine Street', 'B004', 'Branch 4', 25000.00),
('ATM005', 'ATM 5', '202 Maple Street', 'B005', 'Branch 5', 30000.00),
('ATM006', 'ATM 6', '303 Oak Street', 'B006', 'Branch 6', 35000.00),
('ATM007', 'ATM 7', '404 Walnut Street', 'B007', 'Branch 7', 40000.00),
('ATM008', 'ATM 8', '505 Cherry Street', 'B008', 'Branch 8', 45000.00),
('ATM009', 'ATM 9', '606 Pineapple Street', 'B009', 'Branch 9', 50000.00),
('ATM010', 'ATM 10', '707 Banana Street', 'B010', 'Branch 10', 55000.00);

-- Insert values into Card table
INSERT INTO Card (Card_No, Card_Bank_id, Card_CVV, Card_ExpiryDate, Card_Balance, Card_Type, Pin) VALUES
(1234567890123456, 'B001', 123, '2025-01-01', 5000.00, 'Debit', 1234),
(2345678901234567, 'B002', 234, '2025-02-01', 6000.00, 'Credit', 2345),
(3456789012345678, 'B003', 345, '2025-03-01', 7000.00, 'Debit', 3456),
(4567890123456789, 'B004', 456, '2025-04-01', 8000.00, 'Debit', 4567),
(5678901234567890, 'B005', 567, '2025-05-01', 9000.00, 'Credit', 5678),
(6789012345678901, 'B006', 678, '2025-06-01', 10000.00, 'Debit', 6789),
(7890123456789012, 'B007', 789, '2025-07-01', 11000.00, 'Debit', 7890),
(8901234567890123, 'B008', 890, '2025-08-01', 12000.00, 'Credit', 8901),
(9012345678901234, 'B009', 901, '2025-09-01', 13000.00, 'Debit', 9012);

-- Insert values into Branch table
INSERT INTO Branch (Branch_Id, Branch_Name, Branch_loc, B_Bank_Id) VALUES
('BR001', 'Branch 1', '123 Main Street', 'B001'),
('BR002', 'Branch 2', '456 Elm Street', 'B002'),
('BR003', 'Branch 3', '789 Oak Street', 'B003'),
('BR004', 'Branch 4', '101 Pine Street', 'B004'),
('BR005', 'Branch 5', '202 Maple Street', 'B005'),
('BR006', 'Branch 6', '303 Oak Street', 'B006'),
('BR007', 'Branch 7', '404 Walnut Street', 'B007'),
('BR008', 'Branch 8', '505 Cherry Street', 'B008'),
('BR009', 'Branch 9', '606 Pineapple Street', 'B009'),
('BR010', 'Branch 10', '707 Banana Street', 'B010');

-- Insert values into Customer table
INSERT INTO Customer (C_Id, C_add, F_name, M_name, L_name, C_phone) VALUES
('C001', '101 Pine Street', 'John', 'Doe', '', 1234567890),
('C002', '202 Maple Street', 'Jane', 'Smith', '', 2345678901),
('C003', '303 Oak Street', 'Alice', '', 'Johnson', 3456789012),
('C004', '404 Walnut Street', 'Michael', 'Brown', '', 4567890123),
('C005', '505 Cherry Street', 'Emily', '', 'Davis', 5678901234),
('C006', '606 Pineapple Street', 'David', 'Wilson', '', 6789012345),
('C007', '707 Banana Street', 'Sophia', 'Martinez', '', 7890123456),
('C008', '808 Mango Street', 'James', '', 'Anderson', 8901234567),
('C009', '909 Strawberry Street', 'Olivia', 'Taylor', '', 9012345678);

-- Insert values into Customer_Card table
INSERT INTO Customer_Card (C_Id, Card_No) VALUES
('C001', 1234567890123456),
('C002', 2345678901234567),
('C003', 3456789012345678),
('C004', 4567890123456789),
('C005', 5678901234567890),
('C006', 6789012345678901),
('C007', 7890123456789012),
('C008', 8901234567890123),
('C009', 9012345678901234);

-- Insert values into Transaction table
INSERT INTO Transaction (Transaction_Id, Transaction_type, Transaction_date, Transaction_time, Transaction_amount, Transaction_status, T_ATM_Id) VALUES
('T001', 'Withdrawal', '2024-05-09', '12:00:00', 100.00, 'Success', 'ATM001'),
('T002', 'Deposit', '2024-05-09', '13:00:00', 200.00, 'Success', 'ATM002'),
('T003', 'Transfer', '2024-05-09', '14:00:00', 150.00, 'Success', 'ATM003'),
('T004', 'Withdrawal', '2024-05-09', '15:00:00', 120.00, 'Success', 'ATM004'),
('T005', 'Deposit', '2024-05-09', '16:00:00', 180.00, 'Success', 'ATM005'),
('T006', 'Transfer', '2024-05-09', '17:00:00', 200.00, 'Success', 'ATM005'),
('T007', 'Withdrawal', '2024-05-09', '18:00:00', 150.00, 'Success', 'ATM006'),
('T008', 'Deposit', '2024-05-09', '19:00:00', 250.00, 'Success', 'ATM007'),
('T009', 'Withdrawal', '2024-05-09', '20:00:00', 300.00, 'Success','ATM008');

-- Insert values into Transaction_Participant table
INSERT INTO Transaction_Participant (Transaction_Id, Participant_C_Id, Participant_Role) VALUES
('T001', 'C001', 'Sender'), -- John Doe withdrew money from ATM001
('T002', 'C002', 'Sender'), -- Jane Smith deposited money into ATM002
('T003', 'C003', 'Sender'), -- Alice Johnson transferred money from ATM003
('T003', 'C002', 'Receiver'), -- Jane Smith received money from Alice Johnson's transfer
('T004', 'C004', 'Sender'), -- Michael Brown withdrew money from ATM004
('T005', 'C005', 'Sender'), -- Emily Davis deposited money into ATM005
('T006', 'C006', 'Sender'), -- David Wilson transferred money from ATM006
('T006', 'C007', 'Receiver'), -- Sophia Martinez received money from David Wilson's transfer
('T007', 'C007', 'Sender'), -- Sophia Martinez withdrew money from ATM007
('T008', 'C008', 'Sender'), -- James Anderson deposited money into ATM008
('T009', 'C009', 'Sender'); -- Daniel Hernandez withdrew money from ATM010

INSERT INTO Transaction (Transaction_Id, Transaction_type, Transaction_date, Transaction_time, Transaction_amount, Transaction_status, T_ATM_Id) VALUES
('T011', 'Deposit', '2024-05-10', '13:00:00', 200.00, 'Success','ATM001'),
('T012', 'Transfer', '2024-05-10', '14:00:00', 150.00, 'Success','ATM002'),
('T013', 'Withdrawal', '2024-05-10', '15:00:00', 120.00, 'Success', 'ATM003'),
('T014', 'Deposit', '2024-05-10', '16:00:00', 180.00, 'Success', 'ATM004'),
('T015', 'Transfer', '2024-05-10', '17:00:00', 200.00, 'Success','ATM001'),
('T016', 'Withdrawal', '2024-05-10', '18:00:00', 150.00, 'Success', 'ATM001'),
('T017', 'Deposit', '2024-05-10', '19:00:00', 250.00, 'Success', 'ATM001'),
('T018', 'Withdrawal', '2024-05-10', '20:00:00', 300.00, 'Success', 'ATM001');

-- Add more transaction participants
INSERT INTO Transaction_Participant (Transaction_Id, Participant_C_Id, Participant_Role) VALUES
('T011', 'C002', 'Sender'), -- Jane Smith deposited money into ATM002
('T012', 'C003', 'Sender'), -- Alice Johnson transferred money from ATM003
('T012', 'C004', 'Receiver'), -- Michael Brown received money from Alice Johnson's transfer
('T013', 'C004', 'Sender'), -- Michael Brown withdrew money from ATM004
('T014', 'C005', 'Sender'), -- Emily Davis deposited money into ATM005
('T015', 'C006', 'Sender'), -- David Wilson transferred money from ATM006
('T015', 'C007', 'Receiver'), -- Sophia Martinez received money from David Wilson's transfer
('T016', 'C007', 'Sender'), -- Sophia Martinez withdrew money from ATM007
('T017', 'C008', 'Sender'), -- James Anderson deposited money into ATM008
('T018', 'C009', 'Sender'); -- Olivia Taylor withdrew money from ATM009
-- Insert values into Account table
INSERT INTO Account (Acc_no, Acc_type, A_C_Id) VALUES
(1001, 'Savings', 'C001'),
(1002, 'Current', 'C002'),
(1003, 'Savings', 'C003'),
(1004, 'Savings', 'C004'),
(1005, 'Current', 'C005'),
(1006, 'Savings', 'C006'),
(1007, 'Savings', 'C007'),
(1008, 'Current', 'C008'),
(1009, 'Savings', 'C009');

-- Insert values into CurrentA table
INSERT INTO CurrentA (Acc_no) VALUES
(1002),
(1005),
(1008);

-- Insert values into Savings table
INSERT INTO Savings (Acc_no) VALUES
(1001),
(1003),
(1004),
(1006),
(1007),
(1009);
