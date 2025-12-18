<?php
require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

$id = $_GET['id'] ?? 0;

// HEADER PENJUALAN
$q = $conn->query("
    SELECT idpenjualan, kasir, created_at, total_nilai
    FROM view_penjualan_detail
    WHERE idpenjualan = '$id'
    LIMIT 1
");

$data = $q->fetch_assoc();
if (!$data) {
    die("<div class='text-danger fw-bold'>Data tidak ditemukan.</div>");
}

// DETAIL ITEM
$qDetail = $conn->query("
    SELECT jumlah, harga_satuan, subtotal
    FROM view_penjualan_detail
    WHERE idpenjualan = '$id'
");
?>

<!-- Tambah CDN Bootstrap Icons agar ikon tampil di modal -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div style="background:#2b0f4acc; color:white; padding:20px; border-radius:16px;">

    <h4 class="text-warning mb-3">
        <i class="bi bi-eye"></i> Detail Penjualan #<?= $data['idpenjualan'] ?>
    </h4>

    <p><b>Kasir:</b> <?= $data['kasir'] ?></p>
    <p><b>Tanggal:</b> <?= $data['created_at'] ?></p>

    <hr>

    <h5 class="text-warning">Daftar Barang</h5>

    <table class="table table-hover bg-white text-center mt-3">
        <thead>
            <tr>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>
            <?php while ($d = $qDetail->fetch_assoc()): ?>
                <tr>
                    <td><?= $d['jumlah']; ?></td>
                    <td>Rp <?= number_format($d['harga_satuan'], 0, ',', '.'); ?></td>
                    <td>Rp <?= number_format($d['subtotal'], 0, ',', '.'); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <hr>

    <p><b>Total Nilai:</b> Rp <?= number_format($data['total_nilai'], 0, ',', '.'); ?></p>

</div>
