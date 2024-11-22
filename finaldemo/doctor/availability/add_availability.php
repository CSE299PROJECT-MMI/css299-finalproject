<?php
session_start();

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../Create an Account/login.php");
    exit();
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve inputs
$email = $_SESSION['user_id'];
$date = $_POST['date'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];

// Fetch first_name and last_name from the doctors table based on the email
$name_sql = "SELECT first_name, last_name FROM doctors WHERE email = ?";
$name_stmt = $conn->prepare($name_sql);
$name_stmt->bind_param("s", $email);
$name_stmt->execute();
$name_stmt->bind_result($first_name, $last_name);
$name_stmt->fetch();
$name_stmt->close();

if (!$first_name || !$last_name) {
    die("Error: Doctor name not found for the given email.");
}

// Concatenate first_name and last_name to form doctor_name
$doctor_name = $first_name . " " . $last_name;

// Insert the new availability into the doctor_availability table
$insert_sql = "INSERT INTO doctor_availability (doctor_email, available_date, start_time, end_time, doctor_name) 
               VALUES (?, ?, ?, ?, ?)";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("sssss", $email, $date, $start_time, $end_time, $doctor_name);

if ($insert_stmt->execute()) {
    echo "Availability added successfully.";
} else {
    echo "Error: " . $insert_stmt->error;
}

// Close resources
$insert_stmt->close();
$conn->close();

// Redirect to availability page
header("Location: availability.php");
exit();
?>
