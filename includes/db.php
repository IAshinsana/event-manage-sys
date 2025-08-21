<?php
// Database connection - simple mysqli connection
$host = 'localhost';
$username = 'u492478120_event_v2';
$password = ']Cr:=xG0'; // Change this to your MySQL password
$database = 'u492478120_event_v2';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");
?>
