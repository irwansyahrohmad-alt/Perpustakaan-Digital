<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class peminjaman extends Model
{
    use HasFactory;

    public const STATUS_DIAJUKAN     = 'diajukan';
    public const STATUS_DIPINJAM     = 'dipinjam';
    public const STATUS_DITOLAK      = 'ditolak';
    public const STATUS_DIKEMBALIKAN = 'dikembalikan';

    protected $table = 'peminjamans';
    protected $fillable = ['anggota_id', 'buku_id', 'tanggal_pinjam', 'tanggal_kembali', 'status'];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam'  => 'date',
            'tanggal_kembali' => 'date',
        ];
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}
