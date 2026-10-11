# 🔧 MappaService

> **Made for Better Workshop Management**  
> *Sistem Informasi Manajemen Servis, Inventaris, dan Finansial Bengkel Terintegrasi Berbasis Web.*

---

## 📌 Tentang MappaService

**MappaService** adalah aplikasi web manajemen operasional bengkel terpadu yang dirancang untuk membantu bengkel motor/mobil dalam mendigitalkan seluruh siklus kerja harian—mulai dari penerimaan kendaraan pelanggan, penugasan teknisi/mekanik, diagnosis kerusakan, alokasi suku cadang, pembayaran kasir, pencatatan belanja restock, hingga visualisasi analitik performa finansial dan profitabilitas bengkel untuk pemilik (*Owner*).

Dibangun dengan pendekatan **PHP Native & PDO**, antarmuka bertema **Dark Mode (Obsidian & Phosphor Emerald)** yang modern terinspirasi dari standar aplikasi SaaS terkini, serta visualisasi data interaktif ditenagai **Chart.js**.

---

## ✨ Fitur-Fitur Utama

### 👑 1. Executive Dashboard (Owner Portal)
* **Grafik Tren Finansial 30 Hari (Chart.js)**:
  * Visualisasi omset pendapatan (*Total Omset*).
  * Estimasi profit kotor (*Gross Margin* dari jasa dan suku cadang).
  * Estimasi profit bersih (*Net Profit*).
  * Filter interaktif dataset per tren atau seluruh performa.
* **KPI Operasional & Finansial**:
  * Total akumulasi modal belanja *restock* suku cadang.
  * Status tim mekanik (ketersediaan *Tersedia* vs *Sibuk* secara real-time).
  * Status inventaris suku cadang & peringatan dini stok menipis (≤ 5 unit).
  * Database pelanggan dan kendaraan terdaftar.
* **Monitoring Transaksi**: Pemantauan langsung riwayat penerimaan pembayaran kasir terkini.

---

### 💼 2. Operational Dashboard (Kasir Portal)
* **Aktivitas Hari Ini**: Monitoring jumlah servis aktif, servis menunggu konfirmasi, serta ringkasan kendaraan masuk.
* **Tabel Antrean Servis Terkini**: Akses cepat ke detail diagnosis dan pemrosesan kendaraan.
* **Aksi Cepat**: Pintasan registrasi servis baru, penerimaan pelanggan, dan kasir pembayaran.

---

### 🛠️ 3. Manajemen Siklus Servis & Pengerjaan
* **Pencatatan Servis Baru**:
  * Pencarian/pemilihan pelanggan dan kendaraan terdaftar.
  * Pencatatan keluhan awal kendaraan.
  * Penugasan mekanik penanggung jawab (status mekanik otomatis berubah menjadi *Sibuk*).
* **Detail & Diagnosis Servis**:
  * Catatan hasil pemeriksaan fisik / diagnosis kerusakan oleh teknisi.
  * Penentuan dan penyesuaian biaya jasa perbaikan.
  * Alokasi pemakaian suku cadang / sparepart dengan kalkulasi subtotal dan otomatis memotong sisa stok.
* **Manajemen Status Alur Servis**:
  Badge status terstandarisasi dengan indikator visual modern:
  * ⚪ `Menunggu` (Antrean awal)
  * 🟣 `Diperiksa` (Sedang dalam inspeksi teknisi)
  * 🟠 `Menunggu Konfirmasi` (Estimasi menunggu persetujuan pelanggan)
  * 🔵 `Disetujui` (Pelanggan menyetujui tindakan perbaikan)
  * 🔷 `Sedang Diproses` (Dalam tahap pengerjaan suku cadang & servis)
  * 🟢 `Selesai` (Perbaikan selesai, mekanik otomatis kembali *Tersedia*)
  * ❇️ `Sudah Diambil` (Kendaraan telah diserahkan & administrasi lunas)

---

### 💳 4. Transaksi & Kasir Pembayaran
* **Kalkulasi Otomatis**: Integrasi otomatis antara akumulasi biaya jasa bengkel dan total sparepart terpakai.
* **Multi-Metode Pembayaran**: Mendukung pembayaran secara Tunai (*Cash*) maupun non-tunai (*QRIS*).
* **Kalkulator Kembalian Instan**: Validasi nominal pembayaran serta penghitungan uang kembalian secara otomatis.
* **Rincian Bukti Servis / Nota**: Halaman rincian pembayaran lengkap untuk bukti transaksi pelanggan.

