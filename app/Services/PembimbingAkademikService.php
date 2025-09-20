<?php

namespace App\Services;

use App\Models\PembimbingAkademik;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PembimbingAkademikService
{
    /**
     * Assign PA untuk mahasiswa
     */
    public function assignPA(array $data): PembimbingAkademik
    {
        DB::beginTransaction();

        try {
            // Validasi mahasiswa belum punya PA di semester ini
            if (PembimbingAkademik::mahasiswaHasPA($data['id_mahasiswa'], $data['id_semester'])) {
                throw new \Exception('Mahasiswa sudah memiliki Pembimbing Akademik untuk semester ini');
            }

            // Validasi kuota dosen
            $dosen = Dosen::findOrFail($data['id_dosen']);
            if (!$dosen->hasAvailableQuota($data['id_semester'])) {
                throw new \Exception('Kuota dosen PA sudah penuh');
            }

            // Validasi mahasiswa dan dosen aktif
            $mahasiswa = Mahasiswa::with('pengguna')->findOrFail($data['id_mahasiswa']);
            if ($mahasiswa->status_mahasiswa !== 'AKTIF') {
                throw new \Exception('Mahasiswa tidak aktif');
            }

            if (!$dosen->canBePAInstance()) {
                throw new \Exception('Dosen tidak dapat menjadi PA');
            }

            // Create assignment
            $assignment = PembimbingAkademik::create([
                'id_mahasiswa' => $data['id_mahasiswa'],
                'id_dosen' => $data['id_dosen'],
                'id_semester' => $data['id_semester'],
                'status_pa' => 'AKTIF',
                'nomor_sk' => $data['nomor_sk'] ?? null,
                'tanggal_sk' => !empty($data['tanggal_sk']) ? Carbon::parse($data['tanggal_sk']) : null,
                'created_by' => Auth::id()
            ]);

            DB::commit();
            return $assignment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update/Transfer PA
     */
    public function updatePA(string $assignmentId, array $data): PembimbingAkademik
    {
        DB::beginTransaction();

        try {
            $assignment = PembimbingAkademik::findOrFail($assignmentId);

            // Jika ada perubahan dosen
            if (isset($data['id_dosen']) && $data['id_dosen'] !== $assignment->id_dosen) {
                $dosenBaru = Dosen::findOrFail($data['id_dosen']);

                // Validasi kuota dosen baru
                if (!$dosenBaru->hasAvailableQuota($assignment->id_semester)) {
                    throw new \Exception('Kuota dosen baru sudah penuh');
                }

                if (!$dosenBaru->canBePAInstance()) {
                    throw new \Exception('Dosen baru tidak dapat menjadi PA');
                }
            }

            // Update data
            $updateData = [];

            if (isset($data['id_dosen'])) {
                $updateData['id_dosen'] = $data['id_dosen'];
            }

            if (isset($data['nomor_sk'])) {
                $updateData['nomor_sk'] = $data['nomor_sk'];
            }

            if (isset($data['tanggal_sk'])) {
                $updateData['tanggal_sk'] = !empty($data['tanggal_sk'])
                    ? Carbon::parse($data['tanggal_sk'])
                    : null;
            }

            $updateData['updated_by'] = Auth::id();

            $assignment->update($updateData);

            DB::commit();
            return $assignment->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Selesaikan PA (ubah status ke SELESAI)
     */
    public function selesaikanPA(string $assignmentId): PembimbingAkademik
    {
        $assignment = PembimbingAkademik::findOrFail($assignmentId);

        $assignment->update([
            'status_pa' => 'SELESAI',
            'updated_by' => Auth::id()
        ]);

        return $assignment;
    }

    /**
     * Aktifkan kembali PA
     */
    public function aktifkanPA(string $assignmentId): PembimbingAkademik
    {
        $assignment = PembimbingAkademik::findOrFail($assignmentId);

        // Validasi kuota dosen
        if (!$assignment->dosen->hasAvailableQuota($assignment->id_semester)) {
            throw new \Exception('Kuota dosen PA sudah penuh, tidak dapat mengaktifkan kembali');
        }

        $assignment->update([
            'status_pa' => 'AKTIF',
            'updated_by' => Auth::id()
        ]);

        return $assignment;
    }

    /**
     * Get mahasiswa yang belum punya PA di semester tertentu
     */
    public function getMahasiswaBelumPA(string $semesterId, array $filters = [])
    {
        $mahasiswaWithPA = PembimbingAkademik::where('id_semester', $semesterId)
            ->where('status_pa', 'AKTIF')
            ->pluck('id_mahasiswa');

        $query = Mahasiswa::with(['pengguna', 'programStudi.jenjang'])
            ->where('status_mahasiswa', 'AKTIF')
            ->whereNotIn('id_mahasiswa', $mahasiswaWithPA);

        // Apply filters
        if (!empty($filters['program_studi_id'])) {
            $query->where('id_program_studi', $filters['program_studi_id']);
        }

        if (!empty($filters['angkatan'])) {
            $query->where('angkatan', $filters['angkatan']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhereHas('pengguna', function ($qq) use ($search) {
                        $qq->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('nim')->get();
    }

    /**
     * Get dosen yang available untuk semester tertentu
     */
    public function getDosenAvailable(string $semesterId, array $filters = [])
    {
        $query = Dosen::with(['pengguna', 'programStudi.jenjang'])
            ->canBePA();

        // Apply filters
        if (!empty($filters['program_studi_id'])) {
            $query->where('id_program_studi', $filters['program_studi_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nidn', 'like', "%{$search}%")
                    ->orWhereHas('pengguna', function ($qq) use ($search) {
                        $qq->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        return $query->get()
            ->filter(function ($dosen) use ($semesterId) {
                return $dosen->hasAvailableQuota($semesterId);
            })
            ->map(function ($dosen) use ($semesterId) {
                $statusKuota = $dosen->getStatusKuota($semesterId);
                $dosen->status_kuota = $statusKuota;
                return $dosen;
            });
    }

    /**
     * Get analytics kuota PA untuk semester tertentu
     */
    public function getKuotaAnalytics(string $semesterId): array
    {
        return PembimbingAkademik::getAnalyticsBySemester($semesterId);
    }

    /**
     * Get assignment PA dengan filter dan pagination
     */
    public function getAssignments(string $semesterId, array $filters = [], int $perPage = 20)
    {
        $query = PembimbingAkademik::with([
            'mahasiswa.pengguna',
            'mahasiswa.programStudi.jenjang',
            'dosen.pengguna',
            'semester'
        ])
            ->where('id_semester', $semesterId)
            ->where('status_pa', 'AKTIF'); // Only show active assignments

        // Apply filters
        if (!empty($filters['program_studi'])) {
            $query->byProgramStudi($filters['program_studi']);
        }

        if (!empty($filters['dosen'])) {
            $query->byDosen($filters['dosen']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa.pengguna', function ($qq) use ($search) {
                    $qq->where('nama', 'like', "%{$search}%");
                })
                    ->orWhereHas('mahasiswa', function ($qq) use ($search) {
                        $qq->where('nim', 'like', "%{$search}%");
                    })
                    ->orWhereHas('dosen.pengguna', function ($qq) use ($search) {
                        $qq->where('nama', 'like', "%{$search}%");
                    })
                    ->orWhereHas('dosen', function ($qq) use ($search) {
                        $qq->where('nidn', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Bulk assign PA
     */
    public function bulkAssignPA(array $assignments, string $semesterId): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        DB::beginTransaction();

        try {
            foreach ($assignments as $index => $assignmentData) {
                try {
                    $this->assignPA([
                        'id_mahasiswa' => $assignmentData['id_mahasiswa'],
                        'id_dosen' => $assignmentData['id_dosen'],
                        'id_semester' => $semesterId,
                        'nomor_sk' => $assignmentData['nomor_sk'] ?? null,
                        'tanggal_sk' => $assignmentData['tanggal_sk'] ?? null,
                    ]);

                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'row' => $index + 1,
                        'error' => $e->getMessage(),
                        'data' => $assignmentData
                    ];
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }

    /**
     * Get mahasiswa berdasarkan program studi untuk dropdown
     */
    public function getMahasiswaByProgramStudi(string $programStudiId, string $semesterId): array
    {
        $mahasiswa = $this->getMahasiswaBelumPA($semesterId, [
            'program_studi_id' => $programStudiId
        ]);

        return $mahasiswa->map(function ($mhs) {
            return [
                'id_mahasiswa' => $mhs->id_mahasiswa,
                'nim' => $mhs->nim,
                'nama' => $mhs->pengguna->nama,
                'angkatan' => $mhs->angkatan,
                'text' => "{$mhs->nim} - {$mhs->pengguna->nama} ({$mhs->angkatan})"
            ];
        })->toArray();
    }

    /**
     * Get dosen berdasarkan program studi untuk dropdown
     */
    public function getDosenByProgramStudi(string $programStudiId, string $semesterId): array
    {
        $dosens = $this->getDosenAvailable($semesterId, [
            'program_studi_id' => $programStudiId
        ]);

        return $dosens->map(function ($dosen) {
            return [
                'id_dosen' => $dosen->id_dosen,
                'nidn' => $dosen->nidn,
                'nama' => $dosen->pengguna->nama,
                'kuota_tersedia' => $dosen->status_kuota['tersedia'],
                'kuota_total' => $dosen->status_kuota['total'],
                'text' => "{$dosen->pengguna->nama} ({$dosen->nidn}) - Kuota: {$dosen->status_kuota['tersedia']}/{$dosen->status_kuota['total']}"
            ];
        })->toArray();
    }
}
