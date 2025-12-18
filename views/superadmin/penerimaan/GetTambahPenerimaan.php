<?php
session_start();

require_once __DIR__ . '/../../../config/koneksi.php';

$db = new DBConnection();
$conn = $db->getConnection();

// Ambil user login
$iduser = $_SESSION['iduser'] ?? 1;
$username = $_SESSION['username'] ?? "system";

if (!$iduser || !$username) {
    echo "<p class='text-danger'>Sesi user tidak ditemukan. Silakan login ulang.</p>";
    exit;
}

// AMBIL PENGADAAN STATUS 'P'
$qPengadaan = $conn->query("
    SELECT 
        p.idpengadaan, 
        p.timestamp, 
        v.nama_vendor
    FROM pengadaan p
    JOIN vendor v ON v.idvendor = p.vendor_idvendor
    WHERE p.status = 'P'
    ORDER BY p.timestamp DESC
");
?>

<div class="p-3">

    <h4 class="text-warning fw-bold text-center mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Penerimaan
    </h4>

    <form id="formPenerimaan">

        <!-- PENGADAAN -->
        <div class="mb-3">
            <label class="form-label fw-bold">Pilih Pengadaan</label>
            <select name="idpengadaan" id="idpengadaan" class="form-select" required>
                <option value="">-- Pilih Pengadaan --</option>
                <?php while($p = $qPengadaan->fetch_assoc()): ?>
                    <option value="<?= $p['idpengadaan'] ?>">
                        #<?= $p['idpengadaan'] ?> - <?= $p['nama_vendor'] ?> (<?= $p['timestamp'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <hr class="border-light">

        <!-- DETAIL BARANG -->
        <h5 class="fw-bold text-warning">Detail Barang</h5>

        <div class="table-responsive">
            <table class="table table-bordered text-center bg-white" id="tableBarang">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Barang</th>
                        <th>Harga</th>
                        <th>Dipesan</th>
                        <th>Sudah Diterima</th>
                        <th>Sisa</th>
                        <th>Terima Sekarang</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="barangContainer">
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">
                            Pilih pengadaan terlebih dahulu
                        </td>
                    </tr>
                </tbody>
                <tfoot id="tableFooter" style="display: none;">
                    <tr>
                        <td colspan="7" class="text-end fw-bold">Total:</td>
                        <td class="text-end fw-bold fs-5" id="displayTotal">Rp 0</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <input type="hidden" name="iduser" value="<?= $iduser ?>">
        <input type="hidden" name="total" id="total_value">

        <button type="button" class="btn btn-warning w-100 mt-3 fw-bold" 
                onclick="saveTambahPenerimaan()" id="btnSubmit" disabled>
            <i class="bi bi-save"></i> Simpan Penerimaan
        </button>

    </form>
</div>

<script>
let detailBarang = [];

// PILIH PENGADAAN
document.getElementById('idpengadaan').addEventListener('change', function() {
    const idpengadaan = this.value;

    if(!idpengadaan){
        resetTable();
        return;
    }

    fetch("GetDetailPengadaan.php?idpengadaan=" + idpengadaan)
        .then(r => r.json())
        .then(data => {
            if(data.success){
                detailBarang = data.data;
                renderTable();
            } else {
                resetTable();
                alert(data.message);
            }
        })
        .catch(() => {
            resetTable();
            alert("Terjadi kesalahan mengambil data.");
        });
});


function renderTable() {
    const container = document.getElementById('barangContainer');
    let html = '';

    if(detailBarang.length === 0){
        resetTable();
        return;
    }

    detailBarang.forEach((item, i) => {
        html += `
            <tr>
                <td>${i+1}</td>
                <td>
                    ${item.nama_barang}
                    <input type="hidden" name="barang[${i}][idbarang]" value="${item.idbarang}">
                </td>
                <td>Rp ${format(item.harga_satuan)}</td>
                <td>${item.jumlah_pesan}</td>
                <td>${item.jumlah_diterima}</td>
                <td>${item.sisa}</td>
                <td>
                    <input type="number" 
                        name="barang[${i}][jumlah_terima]"
                        class="form-control form-control-sm text-center jml-terima"
                        min="0"
                        max="${item.sisa}"
                        value="0"
                        data-harga="${item.harga_satuan}"
                        data-row="${i}">
                </td>
                <td class="text-end subtotal-row" data-row="${i}">Rp 0</td>
            </tr>
        `;
    });

    container.innerHTML = html;
    document.getElementById('tableFooter').style.display = '';
    document.getElementById('btnSubmit').disabled = false;

    document.querySelectorAll('.jml-terima').forEach(el => {
        el.addEventListener('input', calculateTotal);
    });
}


// HITUNG TOTAL
function calculateTotal() {
    let total = 0;

    document.querySelectorAll('.jml-terima').forEach(el => {
        const jumlah = parseFloat(el.value) || 0;
        const harga = parseFloat(el.dataset.harga);
        const subtotal = jumlah * harga;
        const row = el.dataset.row;

        document.querySelector(`.subtotal-row[data-row="${row}"]`).innerHTML = "Rp " + format(subtotal);
        total += subtotal;
    });

    document.getElementById('displayTotal').innerHTML = "Rp " + format(total);
    document.getElementById('total_value').value = total;
}


// RESET TABLE
function resetTable() {
    document.getElementById('barangContainer').innerHTML = `
        <tr><td colspan="8" class="text-center text-muted py-3">Pilih pengadaan terlebih dahulu</td></tr>
    `;
    document.getElementById('tableFooter').style.display = 'none';
    document.getElementById('btnSubmit').disabled = true;
}


// FORMAT RUPIAH
function format(num) {
    return num.toLocaleString('id-ID');
}


// ✅ FIX SUBMIT — Disesuaikan dengan JSON Response
function saveTambahPenerimaan() {
    const form = new FormData(document.getElementById('formPenerimaan'));

    fetch("SaveTambahPenerimaan.php", {
        method: "POST",
        body: form
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert(res.message);
            location.reload();
        } else {
        alert("✅ " + res.message);
        }
    })
    .catch(() => {
        alert("❌ Terjadi kesalahan saat menyimpan");
    });
}

</script>
