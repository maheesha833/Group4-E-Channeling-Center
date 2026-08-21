<?php
require_once 'includes/db_connect.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// --------------------------------------------------
// Collect and sanitize input
// --------------------------------------------------
$doctorId = (int)($_POST['doctor_id'] ?? 0);
$patientName = trim($_POST['patient_name'] ?? '');
$patientAge = (int)($_POST['patient_age'] ?? 0);
$patientContact = trim($_POST['patient_contact'] ?? '');
$appointmentDate = trim($_POST['appointment_date'] ?? '');

$errors = [];

// --------------------------------------------------
// Server-side validation
// --------------------------------------------------

// Validate doctor ID
if ($doctorId <= 0) {
    $errors[] = "Please select a valid doctor.";
}

// Validate patient name
if ($patientName === '' || strlen($patientName) < 2) {
    $errors[] = "Please enter a valid name.";
}

// Optional: prevent numbers-only names
if ($patientName !== '' && !preg_match("/^[a-zA-Z\s.'-]+$/", $patientName)) {
    $errors[] = "Patient name contains invalid characters.";
}

// Validate age
if ($patientAge <= 0 || $patientAge > 120) {
    $errors[] = "Please enter a valid age between 1 and 120.";
}

// Validate Sri Lankan-style 10-digit contact number
if (!preg_match('/^[0-9]{10}$/', $patientContact)) {
    $errors[] = "Please enter a valid 10-digit contact number.";
}

// --------------------------------------------------
// Validate appointment date
// --------------------------------------------------

$validDate = false;

if ($appointmentDate !== '') {

    $dateObject = DateTime::createFromFormat('Y-m-d', $appointmentDate);

    // Check if the date is exactly in YYYY-MM-DD format
    if (
        $dateObject &&
        $dateObject->format('Y-m-d') === $appointmentDate
    ) {
        $validDate = true;
    }
}

if (!$validDate) {
    $errors[] = "Please choose a valid appointment date.";
} else {

    // Get today's date
    $today = new DateTime(date('Y-m-d'));

    // Appointment date
    $selectedDate = new DateTime($appointmentDate);

    // Do not allow past dates
    if ($selectedDate < $today) {
        $errors[] = "Appointment date cannot be in the past.";
    }
}

// --------------------------------------------------
// Confirm doctor exists and get channeling fee
// --------------------------------------------------

$doctor = null;

if ($doctorId > 0) {

    try {

        $stmt = $pdo->prepare("
            SELECT doctor_id, doctor_name, specialization, channeling_fee
            FROM doctors
            WHERE doctor_id = ?
            LIMIT 1
        ");

        $stmt->execute([$doctorId]);

        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doctor) {
            $errors[] = "Selected doctor does not exist.";
        }

    } catch (PDOException $e) {

        $errors[] = "Unable to verify the selected doctor.";
    }
}

// --------------------------------------------------
// If there are validation errors
// --------------------------------------------------

if (!empty($errors)) {

    $pageTitle = "Booking Error";
    $basePath = "";

    include 'includes/header.php';

    echo '<div class="form-wrapper">';
    echo '<h2>Booking Error</h2>';

    foreach ($errors as $err) {
        echo '<p class="error-msg">'
            . htmlspecialchars($err, ENT_QUOTES, 'UTF-8')
            . '</p>';
    }

    echo '<p>';

    if ($doctorId > 0) {
        echo '<a href="booking.php?doctor_id='
            . htmlspecialchars((string)$doctorId, ENT_QUOTES, 'UTF-8')
            . '">&larr; Go back and try again</a>';
    } else {
        echo '<a href="index.php">&larr; Go back to home</a>';
    }

    echo '</p>';
    echo '</div>';

    include 'includes/footer.php';

    exit;
}

// --------------------------------------------------
// Insert appointment
// --------------------------------------------------

try {

    // Start database transaction
    $pdo->beginTransaction();

    /*
     * Appointment is created as Pending.
     * Payment will be completed through payment.php.
     */
    $insert = $pdo->prepare("
        INSERT INTO appointments
        (
            doctor_id,
            patient_name,
            patient_age,
            patient_contact,
            appointment_date,
            amount_paid,
            payment_status
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            'Pending'
        )
    ");

    $insert->execute([
        $doctorId,
        $patientName,
        $patientAge,
        $patientContact,
        $appointmentDate,
        $doctor['channeling_fee']
    ]);

    // Get newly created appointment ID
    $appointmentId = $pdo->lastInsertId();

    // Commit transaction
    $pdo->commit();

    // --------------------------------------------------
    // Redirect to payment page
    // --------------------------------------------------

    header(
        "Location: payment.php?appointment_id="
        . urlencode($appointmentId)
    );

    exit;

} catch (PDOException $e) {

    // Rollback if transaction is active
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Show database error page
    $pageTitle = "Booking Error";
    $basePath = "";

    include 'includes/header.php';

    echo '<div class="form-wrapper">';
    echo '<h2>Booking Error</h2>';
    echo '<p class="error-msg">
            Sorry, we could not create your appointment.
          </p>';
    echo '<p>
            Please try again. If the problem continues, contact the administrator.
          </p>';
    echo '<p>
            <a href="booking.php?doctor_id='
        . htmlspecialchars((string)$doctorId, ENT_QUOTES, 'UTF-8')
        . '">&larr; Go back and try again</a>
          </p>';
    echo '</div>';

    include 'includes/footer.php';

    exit;
}