<?php
include "koneksi.php";

$id = $_GET['id'];

// Hapus data berdasarkan ID
$hapus = mysqli_query($conn, "DELETE FROM barang WHERE idbarang='$id'");

if ($hapus) {
    // reset auto increment
    mysqli_query($conn, "ALTER TABLE barang AUTO_INCREMENT = 1");
    header("Location: barang.php");
    exit;
} else {
    echo "Gagal hapus: " . mysqli_error($conn);
}
?>



