<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MagangDetail extends Model
{
    protected $table = 'magang_detail';
    protected $primaryKey = 'id_magang_detail';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_magang_detail',
        'id_peserta_bimbingan',
        'tempat_magang',
        'alamat_magang',
        'bidang_magang',
        'id_semester',
    ];

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
     * Get nama tempat magang dengan format yang rapi
     */
    public function getTempatMagangLengkapAttribute()
    {
        $result = $this->tempat_magang;

        if ($this->bidang_magang) {
            $result .= ' - ' . $this->bidang_magang;
        }

        return $result;
    }

    /**
     * Check if magang detail is complete
     */
    public function getIsCompleteAttribute()
    {
        return !empty($this->tempat_magang) && !empty($this->alamat_magang);
    }
}
