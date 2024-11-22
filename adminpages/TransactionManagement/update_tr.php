<?php
session_start();
include '../../connection.php';
$message = '';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

// Update Transaction
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        $transaction_id = $_POST['transactionID'];
        $accountID = $_POST['account_AccountID'];
        $newDate = $_POST['transactionDate'];
        $newType = $_POST['transactionType'];
        $newAmount = $_POST['transactionAmount'];

        // Fetch old transaction details
        $oldTransactionQuery = "SELECT transactionType, transactionAmount FROM transaction WHERE transactionID = '$transaction_id'";
        $oldTransactionResult = mysqli_query($conn, $oldTransactionQuery);

        if ($oldTransactionResult && mysqli_num_rows($oldTransactionResult) > 0) {
            $oldTransaction = mysqli_fetch_assoc($oldTransactionResult);
            $oldType = $oldTransaction['transactionType'];
            $oldAmount = $oldTransaction['transactionAmount'];

            // Start a transaction
            mysqli_begin_transaction($conn);

            try {
                // Update the transaction table
                $updateQuery = "UPDATE transaction 
                                SET transactionDate='$newDate', transactionType='$newType', transactionAmount='$newAmount'
                                WHERE transactionID='$transaction_id'";
                if (!mysqli_query($conn, $updateQuery)) {
                    throw new Exception("Error updating transaction: " . mysqli_error($conn));
                }

                // Adjust the balance in the account table
                $balanceAdjustment = 0;

                // Undo the effect of the old transaction
                if ($oldType === 'Credit') {
                    $balanceAdjustment -= $oldAmount; // Remove credit
                } else {
                    $balanceAdjustment += $oldAmount; // Remove debit
                }

                // Apply the effect of the new transaction
                if ($newType === 'Credit') {
                    $balanceAdjustment += $newAmount; // Add new credit
                } else {
                    $balanceAdjustment -= $newAmount; // Add new debit
                }

                // Update the account balance
                $balanceQuery = "UPDATE account SET Balance = Balance + $balanceAdjustment WHERE AccountID = '$accountID'";
                if (!mysqli_query($conn, $balanceQuery)) {
                    throw new Exception("Error updating account balance: " . mysqli_error($conn));
                }

                // Commit the transaction
                mysqli_commit($conn);
                $message = "Transaction and account balance updated successfully.";
                header("Location: ../manage_transaction.php?message=" . urlencode($message));
                exit();
            } catch (Exception $e) {
                // Rollback in case of error
                mysqli_rollback($conn);
                $message = $e->getMessage();
                error_log($message); // Log for debugging
            }
        } else {
            $message = "Transaction not found.";
        }
    }
}

// Fetch transaction details to populate the form
$transaction_id = isset($_GET['transactionID']) ? mysqli_real_escape_string($conn, $_GET['transactionID']) : '';
$transaction = null;

if ($transaction_id) {
    $result = mysqli_query($conn, "SELECT * FROM transaction WHERE transactionID = '$transaction_id'");
    if ($result) {
        $transaction = mysqli_fetch_assoc($result);
    } else {
        $message = "Error fetching transaction details: " . mysqli_error($conn);
        error_log($message); // Log for debugging
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
            max-width: 700px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
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
            max-width: 400px; /* Set desired width here */
            margin: 0 auto;
            padding: 20px 0; 
            
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
                <h2>Update Transaction</h2>
                <?php if (!empty($message)): ?>
                    <p class="message"><?= htmlspecialchars($message); ?></p>
                <?php endif; ?>
                <?php if ($transaction): ?>
                <form class="form" method="POST" action="">
                    <input type="hidden" name="transactionID" value="<?= htmlspecialchars($transaction['transactionID']); ?>">
                    <input type="hidden" name="account_AccountID" value="<?= htmlspecialchars($transaction['account_AccountID']); ?>">
                    <div>
                        <label for="Date">Date</label>
                        <input type="date" id="Date" name="transactionDate" value="<?= htmlspecialchars($transaction['transactionDate']); ?>" required>
                    </div>
                        <select name="transactionType" required>
                        <option value="Credit" <?= $transaction['transactionType'] === 'Credit' ? 'selected' : ''; ?>>Credit</option>
                        <option value="Debit" <?= $transaction['transactionType'] === 'Debit' ? 'selected' : ''; ?>>Debit</option>
                        </select>
                    <div>
                        <label for="transactionAmount">Amount</label>
                        <input type="number" id="transactionAmount" name="transactionAmount" step="0.01" required value="<?= htmlspecialchars($transaction['transactionAmount']); ?>" required>
                    </div>
                    <button type="submit" name="update">Update</button>
                </form>
                <?php else: ?>
                    <p class="message">No transaction found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>