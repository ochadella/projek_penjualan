<?php
require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

$id = $_GET['id'] ?? 0;

$q = $conn->query("SELECT * FROM pengadaan WHERE idpengadaan = '$id'");
$data = $q->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan");
}

// ambil user
$users = $conn->query("SELECT iduser, username FROM user");

// ambil vendor
$vendors = $conn->query("SELECT idvendor, nama_vendor FROM vendor");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'];
    $vendor = $_POST['vendor'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    $conn->query("
        UPDATE pengadaan
        SET 
            user_iduser = '$user',
            vendor_idvendor = '$vendor',
            timestamp = '$tanggal',
            status = '$status'
        WHERE idpengadaan = '$id'
    ");

    header("Location: DataPengadaan.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Pengadaan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: Poppins;
    background: #4B0082;
    color: white;
}
.card {
    background: rgba(43,15,74,0.85);
    padding: 25px;
    border-radius: 20px;
}
</style>
</head>

<body>

<div class="container mt-5">
    <div class="card">

        <h3 class="text-warning">Edit Pengadaan</h3>

        <form method="POST">

            <label>User</label>
            <select name="user" class="form-control mb-2">
                <?php while($u = $users->fetch_assoc()): ?>
                    <option value="<?= $u['iduser'] ?>" <?= $u['iduser']==$data['user_iduser']?'selected':'' ?>>
                        <?= $u['username'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Vendor</label>
            <select name="vendor" class="form-control mb-2">
                <?php while($v = $vendors->fetch_assoc()): ?>
                    <option value="<?= $v['idvendor'] ?>" <?= $v['idvendor']==$data['vendor_idvendor']?'selected':'' ?>>
                        <?= $v['nama_vendor'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Tanggal</label>
            <input type="datetime-local" name="tanggal" class="form-control mb-2"
                   value="<?= date('Y-m-d\TH:i', strtotime($data['timestamp'])) ?>">

            <label>Status</label>
            <select name="status" class="form-control mb-3">
                <option value="A" <?= $data['status']=='A'?'selected':'' ?>>Aktif</option>
                <option value="N" <?= $data['status']=='N'?'selected':'' ?>>Nonaktif</option>
            </select>

            <button class="btn btn-warning">Simpan</button>
            <a href="DataPengadaan.php" class="btn btn-secondary">Batal</a>

        </form>

    </div>
</div>

</body>
</html>
