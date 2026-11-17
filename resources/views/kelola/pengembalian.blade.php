@extends('layouts.perpus')

@section('title', 'Kelola Pengembalian')

@section('content')
    <h2>Kelola Pengembalian</h2>
    <p style="margin-bottom:16px">Daftar buku yang sedang dipinjam dan belum dikembalikan.</p>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>No</th><th>Anggota</th><th>Judul Buku</th><th>Tanggal Pinjam</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($items as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->anggota?->nama ?? '-' }}</td>
                        <td>{{ $p->buku?->Judul ?? '-' }}</td>
                        <td>{{ $p->tanggal_pinjam?->format('d-m-Y') ?? '-' }}</td>
                        <td>
                            @can('mencatat pengembalian buku')
                                <form method="POST" action="{{ route('kelola.pengembalian.store', $p) }}" class="inline-form">
                                    @csrf
                                    <button type="submit" class="btn btn-small">Catat Pengembalian</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Tidak ada buku yang sedang dipinjam.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
