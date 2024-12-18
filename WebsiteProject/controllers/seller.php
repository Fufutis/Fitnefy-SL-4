<?php
session_start();
include_once __DIR__ . '/../utility/config.php';

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'seller' && $_SESSION['role'] !== 'both')) {
    $_SESSION['message'] = "Unauthorized access.";
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

// Load the Add Product view
include_once __DIR__ . '/../views/add_product_view.php';
