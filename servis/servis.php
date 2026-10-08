<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
harusKasir();

$sql = "SELECT servis.servis_id, pelanggan.nama, kendaraan.nomor_polisi, kendaraan.merk, kendaraan.model,
              servis.keluhan,
              servis.tanggal_masuk
        FROM servis
        INNER JOIN kendaraan ON servis.kendaraan_id = kendaraan.kendaraan_id
        INNER JOIN pelanggan ON kendaraan.pelanggan_id = pelanggan.pelanggan_id
        ORDER BY servis.servis_id DESC";
$stmt = $pdo->query($sql);
$servis = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Data Servis</title>
</head>

<body>
  <h1>Data Servis</h1>
  <a href="kasir.php">← Kembali ke Kasir</a>
  <br><br>
  <a href="tambah_servis.php">+ Catat Servis</a>
  <br><br>
  <table border="1" cellpadding="10">
    <tr>
      <th>ID</th>
      <th>Pelanggan</th>
      <th>Kendaraan</th>
      <th>No Polisi</th>
      <th>Keluhan</th>
      <th>Tanggal Masuk</th>
    </tr>
      <?php foreach ($servis as $data): ?>
        <tr>
          <td><?= $data["servis_id"] ?></td>
          <td><?= htmlspecialchars($data["nama"]) ?></td>
          <td><?= htmlspecialchars($data["merk"]) ?><?= htmlspecialchars($data["model"]) ?></td>
          <td><?= htmlspecialchars($data["nomor_polisi"]) ?></td>
          <td><?= htmlspecialchars($data["keluhan"]) ?></td>
          <td><?= htmlspecialchars($data["tanggal_masuk"]) ?></td>
        </tr>
      <?php endforeach; ?>
  </table>

</body>
</html>