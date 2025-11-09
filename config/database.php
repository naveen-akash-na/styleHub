<?php
$host = "localhost";      // or your MySQL server IP
$port = 3306;             // MySQL default port
$user = "root";           // your MySQL username
$password = "Naveen@05";  // your MySQL password
$dbname = "stylehub";     // your MySQL database name

// Create connection using MySQLi (Object-oriented style)
$conn = new mysqli($host, $user, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8mb4");

?>
