<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signupUsername'], $_POST['signupEmail'], $_POST['signupPassword'], $_POST['signupConfirmPassword'])) {
    include("inc/config.php");

    $username = $_POST['signupUsername'];
    $email = $_POST['signupEmail'];
    $password = $_POST['signupPassword'];
    $confirmPassword = $_POST['signupConfirmPassword'];

    if ($password !== $confirmPassword) {
        $_SESSION['message'] = "Passwords do not match!";
        header("Location: index.php");
        exit;
    }

    // Check if username already exists
    $stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $userCheck = $stmt->get_result();

    if ($userCheck->num_rows > 0) {
        $_SESSION['message'] = "Username already exists!";
        header("Location: index.php");
        exit;
    }
    $stmt->close();

    // Insert new user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Account created successfully! You can now login.";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['message'] = "Error creating account!";
        header("Location: index.php");
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit;
}
