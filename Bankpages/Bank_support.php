<?php
session_start();
include '../connection.php';

$message='';

// Check if the user is logged in
if (!isset($_SESSION['customerId'])) {
    header("Location: ../login.html");
    exit();
}

// Regenerate session ID to prevent session fixation
session_regenerate_id();

// Get customerID from the session
$customeriD = $_SESSION['customerId'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issueType = mysqli_real_escape_string($conn, $_POST['issueType']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $sql = "INSERT INTO customer_support (customerID, issue_type, description, status, created_at)
            VALUES ('$customeriD', '$issueType', '$description', 'Open', NOW())";

    if (mysqli_query($conn, $sql)) {
        $message = "<p style='color: green; text-align: center;'>Your inquiry has been submitted successfully. </p>";
    } else {
        $message= "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support</title>
    <link rel="stylesheet" href="../BankHome.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

body {
    background-color: #f2f2f2;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    align-items: center;
    justify-content: center;
}

.container {
    background-color: white;
    padding: 20px 40px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    text-align: center;
}

h2 {
    font-size: 24px;
    color: #002F6C;
    margin-bottom: 10px;
}

p {
    color: #666;
    margin-bottom: 20px;
}

.support-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

label {
    font-weight: bold;
    color: #333;
    text-align: left;
    font-size: 14px;
}

input, select, textarea {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100%;
}

input:focus, select:focus, textarea:focus {
    border-color: #002F6C;
    outline: none;
}

textarea {
    resize: vertical;
}

button {
    padding: 12px;
    background-color: #002F6C;
    color: white;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #014080;
}
</style>

</head>
<body>

<!-- Main container -->
<div id="main">
    <!-- Header Section -->
    <header id="header">
        <!-- Bank Logo -->
        <div id="logo">
            <img src="../logo.png" width="75px" alt="Bank Logo">
        </div>

        <!-- Navigation Links -->
        <nav class="nav-links">
            <a href="../Bankhome.php">Accounts</a>
            <a href="Bank_transaction.php">Transaction</a>
            <a href="Bank_pay.php">Pay</a>
            <a href="Bank_loan.php">Loans</a>
            <a href="Bank_stats.php">Statements</a>
            <a href="Bank_support.php">Support</a>
            <a href="Bank_profile.php">Profile</a>
            <a href="../login.html">Logout</a>
        </nav>

        <!-- Icons -->
        <div class="icon-group">
            <div class="icon"><i class="ri-search-line"></i></div>
            <div class="icon"><i class="ri-notification-3-line"></i></div>
        </div>
    </header>

</div>

<!-- Display the message here -->
<?php echo $message; ?>

<div class="container">
        <h2>Customer Support</h2>
        <p>Please fill out the form below to submit your inquiry or complaint:</p>
        <form class="support-form" action="#" method="POST">
            <label for="issueType">Issue Type</label>
            <select id="issueType" name="issueType" required>
                <option value="inquiry">Inquiry</option>
                <option value="complaint">Complaint</option>
                <option value="technical">Technical Issue</option>
            </select>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" placeholder="Provide details about your issue..." required></textarea>

            <button type="submit">Submit</button>
        </form>
    </div>



<!-- Footer Section -->
<footer id="footer">
    <div class="footer-content">
        <p>© Copyright 2024 Aegis, Inc. <u>All rights reserved.</u> Various trademarks held by their respective owners.</p>
    </div>
</footer>

</body>
</html>

<?php
$conn->close();
?>
