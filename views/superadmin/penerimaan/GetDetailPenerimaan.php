<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

$id = intval($_GET['id'] ?? 0);

// ================================
//   AMBIL HEADER PENERIMAAN
// ================================
$header = null;
$stmtHead = $conn->prepare("
    SELECT 
        p.idpenerimaan,
        p.created_at AS tanggal_penerimaan,
        u.username AS nama_user,
        pg.idpengadaan,
        v.nama_vendor
    FROM penerimaan p
    JOIN user u ON u.iduser = p.iduser
    JOIN pengadaan pg ON pg.idpengadaan = p.idpengadaan
    JOIN vendor v ON v.idvendor = pg.vendor_idvendor
    WHERE p.idpenerimaan = ?
");
$stmtHead->bind_param("i", $id);
$stmtHead->execute();
$resHead = $stmtHead->get_result();
if ($resHead && $resHead->num_rows > 0) {
    $header = $resHead->fetch_assoc();
}
$stmtHead->close();

if (!$header) {
    echo "<p class='text-danger text-center fw-bold'>Data penerimaan tidak ditemukan.</p>";
    exit;
}

// ================================
//   AMBIL DETAIL BARANG
// ================================
$stmtDetail = $conn->prepare("
    SELECT 
        b.nama_barang,
        dp.jumlah_terima,
        dp.harga_satuan_terima,
        dp.sub_total_terima
    FROM detail_penerimaan dp
    JOIN barang b ON b.idbarang = dp.barang_idbarang
    WHERE dp.idpenerimaan = ?
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
  <title>📦 Detail Penerimaan</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- ✅ CSS SAMA KAYAK DETAIL PENGADAAN -->
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
    .table td { color: #333; }
    .table-hover tbody tr:hover { background-color: #f8f9fa; }
    footer { text-align:center; padding:25px; color:#fff7d0; margin-top:40px; }
  </style>
</head>

<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="DataPenerimaan.php">📦 Penerimaan</a>
    </div>
</nav>

<h1 class="text-center mt-5 text-warning">
    <i class="bi bi-box-arrow-in-down"></i> Detail Penerimaan
</h1>

<!-- ✅ FULL WIDTH PERSIS DETAIL PENGADAAN -->
<div class="container-fluid mt-4 px-5">
  <div class="table-container">

    <h5 class="text-warning mb-3">Informasi Penerimaan #<?= $header['idpenerimaan'] ?></h5>

    <div class="row small mb-3">
        <div class="col-md-6">
            <p><strong>Tanggal:</strong> <?= $header['tanggal_penerimaan'] ?></p>
            <p><strong>Diterima Oleh:</strong> <?= $header['nama_user'] ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Vendor:</strong> <?= $header['nama_vendor'] ?></p>
        </div>
    </div>

    <hr style="border-color: rgba(255,255,255,0.25);">

    <div class="table-responsive mt-3">
      <table class="table table-hover text-center">
        <thead>
            <tr>
              <th>No</th>
              <th>Barang</th>
              <th>Jumlah</th>
              <th>Harga Satuan</th>
              <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
        <?php $no = 1; ?>
        <?php while ($row = $q->fetch_assoc()): ?>
            <?php $total_detail += $row['sub_total_terima']; ?>
            <tr>
                <td><?= $no++ ?></td>
                <td class="text-start"><?= $row['nama_barang'] ?></td>
                <td><?= $row['jumlah_terima'] ?></td>
                <td>Rp <?= number_format($row['harga_satuan_terima'],0,',','.') ?></td>
                <td>Rp <?= number_format($row['sub_total_terima'],0,',','.') ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr class="table-success">
                <td colspan="4" class="fw-bold text-end">Total:</td>
                <td class="fw-bold">Rp <?= number_format($total_detail,0,',','.') ?></td>
            </tr>
        </tfoot>
      </table>
    </div>

    <a href="DataPenerimaan.php" class="btn btn-back mt-3">
        <i class="bi bi-arrow-left-circle"></i> Kembali
    </a>

  </div>
</div>

<footer>© <?= date('Y'); ?> — Superadmin Panel 💛</footer>

</body>
</html>
