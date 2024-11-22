<?php
// Include database connection

// Database connection settings
$host = 'localhost';  // or your host name
$username = 'root';  // database username
$password = '';  // database password
$dbname = 'hospital_management';  // your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}  // Ensure this file contains the connection logic

// Check if an action is set (such as delete, update, etc.)
if(isset($_GET['action'])) {
    $action = $_GET['action'];
    
    // If action is 'delete'
    if($action == 'delete' && isset($_GET['id'])) {
        $appointment_id = $_GET['id'];
        
        // Prepare and execute delete query
        $query = "DELETE FROM appointments WHERE appointment_id = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('i', $appointment_id);
            if($stmt->execute()) {
                echo "Appointment deleted successfully.";
            } else {
                echo "Error deleting appointment.";
            }
            $stmt->close();
        }
    }

    // If action is 'update'
    if($action == 'update' && isset($_GET['id']) && isset($_GET['status'])) {
        $appointment_id = $_GET['id'];
        $status = $_GET['status']; // This would be the new status
        
        // Update appointment status
        $query = "UPDATE appointments SET status = ? WHERE appointment_id = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('si', $status, $appointment_id);
            if($stmt->execute()) {
                echo "Appointment status updated.";
            } else {
                echo "Error updating status.";
            }
            $stmt->close();
        }
    }
}

// Close the database connection
$conn->close();
?>
