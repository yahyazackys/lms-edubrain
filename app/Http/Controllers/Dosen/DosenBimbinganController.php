<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\PesertaBimbingan;
use App\Models\LaporanBab;
use App\Models\BimbinganFile;
use App\Models\Semester;
use App\Models\Dosen;
use App\Models\KknKelompok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\NilaiPerkuliahan;

class DosenBimbinganController extends Controller
{
    /**
     * Display list mahasiswa bimbingan dengan tab KKN, Magang, Skripsi
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            abort(403, 'Akses ditolak. Anda bukan dosen.');
        }

        // Get active semester atau semester yang dipilih
        $selectedSemesterId = $request->get('semester');
        $semesters = Semester::orderBy('is_active', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $selectedSemester = null;
        if ($selectedSemesterId) {
            $selectedSemester = $semesters->where('id_semester', $selectedSemesterId)->first();
        } else {
            $selectedSemester = $semesters->where('is_active', true)->first()
                ?? $semesters->first();
        }

        // Get tab yang aktif (default: kkn)
        $activeTab = $request->get('tab', 'kkn');

        $data = [
            'semesters' => $semesters,
            'selectedSemester' => $selectedSemester,
            'selectedSemesterId' => $selectedSemester?->id_semester,
            'activeTab' => $activeTab,
            'dosen' => $dosen
        ];

        if ($selectedSemester) {
            // Base query untuk mahasiswa bimbingan dosen ini
            $baseQuery = PesertaBimbingan::with([
                'registrasiMahasiswa.mahasiswa.pengguna',
                'registrasiMahasiswa.semester',
                'mataKuliah',
                'kknDetail.kelompokKkn',
                'magangDetail',
                'skripsiDetail',
                'laporanBabs'
            ])
                ->whereHas('registrasiMahasiswa', function ($q) use ($selectedSemester) {
                    $q->where('id_semester', $selectedSemester->id_semester);
                })
                ->where(function ($q) use ($dosen) {
                    $q->where('id_dosen_pembimbing', $dosen->id_dosen)
                        ->orWhere('id_dosen_pembimbing_2', $dosen->id_dosen);
                })
                ->where('status_mata_kuliah', 'APPROVED');

            // Data untuk KKN - Group by kelompok
            $kknPeserta = (clone $baseQuery)
                ->whereHas('mataKuliah', function ($q) {
                    $q->where('jenis_mata_kuliah', 'KKN');
                })
                ->get();

            $data['kkn_kelompok'] = $kknPeserta->groupBy(function ($peserta) {
                return $peserta->kknDetail->id_kelompok_kkn ?? 'no_kelompok';
            })->map(function ($pesertaGroup, $kelompokId) {
                if ($kelompokId === 'no_kelompok') {
                    return null; // Skip peserta yang belum ada kelompok
                }

                $kelompok = $pesertaGroup->first()->kknDetail->kelompokKkn;
                $totalPeserta = $pesertaGroup->count();
                $totalBabSelesai = $pesertaGroup->sum(function ($peserta) {
                    return $peserta->laporanBabs->where('status', 'APPROVED')->count();
                });
                $totalBab = $pesertaGroup->sum(function ($peserta) {
                    return $peserta->laporanBabs->count();
                });
                $pendingReview = $pesertaGroup->sum(function ($peserta) {
                    return $peserta->laporanBabs->where('status', 'SUBMITTED')->count();
                });

                return [
                    'kelompok' => $kelompok,
                    'peserta_count' => $totalPeserta,
                    'progress_percentage' => $totalBab > 0 ? round(($totalBabSelesai / $totalBab) * 100) : 0,
                    'pending_review' => $pendingReview,
                    'last_activity' => $pesertaGroup->flatMap->laporanBabs->max('updated_at')
                ];
            })->filter()->values();

            // Data untuk Magang dan Skripsi - Individual
            $data['magang'] = (clone $baseQuery)
                ->whereHas('mataKuliah', function ($q) {
                    $q->where('jenis_mata_kuliah', 'MAGANG');
                })
                ->get()
                ->map(function ($peserta) {
                    return $this->enrichPesertaData($peserta, 'MAGANG');
                });

            $data['skripsi'] = (clone $baseQuery)
                ->whereHas('mataKuliah', function ($q) {
                    $q->where('jenis_mata_kuliah', 'SKRIPSI');
                })
                ->get()
                ->map(function ($peserta) {
                    return $this->enrichPesertaData($peserta, 'SKRIPSI');
                });

            // Summary stats
            $data['stats'] = [
                'total_kkn_kelompok' => $data['kkn_kelompok']->count(),
                'total_kkn_peserta' => $kknPeserta->count(),
                'total_magang' => $data['magang']->count(),
                'total_skripsi' => $data['skripsi']->count(),
                'pending_review' => LaporanBab::whereHas('pesertaBimbingan', function ($q) use ($dosen, $selectedSemester) {
                    $q->whereHas('registrasiMahasiswa', function ($q2) use ($selectedSemester) {
                        $q2->where('id_semester', $selectedSemester->id_semester);
                    })
                        ->where(function ($q2) use ($dosen) {
                            $q2->where('id_dosen_pembimbing', $dosen->id_dosen)
                                ->orWhere('id_dosen_pembimbing_2', $dosen->id_dosen);
                        });
                })->needsReview()->count()
            ];
        }

        return view('dosen.bimbingan.index', $data);
    }

    public function detailKelompok($id_kelompok_kkn)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        $kelompok = KknKelompok::with([
            'dpl.pengguna',
            'kknDetails.pesertaBimbingan.registrasiMahasiswa.mahasiswa.pengguna',
            'kknDetails.pesertaBimbingan.laporanBabs',
            'kknDokumentasis.uploader.pengguna'
        ])->findOrFail($id_kelompok_kkn);

        // Check authorization - dosen harus jadi DPL
        if ($kelompok->id_dpl !== $dosen->id_dosen) {
            abort(403, 'Anda tidak memiliki akses ke kelompok KKN ini.');
        }

        // Enrich peserta data
        $pesertaList = $kelompok->kknDetails->map(function ($detail) {
            $peserta = $detail->pesertaBimbingan;
            $mahasiswa = $peserta->registrasiMahasiswa->mahasiswa;

            // Progress laporan
            $totalBab = $peserta->laporanBabs->count();
            $babApproved = $peserta->laporanBabs->where('status', 'APPROVED')->count();
            $babPending = $peserta->laporanBabs->where('status', 'SUBMITTED')->count();

            $data = [
                'id_peserta_bimbingan' => $peserta->id_peserta_bimbingan,
                'peran' => $detail->peran_kelompok,
                'mahasiswa' => [
                    'nama' => $mahasiswa->pengguna->nama,
                    'nim' => $mahasiswa->nim,
                    'foto' => $mahasiswa->foto,
                    'email' => $mahasiswa->pengguna->email,
                    'no_hp' => $mahasiswa->pengguna->no_hp,
                    'angkatan' => $mahasiswa->angkatan
                ],
                'progress' => [
                    'total_bab' => $totalBab,
                    'bab_approved' => $babApproved,
                    'bab_pending' => $babPending,
                    'progress_percentage' => $totalBab > 0 ? round(($babApproved / $totalBab) * 100) : 0
                ],
                'last_activity' => $peserta->laporanBabs->max('updated_at')
            ];

            // TAMBAHAN: Check nilai akhir
            $nilai = NilaiPerkuliahan::where('jenis_peserta', 'BIMBINGAN')
                ->where('id_peserta_bimbingan', $peserta->id_peserta_bimbingan)
                ->first();

            if ($nilai) {
                $data['nilai'] = [
                    'angka' => $nilai->nilai_angka,
                    'huruf' => $nilai->nilai_huruf,
                    'indeks' => $nilai->nilai_indeks
                ];
            }

            return $data;
        });

        // Group dokumentasi by type
        $dokumentasi = [
            'images' => $kelompok->kknDokumentasis->where('file_type', 'IMAGE'),
            'documents' => $kelompok->kknDokumentasis->where('file_type', 'DOCUMENT')
        ];

        return view('dosen.bimbingan.detail-kelompok', [
            'kelompok' => $kelompok,
            'peserta_list' => $pesertaList,
            'dokumentasi' => $dokumentasi,
            'dosen' => $dosen
        ]);
    }

    /**
     * Enrich peserta data dengan informasi tambahan
     */
    private function enrichPesertaData($peserta, $jenis)
    {
        $mahasiswa = $peserta->registrasiMahasiswa->mahasiswa;

        $data = [
            'id_peserta_bimbingan' => $peserta->id_peserta_bimbingan,
            'mahasiswa' => [
                'nama' => $mahasiswa->pengguna->nama,
                'nim' => $mahasiswa->nim,
                'foto' => $mahasiswa->foto,
                'program_studi' => $mahasiswa->programStudi->nama_program_studi ?? 'N/A'
            ],
            'mata_kuliah' => $peserta->mataKuliah->nama_mata_kuliah,
            'status' => $peserta->status_mata_kuliah,
            'jenis' => $jenis
        ];

        // Progress laporan
        $totalBab = $peserta->laporanBabs->count();
        $babApproved = $peserta->laporanBabs->where('status', 'APPROVED')->count();
        $babPendingReview = $peserta->laporanBabs->where('status', 'SUBMITTED')->count();

        $data['progress'] = [
            'total_bab' => $totalBab,
            'bab_approved' => $babApproved,
            'bab_pending' => $babPendingReview,
            'progress_percentage' => $totalBab > 0 ? round(($babApproved / $totalBab) * 100) : 0
        ];

        // Data spesifik berdasarkan jenis
        switch ($jenis) {
            case 'KKN':
                if ($peserta->kknDetail) {
                    $data['detail'] = [
                        'kelompok' => $peserta->kknDetail->kelompokKkn->nama_kelompok ?? 'Belum ditentukan',
                        'lokasi' => $peserta->kknDetail->kelompokKkn->lokasi ?? 'Belum ditentukan',
                        'peran' => $peserta->kknDetail->peran_kelompok
                    ];
                }
                break;

            case 'MAGANG':
                if ($peserta->magangDetail) {
                    $data['detail'] = [
                        'tempat' => $peserta->magangDetail->tempat_magang ?? 'Belum ditentukan',
                        'bidang' => $peserta->magangDetail->bidang_magang ?? 'Belum ditentukan'
                    ];
                }
                break;

            case 'SKRIPSI':
                if ($peserta->skripsiDetail) {
                    $data['detail'] = [
                        'judul' => $peserta->skripsiDetail->judul ?? 'Belum ditentukan',
                        'bidang_penelitian' => $peserta->skripsiDetail->bidang_penelitian ?? 'Belum ditentukan',
                        'status_proposal' => $peserta->skripsiDetail->status_proposal_formatted,
                        'progress_skripsi' => $peserta->skripsiDetail->progress_percentage
                    ];
                }
                break;
        }

        // Last activity
        $lastActivity = $peserta->laporanBabs()
            ->latest('updated_at')
            ->first();

        $data['last_activity'] = $lastActivity ? $lastActivity->updated_at->diffForHumans() : 'Belum ada aktivitas';

        $nilai = NilaiPerkuliahan::where('jenis_peserta', 'BIMBINGAN')
            ->where('id_peserta_bimbingan', $peserta->id_peserta_bimbingan)
            ->first();

        if ($nilai) {
            $data['nilai'] = json_encode([
                'nilai_angka' => $nilai->nilai_angka,
                'nilai_huruf' => $nilai->nilai_huruf,
                'nilai_indeks' => $nilai->nilai_indeks
            ]);
        }

        return (object) $data;
    }

