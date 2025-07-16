<?php
header('Content-Type: application/json');
include 'db_config.php';

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

$response = [];

if ($user_id > 0) {
    $sql = "SELECT id, title, message, created_at FROM user_notifications WHERE user_id = $user_id ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);

    $notifications = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }

    echo json_encode(["success" => true, "data" => $notifications]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid user ID"]);
}
?>
