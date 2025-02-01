<?php
session_start();

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['signupUsername'], $_POST['signupEmail'], $_POST['signupPassword'], $_POST['signupConfirmPassword'])
) {

    include("repeat/config.php"); // Contains DB connection info and connects to DB

    $username = trim($_POST['signupUsername']);
    $email = trim($_POST['signupEmail']);
    $password = $_POST['signupPassword'];
    $confirmPassword = $_POST['signupConfirmPassword'];

    // Validate password match
    if ($password !== $confirmPassword) {
        $_SESSION['message'] = "Passwords do not match!";
        $conn->close();
        header("Location: index.php");
        exit;
    }

    // Check username/email availability
    $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $userCheck = $stmt->get_result();

    if ($userCheck->num_rows > 0) {
        $_SESSION['message'] = "Username or Email already exists!";
        $stmt->close();
        $conn->close();
        header("Location: index.php");
        exit;
    }
    $stmt->close();

    // Insert new user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Account created successfully! You can now log in.";
        $stmt->close();
        $conn->close();
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['message'] = "Error creating account!";
        $stmt->close();
        $conn->close();
        header("Location: index.php");
        exit;
    }
} else {
    // If accessed without POST or missing fields
    // Even though we might not have a $stmt, we definitely have $conn from config.php
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
    header("Location: index.php");
    exit;
}
