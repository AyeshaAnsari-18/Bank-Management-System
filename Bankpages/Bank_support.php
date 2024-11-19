<?php 
session_start();
include '../connection.php';

$message='';

// Prevent access to cached pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if the user is logged in
if (!isset($_SESSION['customerId'])) {
    header("Location: ../login.html?message=login_required");
    exit();
}

// Regenerate session ID to prevent session fixation
session_regenerate_id();

// Get customerID from the session
$customeriD = $_SESSION['customerId'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issueType = mysqli_real_escape_string($conn, $_POST['issueType']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $sql = "INSERT INTO customer_support (customer_customerID, issue_type, description, status, created_at)
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
</head>
<style>
/* General Styling */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Gill Sans', sans-serif;
}

html, body {
    height: 100%;
    width: 100%;
    display: flex;
    flex-direction: column;
}

body {
    overflow-y: scroll;
}

/* Main Content Styling */
#main {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center; /* Ensure everything stays centered */
    background-color: #f7f7f7;
    padding: 3% 0;
    position: relative;
}

/* Header Styling */
#header {
    height: 15%;
    width: 100%;
    position: fixed;
    top: 0;
    display: flex;
    align-items: center;
    background-color: white;
    justify-content: space-around;
    padding: 0 20px;
    border-bottom: 1px solid #ccc;
    z-index: 10;
}

#logo {
    height: 70%;
    display: flex;
    align-items: center;
}

#logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.nav-links a {
    color: #032D60;
    font-weight: 700;
    text-decoration: none;
    margin: 0 10px;
    font-size: 16px;
}

.nav-links a:hover {
    color: rgb(38, 152, 212);
}

.icon-group .icon {
    padding: 10px;
    border-radius: 50%;
    color: #032D60;
    cursor: pointer;
    font-size: 18px;
}

.icon-group .icon:hover {
    background-color: rgb(203, 228, 237);
    color: rgb(28, 120, 211);
}

/* Form Container Styling */
.transfer-form-container {
    width: 90%;
    max-width: 500px; /* Limit maximum width */
    background-color: #ffffff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    text-align: center;
    margin: auto; /* Center horizontally */
    position: relative; /* Default position */
    top: 10%; /* Push down slightly from the top */
    transform: translateY(-10%); /* Center vertically */
    color: #032D60;
}

.transfer-form-container h2 {
    color: #032D60;
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}

.transfer-form-container label {
    display: block;
    color: #032D60;
    font-weight: bold;
    margin: 15px 0 5px;
}

.transfer-form-container input[type="text"],
.transfer-form-container input[type="number"],
.transfer-form-container select,
.transfer-form-container textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    margin-bottom: 15px;
}

.transfer-form-container,
.support-form select,
.support-form textarea {
    width: 100%;
}

.transfer-form-container button {
    width: 100%;
    padding: 12px;
    background-color: #032D60;
    color: white;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 15px;
    transition: background-color 0.3s;
}

.transfer-form-container button:hover {
    background-color: rgb(38, 152, 212);
}

/* Success and Error Messages */
.message {
    font-weight: bold;
    margin: 0 auto;
    text-align: center;
    padding: 10px;
    max-width: 90%;
}

.message p {
    margin: 0;
}

.confirmation-msg {
    color: green;
}

.error-msg {
    color: red;
}

/* Footer Styling */
#footer {
    background-color: #032D60;
    color: white;
    text-align: center;
    padding: 10px 0;
    font-size: 14px;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .transfer-form-container {
        width: 95%;
        padding: 20px;
    }

    .nav-links a {
        font-size: 14px;
        margin: 0 5px;
    }

    #header {
        flex-wrap: wrap;
        justify-content: space-between;
        padding: 10px 15px;
    }

    #logo {
        height: 60%;
    }
}
</style>
<body>

<!-- Main container -->
<div id="main">
    <!-- Header Section -->
    <header id="header">
        <div id="logo">
            <img src="../logo.png" width="75px" alt="Bank Logo">
        </div>
        <nav class="nav-links">
            <a href="../Bankhome.php">Accounts</a>
            <a href="Bank_transaction.php">Transaction</a>
            <a href="Bank_pay.php">Pay</a>
            <a href="Bank_loan.php">Loans</a>
            <a href="Bank_stats.php">Statements</a>
            <a href="Bank_support.php">Support</a>
            <a href="Bank_profile.php">Profile</a>
            <a href="Bank_logout.php">Logout</a>
        </nav>
        <div class="icon-group">
            <div class="icon"><i class="ri-search-line"></i></div>
            <div class="icon"><i class="ri-notification-3-line"></i></div>
        </div>
    </header>

    <!-- Form Section -->
    <div class="transfer-form-container" style="margin-top: 5%;">
        <!-- Display the message -->
        <?php echo $message; ?>
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
