<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();

$pageTitle = "Data Kendaraan";

$sql = "SELECT kendaraan.kendaraan_id, pelanggan.nama, kendaraan.nomor_polisi, kendaraan.merk, kendaraan.model, kendaraan.tahun
        FROM kendaraan
        INNER JOIN pelanggan ON kendaraan.pelanggan_id = pelanggan.pelanggan_id
        ORDER BY kendaraan.kendaraan_id DESC";

$stmt = $pdo->query($sql);
$kendaraan = $stmt->fetchAll();

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-7xl mx-auto space-y-6">
  <!-- Header & Actions -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-charcoal pb-6">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-2xl font-bold text-snow tracking-tight">Data Kendaraan Motor</h1>
        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">
          <?= count($kendaraan) ?> Unit Terdaftar
        </span>
      </div>
      <p class="text-sm text-silver mt-1">Registrasi nomor polisi, merk tipe motor, dan pemilik terdaftar.</p>
    </div>

    <div>
      <a href="tambah_kendaraan.php"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>+ Tambah Kendaraan</span>
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
            <th class="px-6 py-3.5">Nomor Polisi</th>
            <th class="px-6 py-3.5">Merk & Model Motor</th>
            <th class="px-6 py-3.5">Tahun</th>
            <th class="px-6 py-3.5">Pemilik Terdaftar</th>
            <th class="px-6 py-3.5 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-charcoal text-silver">
          <?php if (empty($kendaraan)): ?>
            <tr>
              <td colspan="6" class="px-6 py-10 text-center text-smoke font-mono text-sm">
                Belum ada data kendaraan motor yang terdaftar.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($kendaraan as $data): ?>
              <tr class="hover:bg-ash/30 transition-colors">
                <td class="px-6 py-4 font-mono text-xs text-smoke">#<?= $data["kendaraan_id"] ?></td>
                <td class="px-6 py-4 font-mono font-medium text-phosphor">
                  <span class="px-2 py-0.5 rounded bg-ash border border-charcoal text-snow text-xs"><?= htmlspecialchars($data["nomor_polisi"]) ?></span>
                </td>
                <td class="px-6 py-4 font-medium text-snow">
                  <?= htmlspecialchars($data["merk"] . ' ' . $data["model"]) ?>
                </td>
                <td class="px-6 py-4 font-mono text-xs text-smoke"><?= htmlspecialchars($data["tahun"] ?: '-') ?></td>
                <td class="px-6 py-4 text-xs text-silver"><?= htmlspecialchars($data["nama"]) ?></td>
                <td class="px-6 py-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <a href="edit_kendaraan.php?id=<?= $data["kendaraan_id"] ?>"
                       class="px-3 py-1 rounded-full text-xs font-medium bg-ash hover:bg-charcoal border border-charcoal text-snow hover:border-slate transition-all">
                      Edit
                    </a>
                    <form action="hapus_kendaraan.php" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kendaraan ini?')">
                      <input type="hidden" name="id" value="<?= $data["kendaraan_id"] ?>">
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

