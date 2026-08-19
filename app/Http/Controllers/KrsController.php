<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\RegistrasiMahasiswa;
use App\Models\PesertaKelasKuliah;
use App\Models\PesertaBimbingan;
use App\Models\KelasKuliah;
use App\Models\Mahasiswa;
use App\Models\Semester;
use App\Models\PembimbingAkademik;
use App\Models\MataKuliah;
use App\Models\KurikulumMataKuliah;
use App\Models\NilaiPerkuliahan;
use Illuminate\Support\Str;

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

        // Get semester berdasarkan angkatan mahasiswa
        $semesters = $this->getSemestersByAngkatan($mahasiswa->angkatan);

        // Get semester yang dipilih dari request
        $selectedSemesterId = $request->semester;
        $selectedSemester = null;

        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);
        }

        // Jika ada semester terpilih, load data KRS
        $semesterMahasiswa = null;
        $registrasiKrs = null;
        $mataKuliahTerpilih = collect();
        $totalSksSelected = 0;
        $batasSks = 0;
        $sksPerKategori = null;

        if ($selectedSemester) {
            $semesterMahasiswa = $this->hitungSemesterMahasiswa($mahasiswa, $selectedSemester);
            $registrasiKrs = $this->getOrCreateRegistrasiKrs($mahasiswa, $selectedSemester);
            $mataKuliahTerpilih = $this->getMataKuliahTerpilih($registrasiKrs->id_registrasi_mahasiswa);
            $totalSksSelected = $this->hitungTotalSksTerpilih($registrasiKrs->id_registrasi_mahasiswa);
            $batasSks = $this->getBatasSks($mahasiswa, $selectedSemester);
            $sksPerKategori = $this->hitungSksPerKategori($registrasiKrs->id_registrasi_mahasiswa);
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
            'batasSks',
            'sksPerKategori'
        ]));
    }

    /**
     * Halaman pemilihan mata kuliah
     */
    public function pilihMataKuliah($semesterId)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        $selectedSemester = Semester::findOrFail($semesterId);
        $semesterMahasiswa = $this->hitungSemesterMahasiswa($mahasiswa, $selectedSemester);

        // Get mata kuliah tersedia per semester
        $mataKuliahTersedia = $this->getMataKuliahTersediaPerSemester($mahasiswa, $selectedSemester, $semesterMahasiswa);
        $registrasiKrs = $this->getOrCreateRegistrasiKrs($mahasiswa, $selectedSemester);
        $mataKuliahTerpilih = $this->getMataKuliahTerpilih($registrasiKrs->id_registrasi_mahasiswa);

        $batasSks = $this->getBatasSks($mahasiswa, $selectedSemester);
        $sksPerKategori = $this->hitungSksPerKategori($registrasiKrs->id_registrasi_mahasiswa);
        $batasKategori = $this->getBatasKategori($mahasiswa);

        return view('mahasiswa.krs.pilih-mata-kuliah', compact([
            'mataKuliahTersedia',
            'mataKuliahTerpilih',
            'mahasiswa',
            'selectedSemester',
            'batasSks',
            'sksPerKategori',
            'batasKategori'
        ]));
    }

    /**
     * Tambah mata kuliah ke KRS
     */
    public function addMataKuliah(Request $request)
    {
        // Validasi input berdasarkan jenis mata kuliah
        if ($request->has('id_kelas_kuliah')) {
            // Mata kuliah reguler
            $request->validate([
                'id_kelas_kuliah' => 'required|exists:kelas_kuliah,id_kelas_kuliah'
            ]);
        } else {
            // Mata kuliah bimbingan
            $request->validate([
                'id_mata_kuliah' => 'required|exists:mata_kuliah,id_mata_kuliah'
            ]);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();
            $semesterAktif = Semester::where('is_active', true)->first();

            $registrasiKrs = $this->getOrCreateRegistrasiKrs($mahasiswa, $semesterAktif);

            if ($request->has('id_kelas_kuliah')) {
                // Handle mata kuliah reguler
                $result = $this->addMataKuliahReguler($request, $mahasiswa, $registrasiKrs, $semesterAktif);
            } else {
                // Handle mata kuliah bimbingan
                $result = $this->addMataKuliahBimbingan($request, $mahasiswa, $registrasiKrs, $semesterAktif);
            }

            if (!$result['success']) {
                DB::rollBack();
                return response()->json($result, 400);
            }

            DB::commit();
            return response()->json($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle penambahan mata kuliah reguler
     */
    private function addMataKuliahReguler($request, $mahasiswa, $registrasiKrs, $semesterAktif)
    {
        $kelasKuliah = KelasKuliah::with(['kurikulumMataKuliah.mataKuliah'])
            ->findOrFail($request->id_kelas_kuliah);

        // Validasi kapasitas kelas
        if ($kelasKuliah->isPenuh()) {
            return ['success' => false, 'message' => 'Kelas sudah penuh!'];
        }

        // Cek apakah sudah memilih mata kuliah yang sama
        $existingPeserta = PesertaKelasKuliah::where('id_registrasi_mahasiswa', $registrasiKrs->id_registrasi_mahasiswa)
            ->where('id_mata_kuliah', $kelasKuliah->kurikulumMataKuliah->id_mata_kuliah)
            ->first();

        if ($existingPeserta) {
            return [
                'success' => false,
                'message' => 'Anda sudah memilih mata kuliah ini. Batalkan pilihan terlebih dahulu jika ingin memilih kelas lain!'
            ];
        }

        // Validasi bentrok jadwal
        if ($this->cekBentrokJadwal($registrasiKrs->id_registrasi_mahasiswa, $kelasKuliah)) {
            return ['success' => false, 'message' => 'Terjadi bentrok jadwal dengan mata kuliah yang sudah dipilih!'];
        }

        // Validasi batas SKS
        $validationResult = $this->validateSksLimitsForNewCourse(
            $registrasiKrs->id_registrasi_mahasiswa,
            $mahasiswa,
            $kelasKuliah->kurikulumMataKuliah,
            $semesterAktif
        );

        if (!$validationResult['valid']) {
            return ['success' => false, 'message' => $validationResult['message']];
        }

        // Simpan pilihan mata kuliah reguler
        PesertaKelasKuliah::create([
            'id_kelas_kuliah' => $kelasKuliah->id_kelas_kuliah,
            'id_mata_kuliah' => $kelasKuliah->kurikulumMataKuliah->id_mata_kuliah,
            'id_registrasi_mahasiswa' => $registrasiKrs->id_registrasi_mahasiswa,
            'status_mata_kuliah' => 'SELECTED'
        ]);

        return [
            'success' => true,
            'message' => 'Mata kuliah reguler berhasil ditambahkan ke KRS!',
            'data' => [
                'total_sks' => $validationResult['new_total_sks'],
                'jenis' => 'reguler'
            ]
        ];
    }

    /**
     * Handle penambahan mata kuliah bimbingan
     */
    private function addMataKuliahBimbingan($request, $mahasiswa, $registrasiKrs, $semesterAktif)
    {
        $mataKuliah = MataKuliah::findOrFail($request->id_mata_kuliah);

        // Validasi jenis mata kuliah
        if (!in_array($mataKuliah->jenis_mata_kuliah, ['KKN', 'MAGANG', 'SKRIPSI'])) {
            return ['success' => false, 'message' => 'Mata kuliah ini bukan mata kuliah bimbingan!'];
        }

        // Cek apakah sudah memilih mata kuliah bimbingan yang sama
        $existingPeserta = PesertaBimbingan::where('id_registrasi_mahasiswa', $registrasiKrs->id_registrasi_mahasiswa)
            ->where('id_mata_kuliah', $mataKuliah->id_mata_kuliah)
            ->first();

        if ($existingPeserta) {
            return ['success' => false, 'message' => 'Anda sudah memilih mata kuliah bimbingan ini!'];
        }

        // Get kurikulum mata kuliah untuk validasi
        $kurikulumMataKuliah = KurikulumMataKuliah::where('id_kurikulum', $mahasiswa->id_kurikulum)
            ->where('id_mata_kuliah', $mataKuliah->id_mata_kuliah)
            ->first();

        if (!$kurikulumMataKuliah) {
            return ['success' => false, 'message' => 'Mata kuliah tidak tersedia dalam kurikulum Anda!'];
        }

        // Validasi batas SKS
        $validationResult = $this->validateSksLimitsForNewCourse(
            $registrasiKrs->id_registrasi_mahasiswa,
            $mahasiswa,
            $kurikulumMataKuliah,
            $semesterAktif
        );

        if (!$validationResult['valid']) {
            return ['success' => false, 'message' => $validationResult['message']];
        }

        // Simpan pilihan mata kuliah bimbingan
        PesertaBimbingan::create([
            'id_peserta_bimbingan' => (string) Str::uuid(),
            'id_mata_kuliah' => $mataKuliah->id_mata_kuliah,
            'id_registrasi_mahasiswa' => $registrasiKrs->id_registrasi_mahasiswa,
            'status_mata_kuliah' => 'SELECTED'
        ]);

        return [
            'success' => true,
            'message' => 'Mata kuliah bimbingan berhasil ditambahkan ke KRS! Pembimbing akan diatur oleh admin setelah KRS disetujui.',
            'data' => [
                'total_sks' => $validationResult['new_total_sks'],
                'jenis' => 'bimbingan'
            ]
        ];
    }

    /**
     * Hapus mata kuliah dari KRS
     */
    public function removeMataKuliah(Request $request)
    {
        $request->validate([
            'id_peserta' => 'required',
            'jenis' => 'required|in:reguler,bimbingan'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

            if ($request->jenis === 'reguler') {
                $peserta = PesertaKelasKuliah::with('registrasiMahasiswa')
                    ->where('id_peserta', $request->id_peserta)
                    ->whereHas('registrasiMahasiswa', function ($query) use ($mahasiswa) {
                        $query->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
                    })
                    ->first();
            } else {
                $peserta = PesertaBimbingan::with('registrasiMahasiswa')
                    ->where('id_peserta_bimbingan', $request->id_peserta)
                    ->whereHas('registrasiMahasiswa', function ($query) use ($mahasiswa) {
                        $query->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
                    })
                    ->first();
            }

            if (!$peserta) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
            }

            // Cek apakah KRS masih bisa diubah
            if ($peserta->registrasiMahasiswa->status_krs == 'APPROVED') {
                return response()->json(['success' => false, 'message' => 'KRS sudah tidak dapat diubah!'], 400);
            }

            $peserta->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mata kuliah berhasil dihapus dari KRS!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Review KRS sebelum submit
     */
    public function reviewKrs($semesterId)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        $selectedSemester = Semester::findOrFail($semesterId);
        $registrasiKrs = $this->getOrCreateRegistrasiKrs($mahasiswa, $selectedSemester);
        $mataKuliahTerpilih = $this->getMataKuliahTerpilih($registrasiKrs->id_registrasi_mahasiswa);

        // Hitung total SKS
        $totalSks = $this->hitungTotalSksTerpilih($registrasiKrs->id_registrasi_mahasiswa);
        $batasSks = $this->getBatasSks($mahasiswa, $selectedSemester);
        $sksPerKategori = $this->hitungSksPerKategori($registrasiKrs->id_registrasi_mahasiswa);
        $batasKategori = $this->getBatasKategori($mahasiswa);

        // Generate jadwal mingguan untuk mata kuliah reguler
        $jadwalMingguan = $this->generateJadwalMingguan($mataKuliahTerpilih->where('jenis', 'reguler'));

        return view('mahasiswa.krs.review', compact([
            'mahasiswa',
            'selectedSemester',
            'registrasiKrs',
            'mataKuliahTerpilih',
            'totalSks',
            'batasSks',
            'sksPerKategori',
            'batasKategori',
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

            // Validasi PA
            if (!$registrasiKrs->pembimbingAkademik) {
                return redirect()->back()->with('error', 'Anda belum mendapatkan Pembimbing Akademik untuk semester ini.');
            }

            // Reset mata kuliah yang REJECTED menjadi SELECTED
            $rejectedReguler = $registrasiKrs->pesertaKelasKuliah()
                ->where('status_mata_kuliah', 'REJECTED')
                ->update(['status_mata_kuliah' => 'SELECTED']);

            $rejectedBimbingan = $registrasiKrs->pesertaBimbingan()
                ->where('status_mata_kuliah', 'REJECTED')
                ->update(['status_mata_kuliah' => 'SELECTED']);

            // Validasi minimal ada mata kuliah
            $jumlahReguler = $registrasiKrs->pesertaKelasKuliah()
                ->whereIn('status_mata_kuliah', ['SELECTED', 'APPROVED'])
                ->count();

            $jumlahBimbingan = $registrasiKrs->pesertaBimbingan()
                ->whereIn('status_mata_kuliah', ['SELECTED', 'APPROVED'])
                ->count();

            $totalMataKuliah = $jumlahReguler + $jumlahBimbingan;

            if ($totalMataKuliah == 0) {
                return redirect()->back()->with('error', 'Anda belum memilih mata kuliah apapun!');
            }

            // Validasi batas SKS
            $validationResult = $this->validateSksLimits($registrasiKrs->id_registrasi_mahasiswa, $mahasiswa, $semesterAktif);
            if (!$validationResult['valid']) {
                return redirect()->back()->with('error', $validationResult['message']);
            }

            // Update status KRS
            $registrasiKrs->update([
                'status_krs' => 'SUBMITTED',
                'tanggal_submit' => now(),
                'alasan_reject' => null,
            ]);

            DB::commit();

            $successMessage = "KRS berhasil disubmit ke Pembimbing Akademik ({$registrasiKrs->pembimbingAkademik->dosen->pengguna->nama})! " .
                "Total {$totalMataKuliah} mata kuliah ({$jumlahReguler} reguler, {$jumlahBimbingan} bimbingan) dengan {$validationResult['totalSks']} SKS.";

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
     * Get mata kuliah terpilih (gabungan reguler dan bimbingan)
     */
    private function getMataKuliahTerpilih($registrasiId)
    {
        // Mata kuliah reguler
        $mataKuliahReguler = PesertaKelasKuliah::with([
            'mataKuliah',
            'kelasKuliah.dosen.pengguna',
            'kelasKuliah.kurikulumMataKuliah'
        ])
            ->where('id_registrasi_mahasiswa', $registrasiId)
            ->get()
            ->map(function ($peserta) {
                return (object)[
                    'id_peserta' => $peserta->id_peserta,
                    'jenis' => 'reguler',
                    'mataKuliah' => $peserta->mataKuliah,
                    'kelasKuliah' => $peserta->kelasKuliah,
                    'status_mata_kuliah' => $peserta->status_mata_kuliah,
                    'kategori_mata_kuliah' => $peserta->kelasKuliah->kurikulumMataKuliah->kategori_mata_kuliah,
                    'semester' => $peserta->kelasKuliah->kurikulumMataKuliah->semester,
                    'dosen' => $peserta->kelasKuliah->dosen->pengguna->nama ?? 'Belum diatur',
                    'kelas' => $peserta->kelasKuliah->nama_kelas_kuliah,
                    'ruangan' => $peserta->kelasKuliah->nama_ruangan,
                    'hari' => $peserta->kelasKuliah->hari,
                    'jam_mulai' => $peserta->kelasKuliah->jam_mulai,
                    'jam_akhir' => $peserta->kelasKuliah->jam_akhir,
                ];
            });

        // Mata kuliah bimbingan
        $mataKuliahBimbingan = PesertaBimbingan::with([
            'mataKuliah',
            'dosenPembimbing.pengguna',
            'dosenPembimbing2.pengguna'
        ])
            ->where('id_registrasi_mahasiswa', $registrasiId)
            ->get()
            ->map(function ($peserta) {
                // Get kurikulum mata kuliah info
                $kurikulumMk = KurikulumMataKuliah::where('id_mata_kuliah', $peserta->id_mata_kuliah)->first();

                return (object)[
                    'id_peserta' => $peserta->id_peserta_bimbingan,
                    'jenis' => 'bimbingan',
                    'mataKuliah' => $peserta->mataKuliah,
                    'kelasKuliah' => null,
                    'status_mata_kuliah' => $peserta->status_mata_kuliah,
                    'kategori_mata_kuliah' => $kurikulumMk->kategori_mata_kuliah ?? 'MKWPS',
                    'semester' => $kurikulumMk->semester ?? 0,
                    'dosen' => $peserta->dosenPembimbing->pengguna->nama ?? 'Belum diatur',
                    'dosen_pembimbing_2' => $peserta->dosenPembimbing2->pengguna->nama ?? null,
                    'kelas' => null,
                    'ruangan' => null,
                    'hari' => null,
                    'jam_mulai' => null,
                    'jam_akhir' => null,
                ];
            });

        return $mataKuliahReguler->concat($mataKuliahBimbingan);
    }

    /**
     * Get mata kuliah tersedia per semester
     */
    private function getMataKuliahTersediaPerSemester($mahasiswa, $semesterAktif, $semesterMahasiswa)
    {
        $mataKuliahTersedia = KurikulumMataKuliah::with([
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
                // Exclude mata kuliah yang sudah lulus
                $query->select(DB::raw(1))
                    ->from('peserta_kelas_kuliah as pkk')
                    ->join('registrasi_mahasiswa as rm', 'pkk.id_registrasi_mahasiswa', '=', 'rm.id_registrasi_mahasiswa')
                    ->join('nilai_perkuliahan as np', 'pkk.id_peserta', '=', 'np.id_peserta')
                    ->join('kelas_kuliah as kk', 'pkk.id_kelas_kuliah', '=', 'kk.id_kelas_kuliah')
                    ->where('kk.id_kurikulum_mata_kuliah', '=', DB::raw('kurikulum_mata_kuliah.id'))
                    ->where('rm.id_mahasiswa', $mahasiswa->id_mahasiswa)
                    ->whereIn('np.nilai_huruf', ['A', 'B+', 'B', 'C+', 'C']);
            })
            ->get();

        // Group by semester
        $grouped = [];
        foreach ($mataKuliahTersedia as $kurikulumMk) {
            $semester = $kurikulumMk->semester;
            if (!isset($grouped[$semester])) {
                $grouped[$semester] = [];
            }
            $grouped[$semester][] = $kurikulumMk;
        }

        // Sort semesters
        ksort($grouped);

        return $grouped;
    }

    /**
     * Hitung total SKS yang sudah dipilih (reguler + bimbingan)
     */
    private function hitungTotalSksTerpilih($registrasiId)
    {
        $sksReguler = PesertaKelasKuliah::with('mataKuliah')
            ->where('id_registrasi_mahasiswa', $registrasiId)
            ->get()
            ->sum(function ($peserta) {
                return $peserta->mataKuliah->sks_mata_kuliah;
            });

        $sksBimbingan = PesertaBimbingan::with('mataKuliah')
            ->where('id_registrasi_mahasiswa', $registrasiId)
            ->get()
            ->sum(function ($peserta) {
                return $peserta->mataKuliah->sks_mata_kuliah;
            });

        return $sksReguler + $sksBimbingan;
    }

    /**
     * Hitung SKS per kategori (reguler + bimbingan)
     */
    private function hitungSksPerKategori($registrasiId)
    {
        $sksPerKategori = [
            'MKWUUPT' => 0,
            'MKWU' => 0,
            'MKWPS' => 0,
            'MKP' => 0
        ];

        // Dari mata kuliah reguler
        $pesertaReguler = PesertaKelasKuliah::with(['mataKuliah', 'kelasKuliah.kurikulumMataKuliah'])
            ->where('id_registrasi_mahasiswa', $registrasiId)
            ->get();

        foreach ($pesertaReguler as $peserta) {
            $kategori = $peserta->kelasKuliah->kurikulumMataKuliah->kategori_mata_kuliah;
            $sks = $peserta->mataKuliah->sks_mata_kuliah;
            $sksPerKategori[$kategori] += $sks;
        }

        // Dari mata kuliah bimbingan
        $pesertaBimbingan = PesertaBimbingan::with('mataKuliah')
            ->where('id_registrasi_mahasiswa', $registrasiId)
            ->get();

        foreach ($pesertaBimbingan as $peserta) {
            $kurikulumMk = KurikulumMataKuliah::where('id_mata_kuliah', $peserta->id_mata_kuliah)->first();
            $kategori = $kurikulumMk->kategori_mata_kuliah ?? 'MKWPS';
            $sks = $peserta->mataKuliah->sks_mata_kuliah;
            $sksPerKategori[$kategori] += $sks;
        }

        return $sksPerKategori;
    }

    /**
     * Validasi batas SKS untuk mata kuliah baru
     */
    private function validateSksLimitsForNewCourse($registrasiId, $mahasiswa, $kurikulumMataKuliah, $selectedSemester)
    {
        $totalSksSaatIni = $this->hitungTotalSksTerpilih($registrasiId);
        $batasSks = $this->getBatasSks($mahasiswa, $selectedSemester);
        $sksBaru = $kurikulumMataKuliah->mataKuliah->sks_mata_kuliah;
        $newTotalSks = $totalSksSaatIni + $sksBaru;

        // Validasi total SKS
        if ($newTotalSks > $batasSks) {
            return [
                'valid' => false,
                'message' => "Total SKS akan melebihi batas maksimal ({$batasSks} SKS)!"
            ];
        }

        // Validasi SKS per kategori
        $kategori = $kurikulumMataKuliah->kategori_mata_kuliah;
        $sksKategoriSaatIni = $this->hitungSksKategori($registrasiId, $kategori);
        $batasKategori = $this->getBatasKategori($mahasiswa);
        $newSksKategori = $sksKategoriSaatIni + $sksBaru;

        if ($newSksKategori > $batasKategori[$kategori]) {
            return [
                'valid' => false,
                'message' => "SKS kategori {$kategori} akan melebihi batas maksimal ({$batasKategori[$kategori]} SKS)!"
            ];
        }

        return [
            'valid' => true,
            'new_total_sks' => $newTotalSks,
            'new_kategori_sks' => $newSksKategori
        ];
    }

    /**
     * Validasi batas SKS total dan per kategori
     */
    private function validateSksLimits($registrasiId, $mahasiswa, $selectedSemester)
    {
        $totalSks = $this->hitungTotalSksTerpilih($registrasiId);
        $batasSks = $this->getBatasSks($mahasiswa, $selectedSemester);

        // Validasi total SKS
        if ($totalSks > $batasSks) {
            return [
                'valid' => false,
                'message' => "Total SKS ({$totalSks}) melebihi batas maksimal yang diizinkan ({$batasSks} SKS)!"
            ];
        }

        // Validasi SKS per kategori
        $sksPerKategori = $this->hitungSksPerKategori($registrasiId);
        $batasKategori = $this->getBatasKategori($mahasiswa);

        foreach ($sksPerKategori as $kategori => $sks) {
            if ($sks > $batasKategori[$kategori]) {
                return [
                    'valid' => false,
                    'message' => "SKS kategori {$kategori} ({$sks}) melebihi batas maksimal ({$batasKategori[$kategori]} SKS)!"
                ];
            }
        }

        return [
            'valid' => true,
            'totalSks' => $totalSks
        ];
    }

    /**
     * Hitung SKS untuk kategori tertentu (reguler + bimbingan)
     */
    private function hitungSksKategori($registrasiId, $kategori)
    {
        $sksReguler = PesertaKelasKuliah::with(['mataKuliah', 'kelasKuliah.kurikulumMataKuliah'])
            ->where('id_registrasi_mahasiswa', $registrasiId)
            ->whereHas('kelasKuliah.kurikulumMataKuliah', function ($query) use ($kategori) {
                $query->where('kategori_mata_kuliah', $kategori);
            })
            ->get()
            ->sum(function ($peserta) {
                return $peserta->mataKuliah->sks_mata_kuliah;
            });

        $sksBimbingan = PesertaBimbingan::with('mataKuliah')
            ->where('id_registrasi_mahasiswa', $registrasiId)
            ->whereHas('mataKuliah', function ($query) use ($kategori) {
                $query->whereExists(function ($subQuery) use ($kategori) {
                    $subQuery->select(DB::raw(1))
                        ->from('kurikulum_mata_kuliah')
                        ->whereColumn('kurikulum_mata_kuliah.id_mata_kuliah', 'mata_kuliah.id_mata_kuliah')
                        ->where('kategori_mata_kuliah', $kategori);
                });
            })
            ->get()
            ->sum(function ($peserta) {
                return $peserta->mataKuliah->sks_mata_kuliah;
            });

        return $sksReguler + $sksBimbingan;
    }

    // ... (methods lainnya tetap sama seperti getBatasSks, getBatasKategori, dll)
    // Saya akan lanjutkan di response berikutnya karena keterbatasan space

    /**
     * Get batas SKS maksimal berdasarkan IPK
     */
    private function getBatasSks($mahasiswa, $selectedSemester)
    {
        $semesterMahasiswa = $this->hitungSemesterMahasiswa($mahasiswa, $selectedSemester);

        if ($semesterMahasiswa <= 2) {
            return $this->getBatasSksUntuMahasiswaBaru($semesterMahasiswa);
        }

        $ipk = $this->hitungIPK($mahasiswa->id_mahasiswa);

        if ($ipk >= 3.0) {
            return 24;
        } elseif ($ipk >= 2.5) {
            return 21;
        } else {
            return 18;
        }
    }

    /**
     * Get batas SKS khusus untuk mahasiswa baru
     */
    private function getBatasSksUntuMahasiswaBaru($semesterMahasiswa)
    {
        switch ($semesterMahasiswa) {
            case 1:
                return 20;
            case 2:
                return 22;
            default:
                return 24;
        }
    }

    /**
     * Get batas SKS per kategori
     */
    private function getBatasKategori($mahasiswa)
    {
        $kurikulum = $mahasiswa->kurikulum;

        return [
            'MKWUUPT' => $kurikulum->sks_mkwuupt_minimal,
            'MKWU' => $kurikulum->sks_mkwu_minimal,
            'MKWPS' => $kurikulum->sks_mkwps_minimal,
            'MKP' => $kurikulum->sks_mkp_minimal
        ];
    }

    /**
     * Hitung semester mahasiswa saat ini
     */
    private function hitungSemesterMahasiswa($mahasiswa, $semesterAktif)
    {
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
        }

        return $registrasiKrs;
    }

    /**
     * Cek bentrok jadwal untuk mata kuliah reguler
     */
    private function cekBentrokJadwal($registrasiId, $kelasKuliahBaru)
    {
        $kelasKuliahTerpilih = PesertaKelasKuliah::with('kelasKuliah')
            ->where('id_registrasi_mahasiswa', $registrasiId)
            ->get();

        foreach ($kelasKuliahTerpilih as $peserta) {
            $kelas = $peserta->kelasKuliah;

            if (
                $kelas->hari === $kelasKuliahBaru->hari &&
                $kelas->jam_mulai && $kelas->jam_akhir &&
                $kelasKuliahBaru->jam_mulai && $kelasKuliahBaru->jam_akhir
            ) {
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
     * Hitung IPK mahasiswa
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
            return 3.0;
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
    private function generateJadwalMingguan($mataKuliahReguler)
    {
        $jadwal = [];
        $hariList = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];

        foreach ($hariList as $hari) {
            $jadwal[$hari] = [];
        }

        foreach ($mataKuliahReguler as $peserta) {
            if ($peserta->hari && $peserta->jam_mulai && $peserta->jam_akhir) {
                $jadwal[$peserta->hari][] = [
                    'mata_kuliah' => $peserta->mataKuliah->nama_mata_kuliah,
                    'kode_mata_kuliah' => $peserta->mataKuliah->kode_mata_kuliah,
                    'kelas' => $peserta->kelas,
                    'ruangan' => $peserta->ruangan,
                    'dosen' => $peserta->dosen,
                    'jam_mulai' => $peserta->jam_mulai,
                    'jam_akhir' => $peserta->jam_akhir,
                    'sks' => $peserta->mataKuliah->sks_mata_kuliah,
                    'kategori' => $peserta->kategori_mata_kuliah
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

    // Untuk program studi dengan durasi berbeda
    private function getSemestersByAngkatan($angkatan, $durasiTahun = 4)
    {
        $semesterAwal = $angkatan . '1';
        $tahunMaksimal = $angkatan + $durasiTahun + 1; // +1 tahun buffer
        $semesterMaksimal = $tahunMaksimal . '2';

        return Semester::where('kode_semester', '>=', $semesterAwal)
            ->where('kode_semester', '<=', $semesterMaksimal)
            ->orderBy('kode_semester', 'desc')
            ->get();
    }
}
