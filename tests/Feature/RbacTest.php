<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pengujian otomatis untuk skenario tabel soal no. 5.
 * Jalankan: php artisan test --filter=RbacTest
 */
class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function akun(string $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles($role);

        return $user;
    }

    private function dataBuku(): array
    {
        return ['kategori' => 'Fiksi', 'judul' => 'Laskar Pelangi', 'penulis' => 'Andrea Hirata', 'tahun_terbit' => 2005];
    }

    private function buatPeminjaman(User $pemilik, string $status): Peminjaman
    {
        $kategori = Kategori::create(['nama' => 'Umum']);
        $buku = Buku::create(['kategori_id' => $kategori->id, 'Judul' => 'Buku Uji', 'penulis' => 'Penulis', 'tahun_terbit' => '2020']);

        return Peminjaman::create([
            'anggota_id'     => $pemilik->ensureAnggota()->id,
            'buku_id'        => $buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'status'         => $status,
        ]);
    }

    // ---- Soal 1 & 2: autentikasi, session, middleware ----

    public function test_registrasi_membuat_siswa_dengan_password_ter_hash_dan_role_tidak_bisa_dipilih(): void
    {
        $this->post('/register', [
            'name' => 'Murid Baru', 'email' => 'baru@test.id',
            'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
            'role' => 'admin', // coba menyelundupkan role
        ]);

        $user = User::where('email', 'baru@test.id')->first();
        $this->assertNotNull($user);
        $this->assertNotSame('rahasia123', $user->password);
        $this->assertTrue($user->hasRole('siswa'));
        $this->assertFalse($user->hasRole('admin'));
    }

    public function test_login_salah_ditolak_dan_user_login_tidak_bisa_buka_halaman_login(): void
    {
        $user = $this->akun('siswa');

        $this->post('/login', ['email' => $user->email, 'password' => 'salah'])->assertSessionHasErrors();
        $this->assertGuest();

        $this->actingAs($user)->get('/login')->assertRedirect(); // middleware guest
    }

    // ---- Soal 5, skenario 1-15 ----

    public function test_01_belum_login_dialihkan_ke_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
    }

    public function test_02_admin_buka_dashboard_admin(): void
    {
        $this->actingAs($this->akun('admin'))->get('/admin/dashboard')->assertOk();
    }

    public function test_03_admin_tambah_ubah_hapus_buku(): void
    {
        $admin = $this->akun('admin');

        $id = $this->actingAs($admin)->postJson('/api/buku', $this->dataBuku())
            ->assertCreated()->json('data.id');

        $this->actingAs($admin)->putJson("/api/buku/$id", array_merge($this->dataBuku(), ['judul' => 'Edensor']))
            ->assertOk();

        $this->actingAs($admin)->deleteJson("/api/buku/$id")->assertOk();
    }

    public function test_04_admin_kelola_akun_dan_role(): void
    {
        $admin = $this->akun('admin');

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Petugas Baru', 'email' => 'pb@test.id', 'password' => 'rahasia123', 'role' => 'petugas',
        ])->assertRedirect();

        $baru = User::where('email', 'pb@test.id')->firstOrFail();
        $this->assertTrue($baru->hasRole('petugas'));

        $this->actingAs($admin)->patch("/admin/users/{$baru->id}/role", ['role' => 'siswa'])->assertRedirect();
        $this->assertTrue($baru->fresh()->hasRole('siswa'));
    }

    public function test_05_admin_akses_peminjaman_pengembalian_laporan(): void
    {
        $admin = $this->akun('admin');

        $this->actingAs($admin)->get('/kelola/peminjaman')->assertOk();
        $this->actingAs($admin)->get('/kelola/pengembalian')->assertOk();
        $this->actingAs($admin)->get('/laporan')->assertOk();
    }

    public function test_06_petugas_buka_dashboard_petugas(): void
    {
        $this->actingAs($this->akun('petugas'))->get('/petugas/dashboard')->assertOk();
    }

    public function test_07_petugas_kelola_buku(): void
    {
        $petugas = $this->akun('petugas');

        $this->actingAs($petugas)->get('/kelola/buku')->assertOk();
        $this->actingAs($petugas)->postJson('/api/buku', $this->dataBuku())->assertCreated();
    }

    public function test_08_petugas_proses_peminjaman_dan_pengembalian(): void
    {
        $petugas = $this->akun('petugas');
        $pinjam = $this->buatPeminjaman($this->akun('siswa'), 'diajukan');

        $this->actingAs($petugas)->post("/kelola/peminjaman/{$pinjam->id}/setujui")->assertRedirect();
        $this->assertSame('dipinjam', $pinjam->fresh()->status);

        $this->actingAs($petugas)->post("/kelola/pengembalian/{$pinjam->id}")->assertRedirect();
        $this->assertSame('dikembalikan', $pinjam->fresh()->status);
    }

    public function test_09_petugas_tidak_boleh_kelola_akun_atau_role(): void
    {
        $petugas = $this->akun('petugas');
        $target = $this->akun('siswa');

        $this->actingAs($petugas)->get('/admin/users')->assertForbidden();
        $this->actingAs($petugas)->patch("/admin/users/{$target->id}/role", ['role' => 'admin'])->assertForbidden();
        $this->assertTrue($target->fresh()->hasRole('siswa'));
    }

    public function test_10_siswa_lihat_daftar_dan_detail_buku(): void
    {
        $siswa = $this->akun('siswa');
        $kategori = Kategori::create(['nama' => 'Umum']);
        $buku = Buku::create(['kategori_id' => $kategori->id, 'Judul' => 'Buku Uji', 'penulis' => 'P', 'tahun_terbit' => '2020']);

        $this->actingAs($siswa)->get('/buku')->assertOk();
        $this->actingAs($siswa)->get("/buku/{$buku->id}")->assertOk();
    }

    public function test_11_siswa_lihat_dan_ubah_profil_sendiri(): void
    {
        $siswa = $this->akun('siswa');

        $this->actingAs($siswa)->get('/profile')->assertOk();
        $this->actingAs($siswa)->patch('/profile', ['name' => 'Nama Baru', 'email' => $siswa->email])
            ->assertSessionHasNoErrors();
        $this->assertSame('Nama Baru', $siswa->fresh()->name);
    }

    public function test_12_siswa_lihat_riwayat_sendiri(): void
    {
        $siswa = $this->akun('siswa');
        $this->buatPeminjaman($siswa, 'dipinjam');

        $this->actingAs($siswa)->get('/riwayat-peminjaman')->assertOk()->assertSee('Buku Uji');
        $this->actingAs($siswa)->get("/riwayat-peminjaman/{$siswa->id}")->assertOk();
    }

    public function test_13_siswa_tidak_boleh_dashboard_admin_atau_kelola_buku(): void
    {
        $siswa = $this->akun('siswa');

        $this->actingAs($siswa)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($siswa)->get('/kelola/buku')->assertForbidden();
        $this->actingAs($siswa)->postJson('/api/buku', $this->dataBuku())->assertForbidden();
    }

    public function test_14_siswa_tidak_boleh_lihat_riwayat_siswa_lain(): void
    {
        $siswa = $this->akun('siswa');
        $lain = $this->akun('siswa');
        $this->buatPeminjaman($lain, 'dipinjam');

        $this->actingAs($siswa)->get("/riwayat-peminjaman/{$lain->id}")->assertForbidden();
    }

    public function test_15_setelah_logout_harus_login_lagi(): void
    {
        $siswa = $this->akun('siswa');

        $this->actingAs($siswa)->post('/logout');
        $this->assertGuest();

        $this->get('/buku')->assertRedirect(route('login'));
    }

    public function test_api_tulis_buku_tanpa_login_ditolak(): void
    {
        $this->postJson('/api/buku', $this->dataBuku())->assertUnauthorized();
    }
}
