<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get doctor availability along with department name
$sql = "
    SELECT da.doctor_name, da.available_date, da.start_time, da.end_time, d.department, d.email AS doctor_email
    FROM doctor_availability da
    JOIN doctors d ON da.doctor_name = d.first_name
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctor_name = $row['doctor_name'];
        $available_date = $row['available_date'];
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];
        $department_name = $row['department'];
        $doctor_email = $row['doctor_email'];

        // Generate 1-hour time slots
        $current_time = strtotime($start_time);
        $end_time = strtotime($end_time);

        while ($current_time < $end_time) {
            $slot_start_time = date("H:i:s", $current_time);
            $current_time = strtotime("+1 hour", $current_time); // Add 1 hour for next slot
            $slot_end_time = date("H:i:s", $current_time);

            // Insert the time slot into the time_slots table
            $insert_sql = "
                INSERT INTO time_slots (doctor_name, department_name, available_date, start_time, end_time, doctor_email)
                VALUES (?, ?, ?, ?, ?, ?)
            ";

            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssssss", $doctor_name, $department_name, $available_date, $slot_start_time, $slot_end_time, $doctor_email);
            $stmt->execute();
        }
    }
    echo "Time slots populated successfully!";
} else {
    echo "No available times found.";
}

$conn->close();
?>
