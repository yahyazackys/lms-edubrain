<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'dosen';
    protected $primaryKey = 'id_dosen';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        // Inti
        'nidn',
        'status_dosen',
        'status_kepegawaian',
        'id_program_studi',
        'id_pengguna',

        'foto',
        // Biodata Dosen
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'gelar_depan',
        'gelar_belakang',
        'nik',
        'npwp',
        'jalan',
        'dusun',
        'rt',
        'rw',
        'kelurahan',
        'kode_pos',

        // Pembimbing Akademik
        'total_kuota_pa',
    ];

    protected $casts = [
        'total_kuota_pa' => 'integer',
        'tanggal_lahir' => 'date',
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

    public function kelasKuliah()
    {
        return $this->hasMany(KelasKuliah::class, 'id_dosen');
    }

    public function pembimbingAkademik(): HasMany
    {
        return $this->hasMany(PembimbingAkademik::class, 'id_dosen', 'id_dosen');
    }

    // Method untuk menghitung kuota tersisa di semester tertentu
    public function getKuotaTersisa($semesterId): int
    {
        if (!$this->total_kuota_pa) {
            return 0;
        }

        $terpakai = $this->pembimbingAkademik()
            ->where('id_semester', $semesterId)
            ->where('status_pa', 'AKTIF')
            ->count();

        return max(0, $this->total_kuota_pa - $terpakai);
    }

    // Method untuk mendapatkan mahasiswa bimbingan di semester tertentu
    public function getMahasiswaBimbingan($semesterId = null)
    {
        $query = $this->pembimbingAkademik()
            ->where('status_pa', 'AKTIF')
            ->with(['mahasiswa.pengguna', 'semester']);

        if ($semesterId) {
            $query->where('id_semester', $semesterId);
        }

        return $query->get();
    }

    // Accessor untuk nama lengkap dengan gelar
    public function getNamaLengkapAttribute(): string
    {
        if (!$this->pengguna) {
            return $this->nama ?? '-';
        }

        $gelarDepan = $this->gelar_depan ? $this->gelar_depan . ' ' : '';
        $gelarBelakang = $this->gelar_belakang ? ', ' . $this->gelar_belakang : '';

        return trim($gelarDepan . $this->pengguna->nama . $gelarBelakang);
    }

    // Scope untuk dosen yang masih punya kuota
    public function scopeWithKuota($query, $semesterId = null)
    {
        $query->where('status_dosen', 'AKTIF')
            ->whereNotNull('total_kuota_pa')
            ->where('total_kuota_pa', '>', 0);

        if ($semesterId) {
            // Add subquery to filter dosen yang masih punya kuota tersisa
            $query->whereRaw('total_kuota_pa > (
                SELECT COUNT(*) 
                FROM pembimbing_akademik 
                WHERE pembimbing_akademik.id_dosen = dosen.id_dosen 
                AND pembimbing_akademik.id_semester = ? 
                AND pembimbing_akademik.status_pa = "AKTIF"
            )', [$semesterId]);
        }

        return $query;
    }
}
