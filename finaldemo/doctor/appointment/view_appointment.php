<?php 
session_start();

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../Create an Account/login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the appointment_id from the URL
if (!isset($_GET['appointment_id']) || !is_numeric($_GET['appointment_id'])) {
    die("Invalid or missing Appointment ID.");
}

$appointment_id = $_GET['appointment_id'];

// Prepare the SQL query
$sql = "SELECT a.appointment_id, 
               CONCAT(u.first_name, ' ', u.last_name) AS patient_name, 
               u.phone_number, 
               u.birthday, 
               u.gender, 
               a.appointment_date, 
               a.start_time, 
               a.end_time, 
               a.status 
        FROM appointments a
        JOIN appointments_patients ap ON a.patient_id = ap.id  -- Corrected join
        JOIN users u ON ap.email= u.email
        WHERE a.appointment_id = ?";

// Prepare statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

// Bind the parameter
$stmt->bind_param("i", $appointment_id);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Check if any results were found
if ($result->num_rows === 0) {
    die("No appointment found for this ID.");
}

// Fetch the appointment details
$appointment = $result->fetch_assoc();

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard">
        <main class="content">
            <h1>Appointment Details</h1>
            <section class="appointment-details">
                <h2>Patient Information</h2>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($appointment['patient_name']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($appointment['phone_number']); ?></p>
                <p><strong>Birthday:</strong> <?php echo htmlspecialchars($appointment['birthday']); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($appointment['gender']); ?></p>

                <h2>Appointment Information</h2>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
                <p><strong>Start Time:</strong> <?php echo htmlspecialchars($appointment['start_time']); ?></p>
                <p><strong>End Time:</strong> <?php echo htmlspecialchars($appointment['end_time']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($appointment['status'])); ?></p>
            </section>
        </main>

        <!-- Right Sidebar -->
        <aside class="sidebar right">
            <button onclick="window.location.href='appointment.php'">Back to Appointments</button>
        </aside>
    </div>
</body>
</html>
