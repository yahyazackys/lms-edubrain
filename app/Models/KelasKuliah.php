<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KelasKuliah extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kelas_kuliah';
    protected $primaryKey = 'id_kelas_kuliah';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_kelas_kuliah',
        'nama_ruangan',
        'kapasitas',
        'jam_mulai',
        'jam_akhir',
        'hari',
        'bobot_absensi',
        'bobot_tugas',
        'bobot_uas',
        'bobot_uts',
        'status',
        'id_kurikulum_mata_kuliah',
        'id_semester',
        'id_dosen',
    ];

    public function kurikulumMataKuliah()
    {
        return $this->belongsTo(KurikulumMataKuliah::class, 'id_kurikulum_mata_kuliah', 'id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen');
    }

    public function pesertaKelasKuliah()
    {
        return $this->hasMany(PesertaKelasKuliah::class, 'id_kelas_kuliah');
    }

    /**
     * Scope untuk filter berdasarkan semester
     */
    public function scopeBySemester($query, $semesterId)
    {
        return $query->where('id_semester', $semesterId);
    }

    /**
     * Scope untuk filter berdasarkan hari
     */
    public function scopeByHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    /**
     * Hitung jumlah mahasiswa yang sudah terdaftar
     */
    public function getJumlahPesertaSelectedAttribute(): int
    {
        return $this->pesertaKelasKuliah()
            ->where('status_mata_kuliah', 'SELECTED')
            ->whereHas('registrasiMahasiswa', function ($query) {
                $query->where('status_krs', 'SUBMITTED');
            })
            ->count();
    }

    /**
     * Check apakah kelas sudah penuh
     */
    public function isPenuh(): bool
    {
        return $this->jumlah_peserta >= $this->kapasitas;
    }

    // Tambahkan method ini di model KelasKuliah
    public function mataKuliah()
    {
        return $this->hasOneThrough(
            MataKuliah::class,
            KurikulumMataKuliah::class,
            'id', // Foreign key di tabel kurikulum_mata_kuliah
            'id_mata_kuliah', // Foreign key di tabel mata_kuliah
            'id_kurikulum_mata_kuliah', // Local key di tabel kelas_kuliah
            'id_mata_kuliah' // Local key di tabel kurikulum_mata_kuliah
        );
    }
}
