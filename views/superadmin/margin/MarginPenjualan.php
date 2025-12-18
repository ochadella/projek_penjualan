<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

if (!$conn) {
    die("Koneksi database gagal!");
}

// Ambil data Margin Penjualan (Tampilan Utama)
$queryMargin = "SELECT * FROM margin_penjualan ORDER BY idmargin_penjualan ASC";
$resultMargin = $conn->query($queryMargin);

// Ambil data kategori
$queryKategori = "SELECT idkategori, nama_kategori FROM kategori WHERE status = 1 ORDER BY nama_kategori ASC";
$resultKategori = $conn->query($queryKategori);

// Ambil data vendor
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

    .modal-content {
        background: #4B0082;
        color: #fff;
        border-radius: 16px;
    }
    .modal-header { border-bottom: 1px solid #C13584; }
    .modal-footer { border-top: 1px solid #C13584; }

    .form-label-modal {
      font-weight: 600;
      color: #FFD54F;
    }
    .form-control-modal, .form-select-modal {
      background-color: rgba(255, 255, 255, 0.9);
      color: #4B0082;
      border: 1px solid #C13584;
    }
    .form-control-modal:focus, .form-select-modal:focus {
      background-color: #fff;
      border-color: #FFD54F;
      box-shadow: 0 0 0 0.25rem rgba(255, 213, 79, 0.4);
      color: #4B0082;
    }
    .form-select-modal option {
        background: #fff;
        color: #4B0082;
    }

    .btn-cancel {
        background-color: #6c757d;
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        padding: 8px 18px;
        transition: 0.3s;
    }
    .btn-cancel:hover {
        background-color: #5a6268;
        color: #fff;
    }

    .btn-submit {
        background-color: #FFD54F;
        color: #4B0082;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        padding: 8px 18px;
        transition: 0.3s;
    }
    .btn-submit:hover {
        background-color: #F27121;
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
                    <span class="badge bg-info text-dark">
                      <?= htmlspecialchars(ucwords($row['tipe_kebijakan'] ?? 'Global')) ?>
                    </span>
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
                    <!-- EDIT: buka modal edit -->
                    <button
                      type="button"
                      class="btn btn-action-edit btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#editMarginModal"
                      data-id="<?= htmlspecialchars($row['idmargin_penjualan']) ?>"
                      data-persen="<?= htmlspecialchars($row['persen']) ?>"
                      data-tipe="<?= htmlspecialchars($row['tipe_kebijakan']) ?>"
                      data-targetid="<?= htmlspecialchars($row['idtarget']) ?>"
                      data-status="<?= htmlspecialchars($row['status']) ?>"
                    >
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>

                    <!-- HAPUS -->
                    <a href="HapusMargin.php?id=<?= htmlspecialchars($row['idmargin_penjualan']) ?>"
                       class="btn btn-action-hapus btn-sm"
                       onclick="return confirm('Yakin ingin menghapus kebijakan margin ini? Tindakan ini tidak dapat dibatalkan.');">
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

  <!-- ======================= MODAL TAMBAH ======================= -->
  <div class="modal fade" id="tambahMarginModal" tabindex="-1" aria-labelledby="tambahMarginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tambahMarginModalLabel"><i class="bi bi-plus-circle"></i> Tambah Kebijakan Margin</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="TambahMargin.php">
          <div class="modal-body">
            <div class="mb-3">
              <label for="persen" class="form-label-modal">Persentase Margin (%)</label>
              <input type="number" step="0.01" min="0.01" max="100" class="form-control form-control-modal" id="persen" name="persen" placeholder="cth: 15.00" required>
            </div>

            <div class="mb-3">
              <label class="form-label-modal">Tipe Kebijakan Margin</label>
              <select class="form-select form-select-modal" id="tipe_kebijakan_modal" name="tipe_kebijakan" required onchange="toggleTargetModal()">
                <option value="">-- Pilih Tipe --</option>
                <option value="global">Global / Default</option>
                <option value="kategori">Berdasarkan Kategori Barang</option>
                <option value="vendor">Berdasarkan Vendor Pemasok</option>
              </select>
            </div>

            <div id="target-container-modal" class="mb-3" style="display:none;">
              <label for="idtarget" class="form-label-modal">Pilih Target</label>
              <select class="form-select form-select-modal" id="idtarget_modal" name="idtarget"></select>
            </div>
            
            <div class="mb-3">
              <label class="form-label-modal">Status Kebijakan</label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="status" id="statusAktifModal" value="1" checked>
                <label class="form-check-label text-success" for="statusAktifModal">Aktif (Langsung Diterapkan)</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="status" id="statusNonaktifModal" value="0">
                <label class="form-check-label text-light" for="statusNonaktifModal">Nonaktif (Hanya Draft)</label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-submit">Simpan Kebijakan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ======================= MODAL EDIT ======================= -->
  <div class="modal fade" id="editMarginModal" tabindex="-1" aria-labelledby="editMarginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editMarginModalLabel"><i class="bi bi-pencil-square"></i> Edit Kebijakan Margin</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="ProsesEditMargin.php">
          <div class="modal-body">
            
            <input type="hidden" name="idmargin_penjualan" id="edit_id">

            <div class="mb-3">
              <label class="form-label-modal">Persentase Margin (%)</label>
              <input type="number" step="0.01" min="0.01" max="100"
                     class="form-control form-control-modal"
                     id="edit_persen" name="persen" required>
            </div>

            <div class="mb-3">
              <label class="form-label-modal">Tipe Kebijakan</label>
              <select class="form-select form-select-modal" id="edit_tipe" name="tipe_kebijakan" onchange="editToggleTarget()" required>
                <option value="global">Global / Default</option>
                <option value="kategori">Berdasarkan Kategori Barang</option>
                <option value="vendor">Berdasarkan Vendor Pemasok</option>
              </select>
            </div>

            <div class="mb-3" id="edit_target_wrapper" style="display:none;">
              <label class="form-label-modal">Pilih Target</label>
              <select class="form-select form-select-modal" id="edit_target" name="idtarget"></select>
            </div>

            <div class="mb-3">
              <label class="form-label-modal">Status Kebijakan</label>
              <select class="form-select form-select-modal" id="edit_status" name="status">
                <option value="1" class="text-dark">Aktif</option>
                <option value="0" class="text-dark">Nonaktif</option>
              </select>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-submit">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer>© <?= date('Y'); ?> — Superadmin Panel 💛</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // OPTION KATEGORI
    const kategoriOptions = '<?php 
        $kategori_opts = '<option value="0">-- Pilih Kategori --</option>';
        if ($resultKategori && $resultKategori->num_rows > 0) {
            while ($rowK = $resultKategori->fetch_assoc()) {
                $kategori_opts .= '<option value="' . htmlspecialchars($rowK['idkategori']) . '">' . htmlspecialchars($rowK['nama_kategori']) . '</option>';
            }
            $resultKategori->data_seek(0);
        } else {
             $kategori_opts .= '<option value="" disabled>Data Kategori Kosong</option>';
        }
        echo $kategori_opts;
    ?>';
    
    // OPTION VENDOR
    const vendorOptions = '<?php 
        $vendor_opts = '<option value="0">-- Pilih Vendor --</option>';
        if ($resultVendor && $resultVendor->num_rows > 0) {
            while ($rowV = $resultVendor->fetch_assoc()) {
                $vendor_opts .= '<option value="' . htmlspecialchars($rowV['idvendor']) . '">' . htmlspecialchars($rowV['nama_vendor']) . '</option>';
            }
            $resultVendor->data_seek(0);
        } else {
            $vendor_opts .= '<option value="" disabled>Data Vendor Kosong</option>';
        }
        echo $vendor_opts;
    ?>';

    // ===== TAMPIL / HIDE TARGET DI MODAL TAMBAH =====
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

    // ===== TAMPIL / HIDE TARGET DI MODAL EDIT =====
    function editToggleTarget() {
        const tipe = document.getElementById('edit_tipe').value;
        const wrapper = document.getElementById('edit_target_wrapper');
        const select = document.getElementById('edit_target');

        wrapper.style.display = 'none';
        select.removeAttribute('required');

        if (tipe === 'kategori') {
            wrapper.style.display = 'block';
            select.innerHTML = kategoriOptions;
            select.setAttribute('required', 'required');
        } else if (tipe === 'vendor') {
            wrapper.style.display = 'block';
            select.innerHTML = vendorOptions;
            select.setAttribute('required', 'required');
        }
    }

    // ===== ISI DATA KE MODAL EDIT SAAT TOMBOL EDIT DIKLIK =====
    const editModal = document.getElementById('editMarginModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        const id = button.getAttribute('data-id');
        const persen = button.getAttribute('data-persen');
        const tipe = button.getAttribute('data-tipe') || 'global';
        const targetid = button.getAttribute('data-targetid') || '0';
        const status = button.getAttribute('data-status') || '1';

        document.getElementById('edit_id').value = id;
        document.getElementById('edit_persen').value = persen;
        document.getElementById('edit_tipe').value = tipe;
        document.getElementById('edit_status').value = status;

        editToggleTarget();

        if (tipe === 'kategori' || tipe === 'vendor') {
            document.getElementById('edit_target').value = targetid;
        }
    });

    document.addEventListener('DOMContentLoaded', toggleTargetModal);
  </script>
</body>
</html>
