@extends('layouts.perpus')

@section('title', 'Detail Buku')

@section('content')
    @if ($book)
        <div class="detail detail-grid">
            <x-book-cover :book="$book" class="cover-lg" />

            <div>
                <span class="badge">{{ $book['kategori'] }}</span>
                <h2>{{ $book['judul'] }}</h2>

                <table>
                    <tr><th>ID buku</th><td>{{ $book['id'] }}</td></tr>
                    <tr><th>Penulis</th><td>{{ $book['penulis'] }}</td></tr>
                    <tr><th>Tahun terbit</th><td>{{ $book['tahun_terbit'] }}</td></tr>
                    <tr><th>Kategori</th><td>{{ $book['kategori'] }}</td></tr>
                </table>

                @can('mengajukan peminjaman buku')
                    <form method="POST" action="{{ route('peminjaman.ajukan', $book['id']) }}" class="inline-form">
                        @csrf
                        <button type="submit" class="btn">Ajukan pinjam</button>
                    </form>
                @endcan

                <a href="{{ route('buku.index') }}" class="btn btn-ghost">Kembali ke daftar</a>
            </div>
        </div>
    @else
        <div class="not-found">
            <h2>Buku tidak ditemukan</h2>
            <p>Buku yang kamu cari tidak ada di koleksi kami.</p>
            <a href="{{ route('buku.index') }}" class="btn">Kembali ke daftar buku</a>
        </div>
    @endif
@endsection
