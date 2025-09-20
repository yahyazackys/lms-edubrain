<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PengumpulanTugas extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengumpulan_tugas';
    protected $primaryKey = 'id_pengumpulan_tugas';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_tugas',
        'id_mahasiswa',
        'dokumen',
        'nilai',
    ];

    // Relasi ke Tugas
    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'id_tugas', 'id_tugas');
    }

    // Relasi ke Mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    // Helper method untuk cek apakah sudah dinilai
    public function isDinilai()
    {
        return !is_null($this->nilai);
    }
}
