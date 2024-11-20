<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['customerId'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="css/BankHome.css">
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
            <a href="Bankpages/Bank_profile.php">Profile</a>
            <a href="Bankpages/Bank_logout.php">Logout</a>
        </nav>

        <!-- Icons -->
        <div class="icon-group">
            <div class="icon"><i class="ri-search-line"></i></div>
            <div class="icon"><i class="ri-notification-3-line"></i></div>
        </div>
    </header>

    <!-- User Info Section -->
    <div class="user-info">
        <h1>Welcome, <span id="userName">Loading...</span></h1>
        <p><span id="userNameOverview">User</span>'s Account Overview</p>

        <!-- Account Details -->
        <div class="account-details">
            <div class="detail-card">
                <h2>Account Balance</h2>
                <p id="accountBalance">Loading...</p>
            </div>
            <div class="detail-card">
                <h2>Account ID</h2>
                <p id="accountId">Loading...</p>
            </div>
            <div class="detail-card">
                <h2>Account Type</h2>
                <p id="accountType">Loading...</p>
            </div>
            <div class="detail-card">
                <h2>Last Login</h2>
                <p id="lastLogin">Loading...</p>
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

<script>
    // Fetch user and account details from the API
    fetch('api/bankhome_api.php', {
        method: 'GET',
        credentials: 'include' // Include session cookies
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Populate data into the page
            document.getElementById('userName').textContent = data.user.name;
            document.getElementById('userNameOverview').textContent = data.user.name;
            document.getElementById('accountBalance').textContent = `$${parseFloat(data.account.balance).toFixed(2)}`;
            document.getElementById('accountId').textContent = `****${data.account.accountIdLast4}`;
            document.getElementById('accountType').textContent = data.account.accountType;
            document.getElementById('lastLogin').textContent = data.lastLogin;
        } else {
            alert(data.message || 'Error fetching user data.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Unable to fetch account details.');
    });
</script>

</body>
</html>
