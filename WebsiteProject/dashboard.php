<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['message'] = "You must log in first.";
    header("Location: index.php");
    exit;
}
?>

<?php include("inc/header.php"); ?>

<body>
<div class="container mt-5">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>This is a protected page only visible to logged-in users.</p>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</div>
</body>
</html>
