<?php
require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan!");
}

$id = intval($_GET['id']);

// CEK apakah idmargin dipakai di penjualan
$cek = $conn->prepare("SELECT COUNT(*) AS jml FROM penjualan WHERE idmargin_penjualan = ?");
$cek->bind_param("i", $id);
$cek->execute();
$res = $cek->get_result()->fetch_assoc();

if ($res['jml'] > 0) {
    die("<script>
        alert('Tidak bisa dihapus karena sedang dipakai di transaksi penjualan!');
        window.location.href='MarginPenjualan.php';
    </script>");
}

// Jika aman → hapus
$stmt = $conn->prepare("DELETE FROM margin_penjualan WHERE idmargin_penjualan = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>
        alert('Data berhasil dihapus!');
        window.location.href='MarginPenjualan.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal menghapus data!');
        window.location.href='MarginPenjualan.php';
    </script>";
}
?>
