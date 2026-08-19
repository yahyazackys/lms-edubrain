<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MataKuliah extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'mata_kuliah';
    protected $primaryKey = 'id_mata_kuliah';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_mata_kuliah',
        'nama_mata_kuliah',
        'sks_mata_kuliah',
        'jenis_mata_kuliah',
    ];

    // Relasi Many-to-Many ke Kurikulum
    public function kurikulum()
    {
        return $this->belongsToMany(Kurikulum::class, 'kurikulum_mata_kuliah', 'id_mata_kuliah', 'id_kurikulum')
            ->withPivot('semester')
            ->withTimestamps();
    }

    // Relasi ke Kelas Kuliah
    public function kelasKuliah()
    {
        return $this->hasMany(KelasKuliah::class, 'id_mata_kuliah', 'id_mata_kuliah');
    }
}
