<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$doctorId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// --- Handle the update submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $availableDays = trim($_POST['available_days'] ?? '');
    $availableTimes = trim($_POST['available_times'] ?? '');
    $fee = (float)($_POST['channeling_fee'] ?? 0);
    $id = (int)($_POST['doctor_id'] ?? 0);

    $update = $pdo->prepare("
        UPDATE doctors
        SET name = ?, specialization = ?, available_days = ?, available_times = ?, channeling_fee = ?
        WHERE doctor_id = ?
    ");
    $update->execute([$name, $specialization, $availableDays, $availableTimes, $fee, $id]);

    header('Location: dashboard.php?msg=' . urlencode('Doctor updated successfully.'));
    exit;
}

// --- Fetch the doctor to prefill the form ---
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE doctor_id = ?");
$stmt->execute([$doctorId]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

$pageTitle = "Edit Doctor";
$basePath = "../";
include '../includes/header.php';

if (!$doctor) {
    echo '<div class="admin-wrapper"><p class="error-msg">Doctor not found.</p><a href="dashboard.php">&larr; Back</a></div>';
    include '../includes/footer.php';
    exit;
}
?>

<div class="admin-wrapper">
    <div class="form-wrapper" style="max-width:100%;">
        <h2>Edit Doctor</h2>
        <form action="edit_doctor.php?id=<?php echo $doctor['doctor_id']; ?>" method="POST">
            <input type="hidden" name="doctor_id" value="<?php echo $doctor['doctor_id']; ?>">
            <div class="card-row">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($doctor['name']); ?>">
                </div>
                <div class="form-group">
                    <label>Specialization</label>
                    <input type="text" name="specialization" required value="<?php echo htmlspecialchars($doctor['specialization']); ?>">
                </div>
            </div>
            <div class="card-row">
                <div class="form-group">
                    <label>Available Days</label>
                    <input type="text" name="available_days" required value="<?php echo htmlspecialchars($doctor['available_days']); ?>">
                </div>
                <div class="form-group">
                    <label>Available Times</label>
                    <input type="text" name="available_times" required value="<?php echo htmlspecialchars($doctor['available_times']); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Channeling Fee (Rs.)</label>
                <input type="number" step="0.01" name="channeling_fee" required value="<?php echo $doctor['channeling_fee']; ?>">
            </div>
            <button type="submit" class="btn-primary">Save Changes</button>
        </form>
        <p style="margin-top:14px;"><a href="dashboard.php">&larr; Back to dashboard</a></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
