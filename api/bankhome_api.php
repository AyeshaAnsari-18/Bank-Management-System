<?php
header("Content-Type: application/json"); // Response type is JSON
session_start();
include '../connection.php';

// Check if the user is logged in
if (!isset($_SESSION['customerId'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

// Regenerate session ID to prevent session fixation
session_regenerate_id();

// Get customerID from the session
$customerID = $_SESSION['customerId'];

// Fetch user information (Name and AccountID associated with the customer)
$sql_user = "SELECT Name, account_accountID FROM customer WHERE customerId = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $customerID);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$stmt_user->close();

// Check if user details were retrieved
if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'User not found.']);
    exit();
}

// Get the AccountID from the Customer record
$accountID = $user['account_accountID'];

// ** Store AccountID in the session **
$_SESSION['AccountId'] = $accountID;

// Fetch account information using AccountID
$sql_account = "SELECT AccountType, Balance, AccountID FROM account WHERE AccountID = ?";
$stmt_account = $conn->prepare($sql_account);
$stmt_account->bind_param("s", $accountID);
$stmt_account->execute();
$result_account = $stmt_account->get_result();
$account = $result_account->fetch_assoc();
$stmt_account->close();

// Check if account details were retrieved
if (!$account) {
    echo json_encode(['status' => 'error', 'message' => 'Account information not found.']);
    exit();
}

// Prepare the API response
$response = [
    'status' => 'success',
    'user' => [
        'name' => $user['Name'],
        'accountId' => $accountID
    ],
    'account' => [
        'accountType' => $account['AccountType'],
        'balance' => $account['Balance'],
        'accountIdLast4' => substr($account['AccountID'], -4) // Mask the account ID
    ],
    'lastLogin' => date('Y-m-d H:i:s') // Add current server time as last login
];

// Close the database connection
$conn->close();

// Return the response as JSON
echo json_encode($response);
?>
