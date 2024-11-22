<?php
// Start session to access user info
session_start();

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hospital_management"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the doctor's name and department from the URL query string
$doctorName = isset($_GET['doctorName']) ? $_GET['doctorName'] : '';
$department = isset($_GET['department']) ? $_GET['department'] : '';

// Fetch available dates for the doctor from the database
$availableDates = [];
if (!empty($doctorName)) {
    // Query to fetch available dates for the selected doctor
    $sql = "SELECT available_date FROM doctor_availability WHERE doctor_name = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $doctorName);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $availableDates[] = $row['available_date'];
            }
        } else {
            echo "Error executing query: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Auto-fill email from the session (if logged in) or from URL if passed (e.g. index.php passed it)
$email = '';
if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email']; // Assuming email is stored in session
} elseif (isset($_GET['userEmail'])) {
    $email = $_GET['userEmail']; // If email is passed as a query parameter from index.php
}

$conn->close(); // Close the connection after the operation
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../bg01.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .booking-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            max-height: 500px;
            width: 100%;
            text-align: center;
            overflow-y: auto;
            animation: fadeIn 0.5s ease-in-out;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #444;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            color: #555;
            text-align: left;
        }

        input, select, button {
            width: calc(100% - 20px);
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s;
            padding: 12px;
            font-size: 16px;
        }

        button:hover {
            background-color: #218838;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <h1>Book Appointment</h1>

        <form action="submit_appointment.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" name="patient_email" class="form-input" placeholder="Your Email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>" required>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>" required>

            <label for="patient_status">Patient Status:</label>
            <select id="patient_status" name="patient_status" required>
                <option value="New" <?php echo (isset($_POST['patient_status']) && $_POST['patient_status'] === 'New') ? 'selected' : ''; ?>>New</option>
                <option value="Returning" <?php echo (isset($_POST['patient_status']) && $_POST['patient_status'] === 'Returning') ? 'selected' : ''; ?>>Returning</option>
            </select>

            <label for="doctor_name">Doctor:</label>
            <input type="text" name="doctor_name" class="form-input" value="<?php echo htmlspecialchars($doctorName); ?>" readonly>

            <label for="department_name">Department:</label>
            <input type="text" name="department" class="form-input" value="<?php echo htmlspecialchars($department); ?>" readonly>

            <label for="appointment_date">Date:</label>
            <select name="appointment_date" class="form-select" required>
                <option value="">Select Date</option>
                <?php
                // Display available dates for the doctor
                if (count($availableDates) > 0) {
                    foreach ($availableDates as $date) {
                        echo "<option value='$date'>$date</option>";
                    }
                } else {
                    echo "<option value='' disabled>No available dates</option>";
                }
                ?>
            </select>

            <label for="time_slot">Time Slot:</label>
            <select name="time_slot" class="form-select" required>
                <option value="">Select Time Slot</option>
            </select>

            <button type="submit" class="form-button">Confirm Appointment</button>
        </form>
    </div>

    <script>
        const dateSelect = document.querySelector('select[name="appointment_date"]');
const timeSelect = document.querySelector('select[name="time_slot"]');
const messageDiv = document.createElement('div'); // Create a message container
messageDiv.style.color = 'red';
messageDiv.style.marginTop = '10px';
timeSelect.parentNode.insertBefore(messageDiv, timeSelect.nextSibling); // Add it after the time slot dropdown

dateSelect.addEventListener('change', function () {
    const selectedDate = dateSelect.value;
    const doctorName = "<?php echo $doctorName; ?>"; // PHP variable injected into JS

    if (selectedDate) {
        fetch(`get_slots.php?date=${selectedDate}&doctorName=${doctorName}`)
            .then(response => response.json())
            .then(data => {
                timeSelect.innerHTML = "<option value=''>Select Time Slot</option>";
                messageDiv.textContent = ""; // Clear previous message

                if (data.timeSlots && data.timeSlots.length > 0) {
                    // Populate time slots
                    data.timeSlots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot;
                        option.textContent = slot;
                        timeSelect.appendChild(option);
                    });
                } else if (data.message) {
                    // Show message if no slots are available
                    messageDiv.textContent = data.message;
                }
            })
            .catch(error => {
                console.error('Error fetching time slots:', error);
                messageDiv.textContent = 'Error fetching time slots. Please try again.';
            });
    }
});

    </script>
</body>
</html>
