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

// Retrieve the slot_id from the URL
if (!isset($_GET['id'])) { // Make sure this matches the parameter in the URL
    die("Slot ID not provided");
}

$slot_id = $_GET['id'];

// Fetch the current details of the slot
$sql = "SELECT * FROM doctor_availability WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $slot_id);
$stmt->execute();
$availability = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$availability) {
    die("Slot not found");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission to update the slot
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $update_sql = "UPDATE doctor_availability SET available_date=?, start_time=?, end_time=? WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $date, $start_time, $end_time, $slot_id);
    $update_stmt->execute();
    $update_stmt->close();

    header("Location: availability.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Availability</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <div class="dashboard">
        <main class="content">
            <h1>Edit Availability Slot</h1>
            <form action="edit_availability.php?id=<?php echo $slot_id; ?>" method="POST">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" value="<?php echo $availability['available_date']; ?>" required>

                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" value="<?php echo $availability['start_time']; ?>" required>

                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" value="<?php echo $availability['end_time']; ?>" required>

                <button type="submit">Update Slot</button>
            </form>
        </main>
    </div>
</body>
</html>
