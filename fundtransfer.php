<?php
session_start();

// Check if the user is logged in and card number is set in session
if (!isset($_SESSION['card_number'])) {
    header("Location: index1.php");
    exit();
}

$success_message = "";
$error_message = "";
$show_receipt_button = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission
    // Validate and sanitize input
    $receiver_account_number = $_POST['receiver_account_number'];
    $transfer_amount = $_POST['transfer_amount'];
    if ($transfer_amount <= 0 || $transfer_amount < 100 || $transfer_amount > 50000) {
        $error_message = "Please enter a valid transfer amount between 100 and 50,000.";
    } else {
        // Get atm_name and atm_bank_id from GET parameters
        $atm_name = $_GET['atm_name'] ?? '';
        $atm_bank_id = $_GET['atm_bank_id'] ?? '';

        // Call the stored procedure to make the transfer
        $con = mysqli_connect("localhost", "root", "", "atm-tables") or die(mysqli_connect_error());
        $card_number = $_SESSION['card_number'];
        $stmt = $con->prepare("CALL MakeTransferAndUpdateBalance(?, ?, ?, ?)");
        $stmt->bind_param("iids", $card_number, $receiver_account_number, $transfer_amount, $atm_name); // Corrected parameter binding
        $stmt->execute();
        $stmt->close();
        mysqli_close($con);

        // Store success message in session
        $_SESSION['transfer_success'] = "Transfer of $transfer_amount successfully made to account $receiver_account_number.";

        // Redirect to the same page to show the success message
        header("Location: fundtransfer.php?atm_name=" . urlencode($atm_name) . "&atm_bank_id=" . urlencode($atm_bank_id) . "&origin=" . urlencode($_GET['origin'] ?? 'main_page1'));
        exit();
    }
}

if (isset($_SESSION['transfer_success'])) {
    $success_message = $_SESSION['transfer_success'];
    unset($_SESSION['transfer_success']);
    $show_receipt_button = true;
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
    <title>Fund Transfer</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background:#F7EF8A;background-size: 100%">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center" style="color: black; font-weight: bold;">
                        Fund Transfer
                    </div>
                    <div class="card-body">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
                        <form id="transferForm" action="fundtransfer.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>&origin=<?php echo urlencode($origin); ?>" method="post">
                            <div class="form-group">
                                <label for="receiver_account_number">Receiver's Account Number:</label>
                                <input type="text" name="receiver_account_number" id="receiver_account_number" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="transfer_amount">Enter Transfer Amount:</label>
                                <input type="number" name="transfer_amount" id="transfer_amount" class="form-control" min="100" max="50000" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Transfer</button><br><br>
                            <a href="<?php echo htmlspecialchars($origin . '.php?atm_name=' . urlencode($atm_name) . '&atm_bank_id=' . urlencode($atm_bank_id)); ?>" class="btn btn-primary">Go Back</a>
                        </form>
                    </div>
                    <?php if ($show_receipt_button): ?>
                        <div class="card-footer text-center">
                            <button type="button" class="btn btn-success mt-3"  onclick="generateReceipt()">Generate Receipt</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Include Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function generateReceipt() {
            // Redirect to receipt.php
            window.location.href = 'receipt.php';
        }
    </script>
</body>
</html>
