<?php
function processCheckout($conn, $user_id, $cart_items)
{
    $total_price = 0;

    // Fetch product details
    $placeholders = implode(',', array_fill(0, count($cart_items), '?'));
    $stmt = $conn->prepare("SELECT id, name, price, seller_id FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($cart_items)), ...array_keys($cart_items));
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['id'];
        $row['quantity'] = $cart_items[$product_id]['quantity'];
        $row['total'] = $row['quantity'] * $row['price'];
        $products[] = $row;
    }
    $stmt->close();

    // Begin transaction
    $conn->begin_transaction();
    try {
        // Create order group
        $stmt = $conn->prepare("INSERT INTO order_groups (user_id, created_at) VALUES (?, NOW())");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $order_group_id = $stmt->insert_id;
        $stmt->close();

        // Insert orders
        $stmt = $conn->prepare("INSERT INTO orders (order_group_id, user_id, product_id, quantity, total_price, seller_id) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($products as $product) {
            $stmt->bind_param('iiiiii', $order_group_id, $user_id, $product['id'], $product['quantity'], $product['total'], $product['seller_id']);
            $stmt->execute();
        }
        $stmt->close();

        $conn->commit();
        return $order_group_id;
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}
