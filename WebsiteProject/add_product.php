<?php
session_start();

// Ensure the user is logged in and is a seller
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'seller') {
    $_SESSION['message'] = "You must be a seller to add products.";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include("repeat/config.php");

    // Retrieve form inputs
    $seller_id = $_SESSION['user_id'];
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $price = floatval($_POST['price']);
    $product_type = $_POST['product_type'];

    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "uploads/";
        $photo_name = basename($_FILES['photo']['name']);
        $target_path = $upload_dir . uniqid() . "_" . $photo_name;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
            $photo = $target_path;
        } else {
            $_SESSION['message'] = "Failed to upload photo.";
            header("Location: add_product.php");
            exit;
        }
    } else {
        $_SESSION['message'] = "Photo upload failed. Please try again.";
        header("Location: add_product.php");
        exit;
    }

    // Insert product into the database
    $stmt = $conn->prepare("INSERT INTO products (seller_id, name, description, price, photo, product_type) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issdss', $seller_id, $name, $description, $price, $photo, $product_type);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product added successfully!";
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['message'] = "Failed to add product. Please try again.";
        header("Location: add_product.php");
        exit;
    }

    // Close resources
    $stmt->close();
    $conn->close();
}
?>