    /**
     * Detail progress mahasiswa dan management bab
     */
    public function detail($id_peserta_bimbingan)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        $peserta = PesertaBimbingan::with([
            'registrasiMahasiswa.mahasiswa.pengguna',
            'registrasiMahasiswa.mahasiswa.programStudi',
            'mataKuliah',
            'dosenPembimbing.pengguna',
            'dosenPembimbing2.pengguna',
            'kknDetail.kelompokKkn.dpl.pengguna',
            'magangDetail',
            'skripsiDetail',
            'laporanBabs',
            'laporanBabs.bimbinganFiles' => function ($q) {
                $q->latest();
            }
        ])->findOrFail($id_peserta_bimbingan);

        // Check authorization
        if (!in_array($dosen->id_dosen, [$peserta->id_dosen_pembimbing, $peserta->id_dosen_pembimbing_2])) {
            abort(403, 'Anda tidak memiliki akses ke mahasiswa bimbingan ini.');
        }

        $mahasiswa = $peserta->registrasiMahasiswa->mahasiswa;
        $jenisBimbingan = $peserta->mataKuliah->jenis_mata_kuliah;

        // Enrich data
        $data = [
            'peserta' => $peserta,
            'mahasiswa' => $mahasiswa,
            'jenis_bimbingan' => $jenisBimbingan,
            'mata_kuliah' => $peserta->mataKuliah,
            'pembimbing_utama' => $peserta->dosenPembimbing,
            'pembimbing_kedua' => $peserta->dosenPembimbing2,
            'is_pembimbing_utama' => $peserta->id_dosen_pembimbing === $dosen->id_dosen,
            'babs' => $peserta->laporanBabs->map(function ($bab) {
                return [
                    'id_laporan_bab' => $bab->id_laporan_bab,
                    'judul_bab' => $bab->judul_bab,
                    'konten' => $bab->konten, // Added this for edit modal
                    'file_template' => $bab->file_template, // Added this to show template file
                    'status' => $bab->status,
                    'status_formatted' => $bab->status_formatted,
                    'status_badge_class' => $bab->status_badge_class,
                    'submission_count' => $bab->bimbinganFiles->count(),
                    'latest_submission' => $bab->bimbinganFiles->first(),
                    'submitted_at' => $bab->submitted_at,
                    'approved_at' => $bab->approved_at,
                    'catatan_pembimbing' => $bab->catatan_pembimbing,
                    'can_edit' => $bab->can_edit,
                    'can_review' => $bab->can_review,
                    'progress_percentage' => $bab->progress_percentage
                ];
            })
        ];

