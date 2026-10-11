<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$servis_id = $_GET["id"] ?? $_POST["servis_id"] ?? null;
$pageTitle = "Riwayat Servis";

// If specific service ID is requested, handle detail / receipt / payment view
if ($servis_id) {
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

  if ($servis) {
    // Hitung sparepart
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(subtotal),0) FROM detail_servis WHERE servis_id = ?");
    $stmt->execute([$servis_id]);
    $biayaJasa = (float) ($servis['biaya_jasa'] > 0 ? $servis['biaya_jasa'] : 5000);
    $total = $biayaJasa + $totalSparepart;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
      $metode = $_POST["metode_pembayaran"] ?? 'cash';
      $jumlah_bayar = (float) ($_POST["jumlah_bayar"] ?? $total);

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

      $stmt = $pdo->prepare("INSERT INTO transaksi(servis_id, total, metode_pembayaran, jumlah_bayar, kembalian, status_pembayaran)
        VALUES (?, ?, ?, ?, ?, 'lunas')
      ");
      $stmt->execute([$servis_id, $total, $metode, $jumlah_bayar, $kembalian]);
      header("Location: ../transaksi/transaksi.php");
      exit;
    }

    include "../include/header.php";
    include "../include/sidebar.php";
    ?>
    <div class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
      <div class="border-b border-charcoal pb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-snow tracking-tight">Rincian Transaksi Servis #<?= $servis_id ?></h1>
          <p class="text-sm text-silver mt-1">Bukti catatan perbaikan dan pembayaran kendaraan.</p>
        </div>
        <a href="riwayat_servis.php" class="px-4 py-1.5 rounded-full border border-slate text-silver hover:text-snow text-xs transition-all">
          &larr; Kembali ke Daftar
        </a>
      </div>

      <div class="bg-obsidian border border-charcoal rounded-2xl p-6 md:p-7 max-w-xl">
        <div class="p-4 rounded-xl bg-ash/40 border border-charcoal mb-6 flex items-start justify-between">
          <div>
            <span class="text-[11px] text-smoke uppercase tracking-wider font-mono">Pelanggan</span>
            <h3 class="text-sm font-medium text-snow mt-0.5"><?= htmlspecialchars($servis['nama_pelanggan']) ?></h3>
          </div>
          <div class="text-right">
            <span class="text-[11px] text-smoke uppercase tracking-wider font-mono">Kendaraan</span>
            <div class="text-sm font-medium text-snow mt-0.5"><?= htmlspecialchars($servis['merk'] . ' ' . $servis['model']) ?></div>
            <span class="text-xs font-mono text-phosphor"><?= htmlspecialchars($servis['nomor_polisi']) ?></span>
          </div>
        </div>

        <div class="border-t border-b border-charcoal py-4 space-y-2 mb-6 text-sm">
          <div class="flex justify-between text-silver">
            <span>Biaya Jasa Servis</span>
            <span class="font-mono text-snow">Rp <?= number_format($biayaJasa, 0, ',', '.') ?></span>
          </div>
          <div class="flex justify-between text-silver">
            <span>Pemakaian Sparepart</span>
            <span class="font-mono text-snow">Rp <?= number_format($totalSparepart, 0, ',', '.') ?></span>
          </div>
          <div class="flex justify-between text-base font-medium pt-3 border-t border-charcoal/60">
            <span class="text-snow">Total Tagihan</span>
            <span class="font-mono text-phosphor text-lg">Rp <?= number_format($total, 0, ',', '.') ?></span>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <a href="../transaksi/bayar.php?id=<?= $servis_id ?>"
             class="flex-1 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light text-center font-medium text-xs tracking-tight transition-all">
            Buka Kasir Pembayaran
          </a>
          <a href="../servis/detail_servis.php?id=<?= $servis_id ?>"
             class="px-4 py-2 rounded-full border border-slate text-silver hover:text-snow text-xs transition-all">
            Lihat Detail Servis
          </a>
        </div>
      </div>
    </div>
    <?php
    include "../include/footer.php";
    exit;
  }
}

