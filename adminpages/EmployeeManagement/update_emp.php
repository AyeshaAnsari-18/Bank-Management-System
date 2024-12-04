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
    $employeeID = mysqli_real_escape_string($conn, $_POST['employeeID']);
    $newBranchID = mysqli_real_escape_string($conn, $_POST['branchID']);
    $newDepartmentID = mysqli_real_escape_string($conn, $_POST['departmentID']);
    $firstName = mysqli_real_escape_string($conn, $_POST['fname']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phoneNumber = mysqli_real_escape_string($conn, $_POST['phone']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $salary = mysqli_real_escape_string($conn, $_POST['Salary']);
    $hireDate = mysqli_real_escape_string($conn, $_POST['hdate']);

    // Get the current branch and department of the employee
    $queryCurrentDetails = "SELECT branchID, departmentID FROM employee WHERE employeeID = '$employeeID'";
    $resultCurrentDetails = mysqli_query($conn, $queryCurrentDetails);

    if ($resultCurrentDetails && mysqli_num_rows($resultCurrentDetails) > 0) {
        $currentDetails = mysqli_fetch_assoc($resultCurrentDetails);
        $currentBranchID = $currentDetails['branchID'];
        $currentDepartmentID = $currentDetails['departmentID'];

        // If branch ID is being changed, check and handle the branch manager case
        if ($currentBranchID != $newBranchID) {
            // Check if the employee is a manager in the current branch
            $checkManagerQuery = "SELECT * FROM branch WHERE branchManagerID = '$employeeID'";
            $managerResult = mysqli_query($conn, $checkManagerQuery);

            if ($managerResult && mysqli_num_rows($managerResult) > 0) {
                // Update branchManagerID to NULL in the old branch
                $updateManagerQuery = "UPDATE branch SET branchManagerID = NULL WHERE branchID = '$currentBranchID'";
                mysqli_query($conn, $updateManagerQuery);
            }
        }

        // Validate if the new branchID exists in the branch table
        $checkBranchQuery = "SELECT * FROM branch WHERE branchID = '$newBranchID'";
        $branchResult = mysqli_query($conn, $checkBranchQuery);

        // Validate if the new departmentID exists in the department table
        $checkDepartmentQuery = "SELECT * FROM department WHERE departmentID = '$newDepartmentID'";
        $departmentResult = mysqli_query($conn, $checkDepartmentQuery);

        if (mysqli_num_rows($branchResult) > 0 && mysqli_num_rows($departmentResult) > 0) {
            // Proceed with employee update
            $queryUpdate = "UPDATE employee 
                            SET branchID = '$newBranchID', departmentID = '$newDepartmentID', firstName = '$firstName', 
                                lastName = '$lastName', email = '$email', phoneNumber = '$phoneNumber', role = '$role', 
                                salary = '$salary', hireDate = '$hireDate'
                            WHERE employeeID = '$employeeID'";
            if (mysqli_query($conn, $queryUpdate)) {
                if (mysqli_affected_rows($conn) > 0) {
                    // Redirect to manage_employees.php after successful update
                    header("Location: ../manage_employees.php?message=" . urlencode("Employee updated successfully."));
                    exit();
                } else {
                    $message = "No changes made or invalid Employee ID.";
                }
            } else {
                $message = "Error updating employee: " . mysqli_error($conn);
            }
        } else {
            $message = "Invalid Branch ID or Department ID. Please enter valid IDs.";
        }
    } else {
        $message = "Employee not found.";
    }
}

// Fetch employee details to populate the form
$employeeID = isset($_GET['employeeID']) ? mysqli_real_escape_string($conn, $_GET['employeeID']) : '';
$employee = null;

if ($employeeID) {
    $result = mysqli_query($conn, "SELECT * FROM employee WHERE employeeID = '$employeeID'");
    if ($result) {
        $employee = mysqli_fetch_assoc($result);
    } else {
        $message = "Error fetching employee details: " . mysqli_error($conn);
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
                <?php if ($employee): ?>
                <form class="form" method="POST" action="">
                    <input type="hidden" name="employeeID" value="<?= htmlspecialchars($employee['employeeID']); ?>">
                    <div>
                        <label for="departmentID">Department ID</label>
                        <input type="text" id= "departmentID" name="departmentID" value="<?= htmlspecialchars($employee['departmentID']); ?>">
                    </div>
                    <div>
                        <label for="branchID">Branch ID</label>
                        <input type="text" id= "branchID" name="branchID" value="<?= htmlspecialchars($employee['branchID']); ?>">
                    </div>
                    <div>
                        <label for="fname">First Name</label>
                        <input type="text" id="fname" name="fname" value="<?= htmlspecialchars($employee['firstName']); ?>" required>
                    </div>
                    <div>
                        <label for="lname">Last Name</label>
                        <input type="text" id="lname" name="lname" value="<?= htmlspecialchars($employee['lastName']); ?>" required>
                    </div>
                    <div>
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($employee['email']); ?>" required>
                    </div>
                    <div>
                        <label for="phone">Phone Number</label>
                        <input type="number" id="phone" name="phone" value="<?= htmlspecialchars($employee['phoneNumber']); ?>" required>
                    </div>
                    <div>
                        <label for="role">Role</label>
                        <input type="text" id="role" name="role" value="<?= htmlspecialchars($employee['role']); ?>" required>
                    </div>
                    <div>
                        <label for="Salary">Salary</label>
                        <input type="number" id="Salary" name="Salary" value="<?= htmlspecialchars($employee['salary']); ?>" required>
                    </div>
                    <div>
                        <label for="hdate">Hire Date</label>
                        <input type="date" id="hdate" name="hdate" value="<?= htmlspecialchars($employee['hireDate']); ?>" required>
                    </div>
                    <button type="submit" name="update">Update Employee</button>
                </form>
                <?php else: ?>
                    <p class="message">No employee found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
