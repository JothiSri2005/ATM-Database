<?php
session_start();

// Check if the user is logged in and card number is set in session
if(isset($_SESSION['card_number'])) {
    $card_number = $_SESSION['card_number'];
    
    // Check if there is a success message stored in session
    if(isset($_SESSION['pin_change_success'])) {
        // Display the success message and then unset it
        echo '<div class="alert alert-info" role="alert">' . $_SESSION['pin_change_success'] . '</div>';
        unset($_SESSION['pin_change_success']);
    }
    
    // Retrieve the new PIN from the form submission
    if(isset($_POST['new_pin'])) {
        $new_pin = $_POST['new_pin'];
        
        // Check if the PIN is exactly four digits
        if(strlen($new_pin) === 4 && ctype_digit($new_pin)) {
            // Call the stored procedure to change the PIN
            $con = mysqli_connect("localhost", "root", "", "atm-tables") or die(mysqli_connect_error());
            $stmt = $con->prepare("CALL ChangeCardPIN(?, ?)");
            $stmt->bind_param("ii", $card_number, $new_pin);
            $stmt->execute();
            $stmt->bind_result($message);
            $stmt->fetch();
            
            // Store success message in session
            $_SESSION['pin_change_success'] = $message;
            
            $stmt->close();
            mysqli_close($con);
            
            // Redirect to prevent form resubmission on refresh
            header("Location: pin_change.php?atm_name=" . urlencode($_GET['atm_name'] ?? '') . "&atm_bank_id=" . urlencode($_GET['atm_bank_id'] ?? '') . "&origin=" . urlencode($_GET['origin'] ?? 'main_page1'));
            exit();
        } else {
            // Display error message if PIN is not exactly four digits
            echo '<div class="alert alert-danger" role="alert">PIN should contain exactly four digits!</div>';
        }
    }
} else {
    // Redirect back to index1.php if card number is not set in session
    header("Location: index1.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change PIN</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background:#FFB963;background-size: 100%">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center" style="color: black; font-weight: bold;">
                        Change PIN
                    </div>
                    <div class="card-body">
                        <form id="pinForm" action="" method="post">
                            <div class="form-group">
                                <label for="new_pin">New PIN:</label>
                                <input type="hidden" name="new_pin" id="new_pin_hidden">
                                <input type="text" name="new_pin_display" id="new_pin_display" class="form-control" readonly>
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
                            <button type="submit" class="btn btn-primary mt-3">Submit</button>
                        </form>
                    </div>
                    <div class="card-footer">
                        <a href="<?php echo htmlspecialchars($_GET['origin'] ?? 'main_page1'); ?>.php?atm_name=<?php echo urlencode($_GET['atm_name'] ?? ''); ?>&atm_bank_id=<?php echo urlencode($_GET['atm_bank_id'] ?? ''); ?>" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function appendToDisplay(digit) {
            var currentPin = document.getElementById("new_pin_hidden").value;
            currentPin += digit;
            document.getElementById("new_pin_hidden").value = currentPin;
            document.getElementById("new_pin_display").value = "*".repeat(currentPin.length); // Mask PIN display
        }

        function clearDisplay() {
            document.getElementById("new_pin_hidden").value = "";
            document.getElementById("new_pin_display").value = "";
        }
    </script>
</body>
</html>
