# 🔧 MappaService

### Made for Better Workshop Management

MappaService adalah aplikasi web untuk membantu bengkel dalam mengelola proses pelayanan dan pencatatan data secara lebih terstruktur, mulai dari data pelanggan, kendaraan, hingga pencatatan servis.

Project ini dikembangkan sebagai bagian dari proses pembelajaran pengembangan aplikasi web, khususnya dalam penerapan **PHP Native, MySQL, CRUD, dan integrasi database**.

---

## 📌 Tentang MappaService

Dalam operasional bengkel, terdapat berbagai data yang perlu dikelola secara konsisten, seperti data pelanggan, kendaraan, keluhan, serta riwayat servis.

MappaService dikembangkan sebagai solusi sederhana untuk membantu proses tersebut melalui sebuah sistem berbasis web.

Pada tahap awal pengembangan, fokus utama MappaService adalah membangun fondasi sistem berupa:

- 🔐 Authentication dan pembagian role pengguna
- 👤 Pengelolaan data pelanggan
- 🚗 Pengelolaan data kendaraan
- 🛠️ Pencatatan servis dan keluhan
- 🗄️ Integrasi aplikasi dengan database
- 🔄 Implementasi operasi CRUD

Project ini akan dikembangkan secara bertahap sesuai dengan kebutuhan sistem dan proses pembelajaran.

---

## 🎯 Tujuan

MappaService memiliki beberapa tujuan utama:

1. Membantu proses pencatatan data pelanggan dan kendaraan.
2. Membantu kasir dalam mencatat kendaraan yang masuk ke bengkel.
3. Mencatat keluhan pelanggan sebagai bagian dari data servis.
4. Menerapkan sistem CRUD menggunakan PHP Native dan MySQL.
5. Menerapkan pembagian hak akses berdasarkan role pengguna.
6. Menjadi media pembelajaran dalam pengembangan aplikasi web berbasis database.

---

## 👥 Role Pengguna

MappaService menggunakan dua role utama:

### 👑 Owner

Owner merupakan pengguna dengan hak akses yang lebih luas untuk memantau dan mengelola operasional bengkel.

Pada tahap awal, fitur Owner masih dibuat sederhana dan akan dikembangkan secara bertahap.

### 💼 Kasir

Kasir merupakan pengguna yang berperan dalam proses pelayanan dan pencatatan operasional bengkel.

Pada tahap awal, Kasir dapat:

- Mencatat pelanggan
- Mencatat kendaraan
- Mencatat keluhan
- Mencatat data servis

---

## 🚀 Fitur Saat Ini

### 🔐 Authentication
- Login pengguna
- Pembagian role Owner dan Kasir
- Logout
- Pembatasan akses berdasarkan role

### 👤 Data Pelanggan
- Menampilkan data pelanggan
- Menambahkan pelanggan
- Mengubah data pelanggan
- Menghapus pelanggan

### 🚗 Data Kendaraan
- Menampilkan data kendaraan
- Menambahkan kendaraan
- Mengubah data kendaraan
- Menghapus kendaraan
- Menghubungkan kendaraan dengan pelanggan

### 🛠️ Data Servis
- Mencatat kendaraan yang melakukan servis
- Mencatat keluhan pelanggan
- Menyimpan waktu masuk servis
- Menghubungkan servis dengan kendaraan dan pelanggan

> Fitur di atas merupakan bagian dari tahap awal pengembangan dan masih akan terus dikembangkan.

---

## 🧩 Teknologi

MappaService dibangun menggunakan teknologi berikut:

| Teknologi | Penggunaan |
|---|---|
| HTML | Struktur halaman |
| CSS | Tampilan antarmuka |
| JavaScript | Interaksi pada sisi client |
| PHP Native | Backend dan logika aplikasi |
| MySQL / MariaDB | Database |
| PDO | Koneksi dan interaksi database |
| Git | Version control |
| GitHub | Repository dan pengelolaan project |
| Laragon | Local development environment |
| DBeaver | Database management |

---

## 🗂️ Konsep Database

Pada tahap awal, database MappaService menggunakan beberapa tabel utama:

```text
mappaservice_db
│
├── users
│
├── pelanggan
│
├── kendaraan
│
└── servis
