<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

include 'db_config.php';

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['first_name'] ?? '';
    $lname = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $region = $_POST['region'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if (empty($fname) || empty($lname) || empty($email) || empty($region) || empty($phone) || empty($password)) {
        $response = ['success' => false, 'message' => 'Please fill all required fields'];
        echo json_encode($response);
        exit;
    }

    // Check for existing user
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
    $checkStmt->bind_param("ss", $email, $phone);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $response = ['success' => false, 'message' => 'An account with this email or phone already exists'];
        echo json_encode($response);
        $checkStmt->close();
        $conn->close();
        exit;
    }
    $checkStmt->close();

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, region, phone, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $fname, $lname, $email, $region, $phone, $hashedPassword);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        // Insert welcome notification
        $title = "Welcome!";
        $message = "Thank you for signing up with our app.";
        $created_at = date("Y-m-d H:i:s");

        $notifStmt = $conn->prepare("INSERT INTO user_notifications (user_id, title, message, created_at) VALUES (?, ?, ?, ?)");
        $notifStmt->bind_param("isss", $user_id, $title, $message, $created_at);
        $notifStmt->execute();
        $notifStmt->close();

        $response = ['success' => true, 'message' => 'Account created successfully'];
    } else {
        $response = ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }

    $stmt->close();
    $conn->close();
} else {
    $response = ['success' => false, 'message' => 'Invalid request method'];
}

echo json_encode($response);
?>
