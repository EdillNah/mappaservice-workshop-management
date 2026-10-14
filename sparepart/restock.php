<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

// Khusus Owner
harusOwner();

$pageTitle = "Restock & Pengadaan Sparepart";
$successMsg = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "ajax_tambah_master") {
  header('Content-Type: application/json');
  $namaBaru = trim($_POST["nama"] ?? "");
  $hargaBaru = (float) ($_POST["harga"] ?? 0);
  $stokAwal = (int) ($_POST["stok_awal"] ?? 0);

  if (empty($namaBaru)) {
    echo json_encode(["success" => false, "message" => "Nama sparepart tidak boleh kosong."]);
    exit;
  }

  // Cek duplikasi nama mirip di database
  $stmtCheck = $pdo->prepare("SELECT sparepart_id, nama FROM sparepart WHERE LOWER(nama) = LOWER(?) LIMIT 1");
  $stmtCheck->execute([$namaBaru]);
  $existing = $stmtCheck->fetch();

  if ($existing) {
    echo json_encode([
      "success" => false,
      "duplicate" => true,
      "message" => "Sparepart dengan nama serupa sudah terdaftar di master: #" . $existing['sparepart_id'] . " - " . $existing['nama'] . ". Silakan pilih item tersebut dari daftar.",
      "existing_id" => $existing['sparepart_id']
    ]);
    exit;
  }

  try {
    $stmtInsert = $pdo->prepare("INSERT INTO sparepart (nama, harga, stok) VALUES (?, ?, ?)");
    $stmtInsert->execute([$namaBaru, $hargaBaru, $stokAwal]);
    $newId = $pdo->lastInsertId();

    echo json_encode([
      "success" => true,
      "message" => "Sparepart baru berhasil ditambahkan ke master katalog.",
      "item" => [
        "sparepart_id" => (int) $newId,
        "nama" => $namaBaru,
        "harga" => $hargaBaru,
        "stok" => $stokAwal
      ]
    ]);
    exit;
  } catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan sparepart: " . $e->getMessage()]);
    exit;
  }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["simpan_restock"])) {
  $rawItems = $_POST["items"] ?? [];
  $keterangan = trim($_POST["keterangan"] ?? "");
  $tanggal = !empty($_POST["tanggal"]) ? $_POST["tanggal"] . " " . date("H:i:s") : date("Y-m-d H:i:s");
  $userId = $_SESSION["user_id"] ?? null;

  // Filter & sanitasi data array
  $validItems = [];
  $seenIds = [];

  foreach ($rawItems as $it) {
    $spId = isset($it["sparepart_id"]) ? (int) $it["sparepart_id"] : 0;
    $qty = isset($it["jumlah"]) ? (int) $it["jumlah"] : 0;
    $beli = isset($it["harga_beli"]) ? (float) $it["harga_beli"] : 0;

    if ($spId > 0 && $qty > 0 && !isset($seenIds[$spId])) {
      $validItems[] = [
        "sparepart_id" => $spId,
        "jumlah" => $qty,
        "harga_beli" => $beli,
        "subtotal" => $qty * $beli
      ];
      $seenIds[$spId] = true;
    }
  }

  if (empty($validItems)) {
    $errorMsg = "Harap masukkan setidaknya satu item sparepart yang valid dengan jumlah lebih dari 0.";
  } else {
    try {
      // BUNGKUS TRANSAKSI ATOMIC
      $pdo->beginTransaction();

      // 1. Hitung total belanja
      $totalBiaya = 0;
      foreach ($validItems as $v) {
        $totalBiaya += $v["subtotal"];
      }

      // 2. Simpan master restock
      $stmtRestock = $pdo->prepare("INSERT INTO restock (tanggal, total_biaya, keterangan, user_id) VALUES (?, ?, ?, ?)");
      $stmtRestock->execute([$tanggal, $totalBiaya, $keterangan, $userId]);
      $restockId = $pdo->lastInsertId();

      // 3. Simpan detail restock & update stok master sparepart
      $stmtDetail = $pdo->prepare("INSERT INTO detail_restock (restock_id, sparepart_id, jumlah, harga_beli, subtotal) VALUES (?, ?, ?, ?, ?)");
      $stmtUpdateStok = $pdo->prepare("UPDATE sparepart SET stok = stok + ? WHERE sparepart_id = ?");

      foreach ($validItems as $v) {
        $stmtDetail->execute([
          $restockId,
          $v["sparepart_id"],
          $v["jumlah"],
          $v["harga_beli"],
          $v["subtotal"]
        ]);

        $stmtUpdateStok->execute([
          $v["jumlah"],
          $v["sparepart_id"]
        ]);
      }

      $pdo->commit();

      $_SESSION["flash_restock_success"] = "Berhasil menambahkan stok untuk " . count($validItems) . " item sparepart! Total belanja: Rp " . number_format($totalBiaya, 0, ',', '.');
      header("Location: sparepart.php");
      exit;
    } catch (Exception $e) {
      $pdo->rollBack();
      $errorMsg = "Gagal memproses restock: " . $e->getMessage();
    }
  }
}

