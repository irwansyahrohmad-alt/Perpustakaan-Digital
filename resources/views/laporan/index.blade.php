@extends('layouts.perpus')

@section('title', 'Laporan Perpustakaan')

@section('content')
    <h2>Laporan Perpustakaan</h2>

    <h3 class="section-title">Peminjaman per Status</h3>
    <div class="cards">
        @foreach (['diajukan', 'dipinjam', 'dikembalikan', 'ditolak'] as $st)
            <div class="card">
                <div>{{ ucfirst($st) }}</div>
                <div class="angka">{{ $perStatus[$st] ?? 0 }}</div>
            </div>
        @endforeach
    </div>

    <h3 class="section-title">Jumlah Buku per Kategori</h3>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Kategori</th><th>Jumlah Buku</th></tr></thead>
            <tbody>
                @forelse ($perKategori as $k)
                    <tr><td>{{ $k->nama }}</td><td>{{ $k->bukus_count }}</td></tr>
                @empty
                    <tr><td colspan="2">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h3 class="section-title">Jumlah Peminjaman per Buku</h3>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Judul Buku</th><th>Jumlah Peminjaman</th></tr></thead>
            <tbody>
                @forelse ($perBuku as $b)
                    <tr><td>{{ $b->Judul }}</td><td>{{ $b->peminjaman_count }}</td></tr>
                @empty
                    <tr><td colspan="2">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
