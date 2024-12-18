<?php
session_start();

// Include config file with database connection and BASE_URL definition
if (!defined('BASE_URL')) {
    include_once __DIR__ . "/../utility/config.php"; // Adjust path to utility
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['signupUsername'], $_POST['signupEmail'], $_POST['signupPassword'], $_POST['signupConfirmPassword'], $_POST['role'])
) {
    // Retrieve and sanitize input
    $username = trim($_POST['signupUsername']);
    $email = trim($_POST['signupEmail']);
    $password = $_POST['signupPassword'];
    $confirmPassword = $_POST['signupConfirmPassword'];
    $role = trim($_POST['role']); // 'user', 'seller', or 'both'

    // Validate role input
    $validRoles = ['user', 'seller', 'both'];
    if (!in_array($role, $validRoles)) {
        $_SESSION['message'] = "Invalid role selected!";
        header("Location: " . BASE_URL . "/index.php");
        exit;
    }

    // Validate password match
    if ($password !== $confirmPassword) {
        $_SESSION['message'] = "Passwords do not match!";
        header("Location: " . BASE_URL . "/index.php");
        exit;
    }

    // Check if username or email already exists
    $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $userCheck = $stmt->get_result();

    if ($userCheck->num_rows > 0) {
        $_SESSION['message'] = "Username or Email already exists!";
        $stmt->close();
        header("Location: " . BASE_URL . "/index.php");
        exit;
    }
    $stmt->close();

    // Insert new user into the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $username, $email, $hashedPassword, $role);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Account created successfully! You can now log in.";
        $stmt->close();
        header("Location: " . BASE_URL . "/index.php");
        exit;
    } else {
        // Log error for debugging
        error_log("MySQL Error: " . $stmt->error);
        $_SESSION['message'] = "Error creating account!";
        $stmt->close();
        header("Location: " . BASE_URL . "/index.php");
        exit;
    }
} else {
    // Handle invalid access or missing fields
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
    header("Location: " . BASE_URL . "/index.php");
    exit;
}
