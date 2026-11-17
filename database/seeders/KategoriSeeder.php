<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Manga', 'Light Novel', 'Sastra Indonesia', 'Fiksi Ilmiah', 'Sastra Dunia'] as $nama) {
            Kategori::firstOrCreate(['nama' => $nama]);
        }
    }
}
