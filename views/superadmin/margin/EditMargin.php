<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

if (!$conn) { die("Koneksi database gagal!"); }

if (!isset($_GET['id'])) {
    die("ID Margin tidak ditemukan!");
}

$id = intval($_GET['id']);

// Ambil data margin yang mau diedit
$query = "SELECT * FROM margin_penjualan WHERE idmargin_penjualan = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Data margin tidak ditemukan.");
}

$data = $result->fetch_assoc();

// Ambil data kategori (untuk dropdown)
$qKategori = $conn->query("SELECT idkategori, nama_kategori FROM kategori WHERE status = 1 ORDER BY nama_kategori ASC");

// Ambil data vendor (untuk dropdown)
$qVendor = $conn->query("SELECT idvendor, nama_vendor FROM vendor WHERE status = 1 ORDER BY nama_vendor ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Margin Penjualan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
  body { background: rgba(0,0,0,0.5); font-family: Poppins, sans-serif; }
  .modal-content { background:#4B0082; color:#fff; border-radius:15px; }
  .form-label { font-weight:600; color:#FFD54F; }
  .form-control, .form-select {
      background-color:#fff;
      color:#4B0082;
      border:1px solid #C13584;
  }
  .btn-cancel { background:#6c757d; color:#fff; font-weight:600; }
  .btn-submit { background:#FFD54F; color:#4B0082; font-weight:600; }
</style>
</head>

<body class="p-5">

<div class="container mt-4">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Margin Penjualan</h4>
        <a href="MarginPenjualan.php" class="btn-close btn-close-white"></a>
      </div>

      <form method="POST" action="ProsesEditMargin.php">
        <div class="modal-body">

          <input type="hidden" name="idmargin_penjualan" value="<?= $data['idmargin_penjualan']; ?>">

          <div class="mb-3">
            <label class="form-label">Persentase Margin (%)</label>
            <input type="number" step="0.01" name="persen" class="form-control"
                   value="<?= htmlspecialchars($data['persen']); ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Tipe Kebijakan</label>
            <select name="tipe_kebijakan" id="tipe_kebijakan" class="form-select" required onchange="toggleTarget()">
                <option value="global" <?= $data['tipe_kebijakan']=='global'?'selected':''; ?>>Global / Default</option>
                <option value="kategori" <?= $data['tipe_kebijakan']=='kategori'?'selected':''; ?>>Berdasarkan Kategori</option>
                <option value="vendor" <?= $data['tipe_kebijakan']=='vendor'?'selected':''; ?>>Berdasarkan Vendor</option>
            </select>
          </div>

          <div class="mb-3" id="target_wrapper" style="display:none;">
            <label class="form-label">Pilih Target</label>
            <select name="idtarget" id="idtarget" class="form-select">
              <!-- Akan diisi via JS -->
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="1" <?= $data['status']==1?'selected':''; ?>>Aktif</option>
              <option value="0" <?= $data['status']==0?'selected':''; ?>>Nonaktif</option>
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <a href="MarginPenjualan.php" class="btn btn-cancel">Batal</a>
          <button type="submit" class="btn btn-submit">Simpan Perubahan</button>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
let kategoriOptions = `<?php
  $out = '';
  if ($qKategori->num_rows > 0) {
      while($k = $qKategori->fetch_assoc()) {
          $out .= '<option value="'.$k['idkategori'].'">'.$k['nama_kategori'].'</option>';
      }
  }
  echo $out;
?>`;

let vendorOptions = `<?php
  $v = '';
  if ($qVendor->num_rows > 0) {
      while($n = $qVendor->fetch_assoc()) {
          $v .= '<option value="'.$n['idvendor'].'">'.$n['nama_vendor'].'</option>';
      }
  }
  echo $v;
?>`;

let tipe = "<?= $data['tipe_kebijakan']; ?>";
let targetValue = "<?= $data['idtarget']; ?>";

function toggleTarget() {
    let tipeSelect = document.getElementById('tipe_kebijakan').value;
    let wrapper = document.getElementById('target_wrapper');
    let idtarget = document.getElementById('idtarget');

    wrapper.style.display = "none";
    idtarget.innerHTML = "";

    if (tipeSelect === 'kategori') {
        wrapper.style.display = "block";
        idtarget.innerHTML = kategoriOptions;
    } else if (tipeSelect === 'vendor') {
        wrapper.style.display = "block";
        idtarget.innerHTML = vendorOptions;
    }
    idtarget.value = targetValue;
}

// load saat awal
toggleTarget();
</script>

</body>
</html>
