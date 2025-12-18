<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

$id = intval($_GET['id'] ?? 0);

// HEADER
$header = null;
$stmtHead = $conn->prepare("
    SELECT 
        idpengadaan,
        tanggal_pengadaan,
        nama_user,
        vendor AS nama_vendor,
        kontak_vendor,
        jenis_vendor,
        subtotal_nilai,
        ppn,
        total_nilai
    FROM view_pengadaan_lengkap
    WHERE idpengadaan = ?
");
$stmtHead->bind_param("i", $id);
$stmtHead->execute();
$resHead = $stmtHead->get_result();
if ($resHead && $resHead->num_rows > 0) {
    $header = $resHead->fetch_assoc();
}
$stmtHead->close();

// DETAIL
$stmtDetail = $conn->prepare("
    SELECT 
        d.iddetail_pengadaan,
        b.nama_barang,
        d.jumlah,
        d.harga_satuan,
        d.sub_total
    FROM detail_pengadaan d
    JOIN barang b ON b.idbarang = d.idbarang
    WHERE d.idpengadaan = ?
");
$stmtDetail->bind_param("i", $id);
$stmtDetail->execute();
$q = $stmtDetail->get_result();

$total_detail = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>📦 Detail Pengadaan</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- ✅ CSS PERSIS SAMA -->
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
      background: rgba(20, 20, 35, 0.75);
      backdrop-filter: blur(12px);
      box-shadow: 0 3px 12px rgba(0,0,0,0.3);
      padding: 10px 40px;
    }
    .navbar-brand { color: #FFD54F !important; font-weight: 700; font-size: 1.3rem; }
    .nav-link { color: #FFD54F !important; font-weight: 500; margin-left: 18px; transition: 0.3s; }
    .nav-link:hover { color: #fff !important; background-color: rgba(255,213,79,0.2); border-radius: 8px; }
    .btn-back {
      background-color: #9370DB;
      color: #fff;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      padding: 8px 18px;
      transition: 0.3s;
    }
    .btn-back:hover { background-color: #7B68EE; color: #fff; }
    .table-container {
      background: rgba(43,15,74,0.8);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.25);
    }
    .table {
        background-color: #fff;
        color: #333;
        border-radius: 8px;
        overflow: hidden;
    }
    .table th {
        background-color: transparent;
        color: #333;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
    }
    .table td { color: #333; vertical-align: middle; }
    .table-hover tbody tr:hover { background-color: #f8f9fa; }
    footer { text-align:center; padding:25px; color:#fff7d0; font-size:0.9rem; margin-top:40px;}
  </style>
</head>

<body>

<!-- ✅ NAVBAR SAMA -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="DataPengadaan.php">📦 Pengadaan</a>
    </div>
</nav>

<h1 class="text-center mt-5 text-warning">
    <i class="bi bi-receipt-cutoff"></i> Detail Pengadaan
</h1>

<div class="container mt-4">
  <div class="table-container">

    <!-- ✅ HEADER INFO -->
    <h5 class="text-warning mb-3">Informasi Pengadaan #<?= $header['idpengadaan'] ?></h5>

    <div class="row small mb-3">
        <div class="col-md-6">
            <p><strong>Tanggal:</strong> <?= $header['tanggal_pengadaan'] ?></p>
            <p><strong>User:</strong> <?= $header['nama_user'] ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Vendor:</strong> <?= $header['nama_vendor'] ?></p>
            <p><strong>Kontak:</strong> <?= $header['kontak_vendor'] ?></p>
        </div>
    </div>

    <hr style="border-color: rgba(255,255,255,0.25);">

    <!-- ✅ TABEL DETAIL -->
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
        <?php if ($q && $q->num_rows > 0): ?>
            <?php $no = 1; ?>
            <?php while ($row = $q->fetch_assoc()): ?>
                <?php $total_detail += $row['sub_total']; ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="text-start"><?= $row['nama_barang'] ?></td>
                    <td><?= $row['jumlah'] ?></td>
                    <td>Rp <?= number_format($row['harga_satuan'],0,',','.') ?></td>
                    <td>Rp <?= number_format($row['sub_total'],0,',','.') ?></td>
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

    <a href="DataPengadaan.php" class="btn btn-back mt-3">
        <i class="bi bi-arrow-left-circle"></i> Kembali
    </a>

  </div>
</div>

<footer>© <?= date('Y'); ?> — Superadmin Panel 💛</footer>

</body>
</html>
