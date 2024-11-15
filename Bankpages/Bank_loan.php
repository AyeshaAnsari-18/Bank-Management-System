<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Application</title>
    <link rel="stylesheet" href="Bank_loan.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body>

<?php
session_start();
include '../connection.php';

// Debugging: Check if the session is set
if (!isset($_SESSION['AccountId'])) {
    $_SESSION['errorMsg'] = "Session expired or Account ID is unavailable. Please log in again.";
    header("Location: ../login.html");
    exit();
}

$accountId = $_SESSION['AccountId'];

// Database Connection Check
if (!$conn) {
    die("<p class='error-msg'>Database connection failed: " . htmlspecialchars(mysqli_connect_error()) . "</p>");
}

// Fetch Loan Applications for the User
$loans = [];
if ($accountId) {
    $stmt = $conn->prepare("SELECT LoanType, Amount, InterestRate, Status, StartDate, EndDate FROM loan WHERE a_AccountID = ?");
    if ($stmt) {
        $stmt->bind_param("s", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $loans[] = $row;
            }
        }
        $stmt->close();
    } else {
        echo "<p class='error-msg'>Failed to prepare the statement. Please try again later.</p>";
    }
} else {
    echo "<p class='error-msg'>Account ID is invalid. Please log in again.</p>";
}
?>

<!-- Main container -->
<div id="main">
    <!-- Header Section -->
    <header id="header">
        <div id="logo">
            <img src="../logo.png" width="75px" alt="Bank Logo">
        </div>
        <nav class="nav-links">
            <a href="../Bankhome.php">Accounts</a>
            <a href="Bank_transaction.php">Transaction</a>
            <a href="Bank_pay.php">Pay</a>
            <a href="Bank_loan.php">Loans</a>
            <a href="Bank_stats.php">Statements</a>
            <a href="Bank_support.php">Support</a>
            <a href="Bank_profile.php">Profile</a>
            <a href="../login.html">Logout</a>
        </nav>
        <div class="icon-group">
            <div class="icon"><i class="ri-search-line"></i></div>
            <div class="icon"><i class="ri-notification-3-line"></i></div>
        </div>
    </header>

    <!-- Loan Application Section -->
    <section id="loan-application">
        <!-- Loan Application Form -->
        <div class="form-container">
            <h2>Apply for a Personal Loan</h2>

            <!-- Display Success or Error Message -->
            <?php if (isset($_SESSION['confirmationMsg'])): ?>
                <p class="confirmation-msg"><?= htmlspecialchars($_SESSION['confirmationMsg']) ?></p>
                <?php unset($_SESSION['confirmationMsg']); ?>
            <?php elseif (isset($_SESSION['errorMsg'])): ?>
                <p class="error-msg"><?= htmlspecialchars($_SESSION['errorMsg']) ?></p>
                <?php unset($_SESSION['errorMsg']); ?>
            <?php endif; ?>

            <form action="loan_submit.php" method="POST">
                <label for="loan_type">Loan Type:</label>
                <input type="text" id="loan_type" name="LoanType" required>

                <label for="amount">Loan Amount (PKR):</label>
                <input type="number" id="amount" name="Amount" required>

                <label for="interest_rate">Interest Rate (%):</label>
                <input type="number" step="0.01" id="interest_rate" name="InterestRate" required>

                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="StartDate" required>

                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="EndDate" required>

                <input type="submit" value="Submit Application">
            </form>
        </div>

        <!-- Loan History Table -->
        <div class="loan-history">
            <h3>Your Previous Loans</h3>
            <?php if (!empty($loans)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Loan Type</th>
                            <th>Amount (PKR)</th>
                            <th>Interest Rate (%)</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $loan): ?>
                            <tr>
                                <td><?= htmlspecialchars($loan['LoanType']) ?></td>
                                <td><?= htmlspecialchars($loan['Amount']) ?></td>
                                <td><?= htmlspecialchars($loan['InterestRate']) ?></td>
                                <td><?= htmlspecialchars($loan['Status']) ?></td>
                                <td><?= htmlspecialchars($loan['StartDate']) ?></td>
                                <td><?= htmlspecialchars($loan['EndDate']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No previous loans found.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<!-- Footer Section -->
<footer id="footer">
    <p>© Copyright 2024 Aegis, Inc. All rights reserved.</p>
</footer>

</body>
</html>
