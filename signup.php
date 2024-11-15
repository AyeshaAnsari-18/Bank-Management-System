<?php
session_start();
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    if (
        isset($_POST['name'], $_POST['email'], $_POST['address'], $_POST['dob'], 
        $_POST['phone'], $_POST['signup_password'], $_POST['confirm_password'])
    ) {
        $name = sanitize_input($_POST['name']);
        $email = sanitize_input($_POST['email']);
        $address = sanitize_input($_POST['address']);
        $dob = sanitize_input($_POST['dob']);
        $phone = sanitize_input($_POST['phone']);
        $password = sanitize_input($_POST['signup_password']);
        $confirmPassword = sanitize_input($_POST['confirm_password']);

        // Check if passwords match
        if ($password !== $confirmPassword) {
            header("Location: login.html?error=password_mismatch");
            exit();
        }

        // Check if email already exists in the database
        $stmt = $conn->prepare("SELECT Email FROM Customer WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();
            header("Location: login.html?error=email_exists");
            exit();
        }

        // Insert new user data into the database
        $stmt_insert = $conn->prepare("INSERT INTO Customer (Name, Email, Address, DateOfBirth, Phone, UserPassword) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("ssssss", $name, $email, $address, $dob, $phone, $password);

        if ($stmt_insert->execute()) {
            $customerId = $conn->insert_id;

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
                $mail->Body = "<p>Dear $name,</p><p>Your Customer ID is: <b>$customerId</b></p><p>Thank you for registering with Aegis Bank!</p>";

                $mail->send();

                header("Location: login.html?success=1");
                exit();
            } catch (Exception $e) {
                header("Location: login.html?error=email_not_sent");
                exit();
            }
        } else {
            header("Location: login.html?error=registration_failed");
            exit();
        }

        $stmt_insert->close();
    } else {
        header("Location: login.html?error=missing_fields");
        exit();
    }
}

$conn->close();
?>
