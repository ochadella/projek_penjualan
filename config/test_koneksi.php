<?php
require_once "koneksi.php";

$db = new DBConnection();
$conn = $db->getConnection();

if ($conn) {
    echo "✅ Koneksi ke database PBD_UTS berhasil!";
}

$db->close_connection();
?>
