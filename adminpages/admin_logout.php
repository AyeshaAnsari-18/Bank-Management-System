<?php
session_start();

// Debugging: Log session existence
if (isset($_SESSION['admin_id'])) {
    error_log("Logging out admin with ID: " . $_SESSION['admin_id']);
} else {
    error_log("No active session found.");
}

// Completely destroy the session
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session data on the server

// Prevent browser from caching the page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Debugging: Verify session destruction
if (session_status() === PHP_SESSION_NONE) {
    error_log("Session successfully destroyed.");
} else {
    error_log("Session destruction failed.");
}

// Redirect to login page with a success message
header("Location: adminlogin.php?message=Successfully logged out&type=success");
exit();
?>
