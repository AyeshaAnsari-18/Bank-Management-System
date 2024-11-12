<?php
session_start();
include 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['customerId'])) {
    header("Location: login.html");
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

// Fetch account information
$sql_account = "SELECT AccountType, Balance, AccountID FROM account WHERE customer_customerID = ?";
$stmt_account = $conn->prepare($sql_account);
$stmt_account->bind_param("s", $customerID);
$stmt_account->execute();
$result_account = $stmt_account->get_result();
$account = $result_account->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="BankHome.css">
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
            <img src="logo.png" width="75px" alt="Bank Logo">
        </div>

        <!-- Navigation Links -->
        <nav class="nav-links">
            <a href="Bankhome.php">Accounts</a>
            <a href="Bankpages/Bank_transaction.php">Transaction</a>
            <a href="Bankpages/Bank_pay.php">Pay</a>
            <a href="Bankpages/Bank_loan.php">Loans</a>
            <a href="Bankpages/Bank_stats.php">Statements</a>
            <a href="Bankpages/Bank_support.php">Support</a>
            <a href="Bankpages/">Profile</a>
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

        <!-- Account Details -->

        <div class="account-details">
            <div class="detail-card">
                <h2>Account Balance</h2>
                <p>$<?php echo number_format($account['Balance'], 2); ?></p>
            </div>
            <div class="detail-card">
                <h2>Account Number</h2>
                <p><?php echo substr($account['AccountID'], -4); ?></p>
            </div>
            <div class="detail-card">
                <h2>Account Type</h2>
                <p><?php echo htmlspecialchars($account['AccountType']); ?></p>
            </div>
            <div class="detail-card">
                <h2>Last Login</h2>
                <p id="lastLogin">Loading...</p>
            </div>
        </div>

        <script>
        // Display current date and time for "Last Login"
        const now = new Date();
        const options = { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric' };
        document.getElementById('lastLogin').textContent = now.toLocaleString('en-US', options);
        </script>

        
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
