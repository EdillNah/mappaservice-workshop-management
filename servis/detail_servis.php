<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$id = $_GET["id"] ?? null;

$stmt = $pdo->prepare("SELECT
    s.*,
    p.nama AS nama_pelanggan,
    p.no_telpon,
    k.nomor_polisi,
    k.merk,
    k.model,
    m.nama AS nama_mekanik
  FROM servis s
  JOIN kendaraan k ON s.kendaraan_id = k.kendaraan_id
  JOIN pelanggan p ON k.pelanggan_id = p.pelanggan_id
  LEFT JOIN mekanik m ON s.mekanik_id = m.mekanik_id
  WHERE s.servis_id = ?
");

$stmt->execute([$id]);
$servis = $stmt->fetch();

if (!$servis) {
  die("Data servis tidak ditemukan.");
}

$pageTitle = "Detail Servis #" . $servis['servis_id'];

// Sparepart yang digunakan
$stmt = $pdo->prepare("SELECT ds.*, sp.nama AS nama_sparepart
  FROM detail_servis ds
  JOIN sparepart sp ON ds.sparepart_id = sp.sparepart_id
  WHERE ds.servis_id = ?
  ORDER BY ds.detail_servis_id DESC
");

$stmt->execute([$id]);
$detail = $stmt->fetchAll();

$totalSparepart = 0;
foreach ($detail as $item) {
  $totalSparepart += $item['subtotal'];
}

$biayaJasa = (float) ($servis['biaya_jasa'] > 0 ? $servis['biaya_jasa'] : 5000);
$total = $biayaJasa + $totalSparepart;

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-7xl mx-auto space-y-6">
  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-charcoal pb-6">
    <div>
      <div class="flex items-center gap-2.5">
        <h1 class="text-2xl font-medium text-snow tracking-tight">Detail Servis #<?= $servis['servis_id'] ?></h1>
        <?= renderStatusBadge($servis['status']) ?>
      </div>
      <p class="text-sm text-silver mt-1">Kelola pemeriksaan fisik, suku cadang terpakai, dan kalkulasi tagihan.</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="servis.php"
         class="px-3.5 py-1.5 rounded-full border border-slate text-silver hover:text-snow hover:border-graphite text-xs transition-all">
        &larr; Kembali
      </a>
      <a href="../transaksi/bayar.php?id=<?= $servis['servis_id'] ?>"
         class="px-4 py-1.5 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
        Proses Bayar &rarr;
      </a>
    </div>
  </div>

  <!-- 2 Info Cards: Customer & Vehicle vs Service & Mechanic -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <!-- Pelanggan & Motor -->
    <div class="bg-obsidian border border-charcoal rounded-2xl p-6">
      <div class="flex items-center justify-between pb-3 border-b border-charcoal">
        <h3 class="text-xs uppercase tracking-wider font-mono text-snow">Identitas Pelanggan & Motor</h3>
        <span class="w-2 h-2 rounded-full bg-phosphor/70"></span>
      </div>
      <div class="mt-4 space-y-3">
        <div>
          <span class="text-xs text-smoke block">Nama Pemilik</span>
          <span class="text-base font-medium text-snow"><?= htmlspecialchars($servis['nama_pelanggan']) ?></span>
          <span class="text-xs text-silver font-mono ml-2"><?= htmlspecialchars($servis['no_telpon'] ?? '-') ?></span>
        </div>
        <div>
          <span class="text-xs text-smoke block">Kendaraan</span>
          <div class="text-sm text-snow font-medium"><?= htmlspecialchars($servis['merk'] . ' ' . $servis['model']) ?></div>
          <span class="inline-block px-2 py-0.5 rounded bg-ash border border-charcoal text-xs font-mono text-phosphor mt-1">
            <?= htmlspecialchars($servis['nomor_polisi']) ?>
          </span>
        </div>
      </div>
    </div>

    <!-- Mekanik & Keluhan -->
    <div class="bg-obsidian border border-charcoal rounded-2xl p-6">
      <div class="flex items-center justify-between pb-3 border-b border-charcoal">
        <h3 class="text-xs uppercase tracking-wider font-mono text-snow">Penugasan & Keluhan</h3>
        <span class="w-2 h-2 rounded-full bg-blue-400/70"></span>
      </div>
      <div class="mt-4 space-y-3">
        <div>
          <span class="text-xs text-smoke block">Mekanik yang Bertugas</span>
          <span class="text-sm font-medium text-snow">
            <?= htmlspecialchars($servis['nama_mekanik'] ?? 'Belum Ditugaskan') ?>
          </span>
        </div>
        <div>
          <span class="text-xs text-smoke block">Keluhan Konsumen</span>
          <p class="text-xs text-silver bg-ash/40 p-3 rounded-xl border border-charcoal mt-1">
            <?= nl2br(htmlspecialchars($servis['keluhan'])) ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Diagnosis & Update Status Form -->
  <div class="bg-obsidian border border-charcoal rounded-2xl p-6">
    <div class="flex items-center justify-between pb-4 border-b border-charcoal">
      <div>
        <h3 class="text-base font-medium text-snow">Pemeriksaan & Progres Servis</h3>
        <p class="text-xs text-smoke mt-0.5">Catat diagnosis teknisi dan ubah status pengerjaan.</p>
      </div>
      <div class="flex items-center gap-2">
        <span class="text-xs text-smoke font-mono">Status Terkini:</span>
        <?= renderStatusBadge($servis['status']) ?>
      </div>
    </div>

    <form action="update_servis.php" method="POST" class="mt-5 space-y-4">
      <input type="hidden" name="servis_id" value="<?= $servis['servis_id'] ?>">
      
      <div>
        <label class="block text-xs font-medium text-silver mb-1.5">Hasil Pemeriksaan / Diagnosis Kerusakan</label>
        <textarea name="diagnosa"
                  rows="3"
                  placeholder="Catatan hasil bongkar/analisa teknisi..."
                  class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow placeholder-smoke"><?= htmlspecialchars($servis['diagnosa'] ?? '') ?></textarea>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-silver mb-1.5">Biaya Jasa Bengkel (Rp)</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-mono text-smoke">Rp</span>
            <input type="number"
                   name="biaya_jasa"
                   min="0"
                   step="500"
                   value="<?= htmlspecialchars((int)$biayaJasa) ?>"
                   required
                   class="w-full bg-obsidian border border-slate rounded-lg pl-10 pr-3.5 py-2 text-sm text-snow font-mono">
          </div>
          <p class="text-[11px] text-smoke mt-1 font-mono">Standar biaya jasa per servis: Rp 5.000</p>
        </div>

        <div>
          <label class="block text-xs font-medium text-silver mb-1.5">Status Pengerjaan</label>
          <select name="status" class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2 text-sm text-snow font-mono">
            <?php
              $statusList = [
                'diperiksa',
                'menunggu_konfirmasi',
                'disetujui',
                'diproses',
                'selesai',
                'diambil'
              ];
            ?>
            <?php foreach ($statusList as $st): ?>
              <option value="<?= $st ?>" <?= $servis['status'] === $st ? 'selected' : '' ?>>
                <?= ucwords(str_replace('_', ' ', $st)) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="flex items-center justify-end pt-2">
        <button type="submit"
                class="px-5 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          Simpan Diagnosis, Biaya & Status
        </button>
      </div>
    </form>
  </div>

  <!-- Sparepart Digunakan Table Card -->
  <div class="bg-obsidian border border-charcoal rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-charcoal flex items-center justify-between bg-ash/30">
      <div>
        <h3 class="text-base font-medium text-snow">Suku Cadang & Sparepart Digunakan</h3>
        <p class="text-xs text-smoke mt-0.5">Daftar item onderdil yang dipasang pada servis ini</p>
      </div>
      <a href="tambah_detail.php?servis_id=<?= $servis['servis_id'] ?>"
         class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-ash hover:bg-charcoal border border-charcoal text-snow text-xs font-medium transition-all">
        <svg class="w-3.5 h-3.5 text-phosphor" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Pasang Sparepart</span>
      </a>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-ash/50 border-b border-charcoal text-[11px] uppercase tracking-wider text-smoke font-mono">
          <tr>
            <th class="px-6 py-3">Nama Sparepart</th>
            <th class="px-6 py-3">Jumlah</th>
            <th class="px-6 py-3">Harga Satuan</th>
            <th class="px-6 py-3 text-right">Subtotal</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-charcoal text-silver">
          <?php if (empty($detail)): ?>
            <tr>
              <td colspan="4" class="px-6 py-8 text-center text-smoke font-mono text-xs">
                Belum ada pemakaian sparepart pada servis ini.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($detail as $item): ?>
              <tr class="hover:bg-ash/30 transition-colors">
                <td class="px-6 py-3.5 font-medium text-snow"><?= htmlspecialchars($item['nama_sparepart']) ?></td>
                <td class="px-6 py-3.5 font-mono text-xs text-silver"><?= $item['jumlah'] ?> pcs</td>
                <td class="px-6 py-3.5 font-mono text-xs text-smoke">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                <td class="px-6 py-3.5 font-mono text-xs text-phosphor text-right">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Cost Summary Footer -->
    <div class="p-6 bg-ash/20 border-t border-charcoal flex flex-col items-end space-y-1.5">
      <div class="text-xs text-smoke font-mono">
        Biaya Jasa Bengkel: <span class="text-snow font-medium">Rp <?= number_format($biayaJasa, 0, ',', '.') ?></span>
      </div>
      <div class="text-xs text-smoke font-mono">
        Subtotal Sparepart: <span class="text-snow font-medium">Rp <?= number_format($totalSparepart, 0, ',', '.') ?></span>
      </div>
      <div class="text-base text-snow font-mono pt-2 border-t border-charcoal mt-1">
        Estimasi Total Tagihan: <span class="text-phosphor font-medium text-lg">Rp <?= number_format($total, 0, ',', '.') ?></span>
      </div>
    </div>
  </div>
</div>

<?php include "../include/footer.php"; ?>
