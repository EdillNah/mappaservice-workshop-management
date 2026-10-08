<?php

session_start();

require_once "../config/koneksi.php";
require_once "../include/function.php";

harusKasir();

$sql = "SELECT * FROM pelanggan ORDER BY pelanggan_id DESC";

$stmt = $pdo->query($sql);

$pelanggan = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Data Pelanggan</title>
</head>

<body>
  <h1>Data Pelanggan</h1>

  <a href="../dashboard/kasir.php">← Kembali ke Kasir</a>
  <br><br>
  <a href="tambah_pelanggan.php">+ Tambah Pelanggan</a>
  <br><br>
  <table border="1" cellpadding="10">
    <tr>
      <th>No</th>
      <th>ID Pelanggan</th>
      <th>Nama</th>
      <th>No Telepon</th>
      <th>Alamat</th>
      <th>Aksi</th>
    </tr>

    <?php if (empty($pelanggan)): ?>
      <tr>
        <td colspan="6" align="center">Belum ada data pelanggan.</td>
      </tr>
    <?php else: ?>
      <?php
        $no = 1;
        foreach ($pelanggan as $data):
      ?>
        <tr>
          <td><?= $no++; ?></td> 
          <td><?= $data["pelanggan_id"] ?></td>
          <td><?= htmlspecialchars($data["nama"]) ?></td>
          <td><?= htmlspecialchars($data["no_telpon"]) ?></td>
          <td><?= htmlspecialchars($data["alamat"]) ?></td>
          <td>
            <a href="edit_pelanggan.php?id=<?= $data["pelanggan_id"] ?>">Edit</a>
            <br>
            <form action="hapus_pelanggan.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')">
              <input type="hidden" name="id" value="<?= $data["pelanggan_id"] ?>">
              <button type="submit">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>

</body>

</html>
<td>
          