        // Detail spesifik berdasarkan jenis
        switch ($jenisBimbingan) {
            case 'KKN':
                if ($peserta->kknDetail) {
                    $data['detail_bimbingan'] = [
                        'kelompok' => $peserta->kknDetail->kelompokKkn,
                        'peran' => $peserta->kknDetail->peran_kelompok,
                        'dpl' => $peserta->kknDetail->kelompokKkn->dpl ?? null
                    ];
                }
                break;

            case 'MAGANG':
                $data['detail_bimbingan'] = $peserta->magangDetail;
                break;

            case 'SKRIPSI':
                $data['detail_bimbingan'] = $peserta->skripsiDetail;
                break;
        }

        // Progress summary
        $totalBab = $data['babs']->count();
        $babApproved = $data['babs']->where('status', 'APPROVED')->count();
        $babSubmitted = $data['babs']->where('status', 'SUBMITTED')->count();
        $babNeedsRevision = $data['babs']->where('status', 'NEEDS_REVISION')->count();

        $data['progress_summary'] = [
            'total_bab' => $totalBab,
            'bab_approved' => $babApproved,
            'bab_submitted' => $babSubmitted,
            'bab_needs_revision' => $babNeedsRevision,
            'bab_draft' => $totalBab - $babApproved - $babSubmitted - $babNeedsRevision,
            'overall_progress' => $totalBab > 0 ? round(($babApproved / $totalBab) * 100) : 0
        ];

