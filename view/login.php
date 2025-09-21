<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Login Screen</title>
  <script src="../assets/index.js"></script>
  <link rel="stylesheet" href="../assets/style.css">
</head>

<body class="login_body">
  <div class="login">
    <p id="error"><?php
        if (isset($_SESSION['loginError'])) {
            echo $_SESSION['loginError'];
            unset($_SESSION['loginError']);
        }
      ?></p>
    <form onsubmit="return validateSignInForm()" action="../controller/login_check.php" method="post">
      <fieldset>
        <legend>Login</legend>
        <table>
          <tr>
            <td colspan="2"><label>Email</label></td>
          </tr>
          <tr>
             <td ><input type="email" id="email" name="email" placeholder="you@example.com"/></td>
          </tr>
          <tr>
            <td colspan="2"><label>Password</label></td>
          </tr>
          <tr>
                <td colspan="2">
                        <input type="password" id="password" name="password" placeholder="at least 8 characters" />
                </td>
          </tr>
        </table>
        <button type="submit">Login</button>
      </fieldset>
    </form>
    <div id="navigation_login">
        New to IMDb? <a href="../view/SignUp.php">SignUp</a> <br>
        Forget Password? <a href="../view/forget_password.html">Reset</a>
    </div>
  </div>
</body>
</html>
