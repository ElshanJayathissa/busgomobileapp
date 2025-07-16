<?php
header('Content-Type: application/json');
include 'db_config.php';

$id = $_POST['id'] ?? null;
$password = $_POST['password'] ?? null;

if ($id && $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Password updated"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error"]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
}

$conn->close();
