<?php
session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
// Khusus Owner
harusOwner();

$id = $_GET["id"] ?? null;

if (!$id || !ctype_digit((string) $id)) {
  die("ID sparepart tidak valid.");
}

$stmt = $pdo->prepare("SELECT * FROM sparepart WHERE sparepart_id = ?");
$stmt->execute([$id]);
$sparepart = $stmt->fetch();

if (!$sparepart) {
  die("Data sparepart tidak ditemukan.");
}

$error = "";
$pageTitle = "Edit Sparepart";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nama = trim($_POST["nama"] ?? "");
  $harga = $_POST["harga"] ?? "";
  $stok = $_POST["stok"] ?? "";

  if (
    $nama === "" ||
    !is_numeric($harga) ||
    !is_numeric($stok) ||
    (float) $harga < 0 ||
    (int) $stok < 0
  ) {
    $error = "Data tidak valid. Periksa kembali nama, harga, dan stok.";
  } else {
    $stmt = $pdo->prepare("UPDATE sparepart SET nama = ?, harga = ?, stok = ? WHERE sparepart_id = ?");
    $stmt->execute([$nama, $harga, (int) $stok, $id]);

    header("Location: sparepart.php?pesan=berhasil_edit");
    exit;
  }
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
  <!-- Header -->
  <div class="border-b border-charcoal pb-6">
    <div class="flex items-center gap-2">
      <h1 class="text-2xl font-medium text-snow tracking-tight">Edit Data Sparepart</h1>
      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-phosphor/10 border border-phosphor/30 text-phosphor">Owner Only</span>
    </div>
    <p class="text-sm text-silver mt-1">Perbarui informasi harga dan kuantitas stok sparepart #<?= $sparepart['sparepart_id'] ?></p>
  </div>

  <div class="bg-obsidian border border-charcoal rounded-2xl p-6 max-w-xl">
    <?php if ($error !== ""): ?>
      <div class="bg-red-950/40 border border-red-900/60 text-red-300 p-3 rounded-xl mb-5 text-xs flex items-center gap-2">
        <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span><?= htmlspecialchars($error) ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Nama Sparepart</label>
        <input type="text" name="nama" required value="<?= htmlspecialchars($_POST["nama"] ?? $sparepart["nama"]) ?>"
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow">
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Harga Jual (Rp)</label>
        <input type="number" name="harga" min="0" step="0.01" required value="<?= htmlspecialchars($_POST["harga"] ?? $sparepart["harga"]) ?>"
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow font-mono">
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Kuantitas Stok</label>
        <input type="number" name="stok" min="0" step="1" required -value="<?= htmlspecialchars($_POST["stok"] ?? $sparepart["stok"]) ?>"
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow font-mono">
      </div>

      <div class="pt-3 flex items-center gap-3">
        <button type="submit"
                class="px-5 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          Simpan Perubahan
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

