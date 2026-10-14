<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusOwner();

$pageTitle = "Dashboard Owner";

$stmt = $pdo->query("SELECT COALESCE(SUM(total_biaya), 0) FROM restock");
$totalModal = (float) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM restock");
$totalBatchRestock = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM transaksi WHERE status_pembayaran = 'lunas'");
$totalTransaksi = (int) $stmt->fetchColumn();

$sqlTrend = "SELECT 
    DATE(t.tanggal_transaksi) as tgl,
    COUNT(t.transaksi_id) as jumlah_transaksi,
    COALESCE(SUM(t.total), 0) as pendapatan,
    COALESCE(SUM(
      s.biaya_jasa + COALESCE((
        SELECT SUM(ds.subtotal * 0.28) 
        FROM detail_servis ds 
        WHERE ds.servis_id = s.servis_id
      ), 0)
    ), 0) as profit_kotor
  FROM transaksi t
  JOIN servis s ON t.servis_id = s.servis_id
  WHERE t.status_pembayaran = 'lunas'
  GROUP BY DATE(t.tanggal_transaksi)
  ORDER BY tgl ASC
";

$stmtTrend = $pdo->query($sqlTrend);
$trendRows = $stmtTrend->fetchAll(PDO::FETCH_ASSOC);

$chartLabels = [];
$chartPendapatan = [];
$chartProfitKotor = [];
$chartProfitBersih = [];

foreach ($trendRows as $row) {
  $chartLabels[] = date('d M', strtotime($row['tgl']));
  $chartPendapatan[] = (float) $row['pendapatan'];
  $pk = (float) $row['profit_kotor'];
  $chartProfitKotor[] = round($pk);
  $chartProfitBersih[] = round($pk * 0.80);
}

$totalPendapatan = array_sum($chartPendapatan);
$totalProfitKotor = array_sum($chartProfitKotor);
$totalProfitBersih = array_sum($chartProfitBersih);

$stmt = $pdo->query("SELECT COUNT(*) FROM pelanggan");
$totalPelanggan = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM kendaraan");
$totalKendaraan = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM mekanik");
$totalMekanik = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM mekanik WHERE status = 'tersedia'");
$mekanikTersedia = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM sparepart");
$totalSparepart = (int) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT * FROM sparepart WHERE stok <= 5 ORDER BY stok ASC LIMIT 5");
$stokMenipis = $stmt->fetchAll();

