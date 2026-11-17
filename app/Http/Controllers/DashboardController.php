<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Tujuan setelah login (route 'dashboard' bawaan Breeze) sesuai role/permission. */
    public function redirect(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->can('akses dashboard admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->can('akses dashboard petugas')) {
            return redirect()->route('petugas.dashboard');
        }

        // Siswa tidak punya dashboard administrasi
        return redirect()->route('buku.index');
    }

    public function admin(): View
    {
        return view('admin.dashboard', [
            'stats' => $this->stats() + ['pengguna' => User::count()],
        ]);
    }

    public function petugas(): View
    {
        return view('petugas.dashboard', ['stats' => $this->stats()]);
    }

    private function stats(): array
    {
        return [
            'buku'     => Buku::count(),
            'diajukan' => Peminjaman::where('status', Peminjaman::STATUS_DIAJUKAN)->count(),
            'dipinjam' => Peminjaman::where('status', Peminjaman::STATUS_DIPINJAM)->count(),
        ];
    }
}
