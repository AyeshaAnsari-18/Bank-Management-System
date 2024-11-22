<?php 
session_start();
include '../../connection.php'; // Ensure connection.php initializes $conn properly

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

if (isset($_GET['employeeID'])) {
    $employeeID = mysqli_real_escape_string($conn, $_GET['employeeID']);

    // Check if the employee is a manager in any department
    $checkManagerQuery = "SELECT departmentID FROM department WHERE managerID = '$employeeID'";
    $managerResult = mysqli_query($conn, $checkManagerQuery);

    if ($managerResult && mysqli_num_rows($managerResult) > 0) {
        // Update the managerID to NULL in the department table
        $updateManagerQuery = "UPDATE department SET managerID = NULL WHERE managerID = '$employeeID'";
        mysqli_query($conn, $updateManagerQuery);
    }

    // Check if the employee is a manager in any branch
    $checkBranchManagerQuery = "SELECT branchID FROM branch WHERE branchManagerID = '$employeeID'";
    $branchManagerResult = mysqli_query($conn, $checkBranchManagerQuery);

    if ($branchManagerResult && mysqli_num_rows($branchManagerResult) > 0) {
        // Update the branchManagerID to NULL in the branch table
        $updateBranchManagerQuery = "UPDATE branch SET branchManagerID = NULL WHERE branchManagerID = '$employeeID'";
        mysqli_query($conn, $updateBranchManagerQuery);
    }

    // Delete the employee
    $sql_employee = "DELETE FROM employee WHERE employeeID = '$employeeID'";
    if (mysqli_query($conn, $sql_employee)) {
        if (mysqli_affected_rows($conn) > 0) {
            header("Location: ../manage_employees.php?message=" . urlencode("Employee deleted successfully."));
            exit();
        } else {
            $message = "No employee found with the given ID.";
        }
    } else {
        $message = "Error deleting employee: " . mysqli_error($conn);
    }
} else {
    $message = "Invalid request. Employee ID is missing.";
}

header("Location: ../manage_employees.php?message=" . urlencode($message));
exit();
?>
