<?php
// Check if the session is already started
if (session_status() === PHP_SESSION_NONE) {
    // Set session duration and behavior
    ini_set('session.gc_maxlifetime', 3600); // 1 hour
    ini_set('session.cookie_lifetime', 0);  // Expires when browser closes

    // Start session
    session_start();
}
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'fitnefy';

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
