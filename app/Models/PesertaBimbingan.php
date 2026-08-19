<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PesertaBimbingan extends Model
{
    protected $table = 'peserta_bimbingan';
    protected $primaryKey = 'id_peserta_bimbingan';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_peserta_bimbingan',
        'id_mata_kuliah',
        'id_registrasi_mahasiswa',
        'status_mata_kuliah',
        'id_dosen_pembimbing',
        'id_dosen_pembimbing_2',
    ];

    /**
     * Status mata kuliah constants
     */
    const STATUS_SELECTED = 'SELECTED';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

    /**
     * Relasi ke mata kuliah
     */
    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'id_mata_kuliah', 'id_mata_kuliah');
    }

    /**
     * Relasi ke registrasi mahasiswa
     */
    public function registrasiMahasiswa(): BelongsTo
    {
        return $this->belongsTo(RegistrasiMahasiswa::class, 'id_registrasi_mahasiswa', 'id_registrasi_mahasiswa');
    }

    /**
     * Relasi ke dosen pembimbing utama
     */
    public function dosenPembimbing(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'id_dosen_pembimbing', 'id_dosen');
    }

    /**
     * Relasi ke dosen pembimbing kedua (untuk skripsi)
     */
    public function dosenPembimbing2(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'id_dosen_pembimbing_2', 'id_dosen');
    }

    /**
     * Relasi ke detail KKN (jika mata kuliah KKN)
     */
    public function kknDetail(): HasOne
    {
        return $this->hasOne(KknDetail::class, 'id_peserta_bimbingan', 'id_peserta_bimbingan');
    }

    /**
     * Relasi ke detail magang (jika mata kuliah MAGANG)
     */
    public function magangDetail(): HasOne
    {
        return $this->hasOne(MagangDetail::class, 'id_peserta_bimbingan', 'id_peserta_bimbingan');
    }

    /**
     * Relasi ke detail skripsi (jika mata kuliah SKRIPSI)
     */
    public function skripsiDetail(): HasOne
    {
        return $this->hasOne(SkripsiDetail::class, 'id_peserta_bimbingan', 'id_peserta_bimbingan');
    }

    /**
     * Relasi ke laporan bab
     */
    public function laporanBabs(): HasMany
    {
        return $this->hasMany(LaporanBab::class, 'id_peserta_bimbingan', 'id_peserta_bimbingan');
    }

    /**
     * Relasi ke file bimbingan
     */
    public function bimbinganFiles(): HasMany
    {
        return $this->hasMany(BimbinganFile::class, 'id_peserta_bimbingan', 'id_peserta_bimbingan');
    }

    /**
     * Relasi ke nilai perkuliahan (bimbingan)
     */
    public function nilaiPerkuliahan(): HasOne
    {
        return $this->hasOne(NilaiPerkuliahan::class, 'id_peserta_bimbingan', 'id_peserta_bimbingan')
            ->where('jenis_peserta', 'BIMBINGAN');
    }

    /**
     * Get jenis mata kuliah
     */
    public function getJenisMataKuliahAttribute()
    {
        return $this->mataKuliah->jenis_mata_kuliah ?? null;
    }

    /**
     * Check if this is KKN
     */
    public function getIsKknAttribute()
    {
        return $this->jenis_mata_kuliah === 'KKN';
    }

    /**
     * Check if this is MAGANG
     */
    public function getIsMagangAttribute()
    {
        return $this->jenis_mata_kuliah === 'MAGANG';
    }

    /**
     * Check if this is SKRIPSI
     */
    public function getIsSkripsiAttribute()
    {
        return $this->jenis_mata_kuliah === 'SKRIPSI';
    }

    /**
     * Get status pembimbing
     */
    public function getStatusPembimbingAttribute()
    {
        if ($this->is_skripsi) {
            if ($this->id_dosen_pembimbing && $this->id_dosen_pembimbing_2) {
                return 'complete'; // 2 pembimbing lengkap
            } elseif ($this->id_dosen_pembimbing) {
                return 'partial'; // hanya 1 pembimbing
            } else {
                return 'unassigned'; // belum ada pembimbing
            }
        } else {
            // Untuk KKN dan Magang hanya butuh 1 pembimbing
            return $this->id_dosen_pembimbing ? 'assigned' : 'unassigned';
        }
    }

    /**
     * Get mahasiswa info
     */
    public function getMahasiswaAttribute()
    {
        return $this->registrasiMahasiswa->mahasiswa ?? null;
    }

    /**
     * Get semester info
     */
    public function getSemesterAttribute()
    {
        return $this->registrasiMahasiswa->semester ?? null;
    }

    /**
     * Get progress information based on jenis mata kuliah
     */
    public function getProgressInfoAttribute()
    {
        if ($this->is_kkn && $this->kknDetail) {
            $kelompok = $this->kknDetail->kelompokKkn;
            return [
                'type' => 'KKN',
                'kelompok' => $kelompok->nama_kelompok ?? null,
                'lokasi' => $kelompok->lokasi ?? null,
                'peran' => $this->kknDetail->peran_kelompok ?? null,
                'dpl' => $kelompok->dpl->pengguna->nama ?? null,
            ];
        }

        if ($this->is_magang && $this->magangDetail) {
            return [
                'type' => 'MAGANG',
                'tempat_magang' => $this->magangDetail->tempat_magang,
                'bidang_magang' => $this->magangDetail->bidang_magang,
                'pembimbing' => $this->dosenPembimbing->pengguna->nama ?? null,
            ];
        }

        if ($this->is_skripsi && $this->skripsiDetail) {
            return [
                'type' => 'SKRIPSI',
                'judul' => $this->skripsiDetail->judul,
                'bidang_penelitian' => $this->skripsiDetail->bidang_penelitian,
                'status_proposal' => $this->skripsiDetail->status_proposal,
                'pembimbing_1' => $this->dosenPembimbing->pengguna->nama ?? null,
                'pembimbing_2' => $this->dosenPembimbing2->pengguna->nama ?? null,
                'progress_percentage' => $this->skripsiDetail->progress_percentage,
            ];
        }

        return [
            'type' => $this->jenis_mata_kuliah,
            'pembimbing' => $this->dosenPembimbing->pengguna->nama ?? null,
        ];
    }

    /**
     * Scope untuk mata kuliah tertentu
     */
    public function scopeByJenisMataKuliah($query, $jenis)
    {
        return $query->whereHas('mataKuliah', function ($q) use ($jenis) {
            $q->where('jenis_mata_kuliah', $jenis);
        });
    }

    /**
     * Scope untuk KKN
     */
    public function scopeKkn($query)
    {
        return $query->byJenisMataKuliah('KKN');
    }

    /**
     * Scope untuk MAGANG
     */
    public function scopeMagang($query)
    {
        return $query->byJenisMataKuliah('MAGANG');
    }

    /**
     * Scope untuk SKRIPSI
     */
    public function scopeSkripsi($query)
    {
        return $query->byJenisMataKuliah('SKRIPSI');
    }

    /**
     * Scope untuk yang sudah approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status_mata_kuliah', self::STATUS_APPROVED);
    }

    /**
     * Scope untuk yang sudah ada pembimbing
     */
    public function scopeHasPembimbing($query)
    {
        return $query->whereNotNull('id_dosen_pembimbing');
    }

    /**
     * Scope untuk yang belum ada pembimbing
     */
    public function scopeNoPembimbing($query)
    {
        return $query->whereNull('id_dosen_pembimbing');
    }

    /**
     * Scope untuk semester tertentu
     */
    public function scopeBySemester($query, $semesterId)
    {
        return $query->whereHas('registrasiMahasiswa', function ($q) use ($semesterId) {
            $q->where('id_semester', $semesterId);
        });
    }

    /**
     * Get status mata kuliah formatted
     */
    public function getStatusMataKuliahFormattedAttribute()
    {
        $statuses = [
            self::STATUS_SELECTED => 'Dipilih',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
        ];

        return $statuses[$this->status_mata_kuliah] ?? $this->status_mata_kuliah;
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            self::STATUS_SELECTED => 'bg-yellow-100 text-yellow-800',
            self::STATUS_APPROVED => 'bg-green-100 text-green-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
        ];

        return $classes[$this->status_mata_kuliah] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Check if need pembimbing assignment
     */
    public function getNeedPembimbingAssignmentAttribute()
    {
        if ($this->status_mata_kuliah !== self::STATUS_APPROVED) {
            return false;
        }

        if ($this->is_skripsi) {
            return !$this->id_dosen_pembimbing || !$this->id_dosen_pembimbing_2;
        } else {
            return !$this->id_dosen_pembimbing;
        }
    }
}
