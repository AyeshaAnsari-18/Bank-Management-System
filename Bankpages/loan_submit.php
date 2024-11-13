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
$accountId = $_SESSION['AccountId'] ?? null;  // Assuming AccountId is stored in the session after login

// Check if AccountId is set in the session
if (!$accountId) {
    $_SESSION['errorMsg'] = "Account ID is not available. Please log in again.";
    header("Location: Bank_loan.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form inputs
    $loanType = $_POST['LoanType'];
    $amount = $_POST['Amount'];
    $interestRate = $_POST['InterestRate'];
    $startDate = $_POST['StartDate'];
    $endDate = $_POST['EndDate'];
    
    // Prepare and execute the SQL query to insert the loan application
    $stmt = $conn->prepare("INSERT INTO Loan (AccountId, LoanType, Amount, InterestRate, Status, StartDate, EndDate) VALUES (?, ?, ?, ?, 'Pending', ?, ?)");
    $stmt->bind_param("ssddss", $accountId, $loanType, $amount, $interestRate, $startDate, $endDate);

    // Execute the query and check if successful
    if ($stmt->execute()) {
        $_SESSION['confirmationMsg'] = "Loan application submitted successfully.";
    } else {
        $_SESSION['errorMsg'] = "Error submitting loan application: " . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
} else {
    $_SESSION['errorMsg'] = "Invalid request method.";
}

// Redirect back to the loan application page
header("Location: Bank_loan.php");
exit();
?>
