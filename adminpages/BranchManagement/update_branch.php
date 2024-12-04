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
    $branchID = mysqli_real_escape_string($conn, $_POST['branchID']);
    $branchName = mysqli_real_escape_string($conn, $_POST['branchName']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $branchManagerID = mysqli_real_escape_string($conn, $_POST['branchManagerID']);
    $performanceRating = mysqli_real_escape_string($conn, $_POST['performanceRating']);

    // Validate if the branchManagerID exists in the employee table
    $checkManagerQuery = "SELECT * FROM employee WHERE employeeID = '$branchManagerID'";
    $managerResult = mysqli_query($conn, $checkManagerQuery);

    if (mysqli_num_rows($managerResult) > 0) {
        // Proceed with branch update
        $queryUpdate = "UPDATE branch SET branchName = '$branchName', location = '$location', 
                        branchManagerID = '$branchManagerID', performanceRating = '$performanceRating'
                        WHERE branchID = '$branchID'";
        
        if (mysqli_query($conn, $queryUpdate)) {
            if (mysqli_affected_rows($conn) > 0) {
                // Redirect to manage_branch.php after successful update
                header("Location: ../manage_branch.php?message=" . urlencode("Branch updated successfully."));
                exit();
            } else {
                $message = "No changes made or invalid Branch ID.";
            }
        } else {
            $message = "Error updating branch: " . mysqli_error($conn);
        }
    } else {
        $message = "Invalid Branch Manager ID. Please enter a valid employee ID.";
    }
}

// Fetch branch details to populate the form
$branchID = isset($_GET['branchID']) ? mysqli_real_escape_string($conn, $_GET['branchID']) : '';
$branch = null;

if ($branchID) {
    $result = mysqli_query($conn, "SELECT * FROM branch WHERE branchID = '$branchID'");
    if ($result) {
        $branch = mysqli_fetch_assoc($result);
    } else {
        $message = "Error fetching branch details: " . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Employee</title>
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
                <h2>Update Employee</h2>
                <?php if (!empty($message)): ?>
                    <p class="message"><?= htmlspecialchars($message); ?></p>
                <?php endif; ?>
                <form class="form" method="POST" action="">
                <?php if ($branch): ?>
                <form class="form" method="POST" action="">
                    <input type="hidden" name="branchID" value="<?= htmlspecialchars($branch['branchID']); ?>">
                    <div>
                        <label for="branchName">Branch Name</label>
                        <input type="text" id="branchName" name="branchName" value="<?= htmlspecialchars($branch['branchName']); ?>" required>
                    </div>
                    <div>
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" value="<?= htmlspecialchars($branch['location']); ?>" required>
                    </div>
                    <div>
                        <label for="branchManagerID">Branch Manager ID</label>
                        <input type="text" id="branchManagerID" name="branchManagerID" value="<?= htmlspecialchars($branch['branchManagerID']); ?>" required>
                    </div>
                    <div>
                        <label for="performanceRating">Performance Rating (1 to 5)</label>
                        <input type="number" id="performanceRating" name="performanceRating" step="0.1" min="1" max="5" value="<?= htmlspecialchars($branch['performanceRating']); ?>" required>
                    </div>
                    <button type="submit" name="update">Update Branch</button>
                </form>
                <?php else: ?>
                    <p class="message">No branch found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
