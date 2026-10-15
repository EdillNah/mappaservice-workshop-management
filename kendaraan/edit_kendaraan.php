<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$id = (int) ($_GET["id"] ?? 0);
$pageTitle = "Edit Kendaraan";

$sql = "SELECT * FROM kendaraan WHERE kendaraan_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$kendaraan = $stmt->fetch();

if (!$kendaraan) {
  die("Data kendaraan tidak ditemukan.");
}

$stmt = $pdo->query("SELECT * FROM pelanggan ORDER BY nama");
$pelanggan = $stmt->fetchAll();

if (isset($_POST["update"])) {
  $pelanggan_id = $_POST["pelanggan_id"];
  $nomor_polisi = strtoupper(trim($_POST["nomor_polisi"] ?? ""));
  $merk = trim($_POST["merk"] ?? "");
  $model = trim($_POST["model"] ?? "");
  $tahun = trim($_POST["tahun"] ?? "");

  $sql = "UPDATE kendaraan SET pelanggan_id = ?, nomor_polisi = ?, merk = ?, model = ?, tahun = ?
          WHERE kendaraan_id = ?";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $pelanggan_id,
    $nomor_polisi,
    $merk,
    $model,
    $tahun,
    $id
  ]);

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
      <h1 class="text-2xl font-medium text-snow tracking-tight">Edit Data Kendaraan Motor</h1>
      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">#<?= $kendaraan["kendaraan_id"] ?></span>
    </div>
    <p class="text-sm text-silver mt-1">Perbarui nomor polisi, merk tipe motor, dan pemilik terdaftar.</p>
  </div>

  <div class="bg-obsidian border border-charcoal rounded-2xl p-6 max-w-xl">
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Pemilik Terdaftar (Pelanggan)</label>
        <select name="pelanggan_id" required class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow">
          <?php foreach ($pelanggan as $data): ?>
            <option value="<?= $data["pelanggan_id"] ?>" <?= $data["pelanggan_id"] == $kendaraan["pelanggan_id"] ? "selected" : "" ?>>
              <?= htmlspecialchars($data["nama"]) ?> (<?= htmlspecialchars($data["no_telpon"] ?: 'No Telp -') ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Nomor Polisi (Plat Motor)</label>
        <input type="text"
               name="nomor_polisi"
               value="<?= htmlspecialchars($kendaraan["nomor_polisi"]) ?>"
               required
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow font-mono uppercase">
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-silver mb-1.5">Merk Pabrikan</label>
          <input type="text"
                 name="merk"
                 value="<?= htmlspecialchars($kendaraan["merk"]) ?>"
                 class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow">
        </div>

        <div>
          <label class="block text-xs font-medium text-silver mb-1.5">Model / Tipe</label>
          <input type="text"
                 name="model"
                 value="<?= htmlspecialchars($kendaraan["model"]) ?>"
                 class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow">
        </div>
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Tahun Perakitan</label>
        <input type="number"
               name="tahun"
               value="<?= htmlspecialchars($kendaraan["tahun"]) ?>"
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow font-mono">
      </div>

      <div class="pt-3 flex items-center gap-3">
        <button type="submit"
                name="update"
                class="px-5 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          Simpan Perubahan
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