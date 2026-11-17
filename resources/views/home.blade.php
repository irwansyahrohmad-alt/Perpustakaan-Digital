@extends('layouts.perpus')

@section('title', 'Beranda')

@section('content')
    <section class="hero">
        <div>
            <h2>Temukan bacaan langka berikutnya</h2>
            <p>Dari manga dan light novel sampai sastra Indonesia dan fiksi ilmiah. Lihat koleksinya, ajukan peminjaman, dan pantau riwayat pinjamanmu dari satu tempat.</p>

            @auth
                <a href="{{ route('buku.index') }}" class="btn btn-gold">Lihat daftar buku</a>
            @else
                <p>Masuk dulu untuk melihat koleksi buku.</p>
                <a href="{{ route('login') }}" class="btn btn-gold">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-light">Buat akun siswa</a>
                @endif
            @endauth
        </div>

        <div class="shelf" aria-hidden="true">
            <span style="--c:#b4532a; height:72%"></span>
            <span style="--c:#f0c25a; height:90%"></span>
            <span style="--c:#1f5f5b; height:62%"></span>
            <span style="--c:#a8283a; height:82%"></span>
            <span style="--c:#2b3a6b; height:70%"></span>
            <span style="--c:#e0a526; height:96%"></span>
            <span style="--c:#5b2a6b; height:58%"></span>
        </div>
    </section>

    @auth
        @php
            $terbaru = \App\Models\Buku::with('kategori')->orderByDesc('id')->take(4)->get()->map(fn ($b) => [
                'id'           => $b->id,
                'judul'        => $b->Judul,
                'penulis'      => $b->penulis,
                'tahun_terbit' => $b->tahun_terbit,
                'kategori'     => $b->kategori?->nama ?? '-',
                'gambar'       => $b->gambar,
            ]);
        @endphp

        @if ($terbaru->isNotEmpty())
            <h3 class="section-title">Koleksi terbaru</h3>
            <div class="book-grid">
                @foreach ($terbaru as $book)
                    <x-book-card :book="$book" :badge="$book['kategori']" />
                @endforeach
            </div>
        @endif
    @endauth
@endsection
