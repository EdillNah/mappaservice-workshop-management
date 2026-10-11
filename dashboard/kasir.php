<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusKasir();

$pageTitle = "Dashboard Kasir";

$stmt = $pdo->query("SELECT COUNT(*) FROM servis WHERE DATE(tanggal_masuk) = CURDATE()");
$servisHariIni = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM servis WHERE status = 'menunggu_konfirmasi'");
$menungguKonfirmasi = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM servis WHERE status = 'diproses'");
$sedangDiproses = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM servis WHERE status = 'selesai' AND DATE(tanggal_masuk) = CURDATE()");
$selesaiHariIni = (int) $stmt->fetchColumn();

// Mekanik tersedia
$stmt = $pdo->query("SELECT COUNT(*) FROM mekanik WHERE status = 'tersedia'");
$mekanikTersedia = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT
    s.servis_id,
    s.status,
    s.keluhan,
    s.tanggal_masuk,
    p.nama AS nama_pelanggan,
    k.nomor_polisi,
    k.merk,
    k.model
  FROM servis s
  JOIN kendaraan k ON s.kendaraan_id = k.kendaraan_id
  JOIN pelanggan p ON k.pelanggan_id = p.pelanggan_id
  ORDER BY s.servis_id DESC LIMIT 5
");
$servisTerbaru = $stmt->fetchAll();

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-7xl mx-auto space-y-8">
  <!-- Top Welcome & Actions -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-charcoal pb-6">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-2xl font-bold text-snow tracking-tight">Dashboard Kasir</h1>
        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-ash border border-charcoal text-silver">
          Operasional
        </span>
      </div>
      <p class="text-sm text-silver mt-1">Selamat datang, <span style="color:white; font-weight:bold"><?= htmlspecialchars(getUserName()) ?></span>. <br>Pantau arus masuk servis motor dan transaksi kasir.</p>
    </div>

    <div class="flex items-center gap-3 shrink-0">
      <a href="../servis/catat_servis.php"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-phosphor text-obsidian hover:bg-phosphor-light font-medium text-xs tracking-tight transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>+ Catat Servis Baru</span>
      </a>
      <a href="../pelanggan/tambah_pelanggan.php"
         class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-transparent hover:bg-ash border border-slate text-snow hover:border-graphite text-xs transition-all">
        <span>+ Pelanggan</span>
      </a>
    </div>
  </div>

  <!-- Operational Flow Quick Bar -->
  <div class="bg-ash/40 border border-charcoal rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <div class="w-2.5 h-2.5 rounded-full bg-phosphor animate-pulse"></div>
      <span class="text-xs text-silver">Alur Input Pelanggan Baru:</span>
      <span class="text-xs font-mono text-smoke hidden lg:inline">Pelanggan ➔ Kendaraan ➔ Catat Servis ➔ Pengerjaan ➔ Transaksi</span>
    </div>
    <div class="flex items-center gap-2 text-xs">
      <a href="../mekanik/mekanik.php" class="px-3 py-1 rounded-full bg-obsidian border border-charcoal text-silver hover:text-snow hover:border-slate transition-colors font-mono">
        Mekanik Siap: <span class="text-phosphor font-medium"><?= $mekanikTersedia ?></span>
      </a>
      <a href="../transaksi/transaksi.php" class="px-3 py-1 rounded-full bg-obsidian border border-charcoal text-silver hover:text-snow hover:border-slate transition-colors">
        Kasir Pembayaran &rarr;
      </a>
    </div>
  </div>

  <!-- 4 Stat Metric Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1 -->
    <div class="bg-obsidian border border-charcoal rounded-2xl p-5 hover:border-slate transition-all group">
      <div class="flex items-center justify-between">
        <p class="text-xs font-medium text-smoke uppercase tracking-wider font-mono">Servis Hari Ini</p>
        <span class="w-2 h-2 rounded-full bg-phosphor/60 group-hover:bg-phosphor transition-colors"></span>
      </div>
      <div class="mt-3 flex items-baseline gap-2">
        <h3 class="text-3xl font-mono text-snow font-medium"><?= $servisHariIni ?></h3>
        <span class="text-xs text-smoke">unit</span>
      </div>
      <p class="text-[11px] text-smoke mt-2">Total kendaraan terdaftar hari ini</p>
    </div>

    <!-- Card 2 -->
    <div class="bg-obsidian border border-charcoal rounded-2xl p-5 hover:border-slate transition-all group">
      <div class="flex items-center justify-between">
        <p class="text-xs font-medium text-smoke uppercase tracking-wider font-mono">Menunggu Konfirmasi</p>
        <span class="w-2 h-2 rounded-full bg-amber-400/60 group-hover:bg-amber-400 transition-colors"></span>
      </div>
      <div class="mt-3 flex items-baseline gap-2">
        <h3 class="text-3xl font-mono text-amber-300 font-medium"><?= $menungguKonfirmasi ?></h3>
        <span class="text-xs text-smoke">unit</span>
      </div>
      <p class="text-[11px] text-smoke mt-2">Menunggu persetujuan pemilik motor</p>
    </div>

    <!-- Card 3 -->
    <div class="bg-obsidian border border-charcoal rounded-2xl p-5 hover:border-slate transition-all group">
      <div class="flex items-center justify-between">
        <p class="text-xs font-medium text-smoke uppercase tracking-wider font-mono">Sedang Diproses</p>
        <span class="w-2 h-2 rounded-full bg-blue-400/60 group-hover:bg-blue-400 transition-colors"></span>
      </div>
      <div class="mt-3 flex items-baseline gap-2">
        <h3 class="text-3xl font-mono text-blue-300 font-medium"><?= $sedangDiproses ?></h3>
        <span class="text-xs text-smoke">unit</span>
      </div>
      <p class="text-[11px] text-smoke mt-2">Dalam pengerjaan oleh mekanik</p>
    </div>

    <!-- Card 4 -->
    <div class="bg-obsidian border border-charcoal rounded-2xl p-5 hover:border-slate transition-all group">
      <div class="flex items-center justify-between">
        <p class="text-xs font-medium text-smoke uppercase tracking-wider font-mono">Selesai Hari Ini</p>
        <span class="w-2 h-2 rounded-full bg-phosphor/60 group-hover:bg-phosphor transition-colors"></span>
      </div>
      <div class="mt-3 flex items-baseline gap-2">
        <h3 class="text-3xl font-mono text-phosphor font-medium"><?= $selesaiHariIni ?></h3>
        <span class="text-xs text-smoke">unit</span>
      </div>
      <p class="text-[11px] text-smoke mt-2">Siap untuk pembayaran & pengambilan</p>
    </div>
  </div>

  <!-- Recent Services Table Card -->
  <div class="bg-obsidian border border-charcoal rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-charcoal flex items-center justify-between bg-ash/30">
      <div>
        <h2 class="text-base font-medium text-snow">Antrian & Servis Terbaru</h2>
        <p class="text-xs text-smoke mt-0.5">5 catatan aktivitas servis motor terakhir</p>
      </div>
      <a href="../servis/servis.php" class="text-xs text-phosphor hover:underline font-mono">Lihat Semua &rarr;</a>
    </div>

    <?php if (empty($servisTerbaru)): ?>
      <div class="p-10 text-center text-smoke text-sm font-mono">
        Belum ada aktivitas servis motor terdaftar saat ini.
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-ash/50 border-b border-charcoal text-[11px] uppercase tracking-wider text-smoke font-mono">
            <tr>
              <th class="px-6 py-3.5">ID</th>
              <th class="px-6 py-3.5">Pelanggan</th>
              <th class="px-6 py-3.5">Kendaraan</th>
              <th class="px-6 py-3.5">Keluhan</th>
              <th class="px-6 py-3.5 whitespace-nowrap">Status</th>
              <th class="px-6 py-3.5 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-charcoal text-silver">
            <?php foreach ($servisTerbaru as $s): ?>
              <tr class="hover:bg-ash/30 transition-colors">
                <td class="px-6 py-4 font-mono text-xs text-smoke">#<?= $s['servis_id'] ?></td>
                <td class="px-6 py-4 font-medium text-snow"><?= htmlspecialchars($s['nama_pelanggan']) ?></td>
                <td class="px-6 py-4">
                  <div class="text-snow"><?= htmlspecialchars($s['merk'] . ' ' . $s['model']) ?></div>
                  <div class="text-xs font-mono text-smoke"><?= htmlspecialchars($s['nomor_polisi']) ?></div>
                </td>
                <td class="px-6 py-4 text-xs max-w-xs truncate text-silver"><?= htmlspecialchars($s['keluhan']) ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <?= renderStatusBadge($s['status']) ?>
                </td>
                <td class="px-6 py-4 text-right">
                  <a href="../servis/detail_servis.php?id=<?= $s['servis_id'] ?>"
                     class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-snow hover:text-phosphor bg-ash hover:bg-charcoal border border-charcoal transition-all">
                    Detail &rarr;
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include "../include/footer.php"; ?>
