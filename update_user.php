<?php
include 'db_config.php';

$id = $_POST['id'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$region = $_POST['region'];
$phone = $_POST['phone'];

$sql = "UPDATE users SET 
    first_name='$first_name', 
    last_name='$last_name', 
    email='$email', 
    region='$region', 
    phone='$phone' 
    WHERE id=$id";

if (mysqli_query($conn, $sql)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error"]);
}
?>