// Ambil semua data sparepart untuk master list & dropdown
$allSpareparts = $pdo->query("SELECT * FROM sparepart ORDER BY nama ASC")->fetchAll();

// Ambil rekomendasi restock prioritas dibawah 10
$stmtRekomendasi = $pdo->query("SELECT * FROM sparepart ORDER BY stok ASC, nama ASC LIMIT 8");
$rekomendasiList = $stmtRekomendasi->fetchAll();

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-7xl mx-auto space-y-6">
  <!-- Header & Back Navigation -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-charcoal pb-6">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-2xl font-bold text-snow tracking-tight">Restock & Belanja Sparepart</h1>
        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-phosphor/10 border border-phosphor/30 text-phosphor">
          Batch Input
        </span>
      </div>
      <p class="text-sm text-silver mt-1">Input pengadaan suku cadang secara massal dan perbarui stok gudang secara otomatis.</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="sparepart.php"
         class="px-4 py-2 rounded-full border border-slate text-silver hover:text-snow hover:border-graphite text-xs transition-all">
        &larr; Kembali ke Katalog
      </a>
      <button type="button"
              onclick="bukaModalBaru()"
              class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-ash hover:bg-charcoal border border-charcoal hover:border-slate text-snow text-xs font-medium transition-all">
        <svg class="w-3.5 h-3.5 text-phosphor" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Sparepart Baru (Belum Terdaftar)</span>
      </button>
    </div>
  </div>

  <?php if ($errorMsg): ?>
    <div class="p-4 rounded-xl bg-red-950/40 border border-red-900/60 text-red-300 text-xs flex items-center gap-3">
      <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      <span><?= htmlspecialchars($errorMsg) ?></span>
    </div>
  <?php endif; ?>

  <!-- 1. REKOMENDASI RESTOCK CEPAT (QUICK ADD CHIPS/PILLS) -->
  <div class="bg-obsidian border border-charcoal rounded-2xl p-5 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-phosphor animate-pulse"></span>
        <h2 class="text-xs font-bold uppercase tracking-wider font-mono text-smoke">
          Rekomendasi Restock (Sering Dibeli / Stok Tipis)
        </h2>
      </div>
      <span class="text-[11px] text-smoke font-mono">Klik chip untuk langsung tambah ke baris belanja</span>
    </div>

    <div class="flex flex-wrap gap-2 pt-1" id="containerChips">
      <?php if (empty($rekomendasiList)): ?>
        <p class="text-xs text-smoke font-mono">Belum ada item di katalog sparepart.</p>
      <?php else: ?>
        <?php foreach ($rekomendasiList as $sp): ?>
          <?php
            $isTipis = $sp['stok'] <= 10;
            $chipBorder = $isTipis ? 'border-amber-900/50 hover:border-phosphor/80' : 'border-charcoal hover:border-phosphor/80';
            $stokBadgeColor = $isTipis ? 'text-amber-400 bg-amber-950/40' : 'text-smoke bg-charcoal/50';
          ?>
          <button type="button"
                  onclick="quickAddChip(<?= $sp['sparepart_id'] ?>)"
                  data-chip-id="<?= $sp['sparepart_id'] ?>"
                  class="group inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-ash/70 hover:bg-charcoal border <?= $chipBorder ?> text-xs text-snow transition-all duration-150 cursor-pointer active:scale-95">
            <span class="text-phosphor font-bold group-hover:scale-110 transition-transform">+</span>
            <span class="font-medium"><?= htmlspecialchars($sp['nama']) ?></span>
            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded <?= $stokBadgeColor ?>">
              Stok: <?= $sp['stok'] ?>
            </span>
          </button>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- 2. FORMULIR INPUT DINAMIS BATCH RESTOCK -->
  <form method="POST" id="formRestock" onsubmit="return validateBeforeSubmit(event)">
    <input type="hidden" name="simpan_restock" value="1">

    <div class="bg-obsidian border border-charcoal rounded-2xl overflow-hidden shadow-sm">
      <!-- Info Faktur / Pengadaan -->
      <div class="p-5 border-b border-charcoal bg-ash/20 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-silver mb-1.5">Keterangan / Supplier / No. Nota</label>
          <input type="text"
                 name="keterangan"
                 placeholder="Contoh: Belanja Grosir Toko Sumber Jaya Motor (Nota #SJ-889)"
                 class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-xs text-snow placeholder-smoke">
        </div>
        <div>
          <label class="block text-xs font-medium text-silver mb-1.5">Tanggal Pembelian</label>
          <input type="date"
                 name="tanggal"
                 value="<?= date('Y-m-d') ?>"
                 required
                 class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-xs text-snow font-mono">
        </div>
      </div>

      <!-- Tabel Baris Belanjaan Dinamis -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm" id="tabelBelanja">
          <thead class="bg-ash/50 border-b border-charcoal text-[11px] uppercase tracking-wider text-smoke font-mono">
            <tr>
              <th class="px-4 py-3.5 w-12 text-center">No</th>
              <th class="px-4 py-3.5 min-w-[240px]">Pilih Sparepart (Master)</th>
              <th class="px-4 py-3.5 w-24">Kode Part</th>
              <th class="px-4 py-3.5 w-32">Jumlah Masuk</th>
              <th class="px-4 py-3.5 w-44">Harga Beli Satuan (Rp)</th>
              <th class="px-4 py-3.5 w-44 text-right">Subtotal (Rp)</th>
              <th class="px-4 py-3.5 w-16 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-charcoal text-silver" id="bodyBelanja">
            <!-- Diisi secara dinamis oleh JavaScript -->
          </tbody>
        </table>
      </div>

      <!-- State Kosong jika belum ada baris -->
      <div id="emptyState" class="px-6 py-12 text-center border-b border-charcoal/60 bg-ash/10">
        <div class="w-10 h-10 rounded-xl bg-ash border border-charcoal flex items-center justify-center text-smoke mx-auto mb-3">
          <svg class="w-5 h-5 text-smoke" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        </div>
        <p class="text-sm font-medium text-snow">Belum Ada Item Belanjaan Terpilih</p>
        <p class="text-xs text-smoke mt-1 font-mono">
          Klik salah satu chip rekomendasi di atas atau tekan tombol tambah item di bawah.
        </p>
      </div>

      <!-- Action Bar (+ Tambah Item Lainnya) -->
      <div class="p-4 bg-ash/30 border-b border-charcoal flex items-center justify-between">
        <button type="button"
                id="btnTambahBaris"
                onclick="tambahBaris()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-ash hover:bg-charcoal border border-charcoal hover:border-slate text-snow text-xs font-medium transition-all group">
          <div class="w-4 h-4 rounded-md bg-phosphor/20 flex items-center justify-center text-phosphor group-hover:scale-110 transition-transform">
            <svg class="w-3 h-3 text-phosphor" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
          </div>
          <span>Tambah Item Belanjaan Lainnya</span>
        </button>

        <span class="text-xs font-mono text-smoke" id="labelItemCount">0 item dipilih</span>
      </div>

      <div class="p-6 bg-ash/20 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-1 text-xs text-smoke font-mono">
          <div>Pencegahan Duplikasi: <span class="text-phosphor">Aktif (1 Sparepart = 1 Baris)</span></div>
          <div>Database Safety: <span class="text-phosphor">Atomic Transaction (All-or-Nothing)</span></div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-6">
          <div class="text-right">
            <span class="text-xs text-smoke font-mono block">Estimasi Total Belanja</span>
            <span class="text-2xl font-bold font-mono text-phosphor tracking-tight" id="labelGrandTotal">
              Rp 0
            </span>
          </div>

          <div class="flex items-center gap-3">
            <a href="sparepart.php"
               class="px-4 py-2.5 rounded-full border border-slate text-silver hover:text-snow hover:border-graphite text-xs transition-all">
              Batal
            </a>
            <button type="submit"
                    id="btnSubmitRestock"
                    class="px-6 py-2.5 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight shadow-md transition-all flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              <span>Simpan & Masukkan Stok (+)</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>


