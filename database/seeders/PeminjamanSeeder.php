<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /** Contoh data peminjaman dengan status yang konsisten. */
    public function run(): void
    {
        $anggota = Anggota::pluck('id');
        $buku    = Buku::pluck('id');

        if ($anggota->isEmpty() || $buku->isEmpty()) {
            return;
        }

        $contoh = [
            ['status' => Peminjaman::STATUS_DIAJUKAN,     'pinjam' => 0,  'kembali' => null],
            ['status' => Peminjaman::STATUS_DIPINJAM,     'pinjam' => 3,  'kembali' => null],
            ['status' => Peminjaman::STATUS_DIKEMBALIKAN, 'pinjam' => 14, 'kembali' => 7],
        ];

        foreach ($contoh as $c) {
            Peminjaman::create([
                'anggota_id'      => $anggota->random(),
                'buku_id'         => $buku->random(),
                'tanggal_pinjam'  => now()->subDays($c['pinjam'])->toDateString(),
                'tanggal_kembali' => $c['kembali'] ? now()->subDays($c['kembali'])->toDateString() : null,
                'status'          => $c['status'],
            ]);
        }
    }
}
