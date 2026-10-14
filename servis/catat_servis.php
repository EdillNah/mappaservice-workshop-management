<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$pageTitle = "Catat Servis";

$pelanggan = $pdo->query("SELECT * FROM pelanggan ORDER BY nama ASC")->fetchAll();
$mekanik = $pdo->query("SELECT * FROM mekanik WHERE status = 'tersedia' ORDER BY nama ASC")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $kendaraan_id = $_POST["kendaraan_id"];
  $mekanik_id = $_POST["mekanik_id"];
  $keluhan = trim($_POST["keluhan"] ?? "");
  $biaya_jasa = isset($_POST["biaya_jasa"]) && $_POST["biaya_jasa"] !== '' ? (float) $_POST["biaya_jasa"] : 5000;
  
  try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO servis (kendaraan_id, mekanik_id, keluhan, biaya_jasa, status) VALUES (?, ?, ?, ?, 'menunggu')");
    $stmt->execute([$kendaraan_id, $mekanik_id, $keluhan, $biaya_jasa]);

    $stmt = $pdo->prepare("UPDATE mekanik SET status = 'sibuk' WHERE mekanik_id = ?");
    $stmt->execute([$mekanik_id]);

    $pdo->commit();

    header("Location: servis.php");
    exit;
  } catch (Exception $e) {
    $pdo->rollBack();
    die("Gagal menyimpan servis: " . $e->getMessage());
  }
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
  <!-- Header -->
  <div class="border-b border-charcoal pb-6">
    <div class="flex items-center gap-2">
      <h1 class="text-2xl font-bold text-snow tracking-tight">Catat Servis Baru</h1>
      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">Pendaftaran</span>
    </div>
    <p class="text-sm text-silver mt-1">Registrasi kedatangan motor, assign mekanik tersedia, dan catat keluhan pelanggan.</p>
  </div>

  <div class="bg-obsidian border border-charcoal rounded-2xl p-6 md:p-7 max-w-2xl">
    <form method="POST" class="space-y-5">
      <!-- Kendaraan -->
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label class="block text-xs font-medium text-silver">Pilih Kendaraan & Pemilik</label>
          <a href="../kendaraan/tambah_kendaraan.php" class="text-xs text-phosphor hover:underline font-mono">+ Tambah Motor Baru</a>
        </div>
        <select name="kendaraan_id" required class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2.5 text-sm text-snow cursor-pointer transition-colors focus:border-phosphor">
          <option value="">-- Pilih Kendaraan (Pelanggan - Plat - Motor) --</option>
          <?php
            $kendaraan = $pdo->query("
              SELECT
                k.kendaraan_id,
                k.nomor_polisi,
                k.merk,
                k.model,
                p.nama AS nama_pelanggan
              FROM kendaraan k
              JOIN pelanggan p ON k.pelanggan_id = p.pelanggan_id
              ORDER BY p.nama ASC
            ")->fetchAll();
          ?>
          <?php foreach ($kendaraan as $item): ?>
            <option value="<?= $item['kendaraan_id'] ?>">
              <?= htmlspecialchars($item['nama_pelanggan']) ?> • <?= htmlspecialchars($item['nomor_polisi']) ?> • <?= htmlspecialchars($item['merk'] . ' ' . $item['model']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Mekanik -->
      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Tugaskan Mekanik (Status: Tersedia)</label>
        <?php if (empty($mekanik)): ?>
          <div class="p-3 rounded-xl bg-amber-950/30 border border-amber-900/50 text-amber-300 text-xs mb-2">
            Saat ini semua mekanik berstatus <strong>Sibuk</strong>. Anda dapat mengubah status mekanik di menu <a href="../mekanik/mekanik.php" class="underline text-snow">Data Mekanik</a>.
          </div>
        <?php endif; ?>
        <select name="mekanik_id" required class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2.5 text-sm text-snow cursor-pointer transition-colors focus:border-phosphor">
          <option value="">-- Pilih Mekanik Tersedia --</option>
          <?php foreach ($mekanik as $item): ?>
            <option value="<?= $item['mekanik_id'] ?>">
              <?= htmlspecialchars($item['nama']) ?> (Spesialisasi: <?= htmlspecialchars($item['spesialisasi']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Keluhan -->
      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Keluhan Kerusakan / Permintaan Servis</label>
        <textarea name="keluhan"
                  rows="4"
                  required
                  placeholder="Contoh: Tarikan gas berat, rem belakang berdecit, ganti oli mesin dan busi..."
                  class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2.5 text-sm text-snow placeholder-smoke"></textarea>
      </div>

      <div class="pt-3 flex items-center gap-3">
        <button type="submit"
                class="px-5 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          Simpan & Catat Servis
        </button>
        <a href="servis.php"
           class="px-4 py-2 rounded-full border border-slate text-silver hover:text-snow hover:border-graphite text-xs transition-all">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>

<?php include "../include/footer.php"; ?>
