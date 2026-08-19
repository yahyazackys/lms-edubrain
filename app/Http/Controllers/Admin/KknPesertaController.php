<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KknKelompok;
use App\Models\KknDetail;
use App\Models\PesertaBimbingan;
use App\Models\Semester;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class KknPesertaController extends Controller
{
    /**
     * Tampilkan halaman assign peserta KKN dengan filter semester
     */
    public function index(Request $request): View
    {
        $selectedSemesterId = $request->get('semester');
        $selectedKelompokId = $request->get('kelompok'); // Tambah parameter kelompok
        $selectedSemester = null;
        $selectedKelompok = null;
        $anggotaKelompok = collect();
        $mahasiswaTersedia = collect();

        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);

            if ($selectedSemester) {
                // Jika ada parameter kelompok, fokus ke kelompok tersebut
                if ($selectedKelompokId) {
                    $selectedKelompok = KknKelompok::with([
                        'dpl.pengguna',
                        'kknDetails.pesertaBimbingan.registrasiMahasiswa.mahasiswa.pengguna',
                        'kknDetails.pesertaBimbingan.registrasiMahasiswa.mahasiswa.programStudi'
                    ])->where('id_semester', $selectedSemesterId)
                        ->find($selectedKelompokId);

                    if ($selectedKelompok) {
                        // Get anggota kelompok ini saja
                        $anggotaKelompok = $selectedKelompok->kknDetails()
                            ->with([
                                'pesertaBimbingan.registrasiMahasiswa.mahasiswa.pengguna',
                                'pesertaBimbingan.registrasiMahasiswa.mahasiswa.programStudi'
                            ])
                            ->get();
                    }
                }

                // Get mahasiswa yang tersedia untuk KKN (belum di-assign ke kelompok manapun)
                $mataKuliahKkn = MataKuliah::where('jenis_mata_kuliah', 'KKN')->first();
                if ($mataKuliahKkn) {
                    $mahasiswaTersedia = PesertaBimbingan::with([
                        'registrasiMahasiswa.mahasiswa.pengguna',
                        'registrasiMahasiswa.mahasiswa.programStudi',
                        'mataKuliah'
                    ])
                        ->whereHas('registrasiMahasiswa', function ($q) use ($selectedSemesterId) {
                            $q->where('id_semester', $selectedSemesterId);
                        })
                        ->where('id_mata_kuliah', $mataKuliahKkn->id_mata_kuliah)
                        ->where('status_mata_kuliah', 'APPROVED')
                        ->whereDoesntHave('kknDetail')
                        ->orderBy('created_at')
                        ->get();
                }
            }
        }

        // Data untuk dropdown
        $semesters = Semester::orderByDesc('tanggal_mulai')->get();
        $kelompokOptions = collect();

        if ($selectedSemester) {
            $kelompokOptions = KknKelompok::where('id_semester', $selectedSemesterId)
                ->orderBy('nama_kelompok')
                ->get();
        }

        return view('admin.bimbingan.kkn.peserta.index', compact(
            'anggotaKelompok',
            'mahasiswaTersedia',
            'semesters',
            'kelompokOptions',
            'selectedSemester',
            'selectedKelompok',
            'selectedSemesterId',
            'selectedKelompokId'
        ));
    }

    /**
     * Assign mahasiswa ke kelompok KKN
     */
    public function assignMahasiswa(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_kelompok_kkn' => 'required|exists:kkn_kelompok,id_kelompok_kkn',
            'id_peserta_bimbingan' => 'required|exists:peserta_bimbingan,id_peserta_bimbingan',
            'peran_kelompok' => 'required|in:KETUA,ANGGOTA',
        ], [
            'id_kelompok_kkn.required' => 'Kelompok KKN wajib dipilih.',
            'id_kelompok_kkn.exists' => 'Kelompok KKN tidak valid.',
            'id_peserta_bimbingan.required' => 'Mahasiswa wajib dipilih.',
            'id_peserta_bimbingan.exists' => 'Mahasiswa tidak valid.',
            'peran_kelompok.required' => 'Peran kelompok wajib dipilih.',
            'peran_kelompok.in' => 'Peran kelompok tidak valid.',
        ]);

        // Cek apakah mahasiswa sudah di-assign ke kelompok lain
        $existingAssignment = KknDetail::where('id_peserta_bimbingan', $validated['id_peserta_bimbingan'])->exists();
        if ($existingAssignment) {
            return back()->with('error', 'Mahasiswa sudah di-assign ke kelompok lain.');
        }

        // Jika peran KETUA, cek apakah kelompok sudah punya ketua
        if ($validated['peran_kelompok'] === 'KETUA') {
            $existingKetua = KknDetail::where('id_kelompok_kkn', $validated['id_kelompok_kkn'])
                ->where('peran_kelompok', 'KETUA')
                ->exists();

            if ($existingKetua) {
                return back()->with('error', 'Kelompok sudah memiliki ketua. Pilih peran ANGGOTA atau ganti ketua yang ada.');
            }
        }

        DB::transaction(function () use ($validated) {
            KknDetail::create([
                'id_kkn_detail' => (string) Str::uuid(),
                'id_peserta_bimbingan' => $validated['id_peserta_bimbingan'],
                'id_kelompok_kkn' => $validated['id_kelompok_kkn'],
                'peran_kelompok' => $validated['peran_kelompok'],
            ]);

            // Update pembimbing di peserta_bimbingan dengan DPL kelompok
            $kelompok = KknKelompok::find($validated['id_kelompok_kkn']);
            PesertaBimbingan::where('id_peserta_bimbingan', $validated['id_peserta_bimbingan'])
                ->update(['id_dosen_pembimbing' => $kelompok->id_dpl]);
        });

        return redirect()->route('bimbingan.kkn.peserta.index', ['semester' => $request->semester, 'kelompok' => $request->id_kelompok_kkn])
            ->with('success', 'Mahasiswa berhasil di-assign ke kelompok KKN.');
    }

    /**
     * Remove mahasiswa dari kelompok KKN
     */
    public function removeMahasiswa(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_kkn_detail' => 'required|exists:kkn_detail,id_kkn_detail',
        ]);

        $kknDetail = KknDetail::with('pesertaBimbingan')->findOrFail($validated['id_kkn_detail']);

        DB::transaction(function () use ($kknDetail) {
            // Reset pembimbing di peserta_bimbingan
            $kknDetail->pesertaBimbingan->update(['id_dosen_pembimbing' => null]);

            // Hapus dari kelompok
            $kknDetail->delete();
        });

        return back()->with('success', 'Mahasiswa berhasil dihapus dari kelompok KKN.');
    }

    /**
     * Update peran mahasiswa dalam kelompok
     */
    public function updatePeran(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_kkn_detail' => 'required|exists:kkn_detail,id_kkn_detail',
            'peran_kelompok' => 'required|in:KETUA,ANGGOTA',
        ]);

        $kknDetail = KknDetail::findOrFail($validated['id_kkn_detail']);

        // Jika peran KETUA, cek apakah kelompok sudah punya ketua lain
        if ($validated['peran_kelompok'] === 'KETUA') {
            $existingKetua = KknDetail::where('id_kelompok_kkn', $kknDetail->id_kelompok_kkn)
                ->where('peran_kelompok', 'KETUA')
                ->where('id_kkn_detail', '!=', $kknDetail->id_kkn_detail)
                ->exists();

            if ($existingKetua) {
                return back()->with('error', 'Kelompok sudah memiliki ketua lain. Ganti ketua yang ada terlebih dahulu.');
            }
        }

        $kknDetail->update(['peran_kelompok' => $validated['peran_kelompok']]);

        return back()->with('success', 'Peran mahasiswa berhasil diperbarui.');
    }

    /**
     * Get kelompok KKN untuk dropdown AJAX
     */
    public function getKelompok(Request $request)
    {
        $query = KknKelompok::with('dpl.pengguna');

        // Filter berdasarkan semester (dari peserta bimbingan)
        if ($request->has('semester') && $request->semester != '') {
            $query->whereHas('kknDetails.pesertaBimbingan.registrasiMahasiswa', function ($q) use ($request) {
                $q->where('id_semester', $request->semester);
            });
        }

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_kelompok', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('lokasi', 'LIKE', "%{$searchTerm}%");
            });
        }

        $kelompoks = $query->orderBy('nama_kelompok')
            ->get()
            ->map(function ($item) {
                $totalAnggota = $item->kknDetails->count();
                $hasKetua = $item->kknDetails->where('peran_kelompok', 'KETUA')->count() > 0;

                return [
                    'id_kelompok_kkn' => $item->id_kelompok_kkn,
                    'nama_kelompok' => $item->nama_kelompok,
                    'lokasi' => $item->lokasi,
                    'dpl_nama' => $item->dpl->pengguna->nama ?? 'N/A',
                    'total_anggota' => $totalAnggota,
                    'has_ketua' => $hasKetua,
                    'nama_lengkap' => $item->nama_kelompok . ' - ' . $item->lokasi . ' (' . $totalAnggota . ' anggota)',
                ];
            });

        return response()->json($kelompoks);
    }

    /**
     * Get mahasiswa yang tersedia untuk KKN
     */
    public function getMahasiswaTersedia(Request $request)
    {
        $query = PesertaBimbingan::with([
            'registrasiMahasiswa.mahasiswa.pengguna',
            'registrasiMahasiswa.mahasiswa.programStudi',
            'mataKuliah'
        ]);

        // Filter berdasarkan semester
        if ($request->has('semester') && $request->semester != '') {
            $query->whereHas('registrasiMahasiswa', function ($q) use ($request) {
                $q->where('id_semester', $request->semester);
            });
        }

        // Filter hanya mata kuliah KKN
        $query->whereHas('mataKuliah', function ($q) {
            $q->where('jenis_mata_kuliah', 'KKN');
        });

        // Filter hanya yang APPROVED dan belum di-assign
        $query->where('status_mata_kuliah', 'APPROVED')
            ->whereDoesntHave('kknDetail');

        // Pencarian
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('registrasiMahasiswa.mahasiswa.pengguna', function ($mq) use ($searchTerm) {
                    $mq->where('nama', 'LIKE', "%{$searchTerm}%");
                })
                    ->orWhereHas('registrasiMahasiswa.mahasiswa', function ($mq) use ($searchTerm) {
                        $mq->where('nim', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('registrasiMahasiswa.mahasiswa.programStudi', function ($mq) use ($searchTerm) {
                        $mq->where('nama_program_studi', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        $mahasiswas = $query->orderBy('created_at')
            ->get()
            ->map(function ($item) {
                $mahasiswa = $item->registrasiMahasiswa->mahasiswa;
                return [
                    'id_peserta_bimbingan' => $item->id_peserta_bimbingan,
                    'nim' => $mahasiswa->nim,
                    'nama' => $mahasiswa->pengguna->nama,
                    'program_studi' => $mahasiswa->programStudi->nama_program_studi ?? 'N/A',
                    'nama_lengkap' => $mahasiswa->nim . ' - ' . $mahasiswa->pengguna->nama . ' (' . ($mahasiswa->programStudi->nama_program_studi ?? 'N/A') . ')',
                ];
            });

        return response()->json($mahasiswas);
    }
}
