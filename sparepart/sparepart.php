<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
harusKasir();

$data = $pdo->query("SELECT * FROM sparepart ORDER BY sparepart_id DESC")->fetchAll();

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-8">
  <div class="flex justify-between items-center mb-8">
    <div>
      <h2 class="text-2xl font-bold">Sparepart</h2>
      <p class="text-gray-500 mt-1">Kelola sparepart dan stok bengkel.</p>
    </div>

    <a href="tambah_sparepart.php" class="bg-gray-900 text-white px-4 py-2 rounded-lg"> + Tambah Sparepart</a>
  </div>

  <div class="bg-white rounded-xl border overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="text-left px-6 py-4">Nama</th>
          <th class="text-left px-6 py-4">Harga</th>
          <th class="text-left px-6 py-4">Stok</th>
          <th class="text-left px-6 py-4">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data as $item): ?>
          <tr class="border-b last:border-0">
            <td class="px-6 py-4 font-medium"><?= htmlspecialchars($item['nama']) ?></td>
            <td class="px-6 py-4">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
            <td class="px-6 py-4">
              <?php if ($item['stok'] <= 5): ?>
                <span class="text-red-600 font-medium"><?= $item['stok'] ?></span>
              <?php else: ?>
                <?= $item['stok'] ?>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4">
              <a href="edit_sparepart.php?id=<?= $item['sparepart_id'] ?>" class="text-blue-600 mr-3">Edit</a>
              <form action="hapus_sparepart.php" method="POST" class="inline" onsubmit="return confirm('Hapus sparepart ini?')">
                <input type="hidden" name="id" value="<?= $item['sparepart_id'] ?>">
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