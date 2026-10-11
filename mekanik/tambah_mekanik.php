<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
// Khusus Owner
harusOwner();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nama = $_POST["nama"];
  $telepon = $_POST["no_telepon"];
  $spesialisasi = $_POST["spesialisasi"];

  $stmt = $pdo->prepare(" INSERT INTO mekanik (nama, no_telepon, spesialisasi) VALUES (?, ?, ?)");
  $stmt->execute([$nama, $telepon, $spesialisasi]);

  header("Location: mekanik.php");
  exit;
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-8">
  <h2 class="text-2xl font-bold">Tambah Mekanik</h2>

  <p class="text-gray-500 mt-1 mb-8">Tambahkan mekanik baru.</p>

  <div class="bg-white rounded-xl border p-6 max-w-xl">
    <form method="POST" class="space-y-5">
      <div>
        <label class="block text-sm font-medium mb-2">Nama Mekanik</label>
        <input type="text" name="nama" required class="w-full border rounded-lg px-4 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium mb-2">Nomor Telepon</label>
        <input type="text" name="no_telepon" class="w-full border rounded-lg px-4 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium mb-2">Spesialisasi</label>
        <input type="text" name="spesialisasi" placeholder="Contoh: Mesin" class="w-full border rounded-lg px-4 py-2">
      </div>

      <button class="bg-gray-900 text-white px-5 py-2 rounded-lg"> Simpan </button>
      <a href="mekanik.php" class="ml-2 text-gray-600">Batal</a>
    </form>
  </div>

</div>

<?php include "../include/footer.php"; ?>