@props(['href', 'icon', 'active' => false])

@php
    // ikon garis sederhana (24x24); satu path per ikon
    $icons = [
        'home'      => 'M3 11l9-8 9 8M5 10v10h5v-6h4v6h5V10',
        'book'      => 'M4 4h11a3 3 0 0 1 3 3v13H7a3 3 0 0 1-3-3V4z M4 17a3 3 0 0 1 3-3h11',
        'dashboard' => 'M3 3h8v8H3z M13 3h8v5h-8z M13 10h8v11h-8z M3 13h8v8H3z',
        'edit'      => 'M4 20h4l11-11-4-4L4 16v4z M13 7l4 4',
        'swap'      => 'M4 8h14l-4-4 M20 16H6l4 4',
        'return'    => 'M9 14L4 9l5-5 M4 9h10a6 6 0 0 1 0 12h-3',
        'chart'     => 'M4 20V4 M4 20h16 M8 16v-5 M12 16V8 M16 16v-3',
        'users'     => 'M9 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6 M17 11a3 3 0 1 0-1-5.8 M18 14c2 .6 3 2.6 3 5',
        'clock'     => 'M12 7v5l3 2 M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z',
        'history'   => 'M3 12a9 9 0 1 0 3-6.7 M3 4v5h5 M12 8v4l3 2',
        'user'      => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z M4 21c0-4 3.6-7 8-7s8 3 8 7',
        'login'     => 'M15 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4 M10 17l5-5-5-5 M15 12H3',
        'userplus'  => 'M10 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z M2 21c0-4 3.6-7 8-7 M19 8v6 M16 11h6',
    ];
@endphp

<a href="{{ $href }}" title="{{ trim(strip_tags((string) $slot)) }}" @class(['nav-item', 'active' => $active]) @if ($active) aria-current="page" @endif>
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons[$icon] ?? $icons['book'] }}"/></svg>
    <span class="nav-label">{{ $slot }}</span>
</a>
