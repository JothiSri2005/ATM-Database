<?php
session_start();

// Check if the user is logged in and card number is set in session
if (!isset($_SESSION['card_number'])) {
    header("Location: index1.php");
    exit();
}
$atm_name = $_GET['atm_name'] ?? '';
$atm_bank_id = $_GET['atm_bank_id'] ?? '';
$origin = $_GET['origin'] ?? 'main_page1';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .section {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Help Page</h1>

        <div class="section">
            <h2>FAQs</h2>
            <p><strong>Q:</strong> How do I use this ATM?</p>
            <p><strong>A:</strong> To use this ATM first you have to select a ATM_id</p>

            <p><strong>Q:</strong> How do I reset my pin?</p>
            <p><strong>A:</strong> To reset your password, first you have to login into your account and select the change pin button and enter the new password.</p>

            <p><strong>Q:</strong> How do I inquiry my balance?</p>
            <p><strong>A:</strong> To inquiry your balance, first you have to login into your account and select the balance inquiry button</p>

            <p><strong>Q:</strong> How do I withdraw money?</p>
            <p><strong>A:</strong> To withdraw amount, first you have to login into your account.Select the withdraw button to withdraw a amount by entering the amount or Select fast cash button to withdraw a fixed amount by selecting it</p>

            <p><strong>Q:</strong> How do I see the recent transactions?</p>
            <p><strong>A:</strong> To see the recent transaction details, first you have to login into your account and select the mini statement button</p>

            <p><strong>Q:</strong> How do I deposit money to my account?</p>
            <p><strong>A:</strong>  To deposit money to your account, first you have to login into your account and select the deposit button</p>

            <p><strong>Q:</strong> How do I transfer fund?</p>
            <p><strong>A:</strong> To transfer fund to another account, first you have to login into your account and select the fund transfer button</p>

        </div>

        <div class="section">
            <h2>Contact Us</h2>
            <p>If you have any further questions or need assistance, please feel free to contact our support team at <a href="mailto:yuvapriya245@gmail.com">support@example.com</a>.</p>
        </div>
        <a href="<?php echo htmlspecialchars($origin . '.php?atm_name=' . urlencode($atm_name) . '&atm_bank_id=' . urlencode($atm_bank_id)); ?>" class="btn btn-primary">Go Back</a>
                        </form>
    </div>
</body>
</html>