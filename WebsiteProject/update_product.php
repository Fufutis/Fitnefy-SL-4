<?php
session_start();
include("repeat/config.php");

// Ensure the user is logged in and is a seller
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['seller', 'both'])) {
    $_SESSION['message'] = "Unauthorized access.";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $price = floatval($_POST['price']);
    $product_type = $_POST['product_type'];
    $seller_id = $_SESSION['user_id'];

    // Handle file upload (if any)
    $photo_blob = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_blob = file_get_contents($_FILES['photo']['tmp_name']);
    }

    // Update query
    $query = "UPDATE products SET name = ?, description = ?, price = ?, product_type = ?";
    $params = [$name, $description, $price, $product_type];

    if ($photo_blob) {
        $query .= ", photo_blob = ?";
        $params[] = $photo_blob;
    }
    $query .= " WHERE id = ? AND seller_id = ?";
    $params[] = $product_id;
    $params[] = $seller_id;

    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product updated successfully.";
        header("Location: seller_store.php");
    } else {
        $_SESSION['message'] = "Failed to update product.";
        header("Location: edit_product.php?id=$product_id");
    }

    $stmt->close();
    $conn->close();
}
