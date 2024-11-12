<?php
// db_connection.php

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pizza_chops_shop";  // Use your actual database name here

// Create a new connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
