<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkripsiDetail extends Model
{
    protected $table = 'skripsi_detail';
    protected $primaryKey = 'id_skripsi_detail';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_skripsi_detail',
        'id_peserta_bimbingan',
        'judul',
        'bidang_penelitian',
        'status_proposal',
        'tanggal_seminar_proposal',
        'tanggal_sidang_skripsi',
        'id_semester',
    ];

    protected $casts = [
        'tanggal_seminar_proposal' => 'date',
        'tanggal_sidang_skripsi' => 'date',
    ];

    /**
     * Status proposal constants
     */
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_SUBMITTED = 'SUBMITTED';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester', 'id_semester');
    }

    /**
     * Relasi ke peserta bimbingan
     */
    public function pesertaBimbingan(): BelongsTo
    {
        return $this->belongsTo(PesertaBimbingan::class, 'id_peserta_bimbingan', 'id_peserta_bimbingan');
    }

    /**
     * Get status proposal dengan format yang rapi
     */
    public function getStatusProposalFormattedAttribute()
    {
        $statuses = [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Diajukan',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
        ];

        return $statuses[$this->status_proposal] ?? $this->status_proposal;
    }

    /**
     * Get status proposal badge class for UI
     */
    public function getStatusProposalBadgeClassAttribute()
    {
        $classes = [
            self::STATUS_DRAFT => 'bg-gray-100 text-gray-800',
            self::STATUS_SUBMITTED => 'bg-blue-100 text-blue-800',
            self::STATUS_APPROVED => 'bg-green-100 text-green-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
        ];

        return $classes[$this->status_proposal] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Check if proposal is approved
     */
    public function getIsProposalApprovedAttribute()
    {
        return $this->status_proposal === self::STATUS_APPROVED;
    }

    /**
     * Check if ready for seminar
     */
    public function getIsReadyForSeminarAttribute()
    {
        return $this->is_proposal_approved && $this->tanggal_seminar_proposal;
    }

    /**
     * Check if ready for sidang
     */
    public function getIsReadyForSidangAttribute()
    {
        return $this->is_ready_for_seminar && $this->tanggal_sidang_skripsi;
    }

    /**
     * Check if all details are complete
     */
    public function getIsCompleteAttribute()
    {
        return !empty($this->judul) &&
            !empty($this->bidang_penelitian) &&
            $this->status_proposal !== self::STATUS_DRAFT;
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute()
    {
        $steps = 0;
        $completed = 0;

        // Step 1: Judul dan bidang penelitian
        $steps++;
        if ($this->judul && $this->bidang_penelitian) {
            $completed++;
        }

        // Step 2: Proposal approved
        $steps++;
        if ($this->is_proposal_approved) {
            $completed++;
        }

        // Step 3: Seminar proposal scheduled
        $steps++;
        if ($this->tanggal_seminar_proposal) {
            $completed++;
        }

        // Step 4: Sidang scheduled
        $steps++;
        if ($this->tanggal_sidang_skripsi) {
            $completed++;
        }

        return $steps > 0 ? round(($completed / $steps) * 100) : 0;
    }

    /**
     * Scope untuk proposal yang sudah disetujui
     */
    public function scopeProposalApproved($query)
    {
        return $query->where('status_proposal', self::STATUS_APPROVED);
    }

    /**
     * Scope untuk yang siap seminar
     */
    public function scopeReadyForSeminar($query)
    {
        return $query->where('status_proposal', self::STATUS_APPROVED)
            ->whereNotNull('tanggal_seminar_proposal');
    }

    /**
     * Scope untuk yang siap sidang
     */
    public function scopeReadyForSidang($query)
    {
        return $query->where('status_proposal', self::STATUS_APPROVED)
            ->whereNotNull('tanggal_seminar_proposal')
            ->whereNotNull('tanggal_sidang_skripsi');
    }
}
