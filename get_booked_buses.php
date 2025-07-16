<?php
header('Content-Type: application/json');
include 'db_config.php';

$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

$sql = "SELECT b.name, b.image_url, r.booking_date, r.status, r.id AS booking_id
        FROM bus_booking_requests r
        JOIN buses b ON r.bus_id = b.id
        WHERE r.user_id = $userId
        ORDER BY r.booking_date DESC";

$result = mysqli_query($conn, $sql);

$response = [];
if ($result && mysqli_num_rows($result) > 0) {
    $bookedBuses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $bookedBuses[] = [
            'bus_name' => $row['name'],
            'image_url' => $row['image_url'],
            'booking_date' => $row['booking_date'],
            'status' => $row['status'],
            'message' => 'Please confirm early.'
        ];
    }
    echo json_encode(['status' => 'success', 'data' => $bookedBuses]);
} else {
    echo json_encode(['status' => 'empty', 'data' => []]);
}
?>
