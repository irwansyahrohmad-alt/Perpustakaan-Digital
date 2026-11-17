@extends('layouts.perpus')

@section('title', 'Kelola Pengguna')

@section('content')
    <h2>Kelola Pengguna & Role</h2>

    <h3 class="section-title">Tambah Pengguna</h3>
    <form method="POST" action="{{ route('admin.users.store') }}" class="form-row">
        @csrf
        <input type="text" name="name" placeholder="Nama" value="{{ old('name') }}" required>
        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
        <input type="password" name="password" placeholder="Password (min. 8 karakter)" required>
        @can('kelola role pengguna')
            <select name="role" required>
                @foreach ($roles as $r)
                    <option value="{{ $r }}" @selected(old('role', 'siswa') === $r)>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
        @endcan
        <button type="submit" class="btn">Simpan</button>
    </form>

    <h3 class="section-title">Daftar Pengguna</h3>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>No</th><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @foreach ($users as $i => $u)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->roles->pluck('name')->implode(', ') ?: '-' }}</td>
                        <td>
                            @if ($u->is(auth()->user()))
                                <em>(akun kamu)</em>
                            @else
                                @can('kelola role pengguna')
                                    <form method="POST" action="{{ route('admin.users.role', $u) }}" class="inline-form">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role">
                                            @foreach ($roles as $r)
                                                <option value="{{ $r }}" @selected($u->hasRole($r))>{{ ucfirst($r) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-small">Ubah Role</button>
                                    </form>
                                @endcan
                                @can('kelola data pengguna')
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline-form"
                                          onsubmit="return confirm('Hapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-small btn-danger">Hapus</button>
                                    </form>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
