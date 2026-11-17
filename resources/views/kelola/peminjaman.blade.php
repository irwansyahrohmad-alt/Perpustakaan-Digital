@extends('layouts.perpus')

@section('title', 'Kelola Peminjaman')

@section('content')
    <h2>Kelola Peminjaman</h2>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>No</th><th>Anggota</th><th>Judul Buku</th><th>Tanggal Pinjam</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($items as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->anggota?->nama ?? '-' }}</td>
                        <td>{{ $p->buku?->Judul ?? '-' }}</td>
                        <td>{{ $p->tanggal_pinjam?->format('d-m-Y') ?? '-' }}</td>
                        <td><span class="status status-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                        <td>
                            @if ($p->status === 'diajukan')
                                @can('memproses persetujuan peminjaman')
                                    <form method="POST" action="{{ route('kelola.peminjaman.setujui', $p) }}" class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-small">Setujui</button>
                                    </form>
                                    <form method="POST" action="{{ route('kelola.peminjaman.tolak', $p) }}" class="inline-form">
                                        @csrf
                                        <button type="submit" class="btn btn-small btn-danger">Tolak</button>
                                    </form>
                                @endcan
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Belum ada data peminjaman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