<div id="modalSparepartBaru" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="bg-obsidian border border-charcoal rounded-2xl max-w-md w-full p-6 shadow-2xl relative">
    <div class="flex items-center justify-between border-b border-charcoal pb-4 mb-4">
      <div>
        <h3 class="text-base font-bold text-snow">Daftarkan Sparepart Baru</h3>
        <p class="text-xs text-smoke mt-0.5">Tambah komponen baru yang belum pernah terdaftar di katalog master</p>
      </div>
      <button type="button" onclick="tutupModalBaru()" class="p-1 rounded-lg text-smoke hover:text-snow hover:bg-ash">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <form id="formBaruAjax" onsubmit="handleSimpanSparepartBaru(event)" class="space-y-4">
      <div id="modalError" class="hidden p-3 rounded-xl bg-red-950/40 border border-red-900/60 text-red-300 text-xs"></div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Nama Sparepart Baru</label>
        <input type="text" id="namaBaruInput" required placeholder="Contoh: Kampas Rem Depan Vario 160"
              class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-xs text-snow placeholder-smoke">
        <p class="text-[11px] text-smoke mt-1 font-mono">Gunakan nama spesifik untuk membedakan tipe/varian.</p>
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Harga Jual Standar (Rp)</label>
        <input type="number" id="hargaBaruInput" required min="0" step="500" placeholder="50000"
              class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-xs text-snow font-mono">
        <p class="text-[11px] text-smoke mt-1 font-mono">Harga jual yang ditagihkan kepada konsumen bengkel.</p>
      </div>

      <div class="pt-3 flex items-center justify-end gap-3 border-t border-charcoal">
        <button type="button"
                onclick="tutupModalBaru()"
                class="px-4 py-2 rounded-full border border-slate text-silver hover:text-snow text-xs transition-all">
          Batal
        </button>
        <button type="submit"
                id="btnSubmitModal"
                class="px-5 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          Daftarkan & Masukkan ke Belanjaan
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Toast Notifikasi Ringan -->
<div id="toastAlert" class="fixed bottom-5 right-5 z-50 hidden bg-ash border border-charcoal text-snow px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 transition-all duration-200">
  <span class="w-2 h-2 rounded-full bg-phosphor" id="toastDot"></span>
  <span class="text-xs font-medium" id="toastText"></span>
