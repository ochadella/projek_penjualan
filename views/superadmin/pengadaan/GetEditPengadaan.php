<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan");
}

$id = intval($_GET['id']);

// Ambil data pengadaan
$query = "
    SELECT 
        p.idpengadaan,
        p.timestamp,
        p.user_iduser,
        p.vendor_idvendor,
        p.subtotal_nilai,
        p.ppn,
        p.total_nilai,
        p.status,
        u.username,
        v.nama_vendor
    FROM pengadaan p
    LEFT JOIN user u ON p.user_iduser = u.iduser
    LEFT JOIN vendor v ON p.vendor_idvendor = v.idvendor
    WHERE p.idpengadaan = $id
";

$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    die("Data tidak ditemukan");
}

$data = $result->fetch_assoc();

// Ambil semua user untuk dropdown
$queryUser = "SELECT iduser, username FROM user ORDER BY username";
$resultUser = $conn->query($queryUser);

// Ambil semua vendor untuk dropdown
$queryVendor = "SELECT idvendor, nama_vendor FROM vendor ORDER BY nama_vendor";
$resultVendor = $conn->query($queryVendor);
?>

<form id="formEditPengadaan">
    <div class="mb-3">
        <label class="form-label">Tanggal Pengadaan</label>
        <input type="datetime-local" class="form-control" name="timestamp" 
               value="<?= date('Y-m-d\TH:i', strtotime($data['timestamp'])) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">User</label>
        <select class="form-select" name="user_iduser" required>
            <?php while($user = $resultUser->fetch_assoc()): ?>
                <option value="<?= $user['iduser'] ?>" 
                    <?= $user['iduser'] == $data['user_iduser'] ? 'selected' : '' ?>>
                    <?= $user['username'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Vendor</label>
        <select class="form-select" name="vendor_idvendor" required>
            <?php while($vendor = $resultVendor->fetch_assoc()): ?>
                <option value="<?= $vendor['idvendor'] ?>" 
                    <?= $vendor['idvendor'] == $data['vendor_idvendor'] ? 'selected' : '' ?>>
                    <?= $vendor['nama_vendor'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Subtotal Nilai</label>
        <input type="number" class="form-control" name="subtotal_nilai" 
               value="<?= $data['subtotal_nilai'] ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">PPN</label>
        <input type="number" class="form-control" name="ppn" 
               value="<?= $data['ppn'] ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Total Nilai</label>
        <input type="number" class="form-control" name="total_nilai" 
               value="<?= $data['total_nilai'] ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Status</label>
        <select class="form-select" name="status" required>
            <option value="1" <?= $data['status'] == '1' ? 'selected' : '' ?>>Aktif</option>
            <option value="0" <?= $data['status'] == '0' ? 'selected' : '' ?>>Nonaktif</option>
        </select>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-warning" onclick="saveEditPengadaan(<?= $id ?>)">
            <i class="bi bi-save"></i> Simpan
        </button>
    </div>
</form>