<?php
// ================================================================
// RUN THIS FILE ONCE IN YOUR BROWSER AFTER IMPORTING database.sql
// e.g. http://localhost/echanneling/setup_admin.php
// It creates the default admin account safely (proper password hash).
// DELETE this file afterwards for security.
// ================================================================
require_once 'includes/db_connect.php';

$username = 'admin';
$plainPassword = 'admin123';
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Avoid creating duplicate admins if this is run more than once
$check = $pdo->prepare("SELECT admin_id FROM admins WHERE username = ?");
$check->execute([$username]);

if ($check->rowCount() > 0) {
    // Update password instead of inserting a duplicate row
    $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
    $stmt->execute([$hashedPassword, $username]);
    echo "Admin account already existed - password reset to default.<br>";
} else {
    $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashedPassword]);
    echo "Admin account created successfully.<br>";
}

echo "Username: <b>admin</b><br>";
echo "Password: <b>admin123</b><br><br>";
echo "<strong>Please delete setup_admin.php now for security.</strong>";
