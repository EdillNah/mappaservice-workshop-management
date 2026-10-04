1. Informasi Produk

Nama Produk: MappaService
Tagline: Made for Better Workshop Management
Judul Akademik:
Sistem Informasi Manajemen Servis dan Inventaris Bengkel Berbasis Web

Jenis Produk: Web Application
Target: Pengelolaan operasional bengkel
Status: Development / UTS

Teknologi yang direncanakan
Bagian	Teknologi
Frontend	HTML, CSS, JavaScript
UI	Tailwind CSS
Backend	PHP Native
Database	MySQL
Development	VS Code + Laragon
Version Control	Git + GitHub
2. Latar Belakang

MappaService merupakan sistem informasi berbasis web yang dirancang untuk membantu bengkel dalam mengelola proses servis kendaraan secara terintegrasi.

Dalam proses operasional bengkel, terdapat berbagai data yang perlu dikelola, seperti data pelanggan, kendaraan, mekanik, sparepart, servis, transaksi, serta riwayat servis. Pengelolaan data yang tidak terintegrasi dapat menyulitkan pencatatan, pencarian informasi, pemantauan status servis, dan penyimpanan riwayat kendaraan.

MappaService dirancang dengan membagi aktivitas berdasarkan peran Owner dan Kasir. Owner berperan dalam pengelolaan dan pemantauan operasional, sedangkan Kasir menjadi operator utama dalam proses pelayanan pelanggan dan pencatatan servis.

3. Tujuan Produk

MappaService bertujuan untuk:

Mengelola data pelanggan secara terstruktur.
Mengelola data kendaraan pelanggan.
Mengelola data mekanik/pekerja.
Mengelola data sparepart dan stok.
Mencatat proses servis kendaraan.
Mencatat keluhan dan hasil pemeriksaan kendaraan.
Mengelola estimasi biaya dan konfirmasi servis.
Mengelola status pengerjaan servis.
Mengelola transaksi pembayaran.
Menyimpan riwayat servis kendaraan.
Menyediakan laporan bagi Owner.
Mendukung import data menggunakan Excel.
Mendukung upload dokumen Word yang berkaitan dengan servis.
4. Aktor Sistem

MappaService memiliki dua aktor utama:

4.1 Owner

Owner bertanggung jawab terhadap pengelolaan dan pemantauan operasional bengkel.

Hak akses Owner
Login
Dashboard
Kelola pelanggan
Kelola kendaraan
Kelola mekanik/pekerja
Kelola sparepart
Kelola data servis
Kelola transaksi
Melihat riwayat servis
Melihat laporan
Monitoring aktivitas bengkel
4.2 Kasir

Kasir merupakan operator utama dalam proses pelayanan pelanggan dan pencatatan servis.

Hak akses Kasir
Login
Menerima pelanggan
Mencari pelanggan
Menambahkan pelanggan
Mencari kendaraan
Menambahkan kendaraan
Mencatat keluhan pelanggan
Melihat mekanik yang tersedia
Menentukan mekanik
Mencatat hasil pemeriksaan dari mekanik
Mencatat diagnosis
Mencatat sparepart yang digunakan
Mengelola status servis
Mengelola estimasi biaya
Mencatat konfirmasi pelanggan
Mengelola transaksi pembayaran
Menyimpan riwayat servis
Import data Excel
Upload dokumen Word
5. Alur Utama Sistem

Alur utama MappaService:

Login
  ↓
Identifikasi Role
  ↓
┌───────────────┬────────────────┐
│               │                │
Owner          Kasir
│               │
↓               ↓
Dashboard      Pelanggan datang
│               ↓
Management     Cek pelanggan
& Monitoring    ↓
                Cek kendaraan
                ↓
                Catat keluhan
                ↓
                Cek mekanik tersedia
                ↓
                Assign mekanik
                ↓
             Pemeriksaan
                ↓
              Diagnosis
                ↓
          Estimasi servis
                ↓
       Konfirmasi pelanggan
                ↓
        ┌───────┴───────┐
        │               │
     Ditolak          Disetujui
        │               │
        ↓               ↓
  Tidak dilanjutkan   Diproses
                        ↓
                Sparepart digunakan
                        ↓
                 Servis selesai
                        ↓
                   Transaksi
                        ↓
                    Pembayaran
                        ↓
                Riwayat servis
                        ↓
                  Kendaraan diambil
6. Modul Sistem
6.1 Authentication

Semua pengguna internal wajib login.

Username
Password
    ↓
Authentication
    ↓
Role
    ↓
Owner / Kasir

Sistem kemudian memberikan akses sesuai role.

6.2 Dashboard
Owner

Menampilkan informasi seperti:

Jumlah pelanggan
Jumlah kendaraan
Mekanik tersedia
Servis sedang berjalan
Servis selesai
Transaksi
Informasi sparepart
Ringkasan laporan
Kasir

Lebih berfokus pada operasional:

Servis menunggu
Servis diproses
Servis selesai
Mekanik tersedia
Pembayaran
Aktivitas servis hari ini
7. Modul Pelanggan

Kasir dan Owner dapat mengelola data pelanggan.

Data:
ID Pelanggan
Nama
Nomor Telepon
Alamat
Operasi:
Create
Read
Update
Delete
Search
8. Modul Kendaraan

Kendaraan harus terhubung dengan pelanggan.

Data:
ID Kendaraan
Pelanggan
Nomor Polisi
Merk
Model
Tahun

Relasi:

1 Pelanggan
   ↓
Banyak Kendaraan
9. Modul Mekanik

Owner mengelola data mekanik.

Kasir menggunakan data mekanik untuk menentukan mekanik yang menangani servis.

Data:
ID Mekanik
Nama
Nomor Telepon
Spesialisasi
Status

