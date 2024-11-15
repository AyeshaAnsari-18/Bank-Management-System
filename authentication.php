<?php
session_start(); // Start the session
include('connection.php');

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if customer_id and password are set in POST request
if (isset($_POST['customer_id']) && isset($_POST['password'])) {
    // Sanitize input
    $customer_id = sanitize_input($_POST['customer_id']);
    $password = sanitize_input($_POST['password']); // User-provided password (plain text)

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT CustomerId, UserPassword FROM Customer WHERE CustomerId = ?");
    if (!$stmt) {
        // Database error
        header("Location: login.html?error=database_error");
        exit();
    }
    $stmt->bind_param("s", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['UserPassword']; // Plain password from the database
        $customerId = $row['CustomerId'];

        // Compare the passwords directly
        if ($password === $storedPassword) {
            // Set session variables to track the logged-in user
            $_SESSION['customerId'] = $customerId;

            // Redirect to the BankHome page upon successful login
            header("Location: Bankhome.php");
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

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Missing fields, redirect to login with error message
    header("Location: login.html?error=missing_fields");
    exit();
}
?>
