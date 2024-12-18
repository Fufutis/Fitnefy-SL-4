<div class="col">
    <div class="card h-100">
        <img src="data:image/jpeg;base64,<?php echo base64_encode($item['photo_blob']); ?>" class="card-img-top" alt="Product Image">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
            <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
            <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars($item['price']); ?></p>
            <div class="d-flex justify-content-between">
                <button class="btn btn-success" onclick="addToCart(<?php echo $item['id']; ?>)">Add to Cart</button>
                <button class="btn btn-danger" onclick="removeFromWishlist(<?php echo $item['id']; ?>)">Remove</button>
            </div>
        </div>
    </div>
</div>