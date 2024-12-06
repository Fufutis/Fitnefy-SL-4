<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signupUsername'], $_POST['signupEmail'], $_POST['signupPassword'], $_POST['signupConfirmPassword'])) {
    $username = $_POST['signupUsername'];
    $email = $_POST['signupEmail'];
    $password = $_POST['signupPassword'];
    $confirmPassword = $_POST['signupConfirmPassword'];

    if ($password !== $confirmPassword) {
        echo "Passwords do not match!";
        exit;
    }

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'my_database');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if username already exists
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username already exists!";
    } else {
        // Insert new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            echo "Account created successfully!";
        } else {
            echo "Error creating account!";
        }
    }

    $stmt->close();
    $conn->close();
}
