<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Materi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'materi';
    protected $primaryKey = 'id_materi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kelas_kuliah',
        'judul',
        'deskripsi',
        'dokumen',
    ];

    // Relasi ke Kelas Kuliah
    public function kelasKuliah()
    {
        return $this->belongsTo(KelasKuliah::class, 'id_kelas_kuliah', 'id_kelas_kuliah');
    }
}
