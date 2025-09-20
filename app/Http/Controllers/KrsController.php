<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\RegistrasiMahasiswa;
use App\Models\PesertaKelasKuliah;
use App\Models\KelasKuliah;
use App\Models\Mahasiswa;
use App\Models\Semester;
use App\Models\PembimbingAkademik;
use App\Models\MataKuliah;
use App\Models\KurikulumMataKuliah;
use App\Models\NilaiPerkuliahan;

class KrsController extends Controller
{
    /**
     * Dashboard KRS Mahasiswa
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        // Cek eligibility - apakah sudah mencapai SKS lulus
        $totalSksLulus = $this->hitungSksLulus($mahasiswa->id_mahasiswa);
        $kurikulum = $mahasiswa->kurikulum;

        if ($totalSksLulus >= $kurikulum->jumlah_sks_lulus) {
            return view('krs.blocked', [
                'message' => 'Anda sudah mencapai total SKS kelulusan.',
                'totalSks' => $totalSksLulus,
                'maksimalSks' => $kurikulum->jumlah_sks_lulus
            ]);
        }

        // Get semua semester untuk dropdown
        $semesters = Semester::where('is_active', true)->get();

        // Get semester yang dipilih dari request atau session
        $selectedSemesterId = $request->semester;
        $selectedSemester = null;

        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);
            // Simpan ke session
            // session(['krs_semester_aktif' => $selectedSemesterId]);
        }

        // Jika ada semester terpilih, load data KRS
        $semesterMahasiswa = null;
        $registrasiKrs = null;
        $mataKuliahTerpilih = collect();
        $totalSksSelected = 0;
        $batasSks = 0;

        if ($selectedSemester) {
            $semesterMahasiswa = $this->hitungSemesterMahasiswa($mahasiswa, $selectedSemester);
            $registrasiKrs = $this->getOrCreateRegistrasiKrs($mahasiswa, $selectedSemester);
            $mataKuliahTerpilih = $this->getMataKuliahTerpilih($registrasiKrs->id_registrasi_mahasiswa);
            $totalSksSelected = $mataKuliahTerpilih->sum(function ($peserta) {
                return $peserta->mataKuliah->sks_mata_kuliah;
            });
            $batasSks = $this->getBatasSks($mahasiswa);
        }

        return view('mahasiswa.krs.index', compact([
            'mahasiswa',
            'semesters',
            'selectedSemester',
            'selectedSemesterId',
            'semesterMahasiswa',
            'registrasiKrs',
            'mataKuliahTerpilih',
            'totalSksSelected',
            'batasSks'
        ]));
    }

    /**
     * Halaman pemilihan mata kuliah
     */
    public function pilihMataKuliah($semesterId)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        $batasSks = 0;

        $selectedSemester = Semester::findOrFail($semesterId);
        $semesterMahasiswa = $this->hitungSemesterMahasiswa($mahasiswa, $selectedSemester);

        $mataKuliahTersedia = $this->getMataKuliahTersedia($mahasiswa, $selectedSemester, $semesterMahasiswa);
        $registrasiKrs = $this->getOrCreateRegistrasiKrs($mahasiswa, $selectedSemester);
        $mataKuliahTerpilih = $this->getMataKuliahTerpilih($registrasiKrs->id_registrasi_mahasiswa);

        $batasSks = $this->getBatasSks($mahasiswa);

