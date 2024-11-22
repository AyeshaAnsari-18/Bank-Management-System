<?php 
session_start();
include '../../connection.php'; // Ensure connection.php initializes $conn properly

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

if (isset($_GET['branchID'])) {
    $branchID = mysqli_real_escape_string($conn, $_GET['branchID']);

    // Update the branchID of all employees working in this branch to NULL
    $updateEmployeeQuery = "UPDATE employee SET branchID = NULL WHERE branchID = '$branchID'";
    mysqli_query($conn, $updateEmployeeQuery);

    // Check if there are any employees left in the branch before deleting
    $checkEmployeesQuery = "SELECT * FROM employee WHERE branchID = '$branchID'";
    $employeeResult = mysqli_query($conn, $checkEmployeesQuery);

    // Proceed to delete branch if no employees are associated with it, else proceed to delete employees' branchID update
    if (mysqli_num_rows($employeeResult) == 0) {
        // Delete the branch from the branch table
        $sql_branch = "DELETE FROM branch WHERE branchID = '$branchID'";
        if (mysqli_query($conn, $sql_branch)) {
            if (mysqli_affected_rows($conn) > 0) {
                header("Location: ../manage_branch.php?message=" . urlencode("Branch deleted successfully."));
                exit();
            } else {
                $message = "No branch found with the given ID.";
            }
        } else {
            $message = "Error deleting branch: " . mysqli_error($conn);
        }
    } else {
        $message = "Employees are still associated with this branch. Their branch assignments have been removed.";
    }
} else {
    $message = "Invalid request. Branch ID is missing.";
}

header("Location: ../manage_branch.php?message=" . urlencode($message));
exit();
?>
