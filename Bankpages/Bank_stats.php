<?php
// Start session and include database connection
session_start();
include('../connection.php');

// Include PHPMailer for email functionality
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../fpdf186/fpdf.php';

// Fetch customerId and accountId from the session
$customerId = $_SESSION['customerId'] ?? null;
$accountId = $_SESSION['AccountId'] ?? null;

if (!$customerId || !$accountId) {
    echo "<script>alert('User session expired. Please log in again.'); window.location.href = '../Bank_logout.php';</script>";
    exit;
}

// Fetch user's email and name using customerId
$stmt = $conn->prepare("SELECT Email, Name FROM Customer WHERE customerID = ?");
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<script>alert('Customer not found.');</script>";
    exit;
}

$email = $user['Email'];
$name = $user['Name'];

// Handle PDF generation and email sending
$successMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch initial balance from the account table
    $currentBalance = 0;
    $balanceStmt = $conn->prepare("SELECT balance FROM Account WHERE accountID = ?");
    $balanceStmt->bind_param("i", $accountId);
    $balanceStmt->execute();
    $balanceStmt->bind_result($currentBalance);
    $balanceStmt->fetch();
    $balanceStmt->close();

    // Fetch transactions for the account
    $stmt = $conn->prepare("SELECT transactionDate AS date, transactionType AS description, transactionAmount AS amount FROM transaction WHERE account_AccountId = ?");
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (empty($transactions)) {
        echo "<script>alert('No transactions found for this account.');</script>";
        exit;
    }

    // Compute running balance
    foreach ($transactions as &$transaction) {
        $currentBalance += $transaction['amount'];
        $transaction['balance'] = $currentBalance;
    }

    // Ensure statements directory exists
    $statementsDir = '../statements/';
    if (!is_dir($statementsDir)) {
        mkdir($statementsDir, 0777, true);
    }

    // Generate PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Centered Logo
    $pdf->Image('../logo.png', 80, 10, 50); // Centering based on page width (80 x-axis for A4)
    $pdf->Ln(30); // Line break after the logo

    // Greeting Text
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 10, "Dear $name,", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 10, "Your account statement is detailed below.", 0, 1, 'C');
    $pdf->Ln(10);

    // Table Header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 10, 'Date', 1);
    $pdf->Cell(80, 10, 'Description', 1);
    $pdf->Cell(30, 10, 'Amount', 1);
    $pdf->Cell(40, 10, 'Balance', 1);
    $pdf->Ln();

    // Table Content
    foreach ($transactions as $transaction) {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(40, 10, $transaction['date'], 1);
        $pdf->Cell(80, 10, $transaction['description'], 1);
        $pdf->Cell(30, 10, number_format($transaction['amount'], 2), 1);
        $pdf->Cell(40, 10, number_format($transaction['balance'], 2), 1);
        $pdf->Ln();
    }

    // Save PDF
    $filePath = $statementsDir . 'statement_' . $accountId . '.pdf';
    $pdf->Output('F', $filePath);

    // Send email with PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'itsa.ansari@gmail.com';
        $mail->Password = 'jubjhrtppgotstfp';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('itsa.ansari@gmail.com', 'Aegis Bank');
        $mail->addAddress($email, $name);

        // Attach PDF
        $mail->addAttachment($filePath);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your Aegis Bank Statement';
        $mail->Body = "
            <p>Dear $name,</p>
            <p>Please find attached your bank statement for your account <strong>$accountId</strong>.</p>
            <p>Thank you for banking with us.</p>
            <p><strong>Aegis Bank Support Team</strong></p>
        ";

        $mail->send();
        $successMessage = "Statement sent successfully to $email.";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Statements</title>
    <link rel="stylesheet" href="../BankHome.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            font-family: 'Gill Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        #main {
            margin: 0; /* Remove any default margin */
            padding: 0;
            height: 88%;
        }

        .container {
            width: 30%;
            height: 35%;
            margin: auto;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;

            /* Flexbox for centering */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;

            /* Adjust vertical position */
            position: absolute;
            top: 45%; /* Changed from 50% to 45% to reduce gap */
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .container h1 {
            text-align: center;
            color: #004aad;
            font-size: 24px;
        }

        .container form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .container button {
            background-color: #004aad;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .container button:hover {
            background-color: #003580;
        }

        .success-message {
            color: green;
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
            padding: 10px;
            border: 1px solid green;
            border-radius: 5px;
            background-color: #e8f5e9;
        }

        footer {
            text-align: center;
            margin-top: 30px;
            color: #888;
        }
    </style>
</head>
<body>

<div id="main" style="height: 90%;">
    <!-- Header Section -->
    <header id="header">
        <div id="logo">
            <img src="../logo.png" width="75px" alt="Bank Logo">
        </div>
        <nav class="nav-links">
            <a href="../Bankhome.php">Accounts</a>
            <a href="Bank_transaction.php">Transaction</a>
            <a href="Bank_pay.php">Pay</a>
            <a href="Bank_loan.php">Loans</a>
            <a href="Bank_stats.php">Statements</a>
            <a href="Bank_support.php">Support</a>
            <a href="Bank_profile.php">Profile</a>
            <a href="Bank_logout.php">Logout</a>
        </nav>
        <!-- Icons -->
        <div class="icon-group">
            <div class="icon"><i class="ri-search-line"></i></div>
            <div class="icon"><i class="ri-notification-3-line"></i></div>
        </div>
    </header>
    <div class="container">
        <h1 style="color: #032D60;">Generate Statement</h1>
        <form method="POST">
            <button type="submit" style="background-color: #032D60; color:white; margin-top:5%;">Generate and Email Statement</button>
        </form>
        <?php if ($successMessage): ?>
            <div class="success-message"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>
    </div>
</div>
<footer id="footer">
    <div class="footer-content">
        <p>© Copyright 2024 Aegis, Inc. <u>All rights reserved.</u> Various trademarks held by their respective owners.</p>
    </div>
</footer>
</body>
</html>