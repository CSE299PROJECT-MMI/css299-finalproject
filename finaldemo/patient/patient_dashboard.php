<?php
session_start();

// Check if the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../Create an Account/login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve patient details
$email = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, phone_number, birthday, gender FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

$name = $patient['first_name'] . ' ' . $patient['last_name'];
$profile_photo = 'default-photo.png'; // Default profile photo
$stmt->close();

// Query for the upcoming appointment
$upcoming_appointment_query = "
    SELECT doctor_name, appointment_date, start_time, department_name
    FROM appointments_patients
    WHERE email = ? AND appointment_date >= CURDATE()
    ORDER BY appointment_date ASC, start_time ASC
    LIMIT 1
";
$stmt = $conn->prepare($upcoming_appointment_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$upcoming_appointment = $result->fetch_assoc();
$stmt->close();


// Query for counseling history
$counseling_history_query = "
    SELECT appointment_date, start_time, end_time, doctor_name, department_name, patient_status
    FROM appointments_patients
    WHERE email = ? AND department_name LIKE '%Clinic%'
    ORDER BY appointment_date DESC, start_time DESC
";
$stmt = $conn->prepare($counseling_history_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all the rows for the patient's counseling history
$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
$stmt->close();

// Query for total completed sessions
$total_sessions_query = "
    SELECT COUNT(*) AS total_sessions
    FROM appointments_patients
    WHERE email = ? AND appointment_date < CURDATE()
";
$stmt = $conn->prepare($total_sessions_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$total_sessions_data = $result->fetch_assoc();
$total_sessions = $total_sessions_data['total_sessions'];
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="CSS\p.css">
</head>
<body>
    <div class="container">
        <!--aside section-->
        <aside>
            <div class="top">
                <div class="logo">
                    <h2>MS<span class="danger">CARE.</span></h2>
                </div>
                <div class="home">
                    <span class="material-symbols-outlined">home</span>
                </div>
            </div>
            <div class="sidebar">
                <a href="patient.php">
                <span class="material-symbols-outlined">grid_view</span>
                <h3>Dashboard</h3>
                </a>
                <a href="../appointment /index.php?email=<?php echo urlencode($_SESSION['user_id']); ?>" class="active">
                <span class="material-symbols-outlined">book_online</span>
                <h3>Appointments</h3>
                </a>
                <a href="#">
                    <span class="material-symbols-outlined">Medical_Information</span>
                    <h3>Report</h3>
                </a>
                <a href="Password.php">
                    <span class="material-symbols-outlined">person</span>
                    <h3>Password</h3>
                    </a>

                    <a href="feedback_form.php">
                    <span class="material-symbols-outlined">feedback</span>
                    <h3>Feedback</h3>
                </a>
                    <a href="../Create an Account/logout.php">
                        <span class="material-symbols-outlined">Logout</span>
                        <h3>Logout</h3>
                        </a>    

            </div>
            
        </aside>

        <!--main section-->
        <main>
            <h1>Dashboard</h1>
            

            <div class="insight">
                <div class="report">
                    <span class="material-symbols-outlined">Meeting_room</span>
                    <div class="middle">
                        <div class="left">
                        <h1><?php echo htmlspecialchars($total_sessions); ?></h1>
                            
                            <p>Session completed 1</p>
                        </div>
                        
                    </div>
                    <small>Last session 1 month  ago</small>
                </div>
                
                <div class="appointment">
    <span class="material-symbols-outlined">event</span> <!-- Calendar Icon -->
    <div class="middle">
    <div class="left">
    <h3>Upcoming Appointment</h3>
    <?php if ($upcoming_appointment): ?>
        <h1><?php echo htmlspecialchars($upcoming_appointment['doctor_name']); ?></h1> <!-- Doctor's Name -->
        <p>Date: <?php echo htmlspecialchars($upcoming_appointment['appointment_date']); ?></p> <!-- Appointment Date -->
        <p>Time: <?php echo htmlspecialchars($upcoming_appointment['start_time']); ?></p> <!-- Start Time -->
        <p>Department: <?php echo htmlspecialchars($upcoming_appointment['department_name']); ?></p> <!-- Department -->
    <?php else: ?>
        <p>No upcoming appointments found.</p>
    <?php endif; ?>
</div>

    </div>
    <small>Ensure you arrive on time!</small>
</div>

                
                <div class="health-tips">
                    <span class="material-symbols-outlined">local_hospital</span> <!-- Health icon -->
                    <div class="middle">
                        <div class="left">
                            <h3>Health Tips</h3>
                            <p>• Stay hydrated by drinking at least 8 glasses of water a day.</p>
                            <p>• Incorporate at least 30 minutes of physical activity into your daily routine.</p>
                            <p>• Monitor your blood sugar levels regularly if you are diabetic.</p>
                            <p>• Eat a balanced diet rich in fruits, vegetables, and whole grains.</p>
                        </div>
                    </div>
                    <small>Last updated: September 29, 2024</small>
                </div>
                
            </div>

            <!--History-->
            <div class="history">
    <h1>Counseling Appointment History</h1>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Doctor</th>
                <th>Department</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($appointments)): ?>
                <tr>
                    <td colspan="5">No counseling appointments found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                        <td>
                            <?php 
                                echo htmlspecialchars($appointment['start_time']);
                                if (!empty($appointment['end_time'])) {
                                    echo " - " . htmlspecialchars($appointment['end_time']);
                                }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['department_name']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['patient_status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>


                
            

            
        
            
        </main>

        <!--right section-->
        <right>
            
           <div class="top">
        
        
        <div class="theme-toggler">
            <span class="material-symbols-outlined" id="light-icon" style="display: block;">light_mode</span>
            <span class="material-symbols-outlined" id="dark-icon" style="display: none;">dark_mode</span>
        </div>
        
        <div class="profile">
    <div class="info">
        <p><?php echo htmlspecialchars($name); ?></p> <!-- Display the patient's name -->
        <p>Patient</p>
        <small class="text-muted"><?php echo htmlspecialchars($email); ?></small> <!-- Display Email -->
    </div>
    
</div>

    </div>
            <!--end top-->
            <div class="recent-updates">
                <h4>Recent Updates</h4>
                
                <ul>
                    <li><span class="material-symbols-outlined">notifications</span> Reminder: Next appointment in 9 days</li>
                    <li><span class="material-symbols-outlined">event</span> Appointment booked with Dr. Mark Davis</li>
                    <li><span class="material-symbols-outlined">message</span> <a href="chat.html">New message from Dr. Sarah Johnson</a></li>
                </ul>
            </div>
            
            <div class="progress-summary">
                <h4>Progress Summary</h4>
                <ul>
                    <li><span class="material-symbols-outlined">check_circle</span> Completed Sessions: 1/12</li>
                    <li><span class="material-symbols-outlined">payment</span> Payments Completed: 1/3</li>
                    <li><span class="material-symbols-outlined">hourglass_empty</span> Pending Tasks: 2 Payment</li>
                </ul>
            </div>
            
            <div class="shortcuts">
                <h4>Quick Links</h4>
                <ul>

                    <li><span class="material-symbols-outlined">schedule</span> Upcoming Appointments</li>
                    <li><span class="material-symbols-outlined">payment</span> Manage Payments</li>
                    <li><span class="material-symbols-outlined">tips_and_updates</span> Health Tips</li>
                </ul>
            </div>
            
            
        </right>
    </div>
    <script src="patient.js"></script>
    
</body>