---

### 📦 5. Manajemen Inventaris Suku Cadang & Restock
* **Katalog Sparepart**: Pencatatan suku cadang lengkap dengan harga satuan dan sisa stok.
* **Early Warning Stok**: Penanda visual khusus jika stok sparepart habis atau kritis (≤ 5 unit).
* **Modul Restock Batch**:
  * Pencatatan pengadaan suku cadang dari supplier.
  * Penginputan harga beli modal dan jumlah barang.
  * Riwayat belanja modal yang terhubung langsung ke KPI Dashboard Owner.

---

### 👨‍🔧 6. Manajemen Tim Mekanik
* Pendataan mekanik dengan nomor telepon dan spesialisasi teknis (*Mesin*, *Kelistrikan*, *Servis Umum*, dll).
* Pelacakan status ketersediaan (*Tersedia* / *Sibuk*) yang terintegrasi secara otomatis saat ditugaskan ke unit servis.

---

### 👥 7. Manajemen Pelanggan & Kendaraan
* **Data Pelanggan**: Kelola nama, nomor telepon WhatsApp, dan alamat pelanggan.
* **Data Kendaraan**: Relasi kendaraan dengan pemiliknya, mencakup nomor polisi, merk, model, dan tahun pembuatan.

---

### 📜 8. Arsip & Riwayat Servis
* Rekapitulasi seluruh riwayat kendaraan yang pernah melakukan servis.
* Filter pencarian berdasarkan nama pemilik, nomor polisi, maupun teknisi penanggung jawab.
* Status pelunasan pembayaran (*Lunas* / *Belum Bayar*).

---

## 🔄 Alur Operasional Sistem (Service Workflow)

```text
[Pelanggan Datang] ──> [Kasir Catat Servis & Pilih Mekanik]
                                    │
                                    ▼
                         (Mekanik: Status Sibuk)
                                    │
                                    ▼
                         [Pemeriksaan & Diagnosis]
                                    │
                                    ▼
                         [Alokasi Sparepart & Jasa]
                                    │
                                    ▼
                         [Menunggu Konfirmasi Owner/Pelanggan]
                                    │
                                    ▼
                         [Proses Pengerjaan Servis]
                                    │
                                    ▼
                         [Servis Selesai] ──> (Mekanik: Status Tersedia)
                                    │
                                    ▼
                         [Kasir Pembayaran (Cash/QRIS)]
                                    │
                                    ▼
                         [Arsip Riwayat & Laporan Owner]
```

---

## 👥 Peran Pengguna (User Roles)

| Peran | Deskripsi & Hak Akses |
|---|---|
| **👑 Owner** | Memiliki akses menyeluruh: memantau analitik omset, profit kotor, profit bersih, riwayat belanja modal restock, manajemen seluruh master data (mekanik, sparepart, pelanggan, kendaraan), dan evaluasi riwayat bengkel. |
| **💼 Kasir** | Operator garda depan: menerima kendaraan pelanggan, mencatat keluhan servis, mengalokasikan mekanik, mengisi detail diagnosa dan pemakaian sparepart, mengelola status pengerjaan, memproses pembayaran transaksi, serta melihat riwayat servis. |

---

## 🧩 Teknologi yang Digunakan

| Komponen | Teknologi | Keterangan |
|---|---|---|
| **Backend** | PHP Native (v8.x) | Logika sistem, routing modular, dan arsitektur session-based auth |
| **Database Access** | PDO (PHP Data Objects) | Prepared statements untuk keamanan dari SQL Injection |
| **Database Server** | MySQL / MariaDB | Penyimpanan basis data relasional |
| **Styling & UI** | Tailwind CSS | Utility-first CSS dengan custom palet *Obsidian Theme* & Dark Mode |
| **Data Visualization** | Chart.js | Visualisasi interaktif grafik tren finansial & profitabilitas |
| **Typography** | Google Fonts | Inter (UI Font) & JetBrains Mono (Data/Monospace Font) |
| **Spreadsheet Engine** | PhpSpreadsheet | Dukungan integrasi pengolahan berkas data Excel |
| **Dev Environment** | Laragon / XAMPP | Lingkungan web server lokal Apache & MySQL |

