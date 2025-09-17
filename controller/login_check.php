<?php
session_start();


$_SESSION['status'] = false;
$_SESSION['loginError'] = '';
$_SESSION['username'] = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['loginError'] = "Email and password are required.";
        header('Location: ../view/login.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['loginError'] = "Invalid email format.";
        header('Location: ../view/login.php');
        exit();
    }

    $user = [
        'email'    => $email,
        'password' => $password
    ];

    if (loginAdmin($user)) {
        $_SESSION['status'] = true;
        $_SESSION['username'] = $email;
        header('Location: ../view/admin_dashboard.php');
        exit();
    }

    if (login($user)) {
        $_SESSION['status'] = true;
        $_SESSION['username'] = $email;
        header('Location: ../index.php');
        exit();
    } else {
        $_SESSION['loginError'] = "Email and password do not match.";
        header('Location: ../view/login.php');
        exit();
    }

} else {
    header('Location: ../view/login.php');
    exit();
}
