<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "pizza_chops_shop";

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
