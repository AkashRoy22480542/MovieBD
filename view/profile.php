<?php
session_start();
require_once('../model/userModel.php');

if (!isset($_SESSION['status']) || $_SESSION['status'] !== true) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

$user = getUserByEmail($username);

$name = $user['name'];
$image_path = $user['image_path'];
$phone = $user['phone'];
$status = $_SESSION['upload_status'];

if($_SESSION['called']){
  
  unset($_SESSION['called']);
  header('Location: login.php');
 
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>User Profile</title>
  <link rel="stylesheet" href="../assets/style.css" />
  <script src="../assets/index.js"></script>
</head>
<body class="profile_body">



  <div class="profile_container">
  <button type="button" onclick="goBack()" style="margin-top: 10px;">Back</button>
    <h2>User Profile</h2>
    <form id="profileForm"  action="../controller/profileCheck.php" method="post" enctype="multipart/form-data">
      <p id="error" style="color: red;"><?= $status ?></p>

      <div class="profile_picture_section">
        <img id="profilePic" src="<?=$image_path?>" alt="Profile Picture" style="width: 120px; height: 120px; object-fit: cover; border-radius: 60px;" />
        <input type="file" id="uploadPic" name="uploadPic" accept="image/*" onchange="previewProfilePic()" style="display: none;" />
      </div>
   
      <div class="profile_fields">
        <label>Name:</label>
        <input type="text" id="name" name="name" value="<?= $name ?>" readonly/>

        <label>Email:</label>
        <input type="email" id="email" name="email" value="<?= $username ?>" readonly/>

        <label>Phone:</label>
        <input type="text" id="phone" name="phone" value="<?= $phone ?>" readonly/>
      </div>

      <button type="button" id="toggleBtn" onclick="toggleEdit()">Edit</button>
    </form>
  </div>
</body>
</html>
