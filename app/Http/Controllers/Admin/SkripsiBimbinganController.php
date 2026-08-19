<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PesertaBimbingan;
use App\Models\SkripsiDetail;
use App\Models\Dosen;
use App\Models\Semester;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SkripsiBimbinganController extends Controller
{
    /**
     * Tampilkan halaman assign pembimbing skripsi dengan filter semester
     */
    public function index(Request $request): View
    {
        // Get semester yang dipilih
        $selectedSemesterId = $request->get('semester');
        $selectedSemester = null;
        $pesertaSkripsis = collect();

        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);

            if ($selectedSemester) {
                // Get mata kuliah SKRIPSI
                $mataKuliahSkripsi = MataKuliah::where('jenis_mata_kuliah', 'SKRIPSI')->first();

                if ($mataKuliahSkripsi) {
                    $query = PesertaBimbingan::with([
                        'registrasiMahasiswa.mahasiswa.pengguna',
                        'registrasiMahasiswa.mahasiswa.programStudi',
                        'dosenPembimbing.pengguna',
                        'dosenPembimbing.programStudi',
                        'dosenPembimbing2.pengguna',
                        'dosenPembimbing2.programStudi',
                        'mataKuliah',
                        'skripsiDetail'
                    ])
                        ->whereHas('registrasiMahasiswa', function ($q) use ($selectedSemesterId) {
                            $q->where('id_semester', $selectedSemesterId);
                        })
                        ->where('id_mata_kuliah', $mataKuliahSkripsi->id_mata_kuliah)
                        ->where('status_mata_kuliah', 'APPROVED');

                    // Filter berdasarkan status pembimbing
                    if ($request->has('status') && $request->status != '') {
                        if ($request->status === 'complete') {
                            $query->whereNotNull('id_dosen_pembimbing')
                                ->whereNotNull('id_dosen_pembimbing_2');
                        } elseif ($request->status === 'partial') {
                            $query->where(function ($q) {
                                $q->whereNotNull('id_dosen_pembimbing')
                                    ->whereNull('id_dosen_pembimbing_2');
                            });
                        } elseif ($request->status === 'unassigned') {
                            $query->whereNull('id_dosen_pembimbing')
                                ->whereNull('id_dosen_pembimbing_2');
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
                                ->orWhereHas('dosenPembimbing2.pengguna', function ($dq) use ($searchTerm) {
                                    $dq->where('nama', 'LIKE', "%{$searchTerm}%");
                                })
                                ->orWhereHas('skripsiDetail', function ($sdq) use ($searchTerm) {
                                    $sdq->where('judul', 'LIKE', "%{$searchTerm}%")
                                        ->orWhere('bidang_penelitian', 'LIKE', "%{$searchTerm}%");
                                });
                        });
                    }

                    $pesertaSkripsis = $query->orderBy('created_at')->get();
                }
            }
        }

        // Data untuk dropdown
        $semesters = Semester::orderByDesc('tanggal_mulai')->get();

        return view('admin.bimbingan.skripsi.index', compact(
            'pesertaSkripsis',
            'semesters',
            'selectedSemester',
            'selectedSemesterId'
        ));
    }

    /**
     * Assign pembimbing untuk mahasiswa skripsi
     */
    public function assignPembimbing(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_peserta_bimbingan' => 'required|exists:peserta_bimbingan,id_peserta_bimbingan',
            'id_dosen_pembimbing' => 'required|exists:dosen,id_dosen',
            'id_dosen_pembimbing_2' => 'nullable|exists:dosen,id_dosen|different:id_dosen_pembimbing',
            'judul' => 'nullable|string|max:500',
            'bidang_penelitian' => 'nullable|string|max:100',
        ], [
            'id_peserta_bimbingan.required' => 'Mahasiswa wajib dipilih.',
            'id_peserta_bimbingan.exists' => 'Mahasiswa tidak valid.',
            'id_dosen_pembimbing.required' => 'Pembimbing 1 wajib dipilih.',
            'id_dosen_pembimbing.exists' => 'Pembimbing 1 tidak valid.',
            'id_dosen_pembimbing_2.exists' => 'Pembimbing 2 tidak valid.',
            'id_dosen_pembimbing_2.different' => 'Pembimbing 2 harus berbeda dengan Pembimbing 1.',
            'judul.max' => 'Judul skripsi maksimal 500 karakter.',
            'bidang_penelitian.max' => 'Bidang penelitian maksimal 100 karakter.',
        ]);

        // Cek apakah mahasiswa sudah punya pembimbing
        $pesertaBimbingan = PesertaBimbingan::find($validated['id_peserta_bimbingan']);
        if ($pesertaBimbingan->id_dosen_pembimbing) {
            return back()->with('error', 'Mahasiswa sudah memiliki pembimbing. Gunakan fitur update untuk mengubah pembimbing.');
        }

        DB::transaction(function () use ($validated, $pesertaBimbingan) {
            // Update pembimbing
            $pesertaBimbingan->update([
                'id_dosen_pembimbing' => $validated['id_dosen_pembimbing'],
                'id_dosen_pembimbing_2' => $validated['id_dosen_pembimbing_2'] ?? null,
            ]);

            // Create atau update detail skripsi jika ada data
            if ($validated['judul'] || $validated['bidang_penelitian']) {
                SkripsiDetail::updateOrCreate(
                    ['id_peserta_bimbingan' => $validated['id_peserta_bimbingan']],
                    [
                        'id_skripsi_detail' => Str::uuid(),
                        'judul' => $validated['judul'],
                        'bidang_penelitian' => $validated['bidang_penelitian'],
                        'status_proposal' => 'DRAFT',
                    ]
                );
            }
        });

        return redirect()->route('bimbingan.skripsi.index', ['semester' => $request->semester])
            ->with('success', 'Pembimbing skripsi berhasil di-assign.');
    }

    /**
     * Update pembimbing skripsi
     */
    public function updatePembimbing(Request $request, string $id): RedirectResponse
    {
        $pesertaBimbingan = PesertaBimbingan::findOrFail($id);

        $validated = $request->validate([
            'id_dosen_pembimbing' => 'required|exists:dosen,id_dosen',
            'id_dosen_pembimbing_2' => 'nullable|exists:dosen,id_dosen|different:id_dosen_pembimbing',
            'judul' => 'nullable|string|max:500',
            'bidang_penelitian' => 'nullable|string|max:100',
        ], [
            'id_dosen_pembimbing.required' => 'Pembimbing 1 wajib dipilih.',
            'id_dosen_pembimbing.exists' => 'Pembimbing 1 tidak valid.',
            'id_dosen_pembimbing_2.exists' => 'Pembimbing 2 tidak valid.',
            'id_dosen_pembimbing_2.different' => 'Pembimbing 2 harus berbeda dengan Pembimbing 1.',
            'judul.max' => 'Judul skripsi maksimal 500 karakter.',
            'bidang_penelitian.max' => 'Bidang penelitian maksimal 100 karakter.',
        ]);

        DB::transaction(function () use ($validated, $pesertaBimbingan) {
            // Update pembimbing
            $pesertaBimbingan->update([
                'id_dosen_pembimbing' => $validated['id_dosen_pembimbing'],
                'id_dosen_pembimbing_2' => $validated['id_dosen_pembimbing_2'] ?? null,
            ]);

            // Update detail skripsi
            if ($validated['judul'] || $validated['bidang_penelitian']) {
                SkripsiDetail::updateOrCreate(
                    ['id_peserta_bimbingan' => $pesertaBimbingan->id_peserta_bimbingan],
                    [
                        'id_skripsi_detail' => Str::uuid(),
                        'judul' => $validated['judul'],
                        'bidang_penelitian' => $validated['bidang_penelitian'],
                        'status_proposal' => $pesertaBimbingan->skripsiDetail->status_proposal ?? 'DRAFT',
                    ]
                );
            }
        });

        return redirect()->route('bimbingan.skripsi.index', ['semester' => $request->semester])
            ->with('success', 'Pembimbing skripsi berhasil diperbarui.');
    }

    /**
     * Set jadwal seminar dan sidang
     */
    public function setJadwal(Request $request, string $id): RedirectResponse
    {
        $pesertaBimbingan = PesertaBimbingan::with('skripsiDetail')->findOrFail($id);

        if (!$pesertaBimbingan->skripsiDetail) {
            return back()->with('error', 'Detail skripsi belum ada. Tambahkan detail skripsi terlebih dahulu.');
        }

        $validated = $request->validate([
            'tanggal_seminar_proposal' => 'nullable|date|after_or_equal:today',
            'tanggal_sidang_skripsi' => 'nullable|date|after_or_equal:today',
        ], [
            'tanggal_seminar_proposal.date' => 'Format tanggal seminar proposal tidak valid.',
            'tanggal_seminar_proposal.after_or_equal' => 'Tanggal seminar proposal tidak boleh kurang dari hari ini.',
            'tanggal_sidang_skripsi.date' => 'Format tanggal sidang skripsi tidak valid.',
            'tanggal_sidang_skripsi.after_or_equal' => 'Tanggal sidang skripsi tidak boleh kurang dari hari ini.',
        ]);

        // Validasi: seminar proposal harus sebelum sidang skripsi
        if ($validated['tanggal_seminar_proposal'] && $validated['tanggal_sidang_skripsi']) {
            if ($validated['tanggal_seminar_proposal'] >= $validated['tanggal_sidang_skripsi']) {
                return back()->with('error', 'Tanggal seminar proposal harus sebelum tanggal sidang skripsi.');
            }
        }

        $pesertaBimbingan->skripsiDetail->update([
            'tanggal_seminar_proposal' => $validated['tanggal_seminar_proposal'],
            'tanggal_sidang_skripsi' => $validated['tanggal_sidang_skripsi'],
        ]);

        return redirect()->route('bimbingan.skripsi.index', ['semester' => $request->semester])
            ->with('success', 'Jadwal seminar dan sidang berhasil diatur.');
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
     * Get mahasiswa skripsi untuk search AJAX
     */
    public function getMahasiswaSkripsi(Request $request)
    {
        $query = PesertaBimbingan::with([
            'registrasiMahasiswa.mahasiswa.pengguna',
            'registrasiMahasiswa.mahasiswa.programStudi',
            'mataKuliah',
            'skripsiDetail'
        ]);

        // Filter berdasarkan semester
        if ($request->has('semester') && $request->semester != '') {
            $query->whereHas('registrasiMahasiswa', function ($q) use ($request) {
                $q->where('id_semester', $request->semester);
            });
        }

        // Filter hanya mata kuliah SKRIPSI
        $query->whereHas('mataKuliah', function ($q) {
            $q->where('jenis_mata_kuliah', 'SKRIPSI');
        });

        // Filter hanya yang APPROVED
        $query->where('status_mata_kuliah', 'APPROVED');

        // Filter berdasarkan status pembimbing
        if ($request->has('status') && $request->status != '') {
            if ($request->status === 'unassigned') {
                $query->whereNull('id_dosen_pembimbing')
                    ->whereNull('id_dosen_pembimbing_2');
            } elseif ($request->status === 'partial') {
                $query->where(function ($q) {
                    $q->whereNotNull('id_dosen_pembimbing')
                        ->whereNull('id_dosen_pembimbing_2');
                });
            } elseif ($request->status === 'complete') {
                $query->whereNotNull('id_dosen_pembimbing')
                    ->whereNotNull('id_dosen_pembimbing_2');
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
                $hasPembimbing1 = $item->id_dosen_pembimbing ? true : false;
                $hasPembimbing2 = $item->id_dosen_pembimbing_2 ? true : false;

                $status = 'unassigned';
                if ($hasPembimbing1 && $hasPembimbing2) {
                    $status = 'complete';
                } elseif ($hasPembimbing1) {
                    $status = 'partial';
                }

                return [
                    'id_peserta_bimbingan' => $item->id_peserta_bimbingan,
                    'nim' => $mahasiswa->nim,
                    'nama' => $mahasiswa->pengguna->nama,
                    'program_studi' => $mahasiswa->programStudi->nama_program_studi ?? 'N/A',
                    'status_pembimbing' => $status,
                    'judul_skripsi' => $item->skripsiDetail->judul ?? null,
                    'nama_lengkap' => $mahasiswa->nim . ' - ' . $mahasiswa->pengguna->nama . ' (' . ($mahasiswa->programStudi->nama_program_studi ?? 'N/A') . ')',
                ];
            });

        return response()->json($mahasiswas);
    }
}
