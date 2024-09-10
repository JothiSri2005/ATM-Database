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
        header("Location: withdraw.php?atm_name=" . urlencode($atm_name) . "&atm_bank_id=" . urlencode($atm_bank_id) . "&origin=" . urlencode($_GET['origin'] ?? 'main_page1'));
        exit();
    }
}

if (isset($_SESSION['withdraw_success'])) {
    $success_message = $_SESSION['withdraw_success'];
    unset($_SESSION['withdraw_success']);
    $show_receipt_button = true; // Set this variable to true if there's a success message
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
    <title>Withdraw</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background:#96D678;background-size: 100%">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center" style="color: black; font-weight: bold;">
                        Withdrawal
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
                        <form id="withdrawForm" action="withdraw.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>&origin=<?php echo urlencode($origin); ?>" method="post">
                            <div class="form-group">
                                <label for="withdraw_amount_display">Enter Withdrawal Amount:</label>
                                <input type="hidden" name="withdraw_amount" id="withdraw_amount_hidden">
                                <input type="text" id="withdraw_amount_display" class="form-control" readonly>
                            </div>
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="btn-group-vertical w-100">
                                        <div class="btn-group w-100">
                                            <button type="button" class="btn btn-secondary" onclick="appendToDisplay(1)">1</button>
                                            <button type="button" class="btn btn-secondary" onclick="appendToDisplay(2)">2</button>
                                            <button type="button" class="btn btn-secondary" onclick="appendToDisplay(3)">3</button>
                                        </div>
                                        <div class="btn-group w-100">
                                            <button type="button" class="btn btn-secondary" onclick="appendToDisplay(4)">4</button>
                                            <button type="button" class="btn btn-secondary" onclick="appendToDisplay(5)">5</button>
                                            <button type="button" class="btn btn-secondary" onclick="appendToDisplay(6)">6</button>
                                        </div>
                                        <div class="btn-group w-100">
                                            <button type="button" class="btn btn-secondary" onclick="appendToDisplay(7)">7</button>
                                            <button type="button" class="btn btn-secondary" onclick="appendToDisplay(8)">8</button>
                                            <button type="button" class="btn btn-secondary" onclick="appendToDisplay(9)">9</button>
                                        </div>
                                        <div class="btn-group w-100">
                                            <button type="button" class="btn btn-secondary" onclick="appendToDisplay(0)">0</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-danger btn-block" onclick="clearDisplay()">Clear</button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Withdraw</button><br><br>
                            <a href="<?php echo htmlspecialchars($origin . '.php?atm_name=' . urlencode($atm_name) . '&atm_bank_id=' . urlencode($atm_bank_id)); ?>" class="btn btn-primary">Go Back</a>
                        </form>
                    </div>
                    <?php if (isset($show_receipt_button) && $show_receipt_button): ?>
                        <div class="card-footer text-center">
                            <button type="button" class="btn btn-success mt-3"  onclick="generateReceipt()" >Generate Receipt</button>
                    </div>
                        <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Include Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function appendToDisplay(digit) {
            var currentAmount = document.getElementById("withdraw_amount_hidden").value;
            currentAmount += digit;
            document.getElementById("withdraw_amount_hidden").value = currentAmount;
            document.getElementById("withdraw_amount_display").value = currentAmount;
        }

        function clearDisplay() {
            document.getElementById("withdraw_amount_hidden").value = "";
            document.getElementById("withdraw_amount_display").value = "";
        }

        function generateReceipt() {
            // Redirect to receipt

            // Redirect to receipt.php
            window.location.href = 'receipt.php';
        }
    </script>
</body>
</html>

