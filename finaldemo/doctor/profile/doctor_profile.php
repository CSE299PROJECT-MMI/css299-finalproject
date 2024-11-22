<?php
// Start session
session_start();

// Redirect if not logged in or not a doctor
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
$email = $_SESSION['user_id']; // Assuming email is stored in session
$sql = "
SELECT 
    d.email, 
    d.first_name, 
    d.last_name, 
    d.phone_number, 
    d.birthday, 
    d.gender, 
    de.profile_picture, 
    de.school, 
    de.college, 
    de.medical_college, 
    de.other_degrees, 
    de.father_name, 
    de.mother_name, 
    de.address, 
    de.specialties
FROM 
    doctors d
LEFT JOIN 
    doctor_edu de ON d.id = de.user_id
WHERE 
    d.email = ?;
";

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Assign defaults for missing fields
$profilePicture = $doctor['profile_picture'] ?? ($doctor['gender'] === 'Male' ? 'Male.png' : 'Female.png');
$fullName = "Dr. " . ($doctor['first_name'] ?? "N/A") . " " . ($doctor['last_name'] ?? "N/A");
$imagePath = "../doctor/img/" . htmlspecialchars($profilePicture);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="profile-container">
        <h1>Welcome to Doctor Profile</h1>
        <div class="profile">
            <div class="left-side">
                <?php
                // Display the profile picture
                if (file_exists($imagePath)) {
                    echo "<img src='$imagePath' alt='Profile Picture' style='width:150px;height:150px;border-radius:50%;'>";
                } else {
                    echo "<p>Image not found. Check file upload or path.</p>";
                    echo "<p>Expected Path: $imagePath</p>"; // Debugging output
                }
                ?>
                <h2><?php echo htmlspecialchars($fullName); ?></h2>
                <p><strong>Specialties:</strong> <?php echo htmlspecialchars($doctor['specialties'] ?? "Not provided"); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($doctor['email'] ?? "N/A"); ?></p>
                <p><strong>Educations:</strong></p>
                <p><?php echo htmlspecialchars($doctor['school'] ?? "Not provided"); ?></p>
                <p><?php echo htmlspecialchars($doctor['college'] ?? "Not provided"); ?></p>
                <p><?php echo htmlspecialchars($doctor['medical_college'] ?? "Not provided"); ?></p>
                <p><strong>Degrees:</strong> <?php echo htmlspecialchars($doctor['other_degrees'] ?? "N/A"); ?></p>
            </div>
            <div class="right-side">
                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($doctor['phone_number'] ?? "N/A"); ?></p>
                <p><strong>Birthday:</strong> <?php echo htmlspecialchars($doctor['birthday'] ?? "N/A"); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($doctor['gender'] ?? "N/A"); ?></p>
                <p><strong>Father's Name:</strong> <?php echo htmlspecialchars($doctor['father_name'] ?? "N/A"); ?></p>
                <p><strong>Mother's Name:</strong> <?php echo htmlspecialchars($doctor['mother_name'] ?? "N/A"); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($doctor['address'] ?? "N/A"); ?></p>
            </div>
        </div>
        <div class="button_container">
            <button onclick="window.location.href='edit_profile.php'">Edit Profile</button>
            <button onclick="window.location.href='../doctor_dashboard.php'">Dashboard</button>
            <button onclick="window.location.href='../password_change/change_password.php'">Password Change</button>
        </div>
    </div>
</body>
</html>
