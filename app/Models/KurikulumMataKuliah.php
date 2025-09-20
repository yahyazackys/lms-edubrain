<?php
// App/Models/KurikulumMataKuliah.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KurikulumMataKuliah extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kurikulum_mata_kuliah';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kurikulum',
        'id_mata_kuliah',
        'semester',
        'jenis_mata_kuliah',
    ];

    // Relasi ke Kurikulum
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'id_kurikulum');
    }

    // Relasi ke MataKuliah
    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'id_mata_kuliah');
    }

    // Relasi ke KelasKuliah
    public function kelasKuliah()
    {
        return $this->hasMany(KelasKuliah::class, 'id_kurikulum_mata_kuliah', 'id');
    }

    /**
     * Scope untuk filter berdasarkan kurikulum
     */
    public function scopeByKurikulum($query, $kurikulumId)
    {
        return $query->where('id_kurikulum', $kurikulumId);
    }

    /**
     * Scope untuk filter berdasarkan semester
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope untuk filter berdasarkan jenis mata kuliah
     */
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_mata_kuliah', $jenis);
    }

    /**
     * Scope untuk mata kuliah wajib
     */
    public function scopeWajib($query)
    {
        return $query->where('jenis_mata_kuliah', 'WAJIB');
    }

    /**
     * Scope untuk mata kuliah pilihan
     */
    public function scopePilihan($query)
    {
        return $query->where('jenis_mata_kuliah', 'PILIHAN');
    }
}
