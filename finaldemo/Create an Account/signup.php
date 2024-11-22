<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone_number = trim($_POST['phone_number']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $role = $_POST['role'];
    $department = isset($_POST['department']) ? $_POST['department'] : null;

    // Check if email exists in users, doctors, or admins tables
    $checkEmail = "SELECT email FROM users WHERE email = ? 
                   UNION 
                   SELECT email FROM doctors WHERE email = ? 
                   UNION 
                   SELECT email FROM admins WHERE email = ?";
    $stmt = $conn->prepare($checkEmail);
    $stmt->bind_param("sss", $email, $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errorMessage = "An account with this email already exists.";
    } else {
        // Insert based on role
        if ($role === "doctor") {
            $sqlDoctor = "INSERT INTO doctors (first_name, last_name, phone_number, email, password, birthday, gender, department) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtDoctor = $conn->prepare($sqlDoctor);
            $stmtDoctor->bind_param("ssssssss", $first_name, $last_name, $phone_number, $email, $password, $birthday, $gender, $department);

            if ($stmtDoctor->execute()) {
                $_SESSION['user_id'] = $email;
                $_SESSION['role'] = 'doctor';
                // Redirect to login page
                header("Location: login.php");
                exit();
            } else {
                $errorMessage = "Error creating doctor account: " . $stmtDoctor->error;
            }
        } elseif ($role === "admin") {
            $sqlAdmin = "INSERT INTO admins (first_name, last_name, email, password) 
                         VALUES (?, ?, ?, ?)";
            $stmtAdmin = $conn->prepare($sqlAdmin);
            $stmtAdmin->bind_param("ssss", $first_name, $last_name, $email, $password);

            if ($stmtAdmin->execute()) {
                $_SESSION['user_id'] = $email;
                $_SESSION['role'] = 'admin';
                // Redirect to login page
                header("Location: login.php");
                exit();
            } else {
                $errorMessage = "Error creating admin account: " . $stmtAdmin->error;
            }
        } else {
            $sqlUser = "INSERT INTO users (first_name, last_name, phone_number, email, password, birthday, gender, role) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtUser = $conn->prepare($sqlUser);
            $stmtUser->bind_param("ssssssss", $first_name, $last_name, $phone_number, $email, $password, $birthday, $gender, $role);

            if ($stmtUser->execute()) {
                $_SESSION['user_id'] = $email;
                $_SESSION['role'] = $role;
                // Redirect to login page
                header("Location: login.php");
                exit();
            } else {
                $errorMessage = "Error creating user account: " . $stmtUser->error;
            }
        }
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Account</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="form-container">
        <h2>Create an Account</h2>
        <?php if (!empty($errorMessage)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>
        <form method="post" action="">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="birthday">Birthday:</label>
            <input type="date" id="birthday" name="birthday" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

           <!-- No role selection here, we set the role to 'patient' by default -->
    <input type="hidden" name="role" value="patient">

            <div id="department-field" style="display: none;">
                <label for="department">Department:</label>
                <select id="department" name="department">
                    <option value="" disabled selected>Select Department</option>
                    <option value="Psychosexual Disorder Clinic">Psychosexual Disorder Clinic</option>
                    <option value="Addiction Clinic">Addiction Clinic</option>
                    <option value="Psychotherapy Clinic">Psychotherapy Clinic</option>
                    <option value="Depression Clinic">Depression Clinic</option>
                    <option value="Sex Therapy Clinic">Sex Therapy Clinic</option>
                    <option value="Drug Addiction Clinic">Drug Addiction Clinic</option>
                    <option value="Headache Clinic">Headache Clinic</option>
                    <option value="OCD Clinic">OCD Clinic</option>
                    <option value="Geriatric Clinic">Geriatric Clinic</option>
                    <option value="Neurotic Mental Health Clinic">Neurotic Mental Health Clinic</option>
                </select>
            </div>

            <button type="submit">Create Account</button>
        </form>
    </div>

    <script>
        function toggleDepartmentField() {
            const role = document.getElementById("role").value;
            const departmentField = document.getElementById("department-field");
            departmentField.style.display = role === "doctor" ? "block" : "none";
        }
    </script>
</body>
</html>
