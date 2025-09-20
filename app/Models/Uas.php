<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Uas extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'uas';
    protected $primaryKey = 'id_uas';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kelas_kuliah',
        'judul',
        'deskripsi',
        'dokumen',
        'batas_akhir_pengumpulan',
    ];

    protected $casts = [
        'batas_akhir_pengumpulan' => 'datetime',
    ];

    // Relasi ke Kelas Kuliah
    public function kelasKuliah()
    {
        return $this->belongsTo(KelasKuliah::class, 'id_kelas_kuliah', 'id_kelas_kuliah');
    }

    // Relasi ke Pengumpulan UAS
    public function pengumpulanUas()
    {
        return $this->hasMany(PengumpulanUas::class, 'id_uas', 'id_uas');
    }

    // Helper method untuk cek apakah sudah deadline
    public function isExpired()
    {
        return $this->batas_akhir_pengumpulan < now();
    }
}
