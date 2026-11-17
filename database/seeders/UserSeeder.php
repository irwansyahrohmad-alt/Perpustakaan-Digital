<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Akun awal. Role ditetapkan di sini (mekanisme berwenang), bukan dari form registrasi.
     * Password admin diambil dari SEED_ADMIN_PASSWORD di .env (bukan "password").
     * Akun lain tetap memakai password awal: password
     */
    public function run(): void
    {
        $akun = [
            ['name' => 'Admin Perpustakaan',  'email' => 'admin@perpus.test',   'role' => 'admin'],
            ['name' => 'Petugas Perpustakaan', 'email' => 'petugas@perpus.test', 'role' => 'petugas'],
            ['name' => 'Siswa Satu',           'email' => 'siswa@perpus.test',   'role' => 'siswa'],
            ['name' => 'Siswa Dua',            'email' => 'siswa2@perpus.test',  'role' => 'siswa'],
        ];

        $passwordAdmin = env('SEED_ADMIN_PASSWORD');
        if (empty($passwordAdmin)) {
            throw new \RuntimeException('SEED_ADMIN_PASSWORD belum diisi di .env');
        }

        foreach ($akun as $a) {
            $user = User::updateOrCreate(
                ['email' => $a['email']],
                ['name' => $a['name'], 'password' => $a['role'] === 'admin' ? $passwordAdmin : 'password'] // di-hash otomatis oleh cast 'hashed'
            );

            $user->syncRoles($a['role']);
        }
    }
}
