@extends('layouts.perpus')

@section('title', 'Daftar Buku')

@section('content')
    @php $kategoris = collect($books)->pluck('kategori')->unique()->sort()->values(); @endphp

    <div class="page-head">
        <h2>Daftar Buku</h2>
        <span class="count"><span id="jumlah">{{ count($books) }}</span> buku</span>
    </div>

    @can('kelola data buku')
        <p style="margin-bottom:20px"><a href="{{ route('kelola.buku') }}" class="btn">Kelola data buku</a></p>
    @endcan

    @if (count($books) === 0)
        <p>Belum ada data buku.</p>
    @else
        <div class="toolbar">
            <input type="search" id="cari" placeholder="Cari judul atau penulis" aria-label="Cari buku">
            <div class="chips">
                <button type="button" class="chip active" data-kat="">Semua</button>
                @foreach ($kategoris as $k)
                    <button type="button" class="chip" data-kat="{{ $k }}">{{ $k }}</button>
                @endforeach
            </div>
        </div>

        <div class="book-grid" id="grid">
            @foreach ($books as $book)
                <x-book-card :book="$book" :badge="$book['kategori']"
                             data-kat="{{ $book['kategori'] }}"
                             data-cari="{{ mb_strtolower($book['judul'].' '.$book['penulis']) }}">
                    @can('mengajukan peminjaman buku')
                        <form method="POST" action="{{ route('peminjaman.ajukan', $book['id']) }}" class="inline-form">
                            @csrf
                            <button type="submit" class="btn btn-small">Ajukan pinjam</button>
                        </form>
                    @endcan
                </x-book-card>
            @endforeach
        </div>

        <p id="kosong" class="kosong" hidden>Tidak ada buku yang cocok. Coba kata kunci atau kategori lain.</p>

        <script>
        (function () {
            const input  = document.getElementById('cari');
            const chips  = Array.from(document.querySelectorAll('.chip'));
            const cards  = Array.from(document.querySelectorAll('#grid .book-card'));
            const jumlah = document.getElementById('jumlah');
            const kosong = document.getElementById('kosong');
            let kategori = '';

            function terapkan() {
                const q = input.value.trim().toLowerCase();
                let n = 0;
                cards.forEach(function (c) {
                    const cocok = (!kategori || c.dataset.kat === kategori) && (!q || c.dataset.cari.includes(q));
                    c.hidden = !cocok;
                    if (cocok) n++;
                });
                jumlah.textContent = n;
                kosong.hidden = n !== 0;
            }

            input.addEventListener('input', terapkan);
            chips.forEach(function (ch) {
                ch.addEventListener('click', function () {
                    kategori = ch.dataset.kat;
                    chips.forEach(function (x) { x.classList.toggle('active', x === ch); });
                    terapkan();
                });
            });
        })();
        </script>
    @endif
@endsection
