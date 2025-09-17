<?php
session_start();


$_SESSION['signup_error'] = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['signup_error'] = "All fields are required.";
        header("Location: ../view/signup.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['signup_error'] = "Invalid email format.";
        header("Location: ../view/signup.php");
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['signup_error'] = "Password must be at least 8 characters.";
        header("Location: ../view/signup.php");
        exit();
    }

    if (emailExists($email)) {
        $_SESSION['signup_error'] = "Email already exists.";
        header("Location: ../view/signup.php");
        exit();
    }

    $user = [
        0 => [
            'name'        => $name,
            'email'       => $email,
            'phone'       => '',         
            'password'    => $password,
            'image_path'  => ''
        ]
    ];

    if (addUser($user)) {
        $_SESSION['signup_success'] = "Account created successfully!";
        header("Location: ../view/login.php");
        exit();
    } else {
        $_SESSION['signup_error'] = "Failed to create account.";
        header("Location: ../view/signup.php");
        exit();
    }
}
?>
