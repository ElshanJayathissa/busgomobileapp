<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

include 'db_config.php'; // contains $conn

$user_id = $_GET['user_id'] ?? 0;

$sql = "SELECT bs.booking_date, b.departure_time, b.bus_name, b.bus_number, GROUP_CONCAT(bs.seat_number) as seat_numbers
        FROM bus_seats bs
        JOIN bus_schedules b ON bs.bus_id = b.id
        WHERE bs.user_id = ? AND bs.status = 'booked'
        GROUP BY bs.booking_date, bs.bus_id
        ORDER BY bs.booking_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$tickets = [];

while ($row = $result->fetch_assoc()) {
    $tickets[] = [
        'bookingDate' => $row['booking_date'],
        'busTime' => $row['departure_time'],
        'busName' => $row['bus_name'],
        'busNumber' => $row['bus_number'],
        'seatNumbers' => explode(',', $row['seat_numbers']),
    ];
}

echo json_encode($tickets);
?>
