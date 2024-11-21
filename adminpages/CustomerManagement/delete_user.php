<?php
session_start();
include '../../connection.php'; // Ensure connection.php initializes $conn properly

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

// Check if customer_id is provided in the query string
if (isset($_GET['customer_id'])) {
    $customerID = mysqli_real_escape_string($conn, $_GET['customer_id']);
    
    // Step 1: Delete related rows in customer_support
    $sql_support = "DELETE FROM customer_support WHERE customer_customerID = '$customerID'";
    if (mysqli_query($conn, $sql_support)) {
        // Step 2: Delete customer from database
        $sql_customer = "DELETE FROM customer WHERE customerId = '$customerID'";
        if (mysqli_query($conn, $sql_customer)) {
            if (mysqli_affected_rows($conn) > 0) {
                header("Location: ../manage_users.php?message=" . urlencode("Customer deleted successfully."));
                exit();
            } else {
                $message = "No customer found with the given ID.";
            }
        } else {
            $message = "Error deleting customer: " . mysqli_error($conn);
        }
    } else {
        $message = "Error deleting related records: " . mysqli_error($conn);
    }
} else {
    $message = "Invalid request. Customer ID is missing.";
}

// Redirect back to manage_users.php with the error message
header("Location: ../manage_users.php?message=" . urlencode($message));
exit();
?>
