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
$sql_user = "SELECT Name FROM Customer WHERE CustomerId = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $customerID);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

$sql_account = "SELECT AccountType, Balance, AccountID FROM account WHERE customer_customerID = ?";
$stmt_account = $conn->prepare($sql_account);
$stmt_account->bind_param("s", $customerID);
$stmt_account->execute();
$result_account = $stmt_account->get_result();
$account = $result_account->fetch_assoc();

//Fetch recent transactions (limit to last 5)
$sql_transactions = "SELECT transactionDate, transactionType, transactionAmount FROM transaction WHERE account_AccountID = ? ORDER BY transactionDate DESC LIMIT 5";
$stmt_transactions = $conn->prepare($sql_transactions);
$stmt_transactions->bind_param("i", $account['AccountID']);
$stmt_transactions->execute();
$result_transactions = $stmt_transactions->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction</title>
    <link rel="stylesheet" href="../BankHome.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>
<body>

<!-- Main container -->
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
            <a href="#loans">Loans</a>
            <a href="#statements">Statements</a>
            <a href="#support">Support</a>
            <a href="#profile">Profile</a>
            <a href="login.html">Logout</a>
        </nav>

        <!-- Icons -->
        <div class="icon-group">
            <div class="icon"><i class="ri-search-line"></i></div>
            <div class="icon"><i class="ri-notification-3-line"></i></div>
        </div>
    </header>

    <!-- User Info Section -->
    <div class="user-info">
        <h1>Welcome, <?php echo htmlspecialchars($user['Name']); ?></h1>
        <p><?php echo htmlspecialchars($user['Name']); ?>'s Account Overview</p>

       
        <!-- Recent Transactions Section -->
        <div class="transactions">
            <h2>Recent Transactions</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($transaction = $result_transactions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date("F j, Y", strtotime($transaction['transactionDate'])); ?></td>
                        <td><?php echo htmlspecialchars($transaction['transactionType']); ?></td>
                        <td><?php echo ($transaction['transactionType'] == "Debit" ? "-" : "+") . "$" . number_format($transaction['transactionAmount'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
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

<?php
// Close the database connection
$conn->close();
?>
