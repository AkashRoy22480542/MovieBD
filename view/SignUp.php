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
  <title>Sign Up - Movie-Box</title>
  <link rel="stylesheet" href="../assets/sign_up.css">
</head>
<body> 
     <script src="../assets/login_validation.js"></script>

    <div class="signup-container">
    <h2>Create Your Movie-Box Account</h2>
    
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <form action="../controller/signupController.php" method="POST" onsubmit="return validateSignup()">
      <input type="text" id="fullName" name="full_name" placeholder="Full Name" required>
      <input type="email" id="signupEmail" name="email" placeholder="Email" required>
      <input type="password" id="signupPassword" name="password" placeholder="Password" required>
      <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm Password" required>
      <button type="submit">Sign Up</button>
    </form>
    <p class="login-link">Already have an account? <a href="login.php">Login</a></p>

  </div>
</body>
</html>
