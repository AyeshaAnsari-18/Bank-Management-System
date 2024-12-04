<?php
session_start();
include '../../connection.php';
$message = '';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

// Check if account ID is provided
if (isset($_GET['account_id'])) {
    $account_id = intval($_GET['account_id']);

    // Fetch account details
    $result = mysqli_query($conn, "SELECT * FROM account WHERE AccountID = $account_id");
    if ($result && mysqli_num_rows($result) > 0) {
        $account = mysqli_fetch_assoc($result);
    } else {
        header("Location: ../manage_account.php?message=" . urlencode("Account not found."));
        exit();
    }
} else {
    header("Location: ../manage_account.php?message=" . urlencode("Invalid account ID."));
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $account_type = mysqli_real_escape_string($conn, $_POST['account_type']);
    $balance = $_POST['balance'];

    // Update account
    $sql = "UPDATE account SET AccountType = '$account_type', Balance = '$balance' WHERE AccountID = $account_id";
    if (mysqli_query($conn, $sql)) {
        $message = "Account updated successfully.";
        header("Location: ../manage_account.php?message=" . urlencode($message));
        exit();
    } else {
        $message = "Error updating account: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Account</title>
    <link rel="stylesheet" href="../../css/adminpages.css">
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
            gap: 15px;
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
            background-color: darkblue;
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
    </style>
</head>
<body>
    <div id="main">
        <header class="header">
            <div id="logo" style="padding-top: 20px;">
                <img src="../../logo.png" alt="Bank Logo">
            </div>
            <nav class="nav-links">
                <a href="../adminhome.php">Home</a>
                <a href="../manage_users.php">Customer Management</a>
                <a href="../manage_employees.php">Employee Management</a>
                <a href="../manage_transaction.php">Transaction Management</a>
                <a href="../manage_loan.php">Loan Management</a>
                <a href="../manage_branch.php">Branch Management</a>
                <a href="../manage_support.php">Customer Feedback Management</a>
                <a href="../manage_department.php">Department Management</a>
                <a href="../manage_account.php">Accounts Management</a>
                <a href="../manage_reports.php">Reports and Analytics</a>
                <a href="../manage_audit_logs.php">Audit Logs</a>
                <a href="../admin_logout.php">Logout</a>
            </nav>
        </header>
        <div class="user-info">
            <div class="card">
                <h2>Update Account</h2>
                <?php if (!empty($message)): ?>
                    <p class="message"><?= htmlspecialchars($message); ?></p>
                <?php endif; ?>
                <form class="form" method="POST" action="">
                    <div>
                        <label for="account_type">Account Type</label>
                        <input type="text" id="account_type" name="account_type" value="<?= htmlspecialchars($account['AccountType']); ?>" required>
                    </div>
                    <div>
                        <label for="balance">Balance</label>
                        <input type="number" id="balance" name="balance" value="<?= htmlspecialchars($account['Balance']); ?>" required>
                    </div>
                    <button type="submit" name="update">Update Account</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
