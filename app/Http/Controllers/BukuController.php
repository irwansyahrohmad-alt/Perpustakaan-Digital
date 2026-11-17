<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BukuController extends Controller
{
    private function format(Buku $b): array
    {
        return [
            'id'           => $b->id,
            'judul'        => $b->Judul,
            'penulis'      => $b->penulis,
            'tahun_terbit' => $b->tahun_terbit,
            'kategori'     => $b->kategori?->nama ?? '-',
            'gambar'       => $b->gambar,
        ];
    }

    /** Daftar buku (read-only) untuk semua role yang login. */
    public function index(): View
    {
        $books = Buku::with('kategori')->orderByDesc('id')->get()
            ->map(fn (Buku $b) => $this->format($b))
            ->all();

        return view('buku.index', compact('books'));
    }

    public function show($id): View
    {
        $buku = Buku::with('kategori')->find((int) $id);
        $book = $buku ? $this->format($buku) : null;

        return view('buku.show', compact('book'));
    }

    /** Halaman CRUD buku (admin & petugas) - memanggil /api/buku lewat fetch. */
    public function kelola(): View
    {
        return view('buku.kelola');
    }

    // ======================================================================
    // [SOAL 1 - SENGAJA RENTAN] Hanya untuk praktikum audit keamanan.
    // ======================================================================

    /**
     * A2 - SQL Injection: nilai query string 'judul' digabungkan langsung ke string SQL.
     */
    public function cari(Request $request): JsonResponse
    {
        $judul = $request->query('judul');

        $hasil = DB::select("SELECT * FROM bukus WHERE Judul = '$judul'");

        return response()->json($hasil);
    }

    /**
     * A3 - CSRF: dipanggil lewat route GET, jadi cukup dengan membuka/menekan sebuah tautan.
     */
    public function destroy(Buku $buku): RedirectResponse
    {
        $buku->delete();

        return redirect()->route('buku.index')->with('success', 'Buku berhasil dihapus.');
    }
}
