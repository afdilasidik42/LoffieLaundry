<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\MesinController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TrackingController;
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
    Route::post('/login', [AuthController::class, 'login']);
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
});

/*
|--------------------------------------------------------------------------
| Owner Routes — Protected by auth + role:owner
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
});
