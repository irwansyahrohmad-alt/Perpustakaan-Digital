<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

// 'role' sengaja TIDAK ada di $fillable: role hanya boleh diatur lewat syncRoles()/assignRole()
// oleh mekanisme berwenang (seeder atau admin), bukan lewat form registrasi.
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Setiap user baru (termasuk dari form registrasi Breeze) otomatis menjadi "siswa".
     * Admin dapat mengubahnya lewat menu Kelola Pengguna.
     */
    protected static function booted(): void
    {
        static::created(function (User $user): void {
            $siswaAda = Role::where('name', 'siswa')->where('guard_name', 'web')->exists();

            if ($siswaAda && ! $user->hasAnyRole(['admin', 'petugas', 'siswa'])) {
                $user->assignRole('siswa');
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // password otomatis di-hash saat disimpan
        ];
    }

    public function anggota(): HasOne
    {
        return $this->hasOne(Anggota::class);
    }

    /**
     * Data anggota perpustakaan milik user ini (dibuat otomatis saat pertama kali dibutuhkan),
     * karena tabel peminjamans terhubung ke anggotas, bukan langsung ke users.
     */
    public function ensureAnggota(): Anggota
    {
        return Anggota::firstOrCreate(
            ['user_id' => $this->id],
            ['nama' => $this->name, 'email' => $this->email, 'no_telepon' => '-']
        );
    }
}
