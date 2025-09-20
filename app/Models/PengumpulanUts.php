<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PengumpulanUts extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengumpulan_uts';
    protected $primaryKey = 'id_pengumpulan_uts';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_uts',
        'id_mahasiswa',
        'dokumen',
        'nilai',
    ];

    // Relasi ke UTS
    public function uts()
    {
        return $this->belongsTo(Uts::class, 'id_uts', 'id_uts');
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
