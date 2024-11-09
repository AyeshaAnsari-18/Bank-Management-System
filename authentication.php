<?php
session_start();  // Start the session to store session variables
include('connection.php');

// Check if customer_id and password are set in POST request
if (isset($_POST['customer_id']) && isset($_POST['password'])) {
    $customer_id = $_POST['customer_id'];
    $password = $_POST['password'];  // User-provided password (plain text)

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT CustomerId, UserPassword FROM Customer WHERE CustomerId = ?");
    $stmt->bind_param("s", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['UserPassword'];
        $customerId = $row['CustomerId'];

        // Check if provided password matches stored password
        if ($password === $storedPassword) {
            // Set session variables to track the logged-in user
            $_SESSION['customerId'] = $customerId;

            // Redirect to the BankHome page upon successful login
            header("Location: Bankhome.html");
            exit();
        } else {
            // Incorrect password, redirect to login with error message
            header("Location: login.html?error=incorrect_password");
            exit();
        }
    } else {
        // Invalid Customer ID, redirect to login with error message
        header("Location: login.html?error=invalid_customer_id");
        exit();
    }
} else {
    // Missing fields, redirect to login with error message
    header("Location: login.html?error=missing_fields");
    exit();
}
?>
