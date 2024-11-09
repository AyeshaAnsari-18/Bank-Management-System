<?php
include('connection.php');

if (
    isset($_POST['customer_id']) && 
    isset($_POST['name']) && 
    isset($_POST['email']) && 
    isset($_POST['address']) && 
    isset($_POST['dob']) && 
    isset($_POST['phone']) && 
    isset($_POST['signup_password'])
) {
    $customerId = $_POST['customer_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['signup_password'], PASSWORD_DEFAULT); // Encrypt password

    // Check if Customer ID already exists
    $stmt = $conn->prepare("SELECT CustomerId FROM Customer WHERE CustomerId = ?");
    $stmt->bind_param("s", $customerId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Customer ID already exists, prompt user to choose another ID
        echo "<h1>Error: Customer ID already exists. Please choose a different ID.</h1>";
        echo "<a href='login.html'>&larr; Go back to the signup form</a>";
    } else {
        // Insert the new customer into the database
        $stmt = $conn->prepare("INSERT INTO Customer (CustomerId, Name, Email, Address, DateOfBirth, Phone, UserPassword) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $customerId, $name, $email, $address, $dob, $phone, $password);

        if ($stmt->execute()) {
            // Redirect to login page with success message
            header("Location: login.html?success=1");
            exit();
        } else {
            echo "<h1>Error: " . $stmt->error . "</h1>";
        }
    }
} else {
    echo "<h1>Please fill in all required fields for customer registration.</h1>";
    echo "<a href='login.html'>&larr; Go back to the signup form</a>";
}
?>
