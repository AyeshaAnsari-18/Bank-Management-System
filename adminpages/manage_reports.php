<?php
// Start session and include database connection
session_start();
include '../connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

// Fetch admin information securely
$admin_id = $_SESSION['admin_id'];
$admin_id = mysqli_real_escape_string($conn, $admin_id);
$admin_name_query = $conn->prepare("SELECT admin_name FROM admin WHERE adminID = ?");
$admin_name_query->bind_param("i", $admin_id);
$admin_name_query->execute();
$result = $admin_name_query->get_result();
$admin = $result->fetch_assoc();
if (!$admin) {
    die("Admin not found!");
}

// Fetch statistics dynamically
$total_users_query = "SELECT COUNT(*) AS total_users FROM customer";
$total_transactions_query = "SELECT COUNT(*) AS total_transactions FROM transaction";
$total_loans_query = "SELECT COUNT(*) AS total_loans FROM loan WHERE Status = '1'";
$total_balance_query = "SELECT SUM(balance) AS total_balance FROM account";

$total_users = mysqli_fetch_assoc(mysqli_query($conn, $total_users_query))['total_users'] ?? 0;
$total_transactions = mysqli_fetch_assoc(mysqli_query($conn, $total_transactions_query))['total_transactions'] ?? 0;
$total_loans = mysqli_fetch_assoc(mysqli_query($conn, $total_loans_query))['total_loans'] ?? 0;
$total_balance = mysqli_fetch_assoc(mysqli_query($conn, $total_balance_query))['total_balance'] ?? 0;

// Prepare data for dynamic charts
$chart_data = [
    'transactions' => [],
    'balances' => [],
    'users' => [],
    'loans' => []
];

