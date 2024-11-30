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

if (!$admin) {
    die("Error: Admin ID does not exist in the database.");
}

// Handle form submission for inserting a new record
if (isset($_POST['insertRecord'])) {
    $action = mysqli_real_escape_string($conn, $_POST['action']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);
    $ip_address = $_SERVER['REMOTE_ADDR']; // Get IP address
    $timestamp = date('Y-m-d H:i:s'); // Current timestamp

    // Insert the new record into the audit_logs table
    $insert_query = "INSERT INTO audit_logs (admin_id, action, details, ip_address, timestamp) 
                     VALUES ('$admin_id', '$action', '$details', '$ip_address', '$timestamp')";

    if (mysqli_query($conn, $insert_query)) {
        echo "<p class='message success'>New record inserted successfully.</p>";
    } else {
        echo "<p class='message error'>Error inserting record: " . mysqli_error($conn) . "</p>";
    }

    // Reload the page to update the table
    header("Location: manage_audit_logs.php");
    exit();
}

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
    <style>
        .user-info h1{
            font-size: 40px;
            padding-bottom: 10px;
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
        
        .action-buttons {
            padding-bottom: 10px;
            display: flex;
            gap: 10px;
            justify-content: center;
            
        }
        .action-buttons button:hover {
            background-color: rgb(38, 152, 212);
            color: white;
        }
        .action-buttons button {
            padding: 5px 10px;
            font-size: 14px;
            cursor: pointer;
            background-color: #032d60;
            color: white;
        }
        .action-buttons button.update {
            background-color: #032d60;
            color: white;
            border: none;
        }
        .action-buttons button.delete {
            background-color: #032d60;
            color: white;
            border: none;
        }
        .action-buttons button:hover {
            opacity: 0.8;
            background-color: rgb(38, 152, 212);
        }
    </style>
</head>
<body>
<div id="main">
    <!-- Header Section -->
    <header class="header">
        <div id="logo">
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
            <a href="admin_logout.php">Logout</a>
        </nav>
    </header>

    <div class="user-info">
        <h1>Audit Logs</h1>
        <!-- Action Buttons -->
        <div class="action-buttons">
            <button onclick="window.location.href='AuditManagement/download_audit_report.php'">Download Audit Report</button>
            <button onclick="document.getElementById('insertForm').style.display='block';">Insert New Record</button>
        </div>

        <!-- Hidden form for inserting a record -->
        <div id="insertForm" style="display: none; margin-top: 20px;">
            <form method="POST" action="">
                <label for="action">Action:</label>
                <input type="text" id="action" name="action" placeholder="Enter action" disabled required>

                <label for="details">Details:</label>
                <input type="text" id="details" name="details" placeholder="Enter details" disabled required>

                <button type="submit" id="submitButton" name="insertRecord" disabled>Submit</button>
            </form>
        </div>

        <!-- Table -->
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
    <script>
        function enableInsertForm() {
            // Show the form
            const form = document.getElementById('insertForm');
            form.style.display = 'block';

            // Enable the input fields and submit button
            document.getElementById('action').disabled = false;
            document.getElementById('details').disabled = false;
            document.getElementById('submitButton').disabled = false;
        }

        // Attach the function to the button
        const insertButton = document.querySelector('.action-buttons button:nth-child(2)');
        insertButton.addEventListener('click', enableInsertForm);
    </script>

</div>
</body>
</html>
