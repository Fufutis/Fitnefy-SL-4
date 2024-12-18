<?php
session_start();

// Correctly include config file
if (!defined('BASE_URL')) {
    include_once __DIR__ . '/../utility/config.php'; // Adjust to parent utility folder
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "You must log in first.";
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$role = $_SESSION['role'] ?? 'user'; // Default role is 'user'

// Include common header and navbar
include_once __DIR__ . '/../views/partials/header.php';
include_once __DIR__ . '/../views/partials/navbar.php';

// Dynamically include the correct dashboard
if ($role === 'user') {
    include_once __DIR__ . '/../views/user_dashboard.php';
} elseif ($role === 'seller') {
    include_once __DIR__ . '/../views/seller_dashboard.php';
} elseif ($role === 'both') {
    include_once __DIR__ . '/../views/both_dashboard.php';
} else {
    // Default case if no valid role is found
    echo "<h1>Invalid role. Please contact the administrator.</h1>";
    exit;
}