</div>

<!-- ============================================================= -->
<!-- 4. LOGIKA JAVASCRIPT MURNI (VANILLA JS)                         -->
<!-- ============================================================= -->
<script>
// Data Master Sparepart dari Server
let masterSpareparts = <?= json_encode($allSpareparts, JSON_UNESCAPED_UNICODE) ?>;

let rowIndexCounter = 0;

function formatRupiah(num) {
  return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
}

function showToast(text, isError = false) {
  const toast = document.getElementById('toastAlert');
  const toastText = document.getElementById('toastText');
  const toastDot = document.getElementById('toastDot');

  toastText.innerText = text;
  toastDot.className = isError ? 'w-2 h-2 rounded-full bg-red-400' : 'w-2 h-2 rounded-full bg-phosphor';
  toast.classList.remove('hidden');

  setTimeout(() => {
    toast.classList.add('hidden');
  }, 3500);
}

// -------------------------------------------------------------
// Tambah Baris Belanjaan Dinamis
// -------------------------------------------------------------
function tambahBaris(preselectedId = null, initialQty = 1) {
  const tbody = document.getElementById('bodyBelanja');
  const emptyState = document.getElementById('emptyState');
  emptyState.classList.add('hidden');

  const idx = rowIndexCounter++;
  const tr = document.createElement('tr');
  tr.id = `row-${idx}`;
  tr.className = 'hover:bg-ash/30 transition-colors group';

  // Siapkan opsi select berdasarkan master sparepart
  let optionsHtml = `<option value="">-- Pilih Suku Cadang --</option>`;
  masterSpareparts.forEach(sp => {
    const isSelected = (preselectedId !== null && Number(sp.sparepart_id) === Number(preselectedId)) ? 'selected' : '';
    optionsHtml += `<option value="${sp.sparepart_id}" data-harga="${sp.harga}" data-stok="${sp.stok}" ${isSelected}>
      ${sp.nama} (Stok: ${sp.stok} • Jual: ${formatRupiah(sp.harga)})
    </option>`;
  });

  // Tentukan harga beli default (estimasi 70-80% harga jual jika ada)
  let defaultHargaBeli = 0;
  if (preselectedId) {
    const matched = masterSpareparts.find(s => Number(s.sparepart_id) === Number(preselectedId));
    if (matched) {
      defaultHargaBeli = Math.round(Number(matched.harga) * 0.8 / 1000) * 1000;
    }
  }

  tr.innerHTML = `
    <td class="px-4 py-3.5 text-center font-mono text-xs text-smoke row-number">
      ${tbody.children.length + 1}
    </td>
    <td class="px-4 py-3.5">
      <select name="items[${idx}][sparepart_id]"
              required
              onchange="handleSelectChange(${idx}, this)"
              class="w-full bg-obsidian border border-slate rounded-lg px-3 py-2 text-xs text-snow font-medium select-sparepart">
        ${optionsHtml}
      </select>
    </td>
    <td class="px-4 py-3.5 font-mono text-xs text-smoke kode-part">
      ${preselectedId ? '#' + preselectedId : '-'}
    </td>
    <td class="px-4 py-3.5">
      <input type="number"
             name="items[${idx}][jumlah]"
             min="1"
             value="${initialQty}"
             required
             oninput="hitungSubtotal(${idx})"
             class="w-full bg-obsidian border border-slate rounded-lg px-3 py-2 text-xs text-snow font-mono input-jumlah text-center">
    </td>
    <td class="px-4 py-3.5">
      <div class="relative">
        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-[11px] font-mono text-smoke">Rp</span>
        <input type="number"
               name="items[${idx}][harga_beli]"
               min="0"
               step="500"
               value="${defaultHargaBeli}"
               required
               oninput="hitungSubtotal(${idx})"
               class="w-full bg-obsidian border border-slate rounded-lg pl-8 pr-2.5 py-2 text-xs text-snow font-mono input-harga">
      </div>
    </td>
    <td class="px-4 py-3.5 font-mono text-xs text-phosphor text-right font-medium subtotal-display">
      ${formatRupiah(initialQty * defaultHargaBeli)}
    </td>
    <td class="px-4 py-3.5 text-center">
      <button type="button"
              onclick="hapusBaris(${idx})"
              class="p-1.5 rounded-lg text-smoke hover:text-red-400 hover:bg-red-950/30 border border-transparent hover:border-red-900/40 transition-all"
              title="Hapus baris item ini">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
      </button>
    </td>
  `;

  tbody.appendChild(tr);
  updateRowNumbers();
  hitungGrandTotal();

  // Highlight flash
  tr.classList.add('bg-phosphor/10');
  setTimeout(() => tr.classList.remove('bg-phosphor/10'), 800);
}

