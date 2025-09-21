<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\ProgramStudi;
use App\Models\KelasKuliah;
use App\Models\Semester;
use App\Models\RegistrasiMahasiswa;
use App\Models\PesertaKelasKuliah;
use App\Models\SesiAbsensi;
use App\Models\Tugas;
use App\Models\Uts;
use App\Models\Uas;
use App\Models\PengumpulanTugas;
use App\Models\PengumpulanUts;
use App\Models\PengumpulanUas;
use App\Models\NilaiPerkuliahan;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return $this->adminDashboard($request);
            case 'dosen':
                return $this->dosenDashboard($request);
            case 'mahasiswa':
                return $this->mahasiswaDashboard($request);
            default:
                abort(403, 'Role tidak dikenal');
        }
    }

    private function adminDashboard(Request $request)
    {
        // Get all semesters for filter
        $semesters = Semester::orderBy('id_semester', 'desc')->get();
        $selectedSemester = null;

        if ($request->has('semester_id') && $request->semester_id) {
            $selectedSemester = Semester::find($request->semester_id);
        }

        // General Statistics Cards (not semester dependent)
        $stats = [
            'total_mahasiswa' => Mahasiswa::where('status_mahasiswa', 'AKTIF')->count(),
            'total_dosen' => Dosen::where('status_dosen', 'AKTIF')->count(),
            'total_program_studi' => ProgramStudi::where('status', 'A')->count(),
            'total_kelas_semester' => $selectedSemester ?
                KelasKuliah::where('id_semester', $selectedSemester->id_semester)
                ->where('status', 'AKTIF')
                ->count() : 0
        ];

        // Distribusi mahasiswa per program studi (general, not semester dependent)
        $distribusiMahasiswa = ProgramStudi::with('jenjang')
            ->withCount(['mahasiswa' => function ($query) {
                $query->where('status_mahasiswa', 'AKTIF');
            }])
            ->where('status', 'A')
            ->get()
            ->map(function ($prodi) {
                return [
                    'name' => $prodi->nama_program_studi . ' (' . $prodi->jenjang->kode_jenjang_pendidikan . ')',
                    'value' => $prodi->mahasiswa_count
                ];
            });

        // Status KRS untuk semester yang dipilih
        $statusKrs = collect();
        if ($selectedSemester) {
            $statusKrs = RegistrasiMahasiswa::where('id_semester', $selectedSemester->id_semester)
                ->select('status_krs', DB::raw('count(*) as total'))
                ->groupBy('status_krs')
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $this->getStatusKrsLabel($item->status_krs),
                        'value' => $item->total
                    ];
                });
        }

        // Recent Activities - KRS pending approval (untuk semester yang dipilih)
        $pendingKrs = collect();
        if ($selectedSemester) {
            $pendingKrs = RegistrasiMahasiswa::with(['mahasiswa.pengguna', 'mahasiswa.programStudi'])
                ->where('id_semester', $selectedSemester->id_semester)
                ->where('status_krs', 'SUBMITTED')
                ->orderBy('tanggal_submit', 'desc')
                ->take(5)
                ->get();
        }

        // Users need activation (general)
        $pendingUsers = Pengguna::where('is_active', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Kelas tanpa dosen (untuk semester yang dipilih)
        $kelasWithoutDosen = collect();
        if ($selectedSemester) {
            $kelasWithoutDosen = KelasKuliah::with(['kurikulumMataKuliah.mataKuliah'])
                ->where('id_semester', $selectedSemester->id_semester)
                ->whereNull('id_dosen')
                ->where('status', 'AKTIF')
                ->take(5)
                ->get();
        }

        return view('dashboard.admin', compact(
            'stats',
            'distribusiMahasiswa',
            'statusKrs',
            'pendingKrs',
            'pendingUsers',
            'kelasWithoutDosen',
            'semesters',
            'selectedSemester'
        ));
    }

    private function dosenDashboard(Request $request)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            abort(403, 'Data dosen tidak ditemukan');
        }

        // Get all semesters for filter
        $semesters = Semester::orderBy('id_semester', 'desc')->get();
        $selectedSemester = null;

        if ($request->has('semester_id') && $request->semester_id) {
            $selectedSemester = Semester::find($request->semester_id);
        }

        // Statistics Personal
        $stats = [
            'total_kelas_all' => KelasKuliah::where('id_dosen', $dosen->id_dosen)
                ->where('status', 'AKTIF')
                ->count(),
            'total_kelas_semester' => $selectedSemester ?
                KelasKuliah::where('id_dosen', $dosen->id_dosen)
                ->where('id_semester', $selectedSemester->id_semester)
                ->where('status', 'AKTIF')
                ->count() : 0,
            'total_mahasiswa_bimbingan' => $selectedSemester ?
                $dosen->getMahasiswaBimbingan($selectedSemester->id_semester)->count() : 0,
            'tugas_pending' => 0
        ];

        // Total peserta dan tugas pending untuk semester yang dipilih
        if ($selectedSemester) {
            $kelasIds = KelasKuliah::where('id_dosen', $dosen->id_dosen)
                ->where('id_semester', $selectedSemester->id_semester)
                ->where('status', 'AKTIF')
                ->pluck('id_kelas_kuliah');

            $stats['total_peserta_kelas'] = PesertaKelasKuliah::whereIn('id_kelas_kuliah', $kelasIds)
                ->whereHas('registrasiMahasiswa', function ($query) {
                    $query->where('status_krs', 'APPROVED');
                })
                ->where('status_mata_kuliah', 'SELECTED')
                ->count();

            // Tugas/ujian pending penilaian
            $pendingTugas = PengumpulanTugas::whereHas('tugas.kelasKuliah', function ($query) use ($dosen, $selectedSemester) {
                $query->where('id_dosen', $dosen->id_dosen)
                    ->where('id_semester', $selectedSemester->id_semester);
            })
                ->whereNull('nilai')
                ->count();

            $pendingUts = PengumpulanUts::whereHas('uts.kelasKuliah', function ($query) use ($dosen, $selectedSemester) {
                $query->where('id_dosen', $dosen->id_dosen)
                    ->where('id_semester', $selectedSemester->id_semester);
            })
                ->whereNull('nilai')
                ->count();

            $pendingUas = PengumpulanUas::whereHas('uas.kelasKuliah', function ($query) use ($dosen, $selectedSemester) {
                $query->where('id_dosen', $dosen->id_dosen)
                    ->where('id_semester', $selectedSemester->id_semester);
            })
                ->whereNull('nilai')
                ->count();

            $stats['tugas_pending'] = $pendingTugas + $pendingUts + $pendingUas;
        } else {
            $stats['total_peserta_kelas'] = 0;
        }

        // Jadwal hari ini (dari semester yang dipilih atau semua jika tidak ada filter)
        $today = now()->format('l');
        $hariIndonesia = $this->convertDayToIndonesian($today);

        $jadwalQuery = KelasKuliah::with(['kurikulumMataKuliah.mataKuliah'])
            ->where('id_dosen', $dosen->id_dosen)
            ->where('hari', $hariIndonesia)
            ->where('status', 'AKTIF')
            ->orderBy('jam_mulai');

        if ($selectedSemester) {
            $jadwalQuery->where('id_semester', $selectedSemester->id_semester);
        }

        $jadwalHariIni = $jadwalQuery->get();

        // Data semester-dependent lainnya
        $sesiAbsensiTerbuka = collect();
        $deadlinesTerdekat = collect();
        $mahasiswaBelumKrs = collect();

        if ($selectedSemester) {
            // Sesi absensi yang masih terbuka
            $sesiAbsensiTerbuka = SesiAbsensi::with(['kelasKuliah.kurikulumMataKuliah.mataKuliah'])
                ->whereHas('kelasKuliah', function ($query) use ($dosen, $selectedSemester) {
                    $query->where('id_dosen', $dosen->id_dosen)
                        ->where('id_semester', $selectedSemester->id_semester);
                })
                ->where('status', 'dibuka')
                ->where('batas_akhir_absensi', '>', now())
                ->orderBy('batas_akhir_absensi')
                ->take(5)
                ->get();

            // Deadline tugas/ujian terdekat
            $kelasIds = KelasKuliah::where('id_dosen', $dosen->id_dosen)
                ->where('id_semester', $selectedSemester->id_semester)
                ->pluck('id_kelas_kuliah');

            $tugasDeadlines = Tugas::whereIn('id_kelas_kuliah', $kelasIds)
                ->where('batas_akhir_pengumpulan', '>', now())
                ->orderBy('batas_akhir_pengumpulan')
                ->take(3)
                ->get()
                ->map(function ($item) {
                    $item->type = 'tugas';
                    return $item;
                });

            $utsDeadlines = Uts::whereIn('id_kelas_kuliah', $kelasIds)
                ->where('batas_akhir_pengumpulan', '>', now())
                ->orderBy('batas_akhir_pengumpulan')
                ->take(2)
                ->get()
                ->map(function ($item) {
                    $item->type = 'uts';
                    return $item;
                });

            $uasDeadlines = Uas::whereIn('id_kelas_kuliah', $kelasIds)
                ->where('batas_akhir_pengumpulan', '>', now())
                ->orderBy('batas_akhir_pengumpulan')
                ->take(2)
                ->get()
                ->map(function ($item) {
                    $item->type = 'uas';
                    return $item;
                });

            $deadlinesTerdekat = $tugasDeadlines->merge($utsDeadlines)->merge($uasDeadlines)
                ->sortBy('batas_akhir_pengumpulan')
                ->take(5);

            // Mahasiswa bimbingan belum KRS
            $mahasiswaBimbingan = $dosen->getMahasiswaBimbingan($selectedSemester->id_semester);
            $mahasiswaIds = $mahasiswaBimbingan->pluck('id_mahasiswa');

            $mahasiswaSudahKrs = RegistrasiMahasiswa::where('id_semester', $selectedSemester->id_semester)
                ->whereIn('id_mahasiswa', $mahasiswaIds)
                ->whereIn('status_krs', ['SUBMITTED', 'APPROVED'])
                ->pluck('id_mahasiswa');

            $mahasiswaBelumKrs = $mahasiswaBimbingan->filter(function ($pa) use ($mahasiswaSudahKrs) {
                return !$mahasiswaSudahKrs->contains($pa->id_mahasiswa);
            })->take(5);
        }

        return view('dashboard.dosen', compact(
            'stats',
            'jadwalHariIni',
            'sesiAbsensiTerbuka',
            'deadlinesTerdekat',
            'mahasiswaBelumKrs',
            'semesters',
            'selectedSemester',
            'dosen'
        ));
    }

    private function mahasiswaDashboard(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            abort(403, 'Data mahasiswa tidak ditemukan');
        }

        // Get all semesters for filter
        $semesters = Semester::orderBy('id_semester', 'desc')->get();
        $selectedSemester = null;

        if ($request->has('semester_id') && $request->semester_id) {
            $selectedSemester = Semester::find($request->semester_id);
        }

        // Status Akademik
        $statusAkademik = [
            'ip_terakhir' => 0,
            'total_sks_diambil' => 0,
            'status_krs' => 'BELUM_TERDAFTAR',
            'nama_pa' => null
        ];

        if ($selectedSemester) {
            // Get registrasi semester ini
            $registrasi = RegistrasiMahasiswa::where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->where('id_semester', $selectedSemester->id_semester)
                ->first();

            if ($registrasi) {
                $statusAkademik['status_krs'] = $registrasi->status_krs;

                // Total SKS diambil semester ini
                $statusAkademik['total_sks_diambil'] = PesertaKelasKuliah::where('id_registrasi_mahasiswa', $registrasi->id_registrasi_mahasiswa)
                    ->where('status_mata_kuliah', 'SELECTED')
                    ->join('mata_kuliah', 'peserta_kelas_kuliah.id_mata_kuliah', '=', 'mata_kuliah.id_mata_kuliah')
                    ->sum('mata_kuliah.sks_mata_kuliah');
            }

            // Get PA
            $pa = $mahasiswa->getPaAktif($selectedSemester->id_semester);
            if ($pa && $pa->dosen) {
                $statusAkademik['nama_pa'] = $pa->dosen->nama_lengkap;
            }
        }

        // IP semester terakhir (dari semua semester, tidak tergantung filter)
        $nilaiTerakhir = NilaiPerkuliahan::whereHas('pesertaKelasKuliah.registrasiMahasiswa', function ($query) use ($mahasiswa) {
            $query->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
        })
            ->whereNotNull('nilai_indeks')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        if ($nilaiTerakhir->count() > 0) {
            $totalIndeks = $nilaiTerakhir->sum('nilai_indeks');
            $statusAkademik['ip_terakhir'] = round($totalIndeks / $nilaiTerakhir->count(), 2);
        }

        // Data semester-dependent
        $jadwalHariIni = collect();
        $deadlinesTerdekat = collect();
        $sesiAbsensiTerbuka = collect();

        if ($selectedSemester) {
            // Jadwal hari ini
            $today = now()->format('l');
            $hariIndonesia = $this->convertDayToIndonesian($today);

            $registrasi = RegistrasiMahasiswa::where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->where('id_semester', $selectedSemester->id_semester)
                ->where('status_krs', 'APPROVED')
                ->first();

            if ($registrasi) {
                $jadwalHariIni = PesertaKelasKuliah::with(['kelasKuliah.kurikulumMataKuliah.mataKuliah', 'kelasKuliah.dosen.pengguna'])
                    ->where('id_registrasi_mahasiswa', $registrasi->id_registrasi_mahasiswa)
                    ->where('status_mata_kuliah', 'SELECTED')
                    ->whereHas('kelasKuliah', function ($query) use ($hariIndonesia) {
                        $query->where('hari', $hariIndonesia);
                    })
                    ->get()
                    ->sortBy('kelasKuliah.jam_mulai');

                // Deadline dan sesi absensi
                $kelasIds = PesertaKelasKuliah::where('id_registrasi_mahasiswa', $registrasi->id_registrasi_mahasiswa)
                    ->where('status_mata_kuliah', 'SELECTED')
                    ->pluck('id_kelas_kuliah');

                // Deadline tugas/ujian terdekat
                $tugasDeadlines = Tugas::whereIn('id_kelas_kuliah', $kelasIds)
                    ->where('batas_akhir_pengumpulan', '>', now())
                    ->orderBy('batas_akhir_pengumpulan')
                    ->take(3)
                    ->get()
                    ->map(function ($item) {
                        $item->type = 'tugas';
                        $item->submitted = PengumpulanTugas::where('id_tugas', $item->id_tugas)
                            ->where('id_mahasiswa', $this->getCurrentMahasiswaId())
                            ->exists();
                        return $item;
                    });

                $utsDeadlines = Uts::whereIn('id_kelas_kuliah', $kelasIds)
                    ->where('batas_akhir_pengumpulan', '>', now())
                    ->orderBy('batas_akhir_pengumpulan')
                    ->take(2)
                    ->get()
                    ->map(function ($item) {
                        $item->type = 'uts';
                        $item->submitted = PengumpulanUts::where('id_uts', $item->id_uts)
                            ->where('id_mahasiswa', $this->getCurrentMahasiswaId())
                            ->exists();
                        return $item;
                    });

                $uasDeadlines = Uas::whereIn('id_kelas_kuliah', $kelasIds)
                    ->where('batas_akhir_pengumpulan', '>', now())
                    ->orderBy('batas_akhir_pengumpulan')
                    ->take(2)
                    ->get()
                    ->map(function ($item) {
                        $item->type = 'uas';
                        $item->submitted = PengumpulanUas::where('id_uas', $item->id_uas)
                            ->where('id_mahasiswa', $this->getCurrentMahasiswaId())
                            ->exists();
                        return $item;
                    });

                $deadlinesTerdekat = $tugasDeadlines->merge($utsDeadlines)->merge($uasDeadlines)
                    ->sortBy('batas_akhir_pengumpulan')
                    ->take(5);

                // Sesi absensi terbuka
                $sesiAbsensiTerbuka = SesiAbsensi::with(['kelasKuliah.kurikulumMataKuliah.mataKuliah'])
                    ->whereIn('id_kelas_kuliah', $kelasIds)
                    ->where('status', 'dibuka')
                    ->where('batas_akhir_absensi', '>', now())
                    ->orderBy('batas_akhir_absensi')
                    ->take(5)
                    ->get();
            }
        }

        // Nilai terbaru (tidak tergantung semester filter)
        $nilaiTerbaru = NilaiPerkuliahan::with(['pesertaKelasKuliah.mataKuliah'])
            ->whereHas('pesertaKelasKuliah.registrasiMahasiswa', function ($query) use ($mahasiswa) {
                $query->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
            })
            ->whereNotNull('nilai_angka')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.mahasiswa', compact(
            'statusAkademik',
            'jadwalHariIni',
            'deadlinesTerdekat',
            'sesiAbsensiTerbuka',
            'nilaiTerbaru',
            'semesters',
            'selectedSemester',
            'mahasiswa'
        ));
    }

    private function getStatusKrsLabel($status)
    {
        $labels = [
            'DRAFT' => 'Draft',
            'SUBMITTED' => 'Menunggu Persetujuan',
            'APPROVED' => 'Disetujui',
            'REJECTED' => 'Ditolak'
        ];

        return $labels[$status] ?? $status;
    }

    private function convertDayToIndonesian($day)
    {
        $days = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        return $days[$day] ?? $day;
    }

    private function getCurrentMahasiswaId()
    {
        return Auth::user()->mahasiswa->id_mahasiswa ?? null;
    }
}
