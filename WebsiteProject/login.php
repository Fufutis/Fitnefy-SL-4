<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    include("inc/config.php");

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ? OR email = ?');
    $stmt->bind_param('ss', $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $_SESSION['message'] = "Invalid password.";
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['message'] = "User not found.";
        header("Location: index.php");
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit;
}
