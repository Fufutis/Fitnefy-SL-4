<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    include("repeat/config.php"); // Adjust path as needed

    // Trim input to avoid leading/trailing spaces
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ? OR email = ?');
    $stmt->bind_param('ss', $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];

            // Close resources before redirect
            $stmt->close();
            $conn->close();

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $_SESSION['message'] = "Invalid password.";
            
            $stmt->close();
            $conn->close();
            
            header("Location: index.php");
            exit;
        }
    } else {
        $_SESSION['message'] = "User not found.";
        
        $stmt->close();
        $conn->close();
        
        header("Location: index.php");
        exit;
    }
} else {
    // If accessed without POST credentials, redirect to index.
    header("Location: index.php");
    exit;
}
