<?php
$role = $_SESSION['role'] ?? 'user'; // Default role is 'user'

// Include the correct navbar based on the role
if ($role === 'user') {
    include("user_navbar.php");
} elseif ($role === 'seller') {
    include("seller_navbar.php");
} elseif ($role === 'both') {
    include("both_navbar.php");
}
