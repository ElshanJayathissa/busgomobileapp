<?php
header("Content-Type: application/json");
include 'db_config.php';

$bus_id = $_POST['bus_id'] ?? '';

if (empty($bus_id)) {
    echo json_encode(['success' => false, 'message' => 'Missing bus_id']);
    exit;
}

$query = "SELECT seat_number FROM bus_seats WHERE bus_id = ? AND status = 'booked'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $bus_id);
$stmt->execute();
$result = $stmt->get_result();

$seats = [];
while ($row = $result->fetch_assoc()) {
    $seats[] = intval($row['seat_number']);
}

echo json_encode(['success' => true, 'seats' => $seats]);
$stmt->close();
$conn->close();
