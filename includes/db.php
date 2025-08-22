<?php
// Database connection - simple mysqli connection
$host = 'localhost';
$username = 'induwara';
$password = 'Induwara@2004'; // Change this to your MySQL password
$database = 'event_v2';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");
?>
