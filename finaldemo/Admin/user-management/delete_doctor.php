<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if an ID is passed
if (isset($_GET['id'])) {
    $doctor_id = $_GET['id'];

    // Database connection
    $host = '127.0.0.1';
    $dbname = 'hospital_management';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Begin transaction to ensure both deletions happen together
        $pdo->beginTransaction();

        // First, delete the doctor availability records
        $stmt = $pdo->prepare("DELETE FROM doctor_availability WHERE doctor_email = (SELECT email FROM doctors WHERE id = :id)");
        $stmt->bindParam(':id', $doctor_id, PDO::PARAM_INT);
        $stmt->execute();

        // Now, delete the doctor
        $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = :id");
        $stmt->bindParam(':id', $doctor_id, PDO::PARAM_INT);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

        // Redirect back to the admin dashboard or doctors list
        header("Location: user-management.php");
        exit();
    } catch (PDOException $e) {
        // Rollback transaction in case of error
        $pdo->rollBack();
        die("Database error: " . $e->getMessage());
    }
} else {
    echo "Doctor ID is missing.";
}
?>
