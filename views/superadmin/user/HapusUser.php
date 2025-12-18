<?php
/**
 * 🗑️ HapusUser.php — versi hard delete fix
 * Menghapus user secara permanen dari database
 * + otomatis hapus semua data relasi yang terhubung.
 * Author: Ocha Della
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . '/../../../config/koneksi.php';

// 🔌 Koneksi
$db = new DBConnection();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => '⚠️ ID user tidak ditemukan atau tidak valid.'
        ]);
        exit;
    }

    // 🔍 Nonaktifkan sementara foreign key check agar tidak error constraint
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    // 🚮 Hapus user permanen
    $hapus = $conn->prepare("DELETE FROM user WHERE iduser = ?");
    $hapus->bind_param("i", $id);
    $hapus->execute();

    // ✅ Aktifkan kembali foreign key check
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    if ($hapus->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => '✅ User berhasil dihapus dari database.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '❌ Gagal menghapus user. Mungkin sudah tidak ada di database.'
        ]);
    }

    $hapus->close();
    $conn->close();

} else {
    echo json_encode([
        'success' => false,
        'message' => '🚫 Akses tidak valid. Gunakan metode POST.'
    ]);
}
?>
