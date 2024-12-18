<?php
// Include configuration for database connection
include_once __DIR__ . '/../utility/config.php';

// Check if a valid product ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = intval($_GET['id']); // Ensure the ID is an integer

    // Prepare the SQL query to fetch the product's image
    $stmt = $conn->prepare("SELECT photo_blob, name FROM products WHERE id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $stmt->store_result(); // Store the result to check for rows
    $stmt->bind_result($photo_blob, $name);

    // If product is found
    if ($stmt->num_rows > 0) {
        $stmt->fetch();

        if ($photo_blob) {
            // Set headers to force file download
            header("Content-Type: image/jpeg"); // MIME type for JPEG images
            header("Content-Disposition: attachment; filename=\"" . htmlspecialchars($name) . ".jpg\"");
            header("Content-Length: " . strlen($photo_blob)); // Set content length

            echo $photo_blob; // Output the binary image data
            exit;
        }
    }

    // If no valid image found
    header("HTTP/1.1 404 Not Found");
    echo "Image not found.";
    $stmt->close();
} else {
    // If no valid ID is provided
    header("HTTP/1.1 400 Bad Request");
    echo "Invalid request.";
}

// Close the database connection
$conn->close();
/*
<a href="<?php echo BASE_URL; ?>/controllers/download_image_controller.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">
    Download Image
</a>    
*/
