<?php
require_once 'db_config.php';
date_default_timezone_set('Asia/Colombo');

// Get current timestamp
$currentDateTime = date("Y-m-d H:i:s");

// Fetch all schedules
$scheduleQuery = "SELECT id, departure, arrival, travel_date, departure_time, duration FROM bus_schedules";
$scheduleResult = $conn->query($scheduleQuery);

if ($scheduleResult && $scheduleResult->num_rows > 0) {
    while ($schedule = $scheduleResult->fetch_assoc()) {
        $bus_id = $schedule['id'];
        $travel_date = $schedule['travel_date'];
        $departure_time = $schedule['departure_time'];
        $duration = $schedule['duration']; // Format: "1h 30m"

        // Combine date and time into DateTime object
        $departureDateTime = new DateTime("$travel_date $departure_time");

        // Parse duration and add to departure time
        $durationParts = sscanf($duration, "%dh %dm", $hours, $minutes);
        $interval = new DateInterval(sprintf("PT%dH%dM", $hours ?? 0, $minutes ?? 0));
        $departureDateTime->add($interval);

        // Check if trip is completed
        if ($departureDateTime < new DateTime($currentDateTime)) {
            // Delete seats for completed bus
            $delete = $conn->prepare("DELETE FROM bus_seats WHERE bus_id = ?");
            $delete->bind_param("i", $bus_id);
            $delete->execute();
            $delete->close();
        }
    }

    echo json_encode(['success' => true, 'message' => 'Old bookings cleaned up']);
} else {
    echo json_encode(['success' => false, 'message' => 'No schedules found']);
}

$conn->close();
?>
