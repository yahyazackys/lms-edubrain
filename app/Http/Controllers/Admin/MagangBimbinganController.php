<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaBimbingan;
use App\Models\MagangDetail;
use App\Models\Dosen;
use App\Models\Semester;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class MagangBimbinganController extends Controller
{
    /**
     * Tampilkan halaman assign pembimbing magang dengan filter semester
     */
    public function index(Request $request): View
    {
        // Get semester yang dipilih
        $selectedSemesterId = $request->get('semester');
        $selectedSemester = null;
        $pesertaMagangs = collect();

        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);

            if ($selectedSemester) {
                // Get mata kuliah MAGANG
                $mataKuliahMagang = MataKuliah::where('jenis_mata_kuliah', 'MAGANG')->first();

                if ($mataKuliahMagang) {
                    $query = PesertaBimbingan::with([
                        'registrasiMahasiswa.mahasiswa.pengguna',
                        'registrasiMahasiswa.mahasiswa.programStudi',
                        'dosenPembimbing.pengguna',
                        'dosenPembimbing.programStudi',
                        'mataKuliah',
                        'magangDetail'
                    ])
                        ->whereHas('registrasiMahasiswa', function ($q) use ($selectedSemesterId) {
                            $q->where('id_semester', $selectedSemesterId);
                        })
                        ->where('id_mata_kuliah', $mataKuliahMagang->id_mata_kuliah)
                        ->where('status_mata_kuliah', 'APPROVED');

                    // Filter berdasarkan status pembimbing
                    if ($request->has('status') && $request->status != '') {
                        if ($request->status === 'assigned') {
                            $query->whereNotNull('id_dosen_pembimbing');
                        } elseif ($request->status === 'unassigned') {
                            $query->whereNull('id_dosen_pembimbing');
                        }
                    }

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
                                ->orWhereHas('dosenPembimbing.pengguna', function ($dq) use ($searchTerm) {
                                    $dq->where('nama', 'LIKE', "%{$searchTerm}%");
                                })
                                ->orWhereHas('magangDetail', function ($mdq) use ($searchTerm) {
                                    $mdq->where('tempat_magang', 'LIKE', "%{$searchTerm}%");
                                });
                        });
                    }

                    $pesertaMagangs = $query->orderBy('created_at')->get();
                }
            }
        }

        // Data untuk dropdown
        $semesters = Semester::orderByDesc('tanggal_mulai')->get();

        return view('admin.bimbingan.magang.index', compact(
            'pesertaMagangs',
            'semesters',
            'selectedSemester',
            'selectedSemesterId'
        ));
    }

    /**
     * Assign pembimbing untuk mahasiswa magang
     */
    public function assignPembimbing(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_peserta_bimbingan' => 'required|exists:peserta_bimbingan,id_peserta_bimbingan',
            'id_dosen_pembimbing' => 'required|exists:dosen,id_dosen',
            'tempat_magang' => 'nullable|string|max:200',
            'alamat_magang' => 'nullable|string',
            'bidang_magang' => 'nullable|string|max:100',
        ], [
            'id_peserta_bimbingan.required' => 'Mahasiswa wajib dipilih.',
            'id_peserta_bimbingan.exists' => 'Mahasiswa tidak valid.',
            'id_dosen_pembimbing.required' => 'Dosen pembimbing wajib dipilih.',
            'id_dosen_pembimbing.exists' => 'Dosen pembimbing tidak valid.',
            'tempat_magang.max' => 'Tempat magang maksimal 200 karakter.',
            'bidang_magang.max' => 'Bidang magang maksimal 100 karakter.',
        ]);

        // Cek apakah mahasiswa sudah punya pembimbing
        $pesertaBimbingan = PesertaBimbingan::find($validated['id_peserta_bimbingan']);
        if ($pesertaBimbingan->id_dosen_pembimbing) {
            return back()->with('error', 'Mahasiswa sudah memiliki pembimbing. Gunakan fitur update untuk mengubah pembimbing.');
        }

        DB::transaction(function () use ($validated, $pesertaBimbingan) {
            // Update pembimbing
            $pesertaBimbingan->update([
                'id_dosen_pembimbing' => $validated['id_dosen_pembimbing']
            ]);

            // Create atau update detail magang jika ada data
            if ($validated['tempat_magang'] || $validated['alamat_magang'] || $validated['bidang_magang']) {
                MagangDetail::updateOrCreate(
                    ['id_peserta_bimbingan' => $validated['id_peserta_bimbingan']],
                    [
                        'id_magang_detail' => \Illuminate\Support\Str::uuid(),
                        'tempat_magang' => $validated['tempat_magang'],
                        'alamat_magang' => $validated['alamat_magang'],
                        'bidang_magang' => $validated['bidang_magang'],
                    ]
                );
            }
        });

        return redirect()->route('bimbingan.magang.index', ['semester' => $request->semester])
            ->with('success', 'Pembimbing magang berhasil di-assign.');
    }

    /**
     * Update pembimbing magang
     */
    public function updatePembimbing(Request $request, string $id): RedirectResponse
    {
        $pesertaBimbingan = PesertaBimbingan::findOrFail($id);

        $validated = $request->validate([
            'id_dosen_pembimbing' => 'required|exists:dosen,id_dosen',
            'tempat_magang' => 'nullable|string|max:200',
            'alamat_magang' => 'nullable|string',
            'bidang_magang' => 'nullable|string|max:100',
        ], [
            'id_dosen_pembimbing.required' => 'Dosen pembimbing wajib dipilih.',
            'id_dosen_pembimbing.exists' => 'Dosen pembimbing tidak valid.',
            'tempat_magang.max' => 'Tempat magang maksimal 200 karakter.',
            'bidang_magang.max' => 'Bidang magang maksimal 100 karakter.',
        ]);

        DB::transaction(function () use ($validated, $pesertaBimbingan) {
            // Update pembimbing
            $pesertaBimbingan->update([
                'id_dosen_pembimbing' => $validated['id_dosen_pembimbing']
            ]);

            // Update detail magang
            if ($validated['tempat_magang'] || $validated['alamat_magang'] || $validated['bidang_magang']) {
                MagangDetail::updateOrCreate(
                    ['id_peserta_bimbingan' => $pesertaBimbingan->id_peserta_bimbingan],
                    [
                        'id_magang_detail' => \Illuminate\Support\Str::uuid(),
                        'tempat_magang' => $validated['tempat_magang'],
                        'alamat_magang' => $validated['alamat_magang'],
                        'bidang_magang' => $validated['bidang_magang'],
                    ]
                );
            }
        });

        return redirect()->route('bimbingan.magang.index', ['semester' => $request->semester])
            ->with('success', 'Pembimbing magang berhasil diperbarui.');
    }

    /**
     * Get dosen untuk search AJAX
     */
    public function getDosen(Request $request)
    {
        $query = Dosen::with('pengguna', 'programStudi');

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
                    'nama_lengkap' => $item->pengguna->nama . ($item->nidn ? ' (' . $item->nidn . ')' : '') . ($item->programStudi ? ' - ' . $item->programStudi->nama_program_studi : '')
                ];
            })
            ->values();

        return response()->json($dosens);
    }

    /**
     * Get mahasiswa magang untuk search AJAX
     */
    public function getMahasiswaMagang(Request $request)
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

        // Filter hanya mata kuliah MAGANG
        $query->whereHas('mataKuliah', function ($q) {
            $q->where('jenis_mata_kuliah', 'MAGANG');
        });

        // Filter hanya yang APPROVED
        $query->where('status_mata_kuliah', 'APPROVED');

        // Filter berdasarkan status pembimbing
        if ($request->has('status') && $request->status != '') {
            if ($request->status === 'unassigned') {
                $query->whereNull('id_dosen_pembimbing');
            } elseif ($request->status === 'assigned') {
                $query->whereNotNull('id_dosen_pembimbing');
            }
        }

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
                    'has_pembimbing' => $item->id_dosen_pembimbing ? true : false,
                    'nama_lengkap' => $mahasiswa->nim . ' - ' . $mahasiswa->pengguna->nama . ' (' . ($mahasiswa->programStudi->nama_program_studi ?? 'N/A') . ')',
                ];
            });

        return response()->json($mahasiswas);
    }
}
