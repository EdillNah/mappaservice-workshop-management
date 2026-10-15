<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
// Khusus Owner
harusOwner();

$pageTitle = "Tambah Sparepart";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nama = trim($_POST["nama"] ?? "");
  $harga = (float) ($_POST["harga"] ?? 0);
  $stok = (int) ($_POST["stok"] ?? 0);

  $stmt = $pdo->prepare("INSERT INTO sparepart(nama, harga, stok) VALUES (?, ?, ?)");
  $stmt->execute([$nama, $harga, $stok]);

  header("Location: sparepart.php");
  exit;
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
  <!-- Header -->
  <div class="border-b border-charcoal pb-6">
    <div class="flex items-center gap-2">
      <h1 class="text-2xl font-medium text-snow tracking-tight">Tambah Sparepart Baru</h1>
      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-phosphor/10 border border-phosphor/30 text-phosphor">Owner Only</span>
    </div>
    <div>
      <a href="restock.php"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Restock Stok Banyak (Batch) &rarr;</span>
      </a>
    </div>
  </div>

  <div class="p-3.5 rounded-xl bg-ash/40 border border-charcoal flex items-center justify-between text-xs text-silver">
    <div class="flex items-center gap-2.5">
      <svg class="w-4 h-4 text-phosphor shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <span>Formulir ini untuk mendaftarkan <strong>jenis suku cadang baru</strong>. Jika ingin menambah jumlah stok barang yang sudah ada, gunakan fitur <a href="restock.php" class="text-phosphor underline font-medium">Restock Sparepart (Batch)</a>.</span>
    </div>
  </div>

  <div class="bg-obsidian border border-charcoal rounded-2xl p-6 max-w-xl">
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Nama Sparepart</label>
        <input type="text"
               name="nama"
               placeholder="Contoh: Kampas Rem Depan Vario"
               required
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke">
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Harga Jual (Rp)</label>
        <input type="number"
               name="harga"
               min="0"
               placeholder="50000"
               required
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke font-mono">
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Jumlah Stok Awal</label>
        <input type="number"
               name="stok"
               min="0"
               placeholder="10"
               required
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke font-mono">
      </div>

      <div class="pt-3 flex items-center gap-3">
        <button type="submit"
                class="px-5 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          Simpan Sparepart
        </button>
        <a href="sparepart.php"
           class="px-4 py-2 rounded-full border border-slate text-silver hover:text-snow hover:border-graphite text-xs transition-all">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>

<?php include "../include/footer.php"; ?>