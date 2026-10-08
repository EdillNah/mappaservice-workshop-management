<?php

session_start();

require_once "../include/function.php";
harusOwner();

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Owner MappaService</title>
</head>

<body>
  <h1>MappaService</h1>

  <h2>Halaman Owner</h2>
  <p>Selamat datang,<?= htmlspecialchars($_SESSION["name"]) ?></p>

  <p>Login sebagai Owner berhasil.</p>

  <p>Modul Owner belum dibuat pada tahap ini.</p>

  <a href="../views/auth/logout.php">Logout</a>

</body>

</html>