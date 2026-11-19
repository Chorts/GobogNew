<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\StokGobogController;
use App\Http\Controllers\TenanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/stok', [StokGobogController::class, 'index'])->name('stok.index');
    Route::post('/stok/tambah', [StokGobogController::class, 'tambah'])->name('stok.tambah');
    Route::post('/stok/kurangi', [StokGobogController::class, 'kurangi'])->name('stok.kurangi');

    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');

    Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::post('/pengembalian', [PengembalianController::class, 'store'])->name('pengembalian.store');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');

    Route::resource('tenan', TenanController::class)->except(['show']);
    Route::resource('users', UserController::class)->except(['show']);

    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan/harga', [PengaturanController::class, 'updateHarga'])->name('pengaturan.harga');
    Route::post('/pengaturan/bagi-hasil', [PengaturanController::class, 'updateBagiHasil'])->name('pengaturan.bagi-hasil');
});
