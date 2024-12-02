<?php 
session_start();
include '../../connection.php'; // Ensure connection.php initializes $conn properly

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

if (isset($_GET['departmentID'])) {
    $departmentID = mysqli_real_escape_string($conn, $_GET['departmentID']);

    // Update the departmentID of all employees working in this department to NULL
    $updateEmployeeQuery = "UPDATE employee SET departmentID = NULL WHERE departmentID = '$departmentID'";
    mysqli_query($conn, $updateEmployeeQuery);

    // Check if there are any employees left in the branch before deleting
    $checkEmployeesQuery = "SELECT * FROM employee WHERE departmentID = '$departmentID'";
    $employeeResult = mysqli_query($conn, $checkEmployeesQuery);

    
    if (mysqli_num_rows($employeeResult) == 0) {
        $sql_branch = "DELETE FROM department WHERE departmentID = '$departmentID'";
        if (mysqli_query($conn, $sql_branch)) {
            if (mysqli_affected_rows($conn) > 0) {
                header("Location: ../manage_department.php?message=" . urlencode("Department deleted successfully."));
                exit();
            } else {
                $message = "No department found with the given ID.";
            }
        } else {
            $message = "Error deleting department: " . mysqli_error($conn);
        }
    } else {
        $message = "Employees are still associated with this department. Their department assignments have been removed.";
    }
} else {
    $message = "Invalid request. Department ID is missing.";
}

header("Location: ../manage_department.php?message=" . urlencode($message));
exit();
?>
