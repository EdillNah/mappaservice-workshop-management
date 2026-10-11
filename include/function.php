<?php

function sudahLogin() {
  return isset($_SESSION["user_id"]);
}

function harusLogin() {
  if (!sudahLogin()) {
    header("Location: ../views/auth/login.php");
    exit;
  }
}

function harusKasir() {
  harusLogin();

  if ($_SESSION["role"] !== "kasir") {
    header("Location: ../dashboard/owner.php");
    exit;
  }
}

function harusOwner() {
  harusLogin();

  if ($_SESSION["role"] !== "owner") {
    header("Location: ../dashboard/kasir.php");
    exit;
  }
}

function isOwner() {
  return isset($_SESSION["role"]) && $_SESSION["role"] === "owner";
}

function isKasir() {
  return isset($_SESSION["role"]) && $_SESSION["role"] === "kasir";
}

function getUserRole() {
  return $_SESSION["role"] ?? 'guest';
}

function getUserName() {
  return $_SESSION["name"] ?? 'Pengguna';
}

function renderStatusBadge($status) {
  $statusConfig = [
    'menunggu' => [
      'label' => 'Menunggu',
      'class' => 'bg-zinc-500/10 text-zinc-300 border-zinc-500/20',
      'dot'   => 'bg-zinc-400'
    ],
    'diperiksa' => [
      'label' => 'Diperiksa',
      'class' => 'bg-purple-500/10 text-purple-300 border-purple-500/20',
      'dot'   => 'bg-purple-400'
    ],
    'menunggu_konfirmasi' => [
      'label' => 'Menunggu Konfirmasi',
      'class' => 'bg-amber-500/10 text-amber-300 border-amber-500/25',
      'dot'   => 'bg-amber-400 animate-pulse'
    ],
    'disetujui' => [
      'label' => 'Disetujui',
      'class' => 'bg-teal-500/10 text-teal-300 border-teal-500/20',
      'dot'   => 'bg-teal-400'
    ],
    'diproses' => [
      'label' => 'Sedang Diproses',
      'class' => 'bg-sky-500/10 text-sky-300 border-sky-500/20',
      'dot'   => 'bg-sky-400 animate-pulse'
    ],
    'selesai' => [
      'label' => 'Selesai',
      'class' => 'bg-phosphor/10 text-phosphor border-phosphor/25',
      'dot'   => 'bg-phosphor'
    ],
    'diambil' => [
      'label' => 'Sudah Diambil',
      'class' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
      'dot'   => 'bg-emerald-400'
    ],
  ];

  $cfg = $statusConfig[$status] ?? [
    'label' => ucwords(str_replace('_', ' ', $status)),
    'class' => 'bg-ash text-silver border-charcoal',
    'dot'   => 'bg-smoke'
  ];

  return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium tracking-tight whitespace-nowrap border shadow-sm backdrop-blur-sm ' . $cfg['class'] . '">'
       . '<span class="w-1.5 h-1.5 rounded-full shrink-0 ' . $cfg['dot'] . '"></span>'
       . '<span>' . htmlspecialchars($cfg['label']) . '</span>'
       . '</span>';
}
