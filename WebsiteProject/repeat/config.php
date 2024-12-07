<?php
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'registry';

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
