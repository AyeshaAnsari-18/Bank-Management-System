<?php
session_start();
include '../connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Fetch all customers
$result = mysqli_query($conn, "SELECT * FROM department");
if (!$result) {
    die("Error fetching departments: " . mysqli_error($conn));
}
$departments = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get message from query string
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments</title>
    <link rel="stylesheet" href="../css/adminpages.css">
    <style>
        .user-info h1{
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
            width: 20%;
            height:40px;
        }
        .buttons button:hover {
            background-color: rgb(28, 120, 211);
            color: white;
        }
        .buttons button {
            padding: 10px;
            background-color: #032d60;
            color: white;
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
            background-color: darkblue;
            color: white;
            border: none;
        }
        .action-buttons button.delete {
            background-color: darkblue;
            color: white;
            border: none;
        }
        .action-buttons button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div id="main">
        <!-- Header Section -->
        <header class="header">
            <div id="logo" style="padding-top: 20px;">
                <img src="../logo.png" alt="Bank Logo">
            </div>
            <nav class="nav-links">
                <a href="adminhome.php">Home</a>
                <a href="manage_users.php">Customer Management</a>
                <a href="manage_employees.php">Employee Management</a>
                <a href="manage_transaction.php">Transaction Management</a>
                <a href="manage_loan.php">Loan Management</a>
                <a href="manage_branch.php">Branch Management</a>
                <a href="manage_support.php">Customer Feedback Management</a>
                <a href="manage_department.php">Department Management</a>
                <a href="manage_account.php">Accounts Management</a>
                <a href="manage_reports.php">Reports and Analytics</a>
                <a href="manage_audit_logs.php">Audit Logs</a>
                <a href="admin_logout.php">Logout</a>
            </nav>
        </header>

        <!-- Main Content -->
        <div class="user-info">
            <h1>Department Information</h1>
            <?php if (!empty($message)): ?>
                <p style="color:green;" class="message"><?= htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="buttons">
                <button onclick="window.location.href='DepartmentManagement/create_dep.php'">Insert New Department</button>
            </div>

            <!-- Customer Table -->
            <table>
                <thead>
                    <tr>
                        <th>Department ID</th>
                        <th>Department Name</th>
                        <th>Manager ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($departments as $department): ?>
                        <tr>
                            <td><?= htmlspecialchars($department['departmentID'] ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($department['departmentName'] ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($department['managerID'] ?? 'N/A'); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="update" 
                                        onclick="window.location.href='DepartmentManagement/update_dep.php?departmentID=<?= $department['departmentID']; ?>'">
                                        Update
                                    </button>
                                    <button class="delete" 
                                        onclick="if(confirm('Are you sure you want to delete this employee?')) 
                                            window.location.href='DepartmentManagement/delete_dep.php?departmentID=<?= $department['departmentID']; ?>'">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
