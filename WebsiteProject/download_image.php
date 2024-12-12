<?php
include("repeat/config.php");

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']); // Ensure the ID is an integer

    // Fetch the product's image from the database
    $stmt = $conn->prepare("SELECT photo_blob, name FROM products WHERE id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $stmt->bind_result($photo_blob, $name);
    $stmt->fetch();

    if ($photo_blob) {
        // Set headers to force download
        header("Content-Type: image/jpeg"); // Adjust MIME type as needed
        header("Content-Disposition: attachment; filename=\"" . $name . ".jpg\""); // Customize filename
        echo $photo_blob; // Output the binary data
    } else {
        echo "Image not found.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
