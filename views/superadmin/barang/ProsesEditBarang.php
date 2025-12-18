<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

// Ambil data POST
$id       = intval($_POST['idbarang']);
$nama     = $conn->real_escape_string($_POST['nama']);
$jenis    = $conn->real_escape_string($_POST['jenis']);
$harga    = intval($_POST['harga']);
$idsatuan = intval($_POST['idsatuan']);
$status   = isset($_POST['status']) ? 1 : 0;

// Update barang (kolom yang benar)
$update = $conn->query("
    UPDATE barang SET 
        nama_barang = '$nama',
        jenis       = '$jenis',
        harga_modal = $harga,
        idsatuan    = $idsatuan,
        status      = $status
    WHERE idbarang = $id
");

if ($update) {
    echo json_encode([
        'success' => true,
        'message' => '✅ Barang berhasil diperbarui!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => '❌ Gagal memperbarui barang.'
    ]);
}
?>
