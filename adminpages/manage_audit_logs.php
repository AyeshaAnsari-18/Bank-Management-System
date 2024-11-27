<?php
// Start session and include database connection
session_start();
include '../connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Fetch admin information
$admin_id = $_SESSION['admin_id'];
$admin_name_query = "SELECT admin_name FROM admin WHERE adminID = '$admin_id'";
$result = mysqli_query($conn, $admin_name_query);
$admin = mysqli_fetch_assoc($result);

// Fetch audit logs
$audit_logs_query = "SELECT a.id, ad.admin_name, a.action, a.details, a.ip_address, a.timestamp 
                     FROM audit_logs a 
                     JOIN admin ad ON a.admin_id = ad.adminID 
                     ORDER BY a.timestamp DESC";
$audit_logs = mysqli_query($conn, $audit_logs_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs</title>
    <link rel="stylesheet" href="../css/adminpages.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <!-- Main container -->
<div id="main">
    <!-- Header Section -->
    <header class="header">
        <!-- Bank Logo -->
        <div id="logo" style="padding-top: 20px;">
            <img src="../logo.png" alt="Bank Logo">
        </div>

        <!-- Navigation Links -->
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
            <a href="admin_logout.php">Logout</a>
        </nav>
    </header>

    <!-- Content Section -->
    <div class="content">
        <h1>Audit Logs</h1>
        <form method="POST" action="download_audit_report.php">
            <button type="submit">Download Audit Report</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($audit_logs)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['admin_name']) ?></td>
                        <td><?= htmlspecialchars($row['action']) ?></td>
                        <td><?= htmlspecialchars($row['details']) ?></td>
                        <td><?= htmlspecialchars($row['ip_address']) ?></td>
                        <td><?= $row['timestamp'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
