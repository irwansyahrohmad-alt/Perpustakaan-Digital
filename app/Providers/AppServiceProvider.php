<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
public function boot(): void
{
    // Data pribadi (riwayat peminjaman): boleh dilihat oleh pemiliknya sendiri,
    // atau oleh staf yang memang berhak mengelola peminjaman (admin/petugas).
    // Siswa lain TIDAK boleh, walaupun punya permission "melihat riwayat peminjaman sendiri".
    Gate::define('lihat-riwayat', function (User $login, User $pemilik): bool {
        return $login->is($pemilik) || $login->can('kelola peminjaman');
    });

    // Paksa HTTPS di production supaya URL CSS/JS tidak diblokir browser
    if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }
  }
}