// -------------------------------------------------------------
// Hapus Baris
// -------------------------------------------------------------
function hapusBaris(idx) {
  const tr = document.getElementById(`row-${idx}`);
  if (tr) {
    tr.remove();
    updateRowNumbers();
    hitungGrandTotal();

    const tbody = document.getElementById('bodyBelanja');
    if (tbody.children.length === 0) {
      document.getElementById('emptyState').classList.remove('hidden');
    }
  }
}

// Perbarui nomor urut tabel
function updateRowNumbers() {
  const rows = document.querySelectorAll('#bodyBelanja tr');
  rows.forEach((tr, index) => {
    const numEl = tr.querySelector('.row-number');
    if (numEl) numEl.innerText = index + 1;
  });
  document.getElementById('labelItemCount').innerText = `${rows.length} item dipilih`;
}

// -------------------------------------------------------------
// Validasi Duplikasi pada Pilihan Dropdown
// -------------------------------------------------------------
function handleSelectChange(currentIdx, selectEl) {
  const selectedId = selectEl.value;
  const currentRow = document.getElementById(`row-${currentIdx}`);
  const kodeEl = currentRow.querySelector('.kode-part');
  const hargaInput = currentRow.querySelector('.input-harga');

  if (!selectedId) {
    kodeEl.innerText = '-';
    hitungSubtotal(currentIdx);
    return;
  }

  // Cek apakah item ini sudah ada di baris LAIN
  const allSelects = document.querySelectorAll('.select-sparepart');
  for (let s of allSelects) {
    const tr = s.closest('tr');
    if (tr && tr.id !== `row-${currentIdx}` && s.value === selectedId) {
      // Duplikasi ditemukan!
      showToast('Item ini sudah dipilih di baris lain. Kuantitas dialihkan ke baris yang ada.', true);

      // Tambahkan quantity di baris yang sudah ada
      const existingQtyInput = tr.querySelector('.input-jumlah');
      if (existingQtyInput) {
        existingQtyInput.value = Number(existingQtyInput.value) + 1;
        const existingIdx = tr.id.replace('row-', '');
        hitungSubtotal(existingIdx);

        // Flash baris yang sudah ada
        tr.classList.add('bg-phosphor/20');
        setTimeout(() => tr.classList.remove('bg-phosphor/20'), 1000);
      }

      // Hapus baris saat ini karena duplikat
      hapusBaris(currentIdx);
      return;
    }
  }

  // Update kode part dan harga estimasi
  kodeEl.innerText = '#' + selectedId;
  const matched = masterSpareparts.find(s => Number(s.sparepart_id) === Number(selectedId));
  if (matched && Number(hargaInput.value) === 0) {
    // Estimasi harga beli 80% dari harga jual
    hargaInput.value = Math.round(Number(matched.harga) * 0.8 / 1000) * 1000;
  }

  hitungSubtotal(currentIdx);
}

