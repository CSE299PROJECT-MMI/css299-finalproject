<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Create an Account/login.php");
    exit();
}

// Database connection (ensure this part is correct and executed before any queries)
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Your query here
$sql = "SELECT * FROM appointments"; // example query
$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    die("Error executing query: " . $conn->error);
}

// Fetch doctor availability data
$availabilitySql = "SELECT * FROM doctor_availability";
$availabilityResult = $conn->query($availabilitySql);

// Check if the query was successful
if ($availabilityResult === false) {
    die("Error fetching doctor availability: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments</title>
</head>
<style>
/* General Page Layout */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

/* Heading */
h1 {
    text-align: center;
    color: #333;
    margin-top: 30px;
    font-size: 36px;
}

/* Table Styling */
table {
    width: 80%;
    margin: 20px auto;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    font-size: 16px;
}

th {
    background-color: #2c3e50;;
    color: white;
    font-weight: bold;
}

td {
    background-color: #f9f9f9;
}

tr:hover td {
    background-color: #e9f5ff;
}

/* Button Styling for Action Links */

/* Button Styling for Dashboard */
.dashboard-btn {
    /* Green */
    color: black;
    border: none;
    padding: 12px 30px;
    font-size: 18px;
    font-weight: bold;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 30px;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.dashboard-btn a {
    text-decoration: none;
    color: black;
}
 
.dashboard-btn a:hover {
    background-color: #2c3e50;
    color: #e9f5ff;
}


/* Responsive Design */
@media (max-width: 768px) {
    table {
        width: 100%;
        margin: 10px;
    }

    th, td {
        padding: 10px;
        font-size: 14px;
    }

    h1 {
        font-size: 28px;
    }

    .dashboard-btn {
        width: 80%;
        font-size: 16px;
    }
}
</style>
<body>
    <h1>Appointment List</h1>
    <table>
        <thead>
            <tr>
                <th>Appointment ID</th>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Appointment Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Assuming you are displaying the appointments in a table
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['appointment_id'] . "</td>";
                echo "<td>" . $row['patient_name'] . "</td>";
                echo "<td>" . $row['doctor_name'] . "</td>";
                echo "<td>" . $row['appointment_date'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>";
                
                // Action Links for Update and Delete
                echo "<a href='action.php?action=delete&id=" . $row['appointment_id'] . "' onclick='return confirm(\"Are you sure you want to delete this appointment?\")'>Delete</a>  ";
                
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <h1>Doctor Availability Management</h1>
    <table>
        <thead>
            <tr>
                <th>Doctor Name</th>
                <th>Available Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while ($row = $availabilityResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['doctor_name'] . "</td>";
                echo "<td>" . $row['available_date'] . "</td>";
                echo "<td>" . $row['start_time'] . "</td>";
                echo "<td>" . $row['end_time'] . "</td>";
                echo "<td>";
                echo "<a href='delete_availability.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="center-container">
        <button class="dashboard-btn"><a href="../admin_dashboard.php">Dashboard</a></button>
    </div>

    <?php $conn->close(); // Close the database connection ?>
</body>
</html>
