<?php
require '../../fpdf186/fpdf.php';

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate input data
if (!isset($data['transactionChart']) || !isset($data['balanceChart'])) {
    die('Invalid input');
}

$transactionChart = $data['transactionChart'];
$balanceChart = $data['balanceChart'];

// Decode base64 images and save as temporary files
$transactionImagePath = '../../tmp/transaction_chart.png';
$balanceImagePath = '../../tmp/balance_chart.png';

// Create the tmp directory if it doesn't exist
if (!file_exists('../../tmp')) {
    if (!mkdir('../../tmp', 0777, true)) {
        die('Failed to create tmp directory');
    }
}

// Function to decode base64 images
function decode_base64_image($base64_image, $output_path) {
    if (preg_match('/^data:image\/\w+;base64,/', $base64_image)) {
        $base64_image = substr($base64_image, strpos($base64_image, ',') + 1);
    }
    $decoded_image = base64_decode($base64_image);
    if ($decoded_image === false) {
        return false;
    }
    return file_put_contents($output_path, $decoded_image) !== false;
}

// Decode and save transaction chart
if (!decode_base64_image($transactionChart, $transactionImagePath)) {
    die('Failed to decode and save transaction chart');
}

// Decode and save balance chart
if (!decode_base64_image($balanceChart, $balanceImagePath)) {
    die('Failed to decode and save balance chart');
}

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Add Title
$pdf->Cell(0, 10, 'Bank Report', 0, 1, 'C');

// Add Transaction Chart
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Transaction Trends', 0, 1, 'C');
$pdf->Ln(5); // Add line gap
$pdf->Image($transactionImagePath, 10, $pdf->GetY(), 180, 60);

// Add Balance Chart
$pdf->Ln(65); // Space for image height and padding
$pdf->Cell(0, 10, 'Balance Trends', 0, 1, 'C');
$pdf->Ln(5); // Add line gap
$pdf->Image($balanceImagePath, 10, $pdf->GetY(), 180, 60);

// Clean temporary files
if (file_exists($transactionImagePath)) {
    unlink($transactionImagePath);
}
if (file_exists($balanceImagePath)) {
    unlink($balanceImagePath);
}

// Output PDF - Direct download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Bank_Report.pdf"');
header('Content-Length: ' . strlen($pdf->Output('S')));
ob_clean(); // Clean output buffer
flush();    // Flush output buffer
echo $pdf->Output('S'); // Send the PDF as a string to the browser
?>
