<?php
session_start();
if (!isset($_SESSION['card_number'])) {
    header("Location: index1.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "atm-tables") or die(mysqli_connect_error());
$card_number = $_SESSION['card_number'];

// Fetch account details
$account_query = "
    SELECT *
    FROM account_statement_view
    WHERE Card_No = '$card_number'
";
$account_result = mysqli_query($con, $account_query) or die(mysqli_error($con));

if (mysqli_num_rows($account_result) == 0) {
    die("No account found with the provided card number.");
}

$account_row = mysqli_fetch_assoc($account_result);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Info</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/custom.css">
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css">  
</head>
<body style="background:#96D678;background-size: 100%">
<br><br>

<div class="container">
    <div class="card w-100 text-center shadowBlue">
        <div class="card-header text-center" style="color: black; font-weight: bold;">
            Account Information
        </div>
        <div class="card-body">
    <table class="table table-striped table-dark w-75 mx-auto">
        <tbody>
            <tr>
                <td scope="row"><strong>Account Number:</strong></td>
                <td><?php echo $account_row['account_number']; ?></td>
            </tr>
            <tr>
                <td scope="row"><strong>Account Type:</strong></td>
                <td><?php echo $account_row['account_type']; ?></td>
            </tr>
            <tr>
                <td scope="row"><strong>Bank Name:</strong></td>
                <td><?php echo $account_row['bank_name']; ?></td>
            </tr>
            <tr>
                <td scope="row"><strong>Customer Name:</strong></td>
                <td><?php echo $account_row['customer_name']; ?></td>
            </tr>
            <tr>
                <td scope="row"><strong>Customer Phone:</strong></td>
                <td><?php echo $account_row['customer_phone']; ?></td>
            </tr>
            <tr>
                <td scope="row"><strong>Customer Address:</strong></td>
                <td><?php echo $account_row['customer_address']; ?></td>
            </tr>
        </tbody>
    </table>
</div>

        <div class="card-footer text-muted text-center">
            <a href="mini_statement.php?atm_name=<?php echo urlencode($_GET['atm_name'] ?? ''); ?>&atm_bank_id=<?php echo urlencode($_GET['atm_bank_id'] ?? ''); ?>" class="btn btn-primary">Go Back</a>
        </div>
    </div>
</div>

</body>
</html>
