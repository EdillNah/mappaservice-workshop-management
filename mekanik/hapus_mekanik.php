<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
// Khusus Owner
harusOwner();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $id = $_POST["id"];
  $stmt = $pdo->prepare("DELETE FROM mekanik WHERE mekanik_id = ?");

  $stmt->execute([$id]);
}

header("Location: mekanik.php");
exit;