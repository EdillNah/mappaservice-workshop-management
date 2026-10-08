<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusKasir();

$stmt = $pdo->query("SELECT * FROM pelanggan ORDER BY nama");

$pelanggan = $stmt->fetchAll();


if (isset($_POST["simpan"])) {
  $pelanggan_id = $_POST["pelanggan_id"];
  $nomor_polisi = $_POST["nomor_polisi"];
  $merk = $_POST["merk"];
  $model = $_POST["model"];
  $tahun = $_POST["tahun"];

  $sql = "INSERT INTO kendaraan (pelanggan_id, nomor_polisi, merk, model, tahun) VALUES (?, ?, ?, ?, ?)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$pelanggan_id, $nomor_polisi, $merk, $model, $tahun]);

  header("Location: kendaraan.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Tambah Kendaraan</title>
</head>

<body>
  <h1>Tambah Kendaraan</h1>

  <form method="POST">
    <label>Pemilik</label><br>
    <select name="pelanggan_id" required>
      <option value="">-- Pilih Pelanggan --</option>
      <?php foreach ($pelanggan as $data): ?>
        <option value="<?= $data["pelanggan_id"] ?>"><?= htmlspecialchars($data["nama"]) ?></option>
      <?php endforeach; ?>
    </select>
    <br><br>

    <label>Nomor Polisi</label><br>
    <input type="text" name="nomor_polisi" required placeholder="DD 1234 AB">
    <br><br>

    <label>Merk</label><br>
    <input type="text" name="merk" placeholder="Contoh: Honda, Toyota">
    <br><br>

    <label>Model</label><br>
    <input type="text" name="model" placeholder="Contoh: Vario 125, Avanza">
    <br><br>

    <label>Tahun</label><br>
    <input type="number" name="tahun">
    <br><br>

    <button type="submit" name="simpan">Simpan</button>
  </form>
  <br>

  <a href="kendaraan.php">Kembali</a>

</body>

</html>
