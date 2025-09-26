<?php
$host = "localhost";
$dbname = "moviedb";
$dbuser = "root";
$dbpass = "";

function getConnection() {
    global $host, $dbname, $dbuser, $dbpass;

    $con = mysqli_connect($host, $dbuser, $dbpass, $dbname);

    if (!$con) {
        error_log("Connection failed: " . mysqli_connect_error());
        die("Database connection error");
    }

    return $con;
}
?>
