<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$pageTitle = "Daftar Servis";

$data = $pdo->query("SELECT
    s.servis_id,
    s.tanggal_masuk,
    s.keluhan,
    s.status,
    p.nama AS nama_pelanggan,
    k.nomor_polisi,
    k.merk,
    k.model,
    m.nama AS nama_mekanik 
  FROM servis s
  JOIN kendaraan k ON s.kendaraan_id = k.kendaraan_id
  JOIN pelanggan p ON k.pelanggan_id = p.pelanggan_id
  LEFT JOIN mekanik m ON s.mekanik_id = m.mekanik_id
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
        <h1 class="text-2xl font-bold text-snow tracking-tight">Manajemen Servis Motor</h1>
        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">
          <?= count($data) ?> Servis Tercatat
        </span>
      </div>
      <p class="text-sm text-silver mt-1">Pantau perkembangan status perbaikan, mekanik penanggung jawab, dan diagnosis.</p>
    </div>

    <div>
      <a href="catat_servis.php"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Catat Servis Baru</span>
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
            <th class="px-6 py-3.5">Pelanggan</th>
            <th class="px-6 py-3.5">Kendaraan</th>
            <th class="px-6 py-3.5">Mekanik</th>
            <th class="px-6 py-3.5">Keluhan</th>
            <th class="px-6 py-3.5 whitespace-nowrap">Status Pengerjaan</th>
            <th class="px-6 py-3.5 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-charcoal text-silver">
          <?php if (empty($data)): ?>
            <tr>
              <td colspan="7" class="px-6 py-10 text-center text-smoke font-mono text-sm">
                Belum ada data pengerjaan servis terdaftar.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($data as $item): ?>
              <tr class="hover:bg-ash/30 transition-colors">
                <td class="px-6 py-4 font-mono text-xs text-smoke">#<?= $item['servis_id'] ?></td>
                <td class="px-6 py-4 font-medium text-snow"><?= htmlspecialchars($item['nama_pelanggan']) ?></td>
                <td class="px-6 py-4">
                  <div class="text-snow text-xs"><?= htmlspecialchars($item['merk'] . ' ' . $item['model']) ?></div>
                  <div class="text-[11px] font-mono text-smoke"><?= htmlspecialchars($item['nomor_polisi']) ?></div>
                </td>
                <td class="px-6 py-4 text-xs">
                  <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-ash border border-charcoal text-silver">
                    <span class="w-1.5 h-1.5 rounded-full bg-phosphor"></span>
                    <?= htmlspecialchars($item['nama_mekanik'] ?? 'Belum Ditugaskan') ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-xs max-w-xs truncate text-silver"><?= htmlspecialchars($item['keluhan']) ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <?= renderStatusBadge($item['status']) ?>
                </td>
                <td class="px-6 py-4 text-right">
                  <a href="detail_servis.php?id=<?= $item['servis_id'] ?>"
                     class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium text-snow hover:text-phosphor bg-ash hover:bg-charcoal border border-charcoal transition-all">
                    <span>Detail & Sparepart</span>
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
