<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PeminjamanKelolaController extends Controller
{
    public function index(): View
    {
        $items = Peminjaman::with(['anggota', 'buku'])->orderByDesc('id')->get();

        return view('kelola.peminjaman', compact('items'));
    }

    public function setujui(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== Peminjaman::STATUS_DIAJUKAN) {
            return back()->with('error', 'Hanya pengajuan yang bisa disetujui.');
        }

        $peminjaman->update([
            'status'         => Peminjaman::STATUS_DIPINJAM,
            'tanggal_pinjam' => now()->toDateString(),
        ]);

        return back()->with('success', 'Peminjaman disetujui.');
    }

    public function tolak(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== Peminjaman::STATUS_DIAJUKAN) {
            return back()->with('error', 'Hanya pengajuan yang bisa ditolak.');
        }

        $peminjaman->update(['status' => Peminjaman::STATUS_DITOLAK]);

        return back()->with('success', 'Pengajuan ditolak.');
    }

    public function pengembalian(): View
    {
        $items = Peminjaman::with(['anggota', 'buku'])
            ->where('status', Peminjaman::STATUS_DIPINJAM)
            ->orderBy('id')
            ->get();

        return view('kelola.pengembalian', compact('items'));
    }

    public function kembalikan(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== Peminjaman::STATUS_DIPINJAM) {
            return back()->with('error', 'Buku ini tidak sedang dipinjam.');
        }

        $peminjaman->update([
            'status'          => Peminjaman::STATUS_DIKEMBALIKAN,
            'tanggal_kembali' => now()->toDateString(),
        ]);

        return back()->with('success', 'Pengembalian buku dicatat.');
    }
}
