<?php

use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\RecapController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Baca data buku: tetap publik seperti modul sebelumnya
Route::apiResource('buku', BukuController::class)->only(['index', 'show'])->names('api.buku');

// Tulis data buku: wajib login (session) + permission "kelola data buku".
// Grup 'web' ditambahkan supaya session & CSRF aktif di route /api/* ini,
// sehingga halaman Kelola Buku (fetch) tetap bisa memakai URL /api/buku yang sama.
Route::middleware(['web', 'auth', 'permission:kelola data buku'])->group(function () {
    Route::apiResource('buku', BukuController::class)->only(['store', 'update', 'destroy'])->names('api.buku');
});

Route::get('rekap/buku-per-kategori', [RecapController::class, 'bukuPerKategori']);
Route::get('rekap/peminjaman-per-buku', [RecapController::class, 'peminjamanPerBuku']);
