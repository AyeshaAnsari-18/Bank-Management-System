<?php
// Start session and include database connection
session_start();
include '../../connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

// Fetch audit logs
$query = "SELECT a.id, ad.admin_name, a.action, a.details, a.ip_address, a.timestamp 
          FROM audit_logs a 
          JOIN admin ad ON a.admin_id = ad.adminID 
          ORDER BY a.timestamp DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching data: " . mysqli_error($conn));
}

// Set headers for CSV file download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="audit_logs.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write column headers
fputcsv($output, ['ID', 'Admin', 'Action', 'Details', 'IP Address', 'Timestamp']);

// Write rows
while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $row['id'],
        $row['admin_name'],
        $row['action'],
        $row['details'],
        $row['ip_address'],
        $row['timestamp']
    ]);
}

// Close the file
fclose($output);
exit();
