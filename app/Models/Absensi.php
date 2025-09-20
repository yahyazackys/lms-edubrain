<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Absensi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'absensi';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_sesi_absensi',
        'id_mahasiswa',
        'is_hadir',
    ];

    protected $casts = [
        'is_hadir' => 'boolean',
    ];

    // Relasi ke Sesi Absensi
    public function sesiAbsensi()
    {
        return $this->belongsTo(SesiAbsensi::class, 'id_sesi_absensi', 'id_sesi_absensi');
    }

    // Relasi ke Mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    // Helper method untuk cek status kehadiran
    public function getStatusKehadiranAttribute()
    {
        return $this->is_hadir ? 'Hadir' : 'Tidak Hadir';
    }

    // Helper method untuk cek apakah terlambat absen
    public function isTerlambat()
    {
        return $this->created_at > $this->sesiAbsensi->batas_akhir_absensi;
    }
}
