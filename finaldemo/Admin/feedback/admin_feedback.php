<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch feedback data
$sql = "SELECT * FROM feedback ORDER BY date_submitted DESC";
$result = $conn->query($sql);

// Function to convert rating to stars
function ratingToStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rating ? '★' : '☆';  // Filled star or empty star
    }
    return $stars;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback List</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f4f4f4;
            color: #333;
        }
        h1 {
            color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        a {
            text-decoration: none;
        }
       
        .back-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px 20px;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .back-button:hover {
            background-color: #2c3e50;
            color: white;
        }
    </style>
</head>
<body>

<h1>Feedback List</h1>

<?php
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Feedback ID</th>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Feedback Message</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        $rating = ratingToStars($row['rating']); // Convert rating to stars
        echo "<tr>
                <td>{$row['feedback_id']}</td>
                <td>{$row['patient_name']}</td>
                <td>{$row['doctor_name']}</td>
                <td>{$row['feedback_message']}</td>
                <td>{$rating}</td>
                <td>" . ($row['approved'] ? 'Approved' : 'Pending') . "</td>
                <td>
                    <a href='delete_feedback.php?feedback_id={$row['feedback_id']}' 
                       onclick='return confirm(\"Are you sure you want to delete this feedback?\");'>Delete</a>
                </td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No feedback available.</p>";
}

$conn->close();
?>

<a href="../admin_dashboard.php" class="back-button">Back to Admin Dashboard</a>

</body>
</html>
