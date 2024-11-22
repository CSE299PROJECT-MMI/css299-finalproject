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

// Retrieve doctor details
$email = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, phone_number, birthday, gender FROM doctors WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();

// Retrieve doctor's availability slots from doctor_availability table
$availability_sql = "SELECT id, available_date, start_time, end_time FROM doctor_availability WHERE doctor_email=?";
$availability_stmt = $conn->prepare($availability_sql);
$availability_stmt->bind_param("s", $email);
$availability_stmt->execute();
$availability_result = $availability_stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Availability</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="script.js"></script>
</head>
<body>
    <div class="dashboard">
        <!-- Main Content -->
        <main class="content">
            <h1>Manage Your Availability</h1>

            <!-- Add New Availability Slot -->
            <section class="add-availability">
                <h2>Add New Availability Slot</h2>
                <form action="add_availability.php" method="POST">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>

                    <label for="start_time">Start Time:</label>
                    <input type="time" id="start_time" name="start_time" required>

                    <label for="end_time">End Time:</label>
                    <input type="time" id="end_time" name="end_time" required>

                    <button type="submit">Add Slot</button>
                </form>
            </section>

            <!-- View Existing Availability Slots -->
            <section class="current-availability">
                <h2>Your Current Availability</h2>
                <table>
                    <caption>Existing Availability Slots</caption>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($availability = $availability_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $availability['available_date']; ?></td>
                                <td><?php echo $availability['start_time']; ?></td>
                                <td><?php echo $availability['end_time']; ?></td>
                                <td>
                                    <a href="edit_availability.php?id=<?php echo $availability['id']; ?>">Edit</a> | 
                                    <a href="delete_availability.php?id=<?php echo $availability['id']; ?>" onclick="return confirm('Are you sure you want to delete this slot?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

        </main>

        <aside class="sidebar right">
            <!-- Profile Section -->
            <div class="profile" onclick="window.location.href='../profile/doctor_profile.php';" style="cursor: pointer;">
                <?php 
                // Displaying the profile image based on gender with a fallback to default
                if (isset($doctor['gender'])) {
                    $profileImage = ($doctor['gender'] === 'Male') ? '../photo/male.png' : '../photo/female.png';
                } else {
                    $profileImage = '../doctor/img/default.png';
                }
                ?>
                <img src="<?php echo $profileImage; ?>" alt="Profile Picture">

                <p><?php echo isset($doctor['first_name']) && isset($doctor['last_name']) ? $doctor['first_name'] . " " . $doctor['last_name'] : "Doctor"; ?></p>
            </div>

            <!-- Navigation Links -->
            <nav>
                <ul>
                    <li><a href="../doctor_dashboard.php">Dashboard</a></li>
                    <li><a href="../appointment/appointment.php">Appointments</a></li>
                </ul>
            </nav>
        </aside>

    </div>
</body>
</html>
