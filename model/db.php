<?php
$host = "sql12.freesqldatabase.com";
$dbname = "sql12781551";
$dbuser = "sql12781551";
$dbpass = "bLxVR84MU2";

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
