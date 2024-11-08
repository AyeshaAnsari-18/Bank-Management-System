<?php
include('connection.php');

if (isset($_POST['signup_email']) && isset($_POST['signup_password'])) {
    $email = $_POST['signup_email'];
    $password = $_POST['signup_password'];

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO login (UserEmail, UserPassword) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $password);

    if ($stmt->execute()) {
        echo "Registration successful. You can now log in.";
        header("Location: login.html");  // Redirect to the login page after signup
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "<h1>Please enter all required fields for signup.</h1>";
}
?>
