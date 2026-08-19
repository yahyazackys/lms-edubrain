<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KknDokumentasi extends Model
{
    use HasUuids;

    protected $table = 'kkn_dokumentasi';
    protected $primaryKey = 'id_kkn_dokumentasi';

    protected $fillable = [
        'id_kkn_dokumentasi',
        'id_kelompok_kkn',
        'judul',
        'deskripsi',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'original_filename',
        'uploaded_by'
    ];

    protected $casts = [
        'file_type' => 'string',
        'file_size' => 'integer'
    ];

    /**
     * Relasi ke Kelompok KKN
     */
    public function kelompokKkn(): BelongsTo
    {
        return $this->belongsTo(KknKelompok::class, 'id_kelompok_kkn', 'id_kelompok_kkn');
    }

    /**
     * Relasi ke Mahasiswa yang Upload
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'uploaded_by', 'id_mahasiswa');
    }

    /**
     * Accessor untuk format file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return '0 B';

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check apakah file adalah gambar
     */
    public function isImage(): bool
    {
        return $this->file_type === 'IMAGE';
    }

    /**
     * Check apakah file adalah dokumen
     */
    public function isDocument(): bool
    {
        return $this->file_type === 'DOCUMENT';
    }

    /**
     * Scope untuk filter berdasarkan kelompok
     */
    public function scopeByKelompok($query, $kelompokId)
    {
        return $query->where('id_kelompok_kkn', $kelompokId);
    }

    /**
     * Scope untuk filter berdasarkan tipe file
     */
    public function scopeByFileType($query, $type)
    {
        return $query->where('file_type', $type);
    }
}
