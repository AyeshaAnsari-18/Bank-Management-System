<?php
session_start();

// Debugging: Check if the session exists
if (isset($_SESSION['user_id'])) {
    error_log("Logging out user with ID: " . $_SESSION['user_id']);
} else {
    error_log("No active session found.");
}

// Destroy the session completely
session_unset();
session_destroy();

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Debugging: Verify session destruction
if (session_status() === PHP_SESSION_NONE) {
    error_log("Session successfully destroyed.");
} else {
    error_log("Session destruction failed.");
}

// Redirect to login with a success message
header("Location: ../login.html?message=logout_success");
exit();
?>
