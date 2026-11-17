@props(['book', 'badge' => null])

<article {{ $attributes->merge(['class' => 'book-card']) }}>
    <x-book-cover :book="$book" />

    <div class="book-body">
        @if ($badge)
            <span class="badge">{{ $badge }}</span>
        @endif

        {{-- [SOAL 1 - A1 SENGAJA RENTAN] {!! !!} tidak melakukan escaping -> Stored XSS --}}
        <h3 class="book-title">{!! $book['judul'] !!}</h3>

        {{-- kalau sampulnya gambar, penulis ditulis ulang sebagai teks (mudah dibaca/dicari) --}}
        @if (!empty($book['gambar']))
            <p class="author">{{ $book['penulis'] }}</p>
        @endif

        <p class="year">Terbit {{ $book['tahun_terbit'] }}</p>

        <div class="book-actions">
            {{ $slot }}
            <a href="{{ route('buku.show', $book['id']) }}" class="btn btn-ghost btn-small">Lihat detail</a>
        </div>
    </div>
</article>
