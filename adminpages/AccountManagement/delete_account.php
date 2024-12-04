<?php
session_start();
include '../../connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

// Check if account ID is provided
if (isset($_GET['account_id'])) {
    $account_id = intval($_GET['account_id']);
    $message = '';

    // Delete related rows in `transaction` table
    $deleteTransactionSql = "DELETE FROM transaction WHERE account_AccountID = $account_id";
    if (!mysqli_query($conn, $deleteTransactionSql)) {
        $message .= "Error deleting related transactions: " . mysqli_error($conn) . " ";
    }

    // Delete related rows in `loan` table
    $deleteLoanSql = "DELETE FROM loan WHERE a_AccountID = $account_id";
    if (!mysqli_query($conn, $deleteLoanSql)) {
        $message .= "Error deleting related loans: " . mysqli_error($conn) . " ";
    }

    // Delete from `account` table
    $deleteAccountSql = "DELETE FROM account WHERE AccountID = $account_id";
    if (mysqli_query($conn, $deleteAccountSql)) {
        $message .= "Account deleted successfully.";
    } else {
        $message .= "Error deleting account: " . mysqli_error($conn);
    }

    // Redirect back with a message
    header("Location: ../manage_account.php?message=" . urlencode($message));
    exit();
} else {
    header("Location: ../manage_account.php?message=" . urlencode("Invalid account ID."));
    exit();
}
?>
