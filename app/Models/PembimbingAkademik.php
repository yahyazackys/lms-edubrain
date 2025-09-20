<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PembimbingAkademik extends Model
{
    use HasUuids;

    protected $table = 'pembimbing_akademik';
    protected $primaryKey = 'id_pembimbing_akademik';

    protected $fillable = [
        'id_pembimbing_akademik',
        'id_mahasiswa',
        'id_dosen',
        'id_semester',
        'status_pa',
        'nomor_sk',
        'tanggal_sk',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
    ];

    /**
     * Relasi ke Mahasiswa
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    /**
     * Relasi ke Dosen
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'id_dosen');
    }

    /**
     * Relasi ke Semester
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'id_semester', 'id_semester');
    }

    /**
     * Scope untuk join data kuota dosen
     */
    public function scopeWithKuotaInfo($query, $semesterId = null)
    {
        $query->with(['dosen' => function ($q) {
            $q->select('id_dosen', 'nidn', 'total_kuota_pa', 'id_pengguna', 'gelar_depan', 'gelar_belakang')
                ->with('pengguna:id_pengguna,nama');
        }]);

        if ($semesterId) {
            $query->where('id_semester', $semesterId);
        }

        return $query;
    }

    /**
     * Static method untuk menghitung kuota tersisa dosen di semester tertentu
     */
    public static function getKuotaTersisa(string $dosenId, string $semesterId): int
    {
        $dosen = Dosen::find($dosenId);
        if (!$dosen || !$dosen->total_kuota_pa) {
            return 0;
        }

        $terpakai = self::where('id_dosen', $dosenId)
            ->where('id_semester', $semesterId)
            ->where('status_pa', 'AKTIF')
            ->count();

        return max(0, $dosen->total_kuota_pa - $terpakai);
    }

    /**
     * Static method untuk menghitung semester mahasiswa berdasarkan angkatan dan periode semester
     */
    public static function getSemesterMahasiswa(int $angkatan, string $semesterId): int
    {
        // Format semester ID: YYYYS (contoh: 20251, 20252)
        $tahunSemester = (int) substr($semesterId, 0, 4);
        $periodeSemester = (int) substr($semesterId, 4, 1);

        // Hitung selisih tahun
        $selisihTahun = $tahunSemester - $angkatan;

        // Hitung semester
        $semester = ($selisihTahun * 2) + $periodeSemester;

        // Minimal semester 1
        return max(1, $semester);
    }

    /**
     * Scope untuk filter berdasarkan status PA
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status_pa', $status);
    }

    /**
     * Scope untuk filter berdasarkan semester
     */
    public function scopeBySemester($query, string $semesterId)
    {
        return $query->where('id_semester', $semesterId);
    }

    /**
     * Accessor untuk nama lengkap dosen dengan gelar
     */
    public function getNamaDosenLengkapAttribute(): string
    {
        if (!$this->dosen || !$this->dosen->pengguna) {
            return '-';
        }

        $gelarDepan = $this->dosen->gelar_depan ? $this->dosen->gelar_depan . ' ' : '';
        $gelarBelakang = $this->dosen->gelar_belakang ? ', ' . $this->dosen->gelar_belakang : '';

        return trim($gelarDepan . $this->dosen->pengguna->nama . $gelarBelakang);
    }

    /**
     * Accessor untuk format tanggal SK
     */
    public function getTanggalSkFormattedAttribute(): string
    {
        return $this->tanggal_sk ? $this->tanggal_sk->format('d/m/Y') : '-';
    }

    /**
     * Check apakah PA masih aktif
     */
    public function isAktif(): bool
    {
        return $this->status_pa === 'AKTIF';
    }

    /**
     * Check apakah PA sudah selesai
     */
    public function isSelesai(): bool
    {
        return $this->status_pa === 'SELESAI';
    }
}
