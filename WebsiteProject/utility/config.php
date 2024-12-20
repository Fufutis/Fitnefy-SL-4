<?php

// Define the Base URL of the project
define('BASE_URL', 'http://localhost/final-project/WebsiteProject');

// Start the session if it is not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 3600); // 1 hour session duration
    ini_set('session.cookie_lifetime', 0);   // Session expires when browser closes
    ini_set('session.cookie_secure', 1);    // Ensure cookies are sent over HTTPS
    ini_set('session.cookie_httponly', 1);  // Prevent JavaScript access to cookies
    session_start();
}

// Database connection details
$host = 'sql7.freemysqlhosting.net';
$db_user = 'sql7751970';
$db_pass = '6RDwtFyGXs';
$db_name = 'sql7751970';

// Create the database connection
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Handle connection errors
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set the charset for the database connection
$conn->set_charset('utf8mb4');
