document.addEventListener("DOMContentLoaded", function() {
    // Example: Fetch statistics for Overview
    async function fetchStats() {
        try {
            const response = await fetch('php/admin_functions.php?action=get_stats');
            const data = await response.json();
            document.getElementById('totalUsers').textContent = data.total_users;
            document.getElementById('pendingAppointments').textContent = data.pending_appointments;
            document.getElementById('outstandingPayments').textContent = data.outstanding_payments;
        } catch (error) {
            console.error('Error fetching stats:', error);
        }
    }

    fetchStats();
});
