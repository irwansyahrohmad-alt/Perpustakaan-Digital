@props(['book'])

@php
    // warna sampul cadangan (kalau buku belum punya gambar) mengikuti kategori
    $warna = [
        'Manga'            => '#7b1e2b',
        'Light Novel'      => '#b4532a',
        'Sastra Indonesia' => '#1f5f5b',
        'Fiksi Ilmiah'     => '#2b3a6b',
        'Sastra Dunia'     => '#5b2a6b',
    ][$book['kategori']] ?? '#5f1220';

    // gambar bisa berupa path di folder public/ atau URL lengkap
    $gambar = $book['gambar'] ?? null;
    $src = $gambar
        ? (\Illuminate\Support\Str::startsWith($gambar, ['http://', 'https://']) ? $gambar : asset($gambar))
        : null;
@endphp

@if ($src)
    <div {{ $attributes->merge(['class' => 'cover-img']) }}>
        <img src="{{ $src }}" alt="Sampul buku {{ $book['judul'] }}" loading="lazy">
    </div>
@else
    <div {{ $attributes->merge(['class' => 'cover']) }} style="--c: {{ $warna }}">
        <span class="cover-title">{{ $book['judul'] }}</span>
        <span class="cover-author">{{ $book['penulis'] }}</span>
    </div>
@endif
