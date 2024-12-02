<?php
session_start();
include '../../connection.php';
$message = '';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $departmentID = $_POST['departmentID'];
    $branchID = $_POST['branchID'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $role = $_POST['role'];
    $salary = $_POST['salary'];
    $hireDate = $_POST['hireDate'];

    // Check if departmentID exists in the department table
    $checkDepartmentQuery = "SELECT * FROM department WHERE departmentID = '$departmentID'";
    $departmentResult = mysqli_query($conn, $checkDepartmentQuery);
        if (mysqli_num_rows($departmentResult) > 0) {
            // Department exists, now check if branchID exists in the branch table
            if($branchID != NULL){
                $checkBranchQuery = "SELECT * FROM branch WHERE branchID = '$branchID'";
                $branchResult = mysqli_query($conn, $checkBranchQuery);
        
                if (mysqli_num_rows($branchResult) > 0) {
                    // Branch exists, proceed with employee insertion
                    $query = "INSERT INTO employee (departmentID, branchID, firstName, lastName, email, phoneNumber, role, salary, hireDate)
                            VALUES ('$departmentID', '$branchID','$firstName', '$lastName', '$email', '$phoneNumber', '$role', '$salary', '$hireDate')";
                    if (mysqli_query($conn, $query)) {
                        $message = "Employee added successfully.";
                        header("Location: ../manage_employees.php?message=" . urlencode($message));
                        exit();
                    } else {
                        $message = "Error adding employee: " . mysqli_error($conn);
                    }
                } else {
                    // Branch does not exist
                    $message = "Invalid Branch ID. Please enter a valid branch.";
                }
            }
        $query = "INSERT INTO employee (departmentID, firstName, lastName, email, phoneNumber, role, salary, hireDate)
                            VALUES ('$departmentID','$firstName', '$lastName', '$email', '$phoneNumber', '$role', '$salary', '$hireDate')";
                if (mysqli_query($conn, $query)) {
                    $message = "Employee added successfully.";
                    header("Location: ../manage_employees.php?message=" . urlencode($message));
                    exit();
                } else {
                    $message = "Error adding employee: " . mysqli_error($conn);
                }
            }
     else {
        // Department does not exist
        $message = "Invalid Department ID. Please enter a valid department.";
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
                <h2>Add Employee</h2>
                <?php if (!empty($message)): ?>
                    <p class="message"><?= htmlspecialchars($message); ?></p>
                <?php endif; ?>
                <form class="form" method="POST" action="">
                <div>
                        <label for="departmentID">Department ID</label>
                        <input type="text" id="departmentID" name="departmentID" required>
                    </div>
                    <div>
                        <label for="branchID">Branch ID</label>
                        <input type="text" id="branchID" name="branchID">
                    </div>
                    <div>
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="firstName" required>
                    </div>
                    <div>
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" required>
                    </div>
                    <div>
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div>
                        <label for="phoneNumber">Phone Number</label>
                        <input type="number" id="phoneNumber" name="phoneNumber" required>
                    </div>
                    <div>
                        <label for="role">Role</label>
                        <input type="text" id="role" name="role" required>
                    </div>
                    <div>
                        <label for="salary">Salary</label>
                        <input type="number" id="salary" name="salary" step="0.01" required>
                    </div>
                    <div>
                        <label for="hireDate">Hire Date</label>
                        <input type="date" id="hireDate" name="hireDate" required>
                    </div>
                    <button type="submit" name="create">Add Employee</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
