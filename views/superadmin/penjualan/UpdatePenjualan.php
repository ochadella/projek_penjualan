<?php
require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid access!");
}

$id = $_POST['idpenjualan'] ?? 0;
$tanggal = $_POST['tanggal'] ?? '';
$jumlah = $_POST['jumlah'] ?? [];
$idDetail = $_POST['iddetail'] ?? [];

if (!$id) {
    die("Data tidak valid.");
}

/* ========================================
   1️⃣ UPDATE HEADER (tanggal)
========================================= */
$updateHeader = $conn->prepare("
    UPDATE penjualan 
    SET created_at=? 
    WHERE idpenjualan=?
");
$updateHeader->bind_param("si", $tanggal, $id);
$updateHeader->execute();

/* ========================================
   2️⃣ UPDATE DETAIL PENJUALAN
      - update jumlah
      - update subtotal = jumlah * harga_satuan
========================================= */
foreach ($idDetail as $i => $iddetail) {

    $jml = intval($jumlah[$i]);

    // Pastikan harga_satuan diambil dari DB
    $getHarga = $conn->query("
        SELECT harga_satuan 
        FROM detail_penjualan 
        WHERE iddetail_penjualan = $iddetail
    ")->fetch_assoc();

    if (!$getHarga) continue;

    $harga = intval($getHarga['harga_satuan']);
    $subtotal = $jml * $harga;

    // Update jumlah & subtotal
    $q = $conn->prepare("
        UPDATE detail_penjualan
        SET jumlah = ?, subtotal = ?
        WHERE iddetail_penjualan = ?
    ");
    $q->bind_param("iii", $jml, $subtotal, $iddetail);
    $q->execute();
}

/* ========================================
   3️⃣ HITUNG ULANG TOTAL NILAI
========================================= */
$sum = $conn->query("
    SELECT SUM(subtotal) AS total 
    FROM detail_penjualan 
    WHERE idpenjualan = '$id'
")->fetch_assoc()['total'];

$updateTotal = $conn->prepare("
    UPDATE penjualan
    SET total_nilai = ?
    WHERE idpenjualan = ?
");
$updateTotal->bind_param("ii", $sum, $id);
$updateTotal->execute();

/* ========================================
   4️⃣ REDIRECT
========================================= */
echo "<script>
    alert('Penjualan berhasil diperbarui!');
    window.location.href='DataPenjualan.php';
</script>";
?>
