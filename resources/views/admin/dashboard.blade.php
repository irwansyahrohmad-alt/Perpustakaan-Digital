@extends('layouts.perpus')

@section('title', 'Dashboard Admin')

@section('content')
    <h2>Dashboard Admin</h2>
    <p style="margin-bottom:20px">Halo, {{ auth()->user()->name }}. Kamu masuk sebagai <strong>admin</strong>.</p>

    <div class="cards">
        <div class="card"><div>Total Buku</div><div class="angka">{{ $stats['buku'] }}</div></div>
        <div class="card"><div>Total Pengguna</div><div class="angka">{{ $stats['pengguna'] }}</div></div>
        <div class="card"><div>Pengajuan Menunggu</div><div class="angka">{{ $stats['diajukan'] }}</div></div>
        <div class="card"><div>Sedang Dipinjam</div><div class="angka">{{ $stats['dipinjam'] }}</div></div>
    </div>

    <a href="{{ route('admin.users.index') }}" class="btn">Kelola Pengguna & Role</a>
    <a href="{{ route('kelola.buku') }}" class="btn">Kelola Buku</a>
    <a href="{{ route('laporan.index') }}" class="btn">Laporan</a>
@endsection
