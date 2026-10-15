<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$pageTitle = "Tambah Pelanggan";

if (isset($_POST["simpan"])) {
  $nama = trim($_POST["nama"] ?? "");
  $no_telpon = trim($_POST["no_telpon"] ?? "");
  $alamat = trim($_POST["alamat"] ?? "");

  $sql = "INSERT INTO pelanggan (nama, no_telpon, alamat) VALUES (?, ?, ?)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$nama, $no_telpon, $alamat]);

  header("Location: pelanggan.php");
  exit;
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
  <!-- Header -->
  <div class="border-b border-charcoal pb-6">
    <div class="flex items-center gap-2">
      <h1 class="text-2xl font-bold text-snow tracking-tight">Tambah Pelanggan Baru</h1>
      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">Data Master</span>
    </div>
    <p class="text-sm text-silver mt-1">Registrasi identitas pemilik kendaraan sebelum pencatatan servis.</p>
  </div>

  <div class="bg-obsidian border border-charcoal rounded-2xl p-6 max-w-xl">
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Nama Lengkap Pelanggan</label>
        <input type="text"
               name="nama"
               placeholder="Contoh: Budi Santoso"
               required
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke">
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Nomor Telepon / WhatsApp</label>
        <input type="text"
               name="no_telpon"
               placeholder="Contoh: 08123456789"
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke font-mono">
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Alamat Tempat Tinggal</label>
        <textarea name="alamat"
                  rows="3"
                  placeholder="Contoh: Jl. Merdeka No. 45, Kota Makassar"
                  class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke"></textarea>
      </div>

      <div class="pt-3 flex items-center gap-3">
        <button type="submit"
                name="simpan"
                class="px-5 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          Simpan Pelanggan
        </button>
        <a href="pelanggan.php"
           class="px-4 py-2 rounded-full border border-slate text-silver hover:text-snow hover:border-graphite text-xs transition-all">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>

<?php include "../include/footer.php"; ?>

