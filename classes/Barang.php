<?php include "koneksi.php"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Barang</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Data Barang</h2>

    <div class="d-flex justify-content-between mb-3">
        <!-- Tombol kembali ke Dashboard -->
        <a href="dashboard.php" class="btn btn-secondary">⬅ Kembali ke Dashboard</a>
        
        <!-- Tombol tambah barang -->
        <a href="tambah.php" class="btn btn-primary">+ Tambah Barang</a>
    </div>

    <table id="tabelku" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Ambil data dari view
            $data = mysqli_query($conn, "SELECT * FROM view_barang_aktif");

            // Jika query gagal
            if (!$data) {
                die("Query gagal: " . mysqli_error($conn));
            }

            // Jika data ada
            if (mysqli_num_rows($data) > 0) {
                while ($row = mysqli_fetch_assoc($data)) {
                    echo "<tr>
                            <td>".$row['idbarang']."</td>
                            <td>".$row['nama']."</td>
                            <td>".$row['jenis']."</td>
                            <td>".$row['harga']."</td>
                            <td>
                                <a href='edit.php?id=".$row['idbarang']."' class='btn btn-sm btn-warning'>Edit</a>
                                <a href='hapus.php?id=".$row['idbarang']."' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin mau hapus data ini?');\">Hapus</a>
                            </td>
                          </tr>";
                }
            } else {
                // Jika tidak ada data
                echo "<tr>
                        <td colspan='5' class='text-center'>Belum ada data barang.</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- jQuery & DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#tabelku').DataTable({
            "columnDefs": [
                { "orderable": false, "targets": 4 } // kolom Aksi (index ke-4)
            ]
        });
    });
</script>
</body>
</html>
