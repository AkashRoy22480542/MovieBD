<?php
require_once('db.php');

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

function getAdminByEmail($email) {
    $con = getConnection();
    $email = mysqli_real_escape_string($con, $email);

    $sql = "SELECT * FROM admins WHERE email='$email'";
    $result = mysqli_query($con, $sql);

    return ($result && mysqli_num_rows($result) === 1) ? mysqli_fetch_assoc($result) : null;
}

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

function addAdmin($admin) {
    $con = getConnection();
    $a = $admin[0];

    $name = mysqli_real_escape_string($con, $a['name']);
    $email = mysqli_real_escape_string($con, $a['email']);
    $password = password_hash($a['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO admins (name, email, password) 
            VALUES ('$name', '$email', '$password')";

    return mysqli_query($con, $sql);
}

function deleteAdmin($id) {
    $con = getConnection();
    $id = mysqli_real_escape_string($con, $id);

    $sql = "DELETE FROM admins WHERE id='$id'";
    return mysqli_query($con, $sql);
}

function emailExistsAdmin($email) {
    $con = getConnection();
    $email = mysqli_real_escape_string($con, $email);

    $sql = "SELECT * FROM admins WHERE email = '$email'";
    $result = mysqli_query($con, $sql);

    return mysqli_num_rows($result) > 0;
}

?>
