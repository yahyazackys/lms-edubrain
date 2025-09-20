<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dosen;
use App\Models\Semester;
use App\Models\KelasKuliah;
use App\Models\Materi;
use App\Models\SesiAbsensi;
use App\Models\Tugas;
use App\Models\Uas;
use App\Models\Uts;
use App\Services\PerhitunganNilaiAkhirService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalMengajarController extends Controller
{
    protected $gradeService;

    public function __construct(PerhitunganNilaiAkhirService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $dosen = Dosen::where('id_pengguna', $user->id_pengguna)->first();

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan!');
        }

        // Get semester yang dipilih (tidak ada default)
        $selectedSemesterId = $request->get('semester');
        $selectedSemester = null;
        $jadwalMengajar = collect();
        $jadwalPerHari = [];
        $statistikMengajar = [];

        // Hanya ambil data jika ada semester yang dipilih
        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);

            if ($selectedSemester) {
                // Ambil jadwal mengajar dosen untuk semester yang dipilih
                $jadwalMengajar = KelasKuliah::with([
                    'mataKuliah',
                    'dosen.pengguna',
                    'pesertaKelasKuliah' => function ($q) {
                        $q->where('status_mata_kuliah', 'APPROVED');
                    },
                    'pesertaKelasKuliah.registrasiMahasiswa.mahasiswa.pengguna'
                ])
                    ->where('id_dosen', $dosen->id_dosen)
                    ->where('id_semester', $selectedSemesterId)
                    ->get();

                // Group jadwal berdasarkan hari dan hitung statistik
                $jadwalPerHari = $this->groupJadwalPerHari($jadwalMengajar);
                $statistikMengajar = $this->hitungStatistikMengajar($jadwalMengajar);
            }
        }

        // Data untuk dropdown semester - urutkan dari yang terbaru
        $semesters = Semester::orderByDesc('kode_semester')->where('is_active', true)->get();

        return view('dosen.jadwal.index', compact([
            'jadwalPerHari',
            'statistikMengajar',
            'semesters',
            'selectedSemester',
            'selectedSemesterId',
            'dosen'
        ]));
    }

    private function groupJadwalPerHari($jadwalMengajar)
    {
        $jadwalPerHari = [
            'SENIN' => [],
            'SELASA' => [],
            'RABU' => [],
            'KAMIS' => [],
            'JUMAT' => [],
            'SABTU' => []
        ];

        foreach ($jadwalMengajar as $kelas) {
            $hari = $kelas->hari;
            if (isset($jadwalPerHari[$hari]) && $hari) {
                $jadwalPerHari[$hari][] = [
                    'id_kelas_kuliah' => $kelas->id_kelas_kuliah,
                    'kode_mata_kuliah' => $kelas->mataKuliah->kode_mata_kuliah,
                    'nama_mata_kuliah' => $kelas->mataKuliah->nama_mata_kuliah,
                    'nama_kelas' => $kelas->nama_kelas_kuliah,
                    'sks' => $kelas->mataKuliah->sks_mata_kuliah,
                    'jam_mulai' => $kelas->jam_mulai,
                    'jam_akhir' => $kelas->jam_akhir,
                    'ruangan' => $kelas->nama_ruangan,
                    'kapasitas' => $kelas->kapasitas,
                    'status' => $kelas->status,
                    'jumlah_mahasiswa' => $kelas->pesertaKelasKuliah->count(),
                    'kurikulum' => $kelas->kurikulumMataKuliah->kurikulum->nama_kurikulum ?? 'N/A'
                ];
            }
        }

        // Sort jadwal per hari berdasarkan jam mulai
        foreach ($jadwalPerHari as $hari => $jadwals) {
            usort($jadwalPerHari[$hari], function ($a, $b) {
                return $a['jam_mulai'] <=> $b['jam_mulai'];
            });
        }

        return $jadwalPerHari;
    }

    private function hitungStatistikMengajar($jadwalMengajar)
    {
        $totalKelas = $jadwalMengajar->count();
        $totalSks = $jadwalMengajar->sum('mataKuliah.sks_mata_kuliah');
        $totalMahasiswa = $jadwalMengajar->sum(function ($kelas) {
            return $kelas->pesertaKelasKuliah->count();
        });

        // Hitung distribusi per hari
        $distribusiHari = [];
        foreach (['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'] as $hari) {
            $distribusiHari[$hari] = $jadwalMengajar->where('hari', $hari)->count();
        }

        return [
            'total_kelas' => $totalKelas,
            'total_sks' => $totalSks,
            'total_mahasiswa' => $totalMahasiswa,
            'distribusi_hari' => $distribusiHari
        ];
    }

    public function detailKelas($kelasId)
    {
        $user = Auth::user();
        $dosen = Dosen::where('id_pengguna', $user->id_pengguna)->first();

        $kelasKuliah = KelasKuliah::with([
            'mataKuliah',
            'semester',
            'pesertaKelasKuliah' => function ($q) {
                $q->where('status_mata_kuliah', 'APPROVED');
            },
            'pesertaKelasKuliah.registrasiMahasiswa.mahasiswa.pengguna',
            'pesertaKelasKuliah.registrasiMahasiswa.mahasiswa.programStudi'
        ])
            ->where('id_kelas_kuliah', $kelasId)
            ->where('id_dosen', $dosen->id_dosen)
            ->firstOrFail();

        $materis = Materi::where('id_kelas_kuliah', $kelasId)
            ->orderBy('created_at', 'asc')
            ->get();

        $tugas = Tugas::where('id_kelas_kuliah', $kelasId)
            ->orderBy('created_at', 'asc')
            ->get();

        $uts = Uts::where('id_kelas_kuliah', $kelasId)
            ->orderBy('created_at', 'asc')
            ->get();

        $uas = Uas::where('id_kelas_kuliah', $kelasId)
            ->orderBy('created_at', 'asc')
            ->get();

        $sesiAbsensi = SesiAbsensi::where('id_kelas_kuliah', $kelasId)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('dosen.jadwal.detail-kelas', compact('kelasKuliah', 'materis', 'tugas', 'uts', 'uas', 'sesiAbsensi'));
    }

    public function updateBobotPenilaian(Request $request, $kelasId)
    {
        $user = Auth::user();
        $dosen = Dosen::where('id_pengguna', $user->id_pengguna)->first();

        $kelasKuliah = KelasKuliah::where('id_kelas_kuliah', $kelasId)
            ->where('id_dosen', $dosen->id_dosen)
            ->where('status', 'aktif')
            ->firstOrFail();

        $request->validate([
            'bobot_absensi' => 'required|integer|min:0|max:100',
            'bobot_tugas' => 'required|integer|min:0|max:100',
            'bobot_uts' => 'required|integer|min:0|max:100',
            'bobot_uas' => 'required|integer|min:0|max:100',
        ]);

        $totalBobot = $request->bobot_absensi + $request->bobot_tugas + $request->bobot_uts + $request->bobot_uas;

        if ($totalBobot !== 100) {
            return response()->json([
                'success' => false,
                'message' => 'Total bobot penilaian harus 100%. Saat ini: ' . $totalBobot . '%'
            ], 400);
        }

        $kelasKuliah->update([
            'bobot_absensi' => $request->bobot_absensi,
            'bobot_tugas' => $request->bobot_tugas,
            'bobot_uts' => $request->bobot_uts,
            'bobot_uas' => $request->bobot_uas,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bobot penilaian berhasil diperbarui'
        ]);
    }

    /**
     * Preview perhitungan nilai sebelum mengakhiri kelas
     */
    public function previewGrades(Request $request, $kelasId)
    {
        try {
            $user = Auth::user();
            $dosen = Dosen::where('id_pengguna', $user->id_pengguna)->first();

            $kelasKuliah = KelasKuliah::where('id_kelas_kuliah', $kelasId)
                ->where('id_dosen', $dosen->id_dosen)
                ->where('status', 'aktif')
                ->with(['mataKuliah'])
                ->firstOrFail();

            // Validasi kelengkapan penilaian
            $validation = $this->gradeService->validateGradingCompleteness($kelasId);

            // Hitung preview nilai (tanpa menyimpan)
            $gradeResults = $this->gradeService->calculateFinalGrades($kelasId);

            return response()->json([
                'success' => true,
                'kelas' => [
                    'nama' => $kelasKuliah->mataKuliah->nama_mata_kuliah,
                    'kode' => $kelasKuliah->mataKuliah->kode_mata_kuliah,
                    'nama_kelas' => $kelasKuliah->nama_kelas_kuliah,
                ],
                'validation' => $validation,
                'grades_preview' => $gradeResults,
                'statistics' => $this->calculateGradeStatistics($gradeResults),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in previewGrades: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan preview perhitungan nilai.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Akhiri kelas dan hitung nilai akhir
     */
    public function akhiriKelas(Request $request, $kelasId)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $dosen = Dosen::where('id_pengguna', $user->id_pengguna)->first();

            $kelasKuliah = KelasKuliah::where('id_kelas_kuliah', $kelasId)
                ->where('id_dosen', $dosen->id_dosen)
                ->where('status', 'aktif')
                ->firstOrFail();

            // Validasi input dari frontend
            $forceCalculation = $request->input('force_calculation', false);

            if (!$forceCalculation) {
                // Cek kelengkapan penilaian
                $validation = $this->gradeService->validateGradingCompleteness($kelasId);

                if (!$validation['is_complete']) {
                    return response()->json([
                        'success' => false,
                        'requires_confirmation' => true,
                        'message' => 'Beberapa penilaian belum lengkap.',
                        'warnings' => $validation['warnings'],
                    ]);
                }
            }

            // Hitung nilai akhir semua mahasiswa
            $gradeResults = $this->gradeService->calculateFinalGrades($kelasId);

            // ✅ SIMPAN NILAI KE DATABASE
            foreach ($gradeResults as $result) {
                $this->gradeService->saveGradeToDatabase(
                    $result['id_peserta'],
                    $result['nilai_akhir'],
                    $result['nilai_indeks'],
                    $result['nilai_huruf']
                );
            }

            // Update status kelas menjadi selesai
            $kelasKuliah->update(['status' => 'selesai']);

            // Log aktivitas
            Log::info("Kelas diakhiri", [
                'kelas_id' => $kelasId,
                'dosen_id' => $dosen->id_dosen,
                'total_mahasiswa' => count($gradeResults),
                'nilai_tersimpan' => count($gradeResults)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil diakhiri dan nilai akhir telah dihitung serta disimpan.',
                'data' => [
                    'total_mahasiswa' => count($gradeResults),
                    'nilai_tersimpan' => count($gradeResults),
                    'statistics' => $this->calculateGradeStatistics($gradeResults),
                    'grade_details' => $gradeResults // Optional: untuk debugging
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in akhiriKelas: ' . $e->getMessage(), [
                'kelas_id' => $kelasId,
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengakhiri kelas. Silakan coba lagi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hitung statistik nilai kelas
     */
    private function calculateGradeStatistics($gradeResults)
    {
        if (empty($gradeResults)) {
            return null;
        }

        $nilaiList = array_column($gradeResults, 'nilai_akhir');
        $hurufCount = array_count_values(array_column($gradeResults, 'nilai_huruf'));

        return [
            'total_mahasiswa' => count($gradeResults),
            'nilai_tertinggi' => max($nilaiList),
            'nilai_terendah' => min($nilaiList),
            'rata_rata' => round(array_sum($nilaiList) / count($nilaiList), 2),
            'distribusi_huruf' => $hurufCount,
            'tingkat_kelulusan' => [
                'lulus' => count(array_filter($gradeResults, fn ($r) => $r['nilai_huruf'] !== 'E')),
                'tidak_lulus' => count(array_filter($gradeResults, fn ($r) => $r['nilai_huruf'] === 'E')),
            ]
        ];
    }

    /**
     * Lihat detail perhitungan nilai mahasiswa
     */
    public function detailNilaiMahasiswa(Request $request, $kelasId, $pesertaId)
    {
        try {
            $user = Auth::user();
            $dosen = Dosen::where('id_pengguna', $user->id_pengguna)->first();

            // Validasi akses dosen
            KelasKuliah::where('id_kelas_kuliah', $kelasId)
                ->where('id_dosen', $dosen->id_dosen)
                ->firstOrFail();

            // Ambil detail nilai peserta
            $peserta = \App\Models\PesertaKelasKuliah::where('id_peserta', $pesertaId)
                ->where('id_kelas_kuliah', $kelasId)
                ->with([
                    'registrasiMahasiswa.mahasiswa.pengguna',
                    'nilaiPerkuliahan'
                ])
                ->firstOrFail();

            // Hitung ulang komponen untuk detail
            $mahasiswaId = $peserta->registrasiMahasiswa->id_mahasiswa;
            $nilaiAbsensi = $this->gradeService->calculateAttendanceGrade($kelasId, $mahasiswaId);
            $nilaiTugas = $this->gradeService->calculateAssignmentGrade($kelasId, $mahasiswaId);
            $nilaiUts = $this->gradeService->calculateUtsGrade($kelasId, $mahasiswaId);
            $nilaiUas = $this->gradeService->calculateUasGrade($kelasId, $mahasiswaId);

            return response()->json([
                'success' => true,
                'data' => [
                    'mahasiswa' => [
                        'nama' => $peserta->registrasiMahasiswa->mahasiswa->pengguna->nama,
                        'nim' => $peserta->registrasiMahasiswa->mahasiswa->nim,
                    ],
                    'komponen_nilai' => [
                        'absensi' => $nilaiAbsensi,
                        'tugas' => $nilaiTugas,
                        'uts' => $nilaiUts,
                        'uas' => $nilaiUas,
                    ],
                    'nilai_akhir' => $peserta->nilaiPerkuliahan ? [
                        'nilai_angka' => $peserta->nilaiPerkuliahan->nilai_angka,
                        'nilai_indeks' => $peserta->nilaiPerkuliahan->nilai_indeks,
                        'nilai_huruf' => $peserta->nilaiPerkuliahan->nilai_huruf,
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail nilai mahasiswa.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
