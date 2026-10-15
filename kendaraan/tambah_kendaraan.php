<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$pageTitle = "Tambah Kendaraan";

$stmt = $pdo->query("SELECT * FROM pelanggan ORDER BY nama");
$pelanggan = $stmt->fetchAll();

if (isset($_POST["simpan"])) {
  $pelanggan_id = $_POST["pelanggan_id"];
  $nomor_polisi = strtoupper(trim($_POST["nomor_polisi"] ?? ""));
  $merk = trim($_POST["merk"] ?? "");
  $model = trim($_POST["model"] ?? "");
  $tahun = trim($_POST["tahun"] ?? "");

  $sql = "INSERT INTO kendaraan (pelanggan_id, nomor_polisi, merk, model, tahun) VALUES (?, ?, ?, ?, ?)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$pelanggan_id, $nomor_polisi, $merk, $model, $tahun]);

  header("Location: kendaraan.php");
  exit;
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
  <!-- Header -->
  <div class="border-b border-charcoal pb-6">
    <div class="flex items-center gap-2">
      <h1 class="text-2xl font-semibold text-snow tracking-tight">Tambah Kendaraan Motor</h1>
      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">Data Master</span>
    </div>
    <p class="text-sm text-silver mt-1">Daftarkan sepeda motor baru yang dimiliki pelanggan ke dalam database bengkel.</p>
  </div>

  <div class="bg-obsidian border border-charcoal rounded-2xl p-6 max-w-xl">
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-snow mb-1.5">Pemilik Terdaftar (Pelanggan)</label>
        <select name="pelanggan_id" required class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow">
          <option value="">-- Pilih Pelanggan Pemilik --</option>
          <?php foreach ($pelanggan as $data): ?>
            <option value="<?= $data["pelanggan_id"] ?>"><?= htmlspecialchars($data["nama"]) ?> (<?= htmlspecialchars($data["no_telpon"] ?: 'No Telp -') ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-xs font-medium text-snow mb-1.5">Nomor Polisi (Plat Motor)</label>
        <input type="text"
               name="nomor_polisi"
               required
               placeholder="Contoh: DD 1234 AB"
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke font-mono uppercase">
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-snow mb-1.5">Merk Pabrikan</label>
          <input type="text"
                 name="merk"
                 placeholder="Honda / Yamaha / Suzuki"
                 class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke">
        </div>

        <div>
          <label class="block text-xs font-medium text-snow mb-1.5">Model / Tipe</label>
          <input type="text"
                 name="model"
                 placeholder="Vario 160 / NMAX"
                 class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke">
        </div>
      </div>

      <div>
        <label class="block text-xs font-medium text-snow mb-1.5">Tahun Perakitan</label>
        <input type="number"
               name="tahun"
               placeholder="2022"
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke font-mono">
      </div>

      <div class="pt-3 flex items-center gap-3">
        <button type="submit"
                name="simpan"
                class="px-5 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          Simpan Kendaraan
        </button>
        <a href="kendaraan.php"
           class="px-4 py-2 rounded-full border border-slate text-silver hover:text-snow hover:border-graphite text-xs transition-all">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>

<?php include "../include/footer.php"; ?>

