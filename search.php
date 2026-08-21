<?php
// ================================================================
// AJAX ENDPOINT: returns doctors as JSON, filtered by name / specialization
// Called from js/script.js using fetch()
// ================================================================
require_once 'includes/db_connect.php';
header('Content-Type: application/json');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$specialization = isset($_GET['specialization']) ? trim($_GET['specialization']) : '';

$sql = "SELECT doctor_id, name, specialization, available_days, available_times, channeling_fee, profile_image
        FROM doctors WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND name LIKE ?";
    $params[] = "%$search%";
}

if ($specialization !== '') {
    $sql .= " AND specialization = ?";
    $params[] = $specialization;
}

$sql .= " ORDER BY name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($doctors);
