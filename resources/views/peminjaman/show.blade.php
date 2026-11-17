@extends('layouts.perpus')

@section('title', 'Detail Peminjaman')

@section('content')
    <h2>Detail Peminjaman #{{ $peminjaman->id }}</h2>

    <div class="table-wrap">
        <table class="table">
            <tr><th>ID peminjaman</th><td>{{ $peminjaman->id }}</td></tr>
            <tr><th>Nama anggota</th><td>{{ $peminjaman->anggota?->nama ?? '-' }}</td></tr>
            <tr><th>Email anggota</th><td>{{ $peminjaman->anggota?->email ?? '-' }}</td></tr>
            <tr><th>Judul buku</th><td>{{ $peminjaman->buku?->Judul ?? '-' }}</td></tr>
            <tr><th>Tanggal pinjam</th><td>{{ $peminjaman->tanggal_pinjam?->format('d-m-Y') ?? '-' }}</td></tr>
            <tr><th>Tanggal kembali</th><td>{{ $peminjaman->tanggal_kembali?->format('d-m-Y') ?? '-' }}</td></tr>
            <tr><th>Status</th><td><span class="status status-{{ $peminjaman->status }}">{{ ucfirst($peminjaman->status) }}</span></td></tr>
        </table>
    </div>

    <p><a href="{{ route('peminjaman.saya') }}" class="btn btn-ghost">Kembali</a></p>
@endsection
