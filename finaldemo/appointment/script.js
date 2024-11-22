document.getElementById('date').addEventListener('change', function () {
    const doctorEmail = document.getElementById('doctor').value;
    const appointmentDate = this.value;

    fetch('fetch_slots.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ doctor_email: doctorEmail, appointment_date: appointmentDate })
    })
        .then(response => response.json())
        .then(data => {
            const slotsDiv = document.getElementById('time-slots');
            slotsDiv.innerHTML = '<label>Select Time Slot:</label>';

            if (data.length === 0) {
                slotsDiv.innerHTML += '<p>No available slots for this date.</p>';
                return;
            }

            data.forEach(slot => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = `${slot.start_time} - ${slot.end_time}`;
                button.onclick = () => selectSlot(slot.start_time);
                slotsDiv.appendChild(button);
            });

            function selectSlot(startTime) {
                document.getElementById('appointment-form').appendChild(
                    Object.assign(document.createElement('input'), {
                        type: 'hidden',
                        name: 'start_time',
                        value: startTime
                    })
                );
                document.querySelector('button[type="submit"]').disabled = false;
            }
        })
        .catch(console.error);
});
