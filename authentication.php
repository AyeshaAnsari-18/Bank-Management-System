<?php
session_start();  // Start the session to store session variables
include('connection.php');

// Check if email and password are set in POST request
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];  // User-provided password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);  // Hash the password

    // Store $hashedPassword in the database
    $sql = "INSERT INTO login (UserEmail, UserPassword) VALUES ('$email', '$hashedPassword')";
    mysqli_query($conn, $sql);


    // Prevent SQL Injection by using prepared statements
    $stmt = $conn->prepare("SELECT userID, UserPassword FROM login WHERE UserEmail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['UserPassword'];
        $userID = $row['userID'];

        // Verify the password (assuming the password is hashed in the database)
        if (password_verify($password, $storedPassword)) {
            // Set session variables to track the logged-in user
            $_SESSION['userID'] = $userID;
            $_SESSION['email'] = $email;

            // Redirect to the BankHome page
            header("Location: BankHome.php");
            exit();
        } else {
            echo "<h1>Login failed. Invalid password.</h1>";
        }
    } else {
        echo "<h1>Login failed. Invalid email.</h1>";
    }
} else {
    echo "<h1>Please enter both email and password.</h1>";
}
?>
