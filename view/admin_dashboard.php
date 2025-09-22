<?php
session_start();
require_once("../model/db.php");

// Example: You may want to check if user is admin here
// if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
//     header('Location: login.php');
//     exit;
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/admin_dashboard.css" />
</head>
<body>
  <div class="dashboard-container">
    <h1>Admin Dashboard</h1>

    <div class="button-grid">
      <button onclick="location.href='add_user.php'">Add User</button>
      <button onclick="location.href='delete_user.php'">Delete User</button>
      <button onclick="location.href='addmovie.php'">Add Movie</button>
      <button onclick="location.href='deletemovie.php'">Delete Movie</button>
      <button onclick="location.href='add_tvshow.php'">Add TV Show</button>
      <button onclick="location.href='delete_tvshow.php'">Delete TV Show</button>
      <button onclick="location.href='../controller/logout.php'">logout</button>
    </div>
  </div>

  <script src="../assets/admin_dashboard.js"></script>
</body>
</html>
