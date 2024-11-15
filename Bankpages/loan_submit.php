<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include '../connection.php';

// Initialize session
session_start();

// Retrieve Account ID from session
$accountId = $_SESSION['AccountId'] ?? null;  // Ensure that the key 'AccountId' matches the key used in the login flow

// Check if AccountId is set in the session
if (!$accountId) {
    $_SESSION['errorMsg'] = "Account ID is not available. Please log in again.";
    header("Location: login.php");  // Redirect to login page if AccountId is not available
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form inputs
    $loanType = $_POST['LoanType'] ?? '';
    $amount = $_POST['Amount'] ?? 0;
    $interestRate = $_POST['InterestRate'] ?? 0;
    $startDate = $_POST['StartDate'] ?? '';
    $endDate = $_POST['EndDate'] ?? '';

    // Validate inputs
    if (empty($loanType) || empty($amount) || empty($interestRate) || empty($startDate) || empty($endDate)) {
        $_SESSION['errorMsg'] = "All fields are required.";
        header("Location: Bank_loan.php");
        exit();
    }

    // Prepare and execute the SQL query to insert the loan application
    $stmt = $conn->prepare("INSERT INTO loan (a_AccountID, LoanType, Amount, InterestRate, Status, StartDate, EndDate) VALUES (?, ?, ?, ?, 1, ?, ?)");
    $stmt->bind_param("isddss", $accountId, $loanType, $amount, $interestRate, $startDate, $endDate);

    // Execute the query and check if successful
    if ($stmt->execute()) {
        $_SESSION['confirmationMsg'] = "Loan application submitted successfully.";
    } else {
        $_SESSION['errorMsg'] = "Error submitting loan application: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    // Redirect back to the loan application page to avoid form resubmission
    header("Location: Bank_loan.php");
    exit();
} else {
    // Redirect to Bank_loan.php if accessed without a POST request
    $_SESSION['errorMsg'] = "Invalid request.";
    header("Location: Bank_loan.php");
    exit();
}
?>