// Default list view: Riwayat Semua Servis
$stmt = $pdo->query("SELECT
    s.servis_id,
    s.tanggal_masuk,
    s.keluhan,
    s.diagnosa,
    s.status,
    s.biaya_jasa,
    p.nama AS nama_pelanggan,
    k.nomor_polisi,
    k.merk,
    k.model,
    m.nama AS nama_mekanik,
    t.total,
    t.status_pembayaran,
    t.metode_pembayaran,
    t.tanggal_transaksi
  FROM servis s
  JOIN kendaraan k ON s.kendaraan_id = k.kendaraan_id
  JOIN pelanggan p ON k.pelanggan_id = p.pelanggan_id
  LEFT JOIN mekanik m ON s.mekanik_id = m.mekanik_id
  LEFT JOIN transaksi t ON s.servis_id = t.servis_id
  ORDER BY s.servis_id DESC
");
$riwayat = $stmt->fetchAll();

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-7xl mx-auto space-y-6">
  <!-- Header & Actions -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-charcoal pb-6">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-2xl font-bold text-snow tracking-tight">Riwayat Servis & Arsip</h1>
        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">
          <?= count($riwayat) ?> Riwayat Servis
        </span>
      </div>
      <p class="text-sm text-silver mt-1">Arsip riwayat perbaikan motor dan catatan pembayaran pelanggan dari awal hingga selesai.</p>
    </div>

    <div>
      <a href="../servis/catat_servis.php"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Servis Baru</span>
      </a>
    </div>
  </div>

  <!-- Table Card -->
  <div class="bg-obsidian border border-charcoal rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-ash/50 border-b border-charcoal text-[11px] uppercase tracking-wider text-smoke font-mono">
          <tr>
            <th class="px-6 py-3.5">ID Servis</th>
            <th class="px-6 py-3.5">Tanggal</th>
            <th class="px-6 py-3.5">Pelanggan</th>
            <th class="px-6 py-3.5">Kendaraan</th>
            <th class="px-6 py-3.5">Teknisi</th>
            <th class="px-6 py-3.5 whitespace-nowrap">Status Servis</th>
            <th class="px-6 py-3.5">Pembayaran</th>
            <th class="px-6 py-3.5 text-right">Rincian</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-charcoal text-silver">
          <?php if (empty($riwayat)): ?>
            <tr>
              <td colspan="8" class="px-6 py-12 text-center text-smoke font-mono text-sm">
                Belum ada arsip riwayat servis.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($riwayat as $r): ?>
              <tr class="hover:bg-ash/30 transition-colors">
                <td class="px-6 py-4 font-mono text-xs text-smoke">#<?= $r['servis_id'] ?></td>
                <td class="px-6 py-4 font-mono text-xs text-smoke whitespace-nowrap">
                  <?= date('d M Y', strtotime($r['tanggal_masuk'])) ?>
                </td>
                <td class="px-6 py-4 font-medium text-snow"><?= htmlspecialchars($r['nama_pelanggan']) ?></td>
                <td class="px-6 py-4">
                  <div class="text-snow text-xs"><?= htmlspecialchars($r['merk'] . ' ' . $r['model']) ?></div>
                  <div class="text-[11px] font-mono text-smoke"><?= htmlspecialchars($r['nomor_polisi']) ?></div>
                </td>
                <td class="px-6 py-4 text-xs"><?= htmlspecialchars($r['nama_mekanik'] ?? '-') ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <?= renderStatusBadge($r['status']) ?>
                </td>
                <td class="px-6 py-4">
                  <?php if ($r['status_pembayaran'] === 'lunas'): ?>
                    <span class="inline-flex items-center gap-1 text-xs text-phosphor font-mono">
                      <span>Rp <?= number_format($r['total'], 0, ',', '.') ?></span>
                      <span class="text-[10px] px-1.5 py-0.2 rounded bg-phosphor/10 border border-phosphor/20">LUNAS</span>
                    </span>
                  <?php else: ?>
                    <span class="text-xs text-smoke font-mono">Belum Lunas</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-right">
                  <a href="../servis/detail_servis.php?id=<?= $r['servis_id'] ?>"
                     class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium text-snow hover:text-phosphor bg-ash hover:bg-charcoal border border-charcoal transition-all">
                    <span>Lihat</span>
                    <span>&rarr;</span>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "../include/footer.php"; ?>
