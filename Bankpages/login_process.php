<?php
session_start();

// Example: Replace with real database validation
$username = $_POST['username'];
$password = $_POST['password'];

if ($username === 'admin' && $password === 'password') { // Example validation
    $_SESSION['user_id'] = 1; // Set user ID in session
    $_SESSION['username'] = $username;

    // Redirect to home page
    header("Location: ../Bankhome.php");
    exit();
} else {
    // Redirect back to login with an error message
    header("Location: ../login.html?message=invalid_credentials");
    exit();
}
?>
