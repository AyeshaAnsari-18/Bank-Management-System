<?php
session_start();
include '../connection.php'; // Database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Fetch all transactions
$result = mysqli_query($conn, "SELECT * FROM transaction");
if (!$result) {
    die("Error fetching transactions: " . mysqli_error($conn));
}
$transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get message from query string
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Transactions</title>
    <link rel="stylesheet" href="../css/admintransaction.css">
    <style>
        .user-info h1{
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
            width: 20%;
            height:40px;
        }
        .buttons button:hover {
            background-color: rgb(28, 120, 211);
            color: white;
        }
        .buttons button {
            padding: 10px;
            background-color: #032d60;
            color: white;
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
                <a href="manage_users.php">Customer Management</a>
                <a href="manage_employees.php">Employee Management</a>
                <a href="manage_transaction.php">Transaction Management</a>
                <a href="manage_loan.php">Loan Management</a>
                <a href="manage_branch.php">Branch Management</a>
                <a href="manage_support.php">Customer Feedback Management</a>
                <a href="adminlogin.html">Logout</a>
            </nav>
        </header>

        <!-- Main Content -->
        <div class="user-info">
            <h1>Transactions Information</h1>
            <?php if (!empty($message)): ?>
                <p style="color:green;" class="message"><?= htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="buttons">
                <button onclick="window.location.href='TransactionManagement/create_tr.php'">Insert New Transaction</button>
            </div>

            <!-- Transaction Table -->
            <table>
                <thead>
                    <tr>
                    <th>Transaction ID</th>
                    <th>Account ID</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?= htmlspecialchars($transaction['transactionID'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($transaction['account_AccountID'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($transaction['transactionDate'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($transaction['transactionType'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($transaction['transactionAmount'] ?? 'N/A'); ?></td>
                        <td>
                            <div class="action-buttons">
                            <button class="update" 
                                onclick="window.location.href='TransactionManagement/update_tr.php?transactionID=<?= $transaction['transactionID']; ?>'">
                                Update
                            </button>
                            <button class="delete" 
                                onclick="if(confirm('Are you sure you want to delete this transaction?')) 
                                window.location.href='TransactionManagement/delete_tr.php?transactionID=<?= $transaction['transactionID']; ?>'">
                                Delete
                            </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
