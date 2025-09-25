<?php
require_once('db.php');

// User login function
function loginUser($user) {
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

// Get user by email
function getUserByEmail($email) {
    $con = getConnection();
    $email = mysqli_real_escape_string($con, $email);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($con, $sql);

    return ($result && mysqli_num_rows($result) === 1) ? mysqli_fetch_assoc($result) : null;
}

// Get all users
function getAllUsers() {
    $con = getConnection();
    $sql = "SELECT * FROM users";
    $result = mysqli_query($con, $sql);

    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    return $users;
}

// Add new user
function addUser($user) {
    $con = getConnection();
    $a = $user[0];

    $name = mysqli_real_escape_string($con, $a['full_name']);
    $email = mysqli_real_escape_string($con, $a['email']);
    $password = password_hash($a['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (full_name, email, password) 
            VALUES ('$name', '$email', '$password')";

    return mysqli_query($con, $sql);
}

// Delete user
function deleteUser($id) {
    $con = getConnection();
    $id = mysqli_real_escape_string($con, $id);

    $sql = "DELETE FROM users WHERE id='$id'";
    return mysqli_query($con, $sql);
}

// Check if email exists
function emailExistsUser($email) {
    $con = getConnection();
    $email = mysqli_real_escape_string($con, $email);

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $sql);

    return mysqli_num_rows($result) > 0;
}
?>
