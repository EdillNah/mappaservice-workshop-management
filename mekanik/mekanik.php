<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
harusKasir();

$data = $pdo->query("SELECT * FROM mekanik ORDER BY mekanik_id DESC")->fetchAll();

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-8">
  <div class="flex justify-between items-center mb-8">
    <div>
      <h2 class="text-2xl font-bold">Data Mekanik</h2>
      <p class="text-gray-500 mt-1">Kelola data dan ketersediaan mekanik.</p>
    </div>

    <a href="tambah_mekanik.php" class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800">+ Tambah Mekanik</a>
  </div>

  <div class="bg-white rounded-xl border overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="text-left px-6 py-4">Nama</th>
          <th class="text-left px-6 py-4">Telepon</th>
          <th class="text-left px-6 py-4">Spesialisasi</th>
          <th class="text-left px-6 py-4">Status</th>
          <th class="text-left px-6 py-4">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data as $mekanik): ?>
          <tr class="border-b last:border-0">
            <td class="px-6 py-4 font-medium"><?= htmlspecialchars($mekanik['nama']) ?></td>
            <td class="px-6 py-4"><?= htmlspecialchars($mekanik['no_telepon']) ?></td>
            <td class="px-6 py-4"><?= htmlspecialchars($mekanik['spesialisasi']) ?></td>
            <td class="px-6 py-4"><?php if ($mekanik['status'] === 'tersedia'): ?>
              <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">Tersedia</span>
              <?php else: ?>
              <span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-700">Sibuk</span>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4">
              <a href="edit_mekanik.php?id=<?= $mekanik['mekanik_id'] ?>"class="text-blue-600 mr-3">Edit</a>
                <form action="hapus_mekanik.php" method="POST" class="inline" onsubmit="return confirm('Hapus mekanik ini?')">
                  <input type="hidden" name="id" value="<?= $mekanik['mekanik_id'] ?>">
                  <button class="text-red-600">Hapus</button>
                </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include "../include/footer.php"; ?>