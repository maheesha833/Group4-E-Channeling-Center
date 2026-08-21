<?php
require_once 'includes/db_connect.php';
$pageTitle = "Payment";
$basePath = "";

$appointmentId = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;

$stmt = $pdo->prepare("
    SELECT a.*, d.name AS doctor_name, d.specialization
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    WHERE a.appointment_id = ?
");
$stmt->execute([$appointmentId]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

include 'includes/header.php';

if (!$appointment) {
    echo '<div class="form-wrapper"><p class="error-msg">Appointment not found.</p><a href="index.php">&larr; Back to home</a></div>';
    include 'includes/footer.php';
    exit;
}

if ($appointment['payment_status'] === 'Paid') {
    echo '<div class="form-wrapper"><p class="success-msg">This appointment is already paid for.</p><a href="index.php">&larr; Back to home</a></div>';
    include 'includes/footer.php';
    exit;
}
?>

<div class="form-wrapper">
    <h2>Secure Payment</h2>
    <p class="subtitle">This is a mock payment gateway for demo purposes only. No real card data is processed or stored.</p>

    <div class="summary-box">
        <p><strong>Doctor:</strong> <?php echo htmlspecialchars($appointment['doctor_name']); ?></p>
        <p><strong>Patient:</strong> <?php echo htmlspecialchars($appointment['patient_name']); ?></p>
        <p><strong>Appointment Date:</strong> <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
        <p><strong>Amount Due:</strong> Rs. <?php echo number_format($appointment['amount_paid'], 2); ?></p>
    </div>

    <form action="process_payment.php" method="POST" id="paymentForm">
        <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">

        <div class="form-group">
            <label for="card_name">Name on Card</label>
            <input type="text" id="card_name" name="card_name" required placeholder="e.g. K. Perera">
        </div>

        <div class="form-group">
            <label for="card_number">Card Number</label>
            <input type="text" id="card_number" name="card_number" required maxlength="19" placeholder="1234 5678 9012 3456">
        </div>

        <div class="card-row">
            <div class="form-group">
                <label for="card_expiry">Expiry (MM/YY)</label>
                <input type="text" id="card_expiry" name="card_expiry" required maxlength="5" placeholder="MM/YY">
            </div>
            <div class="form-group">
                <label for="card_cvv">CVV</label>
                <input type="text" id="card_cvv" name="card_cvv" required maxlength="3" pattern="[0-9]{3}" placeholder="123">
            </div>
        </div>

        <button type="submit" class="btn-primary">Pay Rs. <?php echo number_format($appointment['amount_paid'], 2); ?></button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Client-side formatting + light validation for the mock card form.
const cardNumber = document.getElementById('card_number');
cardNumber.addEventListener('input', function () {
    // auto-space every 4 digits: 1234 5678 9012 3456
    let digits = this.value.replace(/\D/g, '').slice(0, 16);
    this.value = digits.replace(/(.{4})/g, '$1 ').trim();
});

const cardExpiry = document.getElementById('card_expiry');
cardExpiry.addEventListener('input', function () {
    let digits = this.value.replace(/\D/g, '').slice(0, 4);
    if (digits.length >= 3) {
        this.value = digits.slice(0, 2) + '/' + digits.slice(2);
    } else {
        this.value = digits;
    }
});

document.getElementById('paymentForm').addEventListener('submit', function (e) {
    const numDigits = cardNumber.value.replace(/\D/g, '');
    if (numDigits.length !== 16) {
        e.preventDefault();
        alert('Please enter a valid 16-digit card number.');
    }
});
</script>
