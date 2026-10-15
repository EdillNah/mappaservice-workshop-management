<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$pageTitle = "Data Pelanggan";

$sql = "SELECT * FROM pelanggan ORDER BY pelanggan_id DESC";
$stmt = $pdo->query($sql);
$pelanggan = $stmt->fetchAll();

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-7xl mx-auto space-y-6">
  <!-- Header & Actions -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-charcoal pb-6">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-2xl font-bold text-snow tracking-tight">Data Pelanggan</h1>
        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">
          <?= count($pelanggan) ?> Pelanggan Terdaftar
        </span>
      </div>
      <p class="text-sm text-silver mt-1">Kelola data identitas dan kontak pemilik sepeda motor.</p>
    </div>

    <div>
      <a href="tambah_pelanggan.php"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Tambah Pelanggan</span>
      </a>
    </div>
  </div>

  <!-- Table Card -->
  <div class="bg-obsidian border border-charcoal rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-ash/50 border-b border-charcoal text-[11px] uppercase tracking-wider text-smoke font-mono">
          <tr>
            <th class="px-6 py-3.5">ID</th>
            <th class="px-6 py-3.5">Nama Pelanggan</th>
            <th class="px-6 py-3.5">No. Telepon</th>
            <th class="px-6 py-3.5">Alamat Domisili</th>
            <th class="px-6 py-3.5 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-charcoal text-silver">
          <?php if (empty($pelanggan)): ?>
            <tr>
              <td colspan="5" class="px-6 py-10 text-center text-smoke font-mono text-sm">
                Belum ada data pelanggan yang terdaftar di bengkel.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($pelanggan as $data): ?>
              <tr class="hover:bg-ash/30 transition-colors">
                <td class="px-6 py-4 font-mono text-xs text-smoke">#<?= $data["pelanggan_id"] ?></td>
                <td class="px-6 py-4 font-medium text-snow"><?= htmlspecialchars($data["nama"]) ?></td>
                <td class="px-6 py-4 font-mono text-xs text-silver"><?= htmlspecialchars($data["no_telpon"] ?: '-') ?></td>
                <td class="px-6 py-4 text-xs max-w-sm truncate text-smoke"><?= htmlspecialchars($data["alamat"] ?: '-') ?></td>
                <td class="px-6 py-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <a href="edit_pelanggan.php?id=<?= $data["pelanggan_id"] ?>"
                       class="px-3 py-1 rounded-full text-xs font-medium bg-ash hover:bg-charcoal border border-charcoal text-snow hover:border-slate transition-all">
                      Edit
                    </a>
                    <form action="hapus_pelanggan.php" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data pelanggan ini?')">
                      <input type="hidden" name="id" value="<?= $data["pelanggan_id"] ?>">
                      <button type="submit"
                              class="px-3 py-1 rounded-full text-xs font-medium bg-red-950/20 hover:bg-red-950/50 border border-red-900/40 text-red-400 transition-all">
                        Hapus
                      </button>
                    </form>
                  </div>
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
