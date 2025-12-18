<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

if (!$conn) {
    die("Koneksi database gagal!");
}

$query = "
    SELECT 
        ks.idkartu_stok,
        ks.created_at,
        b.nama_barang,
        ks.jenis_transaksi,
        ks.masuk,
        ks.keluar,
        ks.stock,
        ks.idtransaksi
    FROM kartu_stok ks
    JOIN barang b ON b.idbarang = ks.idbarang
    ORDER BY ks.created_at DESC
";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>📊 Data Kartu Stok — Superadmin</title>
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

    .table-container {
      background: rgba(43, 15, 74, 0.8);
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
    .table-hover tbody tr:hover {
      background-color: #f8f9fa;
    }

    .badge-masuk {
        background-color: #198754;
    }
    .badge-keluar {
        background-color: #dc3545;
    }

    footer { text-align:center; padding:25px; color:#fff7d0; margin-top:40px;}
  </style>
</head>
<body>

  <h1 class="text-center mt-5"><i class="bi bi-boxes"></i> Data Kartu Stok</h1>
  <p class="lead text-center">Riwayat pergerakan stok barang.</p>

  <div class="container mt-4">
    <div class="table-container">

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-warning"><i class="bi bi-table"></i> Riwayat Stok</h5>
        <a href="../../../interface/dashboard_superadmin.php" class="btn btn-warning">
            <i class="bi bi-arrow-left-circle"></i> Kembali
        </a>
      </div>

      <div class="table-responsive">
        <table class="table table-hover text-center">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tanggal</th>
              <th>Barang</th>
              <th>Jenis</th>
              <th>Masuk</th>
              <th>Keluar</th>
              <th>Stok Akhir</th>
              <th>ID Transaksi</th>
            </tr>
          </thead>

          <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $row['idkartu_stok'] ?></td>
                <td><?= $row['created_at'] ?></td>
                <td class="text-start"><?= $row['nama_barang'] ?></td>
                <td>
                  <?php if ($row['jenis_transaksi'] === 'M'): ?>
                    <span class="badge badge-masuk">Masuk</span>
                  <?php else: ?>
                    <span class="badge badge-keluar">Keluar</span>
                  <?php endif; ?>
                </td>
                <td><?= $row['masuk'] ?></td>
                <td><?= $row['keluar'] ?></td>
                <td class="fw-bold"><?= $row['stock'] ?></td>
                <td><?= $row['idtransaksi'] ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="8" class="text-warning">Belum ada data kartu stok.</td></tr>
          <?php endif; ?>
          </tbody>

        </table>
      </div>

    </div>
  </div>

  <footer>© <?= date('Y'); ?> — Superadmin Panel 💛</footer>

</body>
</html>
