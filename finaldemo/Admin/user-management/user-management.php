<?php 
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Database connection
$host = '127.0.0.1';
$dbname = 'hospital_management';
$username = 'root';
$password = ''; // Change this if needed

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch data for doctors
$doctors = $pdo->query("SELECT * FROM doctors")->fetchAll(PDO::FETCH_ASSOC);

// Fetch data for patients
$patients = $pdo->query("
    SELECT 
        users.id AS user_id, 
        users.first_name AS patient_first_name, 
        users.last_name AS patient_last_name, 
        users.email, 
        users.phone_number 
    FROM users
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 80%; margin: auto; overflow: hidden; text-align: center;}
        h1 { text-align: center; color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        table th { background-color: #2c3e50; color: white; }
        .btn { padding: 5px 10px; text-decoration: none; color: #fff; background: #333; border: none; border-radius: 3px; }
        .btn:hover { background: #555; }

        body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f4f4f4; /* Optional background color */
    height: 100vh; /* Full viewport height */
    display: flex;
    justify-content: center;
    align-items: center;
}


.dashboard-btn {
    
    
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.dashboard-btn a {
    text-decoration: none;
    color: black;
    font-weight: bold;
}

.dashboard-btn:hover {
    background-color: #2c3e50;
    color: white; /* Slightly darker green */
}

.dashboard-btn a:hover {
 
    color: white;

}


    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>

        <!-- Doctors Section -->
        <h2>Doctors</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
            <?php if ($doctors): ?>
                <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td><?= htmlspecialchars($doctor['id']) ?></td>
                        <td><?= htmlspecialchars($doctor['first_name']) ?></td>
                        <td><?= htmlspecialchars($doctor['last_name']) ?></td>
                        <td><?= htmlspecialchars($doctor['email']) ?></td>
                        <td><?= htmlspecialchars($doctor['department']) ?></td>
                        <td>
                            <a href="delete_doctor.php?id=<?= htmlspecialchars($doctor['id']) ?>" class="btn" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No doctors found.</td>
                </tr>
            <?php endif; ?>
        </table>

        <!-- Patients Section -->
        <h2>Patients</h2>
        <table>
            <tr>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
            <?php if ($patients): ?>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= htmlspecialchars($patient['user_id']) ?></td>
                        <td><?= htmlspecialchars($patient['patient_first_name']) ?></td>
                        <td><?= htmlspecialchars($patient['patient_last_name']) ?></td>
                        <td><?= htmlspecialchars($patient['email']) ?></td>
                        <td><?= htmlspecialchars($patient['phone_number']) ?></td>
                        <td>
                            <a href="delete_patient.php?id=<?= htmlspecialchars($patient['user_id']) ?>" class="btn" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No patients found.</td>
                </tr>
            <?php endif; ?>
        </table>
        
    <button class="dashboard-btn"><a href="../admin_dashboard.php">Dashboard</a></button>
        


    </div>
</body>
</html>
