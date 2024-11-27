<?php 
session_start();
include '../connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Fetch all support requests
$result = mysqli_query($conn, "SELECT cs.*, c.email FROM customer_support cs 
                                INNER JOIN customer c 
                                ON cs.customer_customerID = c.customerID ");
if (!$result) {
    die("Error fetching support requests: " . mysqli_error($conn));
}
$support_requests = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Message for actions
$message = isset($_GET['message']) ? $_GET['message'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customer Support</title>
    <link rel="stylesheet" href="../css/adminpages.css">
    <style>
        /* General Page Styling */
        .user-info h1 {
            font-size: 40px;
        }
        /* Message Styling */
        .message {
            font-weight: bold;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }
        .message.success {
            color: green;
            background-color: #e6ffee;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            color: red;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        /* Table Styling */
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
        /* Action Buttons Styling */
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .action-buttons button {
            padding: 5px 10px;
            font-size: 14px;
            cursor: pointer;
            background-color: #032d60;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .action-buttons button:hover {
            opacity: 0.8;
            background-color: rgb(38, 152, 212);
        }
        .action-buttons span {
            color: grey;
            font-size: 14px;
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
                <a href="admin_logout.php">Logout</a>
            </nav>
        </header>

        <!-- Main Content -->
        <div class="user-info">
            <h1>Customer Support Information</h1>
            <!-- Display Message -->
            <?php if (!empty($message)): ?>
                <p class="message <?= $status === 'success' ? 'success' : 'error'; ?>">
                    <?= htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>

            <!-- Support Requests Table -->
            <table>
                <thead>
                    <tr>
                        <th>Support ID</th>
                        <th>Customer ID</th>
                        <th>Issue Type</th>
                        <th>Description</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($support_requests as $request): ?>
                        <tr>
                            <td><?= htmlspecialchars($request['Support_id']); ?></td>
                            <td><?= htmlspecialchars($request['customer_customerID']); ?></td>
                            <td><?= htmlspecialchars($request['issue_type']); ?></td>
                            <td><?= htmlspecialchars($request['description']); ?></td>
                            <td><?= htmlspecialchars($request['email']); ?></td>
                            <td><?= htmlspecialchars($request['status']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($request['status'] === 'Open'): ?>
                                        <button class="respond" 
                                            onclick="if(confirm('Are you sure you want to respond and close this ticket?')) 
                                                window.location.href='CustomerSupportManagement/respond_support.php?Support_id=<?= $request['Support_id']; ?>'">
                                            Respond and Close
                                        </button>
                                    <?php else: ?>
                                        <span>Closed</span>
                                    <?php endif; ?>
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
