<?php

session_start();

require_once "../config/koneksi.php";
require_once "../include/function.php";

harusKasir();

if (isset($_POST["simpan"])) {
  $nama = $_POST["nama"];
  $no_telpon = $_POST["no_telpon"];
  $alamat = $_POST["alamat"];

  $sql = "INSERT INTO pelanggan (nama, no_telpon, alamat) VALUES (?, ?, ?)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$nama,$no_telpon,$alamat]);

  header("Location: pelanggan.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Tambah Pelanggan</title>
</head>

<body>
  <h1>Tambah Pelanggan</h1>

  <form method="POST">
    <label>Nama</label><br>
    <input type="text" name="nama" required>
    <br><br>
    <label>No Telepon</label><br>
    <input type="text" name="no_telpon">
    <br><br>
    <label>Alamat</label><br>
    <textarea name="alamat"></textarea>
    <br><br>
    <button type="submit" name="simpan">Simpan</button>
  </form>
  <br>

  <a href="pelanggan.php">Kembali</a>

</body>

</html>
