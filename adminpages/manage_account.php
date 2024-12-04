<?php
session_start();
include '../connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Fetch all accounts
$account_result = mysqli_query($conn, "SELECT * FROM account");
if (!$account_result) {
    // Debugging output for query error
    error_log("Error fetching accounts: " . mysqli_error($conn));
    $accounts = []; // Default to empty array if query fails
} else {
    $accounts = mysqli_fetch_all($account_result, MYSQLI_ASSOC);
}

// Get message from query string
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accounts</title>
    <link rel="stylesheet" href="../css/adminpages.css">
    <style>
        .user-info h1 {
            font-size: 40px;
        }
        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .buttons {
            margin: 15px 15px;
            display: flex;
            gap: 10px;
        }
        .buttons button:hover {
            background-color: rgb(28, 120, 211);
            color: white;
        }
        .buttons button {
            padding: 10px;
            background-color: #032d60;
            color: white;
            border: none;
            cursor: pointer;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .action-buttons button {
            padding: 5px 10px;
            font-size: 14px;
            cursor: pointer;
        }
        .action-buttons button.update {
            background-color: #032d60;
            color: white;
            border: none;
        }
        .action-buttons button.delete {
            background-color: #032d60;
            color: white;
            border: none;
        }
        .action-buttons button:hover {
            opacity: 0.8;
            background-color: rgb(38, 152, 212);
        }
    </style>
</head>
<body>
    <div id="main">
        <!-- Header Section -->
        <header class="header">
            <div id="logo" style="padding-top: 20px;">
                <img src="../logo.png" alt="Bank Logo">
            </div>
            <nav class="nav-links">
                <a href="adminhome.php">Home</a>
                <a href="manage_users.php">Customer Management</a>
                <a href="manage_employees.php">Employee Management</a>
                <a href="manage_transaction.php">Transaction Management</a>
                <a href="manage_loan.php">Loan Management</a>
                <a href="manage_branch.php">Branch Management</a>
                <a href="manage_support.php">Customer Feedback Management</a>
                <a href="manage_department.php">Department Management</a>
                <a href="manage_account.php">Accounts Management</a>
                <a href="manage_reports.php">Reports and Analytics</a>
                <a href="manage_audit_logs.php">Audit Logs</a>
                <a href="admin_logout.php">Logout</a>
            </nav>
        </header>

        <!-- Main Content -->
        <div class="user-info">
            <h1>Account Information</h1>
            <?php if (!empty($message)): ?>
                <p style="color:green;" class="message"><?= htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="buttons">
                <button onclick="window.location.href='AccountManagement/create_account.php'">Insert New Account</button>
            </div>

            <!-- Accounts Table -->
            <table>
                <thead>
                    <tr>
                        <th>Account ID</th>
                        <th>Account Type</th>
                        <th>Balance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($accounts)): ?>
                        <?php foreach ($accounts as $account): ?>
                            <tr>
                                <td><?= htmlspecialchars($account['AccountID'] ?? 'N/A'); ?></td>
                                <td><?= htmlspecialchars($account['AccountType'] ?? 'N/A'); ?></td>
                                <td><?= htmlspecialchars(number_format($account['Balance'] ?? 0, 2)); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="update" 
                                            onclick="window.location.href='AccountManagement/update_account.php?account_id=<?= $account['AccountID']; ?>'">
                                            Update
                                        </button>
                                        <button class="delete" 
                                            onclick="if(confirm('Are you sure you want to delete this account?')) 
                                                window.location.href='AccountManagement/delete_account.php?account_id=<?= $account['AccountID']; ?>'">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No accounts found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
