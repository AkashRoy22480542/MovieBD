<?php
require_once('db.php');
require_once('model/userModel.php');
require_once('model/adminModel.php');

// Start session
session_start();

// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = [
        'email' => $_POST['email'],
        'password' => $_POST['password']
    ];
    
    $rememberMe = isset($_POST['rememberMe']);
    
    if (loginUser($user, $rememberMe)) {
        header("Location: ../view/dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password";
        header("Location: ../view/login.php?error=" . urlencode($error));
        exit();
    }
}

// User login function
function loginUser($user, $rememberMe = false) {
    $isLoggedIn = loginUser($user);
    if ($isLoggedIn) {
        $_SESSION['user_email'] = $user['email'];
        // Set a cookie for 10 minutes if "remember me" is checked
        if ($rememberMe) {
            setcookie('user_logged_in', '1', time() + 600, "/"); // 10 minutes
        }
        return true;
    }
    return false;
}

// Admin login function
function loginAdmin($admin, $rememberMe = false) {
    $isLoggedIn = loginAdmin($admin);
    if ($isLoggedIn) {
        $_SESSION['admin_email'] = $admin['email'];
        // Set a cookie for 10 minutes if "remember me" is checked
        if ($rememberMe) {
            setcookie('admin_logged_in', '1', time() + 600, "/"); // 10 minutes
        }
        return true;
    }
    return false;
}
?>
