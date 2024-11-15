<?php
session_start();
include '../connection.php';
$message = '';
// Check if the user is logged in
if (!isset($_SESSION['customerId'])) {
    header("Location: ../login.html");
    exit();
}

// Regenerate session ID to prevent session fixation
session_regenerate_id();

// Get customerID from the session
$customerID = $_SESSION['customerId'];
$sql_account = "SELECT AccountID, Balance FROM account WHERE customer_customerID = ?";
$stmt_account = $conn->prepare($sql_account);
$stmt_account->bind_param("i", $customerID);
$stmt_account->execute();
$result_account = $stmt_account->get_result();
$account = $result_account->fetch_assoc();

$sender_account_id = $account['AccountID'];
$sender_balance = $account['Balance'];

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Retrieve and validate transfer details from POST
    $sender_accountinput = (int) $_POST['sender_account'];

    if($sender_accountinput == $sender_account_id){
        $receiver_account = $_POST['receiver_account'];
        $amount = (double)$_POST['amount'];
    
        // Validate form data
        if ($amount <= 0) {
            $message = "<p style='color: red; text-align: center;'>Please enter a valid amount.</p>";
            exit();
        }
    
        // Check if the sender has enough balance
        if ($sender_balance < $amount) {
            $message = "<p style='color: red; text-align: center;'>Insufficient funds in the sender's account.</p>";
            exit();
        }
    
        // Start transaction
        $conn->begin_transaction();
    
        try {
            // Fetch receiver's account details
            $sql_receiver = "SELECT AccountID,Balance FROM account WHERE AccountID = ?";
            $stmt_receiver = $conn->prepare($sql_receiver);
            $stmt_receiver->bind_param("i", $receiver_account);
            $stmt_receiver->execute();
            $result_receiver = $stmt_receiver->get_result();
            $receiver = $result_receiver->fetch_assoc();
    
            if (!$receiver) {
                $message = "<p style='color: red; text-align: center;'>Receiver account not found.</p>";
                throw new Exception("Receiver account not found.");
            }
    
            $receiver_account_id = $receiver['AccountID'];
            
    
            // Deduct amount from sender's account
            $sql_deduct = "UPDATE account SET Balance = Balance - ? WHERE AccountID = ?";
            $stmt_deduct = $conn->prepare($sql_deduct);
            $stmt_deduct->bind_param("di", $amount, $sender_account_id);
            $stmt_deduct->execute();
    
            // Add amount to receiver's account
            $sql_add = "UPDATE account SET Balance = Balance + ? WHERE AccountID = ?";
            $stmt_add = $conn->prepare($sql_add);
            $stmt_add->bind_param("di", $amount, $receiver_account_id);
            $stmt_add->execute();
    
            // Record the transaction for the sender (debit)
            $sql_transaction_sender = "INSERT INTO transaction (transactionAmount, transactiondate, transactiontype, account_AccountID) VALUES (?, NOW(), 'debit', ?)";
            $stmt_transaction_sender = $conn->prepare($sql_transaction_sender);
            $stmt_transaction_sender->bind_param("di", $amount, $sender_account_id);
            $stmt_transaction_sender->execute();
    
            // Record the transaction for the receiver (credit)
            $sql_transaction_receiver = "INSERT INTO transaction (transactionAmount, transactiondate, transactiontype, account_AccountID) VALUES (?, NOW(), 'credit', ?)";
            $stmt_transaction_receiver = $conn->prepare($sql_transaction_receiver);
            $stmt_transaction_receiver->bind_param("di", $amount, $receiver_account_id);
            $stmt_transaction_receiver->execute();
    
            // Commit the transaction
            $conn->commit();
            $message = "<p style='color: green; text-align: center;'>Funds transferred successfully!</p>";
            
        } 
        catch (Exception $e) {
            // Rollback transaction if any error occurs
            $conn->rollback();
            $message= "<p style='color: red; text-align: center;'>Error: " . $e->getMessage() . "</p>";
        }
    }
    else{
        $message = "<p style='color: red; text-align: center;'>Wrong sender account id.</p>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay</title>
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
            justify-content: space-between;
        }
        
        .transfer-form-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            padding: 20px;
            margin: 20px auto;
        }

        .transfer-form-container h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-size: 22px;
        }

        .transfer-form-container label {
            font-size: 14px;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }

        .transfer-form-container input[type="text"],
        .transfer-form-container input[type="number"],
        .transfer-form-container textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .transfer-form-container textarea {
            resize: vertical;
            min-height: 80px;
        }

        .transfer-form-container button {
            background-color: #002855;
            color: white;
            font-size: 16px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .transfer-form-container button:hover {
            background-color: #001a3b;
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

    <div class="transfer-form-container">
        <h2>Funds Transfer</h2>
        <form action="#" method="POST">
            <label for="sender-account">Sender's Account Number</label>
            <input type="text" id="sender-account" name="sender_account" required>

            <label for="receiver-account">Receiver's Account Number</label>
            <input type="text" id="receiver-account" name="receiver_account" required>

            <label for="amount">Amount</label>
            <input type="number" id="amount" name="amount" required min="1" step="0.01">

            <button type="submit">Transfer Funds</button>
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
?>