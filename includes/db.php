<?php
$host = "localhost";
$user = "root";  // Your MySQL username
$password = "";  // Your MySQL password
$dbname = "swiggy";  // The name of your database

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
