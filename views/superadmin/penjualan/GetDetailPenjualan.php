<?php
require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

$id = intval($_GET['id'] ?? 0);

// HEADER
$header = $conn->query("
    SELECT 
        p.idpenjualan,
        u.username AS kasir,
        p.created_at,
        p.total_nilai
    FROM penjualan p
    JOIN user u ON u.iduser = p.iduser
    WHERE p.idpenjualan = $id
    LIMIT 1
")->fetch_assoc();

if (!$header) {
    echo "<h4 class='text-danger text-center mt-5'>Data penjualan tidak ditemukan.</h4>";
    exit;
}

// DETAIL
$detail = $conn->query("
    SELECT 
        b.nama_barang,
        d.jumlah,
        d.harga_satuan,
        d.subtotal
    FROM detail_penjualan d
    JOIN barang b ON b.idbarang = d.idbarang
    WHERE d.idpenjualan = $id
");

$total_detail = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>🛒 Detail Penjualan</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #4B0082, #C13584, #E94057, #F27121, #FFD54F);
      background-size: 300% 300%;
      animation: gradientFlow 12s ease infinite;
      color: #fff;
      min-height: 100vh;
    }
    @keyframes gradientFlow {
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
    }
    .navbar {
      background: rgba(20,20,35,0.75);
      backdrop-filter: blur(12px);
      padding: 10px 40px;
      box-shadow: 0 3px 12px rgba(0,0,0,0.3);
    }
    .navbar-brand { color: #FFD54F !important; font-weight: 700; }
    .table-container {
      background: rgba(43,15,74,0.8);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.25);
    }
    .table {
      background: #fff;
      color: #333;
      border-radius: 8px;
    }
    .table-hover tbody tr:hover { background: #f8f9fa; }
    .btn-back {
      background:#9370DB;
      color:#fff;
      font-weight:600;
      border-radius:10px;
      padding:8px 18px;
      border:none;
      transition:0.3s;
    }
    .btn-back:hover { background:#7B68EE; }
    footer { text-align:center; padding:25px; color:#fff7d0; margin-top:40px;}
  </style>
</head>

<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="DataPenjualan.php">🛒 Penjualan</a>
    </div>
</nav>

<h1 class="text-center mt-5 text-warning">
    <i class="bi bi-receipt"></i> Detail Penjualan
</h1>

<div class="container mt-4">
  <div class="table-container">

    <!-- HEADER -->
    <h5 class="text-warning mb-3">Informasi Penjualan #<?= $header['idpenjualan'] ?></h5>

    <div class="row small mb-3">
        <div class="col-md-6">
            <p><strong>Tanggal:</strong> <?= $header['created_at'] ?></p>
            <p><strong>Kasir:</strong> <?= $header['kasir'] ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Total Nilai:</strong> Rp <?= number_format($header['total_nilai'],0,',','.') ?></p>
        </div>
    </div>

    <hr style="border-color: rgba(255,255,255,0.25);">

    <!-- DETAIL TABLE -->
    <div class="table-responsive mt-3">
      <table class="table table-hover text-center">
        <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Barang</th>
              <th>Jumlah</th>
              <th>Harga Satuan</th>
              <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
          <?php if ($detail && $detail->num_rows > 0): ?>
            <?php $no = 1; ?>
            <?php while ($d = $detail->fetch_assoc()): ?>
              <?php $total_detail += $d['subtotal']; ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="text-start"><?= $d['nama_barang']; ?></td>
                    <td><?= $d['jumlah']; ?></td>
                    <td>Rp <?= number_format($d['harga_satuan'], 0, ',', '.'); ?></td>
                    <td>Rp <?= number_format($d['subtotal'], 0, ',', '.'); ?></td>
                </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5" class="text-warning">Tidak ada detail barang.</td></tr>
          <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="table-success">
                <td colspan="4" class="fw-bold text-end">Total Detail:</td>
                <td class="fw-bold">Rp <?= number_format($total_detail,0,',','.') ?></td>
            </tr>
        </tfoot>
      </table>
    </div>

    <a href="DataPenjualan.php" class="btn btn-back mt-3">
        <i class="bi bi-arrow-left-circle"></i> Kembali
    </a>

  </div>
</div>

<footer>© <?= date('Y'); ?> — Superadmin Panel 💛</footer>

</body>
</html>
