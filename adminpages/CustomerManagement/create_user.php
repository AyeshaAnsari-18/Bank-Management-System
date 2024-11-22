<?php
session_start();
include '../../connection.php';
$message = '';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    // Sanitize input
    $account_type = mysqli_real_escape_string($conn, $_POST['account_type']);
    $balance = $_POST['balance'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $phone = $_POST['phone'];
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Insert into `account` table
    $account_sql = "INSERT INTO account (AccountType, Balance) VALUES ('$account_type', '$balance')";
    if (mysqli_query($conn, $account_sql)) {
        $accountID = mysqli_insert_id($conn); // Get last inserted account ID

        // Check if account ID was retrieved
        if ($accountID) {
            // Insert into `customer` table
            $customer_sql = "INSERT INTO customer (account_accountID, Name, Email, Address, DateOfBirth, Phone, UserPassword) 
                             VALUES ('$accountID', '$name', '$email', '$address', '$dob', '$phone', '$password')";
            if (mysqli_query($conn, $customer_sql)) {
                $message = "User created successfully.";
                header("Location: ../manage_users.php?message=" . urlencode($message));
                exit();
            } else {
                $message = "Error inserting customer: " . mysqli_error($conn);
            }
        } else {
            $message = "Failed to retrieve account ID.";
        }
    } else {
        $message = "Error inserting account: " . mysqli_error($conn);
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
                <a href="#">Employee Management</a>
                <a href="../manage_transaction.php">Transaction Management</a>
                <a href="../approve_loans.php">Loan Management</a>
                <a href="#">Branch Management</a>
                <a href="#">Customer Feedback Management</a>
                <a href="adminlogin.html">Logout</a>
            </nav>
        </header>
        <div class="user-info">
            <div class="card">
                <h2>Create Customer</h2>
                <?php if (!empty($message)): ?>
                    <p class="message"><?= htmlspecialchars($message); ?></p>
                <?php endif; ?>
                <form class="form" method="POST" action="">
                    <div>
                        <label for="account_type">Account Type</label>
                        <input type="text" id="account_type" name="account_type" required>
                    </div>
                    <div>
                        <label for="balance">Balance</label>
                        <input type="number" id="balance" name="balance" required>
                    </div>
                    <div>
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div>
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div>
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                    <div>
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" required>
                    </div>
                    <div>
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div>
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" name="create">Create Customer</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
