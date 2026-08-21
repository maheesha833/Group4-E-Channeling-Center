<?php
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$appointmentId = (int)($_POST['appointment_id'] ?? 0);
$cardName = trim($_POST['card_name'] ?? '');
$cardNumber = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
$cardExpiry = trim($_POST['card_expiry'] ?? '');
$cardCvv = trim($_POST['card_cvv'] ?? '');

$errors = [];

// --- Basic mock validation (this is NOT real payment security) ---
if ($cardName === '') $errors[] = "Name on card is required.";
if (strlen($cardNumber) !== 16) $errors[] = "Card number must be 16 digits.";
if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $cardExpiry)) $errors[] = "Expiry must be in MM/YY format.";
if (!preg_match('/^[0-9]{3}$/', $cardCvv)) $errors[] = "CVV must be 3 digits.";

$stmt = $pdo->prepare("SELECT * FROM appointments WHERE appointment_id = ?");
$stmt->execute([$appointmentId]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    $errors[] = "Appointment record not found.";
}

$pageTitle = "Payment Result";
$basePath = "";
include 'includes/header.php';

if (!empty($errors)) {
    echo '<div class="form-wrapper"><h2>Payment Failed</h2>';
    foreach ($errors as $err) {
        echo '<p class="error-msg">' . htmlspecialchars($err) . '</p>';
    }
    echo '<p><a href="payment.php?appointment_id=' . $appointmentId . '">&larr; Try again</a></p></div>';
    include 'includes/footer.php';
    exit;
}

// --- IMPORTANT: only the last 4 digits of the card are ever stored ---
$cardLast4 = substr($cardNumber, -4);

$update = $pdo->prepare("
    UPDATE appointments
    SET payment_status = 'Paid', card_last4 = ?
    WHERE appointment_id = ?
");
$update->execute([$cardLast4, $appointmentId]);
?>

<div class="form-wrapper">
    <h2>Payment Successful ✅</h2>
    <div class="summary-box">
        <p><strong>Appointment ID:</strong> #<?php echo $appointmentId; ?></p>
        <p><strong>Patient:</strong> <?php echo htmlspecialchars($appointment['patient_name']); ?></p>
        <p><strong>Appointment Date:</strong> <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
        <p><strong>Amount Paid:</strong> Rs. <?php echo number_format($appointment['amount_paid'], 2); ?></p>
        <p><strong>Card:</strong> **** **** **** <?php echo htmlspecialchars($cardLast4); ?></p>
    </div>
    <p class="success-msg">Your appointment has been confirmed. Please arrive 15 minutes early.</p>
    <p style="margin-top:14px;"><a href="index.php">&larr; Back to home</a></p>
</div>

<?php include 'includes/footer.php'; ?>
