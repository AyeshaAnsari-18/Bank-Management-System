<?php
header("Content-Type: application/json"); // Set response type to JSON
include('../connection.php');

// Include PHPMailer namespaces
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Handle POST requests only
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Parse the input
    $input = json_decode(file_get_contents("php://input"), true);

    // Validate required fields
    if (!isset($input['receiver_account'], $input['amount']) || $input['amount'] <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required, and the amount must be greater than 0.']);
        exit();
    }

    // Extract and sanitize input
    $receiverAccount = sanitize_input($input['receiver_account']);
    $amount = (float)sanitize_input($input['amount']);

    // Start a session to retrieve sender details
    session_start();
    $senderAccountId = $_SESSION['accountID'] ?? null;
    if (!$senderAccountId) {
        echo json_encode(['status' => 'error', 'message' => 'Sender account not found.']);
        exit();
    }

    // Fetch sender account balance
    $stmt = $conn->prepare("SELECT Balance, Email, Name FROM account JOIN Customer ON account.AccountID = Customer.account_accountID WHERE AccountID = ?");
    $stmt->bind_param("s", $senderAccountId);
    $stmt->execute();
    $sender = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$sender || $sender['Balance'] < $amount) {
        echo json_encode(['status' => 'error', 'message' => 'Insufficient funds.']);
        exit();
    }

    // Check if receiver account exists
    $stmt = $conn->prepare("SELECT AccountID FROM account WHERE AccountID = ?");
    $stmt->bind_param("s", $receiverAccount);
    $stmt->execute();
    $receiver = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$receiver) {
        echo json_encode(['status' => 'error', 'message' => 'Receiver account not found.']);
        exit();
    }

    // Process the transaction
    $conn->begin_transaction();
    try {
        // Deduct amount from sender
        $stmt = $conn->prepare("UPDATE account SET Balance = Balance - ? WHERE AccountID = ?");
        $stmt->bind_param("ds", $amount, $senderAccountId);
        $stmt->execute();

        // Add amount to receiver
        $stmt = $conn->prepare("UPDATE account SET Balance = Balance + ? WHERE AccountID = ?");
        $stmt->bind_param("ds", $amount, $receiverAccount);
        $stmt->execute();

        // Log the transaction
        $stmt = $conn->prepare("INSERT INTO transaction (transactionAmount, transactiondate, transactiontype, account_AccountID) VALUES (?, NOW(), 'debit', ?)");
        $stmt->bind_param("ds", $amount, $senderAccountId);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO transaction (transactionAmount, transactiondate, transactiontype, account_AccountID) VALUES (?, NOW(), 'credit', ?)");
        $stmt->bind_param("ds", $amount, $receiverAccount);
        $stmt->execute();

        $conn->commit();

        // Send email receipt using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
            $mail->SMTPAuth = true;
            $mail->Username = 'itsa.ansari@gmail.com'; // Replace with your email
            $mail->Password = 'jubjhrtppgotstfp'; // Replace with your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('no-reply@yourbank.com', 'Aegis Bank');
            $mail->addAddress($sender['Email'], $sender['Name']);

            $mail->isHTML(true);
            $mail->Subject = 'Transaction Receipt';
            $mail->Body = "
                <h2>Transaction Receipt</h2>
                <p><strong>Sender:</strong> {$sender['Name']} ({$senderAccountId})</p>
                <p><strong>Receiver:</strong> {$receiverAccount}</p>
                <p><strong>Amount:</strong> $" . number_format($amount, 2) . "</p>
                <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
                <p>Thank you for banking with us!</p>
            ";

            $mail->send();

            echo json_encode(['status' => 'success', 'message' => 'Transaction completed. Receipt sent to your email.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'success', 'message' => 'Transaction completed, but receipt email could not be sent.']);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Transaction failed. Please try again later.']);
    }
} else {
    // Handle non-POST requests
    echo json_encode([
        'status' => 'error',
        'message' => 'This endpoint only accepts POST requests.',
        'method_received' => $_SERVER["REQUEST_METHOD"]
    ]);
}

$conn->close();
?>
