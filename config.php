<?php
$dbHost = 'localhost';
$dbUsername = 'root';  // Default MySQL username in XAMPP
$dbPassword = '';      // No password for the root user in XAMPP
$dbName = 'spk_karyawan';

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
