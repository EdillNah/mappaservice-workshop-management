<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$isOwner = isOwner();
$isKasir = isKasir();
$pageTitle = "Data Mekanik";

$data = $pdo->query("SELECT * FROM mekanik ORDER BY mekanik_id DESC")->fetchAll();

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-7xl mx-auto space-y-6">
  <!-- Header & Actions -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-charcoal pb-6">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-2xl font-bold text-snow tracking-tight">Data Mekanik</h1>
        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">
          <?= count($data) ?> Teknisi
        </span>
      </div>
      <p class="text-sm text-silver mt-1">
        Kelola mekanik bengkel.
        <?php if ($isKasir): ?>
          <span class="text-phosphor font-mono text-xs">(Mode Kasir: Hanya dapat mengubah status ketersediaan)</span>
        <?php else: ?>
          <span class="text-phosphor font-mono text-xs">(Mode Owner: Akses penuh tambah, edit & hapus)</span>
        <?php endif; ?>
      </p>
    </div>

    <div>
      <?php if ($isOwner): ?>
        <a href="tambah_mekanik.php"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          <span>Tambah Mekanik</span>
        </a>
      <?php else: ?>
        <button disabled
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-ash/40 border border-charcoal/60 text-smoke cursor-not-allowed opacity-50 font-medium text-xs tracking-tight"
                title="Akses terbatas: Hanya Owner yang dapat menambah mekanik baru">
          <svg class="w-4 h-4 text-smoke" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          <span>Tambah Mekanik (Owner Only)</span>
        </button>
      <?php endif; ?>
    </div>
  </div>

  <!-- Role Notice Alert for Kasir -->
  <?php if ($isKasir): ?>
    <div class="p-3.5 rounded-xl bg-ash/40 border border-charcoal flex items-center justify-between text-xs text-silver">
      <div class="flex items-center gap-2.5">
        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Sebagai <strong>Kasir</strong>, Anda dapat mengubah status pekerjaan mekanik (Tersedia / Sibuk) menggunakan dropdown di tabel. Tombol Edit & Hapus dinonaktifkan.</span>
      </div>
      <span class="font-mono text-[10px] text-smoke uppercase tracking-wider px-2 py-0.5 rounded bg-obsidian border border-charcoal">Read-Only CRUD</span>
    </div>
  <?php endif; ?>

  <!-- Table Card -->
  <div class="bg-obsidian border border-charcoal rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-ash/50 border-b border-charcoal text-[11px] uppercase tracking-wider text-smoke font-mono">
          <tr>
            <th class="px-6 py-3.5">ID</th>
            <th class="px-6 py-3.5">Nama Mekanik</th>
            <th class="px-6 py-3.5">No. Telepon</th>
            <th class="px-6 py-3.5">Spesialisasi</th>
            <th class="px-6 py-3.5">Status Pekerjaan</th>
            <th class="px-6 py-3.5 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-charcoal text-silver">
          <?php foreach ($data as $mekanik): ?>
            <tr class="hover:bg-ash/30 transition-colors">
              <td class="px-6 py-4 font-mono text-xs text-smoke">#<?= $mekanik['mekanik_id'] ?></td>
              <td class="px-6 py-4 font-medium text-snow"><?= htmlspecialchars($mekanik['nama']) ?></td>
              <td class="px-6 py-4 font-mono text-xs text-smoke"><?= htmlspecialchars($mekanik['no_telpon'] ?? $mekanik['no_telepon'] ?? '-') ?></td>
              <td class="px-6 py-4 text-xs"><?= htmlspecialchars($mekanik['spesialisasi'] ?? 'Servis Umum') ?></td>
              
              <!-- Status with interactive switcher -->
              <td class="px-6 py-4">
                <form action="ubah_status.php" method="POST" class="inline-flex items-center gap-2">
                  <input type="hidden" name="mekanik_id" value="<?= $mekanik['mekanik_id'] ?>">
                  
                  <select name="status"
                          onchange="this.form.submit()"
                          class="bg-obsidian border rounded-lg text-xs py-1 px-2.5 font-mono cursor-pointer transition-colors <?= $mekanik['status'] === 'tersedia' ? 'border-phosphor/50 text-phosphor' : 'border-amber-700/60 text-amber-300' ?>">
                    <option value="tersedia" <?= $mekanik['status'] === 'tersedia' ? 'selected' : '' ?>>● Tersedia</option>
                    <option value="sibuk" <?= $mekanik['status'] === 'sibuk' ? 'selected' : '' ?>>● Sibuk</option>
                  </select>
                  <span class="text-[10px] text-smoke font-mono hidden sm:inline">(Klik ubah)</span>
                </form>
              </td>

              <!-- Action Buttons -->
              <td class="px-6 py-4 text-right">
                <?php if ($isOwner): ?>
                  <!-- Owner Enabled Actions -->
                  <div class="inline-flex items-center gap-2">
                    <a href="edit_mekanik.php?id=<?= $mekanik['mekanik_id'] ?>"
                       class="px-3 py-1 rounded-full text-xs font-medium bg-ash hover:bg-charcoal border border-charcoal text-snow hover:border-slate transition-all">
                      Edit
                    </a>
                    <form action="hapus_mekanik.php" method="POST" class="inline" onsubmit="return confirm('Hapus data mekanik ini?')">
                      <input type="hidden" name="id" value="<?= $mekanik['mekanik_id'] ?>">
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
                            title="Hanya Owner yang dapat mengedit data pokok mekanik">
                      Edit
                    </button>
                    <button disabled
                            class="px-3 py-1 rounded-full text-xs font-medium bg-ash/30 border border-charcoal/40 text-smoke cursor-not-allowed opacity-40"
                            title="Hanya Owner yang dapat menghapus data mekanik">
                      Hapus
                    </button>
                  </div>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>

          <?php if (empty($data)): ?>
            <tr>
              <td colspan="6" class="px-6 py-10 text-center text-smoke font-mono text-sm">
                Belum ada data mekanik terdaftar.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "../include/footer.php"; ?>
