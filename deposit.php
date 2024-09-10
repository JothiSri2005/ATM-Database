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
    $deposit_amount = $_POST['deposit_amount'];
    if ($deposit_amount <= 0) {
        $error_message = "Please enter a valid positive deposit amount.";
    } else {
        // Get atm_name and atm_bank_id from GET parameters
        $atm_name = $_GET['atm_name'] ?? '';
        $atm_bank_id = $_GET['atm_bank_id'] ?? '';

        // Call the stored procedure to make the deposit
        $con = mysqli_connect("localhost", "root", "", "atm-tables") or die(mysqli_connect_error());
        $card_number = $_SESSION['card_number'];
        $stmt = $con->prepare("CALL MakeDepositAndUpdateBalance(?, ?, ?)");
        $stmt->bind_param("ids", $card_number, $deposit_amount, $atm_name);
        $stmt->execute();
        $stmt->close();
        mysqli_close($con);

        // Store success message in session
        $_SESSION['deposit_success'] = "Deposit of $deposit_amount successfully made.";

        // Redirect to the same page to show the success message
        header("Location: deposit.php?atm_name=" . urlencode($atm_name) . "&atm_bank_id=" . urlencode($atm_bank_id));
        exit();
    }
}

if (isset($_SESSION['deposit_success'])) {
    $success_message = $_SESSION['deposit_success'];
    unset($_SESSION['deposit_success']);
    $show_receipt_button = true; // Set this variable to true if there's a success message
}

// Get atm_name and atm_bank_id from GET parameters
$atm_name = $_GET['atm_name'] ?? '';
$atm_bank_id = $_GET['atm_bank_id'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background:#D6B4FC;background-size: 100%">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center" style="color: black; font-weight: bold;">
                        Deposit
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
                        <div id="js-error-message" class="alert alert-danger" role="alert" style="display: none;"></div>
                        <form id="depositForm" action="deposit.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>" method="post" onsubmit="return validateForm()">
                            <div class="form-group">
                                <label for="deposit_amount_display">Enter Deposit Amount:</label>
                                <input type="hidden" name="deposit_amount" id="deposit_amount_hidden">
                                <input type="text" id="deposit_amount_display" class="form-control" readonly>
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
                            <button type="submit" class="btn btn-primary mt-3">Deposit</button>
                            <a href="main_page1.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>" class="btn btn-secondary mt-3">Go Back</a>
                        </form>
                    </div>
                    <!-- View Receipt Button -->
                    <?php if (isset($show_receipt_button) && $show_receipt_button): ?>
                        <div class="card-footer text-center">
                            <button type="button" class="btn btn-success" onclick="generateReceipt()">View Receipt</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        function appendToDisplay(digit) {
            var currentAmount = document.getElementById("deposit_amount_hidden").value;
            currentAmount += digit;
            document.getElementById("deposit_amount_hidden").value = currentAmount;
            document.getElementById("deposit_amount_display").value = currentAmount;
        }

        function clearDisplay() {
            document.getElementById("deposit_amount_hidden").value = "";
            document.getElementById("deposit_amount_display").value = "";
        }

        function validateForm() {
            var amount = parseFloat(document.getElementById("deposit_amount_hidden").value);
            var errorMessageDiv = document.getElementById("js-error-message");
            if (isNaN(amount) || amount <= 0) {
                errorMessageDiv.innerText = "Please enter a valid positive deposit amount.";
                errorMessageDiv.style.display = "block";
                return false;
            }
            errorMessageDiv.style.display = "none";
            return true;
        }

        function generateReceipt() {
            // Redirect to receipt.php
            window.location.href = 'receipt.php';
        }
    </script>
</body>
</html>
