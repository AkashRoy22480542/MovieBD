<?php
session_start();

// Get messages from authController redirects
$error_message = isset($_SESSION['signup_error']) ? $_SESSION['signup_error'] : '';
$success_message = isset($_SESSION['signup_success']) ? $_SESSION['signup_success'] : '';

// Clear messages after displaying
if ($error_message) {
    unset($_SESSION['signup_error']);
}
if ($success_message) {
    unset($_SESSION['signup_success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - MovieDB</title>
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
            <a href="login.php">Sign In</a>
        </div>
    </nav>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Join MovieDB</h1>
                <p>Create your account to start exploring</p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="error-message" style="display: block;"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="success-message" style="display: block;"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <form method="POST" action="../Controller/authController.php" onsubmit="return validateSignup()">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="firstName" placeholder="John" required value="<?= isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" placeholder="Doe" required value="<?= isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : '' ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="john@example.com" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                    <div id="password-strength" class="password-strength"></div>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="terms" name="terms" required <?= isset($_POST['terms']) ? 'checked' : '' ?>>
                    <label for="terms">
                        I agree to the <a href="#" target="_blank">Terms of Service</a> and 
                        <a href="#" target="_blank">Privacy Policy</a>
                    </label>
                </div>

                <input type="hidden" name="action" value="signup">
                <button type="submit" class="auth-button">Create Account</button>
            </form>

            <div class="auth-link">
                Already have an account? <a href="login.php">Sign in here</a>
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?>

</body>
</html>
