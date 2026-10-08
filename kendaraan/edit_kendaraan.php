<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
harusKasir();

$id = (int) $_GET["id"];

$sql = "SELECT * FROM kendaraan WHERE kendaraan_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$kendaraan = $stmt->fetch();

if (!$kendaraan) {
  die("Data kendaraan tidak ditemukan.");
}

$stmt = $pdo->query(
  "SELECT * FROM pelanggan ORDER BY nama"
);

$pelanggan = $stmt->fetchAll();

if (isset($_POST["update"])) {
  $pelanggan_id = $_POST["pelanggan_id"];
  $nomor_polisi = $_POST["nomor_polisi"];
  $merk = $_POST["merk"];
  $model = $_POST["model"];
  $tahun = $_POST["tahun"];

  $sql = "UPDATE kendaraan SET pelanggan_id = ?, nomor_polisi = ?, merk = ?, model = ?, tahun = ?
          WHERE kendaraan_id = ?";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $pelanggan_id,
    $nomor_polisi,
    $merk,
    $model,
    $tahun,
    $id
  ]);

  header("Location: kendaraan.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Kendaraan</title>
</head>

<body>
  <h1>Edit Kendaraan</h1>
  <form method="POST">
    <label>Pemilik</label><br>
    <select name="pelanggan_id" required>
    <?php foreach ($pelanggan as $data): ?>
      <option value="<?= $data["pelanggan_id"] ?>"<?= $data["pelanggan_id"] == $kendaraan["pelanggan_id"] ? "selected" : "" ?>>
        <?= htmlspecialchars($data["nama"]) ?>
      </option>
    <?php endforeach; ?>
    </select>
    <br><br>

    <label>Nomor Polisi</label><br>
    <input type="text" name="nomor_polisi" value="<?= htmlspecialchars($kendaraan["nomor_polisi"]) ?>" required>
    <br><br>

    <label>Merk</label><br>
    <input type="text" name="merk" value="<?= htmlspecialchars($kendaraan["merk"]) ?>">
    <br><br>

    <label>Model</label><br>
    <input type="text" name="model" value="<?= htmlspecialchars($kendaraan["model"]) ?>">
    <br><br>

    <label>Tahun</label><br>
    <input type="number" name="tahun" value="<?= htmlspecialchars($kendaraan["tahun"]) ?>">
    <br><br>

    <button type="submit" name="update">Update</button>
  </form>
  <br>
  <a href="kendaraan.php">Kembali</a>

</body>
</html>