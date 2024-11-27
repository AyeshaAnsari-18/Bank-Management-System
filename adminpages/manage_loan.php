<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

// Fetch all loans with a proper SQL query
$result = $conn->query("SELECT LoanId, a_AccountID, LoanType, Amount, InterestRate, Status FROM loan");
if (!$result) {
    die("Error fetching loans: " . $conn->error);
}
$loans = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Management</title>
    <link rel="stylesheet" href="../css/adminpages.css">
    <style>
        /* General Table Styling */
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

        /* User Info Styling */
        .user-info h2 {
            font-size: 30px;
            margin-top: 20px;
        }

        /* Message Styling */
        .message {
            font-weight: bold;
            margin-bottom: 15px;
        }

        .message.success {
            color: green;
        }

        .message.error {
            color: red;
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
            border: none;
            background-color: #032d60;
            color: white;
        }

        .action-buttons button.update {
            background-color: #032d60;
            color: white;
        }

        .action-buttons button.delete {
            background-color: #032d60;
            color: white;
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
    <div class="user-info">
        <h2>Loans Information</h2>
        <!-- Display Messages -->
        <?php if (isset($_GET['message'])): ?>
            <?php 
                // Check for success or error message
                $messageType = isset($_GET['type']) && $_GET['type'] === 'error' ? 'error' : 'success';
            ?>
            <p class="message <?= $messageType; ?>"><?= htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>Account ID</th>
                    <th>Loan Type</th>
                    <th>Amount</th>
                    <th>Interest Rate</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td><?= htmlspecialchars($loan['LoanId']); ?></td>
                        <td><?= htmlspecialchars($loan['a_AccountID']); ?></td>
                        <td><?= htmlspecialchars($loan['LoanType']); ?></td>
                        <td><?= htmlspecialchars($loan['Amount']); ?></td>
                        <td><?= htmlspecialchars($loan['InterestRate']); ?>%</td>
                        <td><?= $loan['Status'] == 1 ? 'Pending' : 'Approved'; ?></td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($loan['Status'] == 1): ?>
                                    <button class="respond" 
                                        onclick="window.location.href='LoanManagement/respond_loan.php?loan_id=<?= $loan['LoanId']; ?>'">
                                        Respond
                                    </button>
                                <?php else: ?>
                                    <span class="approved">Approved</span>
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
