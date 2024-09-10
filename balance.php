<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "atm-tables") or die(mysqli_connect_error());

// Check if card number is set in session
if(isset($_SESSION['card_number'])) {
    $card_number = $_SESSION['card_number'];
    
   $balance_query = "SELECT CheckBalance('$card_number') AS Card_Balance";
    
    $balance_result = mysqli_query($con, $balance_query) or die(mysqli_error($con));
    
    // Check if balance is fetched
    if(mysqli_num_rows($balance_result) > 0) {
        $balance_row = mysqli_fetch_assoc($balance_result);
        $balance_amount = $balance_row['Card_Balance'];
    } else {
        // Handle case where no balance is found
        $error_message = "No balance found for the provided card number.";
    }
} else {
    // Redirect back to index1.php if card number is not set in session
    header("Location: index1.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Balance Inquiry</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/custom.css">
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css">  
</head>
<body style="background:#ffee58;background-size: 100%">
    <div class="container mt-5">
        <h2 class="text-center">Balance Inquiry</h2>
        <div class="row justify-content-center mt-3 ">
            <div class="col-md-6">
                <div class="card shadowBlack">
                    <div class="card-body">
                        <?php if(isset($balance_amount)): ?>
                            <h4 class="card-title">Your Current Balance</h4>
                            <p class="card-text"><?php echo $balance_amount; ?></p>
                        <?php else: ?>
                            <p class="text-danger"><?php echo $error_message; ?></p>
                        <?php endif; ?>
                        <a href="<?php echo htmlspecialchars($_GET['origin'] ?? 'main_page1'); ?>.php?atm_name=<?php echo urlencode($_GET['atm_name'] ?? ''); ?>&atm_bank_id=<?php echo urlencode($_GET['atm_bank_id'] ?? ''); ?>" class="btn btn-primary">Go Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
