<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class LaporanBab extends Model
{
    use HasUuids;

    protected $table = 'laporan_bab';
    protected $primaryKey = 'id_laporan_bab';

    protected $fillable = [
        'id_laporan_bab',
        'id_peserta_bimbingan',
        'judul_bab',
        'konten',
        'file_template',
        'status',
        'catatan_pembimbing',
        'submitted_at',
        'approved_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_SUBMITTED = 'SUBMITTED';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_NEEDS_REVISION = 'NEEDS_REVISION';

    /**
     * Relasi ke peserta bimbingan
     */
    public function pesertaBimbingan(): BelongsTo
    {
        return $this->belongsTo(PesertaBimbingan::class, 'id_peserta_bimbingan', 'id_peserta_bimbingan');
    }

    /**
     * Relasi ke file bimbingan
     */
    public function bimbinganFiles(): HasMany
    {
        return $this->hasMany(BimbinganFile::class, 'id_laporan_bab', 'id_laporan_bab');
    }

    /**
     * Relasi ke mahasiswa melalui peserta bimbingan
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa')
            ->through('pesertaBimbingan.registrasiMahasiswa');
    }

    /**
     * Get status formatted
     */
    public function getStatusFormattedAttribute(): string
    {
        $statuses = [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Diajukan',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_NEEDS_REVISION => 'Perlu Revisi',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        $classes = [
            self::STATUS_DRAFT => 'bg-gray-100 text-gray-800',
            self::STATUS_SUBMITTED => 'bg-blue-100 text-blue-800',
            self::STATUS_APPROVED => 'bg-green-100 text-green-800',
            self::STATUS_NEEDS_REVISION => 'bg-red-100 text-red-800',
        ];

        return $classes[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Check if bab is approved
     */
    public function getIsApprovedAttribute(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if bab needs revision
     */
    public function getNeedsRevisionAttribute(): bool
    {
        return $this->status === self::STATUS_NEEDS_REVISION;
    }

    /**
     * Check if bab is submitted and waiting for review
     */
    public function getIsWaitingForReviewAttribute(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    /**
     * Check if bab can be edited
     */
    public function getCanEditAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_NEEDS_REVISION]);
    }

    /**
     * Check if bab can be submitted
     */
    public function getCanSubmitAttribute(): bool
    {
        return $this->status === self::STATUS_DRAFT || $this->status === self::STATUS_NEEDS_REVISION;
    }

    /**
     * Check if bab can be approved/rejected
     */
    public function getCanReviewAttribute(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    /**
     * Get jalur workflow berikutnya untuk mahasiswa
     */
    public function getNextStatusOptionsForStudentAttribute(): array
    {
        switch ($this->status) {
            case self::STATUS_DRAFT:
            case self::STATUS_NEEDS_REVISION:
                return [self::STATUS_SUBMITTED];
            default:
                return [];
        }
    }

    /**
     * Get jalur workflow berikutnya untuk dosen
     */
    public function getNextStatusOptionsForLecturerAttribute(): array
    {
        switch ($this->status) {
            case self::STATUS_SUBMITTED:
                return [self::STATUS_APPROVED, self::STATUS_NEEDS_REVISION];
            default:
                return [];
        }
    }

    /**
     * Scope untuk bab tertentu
     */
    public function scopeByNomorBab($query, $nomor)
    {
        return $query->where('nomor_bab', $nomor);
    }

    /**
     * Scope untuk status tertentu
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk yang sudah disubmit
     */
    public function scopeSubmitted($query)
    {
        return $query->whereIn('status', [
            self::STATUS_SUBMITTED,
            self::STATUS_APPROVED,
            self::STATUS_NEEDS_REVISION
        ]);
    }

    /**
     * Scope untuk yang perlu direview
     */
    public function scopeNeedsReview($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    /**
     * Scope untuk yang sudah approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope untuk yang perlu revisi
     */
    public function scopeNeedsRevision($query)
    {
        return $query->where('status', self::STATUS_NEEDS_REVISION);
    }

    /**
     * Scope untuk peserta bimbingan tertentu
     */
    public function scopeByPesertaBimbingan($query, $pesertaBimbinganId)
    {
        return $query->where('id_peserta_bimbingan', $pesertaBimbinganId);
    }

    /**
     * Order by nomor bab
     */
    public function scopeOrderByBab($query)
    {
        return $query->orderBy('nomor_bab');
    }

    /**
     * Order by latest submission
     */
    public function scopeOrderByLatestSubmission($query)
    {
        return $query->orderBy('submitted_at', 'desc');
    }

    /**
     * Get full bab identifier
     */
    public function getBabIdentifierAttribute(): string
    {
        return "BAB {$this->nomor_bab}: {$this->judul_bab}";
    }

    /**
     * Get short bab identifier
     */
    public function getShortBabIdentifierAttribute(): string
    {
        return "BAB {$this->nomor_bab}";
    }

    /**
     * Get file count
     */
    public function getFileCountAttribute(): int
    {
        return $this->bimbinganFiles()->count();
    }

    /**
     * Get latest file
     */
    public function getLatestFileAttribute()
    {
        return $this->bimbinganFiles()->latest()->first();
    }

    /**
     * Get latest submission (file atau text)
     */
    public function getLatestSubmissionAttribute()
    {
        return $this->bimbinganFiles()->latest()->first();
    }

    /**
     * Check if has any submission
     */
    public function getHasSubmissionAttribute(): bool
    {
        return $this->bimbinganFiles()->exists();
    }

    /**
     * Check if has content (konten langsung atau file)
     */
    public function getHasContentAttribute(): bool
    {
        return !empty($this->konten) || !empty($this->file_template) || $this->has_submission;
    }

    /**
     * Get submission type (FILE, TEXT, atau MIXED)
     */
    public function getSubmissionTypeAttribute(): ?string
    {
        $files = $this->bimbinganFiles;

        if ($files->isEmpty()) {
            return null;
        }

        $hasFile = $files->where('input_type', BimbinganFile::INPUT_FILE)->isNotEmpty();
        $hasText = $files->where('input_type', BimbinganFile::INPUT_TEXT)->isNotEmpty();

        if ($hasFile && $hasText) {
            return 'MIXED';
        }

        return $hasFile ? 'FILE' : 'TEXT';
    }

    /**
     * Get submission time formatted
     */
    public function getSubmissionTimeFormattedAttribute(): ?string
    {
        if (!$this->submitted_at) {
            return null;
        }

        return $this->submitted_at->format('d M Y H:i');
    }

    /**
     * Get approval time formatted
     */
    public function getApprovalTimeFormattedAttribute(): ?string
    {
        if (!$this->approved_at) {
            return null;
        }

        return $this->approved_at->format('d M Y H:i');
    }

    /**
     * Get days since submission
     */
    public function getDaysSinceSubmissionAttribute(): ?int
    {
        if (!$this->submitted_at) {
            return null;
        }

        return $this->submitted_at->diffInDays(now());
    }

    /**
     * Check if submission is overdue (more than 7 days without review)
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->is_waiting_for_review && $this->days_since_submission > 7;
    }

    /**
     * Get progress percentage for this bab
     */
    public function getProgressPercentageAttribute(): int
    {
        switch ($this->status) {
            case self::STATUS_DRAFT:
                return 25;
            case self::STATUS_SUBMITTED:
                return 50;
            case self::STATUS_NEEDS_REVISION:
                return 75;
            case self::STATUS_APPROVED:
                return 100;
            default:
                return 0;
        }
    }

    /**
     * Method untuk submit bab
     */
    public function submit(): bool
    {
        if (!$this->can_submit) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'submitted_at' => now()
        ]);

        return true;
    }

    /**
     * Method untuk approve bab
     */
    public function approve(?string $catatan = null): bool
    {
        if (!$this->can_review) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'catatan_pembimbing' => $catatan
        ]);

        return true;
    }

    /**
     * Method untuk reject bab
     */
    public function reject(string $catatan): bool
    {
        if (!$this->can_review) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_NEEDS_REVISION,
            'catatan_pembimbing' => $catatan,
            'approved_at' => null
        ]);

        return true;
    }

    /**
     * Method untuk reset ke draft
     */
    public function resetToDraft(): bool
    {
        $this->update([
            'status' => self::STATUS_DRAFT,
            'submitted_at' => null,
            'approved_at' => null,
            'catatan_pembimbing' => null
        ]);

        return true;
    }

    /**
     * Get content preview untuk display
     */
    public function getContentPreviewAttribute(): string
    {
        if (!empty($this->konten)) {
            return Str::limit(strip_tags($this->konten), 100);
        }

        $latestSubmission = $this->latest_submission;
        if ($latestSubmission) {
            if ($latestSubmission->input_type === BimbinganFile::INPUT_TEXT && $latestSubmission->konten) {
                return Str::limit(strip_tags($latestSubmission->konten), 100);
            }

            if ($latestSubmission->input_type === BimbinganFile::INPUT_FILE && $latestSubmission->file_path) {
                return 'File: ' . basename($latestSubmission->file_path);
            }
        }

        return 'Belum ada konten';
    }

    /**
     * Check if this bab blocks progression to next bab
     */
    public function getBlocksProgressionAttribute(): bool
    {
        return !$this->is_approved;
    }

    /**
     * Get mata kuliah name through relationships
     */
    public function getMataKuliahAttribute()
    {
        return $this->pesertaBimbingan->mataKuliah ?? null;
    }

    /**
     * Get dosen pembimbing through relationships
     */
    public function getDosenPembimbingAttribute()
    {
        return $this->pesertaBimbingan->dosenPembimbing ?? null;
    }

    /**
     * Static method to get all available statuses
     */
    public static function getAllStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_APPROVED,
            self::STATUS_NEEDS_REVISION,
        ];
    }

    /**
     * Static method to get status with labels
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Diajukan',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_NEEDS_REVISION => 'Perlu Revisi',
        ];
    }
}