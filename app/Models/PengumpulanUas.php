<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PengumpulanUas extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengumpulan_uas';
    protected $primaryKey = 'id_pengumpulan_uas';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_uas',
        'id_mahasiswa',
        'dokumen',
        'nilai',
    ];

    // Relasi ke UAS
    public function uas()
    {
        return $this->belongsTo(Uas::class, 'id_uas', 'id_uas');
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
