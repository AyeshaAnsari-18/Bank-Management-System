<?php
session_start();
include '../../connection.php';
$message = '';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

// CRUD Operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $accountID = $_POST['account_AccountID'];
        $date = $_POST['transactionDate'];
        $type = $_POST['transactionType'];
        $amount = $_POST['transactionAmount'];

        // Check if account_AccountID exists in the account table
        $checkAccountQuery = "SELECT * FROM account WHERE AccountID = '$accountID'";
        $accountResult = mysqli_query($conn, $checkAccountQuery);

        if (mysqli_num_rows($accountResult) > 0) {
            // Start a transaction
            mysqli_begin_transaction($conn);

            try {
                // Insert into transaction table
                $query = "INSERT INTO transaction (account_AccountID, transactionDate, transactionType, transactionAmount) 
                          VALUES ('$accountID', '$date', '$type', '$amount')";
                if (!mysqli_query($conn, $query)) {
                    throw new Exception("Error inserting transaction: " . mysqli_error($conn));
                }

                // Update the Balance in account table
                $balanceQuery = $type === 'Credit' 
                    ? "UPDATE account SET Balance = Balance + $amount WHERE AccountID = '$accountID'" 
                    : "UPDATE account SET Balance = Balance - $amount WHERE AccountID = '$accountID'";

                if (!mysqli_query($conn, $balanceQuery)) {
                    throw new Exception("Error updating balance: " . mysqli_error($conn));
                }

                // Commit the transaction
                mysqli_commit($conn);
                $message = "Transaction created and account balance updated successfully.";
                header("Location: ../manage_transaction.php?message=" . urlencode($message));
                exit();
            } catch (Exception $e) {
                // Rollback transaction in case of error
                mysqli_rollback($conn);
                $message = $e->getMessage();
            }
        } else {
            // If account doesn't exist, set the message
            $message = "Invalid Account ID. The account does not exist.";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Customer</title>
    <link rel="stylesheet" href="../../css/adminpages.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .card {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
        .card h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            text-align: center;
        }
        .form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .form div {
            flex: 1 1 calc(50% - 15px);
        }
        .form label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form button:hover {
            background-color: #0056b3;
        }
        .message {
            color: green;
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
        select {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
        font-family: Arial, sans-serif;
        background-color: #fff;
        color: #333;
        outline: none;
        transition: border-color 0.3s ease-in-out;
        }

        select:focus {
        border-color: #007BFF; /* Matches the button's blue color */
        }

    </style>
</head>
<body>
    <div id="main">
        <header class="header">
            <div id="logo" style="padding-top: 20px;">
                <img src="../../logo.png" alt="Bank Logo">
            </div>
            <nav class="nav-links">
                <a href="../manage_users.php">Customer Management</a>
                <a href="../manage_employees.php">Employee Management</a>
                <a href="../manage_transaction.php">Transaction Management</a>
                <a href="../approve_loans.php">Loan Management</a>
                <a href="../manage_branch.php">Branch Management</a>
                <a href="#">Customer Feedback Management</a>
                <a href="adminlogin.html">Logout</a>
            </nav>
        </header>
        <div class="user-info">
            <div class="card">
                <h2>Create Transaction</h2>
                <?php if (!empty($message)): ?>
                    <p class="message"><?= htmlspecialchars($message); ?></p>
                <?php endif; ?>
                <form class="form" method="POST" action="">
                    <input type="hidden" name="transactionID">
                    <input type="text" name="account_AccountID" placeholder="Account ID" required>
                    <input type="date" name="transactionDate" required>
                    <select name="transactionType" required>
                        <option value="Credit">Credit</option>
                        <option value="Debit">Debit</option>
                    </select>
                    <input type="number" name="transactionAmount" placeholder="Amount" step="0.01" required>
                    <button type="submit" name="create">Create transaction</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>