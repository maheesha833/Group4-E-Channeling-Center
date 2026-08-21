<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = "Appointments";
$basePath = "../";

// Optional filter by payment status via ?status=Paid etc.
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';

$sql = "
    SELECT a.*, d.name AS doctor_name, d.specialization
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
";
$params = [];
if ($statusFilter !== '') {
    $sql .= " WHERE a.payment_status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY a.booked_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="admin-wrapper">
    <div class="admin-header">
        <h2>Appointments</h2>
        <span>Logged in as <?php echo htmlspecialchars($_SESSION['admin_username']); ?> | <a href="logout.php">Logout</a></span>
    </div>

    <div class="admin-tabs">
        <a href="dashboard.php">Manage Doctors</a>
        <a href="appointments.php" class="active">View Appointments</a>
    </div>

    <div class="admin-tabs">
        <a href="appointments.php" class="<?php echo $statusFilter === '' ? 'active' : ''; ?>">All</a>
        <a href="appointments.php?status=Paid" class="<?php echo $statusFilter === 'Paid' ? 'active' : ''; ?>">Paid</a>
        <a href="appointments.php?status=Pending" class="<?php echo $statusFilter === 'Pending' ? 'active' : ''; ?>">Pending</a>
        <a href="appointments.php?status=Cancelled" class="<?php echo $statusFilter === 'Cancelled' ? 'active' : ''; ?>">Cancelled</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Age</th>
                <th>Contact</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Amount (Rs.)</th>
                <th>Status</th>
                <th>Booked At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($appointments) === 0): ?>
                <tr><td colspan="9">No appointments found.</td></tr>
            <?php else: ?>
                <?php foreach ($appointments as $a): ?>
                <tr>
                    <td>#<?php echo $a['appointment_id']; ?></td>
                    <td><?php echo htmlspecialchars($a['patient_name']); ?></td>
                    <td><?php echo (int)$a['patient_age']; ?></td>
                    <td><?php echo htmlspecialchars($a['patient_contact']); ?></td>
                    <td><?php echo htmlspecialchars($a['doctor_name']); ?> (<?php echo htmlspecialchars($a['specialization']); ?>)</td>
                    <td><?php echo htmlspecialchars($a['appointment_date']); ?></td>
                    <td><?php echo number_format($a['amount_paid'], 2); ?></td>
                    <td>
                        <?php
                        $badgeClass = 'badge-pending';
                        if ($a['payment_status'] === 'Paid') $badgeClass = 'badge-paid';
                        if ($a['payment_status'] === 'Cancelled') $badgeClass = 'badge-cancelled';
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($a['payment_status']); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($a['booked_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
