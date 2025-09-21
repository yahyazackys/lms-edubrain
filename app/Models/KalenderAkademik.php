<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class KalenderAkademik extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'kalender_akademik';

    protected $fillable = [
        'judul',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_all_day',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_all_day' => 'boolean',
    ];

    // TAMBAH ACCESSOR UNTUK SAFETY
    public function getTanggalMulaiAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getTanggalSelesaiAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    // TAMBAH MUTATOR UNTUK MEMASTIKAN FORMAT BENAR
    public function setTanggalMulaiAttribute($value)
    {
        $this->attributes['tanggal_mulai'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function setTanggalSelesaiAttribute($value)
    {
        $this->attributes['tanggal_selesai'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }
}
