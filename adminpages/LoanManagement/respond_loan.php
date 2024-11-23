<?php
session_start();
include '../../connection.php';

// Include PHPMailer namespaces
require '../../PHPMailer/src/Exception.php';
require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

// Validate `loan_id` parameter
if (!isset($_GET['loan_id']) || !is_numeric($_GET['loan_id']) || intval($_GET['loan_id']) <= 0) {
    header('Location: ../manage_loan.php?message=Invalid loan ID.');
    exit();
}

$loanId = intval($_GET['loan_id']);

// Fetch loan details and customer email
$query = "SELECT l.LoanId, l.Amount, l.LoanType, l.InterestRate, l.StartDate, l.EndDate, c.Email 
          FROM loan l
          INNER JOIN customer c ON l.a_AccountID = c.account_accountID
          WHERE l.LoanId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $loanId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $email = $data['Email'];
    $loanType = $data['LoanType'];
    $amount = $data['Amount'];
    $interestRate = $data['InterestRate'];
    $startDate = $data['StartDate'];
    $endDate = $data['EndDate'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../manage_loan.php?message=Invalid email address.');
        exit();
    }

    // Calculate monthly installment
    $durationMonths = ceil((strtotime($endDate) - strtotime($startDate)) / (30 * 24 * 60 * 60));
    $monthlyInterestRate = $interestRate / 100 / 12;
    $monthlyInstallment = ($amount * $monthlyInterestRate) / (1 - pow(1 + $monthlyInterestRate, -$durationMonths));
    $monthlyInstallment = round($monthlyInstallment, 2);
    $nextPaymentDate = date('Y-m-d', strtotime('+1 month', strtotime($startDate)));

    // Prepare email
    $message = "Dear Customer,<br><br>";
    $message .= "Your loan application for a $loanType loan of amount $amount has been approved.<br>";
    $message .= "Loan details:<br>";
    $message .= "Monthly Installment: $monthlyInstallment<br>";
    $message .= "Next Payment Date: $nextPaymentDate<br><br>";
    $message .= "Thank you for choosing Aegis Bank.<br><br>";
    $message .= "Best regards,<br>Aegis Bank Support Team";

    // Send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USER');
        $mail->Password = getenv('SMTP_PASS');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('support@aegisbank.com', 'Aegis Bank Support');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Loan Approval Notification";
        $mail->Body = $message;

        $mail->send();

        // Update loan status
        $update_query = "UPDATE loan SET Status = 0 WHERE LoanId = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $loanId);
        $update_stmt->execute();

        header('Location: ../manage_loan.php?message=Loan approved and email sent.');
        exit();
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        header('Location: ../manage_loan.php?message=Mail could not be sent.');
        exit();
    }
} else {
    header('Location: ../manage_loan.php?message=Loan not found.');
    exit();
}
$conn->close();
?>
