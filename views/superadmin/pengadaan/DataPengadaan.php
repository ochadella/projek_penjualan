<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

if (!$conn) {
    die("Koneksi database gagal!");
}

// AMBIL DATA DARI VIEW
$query = "
    SELECT 
        idpengadaan,
        tanggal_pengadaan,
        nama_user,
        vendor AS nama_vendor,
        kontak_vendor,
        jenis_vendor,
        subtotal_nilai,
        ppn,
        total_nilai,
        status
    FROM view_pengadaan_lengkap
    ORDER BY tanggal_pengadaan DESC
";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>🧾 Daftar Pengadaan — Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- ✅ CSS KAMU TETAP UTUH -->
  <style>
    <?php /* CSS ASLI KAMU — TIDAK DIUBAH */ ?>
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
    .table td {
        color: #333;
        vertical-align: middle;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .btn-add {
      background-color: #FFD54F;
      color: #4B0082;
      font-weight: 600;
      border: none;
      border-radius: 10px;
      padding: 8px 18px;
      transition: 0.3s;
    }
    .btn-add:hover { background-color: #4B0082; color: #fff; }

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
    .btn-back:hover {
      background-color: #7B68EE;
      color: #fff;
    }

    .btn-action-detail {
        background-color: #0d6efd;
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        transition: 0.3s;
        margin-right: 4px;
    }

    .btn-action-edit {
        background-color: #FFD54F;
        color: #333;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
    }

    .btn-action-hapus {
        background-color: #dc3545;
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
    }

    footer { text-align:center; padding:25px; color:#fff7d0; font-size:0.9rem; margin-top:40px;}
  </style>
</head>
<body>

<!-- ✅ NAVBAR (ASLI TIDAK DIUBAH) -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="../../../interface/dashboard_superadmin.php">👑 Superadmin</a>
      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav d-flex align-items-center">
          <li class="nav-item"><a class="nav-link" href="../barang/DataBarang.php">📦 Barang</a></li>
          <li class="nav-item"><a class="nav-link" href="../vendor/DataVendor.php">🏢 Vendor</a></li>
          <li class="nav-item"><a class="nav-link" href="../margin/MarginPenjualan.php">💰 Margin</a></li>
          <li class="nav-item"><a href="../../../auth/logout.php" class="btn btn-logout">🚪 Logout</a></li>
        </ul>
      </div>
    </div>
</nav>

<h1 class="text-center mt-5"><i class="bi bi-clipboard-data"></i> Daftar Pengadaan</h1>
<p class="lead text-center">Riwayat pengadaan barang terbaru beserta total nilainya.</p>

<div class="container mt-4">
  <div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0 text-warning"><i class="bi bi-receipt"></i> Daftar Pengadaan (Terbaru)</h5>
      <div>
        <a href="../../../interface/dashboard_superadmin.php" class="btn btn-back">
          <i class="bi bi-arrow-left-circle"></i> Kembali
        </a>
        <button onclick="openTambahModal()" class="btn btn-add">
          <i class="bi bi-plus-circle"></i> Tambah Pengadaan
        </button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle text-center">
        <thead>
            <tr>
              <th>ID Pengadaan</th>
              <th>Tanggal</th>
              <th>User</th>
              <th>Vendor</th>
              <th>Subtotal</th>
              <th>PPN</th>
              <th>Total Nilai</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                  <td><?= $row['idpengadaan'] ?></td>
                  <td><?= $row['tanggal_pengadaan'] ?></td>
                  <td><?= $row['nama_user'] ?></td>
                  <td><?= $row['nama_vendor'] ?></td>
                  <td>Rp <?= number_format($row['subtotal_nilai'],0,',','.') ?></td>
                  <td>Rp <?= number_format($row['ppn'],0,',','.') ?></td>
                  <td>Rp <?= number_format($row['total_nilai'],0,',','.') ?></td>

                  <td>
                    <?php
                      if ($row['status'] === 'P') echo '<span class="badge bg-success">Proses</span>';
                      elseif ($row['status'] === 'S') echo '<span class="badge bg-secondary">Selesai</span>';
                      else echo '<span class="badge bg-warning text-dark">'.$row['status'].'</span>';
                    ?>
                  </td>

                  <td>
                    <a href="GetDetailPengadaan.php?id=<?= $row['idpengadaan'] ?>"
                      class="btn btn-action-detail btn-sm">
                        <i class="bi bi-eye"></i>
                    </a>


                    <!-- <button class="btn btn-action-edit btn-sm"
                            onclick="openEditModal(<?= $row['idpengadaan'] ?>)">
                        <i class="bi bi-pencil-square"></i>
                    </button> -->

                    <a href="HapusPengadaan.php?id=<?= $row['idpengadaan'] ?>"
                       onclick="return confirm('Yakin ingin menghapus data ini?')"
                       class="btn btn-action-hapus btn-sm">
                       <i class="bi bi-trash"></i>
                    </a>
                  </td>
              </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9" class="text-warning">Belum ada data pengadaan.</td></tr>
        <?php endif; ?>
        </tbody>

      </table>
    </div>
  </div>
</div>

<footer>© <?= date('Y'); ?> — Superadmin Panel 💛</footer>

<!-- ===================================== -->
<!-- MODAL TAMBAH -->
<!-- ===================================== -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="background:#2b0f4acc; color:white; border-radius:16px;">
      <div class="modal-header">
        <h5 class="modal-title text-warning"><i class="bi bi-plus-circle"></i> Tambah Pengadaan</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="tambahContent">
        Loading...
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ ✅ FIX: SCRIPT EKSEKUTOR FORM -->
<script>
function openTambahModal(){
    fetch("GetFormTambahPengadaan.php")
    .then(r => r.text())
    .then(r => {
        document.getElementById("tambahContent").innerHTML = r;

        // ✅ Jalankan script di dalam modal
        const scripts = document.querySelectorAll("#tambahContent script");
        scripts.forEach(oldScript => {
            const newScript = document.createElement("script");
            newScript.textContent = oldScript.textContent;
            document.body.appendChild(newScript);
        });

        new bootstrap.Modal(document.getElementById("modalTambah")).show();
    });
}
</script>

</body>
</html>
