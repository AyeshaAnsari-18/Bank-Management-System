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

if (isset($_GET['Support_id'])) {
    $support_id = intval($_GET['Support_id']);

    // Fetch support details
    $query = "SELECT cs.issue_type, cs.description, c.email 
              FROM customer_support cs
              INNER JOIN customer c ON cs.customer_customerID = c.customerID
              WHERE cs.Support_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $support_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $issue_type = $data['issue_type'];
        $description = $data['description'];
        $email = $data['email'];

        // Determine the response message
        $message = "Dear Customer,<br><br>";
        if ($issue_type === 'inquiry') {
            $message .= "We have received your inquiry: \"$description\".<br> Our team is working on it and will update you shortly.<br><br>";
        } elseif ($issue_type === 'complaint') {
            $message .= "We apologize for the inconvenience regarding \"$description\".<br> Our team is addressing the issue promptly.<br><br>";
        } elseif ($issue_type === 'technical issue') {
            $message .= "We acknowledge the technical issue: \"$description\".<br> Our team is actively resolving it.<br><br>";
        }
        $message .= "Thank you for reaching out to us.<br><br>Best regards,<br>Aegis Bank Support Team";

        // Use PHPMailer to send the email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'itsa.ansari@gmail.com';
            $mail->Password = 'jubjhrtppgotstfp';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('support@aegisbank.com', 'Aegis Bank Support');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Response to your support request";
            $mail->Body = $message;

            $mail->send();

            // Update the support request status
            $update_query = "UPDATE customer_support SET status = 'Closed' WHERE Support_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("i", $support_id);
            if ($update_stmt->execute()) {
                header('Location: ../manage_support.php?message=Support ticket responded and closed.');
                exit();
            }
        } catch (Exception $e) {
            header('Location: ../manage_support.php?message=Email could not be sent. Error: ' . $mail->ErrorInfo);
            exit();
        }
    } else {
        header('Location: ../manage_support.php?message=Support ticket not found.');
        exit();
    }
} else {
    header('Location: ../manage_support.php');
    exit();
}

$conn->close();
?>
