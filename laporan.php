<?php include "koneksi.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        h2 {
            margin-bottom: 20px;
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
        form {
            margin-bottom: 20px;
        }
        input[type="date"], select {
            padding: 6px;
            margin-right: 10px;
        }
        input[type="submit"] {
            padding: 6px 12px;
            background-color: #007bff;
            border: none;
            color: white;
            cursor: pointer;
            font-weight: bold;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f8f9fa;
        }
        .total {
            font-weight: bold;
            background-color: #e9ecef;
        }
    </style>
</head>
<body>

<h2>📊 Laporan Penjualan</h2>

<div class="nav-buttons">
    <a href="dashboard.php">⬅ Kembali ke Dashboard</a>
</div>

<form method="get">
    <label>Dari Tanggal: </label>
    <input type="date" name="dari" required>
    <label>Sampai: </label>
    <input type="date" name="sampai" required>

    <!-- Filter user (opsional) -->
    <label>Kasir/User: </label>
    <select name="iduser">
        <option value="">-- Semua --</option>
        <?php
        $u = mysqli_query($conn, "SELECT * FROM user");
        while ($usr = mysqli_fetch_assoc($u)) {
            echo "<option value='".$usr['iduser']."'>".$usr['username']."</option>";
        }
        ?>
    </select>

    <input type="submit" value="Filter">
</form>

<table>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>ID Barang</th>
        <th>Nama Barang</th>
        <th>Jumlah</th>
        <th>Harga</th>
        <th>Total</th>
        <th>Margin (Opsional)</th>
        <th>User</th>
    </tr>

    <?php
    $no = 1;
    $totalSemua = 0;
    $totalMargin = 0;

    if (isset($_GET['dari']) && isset($_GET['sampai'])) {
        $dari   = $_GET['dari'];
        $sampai = $_GET['sampai'];
        $iduser = $_GET['iduser'];

        $whereUser = "";
        if (!empty($iduser)) {
            $whereUser = "AND p.iduser='$iduser'";
        }

        $sql = mysqli_query($conn, "
            SELECT p.tanggal, b.idbarang, b.nama AS nama_barang, d.jumlah, b.harga, u.username AS nama_user,
                   (d.jumlah * b.harga) AS total,
                   (d.jumlah * (b.harga - IFNULL(b.harga_beli,0))) AS margin
            FROM penjualan p
            JOIN detail_penjualan d ON p.idpenjualan = d.idpenjualan
            JOIN barang b ON d.idbarang = b.idbarang
            LEFT JOIN user u ON p.iduser = u.iduser
            WHERE p.tanggal BETWEEN '$dari' AND '$sampai' $whereUser
            ORDER BY p.tanggal ASC
        ");

        while ($row = mysqli_fetch_assoc($sql)) {
            $totalSemua += $row['total'];
            $totalMargin += $row['margin'];

            echo "<tr>
                    <td>".$no++."</td>
                    <td>".$row['tanggal']."</td>
                    <td>".$row['idbarang']."</td>
                    <td>".$row['nama_barang']."</td>
                    <td>".$row['jumlah']."</td>
                    <td>Rp ".number_format($row['harga'],0,',','.')."</td>
                    <td>Rp ".number_format($row['total'],0,',','.')."</td>
                    <td>Rp ".number_format($row['margin'],0,',','.')."</td>
                    <td>".$row['nama_user']."</td>
                  </tr>";
        }

        echo "<tr class='total'>
                <td colspan='6'>TOTAL</td>
                <td>Rp ".number_format($totalSemua,0,',','.')."</td>
                <td>Rp ".number_format($totalMargin,0,',','.')."</td>
                <td>-</td>
              </tr>";
    } else {
        echo "<tr><td colspan='9'>Silakan pilih tanggal terlebih dahulu</td></tr>";
    }
    ?>
</table>

</body>
</html>
