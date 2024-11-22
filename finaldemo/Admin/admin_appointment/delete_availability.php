<?php
session_start();

// Database connection settings
$host = 'localhost';  // or your host name
$username = 'root';  // database username
$password = '';  // database password
$dbname = 'hospital_management';  // your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Create an Account/login.php");
    exit();
}

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    $availability_id = $_GET['id'];

    // Prepare and execute the delete query
    $query = "DELETE FROM doctor_availability WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('i', $availability_id);
        if ($stmt->execute()) {
            // Store success message in session
            $_SESSION['message'] = "Doctor availability deleted successfully.";
        } else {
            // Store error message in session
            $_SESSION['message'] = "Error deleting doctor availability.";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Error preparing the delete statement.";
    }
} else {
    $_SESSION['message'] = "No availability ID specified.";
}

// Close the database connection
$conn->close();

// Redirect back to the appointments page (or any page you want)
header('Location: appointments.php');
exit;
?>
