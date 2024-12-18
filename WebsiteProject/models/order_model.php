<?php
function getBoughtHistory($conn, $user_id)
{
    $query = "
        SELECT og.id AS order_id, og.created_at AS order_date, SUM(p.price * o.quantity) AS total_price,
               p.name AS product_name, p.price AS product_price, o.quantity
        FROM order_groups og
        JOIN orders o ON og.id = o.order_group_id
        JOIN products p ON o.product_id = p.id
        WHERE og.user_id = ?
        GROUP BY og.id, og.created_at, p.name, p.price, o.quantity
        ORDER BY og.created_at DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getSoldHistory($conn, $user_id)
{
    $query = "
        SELECT o.order_group_id AS order_id, og.created_at AS order_date, SUM(o.quantity * p.price) AS total_revenue,
               p.name AS product_name, p.price AS product_price, o.quantity
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN order_groups og ON o.order_group_id = og.id
        WHERE p.seller_id = ?
        GROUP BY o.order_group_id, og.created_at, p.name, p.price, o.quantity
        ORDER BY og.created_at DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result();
}
