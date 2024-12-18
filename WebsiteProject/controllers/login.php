<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    include("../utility/config.php"); // Database configuration

    // Trim input to avoid leading/trailing spaces
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param('ss', $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Add role to session
            $_SESSION['user_id'] = $user['id'];

            // Close resources before redirect
            $stmt->close();
            $conn->close();

            // Redirect based on role
            $redirectPage = match ($user['role']) {
                'seller' => '../views/seller_dashboard.php',
                'both'   => '../views/both_dashboard.php',
                default  => '../views/user_dashboard.php',
            };

            header("Location: $redirectPage");
            exit;
        } else {
            $_SESSION['message'] = "Invalid password.";
        }
    } else {
        $_SESSION['message'] = "User not found.";
    }

    // Close resources and redirect to login page on failure
    $stmt->close();
    $conn->close();
    header("Location: ../index.php");
    exit;
} else {
    // If accessed without POST credentials, redirect to index.
    header("Location: ../index.php");
    exit;
}
