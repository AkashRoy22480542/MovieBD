<?php
require_once('db.php');

function login($user) {
    $con = getConnection();
    $email = mysqli_real_escape_string($con, $user['email']);
    $password = $user['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        return password_verify($password, $row['password']); 
    }

    return false;
}

function getUserByEmail($email) {
    $con = getConnection();
    $email = mysqli_real_escape_string($con, $email);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($con, $sql);

    return ($result && mysqli_num_rows($result) === 1) ? mysqli_fetch_assoc($result) : null;
}

function getAllUser() {
    $con = getConnection();
    $sql = "SELECT * FROM users";
    $result = mysqli_query($con, $sql);

    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    return $users;
}

function deleteUser($id) {
    $con = getConnection();
    $id = mysqli_real_escape_string($con, $id);

    $sql = "DELETE FROM users WHERE id='$id'";
    return mysqli_query($con, $sql);
}

function addUser($user) {
    $con = getConnection();
    $u = $user[0];

    $name = mysqli_real_escape_string($con, $u['name']);
    $email = mysqli_real_escape_string($con, $u['email']);
    $phone = mysqli_real_escape_string($con, $u['phone']);
    $password    = password_hash($u['password'], PASSWORD_DEFAULT);
    $image_path = mysqli_real_escape_string($con, $u['image_path']);

    // Split name into first_name and last_name for database compatibility
    $nameParts = explode(' ', $name, 2);
    $first_name = $nameParts[0];
    $last_name = isset($nameParts[1]) ? $nameParts[1] : '';
    
    // Use provided username or create default if not provided
    if (isset($u['username']) && !empty($u['username'])) {
        $username = mysqli_real_escape_string($con, $u['username']);
    } else {
        $username = strtolower(str_replace(' ', '', $name)) . rand(100, 999);
    }

    $sql = "INSERT INTO users (first_name, last_name, username, email, password, profile_image)
            VALUES ('$first_name', '$last_name', '$username', '$email', '$password', '$image_path')";

    return mysqli_query($con, $sql);
}

function getImageById($id) {
    $con = getConnection();
    $id = mysqli_real_escape_string($con, $id);

    $sql = "SELECT profile_image FROM users WHERE id='$id'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        return $row['profile_image'];
    }
    return null;
}

function setImageById($id, $path) {
    $con = getConnection();
    $id = mysqli_real_escape_string($con, $id);
    $path = mysqli_real_escape_string($con, $path);

    $sql = "UPDATE users SET profile_image='$path' WHERE id='$id'";
    return mysqli_query($con, $sql);
}

function emailExists($email) {
        $con = getConnection();
        $email = mysqli_real_escape_string($con, $email);
    
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($con, $sql);
    
        return mysqli_num_rows($result) > 0;
}

function updateUserProfile($email, $name, $phone, $image_path) {
    $con = getConnection();
    $email = mysqli_real_escape_string($con, $email);
    $name = mysqli_real_escape_string($con, $name);
    $phone = mysqli_real_escape_string($con, $phone);
    $image_path = mysqli_real_escape_string($con, $image_path);

    // Split name into first_name and last_name for database compatibility
    $nameParts = explode(' ', $name, 2);
    $first_name = $nameParts[0];
    $last_name = isset($nameParts[1]) ? $nameParts[1] : '';

    $sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', profile_image='$image_path' WHERE email='$email'";
    $execution=mysqli_query($con, $sql);
    // $_SESSION['called']=$execution;
    return $execution;
}

    
?>
