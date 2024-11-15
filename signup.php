<?php
header("Content-Type: application/json"); // Set response type to JSON
include('connection.php');

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Retrieve JSON input
$input = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate required fields
    if (!isset($input['name'], $input['email'], $input['address'], $input['dob'], $input['phone'], $input['account_id'], $input['signup_password'], $input['confirm_password'])) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit();
    }

    $name = sanitize_input($input['name']);
    $email = sanitize_input($input['email']);
    $address = sanitize_input($input['address']);
    $dob = sanitize_input($input['dob']);
    $phone = sanitize_input($input['phone']);
    $accountId = sanitize_input($input['account_id']);
    $password = sanitize_input($input['signup_password']);
    $confirmPassword = sanitize_input($input['confirm_password']);

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit();
    }

    // Check if the Account ID exists in the account table
    $stmt = $conn->prepare("SELECT accountID FROM account WHERE accountID = ?");
    $stmt->bind_param("s", $accountId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Invalid Account ID.']);
        exit();
    }
    $stmt->close();

    // Check if email already exists in the database
    $stmt = $conn->prepare("SELECT Email FROM Customer WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
        exit();
    }
    $stmt->close();

    // Insert new user data into the database
    $stmt_insert = $conn->prepare("INSERT INTO Customer (Name, Email, Address, DateOfBirth, Phone, UserPassword, account_accountID) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("ssssssi", $name, $email, $address, $dob, $phone, $password, $accountId);

    if ($stmt_insert->execute()) {
        // Fetch the customerID for the newly inserted customer using email
        $stmt_fetch = $conn->prepare("SELECT customerID FROM Customer WHERE Email = ?");
        $stmt_fetch->bind_param("s", $email);
        $stmt_fetch->execute();
        $stmt_fetch->bind_result($customerId);
        $stmt_fetch->fetch();
        $stmt_fetch->close();

        // Initialize PHPMailer to send the welcome email
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

            $mail->isHTML(true);
            $mail->Subject = 'Welcome to Aegis Bank - Your Customer ID';
            $mail->Body = "
                <p>Dear $name,</p>
                <p>We are thrilled to welcome you to Aegis Bank! Your account has been created successfully.</p>
                <p><strong>Your Customer ID:</strong> <b>$customerId</b></p>
                <p>Please keep this Customer ID secure.</p>
                <p>Warm regards,</p>
                <p><strong>Aegis Bank Support Team</strong></p>
            ";

            $mail->send();

            echo json_encode(['status' => 'success', 'message' => 'Registration successful. Customer ID sent to email.', 'customerId' => $customerId]);
            exit();
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Account created, but email could not be sent.']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed. Please try again.']);
        exit();
    }

    $stmt_insert->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$conn->close();
?>