        return view('mahasiswa.krs.pilih-mata-kuliah', compact([
            'mataKuliahTersedia',
            'mataKuliahTerpilih',
            'mahasiswa',
            'selectedSemester',
            'batasSks',
        ]));
    }

    /**
     * Tambah mata kuliah ke KRS
     */
    public function addMataKuliah(Request $request)
    {
        $request->validate([
            'id_kelas_kuliah' => 'required|exists:kelas_kuliah,id_kelas_kuliah'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();
            $semesterAktif = Semester::where('is_active', true)->first();

            $kelasKuliah = KelasKuliah::with('kurikulumMataKuliah.mataKuliah')
                ->findOrFail($request->id_kelas_kuliah);

            // Validasi kapasitas kelas
            if ($kelasKuliah->isPenuh()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelas sudah penuh!'
                ], 400);
            }

            // Get atau create registrasi KRS
            $registrasiKrs = $this->getOrCreateRegistrasiKrs($mahasiswa, $semesterAktif);

            // ✅ VALIDASI BARU: Cek apakah mahasiswa sudah memilih kelas lain untuk mata kuliah yang sama
            $existingPeserta = PesertaKelasKuliah::where('id_registrasi_mahasiswa', $registrasiKrs->id_registrasi_mahasiswa)
                ->where('id_mata_kuliah', $kelasKuliah->kurikulumMataKuliah->id_mata_kuliah)
                ->with('kelasKuliah')
                ->first();

            if ($existingPeserta) {
                $namaKelasLama = $existingPeserta->kelasKuliah->nama_kelas_kuliah ?? 'Kelas yang sudah dipilih';
                return response()->json([
                    'success' => false,
                    'message' => "Anda sudah memilih kelas \"{$namaKelasLama}\" untuk mata kuliah ini. Batalkan pilihan terlebih dahulu jika ingin memilih kelas lain!"
                ], 400);
            }

            // Validasi bentrok jadwal
            if ($this->cekBentrokJadwal($registrasiKrs->id_registrasi_mahasiswa, $kelasKuliah)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi bentrok jadwal dengan mata kuliah yang sudah dipilih!'
                ], 400);
            }

            // Validasi batas SKS maksimal berdasarkan IPK
            $totalSksSaatIni = $this->hitungTotalSksTerpilih($registrasiKrs->id_registrasi_mahasiswa);
            $batasSks = $this->getBatasSks($mahasiswa);
            $sksBaru = $kelasKuliah->kurikulumMataKuliah->mataKuliah->sks_mata_kuliah;

            if (($totalSksSaatIni + $sksBaru) > $batasSks) {
                return response()->json([
                    'success' => false,
                    'message' => "Total SKS melebihi batas maksimal ({$batasSks} SKS)!"
                ], 400);
            }

            // Simpan pilihan mata kuliah
            PesertaKelasKuliah::create([
                'id_kelas_kuliah' => $kelasKuliah->id_kelas_kuliah,
                'id_mata_kuliah' => $kelasKuliah->kurikulumMataKuliah->id_mata_kuliah,
                'id_registrasi_mahasiswa' => $registrasiKrs->id_registrasi_mahasiswa,
                'status_mata_kuliah' => 'SELECTED'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mata kuliah berhasil ditambahkan ke KRS!',
                'data' => [
                    'total_sks' => $totalSksSaatIni + $sksBaru
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
     * Hapus mata kuliah dari KRS
     */
    public function removeMataKuliah(Request $request)
    {
        $request->validate([
            'id_peserta' => 'required|exists:peserta_kelas_kuliah,id_peserta'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

            $pesertaKelasKuliah = PesertaKelasKuliah::with('registrasiMahasiswa')
                ->where('id_peserta', $request->id_peserta)
                ->whereHas('registrasiMahasiswa', function ($query) use ($mahasiswa) {
                    $query->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
                })
                ->first();

            if (!$pesertaKelasKuliah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan!'
                ], 404);
            }

            // Cek apakah KRS masih bisa diubah
            if ($pesertaKelasKuliah->registrasiMahasiswa->status_krs == 'APPROVED') {
                return response()->json([
                    'success' => false,
                    'message' => 'KRS sudah tidak dapat diubah!'
                ], 400);
            }

            $pesertaKelasKuliah->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mata kuliah berhasil dihapus dari KRS!'
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
     * Review KRS sebelum submit - UPDATE
     */
    public function reviewKrs($semesterId)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        // Get semester dari parameter URL
        $selectedSemester = Semester::findOrFail($semesterId);

        $registrasiKrs = $this->getOrCreateRegistrasiKrs($mahasiswa, $selectedSemester);
        $mataKuliahTerpilih = $this->getMataKuliahTerpilih($registrasiKrs->id_registrasi_mahasiswa);

        // Hitung total SKS
        $totalSks = $mataKuliahTerpilih->sum(function ($peserta) {
            return $peserta->mataKuliah->sks_mata_kuliah;
        });

        // Tambahkan batas SKS - INI YANG HILANG!
        $batasSks = $this->getBatasSks($mahasiswa);

        // Generate jadwal mingguan
        $jadwalMingguan = $this->generateJadwalMingguan($mataKuliahTerpilih);

        return view('mahasiswa.krs.review', compact([
            'mahasiswa',
            'selectedSemester',
            'registrasiKrs',
            'mataKuliahTerpilih',
            'totalSks',
            'batasSks', // TAMBAHKAN INI
            'jadwalMingguan'
        ]));
    }

    /**
     * Submit KRS ke PA
     */
    public function submitKrs(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();
            $semesterAktif = Semester::where('is_active', true)->first();

            $registrasiKrs = RegistrasiMahasiswa::with('pembimbingAkademik.dosen.pengguna')
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->where('id_semester', $semesterAktif->id_semester)
                ->first();

            if (!$registrasiKrs) {
                return redirect()->back()->with('error', 'KRS tidak ditemukan!');
            }

            // Validasi apakah mahasiswa sudah memiliki Pembimbing Akademik
            if (!$registrasiKrs->pembimbingAkademik) {
                return redirect()->back()->with('error', 'Anda belum mendapatkan Pembimbing Akademik untuk semester ini. Silakan hubungi bagian akademik terlebih dahulu.');
            }

            // Validasi status KRS masih bisa diubah
            // if ($registrasiKrs->status_krs !== 'SUBMITTED') {
            //     return redirect()->back()->with('error', 'KRS sudah tidak dapat diubah!');
            // }

            // ✅ TAMBAHAN: Reset semua mata kuliah yang REJECTED menjadi SELECTED
            $rejectedCount = $registrasiKrs->pesertaKelasKuliah()
                ->where('status_mata_kuliah', 'REJECTED')
                ->update(['status_mata_kuliah' => 'SELECTED']);

            // Validasi minimal ada mata kuliah yang dipilih (setelah reset status)
            $jumlahMataKuliah = $registrasiKrs->pesertaKelasKuliah()
                ->whereIn('status_mata_kuliah', ['SELECTED', 'APPROVED'])
                ->count();

            if ($jumlahMataKuliah == 0) {
                return redirect()->back()->with('error', 'Anda belum memilih mata kuliah apapun!');
            }

            // Hitung total SKS untuk validasi tambahan (hanya yang SELECTED dan APPROVED)
            $totalSks = $registrasiKrs->pesertaKelasKuliah()
                ->with('mataKuliah')
                ->whereIn('status_mata_kuliah', ['SELECTED', 'APPROVED'])
                ->get()
                ->sum(function ($peserta) {
                    return $peserta->mataKuliah->sks_mata_kuliah;
                });

            // Validasi batas SKS maksimal
            $batasSks = $this->getBatasSks($mahasiswa);
            if ($totalSks > $batasSks) {
                return redirect()->back()->with('error', "Total SKS ({$totalSks}) melebihi batas maksimal yang diizinkan ({$batasSks} SKS)!");
            }

            // Validasi minimal SKS (opsional, sesuai kebijakan institusi)
            if ($totalSks < 12) {
                return redirect()->back()->with('warning', "Total SKS ({$totalSks}) tergolong rendah. Apakah Anda yakin ingin melanjutkan?");
            }

            // Update status KRS dan tanggal submit
            $registrasiKrs->update([
                'status_krs' => 'SUBMITTED', // Status berubah ke SUBMITTED menunggu approval PA
                'tanggal_submit' => now(),
                'alasan_reject' => null,
            ]);

            DB::commit();

            // ✅ Informasi tambahan jika ada mata kuliah yang di-reset dari REJECTED
            $successMessage = "KRS berhasil disubmit ke Pembimbing Akademik ({$registrasiKrs->pembimbingAkademik->dosen->pengguna->nama})! " .
                "Total {$jumlahMataKuliah} mata kuliah dengan {$totalSks} SKS. " .
                "Silakan tunggu persetujuan dari PA.";

            if ($rejectedCount > 0) {
                $successMessage .= " ({$rejectedCount} mata kuliah yang sebelumnya ditolak telah diajukan kembali)";
            }

            return redirect()->route('krs.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Helper Methods
     */

    /**
     * Hitung SKS yang sudah lulus
     */
    private function hitungSksLulus($mahasiswaId)
    {
        return NilaiPerkuliahan::whereHas('pesertaKelasKuliah', function ($query) use ($mahasiswaId) {
            $query->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswaId) {
                $q->where('id_mahasiswa', $mahasiswaId);
            });
        })
            ->whereIn('nilai_huruf', ['A', 'B+', 'B', 'C+', 'C'])
            ->with('pesertaKelasKuliah.mataKuliah')
            ->get()
            ->sum(function ($nilai) {
                return $nilai->pesertaKelasKuliah->mataKuliah->sks_mata_kuliah;
            });
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
     * Get atau create registrasi KRS
     */
    private function getOrCreateRegistrasiKrs($mahasiswa, $semesterAktif)
    {
        $registrasiKrs = RegistrasiMahasiswa::where('id_mahasiswa', $mahasiswa->id_mahasiswa)
            ->where('id_semester', $semesterAktif->id_semester)
            ->first();

        if (!$registrasiKrs) {
            // Get PA aktif untuk mahasiswa ini
            $pembimbingAkademik = PembimbingAkademik::where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->where('id_semester', $semesterAktif->id_semester)
                ->where('status_pa', 'AKTIF')
                ->first();

            $registrasiKrs = RegistrasiMahasiswa::create([
                'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                'id_semester' => $semesterAktif->id_semester,
                'status_krs' => 'SUBMITTED',
                'id_pembimbing_akademik' => $pembimbingAkademik ? $pembimbingAkademik->id_pembimbing_akademik : null,
            ]);
        } else {
            // kalau registrasi sudah ada tapi belum ada PA, coba update kalau sekarang ada PA
            if (!$registrasiKrs->id_pembimbing_akademik) {
                $pembimbingAkademik = PembimbingAkademik::where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                    ->where('id_semester', $semesterAktif->id_semester)
                    ->where('status_pa', 'AKTIF')
                    ->first();

                if ($pembimbingAkademik) {
                    $registrasiKrs->update([
                        'id_pembimbing_akademik' => $pembimbingAkademik->id_pembimbing_akademik,
                    ]);
                }
            }
        }

        return $registrasiKrs;
    }


    /**
     * Get mata kuliah yang sudah dipilih
     */
    private function getMataKuliahTerpilih($registrasiId)
    {
        return PesertaKelasKuliah::with([
            'mataKuliah',
            'kelasKuliah.dosen.pengguna',
            'kelasKuliah'
        ])
            ->where('id_registrasi_mahasiswa', $registrasiId)
            ->get();
    }

    /**
     * Get mata kuliah yang tersedia untuk dipilih
     */
    private function getMataKuliahTersedia($mahasiswa, $semesterAktif, $semesterMahasiswa)
    {
        return KurikulumMataKuliah::with([
            'mataKuliah',
            'kurikulum',
            'kelasKuliah' => function ($query) use ($semesterAktif) {
                $query->with(['dosen.pengguna', 'semester'])
                    ->where('id_semester', $semesterAktif->id_semester);
            }
        ])
            ->where('id_kurikulum', $mahasiswa->id_kurikulum)
            ->where('semester', '<=', $semesterMahasiswa)
            ->whereNotExists(function ($query) use ($mahasiswa) {
                $query->select(DB::raw(1))
                    ->from('peserta_kelas_kuliah as pkk')
                    ->join('registrasi_mahasiswa as rm', 'pkk.id_registrasi_mahasiswa', '=', 'rm.id_registrasi_mahasiswa')
                    ->join('nilai_perkuliahan as np', 'pkk.id_peserta', '=', 'np.id_peserta')
                    ->join('kelas_kuliah as kk', 'pkk.id_kelas_kuliah', '=', 'kk.id_kelas_kuliah')
                    ->where('kk.id_kurikulum_mata_kuliah', '=', DB::raw('kurikulum_mata_kuliah.id'))
                    ->where('rm.id_mahasiswa', $mahasiswa->id_mahasiswa)
                    ->whereIn('np.nilai_huruf', ['A', 'B+', 'B', 'C+', 'C']);
            })
            ->get()
            ->groupBy('id_mata_kuliah');
    }

    /**
     * Cek bentrok jadwal
     */
    private function cekBentrokJadwal($registrasiId, $kelasKuliahBaru)
    {
        $kelasKuliahTerpilih = PesertaKelasKuliah::with('kelasKuliah')
            ->where('id_registrasi_mahasiswa', $registrasiId)
            ->get();

        foreach ($kelasKuliahTerpilih as $peserta) {
            $kelas = $peserta->kelasKuliah;

            // Cek bentrok hari dan waktu
            if (
                $kelas->hari === $kelasKuliahBaru->hari &&
                $kelas->jam_mulai && $kelas->jam_akhir &&
                $kelasKuliahBaru->jam_mulai && $kelasKuliahBaru->jam_akhir
            ) {

                // Cek overlap waktu
                if (($kelasKuliahBaru->jam_mulai >= $kelas->jam_mulai && $kelasKuliahBaru->jam_mulai < $kelas->jam_akhir) ||
                    ($kelasKuliahBaru->jam_akhir > $kelas->jam_mulai && $kelasKuliahBaru->jam_akhir <= $kelas->jam_akhir) ||
                    ($kelasKuliahBaru->jam_mulai <= $kelas->jam_mulai && $kelasKuliahBaru->jam_akhir >= $kelas->jam_akhir)
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Hitung total SKS yang sudah dipilih
     */
    private function hitungTotalSksTerpilih($registrasiId)
    {
        return PesertaKelasKuliah::with('mataKuliah')
            ->where('id_registrasi_mahasiswa', $registrasiId)
            ->get()
            ->sum(function ($peserta) {
                return $peserta->mataKuliah->sks_mata_kuliah;
            });
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
    private function hitungSemesterMahasiswaSaatIni($mahasiswa)
    {
        $semesterAktif = Semester::where('is_active', true)->first();
        return $this->hitungSemesterMahasiswa($mahasiswa, $semesterAktif);
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

    /**
     * Generate jadwal mingguan
     */
    private function generateJadwalMingguan($mataKuliahTerpilih)
    {
        $jadwal = [];
        $hariList = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];

        foreach ($hariList as $hari) {
            $jadwal[$hari] = [];
        }

        foreach ($mataKuliahTerpilih as $peserta) {
            $kelas = $peserta->kelasKuliah;

            if ($kelas->hari && $kelas->jam_mulai && $kelas->jam_akhir) {
                $jadwal[$kelas->hari][] = [
                    'mata_kuliah' => $peserta->mataKuliah->nama_mata_kuliah,
                    'kode_mata_kuliah' => $peserta->mataKuliah->kode_mata_kuliah,
                    'kelas' => $kelas->nama_kelas_kuliah,
                    'ruangan' => $kelas->nama_ruangan,
                    'dosen' => $kelas->dosen->pengguna->nama ?? 'N/A',
                    'jam_mulai' => $kelas->jam_mulai,
                    'jam_akhir' => $kelas->jam_akhir,
                    'sks' => $peserta->mataKuliah->sks_mata_kuliah
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
}
