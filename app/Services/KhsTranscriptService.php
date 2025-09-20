<?php

namespace App\Services;

use App\Models\Mahasiswa;
use App\Models\Semester;
use App\Models\NilaiPerkuliahan;
use App\Models\PesertaKelasKuliah;
use Illuminate\Support\Collection;

class KhsTranscriptService
{
    /**
     * Generate KHS untuk semester tertentu
     */
    public function generateKhs(string $mahasiswaId, string $semesterId): array
    {
        $mahasiswa = Mahasiswa::with(['pengguna', 'programStudi', 'kurikulum'])
            ->findOrFail($mahasiswaId);

        $semester = Semester::findOrFail($semesterId);

        // Ambil semua mata kuliah yang diambil di semester ini dengan status APPROVED
        $mataKuliahSemester = $this->getMataKuliahBySemester($mahasiswaId, $semesterId);

        // Hitung IP semester ini
        $ipSemester = $this->hitungIpSemester($mataKuliahSemester);

        // Hitung IPK kumulatif sampai semester ini
        $ipkKumulatif = $this->hitungIpkKumulatif($mahasiswaId, $semesterId);

        // Total SKS semester ini
        $totalSksSemester = $mataKuliahSemester->sum(function ($item) {
            return $item['sks'];
        });

        return [
            'mahasiswa' => [
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->pengguna->nama,
                'program_studi' => $mahasiswa->programStudi->nama_program_studi,
                'angkatan' => $mahasiswa->angkatan,
            ],
            'semester' => [
                'kode' => $semester->kode_semester,
                'nama' => $semester->nama_semester,
            ],
            'mata_kuliah' => $mataKuliahSemester,
            'ringkasan' => [
                'total_sks_semester' => $totalSksSemester,
                'ip_semester' => round($ipSemester, 2),
                'ipk_kumulatif' => round($ipkKumulatif, 2),
            ]
        ];
    }

    /**
     * Generate Transcript lengkap
     */
    public function generateTranscript(string $mahasiswaId): array
    {
        $mahasiswa = Mahasiswa::with(['pengguna', 'programStudi', 'kurikulum'])
            ->findOrFail($mahasiswaId);

        // Ambil semua mata kuliah yang pernah diambil (APPROVED dan ada nilainya)
        $semuaMataKuliah = $this->getAllMataKuliahMahasiswa($mahasiswaId);

        // Hitung IPK keseluruhan
        $ipkKeseluruhan = $this->hitungIpkKeseluruhan($semuaMataKuliah);

        // Total SKS yang sudah diambil
        $totalSksLulus = $semuaMataKuliah->sum('sks');

        // Batas SKS dari kurikulum
        $batasSksKurikulum = $mahasiswa->kurikulum->jumlah_sks_lulus ?? 0;

        return [
            'mahasiswa' => [
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->pengguna->nama,
                'program_studi' => $mahasiswa->programStudi->nama_program_studi,
                'angkatan' => $mahasiswa->angkatan,
                'kurikulum' => $mahasiswa->kurikulum->nama_kurikulum ?? '-',
            ],
            'mata_kuliah' => $semuaMataKuliah,
            'ringkasan' => [
                'total_sks_lulus' => $totalSksLulus,
                'batas_sks_kurikulum' => $batasSksKurikulum,
                'persentase_kelulusan' => $batasSksKurikulum > 0 ? round(($totalSksLulus / $batasSksKurikulum) * 100, 2) : 0,
                'ipk_keseluruhan' => round($ipkKeseluruhan, 2),
                'status_kelulusan' => $totalSksLulus >= $batasSksKurikulum ? 'LULUS' : 'BELUM LULUS',
            ]
        ];
    }

