<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->execute([$username]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// password_verify() safely checks the plain password against the stored hash
if ($admin && password_verify($password, $admin['password'])) {
    $_SESSION['admin_id'] = $admin['admin_id'];
    $_SESSION['admin_username'] = $admin['username'];
    header('Location: dashboard.php');
    exit;
} else {
    header('Location: login.php?error=' . urlencode('Invalid username or password.'));
    exit;
}
