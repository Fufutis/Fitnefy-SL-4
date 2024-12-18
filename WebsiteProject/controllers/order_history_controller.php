<?php
session_start();
include_once __DIR__ . '/../utility/config.php';
include_once __DIR__ . '/../models/order_model.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to view your order history.";
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';

// Determine view type for "both" users
$view_type = ($role === 'both')
    ? (isset($_GET['view']) && in_array($_GET['view'], ['bought', 'sold']) ? $_GET['view'] : 'bought')
    : (($role === 'seller') ? 'sold' : 'bought');

// Fetch data based on view type
$order_history = [];
$sold_history = [];

if (($role === 'user' || $role === 'both') && $view_type === 'bought') {
    $order_history = getBoughtHistory($conn, $user_id)->fetch_all(MYSQLI_ASSOC);
}

if (($role === 'seller' || $role === 'both') && $view_type === 'sold') {
    $sold_history = getSoldHistory($conn, $user_id)->fetch_all(MYSQLI_ASSOC);
}

$conn->close();

// Load the appropriate view
include_once __DIR__ . '/../views/order_history_view.php';
