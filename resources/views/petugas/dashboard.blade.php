@extends('layouts.perpus')

@section('title', 'Dashboard Petugas')

@section('content')
    <h2>Dashboard Petugas</h2>
    <p style="margin-bottom:20px">Halo, {{ auth()->user()->name }}. Kamu masuk sebagai <strong>petugas</strong>.</p>

    <div class="cards">
        <div class="card"><div>Total Buku</div><div class="angka">{{ $stats['buku'] }}</div></div>
        <div class="card"><div>Pengajuan Menunggu</div><div class="angka">{{ $stats['diajukan'] }}</div></div>
        <div class="card"><div>Sedang Dipinjam</div><div class="angka">{{ $stats['dipinjam'] }}</div></div>
    </div>

    <a href="{{ route('kelola.peminjaman.index') }}" class="btn">Kelola Peminjaman</a>
    <a href="{{ route('kelola.pengembalian.index') }}" class="btn">Kelola Pengembalian</a>
    <a href="{{ route('kelola.buku') }}" class="btn">Kelola Buku</a>
@endsection
