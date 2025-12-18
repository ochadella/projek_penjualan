<?php
require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

$id = $_GET['id'] ?? 0;

// hapus detail dulu
$conn->query("DELETE FROM detail_pengadaan WHERE idpengadaan = '$id'");

// hapus header
$conn->query("DELETE FROM pengadaan WHERE idpengadaan = '$id'");

header("Location: DataPengadaan.php");
exit;
