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

// Get the logged-in doctor's email from the session
$email = $_SESSION['user_id'];

// Retrieve the doctor's name (used for the feedback page title and display)
$sql = "SELECT first_name, last_name FROM doctors WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
$doctor_name = $doctor['first_name'] . " " . $doctor['last_name'];

// Fetch approved feedback for the logged-in doctor
$sql = "SELECT * FROM feedback WHERE doctor_name = ? AND approved = 1 ORDER BY date_submitted DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $doctor_name);
$stmt->execute();
$result = $stmt->get_result();

// Function to convert rating to stars
function ratingToStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rating ? '★' : '☆';  // Filled or empty stars
    }
    return $stars;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback for <?php echo htmlspecialchars($doctor_name); ?></title>
    <style>
        /* Add your CSS styles here */
    </style>
</head>
<body>
    <h1>Feedback for <?php echo htmlspecialchars($doctor_name); ?></h1>

    <?php
    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Patient Name</th>
                    <th>Feedback Message</th>
                    <th>Rating</th>
                </tr>";
                while ($row = $result->fetch_assoc()) {
                    $rating = ratingToStars($row['rating']);
                    echo "<tr>
                            <td>{$row['patient_name']}</td>
                            <td>{$row['feedback_message']}</td>
                            <td>{$rating}</td>
                          </tr>";
                }
        echo "</table>";
    } else {
        echo "No feedback available for this doctor.";
    }

    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
