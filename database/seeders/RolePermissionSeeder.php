<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache permission Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 16 permission sesuai tabel pada soal no. 4
        $semua = [
            'akses dashboard admin',              // 1
            'akses dashboard petugas',            // 2
            'lihat daftar buku',                  // 3
            'kelola data buku',                   // 4
            'kelola data pengguna',               // 5
            'kelola role pengguna',               // 6
            'kelola peminjaman',                  // 7
            'kelola pengembalian',                // 8
            'mengajukan peminjaman buku',         // 9
            'memproses persetujuan peminjaman',   // 10
            'mencatat pengembalian buku',         // 11
            'melihat status peminjaman sendiri',  // 12
            'melihat riwayat peminjaman sendiri', // 13
            'lihat laporan perpustakaan',         // 14
            'lihat profil sendiri',               // 15
            'ubah profil sendiri',                // 16
        ];

        foreach ($semua as $nama) {
            Permission::findOrCreate($nama, 'web');
        }

        // DatabaseSeeder memakai WithoutModelEvents, sehingga cache permission Spatie
        // tidak ter-refresh otomatis. Reset manual agar permission baru terbaca.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Permission yang dimiliki siswa (dipakai juga oleh admin & petugas)
        $umum = [
            'lihat daftar buku',
            'mengajukan peminjaman buku',
            'melihat status peminjaman sendiri',
            'melihat riwayat peminjaman sendiri',
            'lihat profil sendiri',
            'ubah profil sendiri',
        ];

        // Operasional perpustakaan (dimiliki admin & petugas)
        $operasional = [
            'kelola data buku',
            'kelola peminjaman',
            'kelola pengembalian',
            'memproses persetujuan peminjaman',
            'mencatat pengembalian buku',
            'lihat laporan perpustakaan',
        ];

        Role::findOrCreate('admin', 'web')->syncPermissions(array_merge(
            $umum,
            $operasional,
            ['akses dashboard admin', 'kelola data pengguna', 'kelola role pengguna']
        ));

        Role::findOrCreate('petugas', 'web')->syncPermissions(array_merge(
            $umum,
            $operasional,
            ['akses dashboard petugas']
        ));

        Role::findOrCreate('siswa', 'web')->syncPermissions($umum);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
