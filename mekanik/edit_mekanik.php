<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
// Khusus Owner
harusOwner();

$id = $_GET["id"] ?? null;
$stmt = $pdo->prepare("SELECT * FROM mekanik WHERE mekanik_id = ?");
$stmt->execute([$id]);
$mekanik = $stmt->fetch();

if (!$mekanik) {
  die("Data mekanik tidak ditemukan.");
}

$pageTitle = "Edit Mekanik";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nama = trim($_POST["nama"] ?? "");
  $telepon = trim($_POST["no_telepon"] ?? "");
  $spesialisasi = trim($_POST["spesialisasi"] ?? "");
  $status = $_POST["status"] ?? "tersedia";

  $stmt = $pdo->prepare("UPDATE mekanik SET
      nama = ?,
      no_telpon = ?,
      spesialisasi = ?,
      status = ?
    WHERE mekanik_id = ?
  ");

  $stmt->execute([$nama, $telepon, $spesialisasi, $status, $id]);

  header("Location: mekanik.php");
  exit;
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
  <div class="border-b border-charcoal pb-6">
    <div class="flex items-center gap-2">
      <h1 class="text-2xl font-bold text-snow tracking-tight">Edit Data Mekanik</h1>
      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-phosphor/10 border border-phosphor/30 text-phosphor">Owner Only</span>
    </div>
    <p class="text-sm text-silver mt-1">Perbarui profil dan data mekanik #<?= $mekanik['mekanik_id'] ?></p>
  </div>

  <div class="bg-obsidian border border-charcoal rounded-2xl p-6 max-w-xl">
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Nama Mekanik</label>
        <input type="text"
               name="nama"
               value="<?= htmlspecialchars($mekanik['nama']) ?>"
               required
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow">
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Nomor Telepon</label>
        <input type="text"
               name="no_telepon"
               value="<?= htmlspecialchars($mekanik['no_telpon'] ?? $mekanik['no_telepon'] ?? '') ?>"
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow font-mono">
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Spesialisasi</label>
        <input type="text"
               name="spesialisasi"
               value="<?= htmlspecialchars($mekanik['spesialisasi'] ?? '') ?>"
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow">
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Status Pekerjaan</label>
        <select name="status" class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow">
          <option value="tersedia" <?= $mekanik['status'] === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
          <option value="sibuk" <?= $mekanik['status'] === 'sibuk' ? 'selected' : '' ?>>Sibuk</option>
        </select>
      </div>

      <div class="pt-3 flex items-center gap-3">
        <button type="submit"
                class="px-5 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          Simpan Perubahan
        </button>
        <a href="mekanik.php"
           class="px-4 py-2 rounded-full border border-slate text-silver hover:text-snow hover:border-graphite text-xs transition-all">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>

<?php include "../include/footer.php"; ?>
