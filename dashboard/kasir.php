<?php
session_start();

require_once "../include/function.php";
harusKasir();

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kasir MappaService</title>
</head>

<body>
  <h1>MappaService</h1>

  <h2>Halaman Kasir</h2>

  <p>Selamat datang, <?= htmlspecialchars($_SESSION["name"]) ?></p>
  <hr>
  <h3>Pelayanan</h3>
    <ul>
      <li><a href="../pelanggan/pelanggan.php">Data Pelanggan</a></li>
      <li><a href="../kendaraan/kendaraan.php">Data Kendaraan</a></li>
      <li><a href="../servis/servis.php">Data Servis</a></li>
    </ul>
    <br>
    <a href="../views/auth/logout.php">Logout</a>

</body>

</html>