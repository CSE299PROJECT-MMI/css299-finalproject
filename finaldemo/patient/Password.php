<?php
$servername = "localhost";  // Use string literals for servername
$username = "root";         // Default username for XAMPP MySQL
$password = "";             // Default password for XAMPP MySQL
$dbname = "hospital_management"; // The actual name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start the session
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if new passwords match
    if ($new_password !== $confirm_password) {
        echo "New passwords do not match.";
        exit;
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo "User is not logged in.";
        exit;
    }

    $user_email = $_SESSION['user_id'];  // Assuming the session stores the user's email

    // Query to get the stored password for the logged-in user based on email
    $query = "SELECT password FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $user_email);  // Use the email from session
        $stmt->execute();
        $stmt->bind_result($stored_hashed_password);
        
        // Check if user exists and fetch the stored password
        if ($stmt->fetch()) {
            // Verify the old password
            if (!password_verify($old_password, $stored_hashed_password)) {
                echo "Old password is incorrect.";
                $stmt->close(); // Close the statement here
                exit;
            }

            // Hash the new password
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Close the SELECT statement before executing the UPDATE statement
            $stmt->close();

            // Update the password in the database
            $update_query = "UPDATE users SET password = ? WHERE email = ?";
            if ($update_stmt = $conn->prepare($update_query)) {
                $update_stmt->bind_param("ss", $hashed_new_password, $user_email);  // Update using email
                $update_stmt->execute();
                echo "Password changed successfully.";
                $update_stmt->close();
            } else {
                echo "Error updating password.";
            }
        } else {
            echo "User not found in the database.";
        }

    } else {
        echo "Error executing query.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<style>
    /* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display:flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Form Container */
.form-container {
    width: 100%;
    max-width: 400px;
    margin: 50px auto;
    background-color: white;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

/* Form Heading */
h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

/* Form Elements */
label {
    display: block;
    font-size: 14px;
    color: #555;
    margin-bottom: 8px;
}

input[type="password"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    color: #333;
}

input[type="password"]:focus {
    border-color: #007bff;
    outline: none;
}

/* Button Styles */
button[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

/* Error and Success Messages */
.error, .success {
    color: #ff0000;
    font-size: 14px;
    text-align: center;
}

.success {
    color: #28a745;
}

* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

/* Form Container */
.form-container {
    width: 100%;
    max-width: 400px;
    margin: 50px auto;
    background-color: white;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

/* Form Heading */
h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

/* Form Elements */
label {
    display: block;
    font-size: 14px;
    color: #555;
    margin-bottom: 8px;
}

input[type="password"] {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    color: #333;
}

input[type="password"]:focus {
    border-color: #007bff;
    outline: none;
}

/* Button Styles */
button[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

/* Error and Success Messages */
.error, .success {
    color: #ff0000;
    font-size: 14px;
    text-align: center;
}

.success {
    color: #28a745;
}
.btn {
    background-color: #007bff; /* Green background */
            border: none; /* No border */
            color: white; /* White text */
            padding: 15px 32px; /* Padding inside the button */
            text-align: center; /* Center the text */
            text-decoration: none; /* Remove underline */
            display: inline-block; /* Allow the button to sit inline with other elements */
            font-size: 16px; /* Font size */
            margin: 4px 2px; /* Margin around the button */
            cursor: pointer; /* Show a pointer cursor on hover */
            border-radius: 8px; /* Rounded corners */
            width: 100%;
        }

        /* Style the anchor link inside the button */
        .btn a {
            color: white; /* Make the link text white */
            text-decoration: none; /* Remove underline */
        }

        /* Style the button on hover */
        .btn:hover {
            background-color: #0056b3; /* Darker green when hovered */
        }
.s{
    display: flex;
    gap: 1rem;
    
}
</style>
<!-- HTML Form for password change -->
<form method="POST">
    <label for="old_password">Old Password:</label>
    <input type="password" id="old_password" name="old_password" required><br><br>

    <label for="new_password">New Password:</label>
    <input type="password" id="new_password" name="new_password" required><br><br>
        
    <label for="confirm_password">Confirm New Password:</label>
    <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <div class="s">
        <button type="submit" name="submit" class="btn">Change Password</button>
        <button class="btn" ><a href="patient_dashboard.php">Patient Dashboard </a></button>
        </div>
    
</form>
</body>
</html>
