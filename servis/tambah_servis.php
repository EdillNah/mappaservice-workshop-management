<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
harusKasir();

$sql = "SELECT kendaraan.kendaraan_id, kendaraan.nomor_polisi, kendaraan.merk, kendaraan.model, pelanggan.nama
        FROM kendaraan
        INNER JOIN pelanggan ON kendaraan.pelanggan_id = pelanggan.pelanggan_id
        ORDER BY pelanggan.nama";
$stmt = $pdo->query($sql);
$kendaraan = $stmt->fetchAll();

if (isset($_POST["simpan"])) {
  $kendaraan_id = $_POST["kendaraan_id"];
  $keluhan = $_POST["keluhan"];

  $sql = "INSERT INTO servis (kendaraan_id, keluhan) VALUES (?, ?)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$kendaraan_id, $keluhan]);

  header("Location: servis.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Catat Servis</title>
</head>

<body>
  <h1>Catat Servis</h1>
  <form method="POST">
    <label>Kendaraan</label><br>
    <select name="kendaraan_id" required>
      <option value="">-- Pilih Kendaraan --</option>
        <?php foreach ($kendaraan as $data): ?>
          <option value="<?= $data["kendaraan_id"] ?>">
            <?= htmlspecialchars($data["nama"]) ?>
            -
            <?= htmlspecialchars($data["nomor_polisi"]) ?>
            -
            <?= htmlspecialchars($data["merk"]) ?>
            <?= htmlspecialchars($data["model"]) ?>
          </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Keluhan</label>
    <br>
    <textarea name="keluhan" rows="5" cols="40" required></textarea>
    <br><br>

    <button type="submit" name="simpan">Simpan</button>
  </form><br>

  <a href="servis.php">Kembali</a>

</body>
</html>