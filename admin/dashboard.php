<?php
session_start();
require_once '../includes/db_connect.php';

// --- Route guard: only logged-in admins may view this page ---
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = "Manage Doctors";
$basePath = "../";

$doctors = $pdo->query("SELECT * FROM doctors ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="admin-wrapper">
    <div class="admin-header">
        <h2>Doctor Roster</h2>
        <span>Logged in as <?php echo htmlspecialchars($_SESSION['admin_username']); ?> | <a href="logout.php">Logout</a></span>
    </div>

    <div class="admin-tabs">
        <a href="dashboard.php" class="active">Manage Doctors</a>
        <a href="appointments.php">View Appointments</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <p class="success-msg"><?php echo htmlspecialchars($_GET['msg']); ?></p>
    <?php endif; ?>

    <!-- ==================== ADD NEW DOCTOR FORM ==================== -->
    <div class="form-wrapper" style="max-width:100%; margin: 0 0 30px;">
        <h2>Add New Doctor</h2>
        <form action="add_doctor.php" method="POST">
            <div class="card-row">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required placeholder="Dr. Full Name">
                </div>
                <div class="form-group">
                    <label>Specialization</label>
                    <input type="text" name="specialization" required placeholder="e.g. Cardiologist">
                </div>
            </div>
            <div class="card-row">
                <div class="form-group">
                    <label>Available Days</label>
                    <input type="text" name="available_days" required placeholder="e.g. Monday, Wednesday">
                </div>
                <div class="form-group">
                    <label>Available Times</label>
                    <input type="text" name="available_times" required placeholder="e.g. 5.00 PM - 8.00 PM">
                </div>
            </div>
            <div class="form-group">
                <label>Channeling Fee (Rs.)</label>
                <input type="number" step="0.01" name="channeling_fee" required placeholder="e.g. 2000">
            </div>
            <button type="submit" class="btn-primary">Add Doctor</button>
        </form>
    </div>

    <!-- ==================== DOCTOR LIST ==================== -->
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Specialization</th>
                <th>Days</th>
                <th>Time</th>
                <th>Fee (Rs.)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($doctors) === 0): ?>
                <tr><td colspan="6">No doctors added yet.</td></tr>
            <?php else: ?>
                <?php foreach ($doctors as $doc): ?>
                <tr>
                    <td><?php echo htmlspecialchars($doc['name']); ?></td>
                    <td><?php echo htmlspecialchars($doc['specialization']); ?></td>
                    <td><?php echo htmlspecialchars($doc['available_days']); ?></td>
                    <td><?php echo htmlspecialchars($doc['available_times']); ?></td>
                    <td><?php echo number_format($doc['channeling_fee'], 2); ?></td>
                    <td class="action-links">
                        <a class="edit-link" href="edit_doctor.php?id=<?php echo $doc['doctor_id']; ?>">Edit</a>
                        <a class="delete-link" href="delete_doctor.php?id=<?php echo $doc['doctor_id']; ?>"
                           onclick="return confirm('Delete this doctor? This also removes their appointments.');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
