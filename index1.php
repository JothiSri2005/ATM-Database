<?php 
$con = mysqli_connect("localhost", "root", "", "atm-tables") or die(mysqli_connect_error());
session_start();

// Initialize variables
$atm_name = $atm_bank_id = $atm_amount = $error_message = '';
$selected_atm_id = '';
$availability_message = '';

// Retrieve ATM ID if provided
if(isset($_POST['atm_numbers'])) {
    $selected_atm_id = $_POST['atm_numbers'];
}

// If ATM ID is provided, retrieve ATM details
if (!empty($selected_atm_id)) {
    $select_atm_query = "SELECT ATM_Name, ATM_Bank_Id, ATM_Amount FROM ATM_Machine WHERE ATM_Id = '$selected_atm_id'";
    $select_atm_result = mysqli_query($con, $select_atm_query) or die(mysqli_error($con));
    
    if(mysqli_num_rows($select_atm_result) > 0) {
        $row = mysqli_fetch_assoc($select_atm_result);
        $atm_name = $row['ATM_Name'];
        $atm_bank_id = $row['ATM_Bank_Id'];
        $atm_amount = $row['ATM_Amount'];
        
        // Determine the availability message based on ATM amount
        if ($atm_amount >= 500) {
            $availability_message = '<span style="color: green; font-weight: bold;">Amount Available</span>';
        } else {
            $availability_message = '<span style="color: red; font-weight: bold;">Amount Not Available</span>';
        }
    } else {
        // Handle case where no ATM is found with the given ID
        $error_message = "No ATM found with the selected ID.";
    }
} else {
    // Handle case where no ATM ID is provided
    $error_message = "No ATM ID provided.";
}

// Check if the form is submitted
if(isset($_POST['get'])) {
    $card_number = $_POST['Cardno'];
    $pin_number = $_POST['pin'];
    
    // Check if the card number and PIN are correct
    $check_card_query = "SELECT * FROM Card WHERE Card_No = '$card_number' AND Pin = '$pin_number'";
    $check_card_result = mysqli_query($con, $check_card_query) or die(mysqli_error($con));
    
    // If the card number and PIN are correct
    if(mysqli_num_rows($check_card_result) > 
    0) {
        $card_data = mysqli_fetch_assoc($check_card_result);
        $card_type = $card_data['Card_Type'];
        $expiry_date = strtotime($card_data['Card_ExpiryDate']);
        $current_date = strtotime(date("Y-m-d"));
   

        // Check if the card has expired
        if ($expiry_date < $current_date) {
            $error_message = '<div class="alert alert-danger">The card has expired.</div>';
        } else {
            // Check the card type and redirect accordingly
            if($card_type == 'Debit') {
                // Redirect to main_page1.php for Debit cards
                $_SESSION['card_number'] = $card_number;
                header("Location: main_page1.php?atm_name=$atm_name&atm_bank_id=$atm_bank_id");
                exit();
            } elseif($card_type == 'Credit') {
                // Redirect to main_page2.php for Credit cards
                $_SESSION['card_number'] = $card_number;
                header("Location: main_page2.php?atm_name=$atm_name&atm_bank_id=$atm_bank_id");
                exit();
            }
        }
    } else {
        // If the card number and PIN are incorrect, display an error message
        $error_message = '<div class="alert alert-danger">Incorrect card number or PIN. Please try again.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet
    " type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/custom.css">
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css">
</head>
<body style="background:#96D678;background-size: 100%">
<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color: #f2f2f2;">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <a class="navbar-brand" href="#">
                    <marquee>Welcome to <?php echo $atm_name; ?> of <?php echo $atm_bank_id; ?>. 24x7 service is available...</marquee>
                </a>
            </ul>
            <span class="btn" title="Available amount in the ATM" style="color:white" >
                <?php echo $availability_message; ?>
            </span>
            <br>
        </div>
    </nav><br><br><br>

    <div class="container">
        <div class="card w-75 mx-auto">
            <div class="card-header text-center">
                Funds Transfer
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <?php if(isset($error_message)) { ?>
                        <div><?php echo $error_message; ?></div>
                    <?php } ?>
                    <div class="alert alert-success w-50 mx-auto">
                        <h5>Insert Card</h5>
                        <input type="hidden" name="atm_numbers" value="<?php echo $selected_atm_id; ?>">
                        <input type="text" name="Cardno" class="form-control" placeholder="Enter Card number" required><br>
                        <input type="password" name="pin" class="form-control" placeholder="Enter pin number" required>
                        <button type="submit" name="get" class="btn btn-primary btn-block btn-sm my-1">Enter</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-muted"></div>
        </div>
    </div>
</body>
</html>
