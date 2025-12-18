<?php
session_start([
    'cookie_lifetime' => 86400,
    'read_and_close'  => false,
]);

require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

// 🔐 AMBIL USER LOGIN
$iduser = $_SESSION['iduser'] ?? 1;
$username = $_SESSION['username'] ?? "system";

/*
   🔥 PAKAI VIEW BENAR: view_retur_barang
   Kolom di view:
   - idretur
   - created_at
   - petugas
   - nama_barang
   - jumlah
   - alasan
*/
$query = "SELECT * FROM view_retur_barang ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>↩️ Data Retur Barang — Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    /* ⚠️ CSS TIDAK DIUBAH SAMA SEKALI */
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

    footer { text-align:center; padding:25px; color:#fff7d0; font-size:0.9rem; margin-top:40px;}
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="../../../interface/dashboard_admin.php">↩️ Admin</a>
      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav d-flex align-items-center">
          <li class="nav-item"><a class="nav-link" href="../barang/DataBarang.php">📦 Data Barang</a></li>
          <li class="nav-item"><a class="nav-link" href="../vendor/DataVendor.php">🏢 Vendor</a></li>
          <li class="nav-item"><a class="nav-link" href="../pengadaan/DataPengadaan.php">🧾 Pengadaan</a></li>
          <li class="nav-item"><a class="nav-link" href="../penjualan/DataPenjualan.php">💰 Penjualan</a></li>
          <li class="nav-item"><a href="../../../auth/logout.php" class="btn btn-logout">🚪 Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <h1 class="text-center mt-5"><i class="bi bi-arrow-return-left"></i> Data Retur Barang</h1>
  <p class="lead text-center">Daftar retur barang dari transaksi penjualan.</p>

  <div class="container mt-4">
    <div class="table-container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-warning"><i class="bi bi-arrow-counterclockwise"></i> Daftar Retur</h5>
        <div>
          <a href="../../../interface/dashboard_superadmin.php" class="btn btn-back">
            <i class="bi bi-arrow-left-circle"></i> Kembali
          </a>

          <!-- 🔥 tombol buka popup -->
          <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalTambahRetur">
            <i class="bi bi-plus-circle"></i> Tambah Retur
          </button>

        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead>
            <tr>
              <th>ID Retur</th>
              <th>Tanggal</th>
              <th>Petugas</th>
              <th>Nama Barang</th>
              <th>Jumlah</th>
              <th>Alasan</th>
            </tr>
          </thead>
          <tbody>

            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['idretur']); ?></td>
                  <td><?= htmlspecialchars($row['created_at']); ?></td>
                  <td><?= htmlspecialchars($row['petugas']); ?></td>
                  <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                  <td><?= htmlspecialchars($row['jumlah']); ?></td>
                  <td><?= htmlspecialchars($row['alasan']); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-warning">Belum ada data retur barang.</td></tr>
            <?php endif; ?>

          </tbody>
        </table>
      </div>
    </div>
  </div>


  <!-- ===================================================== -->
  <!-- 🔥 MODAL POPUP TAMBAH RETUR -->
  <!-- ===================================================== -->

  <?php
  // Ambil data penerimaan untuk dropdown
  $q = "SELECT idpenerimaan, created_at FROM penerimaan ORDER BY idpenerimaan DESC";
  $opsi = $conn->query($q);
  ?>

  <div class="modal fade" id="modalTambahRetur" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="background: rgba(30, 0, 50, 0.9); color: white; border-radius: 15px;">

        <div class="modal-header border-0">
          <h5 class="modal-title">➕ Tambah Retur Barang</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-3">

          <form id="formTambahRetur">
            <input type="hidden" name="iduser" value="<?= $iduser ?>">

            <div class="mb-3">
              <label class="form-label">Pilih Penerimaan</label>
              <select class="form-select" name="idpenerimaan" required>
                <option value="">-- Pilih Penerimaan --</option>
                <?php while($row = $opsi->fetch_assoc()): ?>
                  <option value="<?= $row['idpenerimaan'] ?>">
                    <?= $row['idpenerimaan'] ?> — <?= $row['created_at'] ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Jumlah Retur</label>
              <input type="number" name="jumlah" class="form-control" min="1" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Alasan</label>
              <textarea name="alasan" class="form-control" required></textarea>
            </div>
          </form>

        </div>

        <div class="modal-footer border-0">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-success" onclick="saveTambahRetur()">Simpan</button>
        </div>

      </div>
    </div>
  </div>


  <footer>© <?= date('Y'); ?> — Admin Panel 💛</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- 🔥 SCRIPT AJAX -->
  <script>
  function saveTambahRetur() {
      const form = document.getElementById("formTambahRetur");
      const data = new FormData(form);

      fetch("ProsesTambahRetur.php", {
          method: "POST",
          body: data
      })
      .then(res => res.text())
      .then(res => {
          var modal = bootstrap.Modal.getInstance(document.getElementById("modalTambahRetur"));
          modal.hide();
          alert("Retur berhasil ditambahkan!");
          location.reload();
      })
      .catch(err => alert("Gagal: " + err));
  }
  </script>

</body>
</html>
