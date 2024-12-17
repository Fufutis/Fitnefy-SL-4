<?php
$role = $_SESSION['role'] ?? 'user'; // Default role is 'user'

// Function to display the User Navbar
function displayUserNavbar()
{
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">User Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="seller_store.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="wishlist_view.php">Wishlist</a></li>
                    
                    
                    <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="btn btn-light" href="cart_view.php">Cart</a></li>
                    <li class="nav-item"><a class="btn  " ></a></li>
                    <li class="nav-item"><a class="btn btn-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <?php
}

// Function to display the Seller Navbar
function displaySellerNavbar()
{
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Seller Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">

                    <li class="nav-item"><a class="nav-link" href="seller_store.php">My Products</a></li>
                    
                    <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="btn btn-light" href="seller.php">Sell</a></li>
                    <li class="nav-item"><a class="btn  " ></a></li>
                    <li class="nav-item"><a class="btn btn-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <?php
}

// Function to display the Both User and Seller Navbar
function displayBothNavbar()
{
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Both Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="seller_store.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="wishlist_view.php">Wishlist</a></li>

                    <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="btn btn-light" href="cart_view.php">Cart</a></li>
                    <li class="nav-item"><a class="btn  " ></a></li>
                    <li class="nav-item"><a class="btn btn-light" href="seller.php">Sell</a></li>
                    <li class="nav-item"><a class="btn  " ></a></li>

                    <li class="nav-item"><a class="btn btn-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <?php
}

// Display the appropriate navbar based on the user's role
if ($role === 'user') {
    displayUserNavbar();
} elseif ($role === 'seller') {
    displaySellerNavbar();
} elseif ($role === 'both') {
    displayBothNavbar();
}
?>
