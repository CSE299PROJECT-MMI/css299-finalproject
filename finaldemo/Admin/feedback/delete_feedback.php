<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get feedback ID from the URL
$feedback_id = intval($_GET['feedback_id']);

// Delete feedback from the database
$sql = "DELETE FROM feedback WHERE feedback_id = $feedback_id";

if ($conn->query($sql) === TRUE) {
    // Redirect back to feedback list with a success message
    header("Location: admin_feedback.php?status=deleted");
} else {
    echo "Error deleting feedback: " . $conn->error;
}

$conn->close();
?>