// Adjust queries based on the actual column names
for ($month = 1; $month <= 12; $month++) {
    // Check if the `last_updated` column exists in the `account` table
    $check_last_updated_query = "SHOW COLUMNS FROM account LIKE 'last_updated'";
    $check_last_updated_result = mysqli_query($conn, $check_last_updated_query);
    $last_updated_column_exists = mysqli_num_rows($check_last_updated_result) > 0;

    // Check if the `registration_date` column exists in the `customer` table
    $check_registration_date_query = "SHOW COLUMNS FROM customer LIKE 'registration_date'";
    $check_registration_date_result = mysqli_query($conn, $check_registration_date_query);
    $registration_date_column_exists = mysqli_num_rows($check_registration_date_result) > 0;

    // Check if the `approval_date` column exists in the `loan` table
    $check_approval_date_query = "SHOW COLUMNS FROM loan LIKE 'approval_date'";
    $check_approval_date_result = mysqli_query($conn, $check_approval_date_query);
    $approval_date_column_exists = mysqli_num_rows($check_approval_date_result) > 0;

    // Query for balances
    if ($last_updated_column_exists) {
        $balances_query = "SELECT SUM(balance) AS sum FROM account WHERE MONTH(last_updated) = $month";
    } else {
        $balances_query = "SELECT SUM(balance) AS sum FROM account";
    }

    // Query for transactions
    $transactions_query = "SELECT COUNT(*) AS count FROM transaction WHERE MONTH(transactionDate) = $month";

    // Query for users
    if ($registration_date_column_exists) {
        $users_query = "SELECT COUNT(*) AS count FROM customer WHERE MONTH(registration_date) = $month";
    } else {
        // If `registration_date` doesn't exist, we can either omit the query or use another approach
        $users_query = "SELECT COUNT(*) AS count FROM customer";
    }

    // Query for loans
    if ($approval_date_column_exists) {
        $loans_query = "SELECT COUNT(*) AS count FROM loan WHERE Status = '1' AND MONTH(approval_date) = $month";
    } else {
        // If `approval_date` doesn't exist, we can either omit the month filter or modify the query
        $loans_query = "SELECT COUNT(*) AS count FROM loan WHERE Status = '1'";
    }

    // Execute the queries and store the results
    $chart_data['transactions'][] = mysqli_fetch_assoc(mysqli_query($conn, $transactions_query))['count'] ?? 0;
    $chart_data['balances'][] = mysqli_fetch_assoc(mysqli_query($conn, $balances_query))['sum'] ?? 0;
    $chart_data['users'][] = mysqli_fetch_assoc(mysqli_query($conn, $users_query))['count'] ?? 0;
    $chart_data['loans'][] = mysqli_fetch_assoc(mysqli_query($conn, $loans_query))['count'] ?? 0;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports and Analytics</title>
    <link rel="stylesheet" href="../css/adminpages.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card {
            border: 5px;
        }
        .user-info .container {
            display: flex;
            margin: 10px;
        }
        
        #exportPDF {
            padding: 5px;
            width: 10%;
            height:8%;
            border-radius: 5px;
            background-color: #032D60;
            color: white;
        }

        #exportPDF:hover {
            background-color: rgb(38, 152, 212);
            border-color: rgb(38, 152, 212);
        }

        /* Account Details Styling */
        .dashboard {
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
            padding: 20px;
            margin-right: 20%;
        }

        .card {
            width: 250px;
            margin: 15px;
            padding: 20px;
            background-color: #f7f7f7;
            border-radius: 10px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h3 {
            color: #032D60;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 20px;
            font-weight: bold;
            color: #181818;
        }
        
        .charts {
            justify-content: center;
            margin-top: 15%;
        }
    </style>
</head>
<body>
    <div id="main">
        <header class="header">
            <div id="logo" style="padding-top: 20px;">
                <img src="../logo.png" alt="Bank Logo">
            </div>
            <nav class="nav-links">
                <a href="adminhome.php">Home</a>
                <a href="manage_users.php">Customer Management</a>
                <a href="manage_employees.php">Employee Management</a>
                <a href="manage_transaction.php">Transaction Management</a>
                <a href="manage_loan.php">Loan Management</a>
                <a href="manage_branch.php">Branch Management</a>
                <a href="manage_support.php">Customer Feedback Management</a>
                <a href="manage_reports.php">Reports and Analytics</a>
                <a href="manage_audit_logs.php">Audit Logs</a>
                <a href="admin_logout.php">Logout</a>
            </nav>
        </header>

        <div class="user-info">
            <button id="exportPDF">Export PDF</button>
            <div class="container">
            <div class="dashboard">
                <div class="card">
                    <h3>Total Users</h3>
                    <p><?php echo $total_users; ?></p>
                </div>
                <div class="card">
                    <h3>Total Transactions</h3>
                    <p><?php echo $total_transactions; ?></p>
                </div>
                <div class="card">
                    <h3>Total Loans</h3>
                    <p><?php echo $total_loans; ?></p>
                </div>
                <div class="card">
                    <h3>Total Balance</h3>
                    <p>$<?php echo number_format($total_balance, 2); ?></p>
                </div>
            </div>

            <div class="charts">
                <div class="chart-container">
                    <h3>Transaction Trends</h3>
                    <canvas id="transactionChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Total Balance Trends</h3>
                    <canvas id="balanceChart"></canvas>
                </div>
            </div>
            </div>
        </div>

        <script>
            const chartData = <?php echo json_encode($chart_data); ?>;

            function createChart(canvasId, label, data, color) {
                new Chart(document.getElementById(canvasId), {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: label,
                            data: data,
                            borderColor: color,
                            backgroundColor: `${color}33`,
                            borderWidth: 2,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            createChart('transactionChart', 'Transactions', chartData.transactions, 'rgba(75, 192, 192, 1)');
            createChart('balanceChart', 'Balances', chartData.balances, 'rgba(153, 102, 255, 1)');

            document.getElementById('exportPDF').addEventListener('click', () => {
                const transactionChart = document.getElementById('transactionChart').toDataURL('image/png');
                const balanceChart = document.getElementById('balanceChart').toDataURL('image/png');

                fetch('ReportsManagement/generate_pdf.php', {
                    method: 'POST',
                    body: JSON.stringify({ transactionChart, balanceChart }),
                    headers: { 'Content-Type': 'application/json' }
                })
                .then(response => response.blob())
                .then(blob => {
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'Bank_Report.pdf';
                    link.click();
                });
            });
        </script>
    </div>
</body>
</html>
