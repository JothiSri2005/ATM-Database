<?php 
require_once('fpdf/fpdf.php'); // Make sure to include the FPDF library

$con = mysqli_connect("localhost", "root", "", "atm-tables") or die(mysqli_connect_error());
session_start();

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Set font
$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(270, 10, '********************************', 0, 1, 'C'); // Add line

// Execute the stored procedure to fetch the latest transaction details
$sql = "CALL GetLatestTransactionSenderReceiver()";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Add bank name header
        $pdf->Cell(270, 10, '            ' . $row['Bank_Name'] . '             ', 0, 1, 'C'); // Add bank name
        $pdf->Cell(270, 10, '********************************', 0, 1, 'C'); // Add line
        $pdf->Ln(5); // Add extra line spacing

        // Create an array of variables and their corresponding values
        $data = array(
            'ATM Location' => $row['ATM_Location'],
            'Transaction Date' => $row['Transaction_date'],
            'Transaction Time' => $row['Transaction_time'],
            'ATM ID' => $row['ATM_Id'],
            'Card Number' => $row['Customer_Card_Number'],
            'Transaction Type' => $row['Transaction_type'],
            'Transaction ID' => $row['Transaction_Id'],
            'Transaction Amount' => 'RS.' . $row['Transaction_amount'],
            'Available Balance' => 'RS.' . $row['Card_Balance'],
            'Customer Name' => $row['Customer_Name']
        );
        $pdf->SetFont('Courier', 'B', 14);

        // Loop through the data array and output variables and values in a tabular format
        foreach ($data as $variable => $value) {
            $pdf->Cell(70, 10, $variable, 1, 0, 'L');
            $pdf->Cell(200, 10, $value, 1, 1, 'C');
        }

        $pdf->Ln(5); // Add extra line spacing
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(270, 10, '********************************', 0, 1, 'C'); // Add line
        $pdf->Cell(270, 10, '   Thank you for using ' . $row['Bank_Name'] . ' ATM.   ', 0, 1, 'C');
        $pdf->Cell(270, 10, '********************************', 0, 1, 'C'); // Add line
    }
} else {
    // If no transactions found
    $pdf->Cell(270, 10, 'No transactions found', 0, 1, 'C');
}

// Close the database connection
$con->close();

// Output PDF
$pdf->Output();
?>
