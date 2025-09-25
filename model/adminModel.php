<?php
require_once('db.php');

// Admin login function
function loginAdmin($admin) {
    $con = getConnection();
    $email = mysqli_real_escape_string($con, $admin['email']);
    $password = $admin['password'];

    $sql = "SELECT * FROM admins WHERE email='$email'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        return password_verify($password, $row['password']);
    }

    return false;
}

// Get admin by email
function getAdminByEmail($email) {
    $con = getConnection();
    $email = mysqli_real_escape_string($con, $email);

    $sql = "SELECT * FROM admins WHERE email='$email'";
    $result = mysqli_query($con, $sql);

    return ($result && mysqli_num_rows($result) === 1) ? mysqli_fetch_assoc($result) : null;
}

// Get all admins
function getAllAdmins() {
    $con = getConnection();
    $sql = "SELECT * FROM admins";
    $result = mysqli_query($con, $sql);

    $admins = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $admins[] = $row;
    }
    return $admins;
}

// Add new admin
function addAdmin($admin) {
    $con = getConnection();
    $a = $admin[0];

    $name = mysqli_real_escape_string($con, $a['full_name']);
    $email = mysqli_real_escape_string($con, $a['email']);
    $password = password_hash($a['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($con, $a['role']);
    $permissions = json_encode($a['permissions']); // Assuming permissions is an array

    $sql = "INSERT INTO admins (full_name, email, password, role, permissions) 
            VALUES ('$name', '$email', '$password', '$role', '$permissions')";

    return mysqli_query($con, $sql);
}

// Delete admin
function deleteAdmin($id) {
    $con = getConnection();
    $id = mysqli_real_escape_string($con, $id);

    $sql = "DELETE FROM admins WHERE id='$id'";
    return mysqli_query($con, $sql);
}

// Check if email exists
function emailExistsAdmin($email) {
    $con = getConnection();
    $email = mysqli_real_escape_string($con, $email);

    $sql = "SELECT * FROM admins WHERE email = '$email'";
    $result = mysqli_query($con, $sql);

    return mysqli_num_rows($result) > 0;
}
?>
