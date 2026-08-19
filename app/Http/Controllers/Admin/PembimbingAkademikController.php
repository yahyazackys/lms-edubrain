<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Semester;
use App\Models\PembimbingAkademik;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PembimbingAkademikController extends Controller
{
    /**
     * Tampilkan halaman pembimbing akademik dengan filter semester
     */
    public function index(Request $request): View
    {
        // Semester options for dropdown
        $semesters = Semester::orderBy('kode_semester', 'desc')->get();
        $selectedSemester = null;
        $mahasiswas = collect();
        $angkatans = collect();

        // Jika semester dipilih, load data mahasiswa
        if ($request->filled('semester')) {
            $selectedSemester = Semester::find($request->semester);

            if ($selectedSemester) {
                // Query mahasiswa dengan relasi PA
                $query = Mahasiswa::with([
                    'pengguna',
                    'programStudi.jenjang',
                    'pembimbingAkademik' => function ($q) use ($request) {
                        $q->where('id_semester', $request->semester)
                            ->with(['dosen.pengguna', 'semester']);
                    }
                ])
                    ->whereHas('programStudi', function ($q) {
                        $q->where('status', 'A');
                    });

                $mahasiswas = $query->orderBy('angkatan', 'desc')
                    ->orderBy('nim')
                    ->get()
                    ->map(function ($mahasiswa) use ($selectedSemester) {
                        // Hitung semester mahasiswa
                        $semesterMahasiswa = $this->calculateSemesterMahasiswa(
                            $mahasiswa->angkatan,
                            $selectedSemester->kode_semester
                        );

                        $mahasiswa->semester_mahasiswa = $semesterMahasiswa;

                        // Get PA info
                        $pa = $mahasiswa->pembimbingAkademik->first();
                        $mahasiswa->pa_info = $pa;

                        return $mahasiswa;
                    });

                // Get unique angkatans
                $angkatans = $mahasiswas->pluck('angkatan')->unique()->sort()->reverse()->values();
            }
        }

        // Status PA options
        $statusPaOptions = [
            'AKTIF' => 'Aktif',
            'SELESAI' => 'Selesai',
            'BELUM' => 'Belum Ditentukan'
        ];

        return view('admin.pembimbing-akademik.index', compact(
            'semesters',
            'selectedSemester',
            'mahasiswas',
            'angkatans',
            'statusPaOptions'
        ));
    }

    /**
     * Assign dosen PA baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_mahasiswa' => 'required|exists:mahasiswa,id_mahasiswa',
            'id_dosen' => 'required|exists:dosen,id_dosen',
            'id_semester' => 'required|exists:semester,id_semester',
            'nomor_sk' => 'nullable|string|max:100',
            'tanggal_sk' => 'nullable|date',
        ]);

        // Cek apakah mahasiswa sudah punya PA di semester ini
        $existingPa = PembimbingAkademik::where('id_mahasiswa', $validated['id_mahasiswa'])
            ->where('id_semester', $validated['id_semester'])
            ->first();

        if ($existingPa) {
            return back()->with('error', 'Mahasiswa sudah memiliki pembimbing akademik di semester ini.');
        }

        // Cek kuota dosen
        $kuotaTersisa = $this->getKuotaTersisa($validated['id_dosen'], $validated['id_semester']);
        if ($kuotaTersisa <= 0) {
            return back()->with('error', 'Kuota dosen pembimbing akademik sudah habis.');
        }

        DB::beginTransaction();
        try {
            PembimbingAkademik::create([
                'id_pembimbing_akademik' => (string) Str::uuid(),
                'id_mahasiswa' => $validated['id_mahasiswa'],
                'id_dosen' => $validated['id_dosen'],
                'id_semester' => $validated['id_semester'],
                'nomor_sk' => $validated['nomor_sk'],
                'tanggal_sk' => $validated['tanggal_sk'],
                'status_pa' => 'AKTIF',
            ]);

            DB::commit();

            $mahasiswa = Mahasiswa::with('pengguna')->find($validated['id_mahasiswa']);
            $dosen = Dosen::with('pengguna')->find($validated['id_dosen']);

            return back()->with('success', "Berhasil menugaskan {$dosen->pengguna->nama} sebagai PA untuk {$mahasiswa->pengguna->nama}");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update assignment dosen PA
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $pa = PembimbingAkademik::findOrFail($id);

        $validated = $request->validate([
            'id_dosen' => 'required|exists:dosen,id_dosen',
            'nomor_sk' => 'nullable|string|max:100',
            'tanggal_sk' => 'nullable|date',
            'status_pa' => 'required|in:AKTIF,SELESAI',
        ]);

        // Jika ganti dosen, cek kuota dosen baru
        if ($pa->id_dosen !== $validated['id_dosen']) {
            $kuotaTersisa = $this->getKuotaTersisa($validated['id_dosen'], $pa->id_semester);
            if ($kuotaTersisa <= 0) {
                return back()->with('error', 'Kuota dosen pembimbing akademik yang dipilih sudah habis.');
            }
        }

        DB::beginTransaction();
        try {
            $pa->update($validated);
            DB::commit();

            $mahasiswa = $pa->mahasiswa;
            $dosen = Dosen::with('pengguna')->find($validated['id_dosen']);

            return back()->with('success', "Data PA {$mahasiswa->pengguna->nama} berhasil diperbarui");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus assignment PA
     */
    public function destroy(string $id): RedirectResponse
    {
        $pa = PembimbingAkademik::with(['mahasiswa.pengguna', 'dosen.pengguna'])->findOrFail($id);

        DB::beginTransaction();
        try {
            $mahasiswaName = $pa->mahasiswa->pengguna->nama;
            $pa->delete();

            DB::commit();
            return back()->with('success', "Assignment PA untuk {$mahasiswaName} berhasil dihapus");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get dosen dengan kuota tersisa (AJAX)
     */
    public function getDosenWithKuota(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $search = $request->get('search', '');

        if (!$semesterId) {
            return response()->json([]);
        }

        $query = Dosen::with('pengguna')
            ->where('status_dosen', 'AKTIF')
            ->whereNotNull('total_kuota_pa')
            ->where('total_kuota_pa', '>', 0);

        // Search filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nidn', 'like', "%{$search}%")
                    ->orWhereHas('pengguna', function ($q2) use ($search) {
                        $q2->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $dosens = $query->get()->map(function ($dosen) use ($semesterId) {
            $kuotaTersisa = $this->getKuotaTersisa($dosen->id_dosen, $semesterId);

            if ($kuotaTersisa > 0) {
                return [
                    'id_dosen' => $dosen->id_dosen,
                    'nidn' => $dosen->nidn,
                    'nama' => $dosen->pengguna->nama,
                    'gelar_depan' => $dosen->gelar_depan,
                    'gelar_belakang' => $dosen->gelar_belakang,
                    'nama_lengkap' => trim(($dosen->gelar_depan ? $dosen->gelar_depan . ' ' : '') .
                        $dosen->pengguna->nama .
                        ($dosen->gelar_belakang ? ', ' . $dosen->gelar_belakang : '')),
                    'total_kuota' => $dosen->total_kuota_pa,
                    'kuota_tersisa' => $kuotaTersisa,
                    'kuota_terpakai' => $dosen->total_kuota_pa - $kuotaTersisa,
                ];
            }
            return null;
        })->filter()->values();

        return response()->json($dosens);
    }

    /**
     * Hitung semester mahasiswa berdasarkan angkatan dan periode semester
     */
    private function calculateSemesterMahasiswa(int $angkatan, string $semesterId): int
    {
        // Format semester ID: YYYYS (contoh: 20251, 20252)
        $tahunSemester = (int) substr($semesterId, 0, 4);
        $periodeSemester = (int) substr($semesterId, 4, 1);

        // Hitung selisih tahun
        $selisihTahun = $tahunSemester - $angkatan;

        // Hitung semester
        $semester = ($selisihTahun * 2) + $periodeSemester;

        // Minimal semester 1
        return max(1, $semester);
    }

    /**
     * Hitung kuota tersisa dosen di semester tertentu
     */
    private function getKuotaTersisa(string $dosenId, string $semesterId): int
    {
        $dosen = Dosen::find($dosenId);
        if (!$dosen || !$dosen->total_kuota_pa) {
            return 0;
        }

        $terpakai = PembimbingAkademik::where('id_dosen', $dosenId)
            ->where('id_semester', $semesterId)
            ->where('status_pa', 'AKTIF')
            ->count();

        return max(0, $dosen->total_kuota_pa - $terpakai);
    }
}
