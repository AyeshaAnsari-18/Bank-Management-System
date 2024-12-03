<?php
session_start();
include '../connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Fetch all customers
$result = mysqli_query($conn, "SELECT * FROM branch");
if (!$result) {
    die("Error fetching branches: " . mysqli_error($conn));
}
$branches = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get message from query string
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Branch</title>
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
                <a href="manage_reports.php">Reports and Analytics</a>
                <a href="manage_audit_logs.php">Audit Logs</a>
                <a href="manage_department.php">Department Management</a>
                <a href="admin_logout.php">Logout</a>
            </nav>
        </header>

        <!-- Main Content -->
        <div class="user-info">
            <h1>Branch Information</h1>
            <?php if (!empty($message)): ?>
                <p style="color:green;" class="message"><?= htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="buttons">
                <button onclick="window.location.href='BranchManagement/create_branch.php'">Insert New Branch</button>
            </div>

            <!-- Customer Table -->
            <table>
                <thead>
                    <tr>
                        <th>Branch ID</th>
                        <th>Branch Name</th>
                        <th>Location</th>
                        <th>Branch Manager ID</th>
                        <th>Rating</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($branches as $branch): ?>
                        <tr>
                            <td><?= htmlspecialchars($branch['branchID'] ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($branch['branchName'] ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($branch['location'] ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($branch['branchManagerID'] ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($branch['performanceRating'] ?? 'N/A'); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="update" 
                                        onclick="window.location.href='BranchManagement/update_branch.php?branchID=<?= $branch['branchID']; ?>'">
                                        Update
                                    </button>
                                    <button class="delete" 
                                        onclick="if(confirm('Are you sure you want to delete this branch?')) 
                                            window.location.href='BranchManagement/delete_branch.php?branchID=<?= $branch['branchID']; ?>'">
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
