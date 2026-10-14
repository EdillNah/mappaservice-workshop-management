<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$servis_id = $_GET["servis_id"] ?? $_POST["servis_id"] ?? null;

if (!$servis_id) {
  die("ID servis tidak ditemukan.");
}

$stmt = $pdo->prepare("SELECT * FROM servis WHERE servis_id = ?");
$stmt->execute([$servis_id]);
$servis = $stmt->fetch();

if (!$servis) {
  die("Servis tidak ditemukan.");
}

$pageTitle = "Tambah Pemakaian Sparepart";

$sparepart = $pdo->query("SELECT * FROM sparepart WHERE stok > 0 ORDER BY nama ASC")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $sparepart_id = $_POST["sparepart_id"];
  $jumlah = (int) $_POST["jumlah"];
  
  try {
    $pdo->beginTransaction();
    // Ambil data sparepart
    $stmt = $pdo->prepare("SELECT * FROM sparepart WHERE sparepart_id = ? FOR UPDATE");
    $stmt->execute([$sparepart_id]);
    $item = $stmt->fetch();

    if (!$item) {
      throw new Exception("Sparepart tidak ditemukan.");
    }
    // Cek stok
    if ($jumlah <= 0) {
      throw new Exception("Jumlah tidak valid.");
    }

    if ($jumlah > $item['stok']) {
      throw new Exception("Stok tidak mencukupi. Stok tersedia: " . $item['stok']);
    }
    // Hitung subtotal
    $subtotal = $item['harga'] * $jumlah;

    // Simpan detail
    $stmt = $pdo->prepare("INSERT INTO detail_servis (
          servis_id,
          sparepart_id,
          jumlah,
          harga,
          subtotal
        )
      VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([$servis_id, $sparepart_id, $jumlah, $item['harga'], $subtotal]);

    // Kurangi stok
    $stmt = $pdo->prepare("UPDATE sparepart SET stok = stok - ? WHERE sparepart_id = ?");
    $stmt->execute([$jumlah, $sparepart_id]);

    $pdo->commit();

    header("Location: detail_servis.php?id=" . $servis_id);
    exit;
  } catch (Exception $e) {
    $pdo->rollBack();
    die("Gagal mencatat sparepart: " . $e->getMessage());
  }
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
  <!-- Header -->
  <div class="border-b border-charcoal pb-6">
    <div class="flex items-center gap-2">
      <h1 class="text-2xl font-medium text-snow tracking-tight">Catat Penggunaan Sparepart</h1>
      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">Servis #<?= $servis_id ?></span>
    </div>
    <p class="text-sm text-silver mt-1">Pilih suku cadang yang dipasang pada kendaraan pelanggan.</p>
  </div>

  <div class="bg-obsidian border border-charcoal rounded-2xl p-6 max-w-xl">
    <form method="POST" class="space-y-4">
      <input type="hidden" name="servis_id" value="<?= $servis_id ?>">

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Pilih Suku Cadang / Sparepart</label>
        <select name="sparepart_id" required class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow">
          <option value="">-- Pilih item sparepart --</option>
          <?php foreach ($sparepart as $item): ?>
            <option value="<?= $item['sparepart_id'] ?>">
              <?= htmlspecialchars($item['nama']) ?> • Rp <?= number_format($item['harga'], 0, ',', '.') ?> (Stok: <?= $item['stok'] ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Jumlah Digunakan</label>
        <input type="number"
               name="jumlah"
               min="1"
               value="1"
               required
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow font-mono">
      </div>

      <div class="pt-3 flex items-center gap-3">
        <button type="submit"
                class="px-5 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          Tambahkan ke Servis
        </button>
        <a href="detail_servis.php?id=<?= $servis_id ?>"
           class="px-4 py-2 rounded-full border border-slate text-silver hover:text-snow hover:border-graphite text-xs transition-all">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>

<?php include "../include/footer.php"; ?>