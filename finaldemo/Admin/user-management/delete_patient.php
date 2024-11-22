<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if an ID is passed
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $patient_id = $_GET['id'];

    // Database connection
    $host = '127.0.0.1';
    $dbname = 'hospital_management';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the DELETE query
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $patient_id, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect back to the admin dashboard or patients list
            $_SESSION['message'] = "Patient deleted successfully.";
            header("Location: user-management.php");
            exit();
        } else {
            echo "Error deleting the patient.";
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    echo "Patient ID is missing or invalid.";
}
?>
