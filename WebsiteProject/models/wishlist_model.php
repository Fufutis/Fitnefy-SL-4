<?php
function getWishlistItems($conn, $user_id)
{
    $stmt = $conn->prepare("
        SELECT p.id, p.name, p.description, p.price, p.photo_blob 
        FROM wishlist w
        JOIN products p ON w.product_id = p.id
        WHERE w.user_id = ?
    ");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $wishlist_items = [];
    while ($row = $result->fetch_assoc()) {
        $wishlist_items[] = $row;
    }
    $stmt->close();

    return $wishlist_items;
}

function removeWishlistItem($conn, $user_id, $product_id)
{
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param('ii', $user_id, $product_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}
