<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['message'] = "You must log in first.";
    header("Location: index.php");
    exit;
}
?>

<?php include("repeat/header.php"); ?>
<?php include("repeat/navbar.php"); ?>
<body>
<div class="container mt-5">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <a href="calc/Calculator.php" class="btn btn-primary">Go to Calculator</a>

    <p>This is a protected page only visible to logged-in users.</p>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</div>
</body>
</html>
