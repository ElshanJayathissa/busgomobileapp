<?php
require_once("db_config.php");
header("Content-Type: application/json");

// Receive POST data
$user_id = $_POST['user_id'] ?? null;
$bus_id = $_POST['bus_id'] ?? null;
$booking_date = $_POST['booking_date'] ?? null;
$duration_days = isset($_POST['duration_days']) ? intval($_POST['duration_days']) : null;

// Validate inputs
if (!$user_id || !$bus_id || !$booking_date || !$duration_days) {
    echo json_encode(["success" => false, "message" => "Missing required parameters"]);
    exit;
}

// Calculate end_date
$end_date = date('Y-m-d', strtotime("$booking_date +".($duration_days - 1)." days"));

// Insert booking request
$sql = "INSERT INTO bus_booking_requests (user_id, bus_id, booking_date, duration_days, end_date, status, created_at)
        VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisis", $user_id, $bus_id, $booking_date, $duration_days, $end_date);
$success = $stmt->execute();

if ($success) {
    // ✅ Insert notification
    $title = "Bus Booking Request Sent";
    $message = "You have requested to book Bus ID $bus_id from $booking_date to $end_date. Status: pending.";
    $created_at = date("Y-m-d H:i:s");

    $notif_sql = "INSERT INTO user_notifications (user_id, title, message, created_at) VALUES (?, ?, ?, ?)";
    $notif_stmt = $conn->prepare($notif_sql);
    $notif_stmt->bind_param("isss", $user_id, $title, $message, $created_at);
    $notif_stmt->execute();
    $notif_stmt->close();

    echo json_encode(["success" => true, "message" => "Booking successful and notification sent"]);
} else {
    echo json_encode(["success" => false, "message" => "Booking failed"]);
}

$stmt->close();
$conn->close();
?>
