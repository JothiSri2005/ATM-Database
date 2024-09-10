<?php
session_start();
$atm_name = $_GET['atm_name'] ?? '';
$atm_bank_id = $_GET['atm_bank_id'] ?? '';
$con = mysqli_connect("localhost", "root", "", "atm-tables") or die(mysqli_connect_error());

if(isset($_SESSION['card_number'])) {
    $card_number = $_SESSION['card_number'];
    $customer_query = "SELECT Customer.C_Id, Customer.F_name, Customer.L_name, Bank.Bank_Name
                      FROM Customer
                      INNER JOIN Customer_Card ON Customer.C_Id = Customer_Card.C_Id
                      INNER JOIN Card ON Customer_Card.Card_No = Card.Card_No
                      INNER JOIN Bank ON Card.Card_Bank_id = Bank.Bank_Id
                      WHERE Card.Card_No = '$card_number'";
    $customer_result = mysqli_query($con, $customer_query) or die(mysqli_error($con));
    if(mysqli_num_rows($customer_result) > 0) {
        $customer_row = mysqli_fetch_assoc($customer_result);
        $customer_id = $customer_row['C_Id'];
        $customer_name = $customer_row['F_name'] . ' ' . $customer_row['L_name'];
        $bank_name = $customer_row['Bank_Name'];
    } else {
        $error_message = "No customer found with the provided card number.";
    }
} else {
    header("Location: index1.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <style>.container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }</style>
    <title>Main_Page</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/custom.css">
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css">  
</head>
<body style="background: #B7DC83;background-size: 100%">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <a class="navbar-brand" href="#">
                    <marquee>Welcome to <?php echo htmlspecialchars($atm_name); ?> of <?php echo htmlspecialchars($atm_bank_id); ?>. 24x7 service is available...</marquee>
                </a>
            </ul>
             
        </div>
    </nav><br><br><br>
    <div class="row w-100">
        <div class="col" style="padding: 22px;padding-top: 0">
            <div class="jumbotron shadowBlack" style="padding: 25px;min-height: 241px;max-height: 241px;  background-image: url('images/a.jpg');background-position: center;opacity: 0.5;">
                <h4 class="display-5"  style="color: black; font-weight: bold;"><center>Welcome  <?php echo htmlspecialchars($customer_name); ?></center> </h4>
            </div>
            <div id="carouselExampleIndicators" class="carousel slide my-2 rounded-1 shadowBlack" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                    <?php
    echo '<img class="d-block w-100" src="images/' . htmlspecialchars($atm_name) . '.jpg" alt="First slide" style="max-height: 250px">';
?>

                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="row" style="padding: 22px;padding-top: 0">
                <div class="col">
                <div class="card shadowBlack" style=" color: white; padding: 20px;">
                        <div class="card-body">
                            <a href="pin_change.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>&origin=main_page1" class="btn btn-outline-success btn-block">Pin change</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                <div class="card shadowBlack" style=" color: white; padding: 20px;">
                        <div class="card-body">
                            <a href="balance.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>&origin=main_page1" class="btn btn-outline-success btn-block">Balance Inquiry</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="padding: 22px;padding-top: 0">
                <div class="col">
                <div class="card shadowBlack" style=" color: white; padding: 20px;">
                        <div class="card-body">
                            <a href="withdraw.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>&origin=main_page1" class="btn btn-outline-success btn-block">Cash Withdraw</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                <div class="card shadowBlack" style=" color: white; padding: 20px;">
                        <div class="card-body">
                            <a href="mini_statement.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>&origin=main_page1" class="btn btn-outline-success btn-block">Mini Statement</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="padding: 22px;padding-top: 0">
                <div class="col">
                <div class="card shadowBlack" style=" color: white; padding: 20px;">
                        <div class="card-body">
                            <a href="deposit.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>&origin=main_page1" class="btn btn-outline-success btn-block">Deposit</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                <div class="card shadowBlack" style=" color: white; padding: 20px;">
                        <div class="card-body">
                            <a href="fundtransfer.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>&origin=main_page1" class="btn btn-outline-success btn-block">Fund Transfer</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="padding: 22px">
                <div class="col">
                <div class="card shadowBlack" style=" color: white; padding: 20px;">
                        <div class="card-body">
                            <a href="fastcash.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>&origin=main_page1" class="btn btn-outline-success btn-block">Fast Cash</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                <div class="card shadowBlack" style=" color: white; padding: 20px;">
                        <div class="card-body">
                            <a href="help.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>&origin=main_page1" class="btn btn-outline-success btn-block">Help</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

