<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\RegistrasiMahasiswa;
use App\Models\PesertaKelasKuliah;
use App\Models\PembimbingAkademik;
use App\Models\Mahasiswa;
use App\Models\Semester;
use App\Models\Dosen;
use App\Models\KelasKuliah;
use App\Models\NilaiPerkuliahan;

class KrsApprovalController extends Controller
{
    /**
     * Dashboard PA - Tampilkan KRS mahasiswa bimbingan yang perlu approval
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $dosen = Dosen::where('id_pengguna', $user->id_pengguna)->first();

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan!');
        }

        // Get semester yang dipilih
        $selectedSemesterId = $request->get('semester');
        $selectedSemester = null;
        $mahasiswaBimbingan = collect();

        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);

            if ($selectedSemester) {
                // Query SEMUA mahasiswa bimbingan PA dosen di semester tersebut
                $query = PembimbingAkademik::with([
                    'mahasiswa.pengguna',
                    'mahasiswa.programStudi',
                    'mahasiswa.registrasi' => function ($q) use ($selectedSemesterId) {
                        $q->where('id_semester', $selectedSemesterId)
                            ->with([
                                'pesertaKelasKuliah.mataKuliah',
                                'pesertaKelasKuliah.kelasKuliah.dosen.pengguna'
                            ]);
                    },
                    'semester'
                ])
                    ->where('id_dosen', $dosen->id_dosen)
                    ->where('id_semester', $selectedSemesterId)
                    ->where('status_pa', 'AKTIF');

                // Filter berdasarkan status KRS
                if ($request->has('status') && $request->status != '') {
                    $statusFilter = $request->status;
                    $query->whereHas('mahasiswa.registrasi', function ($q) use ($selectedSemesterId, $statusFilter) {
                        $q->where('id_semester', $selectedSemesterId)
                            ->where('status_krs', $statusFilter);
                    }, '=', $statusFilter === 'BELUM_SUBMIT' ? 0 : 1);

                    // Khusus untuk status BELUM_SUBMIT, ambil yang tidak punya registrasi
                    if ($statusFilter === 'BELUM_SUBMIT') {
                        $query->whereDoesntHave('mahasiswa.registrasi', function ($q) use ($selectedSemesterId) {
                            $q->where('id_semester', $selectedSemesterId);
                        });
                    }
                }

                // Filter berdasarkan program studi
                if ($request->has('program_studi') && $request->program_studi != '') {
                    $query->whereHas('mahasiswa', function ($q) use ($request) {
                        $q->where('id_program_studi', $request->program_studi);
                    });
                }

                // Pencarian mahasiswa
                if ($request->has('search') && $request->search != '') {
                    $searchTerm = $request->search;
                    $query->whereHas('mahasiswa', function ($q) use ($searchTerm) {
                        $q->whereHas('pengguna', function ($mq) use ($searchTerm) {
                            $mq->where('nama', 'LIKE', "%{$searchTerm}%");
                        })->orWhere('nim', 'LIKE', "%{$searchTerm}%");
                    });
                }

                $pembimbingAkademiks = $query->orderBy('created_at', 'desc')->paginate(10);

                // Transform data untuk view - buat structure yang konsisten
                $mahasiswaBimbingan = $pembimbingAkademiks->through(function ($pa) use ($selectedSemesterId) {
                    $mahasiswa = $pa->mahasiswa;
                    $registrasi = $mahasiswa->registrasi->first(); // registrasi di semester ini

                    // Buat object yang konsisten dengan struktur sebelumnya
                    $item = (object) [
                        'id_pembimbing_akademik' => $pa->id_pembimbing_akademik,
                        'mahasiswa' => $mahasiswa,
                        'semester_id' => $selectedSemesterId,
                        'batas_sks' => $this->getBatasSks($mahasiswa),
                        'has_registration' => $registrasi !== null,
                    ];

                    if ($registrasi) {
                        // Mahasiswa sudah ada registrasi
                        $item->id_registrasi_mahasiswa = $registrasi->id_registrasi_mahasiswa;
                        $item->status_krs = $registrasi->status_krs;
                        $item->tanggal_submit = $registrasi->tanggal_submit;
                        $item->tanggal_approval = $registrasi->tanggal_approval;
                        $item->alasan_reject = $registrasi->alasan_reject;
                        $item->pesertaKelasKuliah = $registrasi->pesertaKelasKuliah;
                    } else {
                        // Mahasiswa belum ada registrasi
                        $item->id_registrasi_mahasiswa = null;
                        $item->status_krs = 'BELUM_SUBMIT';
                        $item->tanggal_submit = null;
                        $item->tanggal_approval = null;
                        $item->alasan_reject = null;
                        $item->pesertaKelasKuliah = collect();
                    }

                    return $item;
                });

                // Hitung statistik
                $statistik = $this->hitungStatistik($dosen->id_dosen, $selectedSemesterId);
            }
        }

        // Jika tidak ada semester dipilih, set statistik kosong
        if (!$selectedSemester) {
            $statistik = [
                'total_mahasiswa' => 0,
                'submitted' => 0,
                'approved' => 0,
                'pending' => 0,
                'rejected' => 0,
                'belum_submit' => 0
            ];
        }

        // Data untuk dropdown
        $semesters = Semester::orderByDesc('kode_semester')->where('is_active', true)->get();

        $programStudis = DB::table('program_studi')
            ->join('mahasiswa', 'program_studi.id_program_studi', '=', 'mahasiswa.id_program_studi')
            ->join('pembimbing_akademik', 'mahasiswa.id_mahasiswa', '=', 'pembimbing_akademik.id_mahasiswa')
            ->where('pembimbing_akademik.id_dosen', $dosen->id_dosen)
            ->where('pembimbing_akademik.status_pa', 'AKTIF')
            ->select('program_studi.*')
            ->distinct()
            ->get();

        return view('dosen.krs.approval', compact([
            'mahasiswaBimbingan',
            'statistik',
            'semesters',
            'programStudis',
            'selectedSemester',
            'selectedSemesterId'
        ]));
    }

    /**
     * Review detail KRS mahasiswa
     */
    public function review($registrasiId)
    {
        $user = Auth::user();
        $dosen = Dosen::where('id_pengguna', $user->id_pengguna)->first();

        $registrasiMahasiswa = RegistrasiMahasiswa::with([
            'mahasiswa.pengguna',
            'mahasiswa.programStudi.jenjang',
            'mahasiswa.kurikulum',
            'semester',
            'pembimbingAkademik',
            'pesertaKelasKuliah' => function ($query) {
                $query->with([
                    'mataKuliah',
                    'kelasKuliah.dosen.pengguna',
                    'kelasKuliah.kurikulumMataKuliah'
                ]);
            }
        ])
            ->whereHas('pembimbingAkademik', function ($q) use ($dosen) {
                $q->where('id_dosen', $dosen->id_dosen);
            })
            ->findOrFail($registrasiId);

        // Hitung total SKS
        $totalSks = $registrasiMahasiswa->pesertaKelasKuliah->sum(function ($peserta) {
            return $peserta->mataKuliah->sks_mata_kuliah;
        });

        // Generate jadwal mingguan
        $jadwalMingguan = $this->generateJadwalMingguan($registrasiMahasiswa->pesertaKelasKuliah);

        // Get riwayat akademik mahasiswa
        $riwayatAkademik = $this->getRiwayatAkademik($registrasiMahasiswa->mahasiswa->id_mahasiswa);

        // Get rekomendasi berdasarkan IPK dan semester
        $rekomendasi = $this->generateRekomendasi($registrasiMahasiswa);

        return view('dosen.krs.review', compact([
            'registrasiMahasiswa',
            'totalSks',
            'jadwalMingguan',
            'riwayatAkademik',
            'rekomendasi'
        ]));
    }

