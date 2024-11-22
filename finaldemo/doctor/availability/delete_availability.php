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

// Retrieve the slot_id from the URL
if (!isset($_GET['id'])) { // Make sure this matches the parameter in the URL
    die("Slot ID not provided");
}

$slot_id = $_GET['id'];

// Delete the availability slot from the database
$delete_sql = "DELETE FROM doctor_availability WHERE id=?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $slot_id);
$delete_stmt->execute();
$delete_stmt->close();

$conn->close();

// Redirect back to the availability page
header("Location: availability.php");
exit();
