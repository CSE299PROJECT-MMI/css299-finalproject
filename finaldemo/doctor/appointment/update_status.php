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

// Retrieve the appointment_id and status from the URL
if (!isset($_GET['appointment_id']) || !isset($_GET['status'])) {
    die("Appointment ID or Status not provided");
}

$appointment_id = $_GET['appointment_id'];
$status = $_GET['status'];

// Update the status of the appointment
$update_sql = "UPDATE appointments SET status=? WHERE appointment_id=?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("si", $status, $appointment_id);
$update_stmt->execute();
$update_stmt->close();

$conn->close();

// Redirect back to the appointments page
header("Location: appointments.php");
exit();
