<?php
include '../connection.php'; // Database connection
header('Content-Type: application/json');

session_start();

// Parse JSON body
$data = json_decode(file_get_contents('php://input'), true);
$customerId = $data['customer_id'] ?? null;
$password = $data['password'] ?? null;

if (!$customerId || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Customer ID and password are required']);
    exit();
}

// Check database for credentials
$stmt = $conn->prepare("SELECT UserPassword FROM customer WHERE customerId = ?");
$stmt->bind_param("s", $customerId);
$stmt->execute();
$stmt->bind_result($storedPassword);
$stmt->fetch();
$stmt->close();

// Validate credentials (non-hashed passwords)
if (!$storedPassword || $password !== $storedPassword) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid Customer ID or password']);
    exit();
}

// Store customer ID in session
$_SESSION['customerId'] = $customerId;

echo json_encode(['status' => 'success', 'message' => 'Login successful', 'customerId' => $customerId]);
exit();
?>
