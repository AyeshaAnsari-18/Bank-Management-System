<?php
session_start();
include '../../connection.php';
$message = '';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

if (isset($_GET['transactionID'])) {
    $transactionID = mysqli_real_escape_string($conn, $_GET['transactionID']);

    // Fetch transaction details before deleting
    $transactionQuery = "SELECT account_AccountID, transactionType, transactionAmount FROM transaction WHERE transactionID='$transactionID'";
    $transactionResult = mysqli_query($conn, $transactionQuery);

    if ($transactionResult && mysqli_num_rows($transactionResult) > 0) {
        $transaction = mysqli_fetch_assoc($transactionResult);
        $accountID = $transaction['account_AccountID'];
        $transactionType = $transaction['transactionType'];
        $transactionAmount = $transaction['transactionAmount'];

        // Start a transaction
        mysqli_begin_transaction($conn);

        try {
            // Delete the transaction
            $deleteQuery = "DELETE FROM transaction WHERE transactionID='$transactionID'";
            if (!mysqli_query($conn, $deleteQuery)) {
                throw new Exception("Error deleting transaction: " . mysqli_error($conn));
            }

            // Adjust the account balance
            $balanceAdjustment = ($transactionType === 'Credit') ? -$transactionAmount : $transactionAmount;

            $updateBalanceQuery = "UPDATE account SET Balance = Balance + $balanceAdjustment WHERE AccountID='$accountID'";
            if (!mysqli_query($conn, $updateBalanceQuery)) {
                throw new Exception("Error updating account balance: " . mysqli_error($conn));
            }

            // Commit the transaction
            mysqli_commit($conn);
            $message = "Transaction deleted and account balance updated successfully.";
        } catch (Exception $e) {
            // Rollback in case of an error
            mysqli_rollback($conn);
            $message = $e->getMessage();
            error_log($message); // Log the error for debugging
        }
    } else {
        $message = "Transaction not found.";
    }
}

header("Location: ../manage_transaction.php?message=" . urlencode($message));
exit();
?>
