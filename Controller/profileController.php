<?php
session_start();
require_once('../Model/userModel.php');

// Check if user is logged in
if (!isset($_SESSION['status']) || $_SESSION['status'] !== true) {
    header('Location: ../View/login.php');
    exit();
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $image_path = '';
    
    // Handle image upload
    if (isset($_FILES['uploadPic']) && $_FILES['uploadPic']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../Assets/images/profiles/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileInfo = pathinfo($_FILES['uploadPic']['name']);
        $extension = strtolower($fileInfo['extension']);
        
        // Validate image extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($extension, $allowedExtensions)) {
            $_SESSION['upload_status'] = 'Invalid image format. Please use JPG, JPEG, PNG, or GIF.';
            header('Location: ../View/profile.php');
            exit();
        }
        
        // Generate filename using username
        $username = $_SESSION['username'];
        $filename = $username . '_profile.' . $extension;
        $uploadPath = $uploadDir . $filename;
        
        // Delete old profile image if exists
        $oldImage = getUserByEmail($email);
        if ($oldImage && !empty($oldImage['profile_image']) && file_exists($oldImage['profile_image'])) {
            unlink($oldImage['profile_image']);
        }
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['uploadPic']['tmp_name'], $uploadPath)) {
            $image_path = $uploadPath;
        } else {
            $_SESSION['upload_status'] = 'Failed to upload image. Please try again.';
            header('Location: ../View/profile.php');
            exit();
        }
    }
    
    // Validate inputs
    if (empty($name)) {
        $_SESSION['upload_status'] = 'Name cannot be empty.';
        header('Location: ../View/profile.php');
        exit();
    }
    
    try {
        // Update user profile
        if (updateUserProfile($email, $name, '', $image_path)) {
            // Update session with new name
            $nameParts = explode(' ', $name, 2);
            $_SESSION['firstName'] = $nameParts[0];
            $_SESSION['lastName'] = isset($nameParts[1]) ? $nameParts[1] : '';
            
            $_SESSION['upload_status'] = 'Profile updated successfully!';
            $_SESSION['called'] = false; // Don't redirect to login on success
        } else {
            $_SESSION['upload_status'] = 'Failed to update profile. Please try again.';
        }
    } catch (Exception $e) {
        $_SESSION['upload_status'] = 'Error: ' . $e->getMessage();
    }
    
    header('Location: ../View/profile.php');
    exit();
} else {
    // If not POST request, redirect to profile page
    header('Location: ../View/profile.php');
    exit();
}
?>