Status mekanik:

Tersedia
Sibuk

Ketika mendapatkan pekerjaan:

Tersedia
   ↓
Sibuk

Setelah servis selesai:

Sibuk
   ↓
Tersedia
10. Modul Sparepart

Owner mengelola data sparepart.

Kasir mencatat sparepart yang digunakan pada servis.

Data:
ID Sparepart
Nama
Kategori
Harga
Stok

Penggunaan sparepart harus memengaruhi stok.

Contoh:

Stok awal       20
Digunakan        2
-------------------
Stok akhir      18
11. Modul Servis

Ini merupakan modul utama MappaService.

Data servis mencatat:

Pelanggan
Kendaraan
Mekanik
Tanggal masuk
Keluhan
Hasil pemeriksaan
Diagnosis
Tindakan
Estimasi biaya
Konfirmasi pelanggan
Status servis
12. Estimasi dan Konfirmasi Servis

Setelah pemeriksaan dan diagnosis, dibuat estimasi.

Contoh:

Jasa              Rp50.000
Sparepart          Rp75.000
---------------------------
Estimasi           Rp125.000

Pelanggan kemudian memberikan konfirmasi.

Jika disetujui:
Menunggu Konfirmasi
        ↓
     Disetujui
        ↓
     Diproses
Jika ditolak:
Menunggu Konfirmasi
        ↓
      Ditolak
13. Status Servis

Status servis:

Menunggu
    ↓
Diperiksa
    ↓
Menunggu Konfirmasi
    ↓
Disetujui
    ↓
Diproses
    ↓
Selesai
    ↓
Diambil

Status alternatif:

Menunggu Konfirmasi
        ↓
      Ditolak
Status pembayaran dibuat terpisah
Belum Dibayar
      ↓
Lunas

Sehingga kondisi seperti ini tetap bisa terjadi:

Status Servis      : Selesai
Status Pembayaran  : Belum Dibayar
14. Modul Transaksi

Transaksi dilakukan setelah servis selesai.

Komponen biaya:

Biaya jasa
+
Sparepart
+
Biaya lainnya
=
Total

Data transaksi minimal:

ID Transaksi
Servis
Total
Status pembayaran
Tanggal pembayaran
15. Modul Riwayat Servis

Setiap servis yang telah dilakukan disimpan sebagai riwayat kendaraan.

Contoh:

Honda Beat
DD 1234 XX

04 Oktober 2026
Keluhan:
Motor brebet

Diagnosis:
Gangguan sistem pembakaran

Mekanik:
Rizal

Sparepart:
Busi × 1

Total:
Rp75.000

Status:
Selesai

Riwayat dapat digunakan Owner dan Kasir untuk melihat histori kendaraan.

16. Modul Import Excel

Kasir dapat melakukan import data secara massal.

Alur:

File Excel
    ↓
Upload
    ↓
Validasi file
    ↓
Validasi struktur kolom
    ↓
Validasi data
    ↓
Preview
    ↓
Import
    ↓
Database

Contoh penggunaan:

pelanggan.xlsx
kendaraan.xlsx
sparepart.xlsx
17. Modul Dokumen Word

Kasir dapat mengunggah dokumen yang berkaitan dengan servis.

Alur:

Kasir
 ↓
Upload Word
 ↓
Pilih servis
 ↓
Dokumen disimpan
 ↓
Terhubung dengan data servis

Contohnya:

Servis #001
│
├── Data servis
├── Sparepart
├── Transaksi
└── Laporan_Servis.docx
18. Aturan Penting Sistem

Beberapa aturan bisnis yang perlu kita pegang ketika nanti coding:

1. Kendaraan harus memiliki pelanggan
Kendaraan → Pelanggan

Tidak boleh ada kendaraan tanpa pemilik.

2. Servis harus memiliki kendaraan
Servis → Kendaraan
3. Servis dapat memiliki mekanik

Mekanik ditentukan berdasarkan ketersediaan.

4. Mekanik yang sedang menangani servis berstatus Sibuk

Setelah pekerjaannya selesai, kembali Tersedia.

5. Sparepart yang digunakan mengurangi stok
6. Servis tidak langsung diproses sebelum konfirmasi pelanggan
7. Status pembayaran tidak boleh disamakan dengan status servis
8. Riwayat servis tidak dihapus ketika transaksi selesai

Riwayat harus tetap tersedia.

19. MVP untuk UTS

Supaya project tidak melebar ke mana-mana, saya sarankan fitur UTS kita prioritaskan seperti ini:

🔴 Wajib
✅ Login
✅ Role Owner & Kasir
✅ Pelanggan CRUD
✅ Kendaraan CRUD
✅ Mekanik CRUD
✅ Sparepart CRUD
✅ Servis
✅ Assign mekanik
✅ Keluhan
✅ Pemeriksaan & diagnosis
✅ Estimasi
✅ Konfirmasi servis
✅ Status servis
✅ Transaksi
✅ Riwayat servis
🟡 Requirement tugas
✅ Import Excel
✅ Upload Word
🟢 Pendukung
Dashboard
Laporan
Search
Filter
UI Tailwind
20. Urutan Pengembangan

PRD ini nantinya menjadi acuan roadmap:

ANALISIS
   ↓
PRD
   ↓
ERD / DATABASE
   ↓
CRUD DATABASE
   ↓
PHP + MySQL
   ↓
Authentication
   ↓
Pelanggan
   ↓
Kendaraan
   ↓
Mekanik
   ↓
Sparepart
   ↓
Servis
   ↓
Estimasi & Konfirmasi
   ↓
Transaksi
   ↓
Riwayat
   ↓
Excel
   ↓
Word
   ↓
Dashboard & Laporan
   ↓
Tailwind UI
   ↓
Testing
   ↓
UTS