<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
// Khusus Owner
harusOwner();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nama = $_POST["nama"];
  $harga = $_POST["harga"];
  $stok = $_POST["stok"];
  $stmt = $pdo->prepare("INSERT INTO sparepart(nama, harga, stok) VALUES (?, ?, ?)");

  $stmt->execute([$nama,$harga,$stok]);

  header("Location: sparepart.php");
  exit;
}

include "../include/header.php";
include "../include/sidebar.php";
?>

<div class="p-8">
  <h2 class="text-2xl font-bold">Tambah Sparepart</h2>
  <div class="bg-white rounded-xl border p-6 max-w-xl mt-8">
    <form method="POST" class="space-y-5">
      <div>
        <label class="block text-sm font-medium mb-2">Nama Sparepart</label>
        <input type="text" name="nama" required class="w-full border rounded-lg px-4 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium mb-2">Harga</label>
        <input type="number" name="harga" min="0" required class="w-full border rounded-lg px-4 py-2">
      </div>


      <div>
        <label class="block text-sm font-medium mb-2">Stok</label>
        <input type="number" name="stok" min="0" required class="w-full border rounded-lg px-4 py-2">
      </div>

      <button class="bg-gray-900 text-white px-5 py-2 rounded-lg">Simpan</button>
    </form>
  </div>
</div>

<?php include "../include/footer.php"; ?>