<?php

session_start();

require_once "../config/koneksi.php";
require_once "../include/function.php";

harusKasir();

$id = (int) $_GET["id"];

$sql = "SELECT * FROM pelanggan WHERE pelanggan_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

$pelanggan = $stmt->fetch();

if (!$pelanggan) {
  die("Data pelanggan tidak ditemukan.");
}

/*
|--------------------------------------------------------------------------
| Proses UPDATE
|--------------------------------------------------------------------------
*/

if (isset($_POST["update"])) {
  $nama = $_POST["nama"];
  $no_telpon = $_POST["no_telpon"];
  $alamat = $_POST["alamat"];

  $sql = "UPDATE pelanggan SET nama = ?, no_telpon = ?, alamat = ? WHERE pelanggan_id = ?";
  $stmt = $pdo->prepare($sql);

  $stmt->execute([$nama, $no_telpon, $alamat, $id]);

  header("Location: pelanggan.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Edit Pelanggan</title>
</head>

<body>
  <h1>Edit Pelanggan</h1>
  <form method="POST">
    <label>Nama</label><br>
    <input type="text" name="nama" value="<?= htmlspecialchars($pelanggan["nama"]) ?>" required>
    <br><br>
    <label>No Telepon</label><br>
    <input type="text" name="no_telpon" value="<?= htmlspecialchars($pelanggan["no_telpon"]) ?>">
    <br><br>
    <label>Alamat</label><br>
    <textarea name="alamat"><?= htmlspecialchars($pelanggan["alamat"]) ?></textarea>
    <br><br>

        <button type="submit" name="update">
            Update
        </button>

  </form>
  <br>
  <a href="pelanggan.php">Kembali</a>
</body>

</html>