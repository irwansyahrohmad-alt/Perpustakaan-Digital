<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    /**
     * Koleksi awal: judul yang tidak terlalu pasaran (manga, light novel, sastra, fiksi ilmiah).
     * Format: [kategori, judul, penulis, tahun terbit, file sampul di folder public/]
     *
     * Sampul di public/images/buku/*.svg adalah ilustrasi buatan sendiri (bukan sampul asli).
     * Mau pakai sampul asli: taruh file gambarnya di public/images/buku/ lalu ubah kolom terakhir,
     * atau isi dengan URL lengkap (https://...).
     */
    public function run(): void
    {
        $koleksi = [
            // Manga
            ['Manga', 'Oyasumi Punpun', 'Inio Asano', 2007, 'images/buku/oyasumi-punpun.svg'],
            ['Manga', 'Uzumaki', 'Junji Ito', 1998, 'images/buku/uzumaki.svg'],
            ['Manga', 'Pluto', 'Naoki Urasawa', 2003, 'images/buku/pluto.svg'],
            ['Manga', 'Nausicaä of the Valley of the Wind', 'Hayao Miyazaki', 1982, 'images/buku/nausicaa-of-the-valley-of-the-wind.svg'],
            ['Manga', 'Blame!', 'Tsutomu Nihei', 1997, 'images/buku/blame.svg'],
            ['Manga', 'Girls\' Last Tour', 'Tsukumizu', 2014, 'images/buku/girls-last-tour.svg'],
            ['Manga', 'Witch Hat Atelier', 'Kamome Shirahama', 2016, 'images/buku/witch-hat-atelier.svg'],
            ['Manga', 'A Bride\'s Story', 'Kaoru Mori', 2008, 'images/buku/a-bride-s-story.svg'],
            ['Manga', 'Wandering Son', 'Takako Shimura', 2002, 'images/buku/wandering-son.svg'],
            ['Manga', 'Tekkonkinkreet', 'Taiyo Matsumoto', 1993, 'images/buku/tekkonkinkreet.svg'],

            // Light Novel
            ['Light Novel', 'Boogiepop and Others', 'Kouhei Kadono', 1998, 'images/buku/boogiepop-and-others.svg'],
            ['Light Novel', 'Gosick', 'Kazuki Sakuraba', 2003, 'images/buku/gosick.svg'],
            ['Light Novel', 'Crest of the Stars', 'Hiroyuki Morioka', 1996, 'images/buku/crest-of-the-stars.svg'],

            // Sastra Indonesia
            ['Sastra Indonesia', 'Perburuan', 'Pramoedya Ananta Toer', 1950, 'images/buku/perburuan.svg'],
            ['Sastra Indonesia', 'Para Priyayi', 'Umar Kayam', 1992, 'images/buku/para-priyayi.svg'],
            ['Sastra Indonesia', 'Burung-Burung Manyar', 'Y.B. Mangunwijaya', 1981, 'images/buku/burung-burung-manyar.svg'],
            ['Sastra Indonesia', 'Cala Ibi', 'Nukila Amal', 2003, 'images/buku/cala-ibi.svg'],

            // Fiksi Ilmiah
            ['Fiksi Ilmiah', 'We', 'Yevgeny Zamyatin', 1924, 'images/buku/we.svg'],
            ['Fiksi Ilmiah', 'Ubik', 'Philip K. Dick', 1969, 'images/buku/ubik.svg'],
            ['Fiksi Ilmiah', 'Blindsight', 'Peter Watts', 2006, 'images/buku/blindsight.svg'],

            // Sastra Dunia
            ['Sastra Dunia', 'Pedro Páramo', 'Juan Rulfo', 1955, 'images/buku/pedro-paramo.svg'],
            ['Sastra Dunia', 'Invisible Cities', 'Italo Calvino', 1972, 'images/buku/invisible-cities.svg'],
            ['Sastra Dunia', 'Ice', 'Anna Kavan', 1967, 'images/buku/ice.svg'],
        ];

        foreach ($koleksi as [$kategori, $judul, $penulis, $tahun, $gambar]) {
            $k = Kategori::firstOrCreate(['nama' => $kategori]);

            Buku::updateOrCreate(
                ['Judul' => $judul],
                [
                    'kategori_id'  => $k->id,
                    'penulis'      => $penulis,
                    'tahun_terbit' => (string) $tahun,
                    'gambar'       => $gambar,
                ]
            );
        }
    }
}