    /**
     * Approve/Reject mata kuliah individual
     */
    public function updateMataKuliah(Request $request)
    {
        $request->validate([
            'id_peserta' => 'required|exists:peserta_kelas_kuliah,id_peserta',
            'action' => 'required|in:approve,reject',
            'catatan' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $dosen = Dosen::where('id_pengguna', $user->id_pengguna)->first();

            $pesertaKelasKuliah = PesertaKelasKuliah::with(['registrasiMahasiswa.pembimbingAkademik'])
                ->findOrFail($request->id_peserta);

            // Validasi PA
            if ($pesertaKelasKuliah->registrasiMahasiswa->pembimbingAkademik->id_dosen !== $dosen->id_dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berwenang untuk mata kuliah ini!'
                ], 403);
            }

            // Update status mata kuliah
            $newStatus = $request->action === 'approve' ? 'APPROVED' : 'REJECTED';
            $pesertaKelasKuliah->update([
                'status_mata_kuliah' => $newStatus
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status mata kuliah berhasil diupdate!',
                'data' => [
                    'new_status' => $newStatus
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve semua mata kuliah dalam KRS
     */
    public function approveKrs(Request $request, $registrasiId)
    {
        $request->validate([
            'catatan_pa' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $dosen = Dosen::where('id_pengguna', $user->id_pengguna)->first();

            $registrasiMahasiswa = RegistrasiMahasiswa::with(['pembimbingAkademik', 'pesertaKelasKuliah'])
                ->whereHas('pembimbingAkademik', function ($q) use ($dosen) {
                    $q->where('id_dosen', $dosen->id_dosen);
                })
                ->findOrFail($registrasiId);

            // Validasi status KRS
            if ($registrasiMahasiswa->status_krs !== 'SUBMITTED') {
                return redirect()->back()->with('error', 'KRS sudah tidak dapat diubah!');
            }

            // Cek apakah semua mata kuliah sudah di-review
            $belumDiReview = $registrasiMahasiswa->pesertaKelasKuliah()
                ->where('status_mata_kuliah', 'SELECTED')
                ->count();

            if ($belumDiReview > 0) {
                return redirect()->back()->with('error', 'Masih ada mata kuliah yang belum di-review!');
            }

            // Cek apakah ada mata kuliah yang di-approve
            $totalApproved = $registrasiMahasiswa->pesertaKelasKuliah()
                ->where('status_mata_kuliah', 'APPROVED')
                ->count();

            if ($totalApproved == 0) {
                return redirect()->back()->with('error', 'Tidak ada mata kuliah yang di-approve!');
            }

            // Hapus mata kuliah yang di-reject
            $registrasiMahasiswa->pesertaKelasKuliah()
                ->where('status_mata_kuliah', 'REJECTED')
                ->delete();

            // Update status KRS ke APPROVED
            $registrasiMahasiswa->update([
                'status_krs' => 'APPROVED',
                'tanggal_approval' => now()
            ]);

            DB::commit();

            return redirect()->route('krs.approval.index')
                ->with('success', 'KRS berhasil disetujui!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject seluruh KRS
     */
    public function rejectKrs(Request $request, $registrasiId)
    {
        $request->validate([
            'alasan_reject' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $dosen = Dosen::where('id_pengguna', $user->id_pengguna)->first();

            $registrasiMahasiswa = RegistrasiMahasiswa::with(['pembimbingAkademik'])
                ->whereHas('pembimbingAkademik', function ($q) use ($dosen) {
                    $q->where('id_dosen', $dosen->id_dosen);
                })
                ->findOrFail($registrasiId);

            // Validasi status KRS
            if ($registrasiMahasiswa->status_krs !== 'SUBMITTED') {
                return redirect()->back()->with('error', 'KRS sudah tidak dapat diubah!');
            }

            // Reset status KRS ke REJECTED untuk revisi mahasiswa
            $registrasiMahasiswa->update([
                'tanggal_approval' => null,
                'status_krs' => 'REJECTED',
                'alasan_reject' => $request->alasan_reject,
            ]);

            DB::commit();

            return redirect()->route('krs.approval.index')
                ->with('success', 'KRS telah di-reject. Mahasiswa dapat merevisi KRS-nya.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get daftar kelas alternatif untuk penggantian
     */
    public function getKelasAlternatif(Request $request)
    {
        $request->validate([
            'id_mata_kuliah' => 'required|exists:mata_kuliah,id_mata_kuliah',
            'id_semester' => 'required|exists:semester,id_semester'
        ]);

        $kelasAlternatif = KelasKuliah::with([
            'dosen.pengguna',
            'kurikulumMataKuliah.mataKuliah'
        ])
            ->whereHas('kurikulumMataKuliah', function ($query) use ($request) {
                $query->where('id_mata_kuliah', $request->id_mata_kuliah);
            })
            ->where('id_semester', $request->id_semester)
            ->get()
            ->map(function ($kelas) {
                return [
                    'id_kelas_kuliah' => $kelas->id_kelas_kuliah,
                    'nama_kelas_kuliah' => $kelas->nama_kelas_kuliah,
                    'nama_ruangan' => $kelas->nama_ruangan,
                    'hari' => $kelas->hari,
                    'jam_mulai' => $kelas->jam_mulai,
                    'jam_akhir' => $kelas->jam_akhir,
                    'kapasitas' => $kelas->kapasitas,
                    'jumlah_peserta' => $kelas->jumlah_peserta,
                    'dosen_nama' => $kelas->dosen->pengguna->nama ?? 'N/A',
                    'is_penuh' => $kelas->isPenuh()
                ];
            });

        return response()->json($kelasAlternatif);
    }

    /**
     * Helper Methods
     */

    /**
     * Hitung statistik KRS untuk PA
     */
    private function hitungStatistik($dosenId, $semesterId)
    {
        // Ambil semua mahasiswa bimbingan PA
        $allMahasiswaBimbingan = PembimbingAkademik::with([
            'mahasiswa.registrasi' => function ($q) use ($semesterId) {
                $q->where('id_semester', $semesterId);
            }
        ])
            ->where('id_dosen', $dosenId)
            ->where('id_semester', $semesterId)
            ->where('status_pa', 'AKTIF')
            ->get();

        $totalMahasiswa = $allMahasiswaBimbingan->count();

        // Initialize counters
        $belumSubmit = 0;   // Belum submit KRS
        $pending = 0;       // Menunggu Review  
        $approved = 0;      // Disetujui
        $rejected = 0;      // Ditolak

        foreach ($allMahasiswaBimbingan as $pa) {
            $registrasi = $pa->mahasiswa->registrasi->first();

            if (!$registrasi) {
                // Belum ada registrasi sama sekali
                $belumSubmit++;
                continue;
            }

            $statusKrs = $registrasi->status_krs;

            if ($statusKrs === 'DRAFT' || $statusKrs === null) {
                $belumSubmit++;
            } elseif ($statusKrs === 'REJECTED') {
                $rejected++;
            } elseif ($statusKrs === 'APPROVED') {
                $approved++;
            } elseif ($statusKrs === 'SUBMITTED') {
                $pending++;
            } else {
                // Status lainnya dianggap pending
                $pending++;
            }
        }

        return [
            'total_mahasiswa' => $totalMahasiswa,
            'belum_submit' => $belumSubmit,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected
        ];
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

            if ($kelas->hari && $kelas->jam_mulai && $kelas->jam_akhir) {
                $jadwal[$kelas->hari][] = [
                    'id_peserta' => $peserta->id_peserta,
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
     * Get riwayat akademik mahasiswa
     */
    private function getRiwayatAkademik($mahasiswaId)
    {
        return DB::table('registrasi_mahasiswa as rm')
            ->join('semester as s', 'rm.id_semester', '=', 's.id_semester')
            ->leftJoin('peserta_kelas_kuliah as pkk', 'rm.id_registrasi_mahasiswa', '=', 'pkk.id_registrasi_mahasiswa')
            ->leftJoin('nilai_perkuliahan as np', 'pkk.id_peserta', '=', 'np.id_peserta')
            ->leftJoin('mata_kuliah as mk', 'pkk.id_mata_kuliah', '=', 'mk.id_mata_kuliah')
            ->where('rm.id_mahasiswa', $mahasiswaId)
            ->where('rm.status_krs', 'APPROVED')
            ->select([
                's.nama_semester',
                DB::raw('COUNT(pkk.id_peserta) as total_mata_kuliah'),
                DB::raw('SUM(mk.sks_mata_kuliah) as total_sks'),
                DB::raw('AVG(np.nilai_indeks) as ipk_semester'),
                DB::raw('SUM(CASE WHEN np.nilai_huruf IN ("A", "B+", "B", "C+", "C") THEN mk.sks_mata_kuliah ELSE 0 END) as sks_lulus')
            ])
            ->groupBy('rm.id_semester', 's.nama_semester')
            ->orderBy('s.kode_semester')
            ->get();
    }

    /**
     * Generate rekomendasi berdasarkan IPK dan semester
     */
    private function generateRekomendasi($registrasiMahasiswa)
    {
        $rekomendasi = [];
        $totalSks = $registrasiMahasiswa->pesertaKelasKuliah->sum(function ($peserta) {
            return $peserta->mataKuliah->sks_mata_kuliah;
        });

        // Rekomendasi berdasarkan beban SKS
        if ($totalSks > 24) {
            $rekomendasi[] = [
                'type' => 'warning',
                'message' => 'Total SKS terlalu tinggi (' . $totalSks . ' SKS). Pertimbangkan untuk mengurangi beban mata kuliah.'
            ];
        } elseif ($totalSks < 12) {
            $rekomendasi[] = [
                'type' => 'info',
                'message' => 'Total SKS rendah (' . $totalSks . ' SKS). Mahasiswa bisa menambah mata kuliah jika memungkinkan.'
            ];
        }

        // Cek bentrok jadwal
        $bentrokJadwal = $this->cekBentrokJadwal($registrasiMahasiswa->pesertaKelasKuliah);
        if (!empty($bentrokJadwal)) {
            foreach ($bentrokJadwal as $bentrok) {
                $rekomendasi[] = [
                    'type' => 'error',
                    'message' => 'Terdapat bentrok jadwal: ' . $bentrok
                ];
            }
        }

        return $rekomendasi;
    }

    /**
     * Cek bentrok jadwal
     */
    private function cekBentrokJadwal($pesertaKelasKuliahs)
    {
        $bentrok = [];
        $jadwalList = [];

        foreach ($pesertaKelasKuliahs as $peserta) {
            if ($peserta->status_mata_kuliah === 'REJECTED') continue;

            $kelas = $peserta->kelasKuliah;

            if ($kelas->hari && $kelas->jam_mulai && $kelas->jam_akhir) {
                $jadwalKey = $kelas->hari . '-' . $kelas->jam_mulai . '-' . $kelas->jam_akhir;

                if (isset($jadwalList[$jadwalKey])) {
                    $bentrok[] = $peserta->mataKuliah->kode_mata_kuliah . ' bentrok dengan ' . $jadwalList[$jadwalKey];
                } else {
                    $jadwalList[$jadwalKey] = $peserta->mataKuliah->kode_mata_kuliah;
                }
            }
        }

        return $bentrok;
    }

    /**
     * Get batas SKS maksimal berdasarkan IPK
     */
    private function getBatasSks($mahasiswa)
    {
        // Cek apakah mahasiswa baru (semester 1)
        $semesterMahasiswa = $this->hitungSemesterMahasiswaSaatIni($mahasiswa);

        // Untuk mahasiswa semester 1-2, gunakan batas khusus
        if ($semesterMahasiswa <= 2) {
            return $this->getBatasSksUntuMahasiswaBaru($semesterMahasiswa);
        }

        // Untuk mahasiswa semester 3+, hitung berdasarkan IPK
        $ipk = $this->hitungIPK($mahasiswa->id_mahasiswa);

        if ($ipk >= 3.0) {
            return 24; // Maksimal 24 SKS untuk IPK >= 3.0
        } elseif ($ipk >= 2.5) {
            return 21; // Maksimal 21 SKS untuk IPK 2.5-2.99
        } else {
            return 18; // Maksimal 18 SKS untuk IPK < 2.5
        }
    }

    /**
     * Get batas SKS khusus untuk mahasiswa baru
     */
    private function getBatasSksUntuMahasiswaBaru($semesterMahasiswa)
    {
        // Kebijakan batas SKS untuk mahasiswa baru
        switch ($semesterMahasiswa) {
            case 1:
                return 20; // Semester 1: maksimal 20 SKS (lebih konservatif)
            case 2:
                return 22; // Semester 2: maksimal 22 SKS (sedikit lebih longgar)
            default:
                return 24; // Default untuk semester 3+
        }
    }

    /**
     * Hitung semester mahasiswa saat ini
     */
    private function hitungSemesterMahasiswaSaatIni($mahasiswa)
    {
        $semesterAktif = Semester::where('is_active', true)->first();
        return $this->hitungSemesterMahasiswa($mahasiswa, $semesterAktif);
    }

    /**
     * Hitung semester mahasiswa saat ini
     */
    private function hitungSemesterMahasiswa($mahasiswa, $semesterAktif)
    {
        // Logic: tahun semester - tahun angkatan * 2 + (genap = 2, ganjil = 1)
        $tahunSemester = (int) substr($semesterAktif->kode_semester, 0, 4);
        $semesterTipe = (int) substr($semesterAktif->kode_semester, -1);

        $selisihTahun = $tahunSemester - $mahasiswa->angkatan;
        return ($selisihTahun * 2) + $semesterTipe;
    }

    /**
     * Hitung IPK mahasiswa berdasarkan nilai-nilai yang sudah ada
     */
    private function hitungIPK($mahasiswaId)
    {
        $nilaiPerkuliahan = NilaiPerkuliahan::whereHas('pesertaKelasKuliah', function ($query) use ($mahasiswaId) {
            $query->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswaId) {
                $q->where('id_mahasiswa', $mahasiswaId);
            });
        })
            ->with('pesertaKelasKuliah.mataKuliah')
            ->whereNotNull('nilai_huruf')
            ->get();

        if ($nilaiPerkuliahan->count() == 0) {
            return 3.0; // Default IPK untuk mahasiswa baru
        }

        $totalNilai = 0;
        $totalSks = 0;

        foreach ($nilaiPerkuliahan as $nilai) {
            $bobotNilai = $this->getBobotNilai($nilai->nilai_huruf);
            $sks = $nilai->pesertaKelasKuliah->mataKuliah->sks_mata_kuliah;

            $totalNilai += ($bobotNilai * $sks);
            $totalSks += $sks;
        }

        return $totalSks > 0 ? round($totalNilai / $totalSks, 2) : 3.0;
    }

    /**
     * Get bobot nilai dari huruf
     */
    private function getBobotNilai($nilaiHuruf)
    {
        $bobotNilai = [
            'A' => 4.0,
            'B+' => 3.5,
            'B' => 3.0,
            'C+' => 2.5,
            'C' => 2.0,
            'D+' => 1.5,
            'D' => 1.0,
            'E' => 0.0
        ];

        return $bobotNilai[$nilaiHuruf] ?? 0.0;
    }
}
