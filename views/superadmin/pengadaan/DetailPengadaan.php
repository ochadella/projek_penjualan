<?php
require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

$id = $_GET['id'] ?? 0;

$q = $conn->query("SELECT * FROM view_pengadaan_lengkap WHERE idpengadaan = '$id'");
$data = $q->fetch_assoc();

if (!$data) {
    die("<h3>Data pengadaan tidak ditemukan.</h3>");
}

// DETAIL ITEM
$qDetail = $conn->query("
SELECT d.*, b.nama AS nama_barang
FROM detail_pengadaan d
JOIN barang b ON d.idbarang = b.idbarang
WHERE d.idpengadaan = '$id'

");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Pengadaan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: 'Poppins';
    background: linear-gradient(135deg, #4B0082, #C13584, #E94057, #F27121, #FFD54F);
    min-height: 100vh;
    color: #fff;
}
.card-box {
    background: rgba(43,15,74,0.85);
    padding: 25px;
    border-radius: 20px;
}
.table td, .table th {
    color: #333;
}
</style>
</head>

<body>

<div class="container mt-5">
    <div class="card-box">

        <h2 class="text-warning mb-3">Detail Pengadaan</h2>

        <p><b>ID:</b> <?= $data['idpengadaan'] ?></p>
        <p><b>Tanggal:</b> <?= $data['tanggal_pengadaan'] ?></p>
        <p><b>User:</b> <?= $data['nama_user'] ?></p>
        <p><b>Vendor:</b> <?= $data['vendor'] ?></p>

        <hr>

        <h4 class="text-warning">Daftar Barang</h4>

        <table class="table table-hover bg-white text-center mt-3">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Sub Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while($d = $qDetail->fetch_assoc()): ?>
                    <tr>
                        <td><?= $d['nama_barang'] ?></td>
                        <td><?= $d['jumlah'] ?></td>
                        <td>Rp <?= number_format($d['harga_satuan'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($d['sub_total'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <hr>

        <p><b>Subtotal:</b> Rp <?= number_format($data['subtotal_nilai'], 0, ',', '.') ?></p>
        <p><b>PPN:</b> Rp <?= number_format($data['ppn'], 0, ',', '.') ?></p>
        <p><b>Total:</b> Rp <?= number_format($data['total_nilai'], 0, ',', '.') ?></p>

        <a href="DataPengadaan.php" class="btn btn-warning mt-3">Kembali</a>

    </div>
</div>

</body>
</html>
