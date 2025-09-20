<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProgramStudi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'program_studi';
    protected $primaryKey = 'id_program_studi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_program_studi',
        'nama_program_studi',
        'status',
        'id_jenjang_pendidikan',
    ];

    // Relasi ke kurikulum
    public function kurikulum()
    {
        return $this->hasMany(Kurikulum::class, 'id_program_studi', 'id_program_studi');
    }

    // Relasi ke mahasiswa
    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'id_program_studi', 'id_program_studi');
    }

    // Relasi ke jenjang pendidikan
    public function jenjang()
    {
        return $this->belongsTo(JenjangPendidikan::class, 'id_jenjang_pendidikan', 'id_jenjang_pendidikan');
    }
}
