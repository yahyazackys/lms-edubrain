<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Kurikulum extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kurikulum';
    protected $primaryKey = 'id_kurikulum';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_kurikulum',
        'jumlah_sks_lulus',
        'jumlah_sks_wajib',
        'jumlah_sks_pilihan',
        'id_program_studi',
        'id_semester',
    ];

    // Relasi ke Program Studi
    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'id_program_studi', 'id_program_studi');
    }

    // Relasi ke Semester
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester', 'id_semester');
    }

    // Relasi ke Mata Kuliah (melalui tabel pivot)
    public function mataKuliah()
    {
        return $this->belongsToMany(MataKuliah::class, 'kurikulum_mata_kuliah', 'id_kurikulum', 'id_mata_kuliah')
            ->withPivot(['id', 'semester', 'jenis_mata_kuliah'])
            ->withTimestamps();
    }
}
