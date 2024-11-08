<?php
$servername = "sql12.freesqldatabase.com";
$username = "sql12743380";
$password = "23Kb5fmbWA";
$dbname = "sql12743380";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>