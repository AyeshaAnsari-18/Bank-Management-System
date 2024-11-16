<?php
session_start();
include '../connection.php';

// Check if the user is logged in
if (!isset($_SESSION['customerId'])) {
    header("Location: ../login.html");
    exit();
}

// Regenerate session ID to prevent session fixation
session_regenerate_id();

// Get customerID from the session
$customerID = $_SESSION['customerId'];

// Fetch user information
$sql_user = "SELECT Name, account_accountID, Email FROM Customer WHERE CustomerId = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $customerID);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$stmt_user->close();

if (!$user) {
    echo "<p style='color: red;'>User not found.</p>";
    exit();
}

// Fetch account information
$accountID = $user['account_accountID'];
$sql_account = "SELECT AccountType, Balance, AccountID FROM account WHERE AccountID = ?";
$stmt_account = $conn->prepare($sql_account);
$stmt_account->bind_param("s", $accountID);
$stmt_account->execute();
$result_account = $stmt_account->get_result();
$account = $result_account->fetch_assoc();
$stmt_account->close();

if (!$account) {
    echo "<p style='color: red;'>Account information not found.</p>";
    exit();
}

// Initialize variables
$message = '';
$code_sent = false;

// Handle fund transfer submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['send_code'])) {
        // Generate a confirmation code
        $confirmation_code = rand(100000, 999999);
        $_SESSION['confirmation_code'] = $confirmation_code;

        // Send the code to the user's email
        $to = $user['Email'];
        $subject = "Transaction Confirmation Code";
        $email_message = "Your confirmation code is: $confirmation_code";
        $headers = "From: no-reply@yourbank.com";

        if (mail($to, $subject, $email_message, $headers)) {
            $message = "<p style='color: green;'>Confirmation code sent to your email.</p>";
            $code_sent = true;
        } else {
            $message = "<p style='color: red;'>Failed to send confirmation code. Please try again later.</p>";
        }
    } elseif (isset($_POST['confirm_transaction'])) {
        $receiver_account = trim($_POST['receiver_account']);
        $amount = (float)trim($_POST['amount']);
        $reason = trim($_POST['reason']);
        $reschedule = isset($_POST['reschedule']) ? 1 : 0;
        $confirmation_code = trim($_POST['confirmation_code']);

        // Validate inputs
        if (empty($receiver_account) || $amount <= 0 || empty($reason) || empty($confirmation_code)) {
            $message = "<p style='color: red;'>Please fill in all fields correctly.</p>";
        } elseif ($confirmation_code != $_SESSION['confirmation_code']) {
            $message = "<p style='color: red;'>Invalid confirmation code.</p>";
        } elseif ($account['Balance'] < $amount) {
            $message = "<p style='color: red;'>Insufficient funds in your account.</p>";
        } elseif ($receiver_account === $accountID) {
            $message = "<p style='color: red;'>You cannot transfer funds to your own account.</p>";
        } else {
            // Begin transaction
            $conn->begin_transaction();
            try {
                // Check if receiver account exists
                $sql_receiver = "SELECT AccountID FROM account WHERE AccountID = ?";
                $stmt_receiver = $conn->prepare($sql_receiver);
                $stmt_receiver->bind_param("s", $receiver_account);
                $stmt_receiver->execute();
                $receiver = $stmt_receiver->get_result()->fetch_assoc();
                $stmt_receiver->close();

                if (!$receiver) {
                    throw new Exception("Receiver account not found.");
                }

                // Update sender's account balance
                $sql_update_sender = "UPDATE account SET Balance = Balance - ? WHERE AccountID = ?";
                $stmt_update_sender = $conn->prepare($sql_update_sender);
                $stmt_update_sender->bind_param("ds", $amount, $accountID);
                $stmt_update_sender->execute();

                // Update receiver's account balance
                $sql_update_receiver = "UPDATE account SET Balance = Balance + ? WHERE AccountID = ?";
                $stmt_update_receiver = $conn->prepare($sql_update_receiver);
                $stmt_update_receiver->bind_param("ds", $amount, $receiver_account);
                $stmt_update_receiver->execute();

                // Log transactions
                $sql_transaction_sender = "INSERT INTO transaction (transactionAmount, transactiondate, transactiontype, reason, account_AccountID) VALUES (?, NOW(), 'debit', ?, ?)";
                $stmt_transaction_sender = $conn->prepare($sql_transaction_sender);
                $stmt_transaction_sender->bind_param("dss", $amount, $reason, $accountID);
                $stmt_transaction_sender->execute();

                $sql_transaction_receiver = "INSERT INTO transaction (transactionAmount, transactiondate, transactiontype, account_AccountID) VALUES (?, NOW(), 'credit', ?)";
                $stmt_transaction_receiver = $conn->prepare($sql_transaction_receiver);
                $stmt_transaction_receiver->bind_param("ds", $amount, $receiver_account);
                $stmt_transaction_receiver->execute();

                // Commit transaction
                $conn->commit();
                $message = "<p style='color: green;'>Funds transferred successfully!</p>";
            } catch (Exception $e) {
                // Rollback transaction on error
                $conn->rollback();
                $message = "<p style='color: red;'>Transaction failed: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay</title>
    <link rel="stylesheet" href="../BankHome.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
<div id="main">
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
            <a href="../login.html">Logout</a>
        </nav>
        <div class="icon-group">
            <div class="icon"><i class="ri-search-line"></i></div>
            <div class="icon"><i class="ri-notification-3-line"></i></div>
        </div>
    </header>

    <div class="user-info">
        <h1>Welcome, <?php echo htmlspecialchars($user['Name']); ?></h1>
        <p>Account Balance: $<?php echo number_format($account['Balance'], 2); ?></p>
        <div class="transfer-form">
            <h2>Transfer Funds</h2>
            <?php if ($message) echo "<div class='message'>$message</div>"; ?>
            <form method="POST">
                <label for="receiver-account">Receiver's Account:</label>
                <input type="text" id="receiver-account" name="receiver_account" required>
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" min="0.01" step="0.01" required>
                <button type="submit">Transfer</button>
            </form>
        </div>
    </div>
</div>
<footer id="footer">
    <p>&copy; 2024 Aegis, Inc. All rights reserved.</p>
</footer>
</body>
</html>

<?php $conn->close(); ?>
