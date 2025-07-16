
# ATM Database Management System

A secure and database-driven ATM simulation system built using **PHP**, **MySQL**, and **FPDF** for generating receipts. This project allows customers to perform ATM-like operations such as deposits, withdrawals, balance checks, fund transfers, PIN changes, and mini statements through a dynamic web interface.

---

## 📘 Overview

The project simulates a real-world ATM system where users can independently perform banking transactions without staff involvement. Security is ensured via PIN validation and card expiry checks. The backend utilizes stored procedures, triggers, functions, and views to ensure data consistency, confidentiality, and integrity.

---

## 🛠️ Tech Stack

- **Frontend**: PHP
- **Backend**: MySQL
- **PDF Generation**: [FPDF](http://www.fpdf.org/)
- **Tools**: XAMPP / WAMP for local hosting

---

## 🔐 Key Features

- ✅ Card authentication with PIN and expiry date check
- 🔄 PIN change (with validation of old PIN)
- 💰 Balance enquiry (via SQL function)
- 💳 Deposit and Withdrawal operations
- ⚡ Fast Cash feature (pre-defined withdrawal amounts)
- 💸 Fund Transfer between accounts
- 📃 Mini Statement & Account Info
- 🧾 Receipt generation (via FPDF + MySQL cursor)
- 📈 Transaction tracking and role-based participants (Sender/Receiver)
- 🔁 Dynamic update of ATM cash via triggers

---

## 🧱 Database Structure

**Major Tables:**
- `Bank`, `ATM_Machine`, `Card`, `Customer`, `Account`, `Transaction`, `Transaction_Participant`

**Views:**
- `Transaction_Sender_Receiver_View`: shows sender/receiver for each transaction

**Stored Procedures:**
- `ChangeCardPIN`
- `MakeDepositAndUpdateBalance`
- `MakeWithdrawalAndUpdateBalance`
- `MakeTransferAndUpdateBalance`

**Function:**
- `CheckBalance`: Returns card balance

**Triggers:**
- Update ATM cash after deposit/withdrawal
- Automatically replenish ATM if balance falls below ₹1000

---

## 📂 Project Structure

```
/atm-dbms/
├── index.php                  # Select ATM
├── index1.php                 # Card number + PIN login
├── main_page.php             # ATM main menu
├── pin_change.php            # Change card PIN
├── balance.php               # Check balance
├── deposit.php               # Deposit cash
├── withdraw.php              # Withdraw cash
├── fastcash.php              # Fast cash option
├── fundtransfer.php          # Transfer money
├── mini_statement.php        # View recent transactions
├── account_info.php          # Show account info
├── help.php                  # ATM help page
├── receipt.php               # FPDF-based receipt generator
├── db/                       # SQL scripts for schema + procedures
└── assets/                   # CSS, screenshots, icons (optional)
```

---

📄 [View Full Report with Screenshots (PDF)](docs/ATM_DBMS_Project.pdf)


---

## 🧪 How to Run

1. **Clone this repo:**
   ```bash
   git clone https://github.com/JothiSri2005/ATM-Database.git
   ```

2. **Import Database:**
   - Open XAMPP → start Apache & MySQL
   - Go to phpMyAdmin → import `ATM-tables.sql`

3. **Place project in XAMPP `htdocs` folder:**
   ```
   C:/xampp/htdocs/atm-dbms/
   ```

4. **Launch in browser:**
   ```
   http://localhost/atm-dbms/index.php
   ```

---

## 🧾 Sample Card Details

| Card No           | PIN  | Type   | Expiry Date |
|-------------------|------|--------|--------------|
| 1234567890123456  | 1234 | Debit  | 2026-12-31   |
| 9876543210987654  | 4321 | Credit | 2025-10-15   |

*(Add these to the `Card` table manually or include them in your seed SQL)*

---

## 👨‍💻 Team Members
   
- **Jothi Sri S**
- **Yuva Priya D**

> Department of Computer Science and Engineering  
> College of Engineering Guindy, Anna University

---

## 📜 License

This project was developed for educational purposes under the course **CS6106: Database Management Systems**. Feel free to use it for learning or academic projects.

---

## 🙌 Acknowledgements

- [FPDF Library](http://www.fpdf.org/)
- MySQL & phpMyAdmin
- XAMPP stack for development

---
