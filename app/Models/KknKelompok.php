<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KknKelompok extends Model
{
    protected $table = 'kkn_kelompok';
    protected $primaryKey = 'id_kelompok_kkn';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_kelompok_kkn',
        'nama_kelompok',
        'lokasi',
        'alamat_lokasi',
        'id_dpl',
        'periode_mulai',
        'periode_selesai',
        'target_program_kerja',
        'id_semester'
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester', 'id_semester');
    }

    /**
     * Relasi ke Dosen Pembimbing Lapangan
     */
    public function dpl(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'id_dpl', 'id_dosen');
    }

    /**
     * Relasi ke detail anggota KKN
     */
    public function kknDetails(): HasMany
    {
        return $this->hasMany(KknDetail::class, 'id_kelompok_kkn', 'id_kelompok_kkn');
    }

    /**
     * Relasi ke dokumentasi KKN
     */
    public function kknDokumentasis(): HasMany
    {
        return $this->hasMany(KknDokumentasi::class, 'id_kelompok_kkn', 'id_kelompok_kkn');
    }

    /**
     * Get ketua kelompok
     */
    public function getKetuaAttribute()
    {
        return $this->kknDetails()->where('peran_kelompok', 'KETUA')->first();
    }

    /**
     * Get anggota (tanpa ketua)
     */
    public function getAnggotaAttribute()
    {
        return $this->kknDetails()->where('peran_kelompok', 'ANGGOTA')->get();
    }

    /**
     * Get total anggota
     */
    public function getTotalAnggotaAttribute()
    {
        return $this->kknDetails()->count();
    }

    /**
     * Check if kelompok has ketua
     */
    public function getHasKetuaAttribute()
    {
        return $this->kknDetails()->where('peran_kelompok', 'KETUA')->exists();
    }
}
