<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'semester';
    protected $primaryKey = 'id_semester';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_semester',
        'nama_semester',
        'tipe',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    // Relasi ke Pembimbing Akademik
    public function pembimbingAkademik(): HasMany
    {
        return $this->hasMany(PembimbingAkademik::class, 'id_semester');
    }

    // Relasi ke Kelas Kuliah
    public function kelasKuliah()
    {
        return $this->hasMany(KelasKuliah::class, 'id_semester', 'id_semester');
    }

    // Relasi ke Registrasi Mahasiswa
    public function registrasi()
    {
        return $this->hasMany(RegistrasiMahasiswa::class, 'id_semester', 'id_semester');
    }
}
