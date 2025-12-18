<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';

$db  = new DBConnection();
$conn = $db->getConnection();

// Ambil data dari view
$q = $conn->query("SELECT * FROM view_laporan_global ORDER BY tanggal DESC");

// Mulai output buffer
ob_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Global</title>

<style>
body { font-family: Arial, sans-serif; font-size: 12px; }
h2 { text-align:center; margin-bottom: 0; }
p { text-align:center; margin-top: 5px; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
table, th, td { border: 1px solid #000; }
th { background: #eee; }
td, th { padding: 6px; text-align: center; }
</style>

</head>
<body>

<h2>LAPORAN GLOBAL</h2>
<p>Rekapitulasi Pengadaan & Penjualan</p>

<table>
<thead>
<tr>
    <th>ID</th>
    <th>Tanggal</th>
    <th>Jenis Transaksi</th>
    <th>Jumlah Transaksi</th>
    <th>Total Nilai (Rp)</th>
    <th>User</th>
</tr>
</thead>
<tbody>

<?php while ($r = $q->fetch_assoc()) : ?>
<tr>
    <td><?= $r['idlaporan']; ?></td>
    <td><?= $r['tanggal']; ?></td>
    <td><?= $r['jenis_transaksi']; ?></td>
    <td><?= $r['jumlah_transaksi']; ?></td>
    <td>Rp <?= number_format($r['total_nilai'], 0, ',', '.'); ?></td>
    <td><?= $r['nama_user']; ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

</body>
</html>

<?php

$html = ob_get_clean();

// Header supaya jadi PDF
header("Content-type: application/vnd.ms-print-pdf");
header("Content-Disposition: attachment; filename=Laporan_Global.pdf");

// Render PDF pakai engine print PDF bawaan browser
echo $html;
exit;
?>
