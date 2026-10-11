<?php

session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
// Khusus Owner
harusOwner();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $stmt = $pdo->prepare("DELETE FROM sparepart WHERE sparepart_id = ?");
  $stmt->execute([$_POST["id"]]);
}

header("Location: sparepart.php");
exit;