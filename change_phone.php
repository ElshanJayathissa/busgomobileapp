<?php
header('Content-Type: application/json');
include 'db_config.php'; // Ensure this contains $conn = new mysqli(...)

$id = $_POST['id'] ?? null;
$phone = $_POST['phone'] ?? null;

if ($id && $phone) {
    $stmt = $conn->prepare("UPDATE users SET phone = ? WHERE id = ?");
    $stmt->bind_param("si", $phone, $id);

    if ($stmt->execute()) {
        // ✅ Insert notification
        $title = "Phone Number Updated";
        $message = "Your phone number has been successfully updated to $phone.";
        $created_at = date("Y-m-d H:i:s");

        $notif_stmt = $conn->prepare("INSERT INTO user_notifications (user_id, title, message, created_at) VALUES (?, ?, ?, ?)");
        $notif_stmt->bind_param("isss", $id, $title, $message, $created_at);
        $notif_stmt->execute();
        $notif_stmt->close();

        echo json_encode(["success" => true, "message" => "Phone number updated and notification sent"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
}

$conn->close();
?>
