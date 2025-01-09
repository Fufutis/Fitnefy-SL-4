<?php
$role = $_SESSION['role'] ?? 'user'; // Default role is 'user'

// Function to display the User Navbar
function displayUserNavbar()
{
?>
    <nav class="custom-navbar custom-fixed-top custom-dark">
        <div class="custom-container">
            <a class="custom-navbar-brand" href="dashboard.php"><img id="home-logo" src="imgs/house.png" alt="Logo"></a>
            <!-- Hamburger button -->
            <button class="custom-navbar-toggler" type="button">
                <!-- Icon or lines for your hamburger menu -->
                <span class="custom-navbar-toggler-icon"></span>
            </button>
            <div class="custom-navbar-links" id="navbarUser">
                <ul class="custom-nav custom-left">
                    <li class="custom-nav-item"><a class="custom-nav-link" href="seller_store.php">Products</a></li>
                    <li class="custom-nav-item"><a class="custom-nav-link" href="wishlist_view.php">Wishlist</a></li>
                    <li class="custom-nav-item"><a class="custom-nav-link" href="order_history.php">Order History</a></li>
                </ul>
                <ul class="custom-nav custom-right">
                    <li class="custom-nav-item"><a class="custom-btn custom-bg-item" href="cart_view.php">Cart</a></li>

                    <li class="custom-nav-item"><a class="custom-btn custom-danger" href="logout.php"></a></li>
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
    <nav class="custom-navbar custom-fixed-top custom-dark">
        <div class="custom-container">
            <a class="custom-navbar-brand" href="dashboard.php"><img id="home-logo" src="imgs/house.png" alt="Logo"></img></a>
            <button class="custom-navbar-toggler" type="button">
                <span class="custom-navbar-toggler-icon"></span>
            </button>
            <div class="custom-navbar-links" id="navbarSeller">
                <ul class="custom-nav custom-left">
                    <li class="custom-nav-item"><a class="custom-nav-link" href="seller_store.php">My Products</a></li>
                    <li class="custom-nav-item"><a class="custom-nav-link" href="order_history.php">Sold History</a></li>
                </ul>
                <ul class="custom-nav custom-right">
                    <li class="custom-nav-item"><a class="custom-btn custom-bg-item" href="seller.php">Sell</a></li>
                    <li class="custom-nav-item"><a class="custom-btn custom-danger" href="logout.php"></a></li>
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
    <nav class="custom-navbar custom-fixed-top custom-dark">
        <div class="custom-container">
            <a class="custom-navbar-brand" href="dashboard.php"><img id="home-logo" src="imgs/house.png" alt="Logo"></img></a>
            <button class="custom-navbar-toggler" type="button">
                <span class="custom-navbar-toggler-icon"></span>
            </button>
            <div class="custom-navbar-links" id="navbarBoth">
                <ul class="custom-nav custom-left">
                    <li class="custom-nav-item"><a class="custom-nav-link" href="seller_store.php">Products</a></li>
                    <li class="custom-nav-item"><a class="custom-nav-link" href="wishlist_view.php">Wishlist</a></li>
                    <li class="custom-nav-item"><a class="custom-nav-link" href="order_history.php">History</a></li>
                </ul>
                <ul class="custom-nav custom-right">
                    <li class="custom-nav-item"><a class="custom-btn custom-bg-item" href="cart_view.php">Cart</a></li>

                    <li class="custom-nav-item"><a class="custom-btn custom-bg-item" href="seller.php">Sell</a></li>

                    <li class="custom-nav-item"><a class="custom-btn custom-danger" href="logout.php"></a></li>
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
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const toggler = document.querySelector(".custom-navbar-toggler");
        const navbarLinks = document.querySelector(".custom-navbar-links");

        toggler.addEventListener("click", () => {
            navbarLinks.classList.toggle("active");
        });
    });
</script>