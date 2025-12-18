<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../config/koneksi.php';
session_start();

$db = new DBConnection();
$conn = $db->getConnection();

if (!$conn) {
    die("Koneksi database gagal!");
}

// ✅ AMBIL ID PENJUALAN TERBESAR
$sqlMax = "SELECT COALESCE(MAX(idpenjualan), 0) AS max_id FROM penjualan";
$resultMax = $conn->query($sqlMax);

if ($resultMax && $row = $resultMax->fetch_assoc()) {
    $newIdPenjualan = $row['max_id'] + 1;
} else {
    $newIdPenjualan = 1;
}

// Ambil user dari session
$userId = $_SESSION['user_id'] ?? 1;
$currentUser = $conn->query("SELECT iduser, username FROM user WHERE iduser = $userId")->fetch_assoc();

// Ambil barang aktif yang ada stoknya
$barangs = $conn->query("
    SELECT 
        b.idbarang,
        b.nama_barang,
        b.jenis,
        s.nama_satuan,
        IFNULL((
            SELECT stock 
            FROM kartu_stok 
            WHERE idbarang = b.idbarang 
            ORDER BY idkartu_stok DESC 
            LIMIT 1
        ), 0) as stok_tersedia
    FROM barang b
    JOIN satuan s ON b.idsatuan = s.idsatuan
    WHERE b.status = 1
    HAVING stok_tersedia > 0
    ORDER BY b.nama_barang
");

// Simpan data barang ke array untuk JavaScript
$barangList = [];
while ($barang = $barangs->fetch_assoc()) {
    $barangList[] = $barang;
}

// Ambil margin aktif
$marginAktif = $conn->query("
    SELECT persen 
    FROM margin_penjualan 
    WHERE status = 1 
    ORDER BY updated_at DESC 
    LIMIT 1
")->fetch_assoc();
?>

<style>
.form-label { 
    color: #FFD54F; 
    font-weight: 600; 
    margin-bottom: 8px; 
}

.form-control, .form-select {
    background-color: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,213,79,0.3);
    color: white;
    border-radius: 8px;
    padding: 10px;
}

.form-control:focus, .form-select:focus {
    background-color: rgba(255,255,255,0.25);
    border-color: #FFD54F;
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(255,213,79,0.25);
}

.form-control::placeholder { color: rgba(255,255,255,0.5); }
.form-select option { background-color: #2b0f4a; color: white; }

/* Table styling */
.table { 
    background-color: #fff !important; 
    color: #333 !important;
    border-radius: 8px;
    overflow: hidden;
}

.table thead th {
    background-color: #f8f9fa !important;
    color: #333 !important;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
}

.table .form-control,
.table .form-select {
    background-color: #fff;
    border: 1px solid #ced4da;
    color: #333;
    font-size: 0.9rem;
    padding: 6px 10px;
}

.table .form-control:focus,
.table .form-select:focus {
    background-color: #fff;
    border-color: #FFD54F;
    color: #333;
}

.table tfoot {
    background-color: #f8f9fa;
    font-weight: 600;
}

.table tfoot .table-success {
    background-color: rgba(40, 167, 69, 0.2) !important;
}

.btn-add-item {
    background-color: #28a745;
    color: white;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    transition: 0.3s;
}
.btn-add-item:hover { background-color: #218838; }

.btn-submit {
    background-color: #FFD54F;
    color: #4B0082;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    padding: 10px 30px;
    transition: 0.3s;
}
.btn-submit:hover { background-color: #4B0082; color: #fff; }

.info-box {
    background-color: rgba(40,167,69,0.2);
    border-left: 4px solid #28a745;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}
</style>

<div class="p-4">
    <h2 class="text-center mb-4 text-warning">➕ Tambah Penjualan</h2>

    <form id="formTambahPenjualan">

        <!-- ✅ ID PENJUALAN OTOMATIS -->
        <input type="hidden" name="idpenjualan" value="<?= $newIdPenjualan ?>">
        
        <!-- Info User & Margin -->
        <div class="info-box">
            <div class="row">
                <div class="col-md-6">
                    <strong>👤 Kasir:</strong> <?= $currentUser['username'] ?>
                    <input type="hidden" name="iduser" value="<?= $currentUser['iduser'] ?>">
                </div>
                <div class="col-md-6">
                    <strong>💰 Margin Aktif:</strong> <?= $marginAktif['persen'] ?? 0 ?>%
                </div>
            </div>
        </div>

        <hr style="border-color: rgba(255,255,255,0.3);">

        <!-- DETAIL BARANG -->
        <h4 class="fw-bold mt-3 text-warning">Detail Barang</h4>

        <button type="button" class="btn btn-add-item mb-3" onclick="addBarangRow()">
            <i class="bi bi-plus-circle"></i> Tambah Barang
        </button>

        <!-- TABEL ITEM -->
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Barang</th>
                        <th width="100">Stok</th>
                        <th width="100">Jumlah</th>
                        <th width="150">Harga Jual</th>
                        <th width="150">Subtotal</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody id="barangContainer"></tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                        <td colspan="2" class="text-end fw-bold" id="displaySubtotal">Rp 0</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end fw-bold">PPN (10%):</td>
                        <td colspan="2" class="text-end fw-bold" id="displayPPN">Rp 0</td>
                    </tr>
                    <tr class="table-success">
                        <td colspan="5" class="text-end fw-bold">Total:</td>
                        <td colspan="2" class="text-end fw-bold fs-5" id="displayTotal">Rp 0</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- HIDDEN -->
        <input type="hidden" name="ppn" id="inputPPN" value="0">
        <input type="hidden" name="total_nilai" id="inputTotal" value="0">

        <!-- Submit Button -->
        <div class="mt-4">
            <button type="button" class="btn btn-secondary w-100 mb-2" data-bs-dismiss="modal">
                <i class="bi bi-x-circle"></i> Batal
            </button>
            <button type="submit" class="btn btn-submit w-100" id="btnSubmit">
                <i class="bi bi-check-circle"></i> Simpan Penjualan
            </button>
        </div>
    </form>
</div>

<script>
// Data barang dari server (PHP ke JavaScript)
const barangData = <?= json_encode($barangList) ?>;
let rowCounter = 0;

// Format Rupiah
function formatRupiah(number) {
    if (!number || number === 0) return 'Rp 0';
    return 'Rp ' + Math.floor(number).toLocaleString('id-ID');
}

// Tambah baris barang baru
function addBarangRow() {
    rowCounter++;
    
    // Generate options untuk select barang
    let optionsHTML = '<option value="">-- Pilih Barang --</option>';
    barangData.forEach(function(b) {
        optionsHTML += `<option value="${b.idbarang}" data-stok="${b.stok_tersedia}" data-satuan="${b.nama_satuan}">
            ${b.nama_barang} (${b.jenis})
        </option>`;
    });
    
    const row = `
        <tr id="row-${rowCounter}">
            <td class="text-center align-middle">${rowCounter}</td>
            <td>
                <select name="barang[${rowCounter}][idbarang]" class="form-select form-select-sm barang-select" data-row="${rowCounter}" required>
                    ${optionsHTML}
                </select>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm text-center stok-display" data-row="${rowCounter}" readonly value="-">
            </td>
            <td>
                <input type="number" name="barang[${rowCounter}][jumlah]" class="form-control form-control-sm text-center jumlah-input" data-row="${rowCounter}" min="1" value="1" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm text-end harga-display" data-row="${rowCounter}" readonly value="Rp 0">
                <input type="hidden" name="barang[${rowCounter}][harga_jual]" class="harga-value" data-row="${rowCounter}" value="0">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm text-end subtotal-display" data-row="${rowCounter}" readonly value="Rp 0">
                <input type="hidden" name="barang[${rowCounter}][subtotal]" class="subtotal-value" data-row="${rowCounter}" value="0">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeBarangRow(${rowCounter})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `;
    
    document.getElementById('barangContainer').insertAdjacentHTML('beforeend', row);
    attachEventListeners(rowCounter);
    renumberRows();
}

// Hapus baris
function removeBarangRow(rowId) {
    const row = document.getElementById('row-' + rowId);
    if (row) {
        row.remove();
        renumberRows();
        calculateTotal();
    }
}

// Renumber baris
function renumberRows() {
    const rows = document.querySelectorAll('#barangContainer tr');
    rows.forEach(function(row, index) {
        row.querySelector('td:first-child').textContent = index + 1;
    });
}

// Attach event listeners
function attachEventListeners(rowId) {
    const selectBarang = document.querySelector('.barang-select[data-row="' + rowId + '"]');
    if (selectBarang) {
        selectBarang.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const idbarang = this.value;
            const stok = selectedOption.dataset.stok || '-';
            
            // Update stok display
            const stokDisplay = document.querySelector('.stok-display[data-row="' + rowId + '"]');
            if (stokDisplay) stokDisplay.value = stok;
            
            if (idbarang) {
                getHargaJual(rowId, idbarang);
            } else {
                resetRow(rowId);
            }
        });
    }
    
    const inputJumlah = document.querySelector('.jumlah-input[data-row="' + rowId + '"]');
    if (inputJumlah) {
        inputJumlah.addEventListener('input', function() {
            calculateRowSubtotal(rowId);
        });
    }
}

// Get harga jual dari server (via AJAX)
function getHargaJual(rowId, idbarang) {
    fetch('GetHargaJual.php?idbarang=' + idbarang)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const hargaDisplay = document.querySelector('.harga-display[data-row="' + rowId + '"]');
                const hargaValue = document.querySelector('.harga-value[data-row="' + rowId + '"]');
                
                if (hargaDisplay) hargaDisplay.value = formatRupiah(data.harga_jual);
                if (hargaValue) hargaValue.value = data.harga_jual;
                
                calculateRowSubtotal(rowId);
            } else {
                alert('Error: ' + data.message);
                resetRow(rowId);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Gagal mengambil harga jual. Silakan coba lagi.');
            resetRow(rowId);
        });
}

// Hitung subtotal per baris
function calculateRowSubtotal(rowId) {
    const hargaValue = document.querySelector('.harga-value[data-row="' + rowId + '"]');
    const jumlahInput = document.querySelector('.jumlah-input[data-row="' + rowId + '"]');
    const subtotalDisplay = document.querySelector('.subtotal-display[data-row="' + rowId + '"]');
    const subtotalValue = document.querySelector('.subtotal-value[data-row="' + rowId + '"]');
    
    if (!hargaValue || !jumlahInput) return;
    
    const harga = parseInt(hargaValue.value) || 0;
    const jumlah = parseInt(jumlahInput.value) || 0;
    const subtotal = harga * jumlah;
    
    if (subtotalDisplay) subtotalDisplay.value = formatRupiah(subtotal);
    if (subtotalValue) subtotalValue.value = subtotal;
    
    calculateTotal();
}

// Reset row
function resetRow(rowId) {
    const hargaDisplay = document.querySelector('.harga-display[data-row="' + rowId + '"]');
    const hargaValue = document.querySelector('.harga-value[data-row="' + rowId + '"]');
    const subtotalDisplay = document.querySelector('.subtotal-display[data-row="' + rowId + '"]');
    const subtotalValue = document.querySelector('.subtotal-value[data-row="' + rowId + '"]');
    const stokDisplay = document.querySelector('.stok-display[data-row="' + rowId + '"]');
    
    if (hargaDisplay) hargaDisplay.value = 'Rp 0';
    if (hargaValue) hargaValue.value = 0;
    if (subtotalDisplay) subtotalDisplay.value = 'Rp 0';
    if (subtotalValue) subtotalValue.value = 0;
    if (stokDisplay) stokDisplay.value = '-';
    
    calculateTotal();
}

// Hitung total keseluruhan
function calculateTotal() {
    let subtotal = 0;
    
    document.querySelectorAll('.subtotal-value').forEach(function(input) {
        subtotal += parseInt(input.value) || 0;
    });
    
    const ppn = Math.floor(subtotal * 0.10);
    const total = subtotal + ppn;
    
    document.getElementById('displaySubtotal').textContent = formatRupiah(subtotal);
    document.getElementById('displayPPN').textContent = formatRupiah(ppn);
    document.getElementById('displayTotal').textContent = formatRupiah(total);
    
    document.getElementById('inputPPN').value = ppn;
    document.getElementById('inputTotal').value = total;
}

// Default 1 row
addBarangRow();

// Form submit handler dengan AJAX
document.getElementById('formTambahPenjualan').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validasi minimal 1 barang
    const rows = document.querySelectorAll('#barangContainer tr');
    if (rows.length === 0) {
        alert('Tambahkan minimal 1 barang!');
        return;
    }
    
    let hasValidItem = false;
    rows.forEach(row => {
        const select = row.querySelector('.barang-select');
        if (select && select.value) hasValidItem = true;
    });
    
    if (!hasValidItem) {
        alert('Minimal 1 barang harus dipilih!');
        return;
    }
    
    // Disable tombol submit
    const btnSubmit = document.getElementById('btnSubmit');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
    
    // Kirim data via AJAX
    const formData = new FormData(this);
    
    fetch('SaveTambahPenjualan.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Tutup modal
            const modalElement = document.getElementById('modalTambah');
            if (modalElement) {
                bootstrap.Modal.getInstance(modalElement).hide();
            }
            // Reload halaman
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-check-circle"></i> Simpan Penjualan';
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Gagal menyimpan data. Silakan coba lagi.');
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-check-circle"></i> Simpan Penjualan';
    });
});
</script>
