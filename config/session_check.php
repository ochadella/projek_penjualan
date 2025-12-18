<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Kalau user bukan superadmin tapi akses dashboard superadmin
if (basename($_SERVER['PHP_SELF']) == 'dashboard_superadmin.php' && $_SESSION['role'] != 'superadmin') {
    header("Location: dashboard_admin.php");
    exit;
}
?>