// -------------------------------------------------------------
// Hitung Subtotal & Grand Total
// -------------------------------------------------------------
function hitungSubtotal(idx) {
  const row = document.getElementById(`row-${idx}`);
  if (!row) return;

  const qty = Number(row.querySelector('.input-jumlah')?.value || 0);
  const harga = Number(row.querySelector('.input-harga')?.value || 0);
  const subtotal = qty * harga;

  const subtotalDisplay = row.querySelector('.subtotal-display');
  if (subtotalDisplay) {
    subtotalDisplay.innerText = formatRupiah(subtotal);
  }

  hitungGrandTotal();
}

function hitungGrandTotal() {
  const rows = document.querySelectorAll('#bodyBelanja tr');
  let grandTotal = 0;

  rows.forEach(tr => {
    const qty = Number(tr.querySelector('.input-jumlah')?.value || 0);
    const harga = Number(tr.querySelector('.input-harga')?.value || 0);
    grandTotal += (qty * harga);
  });

  document.getElementById('labelGrandTotal').innerText = formatRupiah(grandTotal);
}

// -------------------------------------------------------------
// Quick Add dari Chip Rekomendasi
// -------------------------------------------------------------
function quickAddChip(sparepartId) {
  // Cek apakah item sudah ada di tabel belanja
  const allSelects = document.querySelectorAll('.select-sparepart');
  let existingRow = null;

  allSelects.forEach(s => {
    if (Number(s.value) === Number(sparepartId)) {
      existingRow = s.closest('tr');
    }
  });

  if (existingRow) {
    // Sudah ada: cukup increment quantity (+1)
    const qtyInput = existingRow.querySelector('.input-jumlah');
    if (qtyInput) {
      qtyInput.value = Number(qtyInput.value) + 1;
      const idx = existingRow.id.replace('row-', '');
      hitungSubtotal(idx);

      // Visual feedback
      existingRow.classList.add('bg-phosphor/20');
      setTimeout(() => existingRow.classList.remove('bg-phosphor/20'), 800);
      showToast('Kuantitas item bertambah (+1)');
    }
  } else {
    // Belum ada: tambahkan baris baru
    tambahBaris(sparepartId, 1);
    showToast('Item ditambahkan ke daftar belanjaan');
  }
}

