<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

include '../connection.php';

$message = '';

// Check if the user is logged in
if (!isset($_SESSION['customerId'])) {
    header("Location: ../login.html");
    exit();
}

// Regenerate session ID to prevent session fixation
session_regenerate_id();

// Get customerID from the session
$customerID = $_SESSION['customerId'];

// Fetch the sender's account details by joining the customer and account tables
$sql_account = "SELECT a.AccountID, a.Balance, c.name, c.email 
                FROM account a
                INNER JOIN customer c ON c.account_accountid = a.AccountID
                WHERE c.customerID = ?";
$stmt_account = $conn->prepare($sql_account);
$stmt_account->bind_param("s", $customerID);
$stmt_account->execute();
$result_account = $stmt_account->get_result();
$account = $result_account->fetch_assoc();

if (!$account) {
    $message = "<p style='color: red; text-align: center;'>No associated account found for the customer.</p>";
} else {
    $sender_account_id = $account['AccountID'];
    $sender_balance = $account['Balance'];
    $name = $account['name'];  // Fetching the name of the sender for the email
    $email = $account['email']; // Fetching the email of the sender for the receipt

    // Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $sender_accountinput = (int) $_POST['sender_account'];

        if ($sender_accountinput == $sender_account_id) {
            $receiver_account = $_POST['receiver_account'];
            $amount = (int)$_POST['amount'];

            // Validate the form data
            if ($amount <= 0) {
                $message = "<p style='color: red; text-align: center;'>Please enter a valid amount.</p>";
            } elseif ($sender_balance < $amount) {
                $message = "<p style='color: red; text-align: center;'>Insufficient funds in the sender's account.</p>";
            } else {
                // Start the transaction
                $conn->begin_transaction();

                try {
                    // Fetch the receiver's account details
                    $sql_receiver = "SELECT AccountID, Balance FROM account WHERE AccountID = ?";
                    $stmt_receiver = $conn->prepare($sql_receiver);
                    $stmt_receiver->bind_param("i", $receiver_account);
                    $stmt_receiver->execute();
                    $result_receiver = $stmt_receiver->get_result();
                    $receiver = $result_receiver->fetch_assoc();

                    if (!$receiver) {
                        throw new Exception("Receiver account not found.");
                    }

                    $receiver_account_id = $receiver['AccountID'];

                    // Deduct the amount from the sender's account
                    $sql_deduct = "UPDATE account SET Balance = Balance - ? WHERE AccountID = ?";
                    $stmt_deduct = $conn->prepare($sql_deduct);
                    $stmt_deduct->bind_param("di", $amount, $sender_account_id);
                    $stmt_deduct->execute();

                    // Add the amount to the receiver's account
                    $sql_add = "UPDATE account SET Balance = Balance + ? WHERE AccountID = ?";
                    $stmt_add = $conn->prepare($sql_add);
                    $stmt_add->bind_param("di", $amount, $receiver_account_id);
                    $stmt_add->execute();

                    // Record the transaction for the sender (debit)
                    $sql_transaction_sender = "INSERT INTO transaction (transactionAmount, transactiondate, transactiontype, account_AccountID) 
                                               VALUES (?, NOW(), 'debit', ?)";
                    $stmt_transaction_sender = $conn->prepare($sql_transaction_sender);
                    $stmt_transaction_sender->bind_param("di", $amount, $sender_account_id);
                    $stmt_transaction_sender->execute();

                    // Record the transaction for the receiver (credit)
                    $sql_transaction_receiver = "INSERT INTO transaction (transactionAmount, transactiondate, transactiontype, account_AccountID) 
                                                 VALUES (?, NOW(), 'credit', ?)";
                    $stmt_transaction_receiver = $conn->prepare($sql_transaction_receiver);
                    $stmt_transaction_receiver->bind_param("di", $amount, $receiver_account_id);
                    $stmt_transaction_receiver->execute();

                    // Commit the transaction
                    $conn->commit();
                    $message = "<p style='color: green; text-align: center;'>Funds transferred successfully! You will receive a transaction receipt in a while</p>";

                    // Send email receipt using PHPMailer
                    $mail = new PHPMailer(true);
                    try {
                        // SMTP configuration
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'itsa.ansari@gmail.com'; // Replace with your email
                        $mail->Password = 'jubjhrtppgotstfp'; // Replace with your app password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        // Email settings
                        $mail->setFrom('itsa.ansari@gmail.com', 'Aegis Bank');
                        $mail->addAddress($email, $name);

                        // Email content
                        $mail->isHTML(true);
                        $mail->Subject = 'Transaction Receipt - Aegis Bank';
                        $mail->Body = "
                            <p>Dear $name,</p>
                            <p>Your recent transaction was successful. Here are the details:</p>
                            <ul>
                                <li><strong>Sender's Account:</strong> $sender_account_id</li>
                                <li><strong>Receiver's Account:</strong> $receiver_account</li>
                                <li><strong>Amount Transferred:</strong> $$amount</li>
                                <li><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</li>
                            </ul>
                            <p>Thank you for banking with Aegis Bank!</p>
                            <p><strong>Aegis Bank Support Team</strong></p>
                        ";
                        $mail->send();
                    } catch (Exception $e) {
                        $message .= "<p style='color: red; text-align: center;'>Transaction completed, but receipt email could not be sent.</p>";
                    }
                } catch (Exception $e) {
                    // Rollback transaction if any error occurs
                    $conn->rollback();
                    $message = "<p style='color: red; text-align: center;'>Error: " . $e->getMessage() . "</p>";
                }
            }
        } else {
            $message = "<p style='color: red; text-align: center;'>Wrong sender account ID.</p>";
        }
    }
}

