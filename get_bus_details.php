<?php
header('Content-Type: application/json');
include 'db_config.php';

$bus_id = $_GET['bus_id'] ?? '';

if (empty($bus_id)) {
    echo json_encode(['success' => false, 'message' => 'Bus ID missing']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM bus_schedules WHERE id = ?");
$stmt->bind_param("i", $bus_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'bus' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'No bus found']);
}

$stmt->close();
$conn->close();
?>
