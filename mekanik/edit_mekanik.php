<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
// Khusus Owner
harusOwner();

$id = $_GET["id"] ?? null;
$stmt = $pdo->prepare("SELECT * FROM mekanik WHERE mekanik_id = ?");

$stmt->execute([$id]);
$mekanik = $stmt->fetch();

if (!$mekanik) {
  die("Data mekanik tidak ditemukan.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nama = $_POST["nama"];
  $telepon = $_POST["no_telepon"];
  $spesialisasi = $_POST["spesialisasi"];
  $status = $_POST["status"];

  $stmt = $pdo->prepare("UPDATE mekanik SET
      nama = ?,
      no_telepon = ?,
      spesialisasi = ?,
      status = ?
    WHERE mekanik_id = ?
  ");

  $stmt->execute([$nama, $telepon, $spesialisasi, $status, $id]);

  header("Location: mekanik.php");
  exit;
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-8">
  <h2 class="text-2xl font-bold">Edit Mekanik</h2>

  <div class="bg-white rounded-xl border p-6 max-w-xl mt-8">
    <form method="POST" class="space-y-5">
      <div>
        <label class="block text-sm font-medium mb-2">Nama</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($mekanik['nama']) ?>" required class="w-full border rounded-lg px-4 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium mb-2">Nomor Telepon</label
        <input type="text" name="no_telepon" value="<?= htmlspecialchars($mekanik['no_telepon']) ?>" class="w-full border rounded-lg px-4 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium mb-2">Spesialisasi</label>
        <input type="text" name="spesialisasi" value="<?= htmlspecialchars($mekanik['spesialisasi']) ?>" class="w-full border rounded-lg px-4 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium mb-2">Status</label>
        <select name="status" class="w-full border rounded-lg px-4 py-2">
          <option value="tersedia" <?= $mekanik['status'] === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
          <option value="sibuk"<?= $mekanik['status'] === 'sibuk' ? 'selected' : '' ?>>Sibuk</option>
        </select>
      </div>

      <button class="bg-gray-900 text-white px-5 py-2 rounded-lg">Simpan Perubahan</button>
    </form>
  </div>
</div>

<?php include "../include/footer.php"; ?>