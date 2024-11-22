<?php
session_start();

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../Create an Account/login.php");
    exit();
}
// Set the correct timezone to ensure accurate date and time
date_default_timezone_set('Asia/Dhaka'); // Replace 'Asia/Dhaka' with your timezone

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve doctor details, including profile picture
$email = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, phone_number, birthday, gender, profile_picture FROM doctors WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();

// Retrieve today's appointments from appointments table
$date = date('Y-m-d');  // Today's date
$appointments_sql = "
    SELECT a.patient_name, a.start_time, a.end_time, a.status
    FROM appointments a
    WHERE a.doctor_email = ? AND a.appointment_date = ?";

$appointments_stmt = $conn->prepare($appointments_sql);
$appointments_stmt->bind_param("ss", $email, $date);
$appointments_stmt->execute();
$appointments_result = $appointments_stmt->get_result();

// Initialize counters for appointment status
$total_due = 0;
$total_done = 0;

// Fetch and calculate the appointments
$appointments_list = [];
while ($row = $appointments_result->fetch_assoc()) {
    $appointments_list[] = $row;  // Store appointments in an array
    if ($row['status'] === 'done') {
        $total_done++;
    } else {
        $total_due++;
    }
}
$appointments_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="script.js"></script>
</head>
<body>
    <div class="dashboard">
        <!-- Main Content -->
        <main class="content">
            <h1>Welcome, <?php echo $doctor['first_name'] . " " . $doctor['last_name']; ?>!</h1>
            <section class="today-schedule">
                <h2>Today's Schedule</h2>
                <p><strong>Date:</strong> <?php echo $date; ?></p>
                <table>
                    <caption>Appointment Patient List</caption>
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Patient Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($appointments_list) > 0): ?>
                            <?php foreach ($appointments_list as $appointment): ?>
                                <tr>
                                    <td><?php echo $appointment['start_time']  ?></td>
                                    <td><?php echo $appointment['patient_name']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="2">No appointments for today.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <!-- Appointment Summary -->
            <section class="appointments-summary">
                <table border="1">
                    <caption>Appointments Summary</caption>
                    <thead>
                        <tr>
                            <th>Appointment Status</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Due</strong></td>
                            <td><?php echo $total_due; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Done</strong></td>
                            <td><?php echo $total_done; ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>

        <!-- Right Sidebar -->
        <aside class="sidebar right">
            <!-- Profile Section -->
            <div class="profile" onclick="window.location.href='../doctor/profile/doctor_profile.php'" style="cursor: pointer;">
                <?php 
                $profilePicturePath = '../doctor/img/' . $doctor['profile_picture'];
                if (!empty($doctor['profile_picture']) && file_exists($profilePicturePath)): 
                ?>
                    <img src="<?php echo $profilePicturePath; ?>" alt="Profile Picture">
                <?php else: ?>
                    <?php if ($doctor['gender'] === 'Male'): ?>
                        <img src="photo\male.png" alt="Male Default Picture">
                    <?php elseif ($doctor['gender'] === 'Female'): ?>
                        <img src="photo/female.png" alt="Female Default Picture">
                    <?php else: ?>
                        <img src="../doctor/img/default.png" alt="Default Picture"> 
                    <?php endif; ?>
                <?php endif; ?>
                <p><?php echo $doctor['first_name'] . " " . $doctor['last_name']; ?></p>
            </div>

            <!-- Navigation Links -->
            <nav>
                <ul>
                    <li><a href="../doctor/availability/availability.php">Availability</a></li>
                    <li><a href="../doctor/appointment/appointment.php">Appointments</a></li>
                </ul>
            </nav>

            <!-- Logout Button -->
            <button onclick="logout()" class="logout-button">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            
        
           
        </aside>
        

        
        

    <script>
        function logout() {
            window.location.href = '../Create an Account/logout.php';
        }
    </script>
</body>
</html>
