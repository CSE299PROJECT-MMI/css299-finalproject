<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Create an Account/login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the appointment ID
$appointment_id = $_GET['appointment_id'];

// Update appointment status to 'Approved'
$sql = "UPDATE appointments SET status='Approved' WHERE appointment_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Appointment Approved Successfully!";
} else {
    echo "Error updating appointment status.";
}

$stmt->close();
$conn->close();
?>
