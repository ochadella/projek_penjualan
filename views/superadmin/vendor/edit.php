<?php
include "koneksi.php";

// Ambil ID barang dari URL
$id = $_GET['id'];

// Ambil data barang berdasarkan ID
$q = mysqli_query($conn, "SELECT * FROM barang WHERE idbarang='$id'");
$data = mysqli_fetch_assoc($q);

// Update data jika form disubmit
if (isset($_POST['update'])) {
    $nama     = $_POST['nama'];
    $idsatuan = $_POST['idsatuan'];

    $update = mysqli_query($conn, "UPDATE barang SET nama='$nama', idsatuan='$idsatuan' WHERE idbarang='$id'");

    if ($update) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal update: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        h2 {
            color: #333;
        }
        form {
            max-width: 400px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        label {
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin: 8px 0 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #0056b3;
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

<h2>Edit Barang</h2>

<!-- Tombol Navigasi -->
<div class="nav-buttons">
    <a href="dashboard.php">⬅ Kembali ke Dashboard</a>
    <a href="barang.php">📦 Kembali ke Data Barang</a>
</div>

<form method="POST">
    <label>Nama Barang:</label>
    <input type="text" name="nama" value="<?php echo $data['nama']; ?>">

    <label>ID Satuan:</label>
    <input type="text" name="idsatuan" value="<?php echo $data['idsatuan']; ?>">

    <input type="submit" name="update" value="Update">
</form>

</body>
</html>
