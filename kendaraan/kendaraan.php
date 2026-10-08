<?php

session_start();

require_once "../config/koneksi.php";
require_once "../include/function.php";

harusKasir();

$sql = "SELECT kendaraan.kendaraan_id, pelanggan.nama, kendaraan.nomor_polisi, kendaraan.merk, kendaraan.model, kendaraan.tahun
        FROM kendaraan
        INNER JOIN pelanggan ON kendaraan.pelanggan_id = pelanggan.pelanggan_id
        ORDER BY kendaraan.kendaraan_id DESC";

$stmt = $pdo->query($sql);

$kendaraan = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Data Kendaraan</title>
</head>

<body>
  <h1>Data Kendaraan</h1>

  <a href="../dashboard/kasir.php">← Kembali ke Kasir</a>
  <br><br>
  <a href="tambah_kendaraan.php">+ Tambah Kendaraan</a>
  <br><br>

  <table border="1" cellpadding="10">
    <tr>
      <th>ID</th>
      <th>Pemilik</th>
      <th>No Polisi</th>
      <th>Merk</th>
      <th>Model</th>
      <th>Tahun</th>
      <th>Aksi</th>
    </tr>
    <?php foreach ($kendaraan as $data): ?>
      <tr>
        <td><?= $data["kendaraan_id"] ?></td>
        <td><?= htmlspecialchars($data["nama"]) ?></td>
        <td><?= htmlspecialchars($data["nomor_polisi"]) ?></td>
        <td><?= htmlspecialchars($data["merk"]) ?></td>
        <td><?= htmlspecialchars($data["model"]) ?></td>
        <td><?= htmlspecialchars($data["tahun"]) ?></td>
        <td>
          <a href="edit_kendaraan.php?id=<?= $data["kendaraan_id"] ?>">Edit</a>
          <br>
          <form action="hapus_kendaraan.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus kendaraan ini?')">
            <input type="hidden" name="id" value="<?= $data["kendaraan_id"] ?>">
            <button type="submit">Hapus</button>
          </form>

        </td>

      </tr>
    <?php endforeach; ?>

  </table>
</body>

</html>
