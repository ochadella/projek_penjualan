<?php
require_once __DIR__ . '/../../../config/koneksi.php';
$db = new DBConnection();
$conn = $db->getConnection();

$id = $_GET['id'] ?? 0;

// Ambil header
$q = $conn->query("
    SELECT idpenjualan, kasir, created_at, total_nilai 
    FROM view_penjualan_detail
    WHERE idpenjualan = '$id'
    LIMIT 1
");

$data = $q->fetch_assoc();
if (!$data) {
    die("<div class='text-danger fw-bold'>Data tidak ditemukan.</div>");
}

// Ambil detail dengan iddetail_penjualan
$qDetail = $conn->query("
    SELECT iddetail_penjualan, jumlah, harga_satuan, subtotal
    FROM detail_penjualan
    WHERE idpenjualan = '$id'
");
?>

<div style="background:#2b0f4acc; color:white; padding:20px; border-radius:16px;">

    <h4 class="text-warning mb-3">
        <i class="bi bi-pencil-square"></i> Edit Penjualan #<?= $data['idpenjualan'] ?>
    </h4>

    <form action="UpdatePenjualan.php" method="POST">

        <input type="hidden" name="idpenjualan" value="<?= $data['idpenjualan'] ?>">
        <input type="hidden" id="total_nilai" name="total_nilai" value="<?= $data['total_nilai'] ?>">

        <div class="mb-3">
            <label class="form-label text-warning">Kasir</label>
            <input type="text" class="form-control" value="<?= $data['kasir'] ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label text-warning">Tanggal</label>
            <input type="text" name="tanggal" class="form-control" value="<?= $data['created_at'] ?>" readonly>

        </div>

        <h5 class="text-warning mt-4 mb-2">Detail Barang</h5>

        <table class="table table-hover bg-white text-center mt-2">
            <thead>
                <tr>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
            <?php $i = 0; while ($d = $qDetail->fetch_assoc()): ?>
                <tr>

                    <!-- ID DETAIL WAJIB ADA -->
                    <input type="hidden" name="iddetail[]" value="<?= $d['iddetail_penjualan'] ?>">

                    <td>
                        <input 
                            type="number" 
                            name="jumlah[]" 
                            class="form-control text-center input-jumlah"
                            value="<?= $d['jumlah'] ?>" 
                            min="1"
                            data-harga="<?= $d['harga_satuan'] ?>"
                            data-index="<?= $i ?>">
                    </td>

                    <td>Rp <?= number_format($d['harga_satuan'], 0, ',', '.') ?></td>

                    <td id="subtotal-<?= $i ?>">
                        Rp <?= number_format($d['subtotal'], 0, ',', '.') ?>
                    </td>

                    <input 
                        type="hidden" 
                        class="subtotal-value" 
                        value="<?= $d['subtotal'] ?>" 
                        id="subtotal-val-<?= $i ?>"
                    >

                </tr>
            <?php $i++; endwhile; ?>
            </tbody>
        </table>

        <hr>

        <p class="fw-bold text-end">
            Total Nilai: <span id="total_nilai_view">Rp <?= number_format($data['total_nilai'], 0, ',', '.') ?></span>
        </p>

        <div class="text-end">
            <button type="submit" class="btn btn-warning fw-bold">
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>

<!-- SCRIPT HITUNG TOTAL & SUBTOTAL -->
<script>
document.querySelectorAll(".input-jumlah").forEach(input => {
    input.addEventListener("input", () => {
        let harga = parseInt(input.dataset.harga);
        let jumlah = parseInt(input.value);
        let index = input.dataset.index;

        let subtotal = harga * jumlah;

        // Update tampilan subtotal
        document.getElementById("subtotal-" + index).innerText = 
            "Rp " + subtotal.toLocaleString("id-ID");

        // Update hidden value
        document.getElementById("subtotal-val-" + index).value = subtotal;

        // Hitung total
        let total = 0;
        document.querySelectorAll(".subtotal-value").forEach(s => {
            total += parseInt(s.value);
        });

        // Update tampilan + hidden input
        document.getElementById("total_nilai_view").innerText =
            "Rp " + total.toLocaleString("id-ID");

        document.getElementById("total_nilai").value = total;
    });
});
</script>
