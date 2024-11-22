<?php
session_start();

// Check if the user is logged in as a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$patient_name = $_POST['patient_name'];
$doctor_name = $_POST['doctor_name'];
$feedback_message = $_POST['feedback_message'];
$rating = $_POST['rating'];

// Insert feedback into the database
$sql = "INSERT INTO feedback (patient_name, doctor_name, feedback_message, rating, date_submitted, approved)
        VALUES ('$patient_name', '$doctor_name', '$feedback_message', '$rating', NOW(), 0)";
if ($conn->query($sql) === TRUE) {
    echo "Feedback submitted successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
