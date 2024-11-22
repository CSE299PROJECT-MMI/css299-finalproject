<?php
  // Start session
  session_start();

// Database connection setup
 $servername = "localhost";
$username = "root";
$password = "";
$dbname = "hospital_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect form data and sanitize it
$patientEmail = isset($_POST['patient_email']) ? $conn->real_escape_string($_POST['patient_email']) : '';
$name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
$phoneNumber = isset($_POST['phone_number']) ? $conn->real_escape_string($_POST['phone_number']) : '';
$age = isset($_POST['age']) ? intval($_POST['age']) : 0;
$gender = isset($_POST['gender']) ? $conn->real_escape_string($_POST['gender']) : '';
$address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : '';
$patientStatus = isset($_POST['patient_status']) ? $conn->real_escape_string($_POST['patient_status']) : '';
$doctorName = isset($_POST['doctor_name']) ? $conn->real_escape_string($_POST['doctor_name']) : '';
$department = isset($_POST['department']) ? $conn->real_escape_string($_POST['department']) : '';
$appointmentDate = isset($_POST['appointment_date']) ? $conn->real_escape_string($_POST['appointment_date']) : '';
$timeSlot = isset($_POST['time_slot']) ? $conn->real_escape_string($_POST['time_slot']) : '';

// Check if required fields are populated
if (empty($patientEmail) || empty($name) || empty($appointmentDate) || empty($timeSlot)) {
    die("Please fill all the required fields.");
}

// Convert time slot to start and end times for availability checks
$startTime = $timeSlot;
$endTime = date("H:i:s", strtotime('+1 hour', strtotime($startTime)));

// Fetch the doctor's email based on their full name
$doctorEmailQuery = "
    SELECT email 
    FROM doctors 
    WHERE CONCAT(first_name, ' ', last_name) = ?";
    
$doctorStmt = $conn->prepare($doctorEmailQuery);
$doctorStmt->bind_param("s", $doctorName);
$doctorStmt->execute();
$doctorResult = $doctorStmt->get_result();

if ($doctorResult->num_rows > 0) {
    $doctorRow = $doctorResult->fetch_assoc();
    $doctorEmail = $doctorRow['email'];
} else {
    die("Error: Doctor not found.");
}

$doctorCheckQuery = "
    SELECT * 
    FROM appointments 
    WHERE doctor_email = ? 
      AND appointment_date = ? 
      AND start_time = ?";

$doctorStmt = $conn->prepare($doctorCheckQuery);
$doctorStmt->bind_param("sss", $doctorEmail, $appointmentDate, $startTime);
$doctorStmt->execute();
$doctorResult = $doctorStmt->get_result();

if ($doctorResult->num_rows > 0) {
    // Set an error message in the session
    $_SESSION['error_message'] = "The doctor is not available at the selected start time.";
    
    // Redirect to doctorcheck.php
    header("Location: doctorcheck.php");
    exit();
}


// 2. Check if the patient already has an appointment in the same time slot
$patientCheckQuery = "
    SELECT * 
    FROM appointments_patients 
    WHERE email = ? 
      AND appointment_date = ? 
      AND NOT (? >= end_time OR ? <= start_time)"; // Checking overlap with the patient's existing appointment
$patientStmt = $conn->prepare($patientCheckQuery);
$patientStmt->bind_param("ssss", $patientEmail, $appointmentDate, $startTime, $endTime);
$patientStmt->execute();
$patientResult = $patientStmt->get_result();

if ($patientResult->num_rows > 0) {
    die("You already have an appointment during the selected time slot.");
}

// 3. Insert into appointments_patients table
$insert_sql_patient = "
INSERT INTO appointments_patients (email, name, phone_number, age, gender, address, appointment_date, start_time, patient_status, department_name, doctor_name) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt_patient = $conn->prepare($insert_sql_patient);
$stmt_patient->bind_param("sssssssssss", $patientEmail, $name, $phoneNumber, $age, $gender, $address, $appointmentDate, $startTime, $patientStatus, $department, $doctorName);

if ($stmt_patient->execute()) {
    // Retrieve the patient_id for the newly inserted appointment
    $patient_id_query = "SELECT id FROM appointments_patients WHERE email = ? ORDER BY id DESC LIMIT 1";
    $stmt_patient_id = $conn->prepare($patient_id_query);
    $stmt_patient_id->bind_param("s", $patientEmail);
    $stmt_patient_id->execute();
    $patient_id_result = $stmt_patient_id->get_result();
    if ($patient_id_result->num_rows > 0) {
        $patient_id = $patient_id_result->fetch_assoc()['id'];

        // 4. Insert into appointments table
        $insert_confirmation_sql = "
        INSERT INTO appointments (doctor_email, patient_id, patient_name, appointment_date, start_time, end_time, status, doctor_name) 
        VALUES (?, ?, ?, ?, ?, ?, 'Confirmed', ?)";
        $stmt_confirmation = $conn->prepare($insert_confirmation_sql);
        $stmt_confirmation->bind_param("sssssss", $doctorEmail, $patient_id, $name, $appointmentDate, $startTime, $endTime, $doctorName);

        if ($stmt_confirmation->execute()) {
            // Close the connection before redirecting
            $conn->close();
            header("Location: confirmation.php"); // Booking confirmed
            exit(); // Exit after the redirect
        } else {
            die("Error: " . $stmt_confirmation->error);
        }
    } else {
        die("Error: Could not retrieve patient ID.");
    }
} else {
    die("Error: " . $stmt_patient->error);
}

// Close the connection (not needed if script ends via header/exit)
     
       ?>