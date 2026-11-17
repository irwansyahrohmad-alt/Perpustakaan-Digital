<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;

class RecapController extends Controller
{
    // GET /api/rekap/buku-per-kategori
    public function bukuPerKategori(): JsonResponse
    {
        $data = Kategori::withCount('bukus')->get();

        return response()->json(['data' => $data], 200);
    }

    // GET /api/rekap/peminjaman-per-buku
    public function peminjamanPerBuku(): JsonResponse
    {
        $data = Buku::withCount('peminjaman')
            ->orderByDesc('peminjaman_count')
            ->get();

        return response()->json(['data' => $data], 200);
    }
}
