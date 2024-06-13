<?php

$sname = "localhost"; // Server name or IP address
$uname = "root"; // MySQL username
$password = ""; // MySQL password
$db_name = "ipt101"; // Database name

// Create connection to the MySQL server
$conn = new mysqli($sname, $uname, $password, $db_name);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Terminate script and display error message if connection failed
}

// SQL to create the user table
$sql = "CREATE TABLE IF NOT EXISTS user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(45) NOT NULL,
    password VARCHAR(45) NOT NULL,
    first_name VARCHAR(45) NOT NULL,
    middle_name VARCHAR(45),
    last_name VARCHAR(45) NOT NULL,
    email VARCHAR(45) UNIQUE NOT NULL,
    status VARCHAR(45),
    active VARCHAR(45)
)";

// Execute the SQL query to create the table
if ($conn->query($sql) === false) {
    echo "Error creating table: " . $conn->error;
}

?>