// -------------------------------------------------------------
// Validasi Sebelum Form Disubmit
// -------------------------------------------------------------
function validateBeforeSubmit(e) {
  const rows = document.querySelectorAll('#bodyBelanja tr');
  if (rows.length === 0) {
    e.preventDefault();
    alert('Daftar belanja masih kosong. Harap tambahkan setidaknya 1 item sparepart.');
    return false;
  }

  // Cek jika ada select yang belum dipilih
  let hasEmptySelect = false;
  rows.forEach(tr => {
    const s = tr.querySelector('.select-sparepart');
    if (!s || !s.value) hasEmptySelect = true;
  });

  if (hasEmptySelect) {
    e.preventDefault();
    alert('Terdapat baris belanja yang belum memilih suku cadang sparepart. Harap pilih atau hapus baris kosong.');
    return false;
  }

  return true;
}

function bukaModalBaru() {
  document.getElementById('modalSparepartBaru').classList.remove('hidden');
  document.getElementById('modalError').classList.add('hidden');
  document.getElementById('namaBaruInput').value = '';
  document.getElementById('hargaBaruInput').value = '';
  document.getElementById('namaBaruInput').focus();
}

function tutupModalBaru() {
  document.getElementById('modalSparepartBaru').classList.add('hidden');
}

function handleSimpanSparepartBaru(e) {
  e.preventDefault();
  const nama = document.getElementById('namaBaruInput').value.trim();
  const harga = document.getElementById('hargaBaruInput').value;
  const btn = document.getElementById('btnSubmitModal');
  const errorBox = document.getElementById('modalError');

  btn.disabled = true;
  btn.innerText = 'Menyimpan...';
  errorBox.classList.add('hidden');

  const formData = new FormData();
  formData.append('action', 'ajax_tambah_master');
  formData.append('nama', nama);
  formData.append('harga', harga);
  formData.append('stok_awal', 0);

  fetch('restock.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    btn.disabled = false;
    btn.innerText = 'Daftarkan & Masukkan ke Belanjaan';

    if (!data.success) {
      errorBox.innerText = data.message;
      errorBox.classList.remove('hidden');

      // Jika duplicate, tawarkan untuk langsung menambah yang sudah ada
      if (data.duplicate && data.existing_id) {
        quickAddChip(data.existing_id);
        tutupModalBaru();
      }
      return;
    }

    // Sukses: perbarui master list client
    const newSp = data.item;
    masterSpareparts.push(newSp);

    // Perbarui semua dropdown yang ada
    const allSelects = document.querySelectorAll('.select-sparepart');
    allSelects.forEach(s => {
      const currentVal = s.value;
      const opt = document.createElement('option');
      opt.value = newSp.sparepart_id;
      opt.setAttribute('data-harga', newSp.harga);
      opt.setAttribute('data-stok', newSp.stok);
      opt.innerText = `${newSp.nama} (Stok: ${newSp.stok} • Jual: ${formatRupiah(newSp.harga)})`;
      s.appendChild(opt);
      s.value = currentVal;
    });

    // Tambah chip baru di atas
    const containerChips = document.getElementById('containerChips');
    const chipBtn = document.createElement('button');
    chipBtn.type = 'button';
    chipBtn.onclick = () => quickAddChip(newSp.sparepart_id);
    chipBtn.className = 'group inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-ash/70 hover:bg-charcoal border border-charcoal hover:border-phosphor/80 text-xs text-snow transition-all duration-150 cursor-pointer active:scale-95';
    chipBtn.innerHTML = `
      <span class="text-phosphor font-bold group-hover:scale-110 transition-transform">+</span>
      <span class="font-medium">${newSp.nama}</span>
      <span class="text-[10px] font-mono px-1.5 py-0.5 rounded text-amber-400 bg-amber-950/40">
        Stok: 0
      </span>
    `;
    containerChips.appendChild(chipBtn);

    // Langsung masukkan ke baris belanjaan
    tambahBaris(newSp.sparepart_id, 5);

    tutupModalBaru();
    showToast(`Sparepart "${newSp.nama}" berhasil didaftarkan dan ditambahkan ke daftar belanja.`);
  })
  .catch(err => {
    btn.disabled = false;
    btn.innerText = 'Daftarkan & Masukkan ke Belanjaan';
    errorBox.innerText = 'Terjadi kesalahan jaringan atau server.';
    errorBox.classList.remove('hidden');
  });
}
</script>

<?php include "../include/footer.php"; ?>
