<?php
require_once '../php/database.php';

if ($_GET['action'] === 'get_stats') {
    // Fetch statistics data
    $stats = [
        'total_users' => getTotalUsers(),
        'pending_appointments' => getPendingAppointments(),
        'outstanding_payments' => getOutstandingPayments(),
    ];
    echo json_encode($stats);
}

function getTotalUsers() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as total FROM users");
    return $result->fetch_assoc()['total'];
}

function getPendingAppointments() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE status='due'");
    return $result->fetch_assoc()['total'];
}

function getOutstandingPayments() {
    global $conn;
    $result = $conn->query("SELECT SUM(amount) as total FROM payments WHERE status='pending'");
    return $result->fetch_assoc()['total'];
}
?>
