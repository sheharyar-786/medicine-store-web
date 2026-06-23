<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "medicine_db";

// Using MySQLi for a straightforward connection
$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8 to handle special characters correctly
mysqli_set_charset($conn, "utf8");
?>