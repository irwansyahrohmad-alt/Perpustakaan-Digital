<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') | Rhodes Library</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700;9..144,800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/perpus.css') }}">
</head>
<body>
    <script>
        // ingat status sidebar (desktop) sebelum halaman digambar supaya tidak berkedip
        try {
            if (localStorage.getItem('sb') === '1' && window.matchMedia('(min-width: 901px)').matches) {
                document.body.classList.add('sidebar-collapsed');
            }
        } catch (e) {}
    </script>

    <aside class="sidebar" id="sidebar" aria-label="Menu utama">
        <div class="side-head">
            <a href="{{ route('home') }}" class="brand" title="Rhodes Library">
                <img src="{{ asset('images/R_Book_by_Dick_Blacker-removebg-preview.png') }}" alt="logo" class="logo">
                <span class="brand-text">
                    <strong>Rhodes Library</strong>
                    <small>Temukan buku favoritmu di sini</small>
                </span>
            </a>
        </div>

        <nav class="side-nav">
            <x-nav-item :href="route('home')" icon="home" :active="request()->routeIs('home')">Beranda</x-nav-item>

            @auth
                @can('lihat daftar buku')
                    <x-nav-item :href="route('buku.index')" icon="book" :active="request()->routeIs('buku.*')">Daftar Buku</x-nav-item>
                @endcan

                @role('admin')
                    <x-nav-item :href="route('admin.dashboard')" icon="dashboard" :active="request()->routeIs('admin.dashboard')">Dashboard Admin</x-nav-item>
                @endrole
                @role('petugas')
                    <x-nav-item :href="route('petugas.dashboard')" icon="dashboard" :active="request()->routeIs('petugas.dashboard')">Dashboard Petugas</x-nav-item>
                @endrole

                @canany(['kelola data buku', 'kelola peminjaman', 'kelola pengembalian', 'lihat laporan perpustakaan', 'kelola data pengguna'])
                    <p class="nav-group">Operasional</p>
                    @can('kelola data buku')
                        <x-nav-item :href="route('kelola.buku')" icon="edit" :active="request()->routeIs('kelola.buku')">Kelola Buku</x-nav-item>
                    @endcan
                    @can('kelola peminjaman')
                        <x-nav-item :href="route('kelola.peminjaman.index')" icon="swap" :active="request()->routeIs('kelola.peminjaman.*')">Peminjaman</x-nav-item>
                    @endcan
                    @can('kelola pengembalian')
                        <x-nav-item :href="route('kelola.pengembalian.index')" icon="return" :active="request()->routeIs('kelola.pengembalian.*')">Pengembalian</x-nav-item>
                    @endcan
                    @can('lihat laporan perpustakaan')
                        <x-nav-item :href="route('laporan.index')" icon="chart" :active="request()->routeIs('laporan.*')">Laporan</x-nav-item>
                    @endcan
                    @can('kelola data pengguna')
                        <x-nav-item :href="route('admin.users.index')" icon="users" :active="request()->routeIs('admin.users.*')">Pengguna</x-nav-item>
                    @endcan
                @endcanany

                @canany(['melihat status peminjaman sendiri', 'melihat riwayat peminjaman sendiri', 'lihat profil sendiri'])
                    <p class="nav-group">Akun saya</p>
                    @can('melihat status peminjaman sendiri')
                        <x-nav-item :href="route('peminjaman.saya')" icon="clock" :active="request()->routeIs('peminjaman.saya')">Peminjaman Saya</x-nav-item>
                    @endcan
                    @can('melihat riwayat peminjaman sendiri')
                        <x-nav-item :href="route('riwayat.index')" icon="history" :active="request()->routeIs('riwayat.*')">Riwayat</x-nav-item>
                    @endcan
                    @can('lihat profil sendiri')
                        <x-nav-item :href="route('profile.edit')" icon="user" :active="request()->routeIs('profile.*')">Profil</x-nav-item>
                    @endcan
                @endcanany
            @else
                <x-nav-item :href="route('login')" icon="login" :active="request()->routeIs('login')">Login</x-nav-item>
                @if (Route::has('register'))
                    <x-nav-item :href="route('register')" icon="userplus" :active="request()->routeIs('register')">Registrasi</x-nav-item>
                @endif
            @endauth
        </nav>

        @auth
            <div class="side-foot">
                <div class="me">
                    <span class="avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="me-text">
                        <span class="me-name">{{ auth()->user()->name }}</span>
                        <span class="role-tag">{{ auth()->user()->getRoleNames()->first() ?? 'tanpa role' }}</span>
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn" title="Logout">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 4H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h4 M16 17l5-5-5-5 M21 12H9"/></svg>
                        <span class="logout-label">Logout</span>
                    </button>
                </form>
            </div>
        @endauth
    </aside>

    <div class="scrim" id="scrim"></div>

    <div class="main-wrap">
        <header class="topline">
            <button type="button" class="menu-btn" id="sidebar-toggle" aria-label="Buka atau tutup menu" aria-controls="sidebar">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16 M4 12h16 M4 18h16"/></svg>
            </button>
            <span class="topline-title">@yield('title', 'Beranda')</span>
        </header>

        <main class="container">
            @if (session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="site-footer">
            <p>&copy; {{ date('Y') }} Rhodes Library - [Erwan] ([25-022])</p>
        </footer>
    </div>

    <script>
        (function () {
            var body  = document.body;
            var btn   = document.getElementById('sidebar-toggle');
            var scrim = document.getElementById('scrim');
            var hp    = window.matchMedia('(max-width: 900px)');

            btn.addEventListener('click', function () {
                if (hp.matches) {
                    body.classList.toggle('sidebar-open');
                } else {
                    var tutup = body.classList.toggle('sidebar-collapsed');
                    try { localStorage.setItem('sb', tutup ? '1' : '0'); } catch (e) {}
                }
            });
            scrim.addEventListener('click', function () { body.classList.remove('sidebar-open'); });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') body.classList.remove('sidebar-open');
            });
        })();
    </script>
</body>
</html>
