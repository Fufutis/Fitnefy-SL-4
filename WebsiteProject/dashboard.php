<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['message'] = "You must log in first.";
    header("Location: index.php");
    exit;
}

include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

// Get the role of the logged-in user
$role = $_SESSION['role'] ?? 'user';

// Default filters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'recent'; // Default: Recent
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'desc'; // Default: Descending

// Initialize variables
$sold_items = [];
$products = [];

// Handle seller or both roles
if ($role === 'seller' || $role === 'both') {
    $stmt = $conn->prepare("
        SELECT 
            o.id AS order_id, 
            p.name AS product_name, 
            o.quantity, 
            o.total_price, 
            og.created_at AS order_date 
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN order_groups og ON o.order_group_id = og.id
        WHERE p.seller_id = ?
        ORDER BY og.created_at DESC
    ");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $sold_items[] = $row;
    }

    $stmt->close();
} elseif ($role === 'user') {
    // Fetch all products for users
    $query = "SELECT id, name, description, price, product_type, photo_blob, upload_timestamp FROM products WHERE 1=1";

    // Filter by category
    if (!empty($category)) {
        $query .= " AND product_type = ?";
    }

    // Sorting logic
    if ($sort_by === 'recent') {
        $query .= " ORDER BY upload_timestamp " . ($sort_order === 'asc' ? 'ASC' : 'DESC');
    } elseif ($sort_by === 'price') {
        $query .= " ORDER BY price " . ($sort_order === 'asc' ? 'ASC' : 'DESC');
    }

    // Prepare and execute query
    $stmt = $conn->prepare($query);
    if (!empty($category)) {
        $stmt->bind_param('s', $category);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <!-- Display Different Views Based on Role -->
        <?php if ($role === 'seller' || $role === 'both'): ?>
            <h2 class="mb-4">Sold Items</h2>

            <?php if (empty($sold_items)): ?>
                <div class="alert alert-info">No items have been sold yet.</div>
            <?php else: ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sold_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($item['total_price'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($item['order_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($role === 'user'): ?>
            <!-- For Users (Display Products) -->
            <h2 class="mb-4">All Products</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card h-100">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($product['photo_blob']); ?>"
                                class="card-img-top" alt="Product Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars($product['price']); ?></p>
                                <p class="card-text"><strong>Type:</strong> <?php echo htmlspecialchars($product['product_type']); ?></p>
                                <button class="btn btn-warning mt-2" onclick="addToWishlist(<?php echo $product['id']; ?>)">Add to Wishlist</button>
                                <button class="btn btn-success mt-2" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- JavaScript for Wishlist and Cart -->
    <script>
        function addToCart(productId) {
            $.ajax({
                url: 'cart_action.php',
                type: 'GET',
                data: {
                    action: 'add',
                    product_id: productId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayMessage(response.message, 'success');
                    } else {
                        displayMessage('Error: ' + response.message, 'danger');
                    }
                },
                error: function() {
                    displayMessage('An unexpected error occurred while adding to the cart.', 'danger');
                }
            });
        }

        function addToWishlist(productId) {
            $.ajax({
                url: 'wishlist.php',
                type: 'GET',
                data: {
                    product_id: productId
                },
                dataType: 'json',
                success: function(response) {
                    displayMessage(response.message, 'success');
                },
                error: function() {
                    displayMessage('An error occurred while adding to the wishlist.', 'danger');
                }
            });
        }

        function displayMessage(message, type) {
            const alertBox = `
                <div class="alert alert-${type} fixed-alert" role="alert" style="position: fixed; top: 10px; left: 50%; transform: translateX(-50%); z-index: 1050; width: 90%; max-width: 500px; text-align: center;">
                    ${message}
                </div>`;
            document.body.insertAdjacentHTML('beforeend', alertBox);
            setTimeout(() => {
                const alert = document.querySelector('.fixed-alert');
                if (alert) alert.remove();
            }, 3000);
        }
    </script>
</body>

</html>