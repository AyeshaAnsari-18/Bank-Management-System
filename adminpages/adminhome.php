<?php
// Start session and include database connection
session_start();
include '../connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Fetch admin information
$admin_id = $_SESSION['admin_id'];
$admin_name_query = "SELECT admin_name FROM admin WHERE adminID = '$admin_id'";
$result = mysqli_query($conn, $admin_name_query);
$admin = mysqli_fetch_assoc($result);

// Placeholder queries for bank statistics
$total_users_query = "SELECT COUNT(*) AS total_users FROM customer";
$total_transactions_query = "SELECT COUNT(*) AS total_transactions FROM transaction";
$total_loans_query = "SELECT COUNT(*) AS total_loans FROM loan WHERE Status = 'false'";
$total_balance_query = "SELECT SUM(balance) AS total_balance FROM account";

// Execute queries
$total_users = mysqli_fetch_assoc(mysqli_query($conn, $total_users_query))['total_users'];
$total_transactions = mysqli_fetch_assoc(mysqli_query($conn, $total_transactions_query))['total_transactions'];
$total_loans = mysqli_fetch_assoc(mysqli_query($conn, $total_loans_query))['total_loans'];
$total_balance = mysqli_fetch_assoc(mysqli_query($conn, $total_balance_query))['total_balance'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../BankHome.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>Admin Dashboard - Aegis Bank</title>
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
            <a href="manage_users.php">Manage Users</a>
            <a href="Alltransactions.php">View Transactions</a>
            <a href="adminlogin.html">Logout</a>
        </nav>

        <!-- Icons -->
        <div class="icon-group">
            <div class="icon"><i class="ri-search-line"></i></div>
            <div class="icon"><i class="ri-notification-3-line"></i></div>
        </div>
    </header>

    <!-- User Info Section -->
    <div class="user-info">
        <h1>Welcome, <?php echo htmlspecialchars($admin['admin_name']); ?></h1>
        <p><span id="userNameOverview">Admin</span>'s Account Overview</p>

        <!-- Account Details -->
        <div class="account-details">
            <div class="detail-card">
                <h3>Total Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="detail-card">
                <h3>Total Transactions</h3>
                <p><?php echo $total_transactions; ?></p>
            </div>
            <div class="detail-card">
                <h3>Pending Loans</h3>
                <p><?php echo $total_loans; ?></p>
            </div>
            <div class="detail-card">
                <h3>Total Bank Balance</h3>
                <p>$<?php echo number_format($total_balance, 2); ?></p>
            </div>
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
