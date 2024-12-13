<?php
session_start();
include("repeat/config.php");
include("repeat/header.php");
include("repeat/navbar.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please log in to view your wishlist.";
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch wishlist items
$stmt = $conn->prepare("SELECT products.id, products.name, products.description, products.price, products.photo_blob 
                        FROM wishlist 
                        JOIN products ON wishlist.product_id = products.id 
                        WHERE wishlist.user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$wishlist_items = [];
while ($row = $result->fetch_assoc()) {
    $wishlist_items[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Your Wishlist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h1>Your Wishlist</h1>
        <?php if (empty($wishlist_items)): ?>
            <div class="alert alert-info">Your wishlist is empty.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($wishlist_items as $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($item['photo_blob']); ?>" class="card-img-top" alt="Product Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars($item['price']); ?></p>
                                <button class="btn btn-success add-to-cart" data-product-id="<?php echo $item['id']; ?>">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- JavaScript for AJAX -->
    <script>
        $(document).ready(function() {
            $('.add-to-cart').click(function() {
                const productId = $(this).data('product-id');
                $.ajax({
                    url: 'cart_action.php',
                    type: 'GET',
                    data: {
                        action: 'add',
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function(response) {
                        alert(response.message); // Show success message
                    },
                    error: function() {
                        alert('An error occurred while adding to the cart.');
                    }
                });
            });
        });
    </script>
</body>

</html>