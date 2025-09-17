<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Create Account</title>
  <script src="../assets/index.js"></script>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="signup_body">
  <div class="signup">
    <p id="error"><?php
        if (isset($_SESSION['signup_error'])) {
            echo $_SESSION['signup_error'];
            unset($_SESSION['signup_error']);
        }
      ?></p>
    <form onsubmit="return validateSignUpForm()" action="../controller/signupCheck.php" method="POST">
      <fieldset>
        <legend>Create Account</legend>
        <table>
          <tr>
            <td><label>Your name</label></td>
            <td><input type="text" id="name" name="name" placeholder="First and last name" /></td>
          </tr>
          <tr>
            <td><label>Email</label></td>
            <td><input type="email" id="email" name="email" placeholder="you@example.com" autocomplete="new-password"/></td>
          </tr>
          <tr>
            <td><label>Password</label></td>
            <td>
              <input type="password" id="password" name="password"  autocomplete="new-password" placeholder="at least 8 characters" />
            </td>
          </tr>
          <tr>
            <td><label>Re-enter password</label></td>
            <td><input type="password" id="repassword" name="repassword"/></td>
          </tr>
        </table>
        <button type="submit">Create your account</button>
      </fieldset>
    </form>
    <div id="navigation_signup">
      Already have an account? <a href="../view/login.php">Sign in</a>
    </div>
  </div>

  <script>
    
  </script>
</body>
</html>
