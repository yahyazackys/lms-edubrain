<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RegistrasiMahasiswa extends Model
{
    use HasUuids;

    protected $table = 'registrasi_mahasiswa';
    protected $primaryKey = 'id_registrasi_mahasiswa';

    protected $fillable = [
        'id_registrasi_mahasiswa',
        'id_mahasiswa',
        'id_semester',
        'status_krs',
        'id_pembimbing_akademik',
        'tanggal_submit',
        'tanggal_approval',
        'alasan_reject'
    ];

    protected $casts = [
        'tanggal_submit' => 'datetime',
        'tanggal_approval' => 'datetime'
    ];

    /**
     * Relasi ke Mahasiswa
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    /**
     * Relasi ke Semester
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'id_semester', 'id_semester');
    }

    /**
     * Relasi ke Pembimbing Akademik
     */
    public function pembimbingAkademik(): BelongsTo
    {
        return $this->belongsTo(PembimbingAkademik::class, 'id_pembimbing_akademik', 'id_pembimbing_akademik');
    }

    /**
     * Relasi ke Peserta Kelas Kuliah (Detail KRS)
     */
    public function pesertaKelasKuliah(): HasMany
    {
        return $this->hasMany(PesertaKelasKuliah::class, 'id_registrasi_mahasiswa', 'id_registrasi_mahasiswa');
    }

    public function pesertaBimbingan(): HasMany
    {
        return $this->hasMany(PesertaBimbingan::class, 'id_registrasi_mahasiswa', 'id_registrasi_mahasiswa');
    }

    /**
     * Scope untuk filter berdasarkan semester
     */
    public function scopeBySemester($query, $semesterId)
    {
        return $query->where('id_semester', $semesterId);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_krs', $status);
    }

    /**
     * Scope untuk filter berdasarkan PA
     */
    public function scopeByPembimbingAkademik($query, $paId)
    {
        return $query->where('id_pembimbing_akademik', $paId);
    }
}
