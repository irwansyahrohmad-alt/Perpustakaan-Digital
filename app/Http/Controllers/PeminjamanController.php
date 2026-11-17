<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    /** Mengajukan peminjaman atas nama user yang sedang login (bukan user lain). */
    public function ajukan(Request $request, Buku $buku): RedirectResponse
    {
        $anggota = $request->user()->ensureAnggota();

        $masihAktif = Peminjaman::where('anggota_id', $anggota->id)
            ->where('buku_id', $buku->id)
            ->whereIn('status', [Peminjaman::STATUS_DIAJUKAN, Peminjaman::STATUS_DIPINJAM])
            ->exists();

        if ($masihAktif) {
            return back()->with('error', 'Buku ini masih dalam pengajuan atau sedang kamu pinjam.');
        }

        Peminjaman::create([
            'anggota_id'     => $anggota->id,
            'buku_id'        => $buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'status'         => Peminjaman::STATUS_DIAJUKAN,
        ]);

        return back()->with('success', 'Pengajuan peminjaman dikirim. Tunggu persetujuan petugas.');
    }

    /**
     * [SOAL 1 - A4 SENGAJA RENTAN] IDOR: data diambil hanya dengan id dari URL,
     * tanpa memeriksa apakah peminjaman ini milik user yang sedang login.
     */
    public function show($id): View
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->load(['anggota', 'buku']);

        return view('peminjaman.show', compact('peminjaman'));
    }

    /** Status peminjaman milik sendiri yang masih berjalan. */
    public function saya(Request $request): View
    {
        return $this->daftar(
            $request->user(),
            'Status Peminjaman Saya',
            [Peminjaman::STATUS_DIAJUKAN, Peminjaman::STATUS_DIPINJAM]
        );
    }

    /** Riwayat peminjaman milik sendiri. */
    public function riwayat(Request $request): View
    {
        return $this->daftar($request->user(), 'Riwayat Peminjaman Saya');
    }

    /** Riwayat milik user tertentu: hanya pemilik, atau admin/petugas. */
    public function riwayatUser(User $user): View
    {
        Gate::authorize('lihat-riwayat', $user);

        return $this->daftar($user, 'Riwayat Peminjaman: '.$user->name);
    }

    private function daftar(User $user, string $judul, ?array $status = null): View
    {
        $items = Peminjaman::with('buku')
            ->whereHas('anggota', fn ($q) => $q->where('user_id', $user->id))
            ->when($status, fn ($q) => $q->whereIn('status', $status))
            ->orderByDesc('id')
            ->get();

        return view('peminjaman.daftar', compact('judul', 'items'));
    }
}
