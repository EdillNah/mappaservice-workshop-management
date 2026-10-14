<?php
session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";

harusLogin();


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: servis.php");
  exit;
}

$servis_id = $_POST["servis_id"];
$diagnosa = $_POST["diagnosa"];
$status = $_POST["status"];
$biaya_jasa = isset($_POST["biaya_jasa"]) && $_POST["biaya_jasa"] !== '' ? (float) $_POST["biaya_jasa"] : 5000;

$statusValid = [
  'diperiksa',
  'menunggu_konfirmasi',
  'disetujui',
  'diproses',
  'selesai',
  'diambil'
];

if (!in_array($status, $statusValid)) {
  die("Status tidak valid.");
}

try {
  $pdo->beginTransaction();
  // Ambil mekanik dari servis
  $stmt = $pdo->prepare("SELECT mekanik_id FROM servis WHERE servis_id = ?");
  $stmt->execute([$servis_id]);

  $servis = $stmt->fetch();

  if (!$servis) {
    throw new Exception("Servis tidak ditemukan.");
  }
  
  // Update servis
  $stmt = $pdo->prepare("UPDATE servis SET diagnosa = ?, status = ?, biaya_jasa = ? WHERE servis_id = ?");

  $stmt->execute([$diagnosa, $status, $biaya_jasa, $servis_id]);
  // Jika selesai atau diambil,
  // mekanik kembali tersedia
  if (
    $status === 'selesai'|| $status === 'diambil'
  ) {
    $stmt = $pdo->prepare("UPDATE mekanik SET status = 'tersedia' WHERE mekanik_id = ?");
    $stmt->execute([$servis['mekanik_id']]);
  }

  $pdo->commit();

  header("Location: detail_servis.php?id=". $servis_id);
  exit;
} catch (Exception $e) {
  $pdo->rollBack();
  die("Gagal memperbarui servis: " . $e->getMessage());
}