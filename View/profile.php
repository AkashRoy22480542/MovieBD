<?php
session_start();
require_once('../Model/userModel.php');

if (!isset($_SESSION['status']) || $_SESSION['status'] !== true) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'];
$user = getUserByEmail($email);

if (!$user) {
    header('Location: login.php');
    exit();
}

$name = $user['first_name'] . ' ' . $user['last_name'];
if (!empty($user['profile_image']) && file_exists($user['profile_image'])) {
    $image_path = $user['profile_image'];
} else {
    $image_path = '';
}
$status = isset($_SESSION['upload_status']) ? $_SESSION['upload_status'] : '';

if(isset($_SESSION['called']) && $_SESSION['called']){
    unset($_SESSION['called']);
    header('Location: login.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - MovieDB</title>
    <link rel="stylesheet" href="../Assets/style.css">
    <link rel="stylesheet" href="../Assets/navbar.css">
    <script src="../Assets/script.js"></script>
</head>
<body class="profile_body">
    <nav class="navbar">
        <div class="logo">
            <a href="../home.php">MovieDB</a>
        </div>
        <div class="menu">
            <a href="../home.php">Home</a>
            <a href="../Controller/authController.php?action=logout">Logout</a>
        </div>
    </nav>

    <div class="profile_container">
        <button type="button" class="back-button" onclick="goBack()" style="margin-bottom: 20px;">← Back</button>
        <h2>User Profile</h2>
        
        <form id="profileForm" action="../Controller/profileController.php" method="post" enctype="multipart/form-data">
            <p id="error" style="<?= !empty($status) ? 'display: block;' : 'display: none;' ?>"><?= htmlspecialchars($status) ?></p>

            <div class="profile_picture_section">
                <img id="profilePic" src="<?= htmlspecialchars($image_path) ?>" alt="Profile Picture" onclick="document.getElementById('uploadPic').click();" />
                <input type="file" id="uploadPic" name="uploadPic" accept="image/*" onchange="previewProfilePic()" style="display: none;" />
            </div>
        
            <div class="profile_fields">
                <label>Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" data-original-value="<?= htmlspecialchars($name) ?>" readonly/>

                <label>Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly/>
            </div>

            <button type="button" id="toggleBtn" onclick="toggleEdit()">Edit</button>
            <button type="submit" id="saveBtn" style="display: none;">Save Changes</button>
        </form>
    </div>

<?php include 'footer.php'; ?>

</body>
</html>
