<?php
session_start();

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Create an Account/login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "hospital_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch payment data
$sql = "SELECT * FROM payments"; // example query to fetch payment records
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Management</title>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #2c3e50;;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #2c3e50;;
            color: white;
            font-weight: bold;
        }

        td {
            background-color: #f9f9f9;
        }

        tr:hover td {
            background-color: #e9f5ff;
        }

        /* Button Styling */
        a {
            display: inline-block;
            padding: 8px 16px;
            ;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        a:hover {
            
          color: white;
        }

        .dashboard-btn {
           
            
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            margin-top: 30px;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }

        .dashboard-btn a {
            text-decoration: none;
            color: black;
            font-weight: bold;
        }

        .dashboard-btn:hover {
            background-color: #2c3e50;
            color: white;
        }
        .dashboard-btn a:hover {
            color: white;
        }
    </style>
</head>
<body>

    <h1>Payment Management</h1>

    <table>
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Patient Name</th>
                <th>Amount</th>
                <th>Payment Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['payment_id'] . "</td>";
                echo "<td>" . $row['patient_name'] . "</td>";
                echo "<td>" . $row['amount'] . "</td>";
                echo "<td>" . $row['payment_date'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>";
                echo "<a href='action.php?action=update_status&id=" . $row['payment_id'] . "&status=completed'>Mark as Completed</a> | ";
                echo "<a href='action.php?action=update_status&id=" . $row['payment_id'] . "&status=pending'>Mark as Pending</a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

   
        <button class="dashboard-btn"><a href="../admin_dashboard.php">Dashboard</a></button>
 

</body>
</html>

<?php $conn->close(); ?>
