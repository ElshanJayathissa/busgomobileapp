<?php
// Enable error reporting (for development only)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set response header to JSON
header('Content-Type: application/json');

// Include database configuration
include 'db_config.php'; // Make sure this file defines $conn

$response = [];

// Check DB connection
if (!$conn) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . mysqli_connect_error()
    ]);
    exit;
}

// Fetch data from traveling_places table
$sql = "SELECT * FROM traveling_places";
$result = mysqli_query($conn, $sql);

if ($result) {
    $places = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $places[] = [
            'id' => (int)$row['id'],
            'place_name' => $row['place_name'],
            'image_url' => $row['image_url'],
            'description' => $row['description']
        ];
    }

    echo json_encode([
        'success' => true,
        'places' => $places
    ], JSON_PRETTY_PRINT);

} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Query failed: ' . mysqli_error($conn)
    ]);
}
?>
