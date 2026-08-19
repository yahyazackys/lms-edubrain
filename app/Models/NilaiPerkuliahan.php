<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class NilaiPerkuliahan extends Model
{
    use HasUuids;

    protected $table = 'nilai_perkuliahan';
    protected $primaryKey = 'id_nilai_perkuliahan';

    protected $fillable = [
        'id_nilai_perkuliahan',
        'jenis_peserta',
        'id_peserta',
        'id_peserta_bimbingan',
        'nilai_angka',
        'nilai_indeks',
        'nilai_huruf'
    ];

    protected $casts = [
        'nilai_angka' => 'decimal:2',
        'nilai_indeks' => 'decimal:2',
        'jenis_peserta' => 'string'
    ];

    /**
     * Relasi ke Peserta Kelas Kuliah (untuk mata kuliah reguler)
     */
    public function pesertaKelasKuliah(): BelongsTo
    {
        return $this->belongsTo(PesertaKelasKuliah::class, 'id_peserta', 'id_peserta');
    }

    /**
     * Relasi ke Peserta Bimbingan (untuk mata kuliah bimbingan)
     */
    public function pesertaBimbingan(): BelongsTo
    {
        return $this->belongsTo(PesertaBimbingan::class, 'id_peserta_bimbingan', 'id_peserta_bimbingan');
    }

    /**
     * Accessor untuk format nilai lengkap
     */
    public function getFormattedGradeAttribute()
    {
        return [
            'angka' => $this->nilai_angka,
            'indeks' => $this->nilai_indeks,
            'huruf' => $this->nilai_huruf,
            'keterangan' => $this->getGradeDescription()
        ];
    }

    /**
     * Get grade description based on letter grade
     */
    public function getGradeDescription()
    {
        $descriptions = [
            'A' => 'Sangat Baik',
            'A-' => 'Sangat Baik',
            'B+' => 'Baik',
            'B' => 'Baik',
            'B-' => 'Baik',
            'C+' => 'Cukup',
            'C' => 'Cukup',
            'C-' => 'Cukup',
            'D' => 'Kurang',
            'E' => 'Tidak Lulus'
        ];

        return $descriptions[$this->nilai_huruf] ?? 'Unknown';
    }

    /**
     * Scope untuk filter berdasarkan semester
     */
    public function scopeBySemester($query, $semesterId)
    {
        return $query->where(function ($q) use ($semesterId) {
            $q->where('jenis_peserta', 'KELAS')
                ->whereHas('pesertaKelasKuliah.kelasKuliah', function ($subQ) use ($semesterId) {
                    $subQ->where('id_semester', $semesterId);
                });
        })->orWhere(function ($q) use ($semesterId) {
            $q->where('jenis_peserta', 'BIMBINGAN')
                ->whereHas('pesertaBimbingan.registrasiMahasiswa', function ($subQ) use ($semesterId) {
                    $subQ->where('id_semester', $semesterId);
                });
        });
    }

    /**
     * Scope untuk filter berdasarkan mahasiswa
     */
    public function scopeByMahasiswa($query, $mahasiswaId)
    {
        return $query->where(function ($q) use ($mahasiswaId) {
            $q->where('jenis_peserta', 'KELAS')
                ->whereHas('pesertaKelasKuliah.registrasiMahasiswa', function ($subQ) use ($mahasiswaId) {
                    $subQ->where('id_mahasiswa', $mahasiswaId);
                });
        })->orWhere(function ($q) use ($mahasiswaId) {
            $q->where('jenis_peserta', 'BIMBINGAN')
                ->whereHas('pesertaBimbingan.registrasiMahasiswa', function ($subQ) use ($mahasiswaId) {
                    $subQ->where('id_mahasiswa', $mahasiswaId);
                });
        });
    }
}
