<?php
session_start();
require_once('../model/userModel.php');

if (!isset($_SESSION['status']) || $_SESSION['status'] !== true) {
    header('Location: ../view/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../view/profile.php');
    exit();
}


$name = trim($_POST['name']);
$phone = trim($_POST['phone']);
$image_path = '';
$_SESSION['upload_status']='';

if (empty($name) || empty($phone)) {
    $_SESSION['upload_status'] = "Name and phone cannot be empty.";
    header('Location: ../view/profile.php');
    exit();
}

if (!preg_match('/^01[3-9][0-9]{8}$/', $phone)) {
    $_SESSION['upload_status'] = "Invalid phone number.Only Bangladeshi Number Accepted";
    header('Location: ../view/profile.php');
    exit();
}



if (isset($_FILES['uploadPic']) && $_FILES['uploadPic']['error'] === UPLOAD_ERR_OK) {
        $src = $_FILES['uploadPic']['tmp_name'];
        $ext = explode('.', $_FILES['uploadPic']['name']);
        $filename = explode('.', $_SESSION['username']);
        $des = '../assets/upload/' . $filename[0].'.'.$ext[1];
        if (move_uploaded_file($src, $des)) {
            $image_path = $des;
        } else {
            $user = getUserByEmail($_SESSION['email']);
            $image_path = $user ? $user['image_path'] : '';
        }
}

if ($image_path == '') {
    $image_path = getUserByEmail($_SESSION['username'])['image_path'];
}


updateUserProfile($_SESSION['username'], $name, $phone, $image_path); 

header('Location: ../view/profile.php');
exit();







