<?php
require_once 'includes/db_connect.php';
$pageTitle = "Book Appointment";
$basePath = "";

// --- Get and validate the doctor_id from the URL ---
$doctorId = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM doctors WHERE doctor_id = ?");
$stmt->execute([$doctorId]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    include 'includes/header.php';
    echo '<div class="form-wrapper"><p class="error-msg">Doctor not found.</p><a href="index.php">&larr; Back to doctor list</a></div>';
    include 'includes/footer.php';
    exit;
}

$errors = [];

include 'includes/header.php';
?>

<div class="form-wrapper">
    <h2>Book Appointment</h2>
    <p class="subtitle">with <?php echo htmlspecialchars($doctor['name']); ?> (<?php echo htmlspecialchars($doctor['specialization']); ?>)</p>

    <div class="summary-box">
        <p><strong>Available Days:</strong> <?php echo htmlspecialchars($doctor['available_days']); ?></p>
        <p><strong>Available Time:</strong> <?php echo htmlspecialchars($doctor['available_times']); ?></p>
        <p><strong>Channeling Fee:</strong> Rs. <?php echo number_format($doctor['channeling_fee'], 2); ?></p>
    </div>

    <form action="process_booking.php" method="POST" id="bookingForm">
        <input type="hidden" name="doctor_id" value="<?php echo $doctor['doctor_id']; ?>">

        <div class="form-group">
            <label for="patient_name">Full Name</label>
            <input type="text" id="patient_name" name="patient_name" required placeholder="e.g. Kasun Perera">
        </div>

        <div class="form-group">
            <label for="patient_age">Age</label>
            <input type="number" id="patient_age" name="patient_age" required min="0" max="120" placeholder="e.g. 30">
        </div>

        <div class="form-group">
            <label for="patient_contact">Contact Number</label>
            <input type="tel" id="patient_contact" name="patient_contact" required pattern="[0-9]{10}" placeholder="07XXXXXXXX">
        </div>

        <div class="form-group">
            <label for="appointment_date">Preferred Appointment Date</label>
            <input type="date" id="appointment_date" name="appointment_date" required min="<?php echo date('Y-m-d'); ?>">
        </div>

        <button type="submit" class="btn-primary">Proceed to Payment</button>
    </form>

    <p style="margin-top:14px;"><a href="index.php">&larr; Back to doctor list</a></p>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Simple client-side check: contact number must look valid before submit.
// (Server-side validation in process_booking.php is the real safety net.)
document.getElementById('bookingForm').addEventListener('submit', function (e) {
    const contact = document.getElementById('patient_contact').value.trim();
    if (!/^[0-9]{10}$/.test(contact)) {
        e.preventDefault();
        alert('Please enter a valid 10-digit contact number.');
    }
});
</script>
