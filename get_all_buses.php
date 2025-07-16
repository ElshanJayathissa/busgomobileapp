<?php
header('Content-Type: application/json');
include 'db_config.php';

$response = [];
$sql = "SELECT id, name, type, location, facilities, price, phone, image_url FROM buses";
$result = mysqli_query($conn, $sql);

if ($result) {
    $buses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $buses[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'type' => $row['type'],
            'location' => $row['location'],
            'facilities' => $row['facilities'],
            'price' => $row['price'],
            'phone' => $row['phone'],
            'image_url' => $row['image_url']  // ✅ No appending needed
        ];
    }
    echo json_encode(['buses' => $buses]);
} else {
    echo json_encode(['buses' => []]);
}
?>
