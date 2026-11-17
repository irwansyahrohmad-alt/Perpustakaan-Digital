<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menghubungkan akun login (users) dengan data anggota perpustakaan
        Schema::table('anggotas', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->unique('user_id');
        });

        // Alur peminjaman: diajukan -> dipinjam / ditolak -> dikembalikan
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->string('status', 20)->default('dipinjam')->after('tanggal_kembali');
        });

        // Data lama: yang sudah punya tanggal_kembali dianggap sudah dikembalikan
        DB::table('peminjamans')->whereNotNull('tanggal_kembali')->update(['status' => 'dikembalikan']);
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('anggotas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
