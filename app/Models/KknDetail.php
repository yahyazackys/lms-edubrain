<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KknDetail extends Model
{
    protected $table = 'kkn_detail';
    protected $primaryKey = 'id_kkn_detail';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_kkn_detail',
        'id_peserta_bimbingan',
        'id_kelompok_kkn',
        'peran_kelompok',
    ];

    /**
     * Relasi ke peserta bimbingan
     */
    public function pesertaBimbingan(): BelongsTo
    {
        return $this->belongsTo(PesertaBimbingan::class, 'id_peserta_bimbingan', 'id_peserta_bimbingan');
    }

    /**
     * Relasi ke kelompok KKN
     */
    public function kelompokKkn(): BelongsTo
    {
        return $this->belongsTo(KknKelompok::class, 'id_kelompok_kkn', 'id_kelompok_kkn');
    }

    /**
     * Scope untuk ketua
     */
    public function scopeKetua($query)
    {
        return $query->where('peran_kelompok', 'KETUA');
    }

    /**
     * Scope untuk anggota
     */
    public function scopeAnggota($query)
    {
        return $query->where('peran_kelompok', 'ANGGOTA');
    }
}
