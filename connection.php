<?php      
    $host = "localhost";
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

      
    $conn = mysqli_connect($host, $user, $password, $db_name);  
    if(mysqli_connect_errno()) {  
        die("Failed to connect with MySQL: ". mysqli_connect_error());  
    }  
?>  