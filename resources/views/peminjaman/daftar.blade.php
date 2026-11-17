@extends('layouts.perpus')

@section('title', $judul)

@section('content')
    <h2>{{ $judul }}</h2>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>No</th><th>Judul Buku</th><th>Tanggal Pinjam</th><th>Tanggal Kembali</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($items as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->buku?->Judul ?? '-' }}</td>
                        <td>{{ $p->tanggal_pinjam?->format('d-m-Y') ?? '-' }}</td>
                        <td>{{ $p->tanggal_kembali?->format('d-m-Y') ?? '-' }}</td>
                        <td><span class="status status-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                        <td><a href="{{ route('peminjaman.show', $p->id) }}" class="btn btn-ghost btn-small">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">Belum ada data peminjaman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
