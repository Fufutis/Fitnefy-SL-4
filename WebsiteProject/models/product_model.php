<?php
function getSoldItems($conn, $seller_id)
{
    $stmt = $conn->prepare("
        SELECT o.id AS order_id, p.name AS product_name, o.quantity, o.total_price, og.created_at AS order_date 
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN order_groups og ON o.order_group_id = og.id
        WHERE p.seller_id = ?
        ORDER BY og.created_at DESC
    ");
    $stmt->bind_param('i', $seller_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getProductsBySeller($conn, $seller_id)
{
    $stmt = $conn->prepare("SELECT id, name, description, price, product_type, photo_blob FROM products WHERE seller_id = ?");
    $stmt->bind_param('i', $seller_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getAllProducts($conn, $category = '', $sort_by = 'recent', $sort_order = 'desc')
{
    $query = "SELECT id, name, description, price, product_type, photo_blob, upload_timestamp FROM products WHERE 1=1";
    if (!empty($category)) $query .= " AND product_type = ?";
    $query .= " ORDER BY " . ($sort_by === 'price' ? "price" : "upload_timestamp") . " " . ($sort_order === 'asc' ? "ASC" : "DESC");
    $stmt = $conn->prepare($query);
    if (!empty($category)) $stmt->bind_param('s', $category);
    $stmt->execute();
    return $stmt->get_result();
}
