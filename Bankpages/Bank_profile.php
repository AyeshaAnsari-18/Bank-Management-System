<?php
// Start session and include database connection
session_start();
include('../connection.php');

// Fetch customerId and accountId from the session
$customerId = $_SESSION['customerId'];
$accountId = $_SESSION['AccountId'];

if (!$customerId || !$accountId) {
    echo "User session expired. Please log in again.";
    exit;
}

// Initialize variables
$message = "";
$showUpdateForm = isset($_GET['update']) && $_GET['update'] == 1;

// Fetch user details
$sql = "SELECT * FROM customer WHERE customerId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission for updating user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $updateQuery = "UPDATE customer SET 
                    Email = ?, 
                    Address = ?, 
                    DateOfBirth = ?, 
                    Phone = ?, 
                    UserPassword = ? 
                    WHERE customerId = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssssi", $email, $address, $dob, $phone, $password, $customerId);

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
        $showUpdateForm = false;

        // Refresh user data
        $stmt->close();
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $message = "Error updating profile: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../css/BankHome.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            font-family: 'Gill Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .profile-container {
            margin: auto auto;
            max-width: 500px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #032d60;
            margin-bottom: 20px;
        }
        .user-details p {
            margin: 8px 0;
            font-size: 16px;
            color: #032d60;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #032d60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color:#007bff;
        }
        form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            width: 100%;
            margin-top: 15px;
        }
    </style>
</head>
<body>
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
        <!-- Icons -->
        <div class="icon-group">
            <div class="icon"><i class="ri-search-line"></i></div>
            <div class="icon"><i class="ri-notification-3-line"></i></div>
        </div>
    </header>

    <!-- Profile Section -->
    <div class="profile-container">
        <h2>Welcome, <?php echo htmlspecialchars($user['Name']); ?></h2>

        <?php if (!empty($message)): ?>
            <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if (!$showUpdateForm): ?>
            <!-- Display User Info -->
            <div class="user-details">
                <p><strong>Customer ID:</strong> <?php echo htmlspecialchars($customerId); ?></p>
                <p><strong>Account ID:</strong> <?php echo htmlspecialchars($accountId); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($user['Address']); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['DateOfBirth']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['Phone']); ?></p>
            </div>

            <!-- Update Info Button -->
            <button onclick="window.location.href='Bank_profile.php?update=1'">Update Info</button>
        <?php else: ?>
            <!-- Update Form -->
            <form action="Bank_profile.php" method="POST">
                <p><strong>Customer ID:</strong> <?php echo htmlspecialchars($customerId); ?></p>
                <p><strong>Account ID:</strong> <?php echo htmlspecialchars($accountId); ?></p>
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
                <input type="text" name="address" placeholder="Address" value="<?php echo htmlspecialchars($user['Address']); ?>" required>
                <input type="date" name="dob" placeholder="Date of Birth" value="<?php echo htmlspecialchars($user['DateOfBirth']); ?>" required>
                <input type="text" name="phone" placeholder="Phone" value="<?php echo htmlspecialchars($user['Phone']); ?>" required>
                <input type="password" name="password" placeholder="Password" value="<?php echo htmlspecialchars($user['UserPassword']); ?>" required>
                <button type="submit">Save Changes</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<footer id="footer">
    <div class="footer-content">
        <p>© Copyright 2024 Aegis, Inc. <u>All rights reserved.</u> Various trademarks held by their respective owners.</p>
    </div>
</footer>
</body>
</html>