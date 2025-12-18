<?php
require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

if (!$conn) {
    die("Koneksi database gagal!");
}

$id = $_GET['id'] ?? 0;

if ($id == 0) {
    echo "ERROR";
    exit;
}

// Hapus detail penjualan dulu (FK constraint)
$conn->query("DELETE FROM penjualan_detail WHERE idpenjualan = '$id'");

// Hapus data penjualan
$conn->query("DELETE FROM penjualan WHERE idpenjualan = '$id'");

echo "SUCCESS";
