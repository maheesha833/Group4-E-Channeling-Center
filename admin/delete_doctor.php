<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$doctorId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($doctorId > 0) {
    $stmt = $pdo->prepare("DELETE FROM doctors WHERE doctor_id = ?");
    $stmt->execute([$doctorId]);
}

header('Location: dashboard.php?msg=' . urlencode('Doctor removed.'));
exit;
