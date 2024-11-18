<?php
// Include the database connection file
session_start();
include '../connection.php';


// Get customerID from the session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs
    $admin_id = mysqli_real_escape_string($conn, $_POST['admin_id']);
    $a_password = mysqli_real_escape_string($conn, $_POST['password']);

    // SQL query to fetch admin details
    $sql = "SELECT adminPassword FROM admin WHERE adminID = '$admin_id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Check if password matches
        if ($a_password === $row['adminPassword']) {
            // Redirect to admin dashboard (or any other page)
            $_SESSION['admin_id'] = $admin_id;
            header('Location: adminhome.php');
            exit();
        } else {
            echo "<script>alert('Invalid password. Please try again.');</script>";
            echo "<script>window.location.href = 'adminlogin.php';</script>";
        }
    } else {
        echo "<script>alert('Admin ID not found.');</script>";
        echo "<script>window.location.href = 'adminlogin.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request method.');</script>";
    echo "<script>window.location.href = 'adminlogin.php';</script>";
}

?>