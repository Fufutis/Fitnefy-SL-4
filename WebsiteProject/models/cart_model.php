<?php
function fetchCartItems($conn, $cart_items)
{
    $cart_details = [];
    $total_price = 0;

    if (!empty($cart_items)) {
        $placeholders = implode(',', array_fill(0, count($cart_items), '?'));
        $stmt = $conn->prepare("SELECT id, name, price, photo_blob, description FROM products WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($cart_items)), ...array_keys($cart_items));
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $product_id = $row['id'];
            $row['quantity'] = $cart_items[$product_id]['quantity'];
            $row['total'] = $row['price'] * $row['quantity'];
            $cart_details[$product_id] = $row;
            $total_price += $row['total'];
        }

        $stmt->close();
    }

    return ['cart_details' => $cart_details, 'total_price' => $total_price];
}
