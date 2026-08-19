<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'mahasiswa';
    protected $primaryKey = 'id_mahasiswa';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'foto',

        'nim', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'angkatan',
        'nik', 'nisn', 'npwp', 'agama', 'kode_negara', 'kewarganegaraan', 'jalan',
        'dusun', 'rt', 'rw', 'kelurahan', 'kode_pos',
        'id_program_studi', 'id_kurikulum', 'status_mahasiswa', 'id_pengguna',

        // Ayah
        'nik_ayah', 'nama_ayah', 'tempat_lahir_ayah', 'tanggal_lahir_ayah',
        'nama_pendidikan_ayah', 'nama_pekerjaan_ayah', 'nama_penghasilan_ayah',

        // Ibu
        'nik_ibu', 'nama_ibu', 'tempat_lahir_ibu', 'tanggal_lahir_ibu',
        'nama_pendidikan_ibu', 'nama_pekerjaan_ibu', 'nama_penghasilan_ibu',

        // Wali
        'nama_wali', 'tempat_lahir_wali', 'tanggal_lahir_wali',
        'nama_pendidikan_wali', 'nama_pekerjaan_wali', 'nama_penghasilan_wali',
    ];


    // Relasi
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'id_program_studi');
    }

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'id_kurikulum');
    }

    public function registrasi()
    {
        return $this->hasMany(RegistrasiMahasiswa::class, 'id_mahasiswa');
    }

    public function pembimbingAkademik(): HasMany
    {
        return $this->hasMany(PembimbingAkademik::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    // Method untuk mendapatkan PA aktif di semester tertentu
    public function getPaAktif($semesterId = null)
    {
        $query = $this->pembimbingAkademik()->where('status_pa', 'AKTIF');

        if ($semesterId) {
            $query->where('id_semester', $semesterId);
        }

        return $query->with(['dosen.pengguna'])->first();
    }

    // Method untuk cek apakah sudah punya PA di semester tertentu
    public function hasPaInSemester($semesterId): bool
    {
        return $this->pembimbingAkademik()
            ->where('id_semester', $semesterId)
            ->where('status_pa', 'AKTIF')
            ->exists();
    }
}
