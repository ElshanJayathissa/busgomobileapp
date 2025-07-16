<?php
header("Content-Type: application/json");
include 'db_config.php';

$departure = $_POST['departure'] ?? '';
$arrival = $_POST['arrival'] ?? '';
$travel_date = $_POST['travel_date'] ?? '';
$ac = isset($_POST['ac']) ? intval($_POST['ac']) : -1;  // -1 means both

if (empty($departure) || empty($arrival) || empty($travel_date)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$query = "SELECT * FROM bus_schedules WHERE departure = ? AND arrival = ? AND travel_date = ?";
$params = [$departure, $arrival, $travel_date];

if ($ac === 0 || $ac === 1) {
    $query .= " AND ac = ?";
    $params[] = $ac;
}

$stmt = $conn->prepare($query);

// Dynamically bind parameters
$types = str_repeat('s', 3) . ($ac === 0 || $ac === 1 ? 'i' : '');
if ($ac === 0 || $ac === 1) {
    $stmt->bind_param($types, ...$params);
} else {
    // Without AC param
    $stmt->bind_param('sss', $departure, $arrival, $travel_date);
}

$stmt->execute();
$result = $stmt->get_result();

$buses = [];
while ($row = $result->fetch_assoc()) {
    $buses[] = $row;
}

if (count($buses) > 0) {
    echo json_encode(['success' => true, 'data' => $buses]);
} else {
    echo json_encode(['success' => true, 'data' => [], 'message' => 'No buses found']);
}

$stmt->close();
$conn->close();
?>
