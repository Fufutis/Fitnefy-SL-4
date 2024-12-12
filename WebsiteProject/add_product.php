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

    // Handle file upload and convert to binary data
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_blob = file_get_contents($_FILES['photo']['tmp_name']); // Convert image to binary
    } else {
        $_SESSION['message'] = "Photo upload failed.";
        header("Location: add_product.php");
        exit;
    }

    // Insert product into the database
    $stmt = $conn->prepare("INSERT INTO products (seller_id, name, description, price, photo_blob, product_type) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issdss', $seller_id, $name, $description, $price, $photo_blob, $product_type);

    $stmt->send_long_data(4, $photo_blob); // Send binary data to the 5th parameter
    if ($stmt->execute()) {
        $_SESSION['message'] = "Product added successfully!";
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['message'] = "Failed to add product. SQL Error: " . $stmt->error;
        header("Location: add_product.php");
        exit;
    }

    $stmt->close();
    $conn->close();
}
//Known problem is that when uploaded online no images are allowed to be uploaded for security reasons from infinity free 
//but the code does work on local or generally any other website