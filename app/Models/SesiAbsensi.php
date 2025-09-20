<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SesiAbsensi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sesi_absensi';
    protected $primaryKey = 'id_sesi_absensi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kelas_kuliah',
        'topik',
        'batas_akhir_absensi',
        'status',
    ];

    protected $casts = [
        'batas_akhir_absensi' => 'datetime',
    ];

    // Relasi ke Kelas Kuliah
    public function kelasKuliah()
    {
        return $this->belongsTo(KelasKuliah::class, 'id_kelas_kuliah', 'id_kelas_kuliah');
    }

    // Relasi ke Absensi
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'id_sesi_absensi', 'id_sesi_absensi');
    }

    // Helper method untuk cek apakah sesi masih dibuka
    public function isDibuka()
    {
        return $this->status === 'dibuka' && $this->batas_akhir_absensi > now();
    }

    // Helper method untuk cek apakah sudah expired
    public function isExpired()
    {
        return $this->batas_akhir_absensi < now();
    }

    // Helper method untuk cek berapa mahasiswa yang sudah absen
    public function getTotalHadirAttribute()
    {
        return $this->absensi()->where('is_hadir', true)->count();
    }

    // Helper method untuk cek berapa mahasiswa yang tidak hadir
    public function getTotalTidakHadirAttribute()
    {
        return $this->absensi()->where('is_hadir', false)->count();
    }

    // Helper method untuk total mahasiswa dalam kelas
    public function getTotalMahasiswaAttribute()
    {
        return $this->kelasKuliah->pesertaKelasKuliah()->count();
    }
}
