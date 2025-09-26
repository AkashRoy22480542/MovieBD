<?php
session_start();

// Get messages from authController redirects
$error_message = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
$success_message = isset($_SESSION['login_success']) ? $_SESSION['login_success'] : '';

// Get success message from signup
if (isset($_SESSION['signup_success'])) {
    $success_message = $_SESSION['signup_success'];
    unset($_SESSION['signup_success']);
}

// Clear messages after displaying
if ($error_message) {
    unset($_SESSION['login_error']);
}
if ($success_message) {
    unset($_SESSION['login_success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MovieDB</title>
    <link rel="stylesheet" href="../Assets/style.css">
    <link rel="stylesheet" href="../Assets/navbar.css">
    <script src="../Assets/script.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body class="auth-page">
    <nav class="navbar">
        <div class="logo">
            <a href="../home.php">MovieDB</a>
        </div>
        <div class="menu">
            <a href="../home.php">Home</a>
            <a href="signup.php">Sign Up</a>
        </div>
    </nav>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your MovieDB account</p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="error-message" style="display: block;"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="success-message" style="display: block;"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <form method="POST" action="../Controller/authController.php" onsubmit="return validateLogin()">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <input type="hidden" name="action" value="login">
                <button type="submit" class="auth-button">Sign In</button>
            </form>

            <div class="forgot-password">
                <a href="forgot-password.php">Forgot your password?</a>
            </div>

            <div class="auth-link">
                Don't have an account? <a href="signup.php">Sign up here</a>
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?>

</body>
</html>
