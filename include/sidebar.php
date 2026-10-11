<?php
$currentScript = $_SERVER['PHP_SELF'] ?? '';
$userRole = $_SESSION['role'] ?? 'kasir';
$userName = $_SESSION['name'] ?? ($userRole === 'owner' ? 'Owner' : 'Kasir');
$dashboardUrl = ($userRole === 'owner') ? '../dashboard/owner.php' : '../dashboard/kasir.php';

// Helper to determine active state
function isMenuActive($pattern, $currentScript) {
  if (is_array($pattern)) {
    foreach ($pattern as $p) {
      if (strpos($currentScript, $p) !== false) return true;
    }
    return false;
  }
  return strpos($currentScript, $pattern) !== false;
}

$menus = [
  [
    'label' => 'Dashboard',
    'url' => $dashboardUrl,
    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    'active' => isMenuActive('/dashboard/', $currentScript),
  ],
  [
    'label' => 'Pelanggan',
    'url' => '../pelanggan/pelanggan.php',
    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    'active' => isMenuActive('/pelanggan/', $currentScript),
  ],
  [
    'label' => 'Kendaraan',
    'url' => '../kendaraan/kendaraan.php',
    'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10 M13 8h4.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16H17',
    'active' => isMenuActive('/kendaraan/', $currentScript),
  ],
  [
    'label' => 'Catat Servis',
    'url' => '../servis/catat_servis.php',
    'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
    'active' => isMenuActive('catat_servis.php', $currentScript) || isMenuActive('tambah_servis.php', $currentScript),
  ],
  [
    'label' => 'Daftar Servis',
    'url' => '../servis/servis.php',
    'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
    'active' => (isMenuActive('/servis/', $currentScript) && !isMenuActive('catat_servis.php', $currentScript) && !isMenuActive('tambah_servis.php', $currentScript)),
  ],
  [
    'label' => 'Mekanik',
    'url' => '../mekanik/mekanik.php',
    'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    'active' => isMenuActive('/mekanik/', $currentScript),
  ],
  [
    'label' => 'Sparepart',
    'url' => '../sparepart/sparepart.php',
    'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    'active' => isMenuActive('/sparepart/', $currentScript),
  ],
  [
    'label' => 'Transaksi',
    'url' => '../transaksi/transaksi.php',
    'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
    'active' => isMenuActive('/transaksi/', $currentScript),
  ],
  [
    'label' => 'Riwayat Servis',
    'url' => '../riwayat/riwayat_servis.php',
    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
    'active' => isMenuActive('/riwayat/', $currentScript),
  ],

];
?>

<aside class="w-full md:w-64 bg-obsidian border-b md:border-b-0 md:border-r border-charcoal md:h-screen md:sticky md:top-0 md:self-start flex flex-col shrink-0 select-none z-30">
  <!-- Brand / Header -->
  <div class="px-5 py-5 border-b border-charcoal flex items-center justify-between shrink-0">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 rounded-lg bg-ash border border-charcoal flex items-center justify-center text-phosphor font-mono text-sm font-bold shadow-sm">
        <span class="text-phosphor">M</span>
      </div>
      <div>
        <div class="flex items-center gap-1.5">
          <span class="font-semibold text-snow text-base tracking-tight leading-none">MappaService</span>
          <span class="w-1.5 h-1.5 rounded-full bg-phosphor inline-block"></span>
        </div>
      </div>
    </div>
  </div>

  <!-- Navigation Workflow -->
  <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto min-h-0">
    <div class="px-3 pb-2 pt-1 flex items-center justify-between text-[10px] font-semibold tracking-wider text-smoke uppercase font-mono">
      <span>Alur Servis Bengkel</span>
    </div>

    <?php foreach ($menus as $menu): ?>
      <?php if ($menu['active']): ?>
        <!-- Active Nav Item -->
        <a href="<?= $menu['url'] ?>"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl bg-ash border border-charcoal text-snow font-medium text-sm transition-all duration-150 relative">
          <!-- Active Green Indicator Bar -->
          <span class="absolute left-0 top-2 bottom-2 w-1 bg-phosphor rounded-r"></span>
          <svg class="w-4 h-4 text-phosphor shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="<?= $menu['icon'] ?>" />
          </svg>
          <span class="truncate"><?= $menu['label'] ?></span>
          <span class="ml-auto w-1.5 h-1.5 rounded-full bg-phosphor"></span>
        </a>
      <?php else: ?>
        <!-- Inactive Nav Item -->
        <a href="<?= $menu['url'] ?>"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl text-silver hover:text-snow hover:bg-ash/50 border border-transparent hover:border-charcoal/60 text-sm transition-all duration-150">
          <svg class="w-4 h-4 text-smoke group-hover:text-silver shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6">
            <path stroke-linecap="round" stroke-linejoin="round" d="<?= $menu['icon'] ?>" />
          </svg>
          <span class="truncate"><?= $menu['label'] ?></span>
        </a>
      <?php endif; ?>
    <?php endforeach; ?>
  </nav>

  <!-- User Account & Logout Footer -->
  <div class="p-3 border-t border-charcoal mt-auto bg-obsidian shrink-0">
    <div class="bg-ash/50 border border-charcoal rounded-xl p-2.5 flex items-center justify-between gap-2.5">
      <!-- Info Akun Masuk -->
      <div class="flex items-center gap-2.5 min-w-0 flex-1">
        <div class="relative shrink-0">
          <div class="w-8 h-8 rounded-lg bg-charcoal border border-slate/30 flex items-center justify-center text-xs font-bold font-mono text-snow uppercase">
            <?= strtoupper(substr($userName, 0, 1)) ?>
          </div>
          <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-obsidian <?= $userRole === 'owner' ? 'bg-phosphor' : 'bg-blue-400' ?>"></span>
        </div>
        <div class="min-w-0 flex-1">
          <p class="text-xs font-medium text-snow truncate leading-tight" title="<?= htmlspecialchars($userName) ?>">
            <?= htmlspecialchars($userName) ?>
          </p>
          <span class="text-[10px] font-mono uppercase tracking-wider text-smoke block mt-0.5">
            <?= htmlspecialchars($userRole) ?>
          </span>
        </div>
      </div>

      <!-- Tombol Logout -->
      <a href="../views/auth/logout.php"
         title="Keluar (Logout)"
         class="shrink-0 p-2 rounded-lg text-smoke hover:text-red-400 hover:bg-red-950/40 border border-transparent hover:border-red-900/40 transition-all duration-150 flex items-center justify-center group"
         aria-label="Logout">
        <svg class="w-4 h-4 text-smoke group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
      </a>
    </div>
  </div>
</aside>

<main class="flex-1 min-w-0 bg-obsidian min-h-screen overflow-x-hidden">