$stmt = $pdo->query("SELECT
    t.*,
    s.servis_id,
    p.nama AS nama_pelanggan,
    k.nomor_polisi
  FROM transaksi t
  JOIN servis s ON t.servis_id = s.servis_id
  JOIN kendaraan k ON s.kendaraan_id = k.kendaraan_id
  JOIN pelanggan p ON k.pelanggan_id = p.pelanggan_id
  ORDER BY t.transaksi_id DESC LIMIT 5
");
$transaksiTerbaru = $stmt->fetchAll();

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-6 md:p-8 max-w-7xl mx-auto space-y-8">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-charcoal pb-6">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-2xl font-bold text-snow tracking-tight">Executive Dashboard</h1>
        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono bg-phosphor/10 border border-phosphor/30 text-phosphor">
          Owner Portal
        </span>
      </div>
      <p class="text-sm text-silver mt-1">Selamat datang, <span style="color:white; font-weight:semibold"><?= htmlspecialchars(getUserName()) ?></span>. Ringkasan performa finansial, mekanik, dan inventaris bengkel.</p>
    </div>

    <!-- Quick Owner Management Buttons -->
    <div class="flex flex-wrap items-center gap-2.5">
      <a href="../riwayat/riwayat_servis.php"
         class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-transparent hover:bg-ash border border-slate text-silver hover:text-snow text-xs transition-all">
        <span>Riwayat Servis</span>
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Modal -->
    <div class="bg-obsidian border border-charcoal rounded-2xl p-5 hover:border-slate transition-all group">
      <div class="flex items-center justify-between">
        <p class="text-xs font-medium text-smoke uppercase tracking-wider font-mono">Total Modal</p>
        <a href="../sparepart/restock.php" class="text-[10px] text-smoke group-hover:text-phosphor font-mono">Restock &rarr;</a>
      </div>
      <div class="mt-3 flex items-baseline gap-1">
        <h3 class="text-2xl font-mono text-snow font-medium">Rp <?= number_format($totalModal, 0, ',', '.') ?></h3>
      </div>
      <p class="text-[11px] text-smoke mt-2 font-mono">
        <span class="text-silver"><?= $totalBatchRestock ?> batch restock</span> • Belanja suku cadang
      </p>
    </div>

    <!-- Mechanics -->
    <div class="bg-obsidian border border-charcoal rounded-2xl p-5 hover:border-slate transition-all group">
      <div class="flex items-center justify-between">
        <p class="text-xs font-medium text-smoke uppercase tracking-wider font-mono">Tim Mekanik</p>
        <a href="../mekanik/mekanik.php" class="text-[10px] text-smoke group-hover:text-phosphor font-mono">Kelola &rarr;</a>
      </div>
      <div class="mt-3 flex items-baseline gap-2">
        <h3 class="text-3xl font-mono text-snow font-medium"><?= $totalMekanik ?></h3>
        <span class="text-xs text-smoke">orang</span>
      </div>
      <div class="flex items-center gap-2 mt-2 text-[11px] font-mono">
        <span class="text-phosphor"><?= $mekanikTersedia ?> Tersedia</span>
        <span class="text-smoke">•</span>
        <span class="text-amber-300"><?= max(0, $totalMekanik - $mekanikTersedia) ?> Sibuk</span>
      </div>
    </div>

    <!-- Spareparts -->
    <div class="bg-obsidian border border-charcoal rounded-2xl p-5 hover:border-slate transition-all group">
      <div class="flex items-center justify-between">
        <p class="text-xs font-medium text-smoke uppercase tracking-wider font-mono">Total Sparepart</p>
        <a href="../sparepart/sparepart.php" class="text-[10px] text-smoke group-hover:text-phosphor font-mono">Katalog &rarr;</a>
      </div>
      <div class="mt-3 flex items-baseline gap-2">
        <h3 class="text-3xl font-mono text-snow font-medium"><?= $totalSparepart ?></h3>
        <span class="text-xs text-smoke">item</span>
      </div>
      <p class="text-[11px] text-smoke mt-2 font-mono">
        <?= count($stokMenipis) > 0 ? '<span class="text-amber-400">' . count($stokMenipis) . ' stok menipis (&le; 5)</span>' : '<span class="text-phosphor">Stok aman</span>' ?>
      </p>
    </div>

    <!-- Customers & Vehicles -->
    <div class="bg-obsidian border border-charcoal rounded-2xl p-5 hover:border-slate transition-all group">
      <div class="flex items-center justify-between">
        <p class="text-xs font-medium text-smoke uppercase tracking-wider font-mono">Database Bengkel</p>
        <span class="w-2 h-2 rounded-full bg-slate"></span>
      </div>
      <div class="mt-3 flex items-baseline gap-2">
        <h3 class="text-3xl font-mono text-snow font-medium"><?= $totalPelanggan ?></h3>
        <span class="text-xs text-smoke">pelanggan</span>
      </div>
      <p class="text-[11px] text-smoke mt-2 font-mono"><?= $totalKendaraan ?> motor terdaftar</p>
    </div>
  </div>

  <!-- Trend Finansial: Pendapatan, Profit Kotor & Profit Bersih -->
  <div class="bg-obsidian border border-charcoal rounded-2xl overflow-hidden flex flex-col p-6 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-charcoal pb-5">
      <div>
        <div class="flex items-center gap-2">
          <h2 class="text-lg font-bold text-snow tracking-tight">Tren Finansial & Profitabilitas</h2>
          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono bg-ash border border-charcoal text-silver">
            30 Hari Terakhir
          </span>
        </div>
        <p class="text-xs text-smoke mt-1">
          Pantau pergerakan omset pendapatan, profit kotor (margin jasa & suku cadang), dan estimasi profit bersih.
        </p>
      </div>

      <!-- Quick Summary Pills / Legend Buttons -->
      <div class="flex flex-wrap items-center gap-2">
        <button type="button" id="btn-toggle-all" onclick="filterDataset('all')"
                class="chart-filter-btn px-3 py-1.5 rounded-lg text-xs font-mono font-medium border border-phosphor/40 bg-phosphor/10 text-phosphor transition-all">
          Semua Tren
        </button>
        <button type="button" id="btn-toggle-omset" onclick="filterDataset(0)"
                class="chart-filter-btn px-3 py-1.5 rounded-lg text-xs font-mono font-medium border border-charcoal bg-ash/40 text-silver hover:text-snow hover:border-slate transition-all flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-phosphor inline-block"></span>
          Pendapatan: Rp <?= number_format($totalPendapatan, 0, ',', '.') ?>
        </button>
        <button type="button" id="btn-toggle-kotor" onclick="filterDataset(1)"
                class="chart-filter-btn px-3 py-1.5 rounded-lg text-xs font-mono font-medium border border-charcoal bg-ash/40 text-silver hover:text-snow hover:border-slate transition-all flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-sky-400 inline-block"></span>
          Profit Kotor: Rp <?= number_format($totalProfitKotor, 0, ',', '.') ?>
        </button>
        <button type="button" id="btn-toggle-bersih" onclick="filterDataset(2)"
                class="chart-filter-btn px-3 py-1.5 rounded-lg text-xs font-mono font-medium border border-charcoal bg-ash/40 text-silver hover:text-snow hover:border-slate transition-all flex items-center gap-1.5">
          <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
          Profit Bersih: Rp <?= number_format($totalProfitBersih, 0, ',', '.') ?>
        </button>
      </div>
    </div>

    <!-- Chart Canvas Container -->
    <div class="relative w-full h-72 sm:h-80">
      <canvas id="trendChart"></canvas>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Left: Low Stock Alert -->
    <div class="bg-obsidian border border-charcoal rounded-2xl overflow-hidden flex flex-col">
      <div class="px-6 py-4 border-b border-charcoal flex items-center justify-between bg-ash/30">
        <div>
          <h2 class="text-base font-medium text-snow">Peringatan Stok Sparepart</h2>
          <p class="text-xs text-smoke mt-0.5">Item dengan sisa stok 5 unit atau kurang</p>
        </div>
        <a href="../sparepart/sparepart.php" class="text-xs text-phosphor hover:underline font-mono">Kelola Stok &rarr;</a>
      </div>

      <div class="p-4 flex-1">
        <?php if (empty($stokMenipis)): ?>
          <div class="p-8 text-center text-smoke text-sm font-mono flex flex-col items-center justify-center h-full">
            <svg class="w-8 h-8 text-phosphor/60 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Semua inventaris sparepart dalam kuantitas aman (&gt; 5).</span>
          </div>
        <?php else: ?>
          <div class="space-y-2.5">
            <?php foreach ($stokMenipis as $item): ?>
              <div class="flex items-center justify-between p-3 rounded-xl bg-ash/40 border border-charcoal hover:border-slate transition-colors">
                <div>
                  <h4 class="text-sm font-medium text-snow"><?= htmlspecialchars($item['nama']) ?></h4>
                  <p class="text-xs text-smoke font-mono">Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                </div>
                <div class="flex items-center gap-3">
                  <span class="px-2.5 py-1 rounded-full text-xs font-mono <?= $item['stok'] == 0 ? 'bg-red-950/50 text-red-400 border border-red-900/50' : 'bg-amber-950/40 text-amber-300 border border-amber-900/50' ?>">
                    Sisa: <?= $item['stok'] ?>
                  </span>
                  <a href="../sparepart/edit_sparepart.php?id=<?= $item['sparepart_id'] ?>"
                     class="px-3 py-1 rounded-full text-xs bg-ash hover:bg-charcoal border border-charcoal text-snow transition-colors">
                    Restok
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Right: Recent Transactions -->
    <div class="bg-obsidian border border-charcoal rounded-2xl overflow-hidden flex flex-col">
      <div class="px-6 py-4 border-b border-charcoal flex items-center justify-between bg-ash/30">
        <div>
          <h2 class="text-base font-medium text-snow">Transaksi Terakhir</h2>
          <p class="text-xs text-smoke mt-0.5">Penerimaan pembayaran servis terkini</p>
        </div>
        <a href="../transaksi/transaksi.php" class="text-xs text-phosphor hover:underline font-mono">Semua Transaksi &rarr;</a>
      </div>

      <div class="p-4 flex-1">
        <?php if (empty($transaksiTerbaru)): ?>
          <div class="p-8 text-center text-smoke text-sm font-mono flex flex-col items-center justify-center h-full">
            Belum ada transaksi pembayaran tercatat.
          </div>
        <?php else: ?>
          <div class="space-y-2.5">
            <?php foreach ($transaksiTerbaru as $t): ?>
              <div class="flex items-center justify-between p-3 rounded-xl bg-ash/40 border border-charcoal hover:border-slate transition-colors">
                <div>
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-snow"><?= htmlspecialchars($t['nama_pelanggan']) ?></span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-obsidian border border-charcoal text-smoke"><?= htmlspecialchars($t['nomor_polisi']) ?></span>
                  </div>
                  <p class="text-[11px] text-smoke mt-0.5 font-mono"><?= htmlspecialchars($t['tanggal_transaksi']) ?> • Metode <?= strtoupper($t['metode_pembayaran']) ?></p>
                </div>
                <div class="text-right">
                  <span class="text-sm font-mono font-medium text-phosphor">Rp <?= number_format($t['total'], 0, ',', '.') ?></span>
                  <span class="block text-[10px] font-mono text-smoke uppercase"><?= $t['status_pembayaran'] ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const labels = <?= json_encode($chartLabels) ?>;
  const dataPendapatan = <?= json_encode($chartPendapatan) ?>;
  const dataProfitKotor = <?= json_encode($chartProfitKotor) ?>;
  const dataProfitBersih = <?= json_encode($chartProfitBersih) ?>;

  const canvas = document.getElementById('trendChart');
  const ctx = canvas.getContext('2d');

  // Linear gradients for aesthetic area fill
  const gradPendapatan = ctx.createLinearGradient(0, 0, 0, 320);
  gradPendapatan.addColorStop(0, 'rgba(62, 207, 142, 0.22)');
  gradPendapatan.addColorStop(1, 'rgba(62, 207, 142, 0.00)');

  const gradKotor = ctx.createLinearGradient(0, 0, 0, 320);
  gradKotor.addColorStop(0, 'rgba(56, 189, 248, 0.20)');
  gradKotor.addColorStop(1, 'rgba(56, 189, 248, 0.00)');

  const gradBersih = ctx.createLinearGradient(0, 0, 0, 320);
  gradBersih.addColorStop(0, 'rgba(245, 158, 11, 0.20)');
  gradBersih.addColorStop(1, 'rgba(245, 158, 11, 0.00)');

  const trendChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Total Pendapatan (Omset)',
          data: dataPendapatan,
          borderColor: '#3ecf8e',
          backgroundColor: gradPendapatan,
          borderWidth: 2.2,
          tension: 0.35,
          fill: true,
          pointBackgroundColor: '#3ecf8e',
          pointBorderColor: '#121212',
          pointBorderWidth: 1.5,
          pointRadius: 2.5,
          pointHoverRadius: 6,
        },
        {
          label: 'Profit Kotor',
          data: dataProfitKotor,
          borderColor: '#38bdf8',
          backgroundColor: gradKotor,
          borderWidth: 2.2,
          tension: 0.35,
          fill: true,
          pointBackgroundColor: '#38bdf8',
          pointBorderColor: '#121212',
          pointBorderWidth: 1.5,
          pointRadius: 2.5,
          pointHoverRadius: 6,
        },
        {
          label: 'Profit Bersih',
          data: dataProfitBersih,
          borderColor: '#f59e0b',
          backgroundColor: gradBersih,
          borderWidth: 2.2,
          tension: 0.35,
          fill: true,
          pointBackgroundColor: '#f59e0b',
          pointBorderColor: '#121212',
          pointBorderWidth: 1.5,
          pointRadius: 2.5,
          pointHoverRadius: 6,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: {
        mode: 'index',
        intersect: false,
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: '#181818',
          borderColor: '#2e2e2e',
          borderWidth: 1,
          titleColor: '#fafafa',
          bodyColor: '#b4b4b4',
          titleFont: { family: "'Inter', sans-serif", weight: '600', size: 12 },
          bodyFont: { family: "'JetBrains Mono', monospace", size: 11 },
          padding: 12,
          boxPadding: 6,
          usePointStyle: true,
          callbacks: {
            label: function(context) {
              return ' ' + context.dataset.label + ': Rp ' + Number(context.parsed.y).toLocaleString('id-ID');
            }
          }
        }
      },
      scales: {
        x: {
          grid: {
            color: 'rgba(255, 255, 255, 0.04)',
            tickColor: 'transparent'
          },
          ticks: {
            color: '#898989',
            font: { family: "'JetBrains Mono', monospace", size: 10 },
            maxRotation: 0,
            maxTicksLimit: 12
          }
        },
        y: {
          grid: {
            color: 'rgba(255, 255, 255, 0.05)',
            tickColor: 'transparent'
          },
          ticks: {
            color: '#898989',
            font: { family: "'JetBrains Mono', monospace", size: 10 },
            callback: function(val) {
              if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + ' jt';
              if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + ' rb';
              return 'Rp ' + val;
            }
          }
        }
      }
    }
  });

  function filterDataset(type) {
    document.querySelectorAll('.chart-filter-btn').forEach(btn => {
      btn.classList.remove('border-phosphor/40', 'bg-phosphor/10', 'text-phosphor');
      btn.classList.add('border-charcoal', 'bg-ash/40', 'text-silver');
    });

    if (type === 'all') {
      trendChart.data.datasets.forEach((ds) => { ds.hidden = false; });
      const b = document.getElementById('btn-toggle-all');
      b.classList.remove('border-charcoal', 'bg-ash/40', 'text-silver');
      b.classList.add('border-phosphor/40', 'bg-phosphor/10', 'text-phosphor');
    } else {
      trendChart.data.datasets.forEach((ds, idx) => {
        ds.hidden = (idx !== type);
      });
      const ids = ['btn-toggle-omset', 'btn-toggle-kotor', 'btn-toggle-bersih'];
      const activeBtn = document.getElementById(ids[type]);
      if (activeBtn) {
        activeBtn.classList.remove('border-charcoal', 'bg-ash/40', 'text-silver');
        activeBtn.classList.add('border-phosphor/40', 'bg-phosphor/10', 'text-phosphor');
      }
    }
    trendChart.update();
  }
</script>

<?php include "../include/footer.php"; ?>
