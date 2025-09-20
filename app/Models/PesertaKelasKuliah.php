<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PesertaKelasKuliah extends Model
{
    use HasUuids;

    protected $table = 'peserta_kelas_kuliah';
    protected $primaryKey = 'id_peserta';

    protected $fillable = [
        'id_peserta',
        'id_kelas_kuliah',
        'id_mata_kuliah',
        'id_registrasi_mahasiswa',
        'status_mata_kuliah'
    ];

    /**
     * Relasi ke Kelas Kuliah
     */
    public function kelasKuliah(): BelongsTo
    {
        return $this->belongsTo(KelasKuliah::class, 'id_kelas_kuliah', 'id_kelas_kuliah');
    }

    /**
     * Relasi ke Mata Kuliah
     */
    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'id_mata_kuliah', 'id_mata_kuliah');
    }

    /**
     * Relasi ke Registrasi Mahasiswa
     */
    public function registrasiMahasiswa(): BelongsTo
    {
        return $this->belongsTo(RegistrasiMahasiswa::class, 'id_registrasi_mahasiswa', 'id_registrasi_mahasiswa');
    }

    /**
     * Relasi ke Nilai Perkuliahan
     */
    public function nilaiPerkuliahan(): HasMany
    {
        return $this->hasMany(NilaiPerkuliahan::class, 'id_peserta', 'id_peserta');
    }

    /**
     * Scope untuk filter berdasarkan status mata kuliah
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_mata_kuliah', $status);
    }

    /**
     * Scope untuk filter berdasarkan registrasi mahasiswa
     */
    public function scopeByRegistrasi($query, $registrasiId)
    {
        return $query->where('id_registrasi_mahasiswa', $registrasiId);
    }
}
