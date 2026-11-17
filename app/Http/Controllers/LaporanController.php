<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(): View
    {
        return view('laporan.index', [
            'perKategori' => Kategori::withCount('bukus')->get(),
            'perBuku'     => Buku::withCount('peminjaman')->orderByDesc('peminjaman_count')->get(),
            'perStatus'   => Peminjaman::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }
}
