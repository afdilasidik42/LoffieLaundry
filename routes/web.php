<?php

use App\Http\Controllers\AccuracyTestController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\MesinController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// User Tracking — Public (no auth required)
Route::get('/track', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/track', [TrackingController::class, 'search'])->name('tracking.search');

/*
|--------------------------------------------------------------------------
| Admin Routes — Protected by auth + role:admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Master Data — Kelola Pelanggan
    Route::resource('pelanggan', PelangganController::class)->except(['show']);

    // Master Data — Kelola Layanan
    Route::resource('layanan', LayananController::class)->except(['show']);

    // Master Data — Kelola Bahan
    Route::resource('bahan', BahanController::class)->except(['show']);

    // Master Data — Kelola Mesin
    Route::resource('mesin', MesinController::class)->except(['show']);

    // Operasional — Kelola Pesanan
    Route::resource('pesanan', PesananController::class);

    // Operasional — Kelola Status (Kanban Board)
    Route::get('/status', [StatusController::class, 'index'])->name('status.index');
    Route::put('/status/{id}/update', [StatusController::class, 'update'])->name('status.update');

    // Pelaporan — Kelola Laporan (Admin creates reports)
    Route::get('/laporan', [LaporanController::class, 'adminIndex'])->name('laporan.index');
    Route::post('/laporan/generate', [LaporanController::class, 'generate'])->name('laporan.generate');
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');

    // Prediksi — Riwayat Prediksi GM(1,4) + Re-run
    Route::get('/prediksi', [PrediksiController::class, 'index'])->name('prediksi.index');
    Route::post('/prediksi/{pesanan}/rerun', [PrediksiController::class, 'rerun'])->name('prediksi.rerun');

    // Uji Akurasi — MAPE/MAE Testing
    Route::get('/accuracy', [AccuracyTestController::class, 'index'])->name('accuracy.index');
    Route::get('/accuracy/export-csv', [AccuracyTestController::class, 'exportCsv'])->name('accuracy.export-csv');
});

/*
|--------------------------------------------------------------------------
| Owner Routes — Protected by auth + role:owner
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

    // Pelaporan — Kelola Laporan (Owner views & downloads)
    Route::get('/laporan', [LaporanController::class, 'ownerIndex'])->name('laporan.index');
    Route::get('/laporan/{id}/download-pdf', [LaporanController::class, 'downloadPdf'])->name('laporan.download-pdf');
    Route::get('/laporan/{id}/download-csv', [LaporanController::class, 'downloadCsv'])->name('laporan.download-csv');

    // Visualisasi — Halaman Grafik + Chart.js JSON API Endpoints
    Route::get('/grafik', [GrafikController::class, 'index'])->name('grafik.index');
    Route::get('/grafik/prediksi-akurasi/data', [GrafikController::class, 'prediksiAkurasi'])->name('grafik.prediksi-akurasi.data');
    Route::get('/grafik/volume-transaksi/data', [GrafikController::class, 'volumeTransaksi'])->name('grafik.volume-transaksi.data');
    Route::get('/grafik/tren-pelanggan/data', [GrafikController::class, 'trenPelanggan'])->name('grafik.tren-pelanggan.data');

    // Kelola Pengguna (Staf Admin) — CRUD
    Route::resource('users', UserController::class)->except(['show']);
});

