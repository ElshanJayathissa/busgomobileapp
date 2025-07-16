<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Retrieve POST parameters
$bus_id = $_POST['bus_id'] ?? null;
$seat_number = $_POST['seat_number'] ?? null;
$booking_date = $_POST['booking_date'] ?? null;
$user_id = $_POST['user_id'] ?? null;
$status = $_POST['status'] ?? null;
$bus_fare = isset($_POST['full_fare']) ? floatval($_POST['full_fare']) : null;

// Validate required parameters
if (!$bus_id || !$seat_number || !$booking_date || !$user_id || !$status || $bus_fare === null) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

// Include your DB config (make sure this file defines $conn as a mysqli connection)
require_once 'db_config.php';

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Prepare and bind statement for inserting booking
$stmt = $conn->prepare("INSERT INTO bus_seats (bus_id, seat_number, bus_fare, booking_date, user_id, status) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

// Bind parameters with correct types: i - int, d - double, s - string
$stmt->bind_param("iissis", $bus_id, $seat_number, $bus_fare, $booking_date, $user_id, $status);



// Execute booking insert
if ($stmt->execute()) {
    // Insert notification if booking succeeded
    $title = "Seat Booked";
    $message = "Your seat number $seat_number for Bus ID $bus_id on $booking_date has been booked successfully. Fare: Rs. " . number_format($bus_fare, 2);
    $created_at = date("Y-m-d H:i:s");

    $notif_stmt = $conn->prepare("INSERT INTO user_notifications (user_id, title, message, created_at) VALUES (?, ?, ?, ?)");
    if (!$notif_stmt) {
        echo json_encode(['success' => false, 'message' => 'Notification prepare failed: ' . $conn->error]);
        exit;
    }
    $notif_stmt->bind_param("isss", $user_id, $title, $message, $created_at);
    $notif_stmt->execute();
    $notif_stmt->close();

    echo json_encode(['success' => true, 'message' => 'Booking stored successfully and notification sent']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to store booking: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
