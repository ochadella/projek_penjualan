<?php include "koneksi.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        h2 {
            margin-bottom: 20px;
        }
        table {
            border-collapse: collapse;
        }
        td {
            padding: 8px;
        }
        input[type="text"], input[type="number"], select {
            width: 250px;
            padding: 6px;
        }
        input[type="submit"] {
            padding: 8px 16px;
            background-color: #28a745;
            border: none;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .back-link {
            margin-bottom: 20px;
            display: inline-block;
        }
        .nav-buttons {
            margin-bottom: 20px;
        }
        .nav-buttons a {
            margin-right: 10px;
            padding: 8px 14px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .nav-buttons a:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
<h2>Tambah Barang</h2>

<div class="nav-buttons">
    <a href="dashboard.php">⬅ Kembali ke Dashboard</a>
    <a href="barang.php">⬅ Kembali ke Data Barang</a>
</div>

<form method="post">
    <table>
        <tr>
            <td>Nama Barang</td>
            <td><input type="text" name="nama" required></td>
        </tr>
        <tr>
            <td>Jenis</td>
            <td>
                <select name="jenis" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="ATK">ATK</option>
                    <option value="Makanan">Makanan</option>
                    <option value="Elektronik">Elektronik</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Satuan</td>
            <td>
                <select name="idsatuan" required>
                    <option value="">-- Pilih Satuan --</option>
                    <?php
                    $sat = mysqli_query($conn, "SELECT * FROM satuan");
                    while ($row = mysqli_fetch_assoc($sat)) {
                        echo "<option value='".$row['idsatuan']."'>".$row['nama_satuan']."</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Jumlah</td>
            <td><input type="number" name="jumlah" required></td>
        </tr>
        <tr>
            <td>Status</td>
            <td>
                <select name="status" required>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Harga</td>
            <td><input type="number" name="harga" required></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" name="simpan" value="Simpan"></td>
        </tr>
    </table>
</form>

<?php
if (isset($_POST['simpan'])) {
    $nama     = $_POST['nama'];
    $jenis    = $_POST['jenis'];
    $idsatuan = $_POST['idsatuan'];
    $jumlah   = $_POST['jumlah'];
    $status   = $_POST['status'];
    $harga    = $_POST['harga'];

    // ambil nama_satuan berdasarkan idsatuan
    $qSat = mysqli_query($conn, "SELECT nama_satuan FROM satuan WHERE idsatuan='$idsatuan'");
    $rowSat = mysqli_fetch_assoc($qSat);
    $nama_satuan = $rowSat['nama_satuan'];

    $query = "INSERT INTO barang (jenis, nama, idsatuan, nama_satuan, jumlah, status, harga) 
              VALUES ('$jenis', '$nama', '$idsatuan', '$nama_satuan', '$jumlah', '$status', '$harga')";
    
    if (mysqli_query($conn, $query)) {
        echo "<div style='margin-top:15px; padding:10px; background:#d4edda; 
            color:#155724; border:1px solid #c3e6cb; border-radius:5px;'>
        ✅ Data berhasil disimpan! 
        <a href='barang.php' style='font-weight:bold; color:#0c5460; text-decoration:none;'>Lihat Data</a>
      </div>";
    } else {
        echo "❌ Error: " . mysqli_error($conn);
    }
}
?>
</body>
</html>
