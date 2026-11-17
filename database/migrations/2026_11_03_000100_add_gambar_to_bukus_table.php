<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Path gambar relatif ke folder public/ (mis. images/buku/uzumaki.svg) atau URL lengkap
        Schema::table('bukus', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('tahun_terbit');
        });
    }

    public function down(): void
    {
        Schema::table('bukus', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
    }
};
