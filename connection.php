<?php
// Connect to local MySQL database (XAMPP)

$host = "localhost";   // Localhost for XAMPP
$username = "root";    // Default XAMPP username
$password = "";        // Default has no password
$dbname = "edoc";      // Your database name

// Create connection
$database = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($database->connect_error) {
    die("Connection failed: " . $database->connect_error);
}
?>
