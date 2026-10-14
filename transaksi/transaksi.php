<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$pageTitle = "Transaksi Kasir";

$data = $pdo->query("SELECT
    s.servis_id,
    p.nama AS nama_pelanggan,
    k.nomor_polisi,
    k.merk,
    k.model,
    s.biaya_jasa,
    COALESCE(SUM(ds.subtotal),0) AS total_sparepart
  FROM servis s
  JOIN kendaraan k ON s.kendaraan_id = k.kendaraan_id
  JOIN pelanggan p ON k.pelanggan_id = p.pelanggan_id
  LEFT JOIN detail_servis ds ON s.servis_id = ds.servis_id
  WHERE s.status = 'selesai'
  GROUP BY s.servis_id, p.nama, k.nomor_polisi, k.merk, k.model, s.biaya_jasa
  ORDER BY s.servis_id DESC
")->fetchAll();

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-7xl mx-auto space-y-6">
  <!-- Header & Actions -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-charcoal pb-6">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-2xl font-bold text-snow tracking-tight">Kasir & Pembayaran</h1>
        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">
          <?= count($data) ?> Menunggu Kasir
        </span>
      </div>
      <p class="text-sm text-silver mt-1">Daftar servis berstatus selesai yang siap diproses pembayarannya (Cash / QRIS).</p>
    </div>

    <div>
      <a href="../riwayat/riwayat_servis.php"
         class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-ash hover:bg-charcoal border border-charcoal hover:border-slate text-snow text-xs font-medium transition-all">
        <span>Lihat Riwayat Transaksi Lunas</span>
        <span>&rarr;</span>
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
            <th class="px-6 py-3.5">Nama Pelanggan</th>
            <th class="px-6 py-3.5">Kendaraan</th>
            <th class="px-6 py-3.5">Jasa</th>
            <th class="px-6 py-3.5">Sparepart</th>
            <th class="px-6 py-3.5">Total Tagihan</th>
            <th class="px-6 py-3.5 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-charcoal text-silver">
          <?php foreach ($data as $item): ?>
            <?php 
              $biayaJasa = (float) ($item['biaya_jasa'] > 0 ? $item['biaya_jasa'] : 5000);
              $total = $biayaJasa + $item['total_sparepart']; 
            ?>
            <tr class="hover:bg-ash/30 transition-colors">
              <td class="px-6 py-4 font-mono text-xs text-smoke">#<?= $item['servis_id'] ?></td>
              <td class="px-6 py-4 font-medium text-snow"><?= htmlspecialchars($item['nama_pelanggan']) ?></td>
              <td class="px-6 py-4">
                <div class="text-snow text-xs"><?= htmlspecialchars($item['merk'] . ' ' . $item['model']) ?></div>
                <div class="text-[11px] font-mono text-smoke"><?= htmlspecialchars($item['nomor_polisi']) ?></div>
              </td>
              <td class="px-6 py-4 font-mono text-xs text-smoke">Rp <?= number_format($biayaJasa, 0, ',', '.') ?></td>
              <td class="px-6 py-4 font-mono text-xs text-smoke">Rp <?= number_format($item['total_sparepart'], 0, ',', '.') ?></td>
              <td class="px-6 py-4 font-mono text-sm font-medium text-phosphor">Rp <?= number_format($total, 0, ',', '.') ?></td>
              <td class="px-6 py-4 text-right">
                <a href="bayar.php?id=<?= $item['servis_id'] ?>"
                   class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                  <span>Bayar Sekarang</span>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>

          <?php if (count($data) === 0): ?>
            <tr>
              <td colspan="7" class="px-6 py-12 text-center text-smoke font-mono text-sm">
                <div class="flex flex-col items-center justify-center gap-2">
                  <svg class="w-8 h-8 text-smoke/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  <span>Tidak ada transaksi yang menunggu pembayaran kasir. Semua servis selesai telah diselesaikan.</span>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "../include/footer.php"; ?>
