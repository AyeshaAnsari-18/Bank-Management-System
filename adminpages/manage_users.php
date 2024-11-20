<?php
session_start();
include '../connection.php';
$message='';
$message1='';
$message2='';
// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Handle form submissions for Create, Update, and Delete operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        // Create Customer
        $account_type = mysqli_real_escape_string($conn, $_POST['account_type']);
        $balance = $_POST['balance']; 
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $phone = $_POST['phone'];
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $mysql = "INSERT INTO account (AccountType, Balance) VALUES ('$account_type', '$balance')";
        if (mysqli_query($conn, $mysql)) {
            $accountID = mysqli_insert_id($conn);
            if (!$accountID) {
            $message = "Failed to retrieve account ID.";
            error_log($message); // Log error for debugging
            }
            else{
            $message = "Account created successfully.";
            $sql = "INSERT INTO customer (account_accountID, Name, Email, Address, DateofBirth, Phone, UserPassword) 
                VALUES ('$accountID', '$name', '$email', '$address', '$dob', '$phone', '$password')";
            if (mysqli_query($conn, $sql)) {
                $message = "Customer created successfully.";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
        } else {
            $message = "Error: " . mysqli_error($conn);
        }


    } elseif (isset($_POST['update'])) {
        // Update Customer
        $customerID = $_POST['customer_id'];
        $accountID = $_POST['account_id'];
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $phone =  $_POST['phone'];
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        $sql = "UPDATE customer SET Name = '$name', Email = '$email', Address = '$address', 
                    DateofBirth = '$dob', Phone = '$phone', UserPassword = '$password' 
                WHERE customerId = '$customerID' and account_accountID = '$accountID'";
        if (mysqli_query($conn, $sql)) {
            $message1 = "Customer updated successfully.";
        } else {
            $message1 = "Error: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['delete'])) {
        // Delete Customer
        $customerID = $_POST['customer_id'];
        
        // Retrieve the associated accountID
        $sql_get_account = "SELECT account_accountID FROM customer WHERE customerID = '$customerID'";
        $result = mysqli_query($conn, $sql_get_account);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $accountID = $row['account_accountID'];
            
            // Delete the customer
            $sql_delete_customer = "DELETE FROM customer WHERE customerID = '$customerID'";
            if (mysqli_query($conn, $sql_delete_customer)) {
                // Delete the associated account
                $sql_delete_account = "DELETE FROM account WHERE accountID = '$accountID'";
                if (mysqli_query($conn, $sql_delete_account)) {
                    $message2 = "Customer and associated account deleted successfully.";
                } else {
                    $message2 = "Error deleting account: " . mysqli_error($conn);
                }
            } else {
                $message2 = "Error deleting customer: " . mysqli_error($conn);
            }
        } else {
            $message2 = "Customer not found or error retrieving accountID: " . mysqli_error($conn);
        }
    }
    
}

// Fetch all customers
$result = mysqli_query($conn, "SELECT * FROM customer");
$customers = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
    <!-- <link rel="stylesheet" href="../formstyle.css"> -->
    <style>
        /* General Styles */
    body {
        font-family: 'Roboto', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f5f7fa;
    }

    .wrapper {
        display: flex;
    }

    /* Sidebar */
    .sidebar {
        width: 250px;
        background-color: #1e3a8a;
        color: #fff;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 22px;
        letter-spacing: 1px;
    }

    .sidebar a {
        text-decoration: none;
        color: #ffffff;
        padding: 10px 15px;
        margin: 5px 0;
        border-radius: 5px;
        display: block;
        font-size: 16px;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background-color: #2563eb;
        color: #fff;
    }

    /* Main Content */
    .content {
        flex: 1;
        padding: 30px;
        background-color: #f5f7fa;
    }

    .content h1 {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #1e3a8a;
    }

    .card {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card h2 {
        font-size: 20px;
        margin-bottom: 15px;
        color: #1e3a8a;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-size: 16px;
        font-weight: bold;
        color: #1e3a8a;
        display: block;
        margin-bottom: 8px;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 5px;
        transition: border-color 0.3s;
    }

    .form-group input:focus {
        outline: none;
        border-color: #2563eb;
    }

    .form-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .form-buttons button {
        padding: 10px 20px;
        font-size: 14px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .form-buttons .add-button {
        background-color: #2563eb;
        color: #fff;
    }

    .form-buttons .add-button:hover {
        background-color: #1e40af;
    }

    .form-buttons .discard-button {
        background-color: #f5f5f5;
        color: #333;
        border: 1px solid #ccc;
    }

    .form-buttons .discard-button:hover {
        background-color: #e5e5e5;
    }

    /* Illustration */
    .illustration {
        text-align: center;
        margin-top: 30px;
    }

    .illustration img {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
    }
    .message {
    font-weight: bold;
    margin-bottom: 15px;
    text-align: center;
    }

    .message p {
        margin: 0;
    }

    .confirmation-msg {
        color: green;
    }

    .error-msg {
        color: red;
    }
    </style>
</head>
<body>
<div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Bank Admin</h2>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="manage_users.php">Manage Users</a>
            <a href="transactions.php">Transactions</a>
            <a href="settings.php">Settings</a>
            <a href="logout.php">Logout</a>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="card">
                <h2>Create Customer</h2>
                <?php if (!empty($message)): ?>
            <p class="message"><?= $message; ?></p>
        <?php endif; ?>
                <form class="form" method="POST" action="">
                    <div class="form-group">
                        <label for="account_type">Account Type</label>
                        <input type="text" id="account_type" name="account_type" required>
                    </div>
                    <div class="form-group">
                        <label for="balance">Account Balance</label>
                        <input type="text" id="balance" name="balance" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-buttons">
                        <button type="button" class="discard-button" onclick="window.location.href='dashboard.php'">Discard</button>
                        <button type="submit" name="create" >Create Customer</button>
                    </div>
                </form>

                <?php if (!empty($message1)): ?>
            <p class="message"><?= $message1; ?></p>
        <?php endif; ?>
                <form class="form" method="POST" action="">
                    <h2>Update Customer</h2>
                    <div class="form-group">
                        <label for="customer_id">Customer ID</label>
                        <input type="text" id="customer_id" name="customer_id" required>
                    </div>
                    <div class="form-group">
                        <label for="account_id">Account ID</label>
                        <input type="text" id="account_id" name="account_id" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-buttons">
                        <button type="button" class="discard-button" onclick="window.location.href='dashboard.php'">Discard</button>
                        <button type="submit" name="update" >Update Customer</button>
                    </div>
                </form>
                
                <?php if (!empty($message2)): ?>
            <p class="message"><?= $message2; ?></p>
        <?php endif; ?>
                <form class="form" method="POST" action="">
                    <h2>Delete Customer</h2>
                    <div class="form-group">
                        <label for="customer_id">Customer ID</label>
                        <input type="text" id="customer_id" name="customer_id" required>
                    </div>
                    <div class="form-buttons">
                        <button type="button" class="discard-button" onclick="window.location.href='dashboard.php'">Discard</button>
                        <button type="submit" name="delete">Delete Customer</button>
                    </div>
                </form>
            </div>
            <div class="illustration">
                <img src="../logo.png" alt="Bank Illustration">
            </div>
        </div>
    </div>
</body>
</html>