---

## 🗄️ Struktur Database (`mappaservice_db`)

Database terdiri dari 10 tabel yang saling terintegrasi:

```text
mappaservice_db
├── users             (Akun otentikasi Owner & Kasir)
├── pelanggan         (Data kontak & identitas pelanggan)
├── kendaraan         (Identitas motor, nomor polisi, relasi pelanggan)
├── mekanik           (Daftar teknisi, spesialisasi, status ketersediaan)
├── sparepart         (Katalog suku cadang, harga jual, dan sisa stok)
├── servis            (Pencatatan servis masuk, keluhan, diagnosa, mekanik, biaya jasa, status)
├── detail_servis     (Rincian sparepart yang terpakai pada servis tertentu)
├── transaksi         (Pencatatan invoice, total bayar, kembalian, metode pembayaran)
├── restock           (Batch pengadaan suku cadang, total modal restock, tanggal)
└── detail_restock    (Rincian item suku cadang yang dibeli pada batch restock)
```

---

## 🚀 Panduan Instalasi Lokal

### 1. Prasyarat Sistem
* Web Server: **Laragon** (direkomendasikan) atau **XAMPP**
* **PHP**: Versi 8.0 atau lebih tinggi
* **MySQL / MariaDB**
* Web Browser modern (Google Chrome, Microsoft Edge, Mozilla Firefox)

### 2. Langkah Pemasangan
1. Salin atau clone repositori ini ke folder web server Anda:
   ```bash
   # Jika menggunakan Laragon
   cd C:\laragon\www
   git clone https://github.com/EdillNah/mappaservice-workshop-management.git tes_desain
   ```
2. Pastikan servis **Apache** dan **MySQL** telah berjalan di Laragon.
3. Buka **phpMyAdmin** atau **DBeaver**, lalu buat database baru:
   ```sql
   CREATE DATABASE mappaservice_db;
   ```
4. Impor skema dan data awal dari berkas [database/schema.sql](file:///c:/laragon/www/tes_desain/database/schema.sql) ke dalam database `mappaservice_db`.
5. Sesuaikan konfigurasi database jika diperlukan pada berkas [config/koneksi.php](file:///c:/laragon/www/tes_desain/config/koneksi.php):
   ```php
   $host = "localhost";
   $db   = "mappaservice_db";
   $user = "root";
   $pass = "";
   ```
6. Buka peramban (*browser*) dan akses aplikasi melalui URL:
   ```text
   http://localhost/tes_desain/
   ```

---

## 🔑 Akun Pengguna Bawaan (Default Credentials)

| Role | Username | Password | Keterangan |
|---|---|---|---|
| **Owner** | `owner` | `12345` | Akses penuh ke Executive Dashboard & Laporan |
| **Kasir** | `kasir` | `12345` | Akses ke Operasional Servis & Kasir Pembayaran |

---

## 📁 Struktur Direktori Project

```text
tes_desain/
├── config/              # Konfigurasi koneksi database PDO
├── dashboard/           # Dashboard Owner (analitik) & Kasir (operasional)
├── database/            # Skrip SQL skema tabel dan data awal
├── include/             # Komponen global (header, sidebar, footer, helper functions)
├── kendaraan/           # Modul CRUD data kendaraan
├── mekanik/             # Modul CRUD data teknisi / mekanik
├── pelanggan/           # Modul CRUD data pelanggan
├── riwayat/             # Modul arsip & pelacakan riwayat servis
├── servis/              # Modul alur servis, diagnosis, dan suku cadang
├── sparepart/           # Modul katalog sparepart & transaksi restock
├── transaksi/           # Modul kasir pembayaran (Cash / QRIS)
├── views/auth/          # Form login dan autentikasi
├── index.php            # Gerbang pengalihan awal ke login
└── README.md            # Dokumentasi utama proyek
```

---

## 📄 Lisensi & Kontribusi
Project ini dikembangkan untuk kebutuhan manajemen bengkel profesional dan keperluan akademik.  
Dikelola dan dirawat oleh pengembang **MappaService**.
