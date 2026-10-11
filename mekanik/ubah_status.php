<?php
session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
harusKasir();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: mekanik.php");
  exit;
}

$mekanik_id = $_POST["mekanik_id"] ?? null;
$status = $_POST["status"] ?? null;

// Validasi status
$statusValid = ["tersedia", "sibuk"];

if (!$mekanik_id || !in_array($status, $statusValid)) {
  header("Location: mekanik.php");
  exit;
}

// Update status
$stmt = $pdo->prepare("UPDATE mekanik SET status = ? WHERE mekanik_id = ?");
$stmt->execute([$status,$mekanik_id]);

header("Location: mekanik.php");
exit;