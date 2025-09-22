<?php 
require_once('../model/db.php');
require_once('../model/userModel.php');
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add User</title>
  <link rel="stylesheet" href="style.css">
  <script>
    function validateUserForm() {
      let email = document.querySelector("input[name='email']").value;
      let password = document.querySelector("input[name='password']").value;
      if (!email.includes("@") || password.length < 8) {
        alert("Please enter a valid email and password (min 8 characters).");
        return false;
      }
      return true;
    }
  </script>
</head>
<body>
  <h2>Add User</h2>
  <form method="POST" onsubmit="return validateUserForm()">
    <input type="text" name="name" placeholder="User Name" required>
    <input type="email" name="email" placeholder="User Email" required>
    <input type="password" name="password" placeholder="Password (min 6 chars)" required>
    <button type="submit" name="add_user">Add User</button>
  </form>

  <?php
  if (isset($_POST['add_user'])) {
      $user = [[
          'name' => $_POST['name'],
          'email' => $_POST['email'],
          'password' => $_POST['password']
      ]];

      if (getUserByEmail($user[0]['email'])) {
          echo "<p>Email already exists.</p>";
      } else {
          if (addUser($user)) {
              echo "<p>User added successfully.</p>";
          } else {
              echo "<p>Error adding user.</p>";
          }
      }
  }
  ?>
</body>
</html>