    /**
     * Ambil mata kuliah mahasiswa di semester tertentu
     */
    private function getMataKuliahBySemester(string $mahasiswaId, string $semesterId): Collection
    {
        return NilaiPerkuliahan::with([
            'pesertaKelasKuliah.kelasKuliah.kurikulumMataKuliah.mataKuliah',
            'pesertaKelasKuliah.kelasKuliah.semester',
            'pesertaKelasKuliah.registrasiMahasiswa'
        ])
            ->whereHas('pesertaKelasKuliah', function ($query) use ($mahasiswaId, $semesterId) {
                $query->where('status_mata_kuliah', 'APPROVED')
                    ->whereHas('kelasKuliah', function ($q) use ($semesterId) {
                        $q->where('id_semester', $semesterId)
                            ->where('status', 'selesai'); // Kelas sudah selesai
                    })
                    ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswaId) {
                        $q->where('id_mahasiswa', $mahasiswaId);
                    });
            })
            ->whereNotNull('nilai_indeks') // Sudah ada nilai
            ->get()
            ->map(function ($nilai) {
                $peserta = $nilai->pesertaKelasKuliah;
                $mataKuliah = $peserta->kelasKuliah->kurikulumMataKuliah->mataKuliah;

                return [
                    'kode_mata_kuliah' => $mataKuliah->kode_mata_kuliah,
                    'nama_mata_kuliah' => $mataKuliah->nama_mata_kuliah,
                    'sks' => $mataKuliah->sks_mata_kuliah,
                    'jenis' => $peserta->kelasKuliah->kurikulumMataKuliah->jenis_mata_kuliah,
                    'nilai_angka' => $nilai->nilai_angka,
                    'nilai_huruf' => $nilai->nilai_huruf,
                    'nilai_indeks' => $nilai->nilai_indeks,
                    'mutu' => $nilai->nilai_indeks * $mataKuliah->sks_mata_kuliah, // SKS x Indeks
                ];
            });
    }

    /**
     * Ambil semua mata kuliah mahasiswa yang sudah lulus
     */
    private function getAllMataKuliahMahasiswa(string $mahasiswaId): Collection
    {
        return NilaiPerkuliahan::with([
            'pesertaKelasKuliah.kelasKuliah.kurikulumMataKuliah.mataKuliah',
            'pesertaKelasKuliah.kelasKuliah.semester',
            'pesertaKelasKuliah.registrasiMahasiswa'
        ])
            ->whereHas('pesertaKelasKuliah', function ($query) use ($mahasiswaId) {
                $query->where('status_mata_kuliah', 'APPROVED')
                    ->whereHas('kelasKuliah', function ($q) {
                        $q->where('status', 'selesai'); // Kelas sudah selesai
                    })
                    ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswaId) {
                        $q->where('id_mahasiswa', $mahasiswaId);
                    });
            })
            ->whereNotNull('nilai_indeks') // Sudah ada nilai
            ->where('nilai_huruf', '!=', 'E') // Tidak termasuk nilai E (tidak lulus)
            ->get()
            ->map(function ($nilai) {
                $peserta = $nilai->pesertaKelasKuliah;
                $mataKuliah = $peserta->kelasKuliah->kurikulumMataKuliah->mataKuliah;
                $semester = $peserta->kelasKuliah->semester;

                return [
                    'kode_mata_kuliah' => $mataKuliah->kode_mata_kuliah,
                    'nama_mata_kuliah' => $mataKuliah->nama_mata_kuliah,
                    'sks' => $mataKuliah->sks_mata_kuliah,
                    'jenis' => $peserta->kelasKuliah->kurikulumMataKuliah->jenis_mata_kuliah,
                    'semester_diambil' => $semester->nama_semester,
                    'nilai_angka' => $nilai->nilai_angka,
                    'nilai_huruf' => $nilai->nilai_huruf,
                    'nilai_indeks' => $nilai->nilai_indeks,
                    'mutu' => $nilai->nilai_indeks * $mataKuliah->sks_mata_kuliah,
                ];
            });
    }

    /**
     * Hitung IP semester
     */
    private function hitungIpSemester(Collection $mataKuliah): float
    {
        if ($mataKuliah->isEmpty()) {
            return 0;
        }

        $totalMutu = $mataKuliah->sum('mutu');
        $totalSks = $mataKuliah->sum('sks');

        return $totalSks > 0 ? $totalMutu / $totalSks : 0;
    }

    /**
     * Hitung IPK kumulatif sampai semester tertentu
     */
    private function hitungIpkKumulatif(string $mahasiswaId, string $semesterId): float
    {
        // Ambil semua semester sampai semester yang diminta (berdasarkan kode semester)
        $semesterTarget = Semester::find($semesterId);
        if (!$semesterTarget) return 0;

        $allNilai = NilaiPerkuliahan::with([
            'pesertaKelasKuliah.kelasKuliah.kurikulumMataKuliah.mataKuliah',
            'pesertaKelasKuliah.kelasKuliah.semester',
            'pesertaKelasKuliah.registrasiMahasiswa'
        ])
            ->whereHas('pesertaKelasKuliah', function ($query) use ($mahasiswaId, $semesterTarget) {
                $query->where('status_mata_kuliah', 'APPROVED')
                    ->whereHas('kelasKuliah', function ($q) use ($semesterTarget) {
                        $q->where('status', 'selesai')
                            ->whereHas('semester', function ($s) use ($semesterTarget) {
                                $s->where('kode_semester', '<=', $semesterTarget->kode_semester);
                            });
                    })
                    ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswaId) {
                        $q->where('id_mahasiswa', $mahasiswaId);
                    });
            })
            ->whereNotNull('nilai_indeks')
            ->where('nilai_huruf', '!=', 'E')
            ->get();

        if ($allNilai->isEmpty()) {
            return 0;
        }

        $totalMutu = 0;
        $totalSks = 0;

        foreach ($allNilai as $nilai) {
            $sks = $nilai->pesertaKelasKuliah->kelasKuliah->kurikulumMataKuliah->mataKuliah->sks_mata_kuliah;
            $totalMutu += $nilai->nilai_indeks * $sks;
            $totalSks += $sks;
        }

        return $totalSks > 0 ? $totalMutu / $totalSks : 0;
    }

    /**
     * Hitung IPK keseluruhan
     */
    private function hitungIpkKeseluruhan(Collection $mataKuliah): float
    {
        if ($mataKuliah->isEmpty()) {
            return 0;
        }

        $totalMutu = $mataKuliah->sum('mutu');
        $totalSks = $mataKuliah->sum('sks');

        return $totalSks > 0 ? $totalMutu / $totalSks : 0;
    }

    /**
     * Get daftar semester yang pernah diambil mahasiswa
     */
    public function getSemesterMahasiswa(string $mahasiswaId): Collection
    {
        return Semester::whereHas('kelasKuliah.pesertaKelasKuliah', function ($query) use ($mahasiswaId) {
            $query->where('status_mata_kuliah', 'APPROVED')
                ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswaId) {
                    $q->where('id_mahasiswa', $mahasiswaId);
                });
        })
            ->orderBy('kode_semester')
            ->get(['id_semester', 'kode_semester', 'nama_semester']);
    }
}