<?php
require_once("db_config.php");

$userId = $_GET['user_id'];
$busId = $_GET['bus_id'];

$sql = "SELECT * FROM bus_booking_requests WHERE user_id = ? AND bus_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $userId, $busId);
$stmt->execute();
$result = $stmt->get_result();

$response = [];

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['status'] = 'success';
    $response['data'] = $row;
} else {
    $response['status'] = 'not_found';
}

echo json_encode($response);
?>
