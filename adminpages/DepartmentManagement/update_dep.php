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
    $departmentID = mysqli_real_escape_string($conn, $_POST['departmentID']);
    $departmentName = mysqli_real_escape_string($conn, $_POST['departmentName']);
    $managerID = mysqli_real_escape_string($conn, $_POST['managerID']);

    if($managerID !=NULL){
        // Validate if the branchManagerID exists in the employee table
        $checkManagerQuery = "SELECT * FROM employee WHERE employeeID = '$managerID'";
        $managerResult = mysqli_query($conn, $checkManagerQuery);

        if (mysqli_num_rows($managerResult) > 0) {
            // Proceed with branch update
            $queryUpdate = "UPDATE department SET departmentName = '$departmentName',
                            managerID = '$managerID' WHERE departmentID = '$departmentID'";
            
            if (mysqli_query($conn, $queryUpdate)) {
                if (mysqli_affected_rows($conn) > 0) {
                    // Redirect to manage_branch.php after successful update
                    header("Location: ../manage_department.php?message=" . urlencode("Department updated successfully."));
                    exit();
                } else {
                    $message = "No changes made or invalid Department ID.";
                }
            } else {
                $message = "Error updating department: " . mysqli_error($conn);
            }
        } else {
            $message = "Invalid Manager ID. Please enter a valid employee ID.";
        }
    }
    else{
        $queryUpdate = "UPDATE department SET departmentName = '$departmentName',
                            managerID = NULL WHERE departmentID = '$departmentID'";
            
            if (mysqli_query($conn, $queryUpdate)) {
                if (mysqli_affected_rows($conn) > 0) {
                    // Redirect to manage_branch.php after successful update
                    header("Location: ../manage_department.php?message=" . urlencode("Department updated successfully."));
                    exit();
                } else {
                    $message = "No changes made or invalid Department ID.";
                }
            } else {
                $message = "Error updating department: " . mysqli_error($conn);
            }
    }
}

// Fetch branch details to populate the form
$departmentID = isset($_GET['departmentID']) ? mysqli_real_escape_string($conn, $_GET['departmentID']) : '';
$department = null;

if ($departmentID) {
    $result = mysqli_query($conn, "SELECT * FROM department WHERE departmentID = '$departmentID'");
    if ($result) {
        $department = mysqli_fetch_assoc($result);
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
                <h2>Update Department</h2>
                <?php if (!empty($message)): ?>
                    <p class="message"><?= htmlspecialchars($message); ?></p>
                <?php endif; ?>
                <form class="form" method="POST" action="">
                <?php if ($department): ?>
                <form class="form" method="POST" action="">
                    <input type="hidden" name="departmentID" value="<?= htmlspecialchars($department['departmentID']); ?>">
                    <div>
                        <label for="departmentName">Department Name</label>
                        <input type="text" id="departmentName" name="departmentName" value="<?= htmlspecialchars($department['departmentName']); ?>" required>
                    </div>
                    <div>
                        <label for="managerID">Manager ID</label>
                        <input type="text" id="managerID" name="managerID" value="<?= htmlspecialchars($department['managerID']); ?>">
                    </div>
                    <button type="submit" name="update">Update department</button>
                </form>
                <?php else: ?>
                    <p class="message">No department found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
