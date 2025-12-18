<?php
require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

$id = intval($_GET['id']);

// ================================
//   AMBIL HEADER PENERIMAAN
//   PAKAI VIEW YANG BENAR
// ================================
$q = $conn->query("
    SELECT 
        idpenerimaan,
        tanggal_penerimaan,
        diterima_oleh,
        SUM(sub_total_terima) AS total_nilai
    FROM view_penerimaan_barang
    WHERE idpenerimaan = $id
    GROUP BY idpenerimaan, tanggal_penerimaan, diterima_oleh
");

$data = $q->fetch_assoc();

if (!$data) {
    echo "<p class='text-danger'>Data penerimaan tidak ditemukan.</p>";
    exit;
}
?>

<form id="formEditPenerimaan">
    <div class="mb-3">
        <label class="form-label">Tanggal Penerimaan</label>
        <input type="datetime-local" 
               name="tanggal_penerimaan" 
               class="form-control"
               value="<?= date('Y-m-d\TH:i', strtotime($data['tanggal_penerimaan'])) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
            <option value="1" selected>Aktif</option>
            <option value="0">Nonaktif</option>
        </select>
    </div>

    <button type="button" class="btn btn-success"
            onclick="saveEditPenerimaan(<?= $data['idpenerimaan'] ?>)">
        Simpan
    </button>
</form>
