<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$pageTitle = "Catat Servis Cepat";

$sql = "SELECT kendaraan.kendaraan_id, kendaraan.nomor_polisi, kendaraan.merk, kendaraan.model, pelanggan.nama
        FROM kendaraan
        INNER JOIN pelanggan ON kendaraan.pelanggan_id = pelanggan.pelanggan_id
        ORDER BY pelanggan.nama";
$stmt = $pdo->query($sql);
$kendaraan = $stmt->fetchAll();

if (isset($_POST["simpan"])) {
  $kendaraan_id = $_POST["kendaraan_id"];
  $keluhan = trim($_POST["keluhan"] ?? "");
  $biaya_jasa = isset($_POST["biaya_jasa"]) && $_POST["biaya_jasa"] !== '' ? (float) $_POST["biaya_jasa"] : 5000;

  $sql = "INSERT INTO servis (kendaraan_id, keluhan, biaya_jasa) VALUES (?, ?, ?)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$kendaraan_id, $keluhan, $biaya_jasa]);

  header("Location: servis.php");
  exit;
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
  <!-- Header -->
  <div class="border-b border-charcoal pb-6">
    <div class="flex items-center gap-2">
      <h1 class="text-2xl font-medium text-snow tracking-tight">Catat Servis Kendaraan</h1>
      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">Quick Form</span>
    </div>
    <p class="text-sm text-silver mt-1">Pendaftaran servis cepat tanpa penugasan mekanik awal.</p>
  </div>

  <div class="bg-obsidian border border-charcoal rounded-2xl p-6 max-w-xl">
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Pilih Kendaraan & Pemilik</label>
        <select name="kendaraan_id" required class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow">
          <option value="">-- Pilih Kendaraan --</option>
          <?php foreach ($kendaraan as $data): ?>
            <option value="<?= $data["kendaraan_id"] ?>">
              <?= htmlspecialchars($data["nama"]) ?> • <?= htmlspecialchars($data["nomor_polisi"]) ?> • <?= htmlspecialchars($data["merk"] . ' ' . $data["model"]) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Keluhan Kerusakan</label>
        <textarea name="keluhan"
                  rows="4"
                  required
                  placeholder="Keluhan dari pemilik kendaraan..."
                  class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke"></textarea>
      </div>



      <div class="pt-3 flex items-center gap-3">
        <button type="submit"
                name="simpan"
                class="px-5 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          Simpan Servis
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