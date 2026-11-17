<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kelola\PeminjamanKelolaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

/*
|--------------------------------------------------------------------------
| Semua route di bawah ini wajib login (middleware auth).
| Pembatasan per fitur memakai middleware permission dari Spatie.
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Setelah login Breeze mengarah ke route 'dashboard' -> diarahkan sesuai role
    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');

    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('permission:akses dashboard admin')->name('admin.dashboard');

    Route::get('/petugas/dashboard', [DashboardController::class, 'petugas'])
        ->middleware('permission:akses dashboard petugas')->name('petugas.dashboard');

    // ---- Buku (lihat) ----
    Route::middleware('permission:lihat daftar buku')->group(function () {
        Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
        Route::get('/buku/{id}', [BukuController::class, 'show'])->name('buku.show');
    });

    // ======================================================================
    // [SOAL 1 - SENGAJA RENTAN] Tiga route di bawah hanya untuk audit keamanan.
    // ======================================================================
    // A2: SQL Injection
    Route::get('/buku-cari', [BukuController::class, 'cari'])->name('buku.cari');

    // A3: CSRF (penghapusan lewat GET)
    Route::get('/buku/{buku}/hapus', [BukuController::class, 'destroy'])
        ->middleware('permission:kelola data buku')->name('buku.hapus');

    // A4: IDOR (tidak ada pemeriksaan pemilik)
    Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.show');

    // ---- Buku (kelola: admin & petugas) ----
    Route::get('/kelola/buku', [BukuController::class, 'kelola'])
        ->middleware('permission:kelola data buku')->name('kelola.buku');

    // ---- Peminjaman milik sendiri (semua role) ----
    Route::post('/peminjaman/ajukan/{buku}', [PeminjamanController::class, 'ajukan'])
        ->middleware('permission:mengajukan peminjaman buku')->name('peminjaman.ajukan');

    Route::get('/peminjaman-saya', [PeminjamanController::class, 'saya'])
        ->middleware('permission:melihat status peminjaman sendiri')->name('peminjaman.saya');

    Route::get('/riwayat-peminjaman', [PeminjamanController::class, 'riwayat'])
        ->middleware('permission:melihat riwayat peminjaman sendiri')->name('riwayat.index');

    // Riwayat milik user lain: dicek lagi lewat Gate 'lihat-riwayat' (identitas), bukan cuma permission
    Route::get('/riwayat-peminjaman/{user}', [PeminjamanController::class, 'riwayatUser'])
        ->middleware('permission:melihat riwayat peminjaman sendiri')->name('riwayat.show');

    // ---- Operasional perpustakaan (admin & petugas) ----
    Route::prefix('kelola')->name('kelola.')->group(function () {
        Route::get('peminjaman', [PeminjamanKelolaController::class, 'index'])
            ->middleware('permission:kelola peminjaman')->name('peminjaman.index');
        Route::post('peminjaman/{peminjaman}/setujui', [PeminjamanKelolaController::class, 'setujui'])
            ->middleware('permission:memproses persetujuan peminjaman')->name('peminjaman.setujui');
        Route::post('peminjaman/{peminjaman}/tolak', [PeminjamanKelolaController::class, 'tolak'])
            ->middleware('permission:memproses persetujuan peminjaman')->name('peminjaman.tolak');

        Route::get('pengembalian', [PeminjamanKelolaController::class, 'pengembalian'])
            ->middleware('permission:kelola pengembalian')->name('pengembalian.index');
        Route::post('pengembalian/{peminjaman}', [PeminjamanKelolaController::class, 'kembalikan'])
            ->middleware('permission:mencatat pengembalian buku')->name('pengembalian.store');
    });

    Route::get('/laporan', [LaporanController::class, 'index'])
        ->middleware('permission:lihat laporan perpustakaan')->name('laporan.index');

    // ---- Manajemen pengguna & role (khusus admin) ----
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:kelola data pengguna')->name('index');
        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:kelola data pengguna')->name('store');
        Route::delete('{user}', [UserController::class, 'destroy'])
            ->middleware('permission:kelola data pengguna')->name('destroy');
        Route::patch('{user}/role', [UserController::class, 'updateRole'])
            ->middleware('permission:kelola role pengguna')->name('role');
    });

    // ---- Profil sendiri (ProfileController bawaan Breeze selalu memakai user yang login) ----
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->middleware('permission:lihat profil sendiri')->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->middleware('permission:ubah profil sendiri')->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->middleware('permission:ubah profil sendiri')->name('profile.destroy');
});

// Route login, register, logout, dll. dari Laravel Breeze (sudah memakai middleware guest / auth)
require __DIR__.'/auth.php';
