<?php
session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
// Khusus Owner
harusOwner();

// Ambil ID sparepart dari URL
$id = $_GET["id"] ?? null;

if (!$id || !ctype_digit((string) $id)) {
  die("ID sparepart tidak valid.");
}

$stmt = $pdo->prepare("SELECT * FROM sparepart WHERE sparepart_id = ?");

$stmt->execute([$id]);
$sparepart = $stmt->fetch();

if (!$sparepart) {
  die("Data sparepart tidak ditemukan.");
}
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nama = trim($_POST["nama"] ?? "");
  $harga = $_POST["harga"] ?? "";
  $stok = $_POST["stok"] ?? "";

  if (
    $nama === "" ||
    !is_numeric($harga) ||
    !is_numeric($stok) ||
    (float) $harga < 0 ||
    (int) $stok < 0 ||
    (float) $stok != (int) $stok
  ) {
    $error = "Data tidak valid. Periksa kembali nama, harga, dan stok.";
  } else {
    $stmt = $pdo->prepare(" UPDATE sparepart SET nama = ?, harga = ?, stok = ? WHERE sparepart_id = ? ");
    $stmt->execute([$nama, $harga, (int) $stok,$id]);

    header("Location: sparepart.php?pesan=berhasil_edit");
    exit;
  }
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-8">
  <div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Edit Sparepart</h2>
    <p class="text-gray-500 mt-1">Perbarui informasi dan stok sparepart.</p>
  </div>
  <div class="bg-white rounded-xl border p-6 max-w-xl">
    <?php if ($error !== ""): ?>
      <div class="bg-red-50 text-red-700 p-3 rounded-lg mb-5">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <div>
        <label class="block text-sm font-medium mb-2">Nama Sparepart</label>
        <input type="text" name="nama" required value="<?= htmlspecialchars($_POST["nama"] ?? $sparepart["nama"])?>"
          class="w-full border rounded-lg px-4 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium mb-2">Harga (Rp)</label>
        <input type="number" name="harga" min="0" step="0.01" required value="<?= htmlspecialchars($_POST["harga"] ?? $sparepart["harga"]) ?>"
          class="w-full border rounded-lg px-4 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium mb-2">Stok</label>
        <input type="number" name="stok" min="0" step="1" required value="<?= htmlspecialchars($_POST["stok"] ?? $sparepart["stok"]) ?>"
          class="w-full border rounded-lg px-4 py-2">
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-lg hover:bg-gray-700">Simpan Perubahan</button>
        <a href="sparepart.php" class="px-5 py-2 rounded-lg border text-gray-600 hover:bg-gray-50">Batal</a>
      </div>
    </form>
  </div>
</div>

<?php include "../include/footer.php"; ?>
