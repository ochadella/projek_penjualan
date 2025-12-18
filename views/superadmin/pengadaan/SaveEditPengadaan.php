<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan");
}

$id = intval($_GET['id']);

// Ambil data dari POST
$timestamp = $_POST['timestamp'] ?? '';
$user_iduser = intval($_POST['user_iduser'] ?? 0);
$vendor_idvendor = intval($_POST['vendor_idvendor'] ?? 0);
$subtotal_nilai = intval($_POST['subtotal_nilai'] ?? 0);
$ppn = intval($_POST['ppn'] ?? 0);
$total_nilai = intval($_POST['total_nilai'] ?? 0);
$status = $_POST['status'] ?? '1';

// Validasi
if (empty($timestamp) || $user_iduser == 0 || $vendor_idvendor == 0) {
    die("Data tidak lengkap");
}

// Update data
$query = "
    UPDATE pengadaan SET
        timestamp = ?,
        user_iduser = ?,
        vendor_idvendor = ?,
        subtotal_nilai = ?,
        ppn = ?,
        total_nilai = ?,
        status = ?
    WHERE idpengadaan = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("siiiiisi", $timestamp, $user_iduser, $vendor_idvendor, $subtotal_nilai, $ppn, $total_nilai, $status, $id);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>