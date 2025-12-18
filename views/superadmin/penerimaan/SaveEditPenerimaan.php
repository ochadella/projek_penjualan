<?php
require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

$id = $_POST['id'];
$tanggal = $_POST['tanggal'];
$status = $_POST['status'];

$conn->query("
    UPDATE penerimaan
    SET tanggal='$tanggal',
        status='$status'
    WHERE idpenerimaan='$id'
");

echo "Penerimaan berhasil diperbarui 👍";
