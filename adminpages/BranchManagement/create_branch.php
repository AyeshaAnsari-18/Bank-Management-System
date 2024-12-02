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
    $branchName = $_POST['branchName'];
    $location = $_POST['location'];
    $branchManagerID = $_POST['branchManagerID'];
    $performanceRating = $_POST['performanceRating'];

    if (is_numeric($performanceRating) && $performanceRating >= 0 && $performanceRating <= 5) {
        // Check if the branchManagerID exists in the employee table
        if($branchManagerID!=NULL){
        $checkManagerQuery = "SELECT * FROM employee WHERE employeeID = '$branchManagerID'";
        $managerResult = mysqli_query($conn, $checkManagerQuery);

        if (mysqli_num_rows($managerResult) > 0) {
            // Manager exists, proceed with branch insertions
            $query = "INSERT INTO branch (branchName, location, branchManagerID, performanceRating)
                    VALUES ('$branchName', '$location', '$branchManagerID', '$performanceRating')";
            
            if (mysqli_query($conn, $query)) {
                $message = "Branch added successfully.";
                header("Location: ../manage_branch.php?message=" . urlencode($message));
                exit();
            } else {
                $message = "Error adding branch: " . mysqli_error($conn);
            }
        } else {
            $message = "Invalid Branch Manager ID. Please enter a valid employee ID.";
        }
        }
        else{
            $query = "INSERT INTO branch (branchName, location,  performanceRating)
                    VALUES ('$branchName', '$location', '$performanceRating')";
            
            if (mysqli_query($conn, $query)) {
                $message = "Branch added successfully.";
                header("Location: ../manage_branch.php?message=" . urlencode($message));
                exit();
            } else {
                $message = "Error adding branch: " . mysqli_error($conn);
                }
        }
    }
    else {
        $message = "Performance Rating must be a number between 1 and 5.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Branch</title>
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
            color: darkblue;
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
                <a href="../manage_department.php">Department Management</a>
                <a href="../manage_support.php">Customer Feedback Management</a>
                <a href="adminlogin.html">Logout</a>
            </nav>
        </header>
        <div class="user-info">
            <div class="card">
                <h2>Add Branch</h2>
                <?php if (!empty($message)): ?>
                    <p class="message"><?= htmlspecialchars($message); ?></p>
                <?php endif; ?>
                <form class="form" method="POST" action="">
                    <div>
                        <label for="branchName">Branch Name</label>
                        <input type="text" id="branchName" name="branchName" required>
                    </div>
                    <div>
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" required>
                    </div>
                    <div>
                        <label for="branchManagerID">Branch Manager ID</label>
                        <input type="text" id="branchManagerID" name="branchManagerID" >
                    </div>
                    <div>
                        <label for="performanceRating">Performance Rating (1 to 5)</label>
                        <input type="number" id="performanceRating" name="performanceRating" step="0.1" min="1" max="5" required>
                    </div>
                    <button type="submit" name="create">Add Branch</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
