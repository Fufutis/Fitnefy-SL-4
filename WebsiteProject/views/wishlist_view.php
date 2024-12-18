// Add to Wishlist
function addToWishlist(productId) {
$.ajax({
url: '<?php echo BASE_URL; ?>/controllers/wishlist_controller.php',
type: 'POST',
data: {
action: 'add',
product_id: productId
},
dataType: 'json',
success: function(response) {
alert(response.message);
},
error: function() {
alert('An error occurred while adding to the wishlist.');
}
});
}

// Remove from Wishlist
function removeFromWishlist(productId) {
$.ajax({
url: '<?php echo BASE_URL; ?>/controllers/wishlist_controller.php',
type: 'POST',
data: {
action: 'remove',
product_id: productId
},
dataType: 'json',
success: function(response) {
alert(response.message);
location.reload(); // Refresh the page to reflect changes
},
error: function() {
alert('An error occurred while removing from the wishlist.');
}
});
}