<?php
session_start();

require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../include/function.php";
require_once __DIR__ . "/../vendor/autoload.php";

harusOwner();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil data sparepart dari database
$stmt = $pdo->query("SELECT sparepart_id, nama, harga, stok FROM sparepart ORDER BY sparepart_id ASC");

$data = $stmt->fetchAll();

// Buat file Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Data Sparepart");

// Judul kolom
$sheet->fromArray(["ID", "Nama Sparepart", "Harga", "Stok"],null,"A1");

// Isi data
$baris = 2;

foreach ($data as $item) {
    $sheet->fromArray([
        $item["sparepart_id"],
        $item["nama"],
        $item["harga"],
        $item["stok"]
    ], null, "A" . $baris);

    $baris++;
}

// Format header dan lebar kolom
$sheet->getStyle("A1:D1")->getFont()->setBold(true);

foreach (range("A", "D") as $kolom) {
    $sheet->getColumnDimension($kolom)->setAutoSize(true);
}

// Kirim sebagai file unduhan
$filename = "data_sparepart_" . date("Ymd_His") . ".xlsx";

header(
    "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
);
header(
    'Content-Disposition: attachment; filename="' . $filename . '"'
);
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
