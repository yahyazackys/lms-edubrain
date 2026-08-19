<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KelasKuliah;
use App\Models\ProgramStudi;
use App\Models\Kurikulum;
use App\Models\Semester;
use App\Models\Dosen;
use App\Models\KurikulumMataKuliah;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class KelasKuliahController extends Controller
{
    /**
     * Tampilkan halaman kelas kuliah dengan filter semester wajib
     */
    public function index(Request $request): View
    {
        // Get semester yang dipilih
        $selectedSemesterId = $request->get('semester');
        $selectedSemester = null;
        $kelasKuliahs = collect();

        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);

            if ($selectedSemester) {
                $query = KelasKuliah::with([
                    'kurikulumMataKuliah.kurikulum.programStudi.jenjang',
                    'kurikulumMataKuliah.mataKuliah',
                    'semester',
                    'dosen.pengguna'
                ])->where('id_semester', $selectedSemesterId);

                // Filter berdasarkan program studi
                if ($request->has('program_studi') && $request->program_studi != '') {
                    $query->whereHas('kurikulumMataKuliah.kurikulum', function ($q) use ($request) {
                        $q->where('id_program_studi', $request->program_studi);
                    });
                }

                // Pencarian
                if ($request->has('search') && $request->search != '') {
                    $searchTerm = $request->search;
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('nama_kelas_kuliah', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('nama_ruangan', 'LIKE', "%{$searchTerm}%")
                            ->orWhereHas('kurikulumMataKuliah.mataKuliah', function ($mq) use ($searchTerm) {
                                $mq->where('kode_mata_kuliah', 'LIKE', "%{$searchTerm}%")
                                    ->orWhere('nama_mata_kuliah', 'LIKE', "%{$searchTerm}%");
                            })
                            ->orWhereHas('dosen.pengguna', function ($dq) use ($searchTerm) {
                                $dq->where('nama', 'LIKE', "%{$searchTerm}%");
                            });
                    });
                }

                $kelasKuliahs = $query->orderBy('nama_kelas_kuliah')->get();
            }
        }

        // Data untuk dropdown
        $semesters = Semester::orderByDesc('tanggal_mulai')->get();
        $programStudis = ProgramStudi::with('jenjang')->orderBy('nama_program_studi')->get();

        return view('admin.kelas-kuliah.index', compact(
            'kelasKuliahs',
            'semesters',
            'programStudis',
            'selectedSemester',
            'selectedSemesterId'
        ));
    }

    /**
     * Simpan kelas kuliah baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas_kuliah' => 'required|string|max:100',
            'nama_ruangan' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:1|max:200',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_akhir' => 'nullable|date_format:H:i|after:jam_mulai',
            'id_kurikulum_mata_kuliah' => 'required|exists:kurikulum_mata_kuliah,id',
            'id_semester' => 'required|exists:semester,id_semester',
            'id_dosen' => 'required|exists:dosen,id_dosen',
        ], [
            'nama_kelas_kuliah.required' => 'Nama kelas kuliah wajib diisi.',
            'nama_kelas_kuliah.max' => 'Nama kelas kuliah maksimal 100 karakter.',
            'nama_ruangan.required' => 'Nama ruangan wajib diisi.',
            'nama_ruangan.max' => 'Nama ruangan maksimal 100 karakter.',
            'kapasitas.required' => 'Kapasitas kelas wajib diisi.',
            'kapasitas.min' => 'Kapasitas kelas minimal 1.',
            'kapasitas.max' => 'Kapasitas kelas maksimal 200.',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid (HH:MM).',
            'jam_akhir.date_format' => 'Format jam akhir tidak valid (HH:MM).',
            'jam_akhir.after' => 'Jam akhir harus setelah jam mulai.',
            'id_kurikulum_mata_kuliah.required' => 'Mata kuliah wajib dipilih.',
            'id_kurikulum_mata_kuliah.exists' => 'Mata kuliah tidak valid.',
            'id_semester.required' => 'Semester wajib dipilih.',
            'id_semester.exists' => 'Semester tidak valid.',
            'id_dosen.required' => 'Dosen wajib dipilih.',
            'id_dosen.exists' => 'Dosen tidak valid.',
        ]);

        // Cek duplikasi kelas dalam semester yang sama
        $exists = KelasKuliah::where('nama_kelas_kuliah', $validated['nama_kelas_kuliah'])
            ->where('id_semester', $validated['id_semester'])
            ->whereRaw('id_kurikulum_mata_kuliah = ?', [$validated['id_kurikulum_mata_kuliah']])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Kelas kuliah dengan nama dan mata kuliah yang sama sudah ada di semester ini.');
        }

        KelasKuliah::create([
            'id_kelas_kuliah' => (string) Str::uuid(),
            'nama_kelas_kuliah' => $validated['nama_kelas_kuliah'],
            'nama_ruangan' => $validated['nama_ruangan'],
            'kapasitas' => $validated['kapasitas'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_akhir' => $validated['jam_akhir'],
            'id_kurikulum_mata_kuliah' => $validated['id_kurikulum_mata_kuliah'],
            'id_semester' => $validated['id_semester'],
            'id_dosen' => $validated['id_dosen'],
        ]);

        return redirect()->route('kelas-kuliah.index', ['semester' => $validated['id_semester']])
            ->with('success', 'Kelas kuliah berhasil ditambahkan.');
    }

    /**
     * Update kelas kuliah
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $kelasKuliah = KelasKuliah::findOrFail($id);

        $validated = $request->validate([
            'nama_kelas_kuliah' => 'required|string|max:100',
            'nama_ruangan' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:1|max:200',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_akhir' => 'nullable|date_format:H:i|after:jam_mulai',
            'id_kurikulum_mata_kuliah' => 'required|exists:kurikulum_mata_kuliah,id',
            'id_semester' => 'required|exists:semester,id_semester',
            'id_dosen' => 'required|exists:dosen,id_dosen',
        ], [
            'nama_kelas_kuliah.required' => 'Nama kelas kuliah wajib diisi.',
            'nama_kelas_kuliah.max' => 'Nama kelas kuliah maksimal 100 karakter.',
            'nama_ruangan.required' => 'Nama ruangan wajib diisi.',
            'nama_ruangan.max' => 'Nama ruangan maksimal 100 karakter.',
            'kapasitas.required' => 'Kapasitas kelas wajib diisi.',
            'kapasitas.min' => 'Kapasitas kelas minimal 1.',
            'kapasitas.max' => 'Kapasitas kelas maksimal 200.',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid (HH:MM).',
            'jam_akhir.date_format' => 'Format jam akhir tidak valid (HH:MM).',
            'jam_akhir.after' => 'Jam akhir harus setelah jam mulai.',
            'id_kurikulum_mata_kuliah.required' => 'Mata kuliah wajib dipilih.',
            'id_kurikulum_mata_kuliah.exists' => 'Mata kuliah tidak valid.',
            'id_semester.required' => 'Semester wajib dipilih.',
            'id_semester.exists' => 'Semester tidak valid.',
            'id_dosen.required' => 'Dosen wajib dipilih.',
            'id_dosen.exists' => 'Dosen tidak valid.',
        ]);

        // Cek duplikasi kelas dalam semester yang sama (kecuali data yang sedang diupdate)
        $exists = KelasKuliah::where('nama_kelas_kuliah', $validated['nama_kelas_kuliah'])
            ->where('id_semester', $validated['id_semester'])
            ->whereRaw('id_kurikulum_mata_kuliah = ?', [$validated['id_kurikulum_mata_kuliah']])
            ->where('id_kelas_kuliah', '!=', $kelasKuliah->id_kelas_kuliah)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Kelas kuliah dengan nama dan mata kuliah yang sama sudah ada di semester ini.');
        }

        $kelasKuliah->update($validated);

        return redirect()->route('kelas-kuliah.index', ['semester' => $validated['id_semester']])
            ->with('success', 'Kelas kuliah berhasil diperbarui.');
    }

    /**
     * Hapus kelas kuliah
     */
    public function destroy(string $id): RedirectResponse
    {
        $kelasKuliah = KelasKuliah::findOrFail($id);
        $kelasKuliahName = $kelasKuliah->nama_kelas_kuliah;
        $semesterId = $kelasKuliah->id_semester;

        // Cek apakah kelas kuliah masih memiliki peserta
        if ($kelasKuliah->pesertaKelasKuliah()->where('status_mata_kuliah', 'APPROVED')->exists()) {
            return back()->with('error', 'Kelas kuliah tidak dapat dihapus karena masih memiliki peserta.');
        }

        $kelasKuliah->delete();

        return redirect()->route('kelas-kuliah.index', ['semester' => $semesterId])
            ->with('success', "Kelas kuliah \"{$kelasKuliahName}\" berhasil dihapus.");
    }

    /**
     * Get program studi untuk search AJAX
     */
    public function getProgramStudi(Request $request)
    {
        $query = ProgramStudi::with('jenjang');

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode_program_studi', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('nama_program_studi', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('jenjang', function ($jq) use ($searchTerm) {
                        $jq->where('nama_jenjang_pendidikan', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('kode_jenjang_pendidikan', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        $programStudis = $query->orderBy('nama_program_studi')
            ->get()
            ->map(function ($item) {
                return [
                    'id_program_studi' => $item->id_program_studi,
                    'kode_program_studi' => $item->kode_program_studi,
                    'nama_program_studi' => $item->nama_program_studi,
                    'jenjang' => $item->jenjang->kode_jenjang_pendidikan,
                    'nama_lengkap' => ($item->kode_program_studi ? $item->kode_program_studi . ' - ' : '') . $item->jenjang->kode_jenjang_pendidikan . ' ' . $item->nama_program_studi
                ];
            });

        return response()->json($programStudis);
    }

    /**
     * Get kurikulum berdasarkan program studi
     */
    public function getKurikulum(Request $request)
    {
        $query = Kurikulum::with('programStudi.jenjang', 'semester');

        // Filter berdasarkan program studi (wajib)
        if ($request->has('program_studi') && $request->program_studi != '') {
            $query->where('id_program_studi', $request->program_studi);
        } else {
            // Jika tidak ada program studi dipilih, kembalikan array kosong
            return response()->json([]);
        }

        // Pencarian
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_kurikulum', 'LIKE', "%{$searchTerm}%");
            });
        }

        $kurikulums = $query->orderBy('nama_kurikulum')
            ->get()
            ->map(function ($item) {
                return [
                    'id_kurikulum' => $item->id_kurikulum,
                    'nama_kurikulum' => $item->nama_kurikulum,
                    'program_studi' => $item->programStudi->nama_program_studi,
                    'semester' => $item->semester->nama_semester,
                    'jenjang' => $item->programStudi->jenjang->kode_jenjang_pendidikan,
                    'nama_lengkap' => $item->nama_kurikulum . ' (' . $item->semester->nama_semester . ')'
                ];
            });

        return response()->json($kurikulums);
    }

    /**
     * Get mata kuliah berdasarkan kurikulum
     */
    public function getKurikulumMataKuliah(Request $request)
    {
        $query = DB::table('kurikulum_mata_kuliah')
            ->join('kurikulum', 'kurikulum_mata_kuliah.id_kurikulum', '=', 'kurikulum.id_kurikulum')
            ->join('mata_kuliah', 'kurikulum_mata_kuliah.id_mata_kuliah', '=', 'mata_kuliah.id_mata_kuliah')
            ->join('program_studi', 'kurikulum.id_program_studi', '=', 'program_studi.id_program_studi')
            ->join('jenjang_pendidikan', 'program_studi.id_jenjang_pendidikan', '=', 'jenjang_pendidikan.id_jenjang_pendidikan');

        // Filter berdasarkan kurikulum (wajib)
        if ($request->has('kurikulum') && $request->kurikulum != '') {
            $query->where('kurikulum_mata_kuliah.id_kurikulum', $request->kurikulum);
        } else {
            // Jika tidak ada kurikulum dipilih, kembalikan array kosong
            return response()->json([]);
        }

        // Pencarian
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('mata_kuliah.kode_mata_kuliah', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('mata_kuliah.nama_mata_kuliah', 'LIKE', "%{$searchTerm}%");
            });
        }

        $kurikulumMataKuliahs = $query->select([
            'kurikulum_mata_kuliah.id',
            'kurikulum_mata_kuliah.semester as semester_mk',
            'kurikulum_mata_kuliah.kategori_mata_kuliah', // ✅ Fix: Gunakan kategori_mata_kuliah dari kurikulum_mata_kuliah
            'mata_kuliah.jenis_mata_kuliah',              // ✅ Fix: Ambil jenis_mata_kuliah dari mata_kuliah
            'mata_kuliah.kode_mata_kuliah',
            'mata_kuliah.nama_mata_kuliah',
            'mata_kuliah.sks_mata_kuliah',
            'program_studi.nama_program_studi',
            'program_studi.id_program_studi',
            'jenjang_pendidikan.kode_jenjang_pendidikan',
            'kurikulum.nama_kurikulum'
        ])
            ->orderBy('kurikulum_mata_kuliah.semester')
            ->orderBy('mata_kuliah.kode_mata_kuliah')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'kode_mata_kuliah' => $item->kode_mata_kuliah,
                    'nama_mata_kuliah' => $item->nama_mata_kuliah,
                    'sks_mata_kuliah' => $item->sks_mata_kuliah,
                    'semester_mk' => $item->semester_mk,
                    'kategori_mata_kuliah' => $item->kategori_mata_kuliah, // ✅ Fix: Kategori dari kurikulum_mata_kuliah
                    'jenis_mata_kuliah' => $item->jenis_mata_kuliah,       // ✅ Fix: Jenis dari mata_kuliah
                    'program_studi' => $item->nama_program_studi,
                    'jenjang' => $item->kode_jenjang_pendidikan,
                    'kurikulum' => $item->nama_kurikulum,
                    'nama_lengkap' => $item->kode_mata_kuliah . ' - ' . $item->nama_mata_kuliah .
                        ' (Sem. ' . $item->semester_mk . ', ' . $item->sks_mata_kuliah . ' SKS, ' .
                        $item->jenis_mata_kuliah . ', ' . $item->kategori_mata_kuliah . ')'
                ];
            });

        return response()->json($kurikulumMataKuliahs);
    }

    /**
     * Get dosen untuk search AJAX
     */
    public function getDosen(Request $request)
    {
        $query = Dosen::with('pengguna', 'programStudi.jenjang');

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nidn', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('pengguna', function ($pq) use ($searchTerm) {
                        $pq->where('nama', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('programStudi', function ($psq) use ($searchTerm) {
                        $psq->where('nama_program_studi', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        $dosens = $query->get()
            ->sortBy('pengguna.nama')
            ->map(function ($item) {
                return [
                    'id_dosen' => $item->id_dosen,
                    'nidn' => $item->nidn,
                    'nama' => $item->pengguna->nama,
                    'program_studi' => $item->programStudi->nama_program_studi ?? '',
                    'jenjang' => $item->programStudi->jenjang->kode_jenjang_pendidikan ?? '',
                    'status_dosen' => $item->status_dosen,
                    'nama_lengkap' => $item->pengguna->nama . ($item->nidn ? ' (' . $item->nidn . ')' : '') . ($item->programStudi ? ' - ' . $item->programStudi->nama_program_studi : '')
                ];
            })
            ->values();

        return response()->json($dosens);
    }

    public function getKurikulumMataKuliahDetail($id)
    {
        $kurikulumMataKuliah = KurikulumMataKuliah::with([
            'kurikulum.programStudi.jenjang',
            'mataKuliah'
        ])->findOrFail($id);

        return response()->json([
            'id' => $kurikulumMataKuliah->id,
            'id_program_studi' => $kurikulumMataKuliah->kurikulum->id_program_studi,
            'id_kurikulum' => $kurikulumMataKuliah->id_kurikulum,
            // Format yang SAMA dengan getProgramStudi()
            'program_studi_lengkap' => ($kurikulumMataKuliah->kurikulum->programStudi->kode_program_studi ?
                $kurikulumMataKuliah->kurikulum->programStudi->kode_program_studi . ' - ' : '') .
                $kurikulumMataKuliah->kurikulum->programStudi->jenjang->kode_jenjang_pendidikan . ' ' .
                $kurikulumMataKuliah->kurikulum->programStudi->nama_program_studi,
            // Format yang SAMA dengan getKurikulum()
            'kurikulum_lengkap' => $kurikulumMataKuliah->kurikulum->nama_kurikulum . ' (' .
                $kurikulumMataKuliah->kurikulum->semester->nama_semester . ')',
            // Format yang SAMA dengan getKurikulumMataKuliah()
            'mata_kuliah_lengkap' => $kurikulumMataKuliah->mataKuliah->kode_mata_kuliah . ' - ' .
                $kurikulumMataKuliah->mataKuliah->nama_mata_kuliah . ' (Sem. ' .
                $kurikulumMataKuliah->semester . ', ' .
                $kurikulumMataKuliah->mataKuliah->sks_mata_kuliah . ' SKS, ' .
                $kurikulumMataKuliah->jenis_mata_kuliah . ')'
        ]);
    }

    public function getDosenDetail($id)
    {
        $dosen = Dosen::with('pengguna', 'programStudi')->findOrFail($id);

        return response()->json([
            'id_dosen' => $dosen->id_dosen,
            // Format yang SAMA dengan getDosen()
            'nama_lengkap' => $dosen->pengguna->nama . ($dosen->nidn ? ' (' . $dosen->nidn . ')' : '') .
                ($dosen->programStudi ? ' - ' . $dosen->programStudi->nama_program_studi : '')
        ]);
    }

    /**
     * Get kelas kuliah untuk AJAX/API
     */
    public function getKelasKuliah(Request $request)
    {
        $query = KelasKuliah::with([
            'kurikulumMataKuliah.mataKuliah',
            'kurikulumMataKuliah.kurikulum.programStudi',
            'dosen.pengguna',
            'semester'
        ]);

        if ($request->has('semester') && $request->semester != '') {
            $query->where('id_semester', $request->semester);
        }

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_kelas_kuliah', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('kurikulumMataKuliah.mataKuliah', function ($mq) use ($searchTerm) {
                        $mq->where('kode_mata_kuliah', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('nama_mata_kuliah', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        $kelasKuliahs = $query->orderBy('nama_kelas_kuliah')
            ->get()
            ->map(function ($item) {
                return [
                    'id_kelas_kuliah' => $item->id_kelas_kuliah,
                    'nama_kelas_kuliah' => $item->nama_kelas_kuliah,
                    'nama_ruangan' => $item->nama_ruangan,
                    'kapasitas' => $item->kapasitas,
                    'mata_kuliah' => $item->kurikulumMataKuliah->mataKuliah->kode_mata_kuliah . ' - ' . $item->kurikulumMataKuliah->mataKuliah->nama_mata_kuliah,
                    'dosen' => $item->dosen->pengguna->nama,
                    'semester' => $item->semester->nama_semester,
                    'program_studi' => $item->kurikulumMataKuliah->kurikulum->programStudi->nama_program_studi,
                    'nama_lengkap' => $item->nama_kelas_kuliah . ' - ' . $item->kurikulumMataKuliah->mataKuliah->kode_mata_kuliah . ' (' . $item->semester->nama_semester . ')',
                ];
            });

        return response()->json($kelasKuliahs);
    }

    /**
     * Statistik kelas kuliah
     */
    public function statistik(string $semesterId = null)
    {
        $query = KelasKuliah::query();

        if ($semesterId) {
            $query->where('id_semester', $semesterId);
        }

        $stats = [
            'total_kelas' => $query->count(),
            'rata_kapasitas' => $query->avg('kapasitas'),
            'total_kapasitas' => $query->sum('kapasitas'),
            'kelas_per_program_studi' => $query->with('kurikulumMataKuliah.kurikulum.programStudi')
                ->get()
                ->groupBy('kurikulumMataKuliah.kurikulum.programStudi.nama_program_studi')
                ->map(function ($kelasKuliahs, $programStudi) {
                    return [
                        'program_studi' => $programStudi,
                        'jumlah' => $kelasKuliahs->count()
                    ];
                })
                ->values(),
            'kelas_per_dosen' => $query->with('dosen.pengguna')
                ->get()
                ->groupBy('dosen.pengguna.nama')
                ->map(function ($kelasKuliahs, $dosenNama) {
                    return [
                        'dosen' => $dosenNama,
                        'jumlah' => $kelasKuliahs->count()
                    ];
                })
                ->values(),
        ];

        return response()->json($stats);
    }
}
