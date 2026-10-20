<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
// Khusus Owner
harusOwner();

$pageTitle = "Tambah Mekanik";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nama = trim($_POST["nama"] ?? "");
  $telepon = trim($_POST["no_telepon"] ?? "");
  $spesialisasi = trim($_POST["spesialisasi"] ?? "");

  $stmt = $pdo->prepare("INSERT INTO mekanik (nama, no_telpon, spesialisasi) VALUES (?, ?, ?)");
  $stmt->execute([$nama, $telepon, $spesialisasi]);

  header("Location: mekanik.php");
  exit;
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
  <!-- Header -->
  <div class="border-b border-charcoal pb-6">
    <div class="flex items-center gap-2">
      <h1 class="text-2xl font-bold text-snow tracking-tight">Tambah Mekanik Baru</h1>
      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-phosphor/10 border border-phosphor/30 text-phosphor">Owner Only</span>
    </div>
    <p class="text-sm text-silver mt-1">Daftarkan teknisi baru ke dalam sistem bengkel.</p>
  </div>

  <div class="bg-obsidian border border-charcoal rounded-2xl p-6 max-w-xl">
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Nama Mekanik</label>
        <input type="text"
               name="nama"
               placeholder="Contoh: Rahmat Hidayat"
               required
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke">
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Nomor Telepon / WhatsApp</label>
        <input type="text"
               name="no_telepon"
               placeholder="Contoh: 081234567890"
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke font-mono">
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Spesialisasi</label>
        <input type="text"
               name="spesialisasi"
               placeholder="Contoh: Injeksi, Kelistrikan, Mesin 2T/4T"
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke">
      </div>

      <div class="pt-3 flex items-center gap-3">
        <button type="submit"
                class="px-5 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          Simpan Mekanik
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
