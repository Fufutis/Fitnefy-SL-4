<?php include("inc/header.php"); ?>

<body>
<div class="container mt-5">
    <?php
    session_start();
    // Display any messages (e.g., errors or success messages) passed via session
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-info">' . htmlspecialchars($_SESSION['message']) . '</div>';
        unset($_SESSION['message']);
    }
    ?>
    
    <div class="row">
        <div class="col-md-6">
            <h2>Login</h2>
            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label>Username or Email</label>
                    <input type="text" name="username" class="form-control" required/>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required/>
                </div>
                <button class="btn btn-primary" type="submit">Login</button>
            </form>
        </div>

        <div class="col-md-6">
            <h2>Create Account</h2>
            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="signupUsername" class="form-control" required/>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="signupEmail" class="form-control" required/>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="signupPassword" class="form-control" required/>
                </div>
                <div class="mb-3">
                    <label>Confirm Password</label>
                    <input type="password" name="signupConfirmPassword" class="form-control" required/>
                </div>
                <button class="btn btn-success" type="submit">Sign Up</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
