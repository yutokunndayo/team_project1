<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "災害";

$conn= mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
die("Database Fail To Connect: " . mysqli_connect_error());
}
?>