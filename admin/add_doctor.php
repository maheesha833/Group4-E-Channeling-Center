<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$specialization = trim($_POST['specialization'] ?? '');
$availableDays = trim($_POST['available_days'] ?? '');
$availableTimes = trim($_POST['available_times'] ?? '');
$fee = (float)($_POST['channeling_fee'] ?? 0);

if ($name === '' || $specialization === '' || $availableDays === '' || $availableTimes === '' || $fee <= 0) {
    header('Location: dashboard.php?msg=' . urlencode('Please fill in all fields correctly.'));
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO doctors (name, specialization, available_days, available_times, channeling_fee)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->execute([$name, $specialization, $availableDays, $availableTimes, $fee]);

header('Location: dashboard.php?msg=' . urlencode('Doctor added successfully.'));
exit;
