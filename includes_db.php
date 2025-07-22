<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$servername = 'localhost';
$dbname = 'crowdfunding';
$username = 'root';
$password = '';

// Check if connection is already established
if (!isset($conn)) {
    $conn = new mysqli($servername, $username, $password, $dbname, 3308);
    
    // Check connection
    if ($conn->connect_error) {
        die("❌ Database Connection Failed: " . $conn->connect_error);
    }
}
?>
