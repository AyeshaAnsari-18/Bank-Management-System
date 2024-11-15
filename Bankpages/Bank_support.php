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
</head>
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
            <a href="../login.html">Logout</a>
        </nav>
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
