<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BimbinganFile extends Model
{
    use HasUuids;

    protected $table = 'bimbingan_file';
    protected $primaryKey = 'id_bimbingan_file';

    protected $fillable = [
        'id_bimbingan_file',
        'id_peserta_bimbingan',
        'id_laporan_bab',
        'file_path',
        'konten',
        'input_type',
        'keterangan'
    ];

    protected $casts = [
        'input_type' => 'string'
    ];

    /**
     * Input type constants
     */
    const INPUT_FILE = 'FILE';
    const INPUT_TEXT = 'TEXT';

    /**
     * Relasi ke Peserta Bimbingan
     */
    public function pesertaBimbingan(): BelongsTo
    {
        return $this->belongsTo(PesertaBimbingan::class, 'id_peserta_bimbingan', 'id_peserta_bimbingan');
    }

    /**
     * Relasi ke Laporan Bab
     */
    public function laporanBab(): BelongsTo
    {
        return $this->belongsTo(LaporanBab::class, 'id_laporan_bab', 'id_laporan_bab');
    }

    /**
     * Relasi ke mahasiswa melalui peserta bimbingan
     */
    public function mahasiswa()
    {
        return $this->pesertaBimbingan->registrasiMahasiswa->mahasiswa ?? null;
    }

    /**
     * Relasi ke dosen pembimbing melalui peserta bimbingan
     */
    public function dosenPembimbing()
    {
        return $this->pesertaBimbingan->dosenPembimbing ?? null;
    }

    /**
     * Relasi ke mata kuliah melalui peserta bimbingan
     */
    public function mataKuliah()
    {
        return $this->pesertaBimbingan->mataKuliah ?? null;
    }

    /**
     * Check apakah ini file upload
     */
    public function isFileUpload(): bool
    {
        return $this->input_type === self::INPUT_FILE;
    }

    /**
     * Check apakah ini text input
     */
    public function isTextInput(): bool
    {
        return $this->input_type === self::INPUT_TEXT;
    }

    /**
     * Check apakah file exists di storage
     */
    public function fileExists(): bool
    {
        if (!$this->isFileUpload() || !$this->file_path) {
            return false;
        }

        return Storage::exists($this->file_path);
    }

    /**
     * Get file size dari storage
     */
    public function getFileSizeAttribute(): ?int
    {
        if (!$this->isFileUpload() || !$this->file_path || !$this->fileExists()) {
            return null;
        }

        return Storage::size($this->file_path);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $size = $this->file_size;

        if (!$size) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $size;

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file extension
     */
    public function getFileExtensionAttribute(): ?string
    {
        if (!$this->isFileUpload() || !$this->file_path) {
            return null;
        }

        return pathinfo($this->file_path, PATHINFO_EXTENSION);
    }

    /**
     * Get original filename dari path
     */
    public function getOriginalFilenameAttribute(): ?string
    {
        if (!$this->isFileUpload() || !$this->file_path) {
            return null;
        }

        return basename($this->file_path);
    }

    /**
     * Get display name untuk file
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->isFileUpload()) {
            return $this->original_filename ?: 'File tidak diketahui';
        }

        return 'Konten Text';
    }

    /**
     * Get content untuk display
     */
    public function getDisplayContentAttribute(): string
    {
        if ($this->isFileUpload()) {
            return $this->file_path ? $this->original_filename : 'File tidak ditemukan';
        }

        return $this->konten ? Str::limit(strip_tags($this->konten), 100) : 'Konten kosong';
    }

    /**
     * Get full content untuk display
     */
    public function getFullContentAttribute(): string
    {
        if ($this->isTextInput() && $this->konten) {
            return $this->konten;
        }

        if ($this->isFileUpload() && $this->file_path) {
            return "File: {$this->original_filename}";
        }

        return 'Tidak ada konten';
    }

    /**
     * Get content preview dengan HTML formatting
     */
    public function getContentPreviewAttribute(): string
    {
        if ($this->isTextInput() && $this->konten) {
            return Str::limit($this->konten, 200);
        }

        return null;
    }

    /**
     * Get icon berdasarkan input type atau file extension
     */
    public function getDisplayIconAttribute(): string
    {
        if ($this->isTextInput()) {
            return 'text-document';
        }

        if ($this->isFileUpload()) {
            $extension = strtolower($this->file_extension ?? '');

            switch ($extension) {
                case 'pdf':
                    return 'document-pdf';
                case 'doc':
                case 'docx':
                    return 'document-word';
                case 'xls':
                case 'xlsx':
                    return 'document-excel';
                case 'ppt':
                case 'pptx':
                    return 'document-powerpoint';
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    return 'image';
                case 'zip':
                case 'rar':
                    return 'archive';
                default:
                    return 'document';
            }
        }

        return 'unknown';
    }

    /**
     * Get color class berdasarkan input type
     */
    public function getColorClassAttribute(): string
    {
        if ($this->isTextInput()) {
            return 'text-blue-600 bg-blue-50';
        }

        return 'text-green-600 bg-green-50';
    }

    /**
     * Check apakah file bisa di-preview
     */
    public function canPreview(): bool
    {
        if ($this->isTextInput()) {
            return true;
        }

        if ($this->isFileUpload()) {
            $previewableExtensions = ['pdf', 'txt', 'jpg', 'jpeg', 'png', 'gif'];
            return in_array(strtolower($this->file_extension ?? ''), $previewableExtensions);
        }

        return false;
    }

    /**
     * Check apakah file bisa di-download
     */
    public function canDownload(): bool
    {
        return $this->isFileUpload() && $this->file_path && $this->fileExists();
    }

    /**
     * Get download URL
     */
    public function getDownloadUrlAttribute(): ?string
    {
        if (!$this->canDownload()) {
            return null;
        }

        return route('bimbingan.file.download', $this->id_bimbingan_file);
    }

    /**
     * Get file MIME type dari extension
     */
    public function getMimeTypeAttribute(): ?string
    {
        if (!$this->isFileUpload() || !$this->file_extension) {
            return null;
        }

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
        ];

        return $mimeTypes[strtolower($this->file_extension)] ?? 'application/octet-stream';
    }

    /**
     * Scope untuk filter berdasarkan input type
     */
    public function scopeByInputType($query, $type)
    {
        return $query->where('input_type', $type);
    }

    /**
     * Scope untuk filter berdasarkan peserta bimbingan
     */
    public function scopeByPesertaBimbingan($query, $pesertaId)
    {
        return $query->where('id_peserta_bimbingan', $pesertaId);
    }

    /**
     * Scope untuk filter berdasarkan bab
     */
    public function scopeByLaporanBab($query, $babId)
    {
        return $query->where('id_laporan_bab', $babId);
    }

    /**
     * Scope untuk file uploads saja
     */
    public function scopeFileUploads($query)
    {
        return $query->where('input_type', self::INPUT_FILE);
    }

    /**
     * Scope untuk text inputs saja
     */
    public function scopeTextInputs($query)
    {
        return $query->where('input_type', self::INPUT_TEXT);
    }

    /**
     * Scope untuk yang punya file path
     */
    public function scopeWithFile($query)
    {
        return $query->whereNotNull('file_path');
    }

    /**
     * Scope untuk yang punya konten text
     */
    public function scopeWithText($query)
    {
        return $query->whereNotNull('konten');
    }

    /**
     * Scope untuk order by latest
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope untuk order by oldest
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Get formatted content berdasarkan type
     */
    public function getFormattedContentAttribute(): array
    {
        if ($this->isFileUpload() && $this->file_path) {
            return [
                'type' => 'file',
                'content' => $this->original_filename,
                'path' => $this->file_path,
                'size' => $this->formatted_file_size,
                'extension' => $this->file_extension,
                'mime_type' => $this->mime_type,
                'can_preview' => $this->canPreview(),
                'can_download' => $this->canDownload(),
                'download_url' => $this->download_url
            ];
        }

        if ($this->isTextInput() && $this->konten) {
            return [
                'type' => 'text',
                'content' => $this->konten,
                'preview' => $this->content_preview,
                'word_count' => str_word_count(strip_tags($this->konten)),
                'char_count' => strlen($this->konten)
            ];
        }

        return [
            'type' => 'empty',
            'content' => 'Konten kosong',
            'preview' => null
        ];
    }

    /**
     * Get upload time formatted
     */
    public function getUploadTimeFormattedAttribute(): string
    {
        return $this->created_at->format('d M Y H:i');
    }

    /**
     * Get time ago
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Method untuk delete file dari storage
     */
    public function deleteFile(): bool
    {
        if ($this->isFileUpload() && $this->file_path && $this->fileExists()) {
            return Storage::delete($this->file_path);
        }

        return true;
    }

    /**
     * Override delete method untuk hapus file juga
     */
    public function delete(): bool
    {
        $this->deleteFile();
        return parent::delete();
    }

    /**
     * Static method untuk create file upload
     */
    public static function createFileUpload(array $data): self
    {
        return self::create(array_merge($data, [
            'input_type' => self::INPUT_FILE
        ]));
    }

    /**
     * Static method untuk create text input
     */
    public static function createTextInput(array $data): self
    {
        return self::create(array_merge($data, [
            'input_type' => self::INPUT_TEXT
        ]));
    }

    /**
     * Static method untuk get input type options
     */
    public static function getInputTypeOptions(): array
    {
        return [
            self::INPUT_FILE => 'Upload File',
            self::INPUT_TEXT => 'Input Text',
        ];
    }

    /**
     * Get nomor bab dari relasi
     */
    public function getNomorBabAttribute(): ?int
    {
        return $this->laporanBab->nomor_bab ?? null;
    }

    /**
     * Get judul bab dari relasi
     */
    public function getJudulBabAttribute(): ?string
    {
        return $this->laporanBab->judul_bab ?? null;
    }

    /**
     * Get mahasiswa name
     */
    public function getMahasiswaNameAttribute(): ?string
    {
        $mahasiswa = $this->mahasiswa();
        return $mahasiswa ? $mahasiswa->nama : null;
    }

    /**
     * Get mahasiswa NIM
     */
    public function getMahasiswaNimAttribute(): ?string
    {
        $mahasiswa = $this->mahasiswa();
        return $mahasiswa ? $mahasiswa->nim : null;
    }

    /**
     * Get status bab terkait
     */
    public function getBabStatusAttribute(): ?string
    {
        return $this->laporanBab->status ?? null;
    }

    /**
     * Check if this is the latest submission for the bab
     */
    public function getIsLatestSubmissionAttribute(): bool
    {
        if (!$this->id_laporan_bab) {
            return true;
        }

        $latestSubmission = $this->laporanBab->latest_submission;
        return $latestSubmission && $latestSubmission->id_bimbingan_file === $this->id_bimbingan_file;
    }
}
