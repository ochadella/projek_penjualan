<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

/*
|-------------------------------------------------
|  AMBIL DATA LAPORAN GLOBAL DARI VIEW
|-------------------------------------------------
*/
$query = "SELECT * FROM view_laporan_global ORDER BY tanggal DESC";
$laporan = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>📊 Laporan Global — Superadmin</title>
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
      background: rgba(20, 20, 35, 0.75);
      backdrop-filter: blur(12px);
      box-shadow: 0 3px 12px rgba(0,0,0,0.3);
      padding: 10px 40px;
    }
    .navbar-brand { color: #FFD54F !important; font-weight: 700; font-size: 1.3rem; }
    .nav-link { color: #FFD54F !important; font-weight: 500; margin-left: 18px; transition: 0.3s; }
    .nav-link:hover { color: #fff !important; background-color: rgba(255,213,79,0.2); border-radius: 8px; }
    .btn-logout { background-color: #FFD54F; color: #4B0082; font-weight: 600; border: none; border-radius: 8px; padding: 6px 18px; transition: 0.3s; margin-left: 18px; }
    .btn-logout:hover { background-color: #4B0082; color: #fff; }

    .table-container {
      background: rgba(43,15,74,0.8);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      padding: 25px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.25);
    }

    .btn-export {
      background-color: #FFD54F;
      color: #4B0082;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      padding: 8px 18px;
      transition: 0.3s;
    }
    .btn-export:hover { background-color: #4B0082; color: #fff; }

    .btn-back {
      background-color: #9370DB;
      color: #fff;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      padding: 8px 18px;
      transition: 0.3s;
      margin-right: 10px;
    }
    .btn-back:hover { background-color: #7B68EE; color: #fff; }

    footer { text-align:center; padding:25px; color:#fff7d0; font-size:0.9rem; margin-top:40px;}
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="../../../interface/dashboard_superadmin.php">💼 Superadmin</a>
      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav d-flex align-items-center">
          <li class="nav-item"><a class="nav-link" href="../user/DataUser.php">👥 User</a></li>
          <li class="nav-item"><a class="nav-link" href="../role/ManajemenRole.php">🧩 Role</a></li>
          <li class="nav-item"><a class="nav-link" href="../log/LogAktivitas.php">📝 Log</a></li>
          <li class="nav-item"><a class="nav-link" href="../margin/MarginPenjualan.php">💰 Margin</a></li>
          <li class="nav-item"><a href="../../../auth/logout.php" class="btn btn-logout">🚪 Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <h1 class="text-center mt-5"><i class="bi bi-bar-chart-line"></i> Laporan Global</h1>
  <p class="lead text-center">Rekapitulasi data pengadaan, penjualan, dan margin sistem.</p>

  <div class="container mt-4">
    <div class="table-container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-warning"><i class="bi bi-graph-up"></i> Data Laporan Global</h5>
        <div>
          <a href="../../../interface/dashboard_superadmin.php" class="btn btn-back">
            <i class="bi bi-arrow-left-circle"></i> Kembali
          </a>
          <a href="ExportPDFLaporanGlobal.php" class="btn btn-export">
              <i class="bi bi-file-earmark-arrow-down"></i> Export PDF
          </a>

        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead>
            <tr>
              <th>ID Laporan</th>
              <th>Tanggal</th>
              <th>Jenis Transaksi</th>
              <th>Jumlah Transaksi</th>
              <th>Total Nilai (Rp)</th>
              <th>User</th>
            </tr>
          </thead>
          <tbody>

            <?php if ($laporan && $laporan->num_rows > 0): ?>
              <?php while ($row = $laporan->fetch_assoc()): ?>
                <tr>
                  <td><?= $row['idlaporan']; ?></td>
                  <td><?= $row['tanggal']; ?></td>
                  <td><?= $row['jenis_transaksi']; ?></td>
                  <td><?= $row['jumlah_transaksi']; ?></td>
                  <td>Rp <?= number_format($row['total_nilai'], 0, ',', '.'); ?></td>
                  <td><?= $row['nama_user']; ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-warning">Belum ada data laporan global.</td>
              </tr>
            <?php endif; ?>

          </tbody>
        </table>
      </div>
    </div>
  </div>

  <footer>© <?= date('Y'); ?> — Superadmin Panel 💛</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
