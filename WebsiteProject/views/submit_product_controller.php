<?php
session_start();
include_once __DIR__ . '/../utility/config.php';

// Ensure the user is logged in
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'seller' && $_SESSION['role'] !== 'both')) {
    $_SESSION['message'] = "Unauthorized access.";
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $price = floatval($_POST['price']);
    $product_type = $_POST['product_type'];
    $seller_id = $_SESSION['user_id'];

    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_blob = file_get_contents($_FILES['photo']['tmp_name']);
    } else {
        $_SESSION['message'] = "Error uploading photo.";
        header("Location: " . BASE_URL . "/controllers/add_product_controller.php");
        exit;
    }

    // Insert product into the database
    $stmt = $conn->prepare("
        INSERT INTO products (name, description, price, product_type, photo_blob, seller_id) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('ssdsbi', $name, $description, $price, $product_type, $photo_blob, $seller_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product added successfully!";
    } else {
        $_SESSION['message'] = "Failed to add product.";
    }

    $stmt->close();
    $conn->close();

    header("Location: " . BASE_URL . "/controllers/add_product_controller.php");
    exit;
}
