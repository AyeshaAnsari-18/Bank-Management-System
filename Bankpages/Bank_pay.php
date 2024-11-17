<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Fund Transfer</title>
    <link rel="stylesheet" href="../Bankhome.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body>

<?php
session_start();
include '../connection.php';

// Initialize variables
$errorMsg = null;
$successMsg = null;

// Validate session
if (!isset($_SESSION['AccountId'])) {
    $errorMsg = "Your session has expired or Account ID is unavailable. Please log in again.";
} else {
    $accountId = $_SESSION['AccountId'];

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $receiverAccount = $_POST['receiver_account'];
        $amount = $_POST['amount'];

        // Validate form inputs
        if (empty($receiverAccount) || empty($amount) || $amount <= 0) {
            $errorMsg = "Please provide valid Receiver's Account ID and Amount.";
        } else {
            // Check database connection
            if (!$conn) {
                $errorMsg = "Database connection failed: " . htmlspecialchars(mysqli_connect_error());
            } else {
                // Begin transaction
                $conn->begin_transaction();

                try {
                    // Check sender's balance
                    $stmt_balance = $conn->prepare("SELECT Balance FROM account WHERE AccountID = ?");
                    $stmt_balance->bind_param("s", $accountId);
                    $stmt_balance->execute();
                    $result_balance = $stmt_balance->get_result();
                    $sender = $result_balance->fetch_assoc();
                    $stmt_balance->close();

                    if (!$sender || $sender['Balance'] < $amount) {
                        throw new Exception("Insufficient balance to complete the transfer.");
                    }

                    // Deduct amount from sender
                    $stmt_deduct = $conn->prepare("UPDATE account SET Balance = Balance - ? WHERE AccountID = ?");
                    $stmt_deduct->bind_param("ds", $amount, $accountId);
                    if (!$stmt_deduct->execute()) {
                        throw new Exception("Failed to deduct amount from sender's account.");
                    }
                    $stmt_deduct->close();

                    // Add amount to receiver
                    $stmt_add = $conn->prepare("UPDATE account SET Balance = Balance + ? WHERE AccountID = ?");
                    $stmt_add->bind_param("ds", $amount, $receiverAccount);
                    if (!$stmt_add->execute()) {
                        throw new Exception("Failed to credit amount to receiver's account. Please check the Receiver's Account ID.");
                    }
                    $stmt_add->close();

                    // Insert transaction record
                    $stmt_transaction = $conn->prepare("INSERT INTO transaction (SenderAccountID, ReceiverAccountID, Amount, Date) VALUES (?, ?, ?, NOW())");
                    $stmt_transaction->bind_param("ssd", $accountId, $receiverAccount, $amount);
                    if (!$stmt_transaction->execute()) {
                        throw new Exception("Failed to record the transaction.");
                    }
                    $stmt_transaction->close();

                    // Commit transaction
                    $conn->commit();
                    $successMsg = "Funds successfully transferred!";
                } catch (Exception $e) {
                    $conn->rollback();
                    $errorMsg = $e->getMessage();
                }
            }
        }
    }
}
?>

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
    </header>

    <div class="user-info">
        <h1>Transfer Funds</h1>
        
        <?php if (!empty($successMsg)): ?>
            <p class="success"><?php echo $successMsg; ?></p>
        <?php elseif (!empty($errorMsg)): ?>
            <p class="error"><?php echo $errorMsg; ?></p>
        <?php endif; ?>

        <form method="POST" action="Bank_pay.php">
            <label for="receiver-account">Receiver's Account ID:</label>
            <input type="text" id="receiver-account" name="receiver_account" required>
            
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" min="0.01" step="0.01" required>
            
            <button type="submit">Transfer</button>
        </form>
    </div>
</div>
<footer id="footer">
    <p>&copy; 2024 Aegis, Inc. All rights reserved.</p>
</footer>
</body>
</html>
