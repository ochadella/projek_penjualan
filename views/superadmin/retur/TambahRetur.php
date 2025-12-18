<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

// Ambil data penerimaan
$queryPenerimaan = "
SELECT 
    p.idpenerimaan,
    p.created_at
FROM penerimaan p
ORDER BY p.created_at DESC";
$hasilPenerimaan = $conn->query($queryPenerimaan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Retur Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="mb-4">➕ Tambah Retur Barang</h3>

        <form action="ProsesTambahRetur.php" method="POST">

            <!-- Pilih Penerimaan -->
            <div class="mb-3">
                <label class="form-label">Pilih Penerimaan</label>
                <select name="idpenerimaan" class="form-select" required>
                    <option value="">-- Pilih Penerimaan --</option>
                    <?php while ($row = $hasilPenerimaan->fetch_assoc()): ?>
                        <option value="<?= $row['idpenerimaan'] ?>">
                            ID <?= $row['idpenerimaan'] ?> — <?= $row['created_at'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Input Jumlah Retur -->
            <div class="mb-3">
                <label class="form-label">Jumlah Retur</label>
                <input type="number" name="jumlah" class="form-control" required min="1">
            </div>

            <!-- Input Alasan -->
            <div class="mb-3">
                <label class="form-label">Alasan Retur</label>
                <textarea name="alasan" class="form-control" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Retur</button>
            <a href="DataRetur.php" class="btn btn-secondary">Kembali</a>

        </form>
    </div>
</div>

</body>
</html>
