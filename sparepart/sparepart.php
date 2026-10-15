<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$isOwner = isOwner();
$isKasir = isKasir();
$pageTitle = "Data Sparepart";

$flashSuccess = $_SESSION["flash_restock_success"] ?? null;
unset($_SESSION["flash_restock_success"]);

$data = $pdo->query("SELECT * FROM sparepart ORDER BY nama ASC")->fetchAll();

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-7xl mx-auto space-y-6">
  <!-- Header & Actions -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-charcoal pb-6">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-2xl font-bold text-snow tracking-tight">Katalog & Stok Sparepart</h1>
        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">
          <?= count($data) ?> Item
        </span>
      </div>
      <p class="text-sm text-silver mt-1">
        Daftar komponen dan stok suku cadang bengkel.
        <?php if ($isKasir): ?>
          <span class="text-phosphor font-mono text-xs">(Mode Kasir: Read-only inventaris)</span>
        <?php else: ?>
          <span class="text-phosphor font-mono text-xs">(Mode Owner: Akses penuh kelola harga & stok)</span>
        <?php endif; ?>
      </p>
    </div>

    <div class="flex flex-wrap items-center gap-2.5">
      <?php if ($isOwner): ?>
        <a href="restock.php"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          <span>Restock Sparepart (Batch)</span>
        </a>
        <a href="export_excel.php"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-ash hover:bg-charcoal border border-charcoal hover:border-slate text-snow text-xs font-medium transition-all">
          <svg class="w-3.5 h-3.5 text-phosphor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <span>Export Excel</span>
        </a>
      <?php else: ?>
        <button disabled
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-ash/40 border border-charcoal/60 text-smoke cursor-not-allowed opacity-50 font-medium text-xs tracking-tight"
                title="Akses terbatas: Kasir hanya dapat melihat stok (Read-Only)">
          <svg class="w-4 h-4 text-smoke" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          <span>Restock (Owner Only)</span>
        </button>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($flashSuccess): ?>
    <div class="p-4 rounded-xl bg-phosphor/10 border border-phosphor/30 text-snow text-xs flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <svg class="w-4 h-4 text-phosphor shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span><?= htmlspecialchars($flashSuccess) ?></span>
      </div>
      <span class="text-[10px] font-mono uppercase tracking-wider text-phosphor font-bold">Sukses</span>
    </div>
  <?php endif; ?>

  <!-- Role Notice Alert for Kasir -->
  <?php if ($isKasir): ?>
    <div class="p-3.5 rounded-xl bg-ash/40 border border-charcoal flex items-center justify-between text-xs text-silver">
      <div class="flex items-center gap-2.5">
        <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Sebagai <strong>Kasir</strong>, Anda hanya memiliki izin membaca (Read-Only) data sparepart & stok untuk pemeriksaan servis. Penambahan, edit harga, dan penghapusan hanya dapat dilakukan oleh Owner.</span>
      </div>
      <span class="font-mono text-[10px] text-smoke uppercase tracking-wider px-2 py-0.5 rounded bg-obsidian border border-charcoal">Read-Only</span>
    </div>
  <?php endif; ?>

  <!-- Table Card -->
  <div class="bg-obsidian border border-charcoal rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-ash/50 border-b border-charcoal text-[11px] uppercase tracking-wider text-smoke font-mono">
          <tr>
            <th class="px-6 py-3.5">ID</th>
            <th class="px-6 py-3.5">Nama Sparepart</th>
            <th class="px-6 py-3.5">Harga Satuan</th>
            <th class="px-6 py-3.5">Stok Tersedia</th>
            <th class="px-6 py-3.5">Kondisi</th>
            <th class="px-6 py-3.5 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-charcoal text-silver">
          <?php foreach ($data as $item): ?>
            <tr class="hover:bg-ash/30 transition-colors">
              <td class="px-6 py-4 font-mono text-xs text-smoke">#<?= $item['sparepart_id'] ?></td>
              <td class="px-6 py-4 font-medium text-snow"><?= htmlspecialchars($item['nama']) ?></td>
              <td class="px-6 py-4 font-mono text-sm text-phosphor">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
              <td class="px-6 py-4 font-mono font-medium text-snow"><?= $item['stok'] ?> unit</td>
              <td class="px-6 py-4">
                <?php if ($item['stok'] > 5): ?>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-phosphor/10 text-phosphor border border-phosphor/30">
                    Tersedia
                  </span>
                <?php elseif ($item['stok'] > 0): ?>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-amber-950/40 text-amber-300 border border-amber-900/50">
                    Menipis (&le; 5)
                  </span>
                <?php else: ?>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-red-950/40 text-red-400 border border-red-900/50">
                    Habis (0)
                  </span>
                <?php endif; ?>
              </td>

              <!-- Action Buttons -->
              <td class="px-6 py-4 text-right">
                <?php if ($isOwner): ?>
                  <!-- Owner Enabled Actions -->
                  <div class="inline-flex items-center gap-2">
                    <a href="edit_sparepart.php?id=<?= $item['sparepart_id'] ?>"
                       class="px-3 py-1 rounded-full text-xs font-medium bg-ash hover:bg-charcoal border border-charcoal text-snow hover:border-slate transition-all">
                      Edit
                    </a>
                    <form action="hapus_sparepart.php" method="POST" class="inline" onsubmit="return confirm('Hapus item sparepart ini?')">
                      <input type="hidden" name="id" value="<?= $item['sparepart_id'] ?>">
                      <button type="submit"
                              class="px-3 py-1 rounded-full text-xs font-medium bg-red-950/20 hover:bg-red-950/50 border border-red-900/40 text-red-400 transition-all">
                        Hapus
                      </button>
                    </form>
                  </div>
                <?php else: ?>
                  <!-- Kasir Disabled Actions -->
                  <div class="inline-flex items-center gap-2">
                    <button disabled
                            class="px-3 py-1 rounded-full text-xs font-medium bg-ash/30 border border-charcoal/40 text-smoke cursor-not-allowed opacity-40"
                            title="Hanya Owner yang dapat mengubah harga & stok sparepart">
                      Edit
                    </button>
                    <button disabled
                            class="px-3 py-1 rounded-full text-xs font-medium bg-ash/30 border border-charcoal/40 text-smoke cursor-not-allowed opacity-40"
                            title="Hanya Owner yang dapat menghapus sparepart">
                      Hapus
                    </button>
                  </div>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>

          <?php if (count($data) === 0): ?>
            <tr>
              <td colspan="6" class="px-6 py-10 text-center text-smoke font-mono text-sm">
                Belum ada data sparepart di inventaris.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "../include/footer.php"; ?>
