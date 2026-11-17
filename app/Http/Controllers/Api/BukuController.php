<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BukuResource;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    private function rules(): array
    {
        return [
            'kategori'     => 'required|string|max:100',
            'judul'        => 'required|string|max:255',
            'penulis'      => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|digits:4|min:1900|max:' . date('Y'),
        ];
    }

    // Memetakan atribut request -> kolom database
    private function toColumns(array $v): array
    {
        // kategori dikirim sebagai nama; dicari di tabel kategoris, dibuat jika belum ada
        $kategori = Kategori::firstOrCreate(['nama' => trim($v['kategori'])]);

        return [
            'kategori_id'  => $kategori->id,
            'Judul'        => $v['judul'],
            'penulis'      => $v['penulis'],
            'tahun_terbit' => $v['tahun_terbit'],
        ];
    }

    public function index()
    {
        return BukuResource::collection(
            Buku::with('kategori')->orderBy('id', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $buku = Buku::create($this->toColumns($validated));

        return (new BukuResource($buku->load('kategori')))
            ->response()->setStatusCode(201);
    }

    public function show(Buku $buku)
    {
        return new BukuResource($buku->load('kategori'));
    }

    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate($this->rules());
        $buku->update($this->toColumns($validated));

        return new BukuResource($buku->load('kategori'));
    }

    public function destroy(Buku $buku)
    {
        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus']);
    }
}
