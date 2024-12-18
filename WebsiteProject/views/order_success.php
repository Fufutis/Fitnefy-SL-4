<?php
session_start();
$order_group_id = $_SESSION['order_group_id'] ?? null;
if (!$order_group_id) {
    header("Location: cart_view.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-success">Order Placed Successfully!</h1>
        <p>Your order ID: <strong><?php echo htmlspecialchars($order_group_id); ?></strong></p>
        <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
    </div>
</body>

</html>