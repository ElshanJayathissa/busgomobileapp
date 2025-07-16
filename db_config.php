<?php
$host = "sql8.freesqldatabase.com";
$user = "sql8790262";
$pass = "2cVQq9tUrD";
$db = "sql8790262";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
