<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$servis_id = $_GET["id"] ?? $_POST["servis_id"] ?? null;

$stmt = $pdo->prepare("SELECT
    s.*,
    p.nama AS nama_pelanggan,
    k.nomor_polisi,
    k.merk,
    k.model
  FROM servis s
  JOIN kendaraan k ON s.kendaraan_id = k.kendaraan_id
  JOIN pelanggan p ON k.pelanggan_id = p.pelanggan_id
  WHERE s.servis_id = ?
");

$stmt->execute([$servis_id]);
$servis = $stmt->fetch();

if (!$servis) {
  die("Servis tidak ditemukan.");
}

$pageTitle = "Pembayaran Servis #" . $servis_id;

// Hitung sparepart
$stmt = $pdo->prepare("SELECT COALESCE(SUM(subtotal),0) FROM detail_servis WHERE servis_id = ?");
$stmt->execute([$servis_id]);
$totalSparepart = (float) $stmt->fetchColumn();

$biayaJasa = (float) ($servis['biaya_jasa'] > 0 ? $servis['biaya_jasa'] : 5000);
$total = $biayaJasa + $totalSparepart;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $metode = $_POST["metode_pembayaran"];
  $jumlah_bayar = (float) $_POST["jumlah_bayar"];

  if (!in_array($metode, ['cash', 'qris'])) {
    die("Metode pembayaran tidak valid.");
  }

  if ($metode === 'qris') {
    $jumlah_bayar = $total;
  }

  if ($jumlah_bayar < $total) {
    die("Jumlah pembayaran kurang.");
  }

  $kembalian = $jumlah_bayar - $total;

  try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO transaksi(servis_id, total, metode_pembayaran, jumlah_bayar, kembalian, status_pembayaran)
      VALUES (?, ?, ?, ?, ?, 'lunas')
    ");
    $stmt->execute([$servis_id, $total, $metode, $jumlah_bayar, $kembalian]);

    // Update status servis jadi diambil / lunas
    $stmt = $pdo->prepare("UPDATE servis SET status = 'diambil' WHERE servis_id = ?");
    $stmt->execute([$servis_id]);

    $pdo->commit();

    header("Location: transaksi.php");
    exit;
  } catch (Exception $e) {
    $pdo->rollBack();
    die("Gagal memproses pembayaran: " . $e->getMessage());
  }
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
  <!-- Header -->
  <div class="border-b border-charcoal pb-6">
    <div class="flex items-center gap-2">
      <h1 class="text-2xl font-bold text-snow tracking-tight">Kasir Pembayaran Servis</h1>
      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">#<?= $servis_id ?></span>
    </div>
    <p class="text-sm text-silver mt-1">Selesaikan administrasi transaksi dan cetak rincian pembayaran pelanggan.</p>
  </div>

  <div class="bg-obsidian border border-charcoal rounded-2xl p-6 md:p-7 max-w-xl">
    <!-- Ringkasan Order Pelanggan -->
    <div class="p-4 rounded-xl bg-ash/40 border border-charcoal mb-6 flex items-start justify-between">
      <div>
        <span class="text-[11px] text-smoke uppercase tracking-wider font-mono">Pelanggan</span>
        <h3 class="text-sm font-medium text-snow mt-0.5"><?= htmlspecialchars($servis['nama_pelanggan']) ?></h3>
      </div>
      <div class="text-right">
        <span class="text-[11px] text-smoke uppercase tracking-wider font-mono">Unit Motor</span>
        <div class="text-sm font-medium text-snow mt-0.5"><?= htmlspecialchars($servis['merk'] . ' ' . $servis['model']) ?></div>
        <span class="text-xs font-mono text-phosphor"><?= htmlspecialchars($servis['nomor_polisi']) ?></span>
      </div>
    </div>

    <!-- Rincian Biaya -->
    <div class="border-t border-b border-charcoal py-4 space-y-2.5 mb-6 text-sm">
      <div class="flex justify-between text-silver">
        <span>Biaya Jasa Servis</span>
        <span class="font-mono text-snow">Rp <?= number_format($biayaJasa, 0, ',', '.') ?></span>
      </div>
      <div class="flex justify-between text-silver">
        <span>Total Pemakaian Sparepart</span>
        <span class="font-mono text-snow">Rp <?= number_format($totalSparepart, 0, ',', '.') ?></span>
      </div>
      <div class="flex justify-between text-base font-medium pt-3 border-t border-charcoal/60">
        <span class="text-snow">Total yang Harus Dibayar</span>
        <span class="font-mono text-phosphor text-lg">Rp <?= number_format($total, 0, ',', '.') ?></span>
      </div>
    </div>

    <!-- Form Pembayaran -->
    <form method="POST" class="space-y-4">
      <input type="hidden" name="servis_id" value="<?= $servis_id ?>">

      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Metode Pembayaran</label>
        <select name="metode_pembayaran"
                id="metodePembayaran"
                onchange="ubahMetode()"
                required
                class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2.5 text-sm text-snow font-mono">
          <option value="cash">Tunai (Cash)</option>
          <option value="qris">QRIS / Non-Tunai</option>
        </select>
      </div>

      <div id="jumlahBayarGroup">
        <label class="block text-xs font-medium text-silver mb-1.5">Nominal Uang Diterima (Rp)</label>
        <input type="number"
               name="jumlah_bayar"
               id="inputBayar"
               min="<?= $total ?>"
               value="<?= $total ?>"
               required
               class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2.5 text-sm text-snow font-mono">
        <p class="text-[11px] text-smoke mt-1 font-mono">Kembalian akan dihitung secara otomatis oleh sistem.</p>
      </div>

      <div class="pt-4 flex items-center gap-3">
        <button type="submit"
                class="flex-1 py-2.5 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-sm tracking-tight transition-all flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          <span>Konfirmasi & Selesaikan Transaksi</span>
        </button>
        <a href="transaksi.php"
           class="px-4 py-2.5 rounded-full border border-slate text-silver hover:text-snow hover:border-graphite text-xs transition-all">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>

<script>
  function ubahMetode() {
    const metode = document.getElementById("metodePembayaran").value;
    const group = document.getElementById("jumlahBayarGroup");
    const input = document.getElementById("inputBayar");

    if (metode === "qris") {
      group.style.display = "none";
      input.value = "<?= $total ?>";
    } else {
      group.style.display = "block";
    }
  }
</script>

<?php include "../include/footer.php"; ?>
