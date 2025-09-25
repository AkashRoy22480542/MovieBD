<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_email']) || isset($_COOKIE['user_logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

// Check for error message from controller
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Movie-Box</title>
  <link rel="stylesheet" href="../assets/login.css">
</head>
<body>
   <script src="../assets/login_validation.js"></script>

  <div class="login-container">
    <h2>Login to Movie-Box</h2>
    
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <form action="../controller/loginController.php" method="POST" onsubmit="return validateLogin()">
      <input type="email" id="loginEmail" name="email" placeholder="Email" required>
      <input type="password" id="loginPassword" name="password" placeholder="Password" required>
      
      <label for="rememberMe">
        <input type="checkbox" id="rememberMe" name="rememberMe"> Remember Me
      </label>
      
      <button type="submit">Login</button>
    </form>
    <p class="signup-link">Don't have an account? <a href="signup.php">Sign Up</a></p>

  </div>
</body>
</html>
