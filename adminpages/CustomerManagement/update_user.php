<?php
session_start();
include '../../connection.php'; // Ensure connection.php initializes $conn properly
$message = ''; // Message for success or error

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

// Handle form submissions for Update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Fetch and sanitize input
    $customerID = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $accountID = mysqli_real_escape_string($conn, $_POST['account_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validate IDs
    if (!is_numeric($customerID) || !is_numeric($accountID)) {
        $message = "Invalid Customer ID or Account ID.";
    } else {
        // Update customer information in the database
        $sql = "UPDATE customer 
                SET Name = '$name', Email = '$email', Address = '$address', 
                    DateOfBirth = '$dob', Phone = '$phone', UserPassword = '$password' 
                WHERE customerId = '$customerID' AND account_accountID = '$accountID'";

        if (mysqli_query($conn, $sql)) {
            if (mysqli_affected_rows($conn) > 0) {
                // Redirect to manage_users.php after a successful update
                header("Location: ../manage_users.php?message=" . urlencode("Customer updated successfully."));
                exit();
            } else {
                $message = "No changes made or invalid Customer ID/Account ID.";
            }
        } else {
            $message = "Error updating customer: " . mysqli_error($conn);
            error_log($message); // Log error for debugging
        }
    }
}

// Fetch customer details to populate the form
$customerID = isset($_GET['customer_id']) ? mysqli_real_escape_string($conn, $_GET['customer_id']) : '';
$customer = null;

if ($customerID) {
    $result = mysqli_query($conn, "SELECT * FROM customer WHERE customerId = '$customerID'");
    if ($result) {
        $customer = mysqli_fetch_assoc($result);
    } else {
        $message = "Error fetching customer details: " . mysqli_error($conn);
        error_log($message); // Log error for debugging
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Customer</title>
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
                <a href="../manage_employees.php">Employee Management</a>
                <a href="../manage_transaction.php">Transaction Management</a>
                <a href="../approve_loans.php">Loan Management</a>
                <a href="../manage_branch.php">Branch Management</a>
                <a href="../manage_support.php">Customer Feedback Management</a>
                <a href="../manage_department.php">Department Management</a>
                <a href="adminlogin.html">Logout</a>
            </nav>
        </header>
        <div class="user-info">
            <div class="card">
                <h2>Update Customer</h2>
                <?php if (!empty($message)): ?>
                    <p class="message"><?= htmlspecialchars($message); ?></p>
                <?php endif; ?>
                <?php if ($customer): ?>
                <form class="form" method="POST" action="">
                    <input type="hidden" name="customer_id" value="<?= htmlspecialchars($customer['customerId']); ?>">
                    <input type="hidden" name="account_id" value="<?= htmlspecialchars($customer['account_accountID']); ?>">
                    <div>
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($customer['Name']); ?>" required>
                    </div>
                    <div>
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($customer['Email']); ?>" required>
                    </div>
                    <div>
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" value="<?= htmlspecialchars($customer['Address']); ?>" required>
                    </div>
                    <div>
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($customer['DateOfBirth']); ?>" required>
                    </div>
                    <div>
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($customer['Phone']); ?>" required>
                    </div>
                    <div>
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" value="<?= htmlspecialchars($customer['UserPassword']); ?>" required>
                    </div>
                    <button type="submit" name="update">Update Customer</button>
                </form>
                <?php else: ?>
                    <p class="message">No customer found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
