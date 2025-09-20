<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RegistrasiMahasiswa;
use App\Models\PesertaKelasKuliah;
use App\Models\Semester;
use App\Models\ProgramStudi;
use App\Models\Mahasiswa;
use App\Models\KelasKuliah;
use App\Models\Dosen;
use App\Models\PembimbingAkademik;

class KrsAdminController extends Controller
{
    /**
     * Dashboard monitoring KRS
     */
    public function index(Request $request)
    {
        // Get semester yang dipilih
        $selectedSemesterId = $request->get('semester');
        $selectedSemester = null;

        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);
        } else {
            $selectedSemester = Semester::where('is_active', true)->first();
            $selectedSemesterId = $selectedSemester->id_semester ?? null;
        }

        // Data untuk dropdown
        $semesters = Semester::orderByDesc('tanggal_mulai')->get();
        $programStudis = ProgramStudi::with('jenjang')->orderBy('nama_program_studi')->get();

        if (!$selectedSemester) {
            return view('admin.krs.index', compact('semesters', 'programStudis', 'selectedSemester'));
        }

        // Statistik umum
        $statistikUmum = $this->getStatistikUmum($selectedSemesterId);

        // Statistik per program studi
        $statistikProdi = $this->getStatistikPerProgramStudi($selectedSemesterId);

        // Statistik per PA
        $statistikPA = $this->getStatistikPerPA($selectedSemesterId);

        // Progress KRS per hari
        $progressHarian = $this->getProgressHarian($selectedSemesterId);

        // Top mata kuliah yang paling dipilih
        $topMataKuliah = $this->getTopMataKuliah($selectedSemesterId);

        return view('admin.krs.index', compact([
            'semesters',
            'programStudis',
            'selectedSemester',
            'statistikUmum',
            'statistikProdi',
            'statistikPA',
            'progressHarian',
            'topMataKuliah'
        ]));
    }

    /**
     * Laporan detail KRS
     */
    public function laporan(Request $request)
    {
        // Get semester yang dipilih
        $selectedSemesterId = $request->get('semester');
        $selectedSemester = null;
        $laporanData = collect();

        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);
        } else {
            $selectedSemester = Semester::where('is_active', true)->first();
            $selectedSemesterId = $selectedSemester->id_semester ?? null;
        }

        if ($selectedSemester) {
            // Query dasar
            $query = RegistrasiMahasiswa::with([
                'mahasiswa.pengguna',
                'mahasiswa.programStudi.jenjang',
                'semester',
                'pembimbingAkademik.dosen.pengguna',
                'pesertaKelasKuliah.mataKuliah'
            ])->where('id_semester', $selectedSemesterId);

            // Filter berdasarkan program studi
            if ($request->has('program_studi') && $request->program_studi != '') {
                $query->whereHas('mahasiswa', function ($q) use ($request) {
                    $q->where('id_program_studi', $request->program_studi);
                });
            }

            // Filter berdasarkan status
            if ($request->has('status') && $request->status != '') {
                $query->where('status_krs', $request->status);
            }

            // Filter berdasarkan PA
            if ($request->has('pembimbing_akademik') && $request->pembimbing_akademik != '') {
                $query->where('id_pembimbing_akademik', $request->pembimbing_akademik);
            }

            // Pencarian mahasiswa
            if ($request->has('search') && $request->search != '') {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHas('mahasiswa.pengguna', function ($mq) use ($searchTerm) {
                        $mq->where('nama', 'LIKE', "%{$searchTerm}%");
                    })
                        ->orWhereHas('mahasiswa', function ($mq) use ($searchTerm) {
                            $mq->where('nim', 'LIKE', "%{$searchTerm}%");
                        });
                });
            }

            // Sorting
            $sortBy = $request->get('sort', 'created_at');
            $sortDir = $request->get('direction', 'desc');

            switch ($sortBy) {
                case 'nama':
                    $query->join('mahasiswa', 'registrasi_mahasiswa.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
                        ->join('pengguna', 'mahasiswa.id_pengguna', '=', 'pengguna.id_pengguna')
                        ->orderBy('pengguna.nama', $sortDir)
                        ->select('registrasi_mahasiswa.*');
                    break;
                case 'nim':
                    $query->join('mahasiswa', 'registrasi_mahasiswa.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
                        ->orderBy('mahasiswa.nim', $sortDir)
                        ->select('registrasi_mahasiswa.*');
                    break;
                case 'tanggal_submit':
                    $query->orderBy('tanggal_submit', $sortDir);
                    break;
                case 'tanggal_approval':
                    $query->orderBy('tanggal_approval', $sortDir);
                    break;
                default:
                    $query->orderBy($sortBy, $sortDir);
            }

            $laporanData = $query->paginate(20);

            // Hitung total SKS untuk setiap mahasiswa
            foreach ($laporanData as $registrasi) {
                $registrasi->total_sks = $registrasi->pesertaKelasKuliah
                    ->where('status_mata_kuliah', '!=', 'REJECTED')
                    ->sum(function ($peserta) {
                        return $peserta->mataKuliah->sks_mata_kuliah;
                    });
            }
        }

        // Data untuk dropdown
        $semesters = Semester::orderByDesc('tanggal_mulai')->get();
        $programStudis = ProgramStudi::with('jenjang')->orderBy('nama_program_studi')->get();
        $pembimbingAkademiks = [];

        if ($selectedSemesterId) {
            $pembimbingAkademiks = PembimbingAkademik::with('dosen.pengguna')
                ->where('id_semester', $selectedSemesterId)
                ->where('status_pa', 'AKTIF')
                ->get();
        }

        return view('admin.krs.laporan', compact([
            'laporanData',
            'semesters',
            'programStudis',
            'pembimbingAkademiks',
            'selectedSemester',
            'selectedSemesterId'
        ]));
    }

    /**
     * Aktivasi mass KRS (ubah status dari APPROVED ke ACTIVE)
     */
    public function aktivasiMassKrs(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semester,id_semester',
            'program_studi_ids' => 'nullable|array',
            'program_studi_ids.*' => 'exists:program_studi,id_program_studi'
        ]);

        try {
            DB::beginTransaction();

            $query = RegistrasiMahasiswa::where('id_semester', $request->semester_id)
                ->where('status_krs', 'APPROVED');

            // Filter per program studi jika dipilih
            if ($request->has('program_studi_ids') && !empty($request->program_studi_ids)) {
                $query->whereHas('mahasiswa', function ($q) use ($request) {
                    $q->whereIn('id_program_studi', $request->program_studi_ids);
                });
            }

            $jumlahDiaktivasi = $query->update(['status_krs' => 'ACTIVE']);

            DB::commit();

            return redirect()->back()->with(
                'success',
                "Berhasil mengaktivasi {$jumlahDiaktivasi} KRS mahasiswa."
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with(
                'error',
                'Terjadi kesalahan saat aktivasi KRS: ' . $e->getMessage()
            );
        }
    }

    /**
     * Export laporan ke Excel/CSV
     */
    public function export(Request $request)
    {
        $request->validate([
            'semester' => 'required|exists:semester,id_semester',
            'format' => 'required|in:excel,csv,pdf'
        ]);

        // TODO: Implement export functionality
        // Bisa menggunakan package seperti Laravel Excel atau barryvdh/laravel-dompdf

        return redirect()->back()->with('info', 'Fitur export sedang dalam pengembangan.');
    }

    /**
     * Detail KRS mahasiswa untuk admin
     */
    public function detailMahasiswa($registrasiId)
    {
        $registrasiMahasiswa = RegistrasiMahasiswa::with([
            'mahasiswa.pengguna',
            'mahasiswa.programStudi.jenjang',
            'mahasiswa.kurikulum',
            'semester',
            'pembimbingAkademik.dosen.pengguna',
            'pesertaKelasKuliah' => function ($query) {
                $query->with([
                    'mataKuliah',
                    'kelasKuliah.dosen.pengguna',
                    'kelasKuliah.kurikulumMataKuliah'
                ]);
            }
        ])->findOrFail($registrasiId);

        // Hitung total SKS
        $totalSks = $registrasiMahasiswa->pesertaKelasKuliah
            ->where('status_mata_kuliah', '!=', 'REJECTED')
            ->sum(function ($peserta) {
                return $peserta->mataKuliah->sks_mata_kuliah;
            });

        // Generate jadwal mingguan
        $jadwalMingguan = $this->generateJadwalMingguan($registrasiMahasiswa->pesertaKelasKuliah);

        // Get timeline aktivitas KRS
        $timeline = $this->getTimelineKrs($registrasiMahasiswa);

        return view('admin.krs.detail', compact([
            'registrasiMahasiswa',
            'totalSks',
            'jadwalMingguan',
            'timeline'
        ]));
    }

    /**
     * Statistik kelas kuliah dan kapasitas
     */
    public function statistikKelas(Request $request)
    {
        $selectedSemesterId = $request->get('semester');
        $selectedSemester = null;

        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);
        } else {
            $selectedSemester = Semester::where('is_active', true)->first();
            $selectedSemesterId = $selectedSemester->id_semester ?? null;
        }

        $statistikKelas = [];

        if ($selectedSemester) {
            $query = KelasKuliah::with([
                'kurikulumMataKuliah.mataKuliah',
                'kurikulumMataKuliah.kurikulum.programStudi',
                'dosen.pengguna'
            ])
                ->where('id_semester', $selectedSemesterId);

            // Filter program studi jika dipilih
            if ($request->has('program_studi') && $request->program_studi != '') {
                $query->whereHas('kurikulumMataKuliah.kurikulum', function ($q) use ($request) {
                    $q->where('id_program_studi', $request->program_studi);
                });
            }

            $kelasKuliahs = $query->get();

            foreach ($kelasKuliahs as $kelas) {
                $jumlahPeserta = PesertaKelasKuliah::where('id_kelas_kuliah', $kelas->id_kelas_kuliah)
                    ->where('status_mata_kuliah', '!=', 'REJECTED')
                    ->count();

                $persentaseKapasitas = ($jumlahPeserta / $kelas->kapasitas) * 100;

                $statistikKelas[] = [
                    'id_kelas_kuliah' => $kelas->id_kelas_kuliah,
                    'nama_kelas' => $kelas->nama_kelas_kuliah,
                    'mata_kuliah' => $kelas->kurikulumMataKuliah->mataKuliah->nama_mata_kuliah,
                    'kode_mata_kuliah' => $kelas->kurikulumMataKuliah->mataKuliah->kode_mata_kuliah,
                    'program_studi' => $kelas->kurikulumMataKuliah->kurikulum->programStudi->nama_program_studi,
                    'dosen' => $kelas->dosen->pengguna->nama ?? 'N/A',
                    'kapasitas' => $kelas->kapasitas,
                    'jumlah_peserta' => $jumlahPeserta,
                    'sisa_kapasitas' => $kelas->kapasitas - $jumlahPeserta,
                    'persentase_kapasitas' => round($persentaseKapasitas, 1),
                    'status_kapasitas' => $this->getStatusKapasitas($persentaseKapasitas)
                ];
            }

            // Sort berdasarkan persentase kapasitas descending
            usort($statistikKelas, function ($a, $b) {
                return $b['persentase_kapasitas'] <=> $a['persentase_kapasitas'];
            });
        }

        // Data untuk dropdown
        $semesters = Semester::orderByDesc('tanggal_mulai')->get();
        $programStudis = ProgramStudi::with('jenjang')->orderBy('nama_program_studi')->get();

        return view('admin.krs.statistik-kelas', compact([
            'statistikKelas',
            'semesters',
            'programStudis',
            'selectedSemester'
        ]));
    }

    /**
     * Helper Methods
     */

    /**
     * Get statistik umum KRS
     */
    private function getStatistikUmum($semesterId)
    {
        $totalMahasiswa = RegistrasiMahasiswa::where('id_semester', $semesterId)->count();

        $submitted = RegistrasiMahasiswa::where('id_semester', $semesterId)
            ->whereNotNull('tanggal_submit')->count();

        $approved = RegistrasiMahasiswa::where('id_semester', $semesterId)
            ->where('status_krs', 'APPROVED')->count();

        $active = RegistrasiMahasiswa::where('id_semester', $semesterId)
            ->where('status_krs', 'ACTIVE')->count();

        $avgSks = DB::table('registrasi_mahasiswa as rm')
            ->join('peserta_kelas_kuliah as pkk', 'rm.id_registrasi_mahasiswa', '=', 'pkk.id_registrasi_mahasiswa')
            ->join('mata_kuliah as mk', 'pkk.id_mata_kuliah', '=', 'mk.id_mata_kuliah')
            ->where('rm.id_semester', $semesterId)
            ->where('pkk.status_mata_kuliah', '!=', 'REJECTED')
            ->select('rm.id_registrasi_mahasiswa', DB::raw('SUM(mk.sks_mata_kuliah) as total_sks'))
            ->groupBy('rm.id_registrasi_mahasiswa')
            ->avg('total_sks');

        return [
            'total_mahasiswa' => $totalMahasiswa,
            'submitted' => $submitted,
            'approved' => $approved,
            'active' => $active,
            'pending' => $submitted - $approved,
            'belum_submit' => $totalMahasiswa - $submitted,
            'avg_sks' => round($avgSks ?? 0, 1)
        ];
    }

    /**
     * Get statistik per program studi
     */
    private function getStatistikPerProgramStudi($semesterId)
    {
        return DB::table('registrasi_mahasiswa as rm')
            ->join('mahasiswa as m', 'rm.id_mahasiswa', '=', 'm.id_mahasiswa')
            ->join('program_studi as ps', 'm.id_program_studi', '=', 'ps.id_program_studi')
            ->join('jenjang_pendidikan as jp', 'ps.id_jenjang_pendidikan', '=', 'jp.id_jenjang_pendidikan')
            ->where('rm.id_semester', $semesterId)
            ->select([
                'ps.nama_program_studi',
                'jp.kode_jenjang_pendidikan',
                DB::raw('COUNT(*) as total_mahasiswa'),
                DB::raw('SUM(CASE WHEN rm.tanggal_submit IS NOT NULL THEN 1 ELSE 0 END) as submitted'),
                DB::raw('SUM(CASE WHEN rm.status_krs = "APPROVED" THEN 1 ELSE 0 END) as approved'),
                DB::raw('SUM(CASE WHEN rm.status_krs = "ACTIVE" THEN 1 ELSE 0 END) as active')
            ])
            ->groupBy('ps.id_program_studi', 'ps.nama_program_studi', 'jp.kode_jenjang_pendidikan')
            ->orderBy('ps.nama_program_studi')
            ->get();
    }

    /**
     * Get statistik per PA
     */
    private function getStatistikPerPA($semesterId)
    {
        return DB::table('registrasi_mahasiswa as rm')
            ->join('pembimbing_akademik as pa', 'rm.id_pembimbing_akademik', '=', 'pa.id_pembimbing_akademik')
            ->join('dosen as d', 'pa.id_dosen', '=', 'd.id_dosen')
            ->join('pengguna as p', 'd.id_pengguna', '=', 'p.id_pengguna')
            ->where('rm.id_semester', $semesterId)
            ->select([
                'p.nama as nama_dosen',
                'd.nidn',
                DB::raw('COUNT(*) as total_mahasiswa'),
                DB::raw('SUM(CASE WHEN rm.tanggal_submit IS NOT NULL THEN 1 ELSE 0 END) as submitted'),
                DB::raw('SUM(CASE WHEN rm.status_krs = "APPROVED" THEN 1 ELSE 0 END) as approved')
            ])
            ->groupBy('pa.id_dosen', 'p.nama', 'd.nidn')
            ->orderBy('p.nama')
            ->get();
    }

    /**
     * Get progress KRS per hari
     */
    private function getProgressHarian($semesterId)
    {
        return DB::table('registrasi_mahasiswa')
            ->where('id_semester', $semesterId)
            ->whereNotNull('tanggal_submit')
            ->select([
                DB::raw('DATE(tanggal_submit) as tanggal'),
                DB::raw('COUNT(*) as jumlah_submit')
            ])
            ->groupBy(DB::raw('DATE(tanggal_submit)'))
            ->orderBy('tanggal')
            ->limit(30)
            ->get();
    }

    /**
     * Get top mata kuliah yang paling dipilih
     */
    private function getTopMataKuliah($semesterId)
    {
        return DB::table('peserta_kelas_kuliah as pkk')
            ->join('registrasi_mahasiswa as rm', 'pkk.id_registrasi_mahasiswa', '=', 'rm.id_registrasi_mahasiswa')
            ->join('mata_kuliah as mk', 'pkk.id_mata_kuliah', '=', 'mk.id_mata_kuliah')
            ->where('rm.id_semester', $semesterId)
            ->where('pkk.status_mata_kuliah', '!=', 'REJECTED')
            ->select([
                'mk.kode_mata_kuliah',
                'mk.nama_mata_kuliah',
                'mk.sks_mata_kuliah',
                DB::raw('COUNT(*) as jumlah_peminat')
            ])
            ->groupBy('mk.id_mata_kuliah', 'mk.kode_mata_kuliah', 'mk.nama_mata_kuliah', 'mk.sks_mata_kuliah')
            ->orderByDesc('jumlah_peminat')
            ->limit(10)
            ->get();
    }

    /**
     * Generate jadwal mingguan
     */
    private function generateJadwalMingguan($pesertaKelasKuliahs)
    {
        $jadwal = [];
        $hariList = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];

        foreach ($hariList as $hari) {
            $jadwal[$hari] = [];
        }

        foreach ($pesertaKelasKuliahs as $peserta) {
            if ($peserta->status_mata_kuliah === 'REJECTED') continue;

            $kelas = $peserta->kelasKuliah;

            if ($kelas && $kelas->hari && $kelas->jam_mulai && $kelas->jam_akhir) {
                $jadwal[$kelas->hari][] = [
                    'mata_kuliah' => $peserta->mataKuliah->nama_mata_kuliah,
                    'kode_mata_kuliah' => $peserta->mataKuliah->kode_mata_kuliah,
                    'kelas' => $kelas->nama_kelas_kuliah,
                    'ruangan' => $kelas->nama_ruangan,
                    'dosen' => $kelas->dosen->pengguna->nama ?? 'N/A',
                    'jam_mulai' => $kelas->jam_mulai,
                    'jam_akhir' => $kelas->jam_akhir,
                    'sks' => $peserta->mataKuliah->sks_mata_kuliah,
                    'status' => $peserta->status_mata_kuliah
                ];
            }
        }

        // Sort by jam_mulai for each day
        foreach ($jadwal as $hari => $classes) {
            usort($jadwal[$hari], function ($a, $b) {
                return strcmp($a['jam_mulai'], $b['jam_mulai']);
            });
        }

        return $jadwal;
    }

    /**
     * Get timeline aktivitas KRS
     */
    private function getTimelineKrs($registrasiMahasiswa)
    {
        $timeline = [];

        $timeline[] = [
            'tanggal' => $registrasiMahasiswa->created_at,
            'aktivitas' => 'KRS dibuat',
            'keterangan' => 'Mahasiswa mulai menyusun KRS',
            'icon' => 'fas fa-plus-circle',
            'color' => 'blue'
        ];

        if ($registrasiMahasiswa->tanggal_submit) {
            $timeline[] = [
                'tanggal' => $registrasiMahasiswa->tanggal_submit,
                'aktivitas' => 'KRS disubmit ke PA',
                'keterangan' => 'KRS diserahkan kepada ' . ($registrasiMahasiswa->pembimbingAkademik->dosen->pengguna->nama ?? 'Pembimbing Akademik'),
                'icon' => 'fas fa-paper-plane',
                'color' => 'yellow'
            ];
        }

        if ($registrasiMahasiswa->tanggal_approval) {
            $timeline[] = [
                'tanggal' => $registrasiMahasiswa->tanggal_approval,
                'aktivitas' => 'KRS disetujui PA',
                'keterangan' => 'KRS telah disetujui dan siap diaktivasi',
                'icon' => 'fas fa-check-circle',
                'color' => 'green'
            ];
        }

        if ($registrasiMahasiswa->status_krs === 'ACTIVE') {
            $timeline[] = [
                'tanggal' => $registrasiMahasiswa->updated_at,
                'aktivitas' => 'KRS diaktivasi',
                'keterangan' => 'KRS telah aktif, mahasiswa dapat mengikuti perkuliahan',
                'icon' => 'fas fa-play-circle',
                'color' => 'green'
            ];
        }

        return collect($timeline)->sortBy('tanggal');
    }

    /**
     * Get status kapasitas kelas
     */
    private function getStatusKapasitas($persentase)
    {
        if ($persentase >= 100) {
            return 'penuh';
        } elseif ($persentase >= 80) {
            return 'hampir_penuh';
        } elseif ($persentase >= 50) {
            return 'sedang';
        } else {
            return 'kosong';
        }
    }
}
