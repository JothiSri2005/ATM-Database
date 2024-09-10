<?php
session_start();

// Check if the user is logged in and card number is set in session
if (!isset($_SESSION['card_number'])) {
    header("Location: index1.php");
    exit();
}

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission
    // Validate and sanitize input
    $withdraw_amount = $_POST['withdraw_amount'];
    if ($withdraw_amount <= 0 || $withdraw_amount < 100 || $withdraw_amount > 50000) {
        $error_message = "Please enter a valid withdrawal amount between 100 and 50,000.";
    } else {
        // Get atm_name and atm_bank_id from GET parameters
        $atm_name = $_GET['atm_name'] ?? '';
        $atm_bank_id = $_GET['atm_bank_id'] ?? '';

        // Call the stored procedure to make the withdrawal
        $con = mysqli_connect("localhost", "root", "", "atm-tables") or die(mysqli_connect_error());
        $card_number = $_SESSION['card_number'];
        $stmt = $con->prepare("CALL MakeWithdrawalAndUpdateBalance(?, ?, ?)");
        $stmt->bind_param("ids", $card_number, $withdraw_amount, $atm_name); // Corrected parameter binding
        $stmt->execute();
        $stmt->close();
        mysqli_close($con);

        // Store success message in session
        $_SESSION['withdraw_success'] = "Withdrawal of $withdraw_amount successfully made.";

        // Redirect to the same page to show the success message
        header("Location: fastcash.php?atm_name=" . urlencode($atm_name) . "&atm_bank_id=" . urlencode($atm_bank_id) . "&origin=" . urlencode($_GET['origin'] ?? 'main_page1'));
        exit();
    }
}

if (isset($_SESSION['withdraw_success'])) {
    $success_message = $_SESSION['withdraw_success'];
    unset($_SESSION['withdraw_success']);
}

// Get atm_name and atm_bank_id from GET parameters
$atm_name = $_GET['atm_name'] ?? '';
$atm_bank_id = $_GET['atm_bank_id'] ?? '';
$origin = $_GET['origin'] ?? 'main_page1';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fast Cash Withdrawal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .btn-amount {
            width: calc(33.33% - 10px);
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .amount-selection {
            margin-bottom: 20px;
        }
    </style>
</head>
<body style="background:#C8A2C9;background-size: 100%">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Select Withdrawal Amount</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        <?php
                        if (!empty($error_message)) {
                            echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
                        }
                        ?>
                        <div class="amount-selection">
                            <form id="fastCashForm" action="fastcash.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>&origin=<?php echo urlencode($origin); ?>" method="post">
                                <button type="button" class="btn btn-primary btn-amount" onclick="selectAmount(100)">100</button>
                                <button type="button" class="btn btn-primary btn-amount" onclick="selectAmount(200)">200</button>
                                <button type="button" class="btn btn-primary btn-amount" onclick="selectAmount(500)">500</button>
                                <button type="button" class="btn btn-primary btn-amount" onclick="selectAmount(1000)">1000</button>
                                <button type="button" class="btn btn-primary btn-amount" onclick="selectAmount(2000)">2000</button>
                                <button type="button" class="btn btn-primary btn-amount" onclick="selectAmount(5000)">5000</button>
                                <input type="hidden" name="withdraw_amount" id="withdraw_amount">
                            </form>
                            </div>
                            <a href="<?php echo htmlspecialchars($origin . '.php?atm_name=' . urlencode($atm_name) . '&atm_bank_id=' . urlencode($atm_bank_id)); ?>" class="btn btn-success">Go Back</a>
                       
                    </div>
                    <!-- View Receipt Button -->
                    <?php if ($success_message): ?>
                        <div class="card-footer text-center">
                            <button type="button" class="btn btn-success" onclick="generateReceipt()">View Receipt</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        function selectAmount(amount) {
            document.getElementById("withdraw_amount").value = amount;
            document.getElementById("fastCashForm").submit();
        }

        function generateReceipt() {
            // Redirect to receipt.php
            window.location.href = 'receipt.php';
        }
    </script>
</body>
</html>