        return view('dosen.bimbingan.detail', $data);
    }

    /**
     * Tambah bab baru
     */
    public function storeBab(Request $request, $id_peserta_bimbingan)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        $peserta = PesertaBimbingan::findOrFail($id_peserta_bimbingan);

        // Check authorization
        if (!in_array($dosen->id_dosen, [$peserta->id_dosen_pembimbing, $peserta->id_dosen_pembimbing_2])) {
            abort(403);
        }

        $request->validate([
            'judul_bab' => 'required|string|max:200',
            'konten' => 'nullable|string',
            'file_template' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
        ]);

        $data = [
            'id_laporan_bab' => (string) Str::uuid(),
            'id_peserta_bimbingan' => $id_peserta_bimbingan,
            'judul_bab' => $request->judul_bab,
            'konten' => $request->konten,
            'status' => 'DRAFT'
        ];

        // Handle file template upload
        if ($request->hasFile('file_template')) {
            $file = $request->file('file_template');
            $filename = 'template_bab_' . $request->judul_bab . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('templates/bab', $filename, 'public');
            $data['file_template'] = $filePath;
        }

        LaporanBab::create($data);

        return redirect()->back()->with('success', 'Bab berhasil ditambahkan.');
    }

    /**
     * Update bab
     */
    public function updateBab(Request $request, $id_peserta_bimbingan, $id_laporan_bab)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        $peserta = PesertaBimbingan::findOrFail($id_peserta_bimbingan);
        $bab = LaporanBab::where('id_peserta_bimbingan', $id_peserta_bimbingan)
            ->findOrFail($id_laporan_bab);

        // Check authorization
        if (!in_array($dosen->id_dosen, [$peserta->id_dosen_pembimbing, $peserta->id_dosen_pembimbing_2])) {
            abort(403);
        }

        $request->validate([
            'judul_bab' => 'required|string|max:200',
            'konten' => 'nullable|string',
            'file_template' => 'nullable|file|mimes:pdf,doc,docx|max:10240'
        ]);

        $data = [
            'judul_bab' => $request->judul_bab,
            'konten' => $request->konten
        ];

        // Handle file template upload
        if ($request->hasFile('file_template')) {
            // Delete old file if exists
            if ($bab->file_template) {
                Storage::disk('public')->delete($bab->file_template);
            }

            $file = $request->file('file_template');
            $filename = 'template_bab_' . $request->judul_bab . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('templates/bab', $filename, 'public');
            $data['file_template'] = $filePath;
        }

        $bab->update($data);

        return redirect()->back()->with('success', 'Bab berhasil diperbarui.');
    }

    /**
     * Delete bab
     */
    public function deleteBab($id_peserta_bimbingan, $id_laporan_bab)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        $peserta = PesertaBimbingan::findOrFail($id_peserta_bimbingan);
        $bab = LaporanBab::where('id_peserta_bimbingan', $id_peserta_bimbingan)
            ->findOrFail($id_laporan_bab);

        // Check authorization
        if (!in_array($dosen->id_dosen, [$peserta->id_dosen_pembimbing, $peserta->id_dosen_pembimbing_2])) {
            abort(403);
        }

        // Delete file template if exists
        if ($bab->file_template) {
            Storage::disk('public')->delete($bab->file_template);
        }

        // Delete related files
        foreach ($bab->bimbinganFiles as $file) {
            $file->deleteFile();
            $file->delete();
        }

        $bab->delete();

        return redirect()->back()->with('success', 'Bab berhasil dihapus.');
    }

    /**
     * Review dan approve/reject bab
     */
    public function reviewBab(Request $request, $id_peserta_bimbingan, $id_laporan_bab)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        $peserta = PesertaBimbingan::findOrFail($id_peserta_bimbingan);
        $bab = LaporanBab::where('id_peserta_bimbingan', $id_peserta_bimbingan)
            ->findOrFail($id_laporan_bab);

        // Check authorization
        if (!in_array($dosen->id_dosen, [$peserta->id_dosen_pembimbing, $peserta->id_dosen_pembimbing_2])) {
            abort(403);
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan_pembimbing' => 'nullable|string|max:1000'
        ]);

        if ($request->action === 'approve') {
            $bab->approve($request->catatan_pembimbing);
            $message = 'Bab berhasil disetujui.';
        } else {
            $bab->reject($request->catatan_pembimbing ?: 'Perlu revisi.');
            $message = 'Bab memerlukan revisi.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getSubmissions($pesertaId, $babId)
    {
        try {
            $peserta = PesertaBimbingan::findOrFail($pesertaId);

            // Load bab with submissions relationship
            $bab = LaporanBab::with(['bimbinganFiles' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
                ->where('id_laporan_bab', $babId)
                ->where('id_peserta_bimbingan', $pesertaId)
                ->firstOrFail();

            // Map submissions data
            $submissions = $bab->bimbinganFiles->map(function ($submission) {
                return [
                    'id_bimbingan_file' => $submission->id_bimbingan_file,
                    'input_type' => $submission->input_type,
                    'file_path' => $submission->file_path,
                    'konten' => $submission->konten,
                    'status' => $submission->status,
                    'catatan_pembimbing' => $submission->catatan_pembimbing,
                    'created_at' => $submission->created_at,
                ];
            });

            return response()->json([
                'bab' => [
                    'id_laporan_bab' => $bab->id_laporan_bab,
                    'judul_bab' => $bab->judul_bab
                ],
                'submissions' => $submissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load submissions',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store nilai akhir mahasiswa bimbingan
     */
    public function storeNilai(Request $request)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        $request->validate([
            'id_peserta_bimbingan' => 'required|uuid|exists:peserta_bimbingan,id_peserta_bimbingan',
            'nilai_angka' => 'required|numeric|min:0|max:100'
        ]);

        $peserta = PesertaBimbingan::findOrFail($request->id_peserta_bimbingan);

        // Check authorization
        if (!in_array($dosen->id_dosen, [$peserta->id_dosen_pembimbing, $peserta->id_dosen_pembimbing_2])) {
            abort(403, 'Anda tidak memiliki akses untuk memberikan nilai kepada mahasiswa ini.');
        }

        // Convert nilai angka to huruf and indeks
        $nilaiAngka = $request->nilai_angka;
        $converted = $this->convertNilai($nilaiAngka);

        // Check if nilai already exists
        $nilai = NilaiPerkuliahan::where('jenis_peserta', 'BIMBINGAN')
            ->where('id_peserta_bimbingan', $request->id_peserta_bimbingan)
            ->first();

        if ($nilai) {
            // Update existing nilai
            $nilai->update([
                'nilai_angka' => $nilaiAngka,
                'nilai_huruf' => $converted['huruf'],
                'nilai_indeks' => $converted['indeks']
            ]);
            $message = 'Nilai berhasil diperbarui.';
        } else {
            // Create new nilai
            NilaiPerkuliahan::create([
                'id_nilai_perkuliahan' => (string) Str::uuid(),
                'jenis_peserta' => 'BIMBINGAN',
                'id_peserta' => null,
                'id_peserta_bimbingan' => $request->id_peserta_bimbingan,
                'nilai_angka' => $nilaiAngka,
                'nilai_huruf' => $converted['huruf'],
                'nilai_indeks' => $converted['indeks']
            ]);
            $message = 'Nilai berhasil disimpan.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Convert nilai angka to huruf and indeks
     */
    private function convertNilai($nilaiAngka)
    {
        if ($nilaiAngka >= 85) return ['huruf' => 'A', 'indeks' => 4.00];
        if ($nilaiAngka >= 80) return ['huruf' => 'A-', 'indeks' => 3.70];
        if ($nilaiAngka >= 75) return ['huruf' => 'B+', 'indeks' => 3.30];
        if ($nilaiAngka >= 70) return ['huruf' => 'B', 'indeks' => 3.00];
        if ($nilaiAngka >= 65) return ['huruf' => 'B-', 'indeks' => 2.70];
        if ($nilaiAngka >= 60) return ['huruf' => 'C+', 'indeks' => 2.30];
        if ($nilaiAngka >= 55) return ['huruf' => 'C', 'indeks' => 2.00];
        if ($nilaiAngka >= 50) return ['huruf' => 'C-', 'indeks' => 1.70];
        if ($nilaiAngka >= 45) return ['huruf' => 'D', 'indeks' => 1.00];
        return ['huruf' => 'E', 'indeks' => 0.00];
    }
}
