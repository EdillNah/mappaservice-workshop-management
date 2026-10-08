<?php
session_start();
require_once "../config/koneksi.php";
require_once "../include/function.php";
harusKasir();

include "../include/header.php";
include "../include/sidebar.php";
?>
<div class="p-8">

    <!-- Header halaman -->
    <div class="mb-8">

        <h2 class="text-2xl font-bold text-gray-800">
            Dashboard Kasir
        </h2>

        <p class="text-gray-500 mt-1">
            Kelola aktivitas operasional bengkel.
        </p>

    </div>


    <!-- Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

        <div class="bg-white p-5 rounded-xl border">
            <p class="text-sm text-gray-500">
                Servis Hari Ini
            </p>

            <h3 class="text-2xl font-bold mt-2">
                0
            </h3>
        </div>


        <div class="bg-white p-5 rounded-xl border">
            <p class="text-sm text-gray-500">
                Menunggu Konfirmasi
            </p>

            <h3 class="text-2xl font-bold mt-2">
                0
            </h3>
        </div>


        <div class="bg-white p-5 rounded-xl border">
            <p class="text-sm text-gray-500">
                Sedang Diproses
            </p>

            <h3 class="text-2xl font-bold mt-2">
                0
            </h3>
        </div>


        <div class="bg-white p-5 rounded-xl border">
            <p class="text-sm text-gray-500">
                Selesai Hari Ini
            </p>

            <h3 class="text-2xl font-bold mt-2">
                0
            </h3>
        </div>

    </div>


    <!-- Aktivitas terbaru -->
    <div class="mt-8 bg-white rounded-xl border">

        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold">
                Servis Terbaru
            </h3>
        </div>

        <div class="p-6 text-gray-500 text-sm">
            Belum ada data servis.
        </div>

    </div>

</div>

<?php include "../include/footer.php"; ?>