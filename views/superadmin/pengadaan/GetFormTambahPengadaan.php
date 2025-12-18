<?php
session_start([
    'cookie_lifetime' => 86400,
    'read_and_close'  => false,
]);

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

// ✅ Ambil daftar vendor AKTIF saja
$vendor = $conn->query("SELECT idvendor, nama_vendor FROM vendor WHERE status = 'Aktif'");

// Ambil barang aktif
$barang_result = $conn->query("
    SELECT 
        idbarang, 
        nama_barang, 
        harga_modal
    FROM barang
    WHERE status = 1
    ORDER BY idbarang ASC
");

$barang_list = [];
if ($barang_result) {
    while($b = $barang_result->fetch_assoc()) {
        $barang_list[] = $b;
    }
}
?>

<div class="p-4">

    <h2 class="text-center mb-4">➕ Tambah Pengadaan</h2>

    <form id="formPengadaan">

        <!-- VENDOR -->
        <div class="mb-3">
            <label class="form-label fw-bold">Vendor</label>
            <select name="idvendor" id="idvendor" class="form-select" required>
                <option value="">Pilih Vendor</option>
                <?php while($v = $vendor->fetch_assoc()): ?>
                <option value="<?= $v['idvendor'] ?>"><?= htmlspecialchars($v['nama_vendor']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <hr>

        <!-- DETAIL BARANG -->
        <h4 class="fw-bold mt-3">Detail Barang</h4>

        <button type="button" class="btn btn-success mb-3" onclick="addBarangRow()">
            <i class="bi bi-plus-circle"></i> Tambah Barang
        </button>

        <!-- TABEL ITEM -->
        <div class="table-responsive">
            <table class="table table-bordered text-center bg-white">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Barang</th>
                        <th width="150">Harga Satuan</th>
                        <th width="100">Jumlah</th>
                        <th width="150">Subtotal</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody id="barangContainer"></tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                        <td colspan="2" class="text-end fw-bold" id="displaySubtotal">Rp 0</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end fw-bold">PPN (10%):</td>
                        <td colspan="2" class="text-end fw-bold" id="displayNilaiPPN">Rp 0</td>
                    </tr>
                    <tr class="table-success">
                        <td colspan="4" class="text-end fw-bold">Total:</td>
                        <td colspan="2" class="text-end fw-bold fs-5" id="displayTotal">Rp 0</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- HIDDEN -->
        <input type="hidden" name="iduser" value="<?= $iduser ?>">
        <input type="hidden" name="username" value="<?= $username ?>">
        <input type="hidden" id="display_subtotal" name="subtotal">
        <input type="hidden" id="display_total" name="total">

        <div class="mt-4">
            <button type="button" class="btn btn-warning w-100 mt-3 fw-bold" onclick="saveTambahPengadaan()">
                <i class="bi bi-save"></i> Simpan Pengadaan
            </button>
        </div>
    </form>
</div>

<script>
// Data barang dari server (PHP ke JavaScript)
const barangData = <?php echo json_encode($barang_list); ?>;
let rowCounter = 0;

// Tambah baris barang baru
function addBarangRow() {
    rowCounter++;
    
    // Generate options untuk select barang
    let optionsHTML = '<option value="">-- Pilih Barang --</option>';
    barangData.forEach(function(b) {
        optionsHTML += '<option value="' + b.idbarang + '" data-harga="' + b.harga_modal + '">' + 
                       b.nama_barang + '</option>';
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
                <input type="text" class="form-control form-control-sm text-end harga-display" data-row="${rowCounter}" readonly value="Rp 0">
                <input type="hidden" name="barang[${rowCounter}][harga]" class="harga-value" data-row="${rowCounter}" value="0">
            </td>
            <td>
                <input type="number" name="barang[${rowCounter}][jumlah]" class="form-control form-control-sm text-center jumlah-input" data-row="${rowCounter}" min="1" value="1" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm text-end subtotal-display" data-row="${rowCounter}" readonly value="Rp 0">
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
    const selectBarang = document.querySelector('select[data-row="' + rowId + '"]');
    if (selectBarang) {
        selectBarang.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const harga = parseFloat(selectedOption.dataset.harga) || 0;
            
            const hargaDisplay = document.querySelector('.harga-display[data-row="' + rowId + '"]');
            const hargaValue = document.querySelector('.harga-value[data-row="' + rowId + '"]');
            
            if (hargaDisplay) hargaDisplay.value = formatRupiah(harga);
            if (hargaValue) hargaValue.value = harga;
            
            calculateRowSubtotal(rowId);
        });
    }
    
    const inputJumlah = document.querySelector('.jumlah-input[data-row="' + rowId + '"]');
    if (inputJumlah) {
        inputJumlah.addEventListener('input', function() {
            calculateRowSubtotal(rowId);
        });
    }
}

// Hitung subtotal per baris
function calculateRowSubtotal(rowId) {
    const hargaValue = document.querySelector('.harga-value[data-row="' + rowId + '"]');
    const jumlahInput = document.querySelector('.jumlah-input[data-row="' + rowId + '"]');
    const subtotalDisplay = document.querySelector('.subtotal-display[data-row="' + rowId + '"]');
    
    const harga = parseFloat(hargaValue.value) || 0;
    const jumlah = parseInt(jumlahInput.value) || 0;
    const subtotal = harga * jumlah;
    
    subtotalDisplay.value = formatRupiah(subtotal);
    
    calculateTotal();
}

// Hitung total keseluruhan
function calculateTotal() {
    let subtotal = 0;
    
    document.querySelectorAll('.subtotal-display').forEach(function(input) {
        const value = parseFloat(input.value.replace(/[^0-9]/g, '')) || 0;
        subtotal += value;
    });
    
    const ppnPersen = 10;
    const nilaiPPN = Math.floor(subtotal * ppnPersen / 100);
    const total = subtotal + nilaiPPN;
    
    document.getElementById('displaySubtotal').textContent = formatRupiah(subtotal);
    document.getElementById('displayNilaiPPN').textContent = formatRupiah(nilaiPPN);
    document.getElementById('displayTotal').textContent = formatRupiah(total);
    
    document.getElementById('display_subtotal').value = subtotal;
    document.getElementById('display_total').value = total;
}

function formatRupiah(number) {
    if (!number || number === 0) return 'Rp 0';
    return 'Rp ' + Math.floor(number).toLocaleString('id-ID');
}

// ✅ FIX: DEFAULT 1 ROW (HANYA SEKALI)
addBarangRow();

// ✅ SIMPAN PENGADAAN
function saveTambahPengadaan() {

    const rows = document.querySelectorAll('#barangContainer tr');
    if (rows.length === 0) {
        alert('Tambahkan minimal 1 barang!');
        return;
    }

    const idvendor = document.getElementById('idvendor').value;
    if (!idvendor) {
        alert('Pilih vendor terlebih dahulu!');
        return;
    }

    const formData = new FormData();
    formData.append('idvendor', idvendor);
    formData.append('iduser', document.querySelector('input[name="iduser"]').value);
    formData.append('subtotal', document.getElementById('display_subtotal').value);
    formData.append('total', document.getElementById('display_total').value);

    // ✅ Ambil data detail barang
    let index = 0;
    rows.forEach(row => {
        const idbarang = row.querySelector('select').value;
        const jumlah = row.querySelector('.jumlah-input').value;

        if (idbarang && jumlah) {
            formData.append(`detail[${index}][idbarang]`, idbarang);
            formData.append(`detail[${index}][jumlah]`, jumlah);
            index++;
        }
    });

    fetch("SaveTambahPengadaan.php", {
        method: "POST",
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert("✅ Pengadaan berhasil disimpan!");
            location.reload();
        } else {
            alert("❌ Gagal: " + res.message);
        }
    })
    .catch(() => {
        alert("❌ Terjadi kesalahan saat menyimpan data");
    });
}
</script>
