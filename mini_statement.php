<?php
session_start();
if (!isset($_SESSION['card_number'])) {
    header("Location: index1.php");
    exit();
}

$atm_name = isset($_GET['atm_name']) ? $_GET['atm_name'] : '';
$atm_bank_id = isset($_GET['atm_bank_id']) ? $_GET['atm_bank_id'] : '';
$origin = isset($_GET['origin']) ? $_GET['origin'] : 'main_page1';

$con = mysqli_connect("localhost", "root", "", "atm-tables") or die(mysqli_connect_error());
$card_number = $_SESSION['card_number'];

// Fetch customer details
$customer_query = "
    SELECT Customer.C_Id, Card.Card_No
    FROM Customer
    INNER JOIN Customer_Card ON Customer.C_Id = Customer_Card.C_Id
    INNER JOIN Card ON Customer_Card.Card_No = Card.Card_No
    WHERE Card.Card_No = '$card_number'
";
$customer_result = mysqli_query($con, $customer_query) or die(mysqli_error($con));

if (mysqli_num_rows($customer_result) == 0) {
    die("No customer found with the provided card number.");
}

$customer_row = mysqli_fetch_assoc($customer_result);
$customer_id = $customer_row['C_Id'];

// Fetch transactions
$transaction_query = "
    SELECT T.Transaction_Id, T.Transaction_type, T.Transaction_date, T.Transaction_time, T.Transaction_amount, T.Transaction_status, 
           T.sender_id, T.receiver_id
    FROM Transaction_Sender_Receiver_View T
    WHERE T.sender_id = '$customer_id' OR T.receiver_id = '$customer_id'
    ORDER BY T.Transaction_date DESC, T.Transaction_time DESC
    LIMIT 10
";
$transaction_result = mysqli_query($con, $transaction_query) or die(mysqli_error($con));

$transactions = [];
while ($row = mysqli_fetch_assoc($transaction_result)) {
    $transactions[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mini Statement</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/custom.css">
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css">  
    <style>
        .withdraw {
            background-color: #f2dede;
        }
        .deposit {
            background-color: #dff0d8;
        }
        .transfer {
            background-color: #fcf8e3;
        }
    </style>
</head>
<body style="background:#F6ADCE;background-size: 100%">
<br><br>

<div class="container">
    <div class="card w-75 mx-auto">
        <div class="card-header text-center" style="color: black; font-weight: bold;">
            Mini Statement
        </div>
        <div class="card-body">
            <?php foreach ($transactions as $transaction): ?>
                <?php
                if ($transaction['Transaction_type'] == 'Withdrawal') {
                    echo "<div class='alert alert-secondary'>You withdrew Rs." . $transaction['Transaction_amount'] . " from your account at " . $transaction['Transaction_date'] . " " . $transaction['Transaction_time'] . "</div>";
                } elseif ($transaction['Transaction_type'] == 'Deposit') {
                    echo "<div class='alert alert-success deposit'>You deposited Rs." . $transaction['Transaction_amount'] . " in your account at " . $transaction['Transaction_date'] . " " . $transaction['Transaction_time'] . "</div>";
                } elseif ($transaction['Transaction_type'] == 'Transfer') {
                    $receiver_id = $transaction['receiver_id'];
                    $receiver_query = "SELECT F_name, L_name FROM Customer WHERE C_Id = '$receiver_id'";
                    $receiver_result = mysqli_query($con, $receiver_query) or die(mysqli_error($con));
                    $receiver_row = mysqli_fetch_assoc($receiver_result);
                    $receiver_name = $receiver_row['F_name'] . ' ' . $receiver_row['L_name'];

                    echo "<div class='alert alert-warning transfer'>Transfer of Rs." . $transaction['Transaction_amount'] . " from your account at " . $transaction['Transaction_date'] . " " . $transaction['Transaction_time'] . " to account no. " . $transaction['receiver_id'] . " of " . $receiver_name . "</div>";
                }
                ?>
            <?php endforeach; ?>
        </div>
        <div class="card-footer text-muted text-center">
            <a href="<?php echo htmlspecialchars($_GET['origin'] ?? 'main_page1'); ?>.php?atm_name=<?php echo urlencode($atm_name); ?>&atm_bank_id=<?php echo urlencode($atm_bank_id); ?>" class="btn btn-primary">Back to Main Page</a>
            <form action="account_info.php" method="GET" style="display: inline;">
                <input type="hidden" name="atm_name" value="<?php echo htmlspecialchars($atm_name); ?>">
                <input type="hidden" name="atm_bank_id" value="<?php echo htmlspecialchars($atm_bank_id); ?>">
                <button type="submit" class="btn btn-primary">Get Account Info</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
