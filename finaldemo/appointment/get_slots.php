<?php
// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hospital_management"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the selected date and doctor's name from the URL query parameters
$selectedDate = isset($_GET['date']) ? $_GET['date'] : '';
$doctorName = isset($_GET['doctorName']) ? $_GET['doctorName'] : '';

$response = [];

if (!empty($selectedDate) && !empty($doctorName)) {
    // Query to fetch available time slots for the doctor on the selected date
    $sql = "
        SELECT start_time, end_time 
        FROM doctor_availability 
        WHERE doctor_name = ? AND available_date = ?";
        
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $doctorName, $selectedDate);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $timeSlots = [];
                while ($row = $result->fetch_assoc()) {
                    // Convert 24-hour format to 12-hour format (AM/PM)
                    $startTime = strtotime($row['start_time']);
                    $endTime = strtotime($row['end_time']);

                    // Generate slots in 1-hour intervals
                    while ($startTime < $endTime) {
                        $slotStart = date("h:i A", $startTime);
                        $slotEnd = date("h:i A", strtotime('+1 hour', $startTime));
                        $timeSlots[] = "$slotStart - $slotEnd"; // Combine start and end times
                        $startTime = strtotime('+1 hour', $startTime);
                    }
                }
                $response['timeSlots'] = $timeSlots;
            } else {
                $response['message'] = "No available slots for this date.";
            }
        } else {
            $response['message'] = "Error executing query: " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    $response['message'] = "Invalid parameters provided.";
}

$conn->close();

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>