// Close the database connection
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funds Transfer</title>
    <link rel="stylesheet" href="../BankHome.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
/* General Styling */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Gill Sans', sans-serif;
}

html, body {
    height: 100%; /* Ensure the HTML and body take the full height of the viewport */
    width: 100%;
    display: flex;
    flex-direction: column; /* Arrange content in a vertical stack */
}

/* Main Content Styling */
#main {
    flex: 1; /* Allow the main section to grow and fill the remaining space */
    display: flex;
    justify-content: center;
    align-items: flex-start;
    background-color: #f7f7f7;
    padding: 3% 0; /* Add space above and below the main content */
}

/* Header Styling */
#header {
    height: 15%;
    width: 100%;
    position: fixed;
    top: 0;
    display: flex;
    align-items: center;
    background-color: white;
    justify-content: space-around;
    padding: 0 20px;
    border-bottom: 1px solid #ccc;
    z-index: 10;
}

#logo {
    height: 70%;
    display: flex;
    align-items: center;
}

#logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.nav-links a {
    color: #032D60;
    font-weight: 700;
    text-decoration: none;
    margin: 0 10px;
    font-size: 16px;
}

.nav-links a:hover {
    color: rgb(38, 152, 212);
}

.icon-group .icon {
    padding: 10px;
    border-radius: 50%;
    color: #032D60;
    cursor: pointer;
    font-size: 18px;
}

.icon-group .icon:hover {
    background-color: rgb(203, 228, 237);
    color: rgb(28, 120, 211);
}

/* Form Container Styling */
.transfer-form-container {
    width: 90%;
    max-width: 500px;
    background-color: #ffffff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    text-align: center;
    margin: 80px auto 20px; /* Add gap between header and the form */
}

.transfer-form-container h2 {
    color: #032D60;
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}

.transfer-form-container label {
    display: block;
    color: #032D60;
    font-weight: bold;
    margin: 15px 0 5px;
}

.transfer-form-container input[type="text"],
.transfer-form-container input[type="number"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    margin-bottom: 10px;
}

.transfer-form-container button {
    width: 100%;
    padding: 12px;
    background-color: #032D60;
    color: white;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 20px;
    transition: background-color 0.3s;
}

.transfer-form-container button:hover {
    background-color: rgb(38, 152, 212);
}

/* Success and Error Messages */
.message {
    font-weight: bold;
    margin-bottom: 15px;
    text-align: center;
}

.message p {
    margin: 0;
}

.confirmation-msg {
    color: green;
}

.error-msg {
    color: red;
}

/* Footer Styling */
#footer {
    background-color: #032D60;
    color: white;
    text-align: center;
    padding: 10px 0;
    font-size: 14px;
    display: flex;
    justify-content: center;
    align-items: center; /* Center vertically */
}

/* Responsive Design */
@media (max-width: 768px) {
    .transfer-form-container {
        width: 95%;
        padding: 20px;
    }

    .nav-links a {
        font-size: 14px;
        margin: 0 5px;
    }

    #header {
        flex-wrap: wrap;
        justify-content: space-between;
        padding: 10px 15px;
    }

    #logo {
        height: 60%;
    }
}
</style>


</head>
<body>
<div id="main">
    <!-- Header Section -->
    <header id="header">
        <!-- Bank Logo -->
        <div id="logo">
            <img src="../logo.png" width="75px" alt="Bank Logo">
        </div>

        <!-- Navigation Links -->
        <nav class="nav-links">
            <a href="../Bankhome.php">Accounts</a>
            <a href="Bank_transaction.php">Transaction</a>
            <a href="Bank_pay.php">Pay</a>
            <a href="Bank_loan.php">Loans</a>
            <a href="Bank_stats.php">Statements</a>
            <a href="Bank_support.php">Support</a>
            <a href="Bankpages/">Profile</a>
            <a href="login.html">Logout</a>
        </nav>

        <!-- Icons -->
        <div class="icon-group">
            <div class="icon"><i class="ri-search-line"></i></div>
            <div class="icon"><i class="ri-notification-3-line"></i></div>
        </div>
    </header>
    
    <div class="transfer-form-container">
        <h2>Funds Transfer</h2>
        <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>
        <form action="#" method="POST">
            <label for="sender-account">Sender's Account Number</label>
            <input type="text" id="sender-account" name="sender_account" value="<?php echo $sender_account_id; ?>" readonly>

            <label for="receiver-account">Receiver's Account Number</label>
            <input type="text" id="receiver-account" name="receiver_account" required>

            <label for="amount">Amount</label>
            <input type="number" id="amount" name="amount" required min="1" step="0.01">

            <button type="submit">Transfer Funds</button>
        </form>
    </div>
    
</div>
<!-- Footer Section -->
<footer id="footer">
    <div class="footer-content">
        <p>© Copyright 2024 Aegis, Inc. <u>All rights reserved.</u> Various trademarks held by their respective owners.</p>
    </div>
</footer>
</body>
</html>
