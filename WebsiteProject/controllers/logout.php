<?php
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Include configuration file to access BASE_URL
include_once __DIR__ . '/../utility/config.php';
// Remove the session cookie (if it exists)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Remove all other cookies
foreach ($_COOKIE as $key => $value) {
    setcookie($key, '', time() - 3600, '/'); // Set cookie expiration in the past
}

// Redirect to login page
header("Location: " . BASE_URL . "/index.php");
exit;
