<?php

session_start();

require_once "../config/koneksi.php";
require_once "../include/function.php";
harusKasir();

if (!isset($_POST["id"])) {
  header("Location: pelanggan.php");
  exit;
}

$id = (int) $_POST["id"];

$sql = "DELETE FROM pelanggan WHERE pelanggan_id = ?";

$stmt = $pdo->prepare($sql);

$stmt->execute([$id]);

header("Location: pelanggan.php");
exit;
