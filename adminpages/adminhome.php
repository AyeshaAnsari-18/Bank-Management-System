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
$total_loans_query = "SELECT COUNT(*) AS total_loans FROM loan WHERE Status = '1'";
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
    <link rel="stylesheet" href="../css/adminpages.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <!-- Main container -->
<div id="main">
    <!-- Header Section -->
    <header class="header">
        <!-- Bank Logo -->
        <div id="logo" style="padding-top: 20px;">
            <img src="../logo.png" alt="Bank Logo">
        </div>

        <!-- Navigation Links -->
        <nav class="nav-links">
            <a href="adminhome.php">Home</a>
            <a href="manage_users.php">Customer Management</a>
            <a href="manage_employees.php">Employee Management</a>
            <a href="manage_transaction.php">Transaction Management</a>
            <a href="manage_loan.php">Loan Management</a>
            <a href="manage_branch.php">Branch Management</a>
            <a href="manage_support.php">Customer Feedback Management</a>
            <a href="admin_logout.php">Logout</a>
        </nav>
    </header>

    <!-- Right Content -->
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

</body>
</html>