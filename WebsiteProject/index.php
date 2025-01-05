<?php include("repeat/header.php"); ?>

<body>
    <?php
    session_start();
    // Display any session-based messages (e.g., errors, success messages)
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-info" role="alert">' . htmlspecialchars($_SESSION['message']) . '</div>';
        unset($_SESSION['message']);
    }
    ?>

    <!-- Tiled Background -->
    <div class="bg-container">
        <?php
        // Generate 600 tiles dynamically
        for ($i = 0; $i < 3000; $i++) {
            echo '<div class="tile"></div>';
        }
        ?>
    </div>

    <!-- Main Content -->
    <div class="container mt-5 main-content login">
        <div class="row">
            <?php
            // Determine which form to show: login or register
            $action = isset($_GET['action']) ? $_GET['action'] : 'login';
            ?>

            <?php if ($action === 'login'): ?>
                <!-- Login Form -->
                <div class="col-md-6">
                    <h2>Login</h2>
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label>Username or Email</label>
                            <input type="text" name="username" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required />
                        </div>
                        <button class="btn btn-primary" type="submit">Login</button>
                    </form>
                    <p class="mt-3">Don't have an account? <a href="?action=register">Create Account</a></p>
                </div>
            <?php elseif ($action === 'register'): ?>
                <!-- Registration Form -->
                <div class="col-md-6">
                    <h2>Create Account</h2>
                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="signupUsername" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="signupEmail" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="signupPassword" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="signupConfirmPassword" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label>Role</label>
                            <select name="role" class="form-select" required>
                                <option value="user">User</option>
                                <option value="seller">Seller</option>
                                <option value="both">Both</option>
                            </select>
                        </div>
                        <button class="btn btn-success" type="submit">Sign Up</button>
                    </form>
                    <p class="mt-3">Already have an account? <a href="?action=login">Login</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>