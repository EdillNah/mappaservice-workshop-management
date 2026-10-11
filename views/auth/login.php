<?php

session_start();
require_once "../../config/koneksi.php";

$error = "";

if (isset($_POST["login"])) {
  $username = trim($_POST["username"] ?? "");
  $password = trim($_POST["password"] ?? "");

  $sql = "SELECT * FROM users WHERE username = ?";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([$username]);

  $user = $stmt->fetch();

  if ($user && $password === $user["password"]) {
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["name"] = $user["name"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];

    if ($user["role"] === "owner") {
      header("Location: ../../dashboard/owner.php");
      exit;
    }

    if ($user["role"] === "kasir") {
      header("Location: ../../dashboard/kasir.php");
      exit;
    }
  } else {
    $error = "Username atau password salah. Silakan coba kembali.";
  }
}

?>
<!DOCTYPE html>
<html lang="id" class="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk — MappaService</title>

  <!-- font inter dan Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'system-ui', 'sans-serif'],
            mono: ['"JetBrains Mono"', 'monospace'],
          },
          colors: {
            obsidian: '#121212',
            ash: '#242424',
            charcoal: '#2e2e2e',
            slate: '#393939',
            graphite: '#4d4d4d',
            smoke: '#898989',
            silver: '#b4b4b4',
            snow: '#fafafa',
            phosphor: {
              DEFAULT: '#3ecf8e',
              hover: '#5be8a6',
              border: '#1f4b37',
            }
          }
        }
      }
    }
  </script>
  <style>
    body {
      background-color: #121212;
      color: #fafafa;
      font-family: 'Inter', system-ui, sans-serif;
      letter-spacing: -0.007em;
    }
    input:focus {
      outline: none !important;
      border-color: #3ecf8e !important;
      box-shadow: none !important;
    }
  </style>
</head>

<body class="min-h-screen bg-obsidian text-snow flex flex-col justify-center items-center p-4 selection:bg-phosphor/20 selection:text-phosphor">

  <div class="fixed inset-0 pointer-events-none opacity-20" style="background-image: radial-gradient(#2e2e2e 1px, transparent 1px); background-size: 24px 24px;"></div>

  <div class="w-full max-w-[420px] relative z-10">
    <div class="text-center mb-8">
      <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-snow flex items-center justify-center gap-2">
        MappaService <span class="w-2.5 h-2.5 rounded-full bg-phosphor"></span>
      </h1>
      <p class="text-xs text-smoke mt-2 font-mono">Workshop Management & POS System</p>
    </div>

    <div class="bg-obsidian border border-charcoal rounded-2xl p-7 shadow-2xl relative">
      <div class="mb-6">
        <h2 class="text-base font-medium text-snow">Masuk ke Sistem</h2>
        <p class="text-xs text-smoke mt-1">Gunakan akun Kasir atau Owner untuk melanjutkan</p>
      </div>

      <?php if ($error): ?>
        <div class="mb-5 p-3 rounded-lg bg-red-950/40 border border-red-900/60 text-red-300 text-xs flex items-start gap-2.5">
          <svg class="w-4 h-4 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <form method="POST" class="space-y-4">
        <div>
          <label class="block text-xs font-medium text-silver mb-1.5">Username</label>
          <input type="text" name="username" placeholder="admin@email" required autocomplete="username"
                class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2.5 text-sm text-snow placeholder-smoke transition-colors">
        </div>

        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="block text-xs font-medium text-silver">Password</label>
          </div>
          <input type="password" name="password" placeholder="••••••••" required autocomplete="current-password"
                class="w-full bg-obsidian border border-slate rounded-lg px-3.5 py-2.5 text-sm text-snow placeholder-smoke transition-colors">
        </div>

        <button type="submit" name="login"
                class="w-full mt-2 bg-phosphor text-obsidian hover:bg-phosphor-hover border border-phosphor hover:border-phosphor-border font-medium text-sm py-2.5 px-4 rounded-full transition-all duration-150 flex items-center justify-center gap-2 group">
          <span>Masuk ke Dashboard</span>
          <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </button>
      </form>
    </div>

    <p class="text-center text-[11px] text-graphite mt-6 font-mono">
      &copy; <?= date('Y') ?> MappaService • Workshop Management Platform
    </p>
  </div>
</body>

</html>
