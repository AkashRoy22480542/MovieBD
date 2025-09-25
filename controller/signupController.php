<?php
require_once('db.php');
require_once('model/userModel.php');

// Start session
session_start();

// Process signup form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = [
        'full_name' => $_POST['full_name'],
        'email' => $_POST['email'],
        'password' => $_POST['password']
    ];
    
    // Verify password confirmation
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $error = "Passwords do not match!";
    } else {
        if (signupUser($user)) {
            header("Location: ../view/dashboard.php");
            exit();
        } else {
            $error = "Email already exists or registration failed";
        }
    }
    
    // Redirect back with error
    if (isset($error)) {
        header("Location: ../view/signup.php?error=" . urlencode($error));
        exit();
    }
}

// User signup function
function signupUser($user) {
    // Check if email already exists
    if (emailExistsUser($user['email'])) {
        return false;
    }
    
    // Add user to database
    $userData = [[
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'password' => $user['password']
    ]];
    
    $userId = addUser($userData);
    
    if ($userId) {
        // Set session after successful signup
        $_SESSION['user_email'] = $user['email'];
        return true;
    }
    
    return false;
}

// Admin signup function
function signupAdmin($admin) {
    // Check if email already exists
    if (emailExistsAdmin($admin['email'])) {
        return false;
    }
    
    // Add admin to database
    $adminData = [[
        'full_name' => $admin['full_name'],
        'email' => $admin['email'],
        'password' => $admin['password'],
        'role' => $admin['role'] ?? 'admin',
        'permissions' => $admin['permissions'] ?? []
    ]];
    
    $adminId = addAdmin($adminData);
    
    if ($adminId) {
        // Set session after successful signup
        $_SESSION['admin_email'] = $admin['email'];
        return true;
    }
    
    return false;
}
?>
