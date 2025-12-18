<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

if (!$conn) {
    die("Koneksi database gagal!");
}

// Urut berdasarkan ID ASC agar sesuai database (1,2,3,4)
$queryMargin = "SELECT * FROM margin_penjualan ORDER BY idmargin_penjualan ASC";
$resultMargin = $conn->query($queryMargin);

// Ambil data kategori untuk modal
$queryKategori = "SELECT idkategori, nama_kategori FROM kategori WHERE status = 1 ORDER BY nama_kategori ASC";
$resultKategori = $conn->query($queryKategori);

// Ambil data vendor untuk modal
$queryVendor = "SELECT idvendor, nama_vendor FROM vendor WHERE status = 1 ORDER BY nama_vendor ASC";
$resultVendor = $conn->query($queryVendor);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>💰 Margin Penjualan — Superadmin</title>
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
    
    /* Style Tabel - Konsisten dengan Data Satuan */
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

    /* Tombol Edit - Kuning */
    .btn-action-edit {
        background-color: #FFD54F;
        color: #333;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        padding: 6px 15px;
        transition: 0.3s;
        margin-right: 5px;
    }
    .btn-action-edit:hover {
        background-color: #FFC107;
        color: #333;
    }

    /* Tombol Hapus - Merah seperti Data Satuan */
    .btn-action-hapus {
        background-color: #dc3545;
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        padding: 6px 15px;
        transition: 0.3s;
    }
    .btn-action-hapus:hover {
        background-color: #c82333;
        color: #fff;
    }

    /* Tombol Tambah - Kuning (sebelah kanan) */
    .btn-add {
        background-color: #FFD54F;
        color: #333;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        padding: 8px 18px;
        transition: 0.3s;
    }
    .btn-add:hover {
        background-color: #FFC107;
        color: #333;
    }

    /* Tombol Kembali - Ungu (sebelah kiri) */
    .btn-back {
      background-color: #9370DB;
      color: #fff;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      padding: 8px 18px;
      transition: 0.3s;
    }
    .btn-back:hover {
      background-color: #7B68EE;
      color: #fff;
    }
    
    /* ===== STYLING MODAL FORM - SIMPLE SEPERTI DATA SATUAN ===== */
    .modal-content {
        background: #fff;
        color: #333;
        border-radius: 12px;
        border: none;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
    }
    
    .modal-header {
        background-color: #FFD54F;
        color: #333;
        border-bottom: none;
        padding: 15px 20px;
        border-radius: 12px 12px 0 0;
    }
    
    .modal-title {
        color: #333;
        font-weight: 700;
        font-size: 1.3rem;
    }
    
    .btn-close {
        filter: brightness(0);
    }
    
    .modal-body {
        padding: 25px;
        background: #fff;
    }
    
    .modal-footer {
        border-top: 1px solid #dee2e6;
        padding: 15px 20px;
        background: #fff;
        border-radius: 0 0 12px 12px;
    }
    
    /* Label Form - Hitam */
    .form-label {
      font-weight: 600;
      color: #333;
      font-size: 0.95rem;
      margin-bottom: 8px;
    }
    
    /* Input & Select - Simple */
    .form-control, .form-select {
      background-color: #fff;
      color: #333;
      border: 1px solid #ced4da;
      border-radius: 6px;
      padding: 10px 12px;
      transition: border-color 0.3s;
    }
    
    .form-control:focus, .form-select:focus {
      background-color: #fff;
      border-color: #FFD54F;
      box-shadow: 0 0 0 0.2rem rgba(255, 213, 79, 0.25);
      color: #333;
      outline: none;
    }

    .form-control::placeholder {
        color: #999;
    }

    /* Dropdown Option */
    .form-select option {
        background: #fff;
        color: #333;
    }

    /* Checkbox Styling */
    .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 3px;
        cursor: pointer;
    }
    
    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
    
    .form-check-label {
        color: #333;
        font-weight: 500;
        cursor: pointer;
        margin-left: 8px;
    }

    /* Tombol Modal */
    .btn-batal {
        background-color: #6c757d;
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        padding: 8px 20px;
        transition: 0.3s;
    }
    .btn-batal:hover {
        background-color: #5a6268;
        color: #fff;
    }

    .btn-simpan {
        background-color: #198754;
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        padding: 8px 20px;
        transition: 0.3s;
    }
    .btn-simpan:hover {
        background-color: #157347;
        color: #fff;
    }

    footer { text-align:center; padding:25px; color:#fff7d0; font-size:0.9rem; margin-top:40px;}
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="../../../interface/dashboard_superadmin.php">👑 Superadmin</a>
      <div class="collapse navbar-collapse justify-content-end">
        <ul class="navbar-nav d-flex align-items-center">
          <li class="nav-item"><a class="nav-link" href="../barang/DataBarang.php">📦 Barang</a></li>
          <li class="nav-item"><a class="nav-link" href="../vendor/DataVendor.php">🏢 Vendor</a></li>
          <li class="nav-item"><a class="nav-link" href="../penjualan/DataPenjualan.php">🛒 Penjualan</a></li>
          <li class="nav-item"><a href="../../../auth/logout.php" class="btn btn-logout">🚪 Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <h1 class="text-center mt-5"><i class="bi bi-graph-up"></i> Kebijakan Margin Penjualan</h1>
  <p class="lead text-center">Atur kebijakan persentase keuntungan penjualan yang berlaku di sistem.</p>

  <div class="container mt-4">
    <div class="table-container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 text-warning"><i class="bi bi-cash-stack"></i> Daftar Kebijakan Margin</h5>
        <div>
            <a href="../../../interface/dashboard_superadmin.php" class="btn btn-back me-2">
              <i class="bi bi-arrow-left-circle"></i> Kembali
            </a>
            <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#tambahMarginModal">
              <i class="bi bi-plus-circle"></i> Tambah Kebijakan
            </button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead>
            <tr>
              <th>No. Urut</th>
              <th>Persentase</th>
              <th>Tipe/Target Kebijakan</th>
              <th>Status</th>
              <th>Dibuat Oleh</th>
              <th>Diperbarui</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($resultMargin && $resultMargin->num_rows > 0): ?>
              <?php $no_urut = 1; ?>
              <?php while ($row = $resultMargin->fetch_assoc()): ?>
                <tr>
                  <td><?= $no_urut ?></td>
                  <td><?= htmlspecialchars(number_format($row['persen'] ?? 0, 2)) ?>%</td>
                  <td>
                    <span class="badge bg-info text-dark"><?= htmlspecialchars(ucwords($row['tipe_kebijakan'] ?? 'Global')) ?></span>
                  </td>
                  <td>
                    <?php if (($row['status'] ?? 0) == 1): ?>
                      <span class="badge bg-success">Aktif</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Nonaktif</span>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($row['iduser'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($row['updated_at'] ?? '-') ?></td>
                  <td>
                    <a href="EditMargin.php?id=<?= htmlspecialchars($row['idmargin_penjualan']) ?>" class="btn btn-action-edit btn-sm">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <a href="HapusMargin.php?id=<?= htmlspecialchars($row['idmargin_penjualan']) ?>" class="btn btn-action-hapus btn-sm" onclick="return confirm('Yakin ingin menghapus kebijakan margin ini? Tindakan ini tidak dapat dibatalkan.');">
                        <i class="bi bi-trash"></i> Hapus
                    </a>
                  </td>
                </tr>
              <?php $no_urut++; ?>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-muted">Belum ada data kebijakan margin penjualan.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <!-- MODAL FORM TAMBAH KEBIJAKAN - SIMPLE -->
  <div class="modal fade" id="tambahMarginModal" tabindex="-1" aria-labelledby="tambahMarginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tambahMarginModalLabel"><i class="bi bi-plus-circle"></i> Tambah Kebijakan Margin</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="TambahMargin.php">
          <div class="modal-body">
            
            <div class="mb-3">
              <label for="persen" class="form-label">Persentase Margin (%)</label>
              <input type="number" step="0.01" min="0.01" max="100" class="form-control" id="persen" name="persen" placeholder="cth: 15.00" required>
            </div>

            <div class="mb-3">
              <label for="tipe_kebijakan" class="form-label">Tipe Kebijakan Margin</label>
              <select class="form-select" id="tipe_kebijakan_modal" name="tipe_kebijakan" required onchange="toggleTargetModal()">
                <option value="">-- Pilih Tipe --</option>
                <option value="global">Global / Default</option>
                <option value="kategori">Berdasarkan Kategori Barang</option>
                <option value="vendor">Berdasarkan Vendor Pemasok</option>
              </select>
            </div>

            <div id="target-container-modal" class="mb-3" style="display:none;">
              <label for="idtarget" class="form-label">Pilih Target</label>
              <select class="form-select" id="idtarget_modal" name="idtarget">
              </select>
            </div>
            
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="status" id="statusAktif" value="1" checked>
                <label class="form-check-label" for="statusAktif">Aktif</label>
              </div>
            </div>
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-batal" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-simpan">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer>© <?= date('Y'); ?> — Superadmin Panel 💛</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    const kategoriOptions = '<?php 
        $kategori_opts = '<option value="0">-- Pilih Kategori --</option>';
        if ($resultKategori && $resultKategori->num_rows > 0) {
            while ($row = $resultKategori->fetch_assoc()) {
                $kategori_opts .= '<option value="' . htmlspecialchars($row['idkategori']) . '">' . htmlspecialchars($row['nama_kategori']) . '</option>';
            }
            $resultKategori->data_seek(0);
        } else {
             $kategori_opts .= '<option value="" disabled>Data Kategori Kosong</option>';
        }
        echo $kategori_opts;
    ?>';
    
    const vendorOptions = '<?php 
        $vendor_opts = '<option value="0">-- Pilih Vendor --</option>';
        if ($resultVendor && $resultVendor->num_rows > 0) {
            while ($row = $resultVendor->fetch_assoc()) {
                $vendor_opts .= '<option value="' . htmlspecialchars($row['idvendor']) . '">' . htmlspecialchars($row['nama_vendor']) . '</option>';
            }
            $resultVendor->data_seek(0);
        } else {
            $vendor_opts .= '<option value="" disabled>Data Vendor Kosong</option>';
        }
        echo $vendor_opts;
    ?>';

    function toggleTargetModal() {
        const tipe = document.getElementById('tipe_kebijakan_modal').value;
        const container = document.getElementById('target-container-modal');
        const targetSelect = document.getElementById('idtarget_modal');
        
        container.style.display = 'none';
        targetSelect.removeAttribute('required');
        
        if (tipe === 'kategori') {
            targetSelect.innerHTML = kategoriOptions;
            container.style.display = 'block';
            targetSelect.setAttribute('required', 'required');
        } else if (tipe === 'vendor') {
            targetSelect.innerHTML = vendorOptions;
            container.style.display = 'block';
            targetSelect.setAttribute('required', 'required');
        }
    }
    
    document.addEventListener('DOMContentLoaded', toggleTargetModal);
    
  </script>
</body>
</html>