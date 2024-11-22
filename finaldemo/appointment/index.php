<?php
session_start();

// Check if the user is logged in and has the role of 'patient'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: ../Create an Account/login.php");
    exit();
}

// Get the email from the session or query string
$email = isset($_GET['email']) ? $_GET['email'] : $_SESSION['user_id'];

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch doctors' data
$sql = "SELECT first_name, last_name, department FROM doctors";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Schedule</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        
        .schedule-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        h1 {
            text-align: center;
            color: #333;
        }
        
        /* Search Bar */
        .search-bar {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        /* Table Styling */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .schedule-table th, .schedule-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .schedule-table th {
            background-color: #007bff;
            color: white;
        }
        
        /* Button Styling */
        .book-button {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .book-button:hover {
            background-color: #218838;
        }

        /* Email Display */
        .email-display {
            margin-top: 20px;
            font-size: 16px;
            color: #333;
        }

            a{
                text-decoration: none;
                color: #333;
                font-weight: bold;
                margin-top: 1rem;
                
            }

            .khan{
                margin-top: 1rem;
            }

            .khan:hover{
                background-color: #218838;
                padding: 10px;
                color:white;
            }
            
    </style>
</head>
<body>
    <div class="schedule-container">
        <h1>Doctor Schedule</h1>

        <!-- Display User's Email -->
        <div class="email-display">
            <strong>Your Email:</strong> <?php echo htmlspecialchars($email); ?>
        </div>

        <!-- Search Bar -->
        <input type="text" id="searchInput" class="search-bar" placeholder="Search by doctor name or department...">

        <table class="schedule-table">
            <thead>
                <tr>
                    <th>Doctor Name</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="scheduleTableBody">
                <?php
                // Check if there are results
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        $doctorName = $row['first_name'] . ' ' . $row['last_name'];
                        $department = $row['department'];
                        echo "<tr>";
                        echo "<td>$doctorName</td>";
                        echo "<td>$department</td>";
                        echo "<td><a href='book_appointment.php?doctorName=$doctorName&department=$department&userEmail=$email' class='book-button'>Book Appointment</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No doctors available.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a class="khan" href="../patient/patient_dashboard.php">Back to patient dashboard</a>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function () {
            let filter = document.getElementById('searchInput').value.toUpperCase();
            let table = document.getElementById('scheduleTableBody');
            let rows = table.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                let cells = rows[i].getElementsByTagName('td');
                let doctorName = cells[0].textContent || cells[0].innerText;
                let department = cells[1].textContent || cells[1].innerText;

                if (doctorName.toUpperCase().includes(filter) || department.toUpperCase().includes(filter)) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        });
    </script>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
