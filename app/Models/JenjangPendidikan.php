<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class JenjangPendidikan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'jenjang_pendidikan';
    protected $primaryKey = 'id_jenjang_pendidikan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_jenjang_pendidikan',
        'nama_jenjang_pendidikan',
    ];

    // Relasi ke Program Studi
    public function programStudi()
    {
        return $this->hasMany(ProgramStudi::class, 'id_jenjang_pendidikan', 'id_jenjang_pendidikan');
    }
}
