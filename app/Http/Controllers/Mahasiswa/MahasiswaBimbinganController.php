<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PesertaBimbingan;
use App\Models\LaporanBab;
use App\Models\BimbinganFile;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MahasiswaBimbinganController extends Controller
{
    /**
     * Dashboard mahasiswa dengan tab KKN, Magang, Skripsi
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            abort(403, 'Akses ditolak. Anda bukan mahasiswa.');
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
            'mahasiswa' => $mahasiswa
        ];

        if ($selectedSemester) {
            // Base query untuk peserta bimbingan mahasiswa ini
            $baseQuery = PesertaBimbingan::with([
                'registrasiMahasiswa.mahasiswa.pengguna',
                'mataKuliah',
                'dosenPembimbing.pengguna',
                'dosenPembimbing2.pengguna',
                'laporanBabs.bimbinganFiles',
                'kknDetail.kelompokKkn',
                'magangDetail',
                'skripsiDetail'
            ])
                ->whereHas('registrasiMahasiswa', function ($q) use ($selectedSemester, $mahasiswa) {
                    $q->where('id_semester', $selectedSemester->id_semester)
                        ->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
                })
                ->where('status_mata_kuliah', 'APPROVED');

            // Data untuk KKN
            $data['kkn'] = (clone $baseQuery)
                ->whereHas('mataKuliah', function ($q) {
                    $q->where('jenis_mata_kuliah', 'KKN');
                })
                ->get()
                ->map(function ($peserta) {
                    return $this->enrichPesertaData($peserta, 'KKN');
                });

            // Data untuk Magang
            $data['magang'] = (clone $baseQuery)
                ->whereHas('mataKuliah', function ($q) {
                    $q->where('jenis_mata_kuliah', 'MAGANG');
                })
                ->get()
                ->map(function ($peserta) {
                    return $this->enrichPesertaData($peserta, 'MAGANG');
                });

            // Data untuk Skripsi
            $data['skripsi'] = (clone $baseQuery)
                ->whereHas('mataKuliah', function ($q) {
                    $q->where('jenis_mata_kuliah', 'SKRIPSI');
                })
                ->get()
                ->map(function ($peserta) {
                    return $this->enrichPesertaData($peserta, 'SKRIPSI');
                });

            // Summary stats
            $allPeserta = collect([$data['kkn'], $data['magang'], $data['skripsi']])->flatten();
            $data['stats'] = [
                'total_kkn' => $data['kkn']->count(),
                'total_magang' => $data['magang']->count(),
                'total_skripsi' => $data['skripsi']->count(),
                'total_bab' => $allPeserta->sum('progress.total_bab'),
                'total_approved' => $allPeserta->sum('progress.bab_approved'),
                'total_pending' => $allPeserta->sum('progress.bab_submitted'),
                'total_needs_revision' => $allPeserta->sum('progress.bab_needs_revision')
            ];
        }

        return view('mahasiswa.bimbingan.index', $data);
    }

    /**
     * Detail tracking laporan mahasiswa
     */
    public function detail($id_peserta_bimbingan)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $peserta = PesertaBimbingan::with([
            'registrasiMahasiswa.mahasiswa.pengguna',
            'mataKuliah',
            'dosenPembimbing.pengguna',
            'dosenPembimbing2.pengguna',
            'laporanBabs.bimbinganFiles' => function ($q) {
                $q->latest();
            },
            'kknDetail.kelompokKkn',
            'magangDetail',
            'skripsiDetail'
        ])->findOrFail($id_peserta_bimbingan);

        // Check authorization
        if ($peserta->registrasiMahasiswa->id_mahasiswa !== $mahasiswa->id_mahasiswa) {
            abort(403, 'Anda tidak memiliki akses ke bimbingan ini.');
        }

        $jenisBimbingan = $peserta->mataKuliah->jenis_mata_kuliah;

        // Enrich bab data
        $data = [
            'peserta' => $peserta,
            'mahasiswa' => $mahasiswa,
            'jenis_bimbingan' => $jenisBimbingan,
            'mata_kuliah' => $peserta->mataKuliah,
            'pembimbing_utama' => $peserta->dosenPembimbing,
            'pembimbing_kedua' => $peserta->dosenPembimbing2,
            'babs' => $peserta->laporanBabs->map(function ($bab) {
                return [
                    'id_laporan_bab' => $bab->id_laporan_bab,
                    'judul_bab' => $bab->judul_bab,
                    'konten' => $bab->konten,
                    'file_template' => $bab->file_template,
                    'status' => $bab->status,
                    'status_formatted' => $bab->status_formatted,
                    'catatan_pembimbing' => $bab->catatan_pembimbing,
                    'submission_count' => $bab->bimbinganFiles->count(),
                    'latest_submission' => $bab->bimbinganFiles->first(),
                    'submissions' => $bab->bimbinganFiles,
                    'can_submit' => in_array($bab->status, ['DRAFT', 'NEEDS_REVISION']),
                    'created_at' => $bab->created_at,
                    'submitted_at' => $bab->submitted_at,
                    'approved_at' => $bab->approved_at
                ];
            })
        ];

        // Detail spesifik berdasarkan jenis
        switch ($jenisBimbingan) {
            case 'KKN':
                $data['detail_bimbingan'] = $peserta->kknDetail ? [
                    'kelompok' => $peserta->kknDetail->kelompokKkn,
                    'peran' => $peserta->kknDetail->peran_kelompok,
                    'target_program_kerja' => $peserta->kknDetail->target_program_kerja,
                ] : null;
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

        return view('mahasiswa.bimbingan.detail', $data);
    }

    /**
     * Submit bab (file atau text)
     */
    public function submitBab(Request $request, $id_peserta_bimbingan, $id_laporan_bab)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $peserta = PesertaBimbingan::findOrFail($id_peserta_bimbingan);
        $bab = LaporanBab::where('id_peserta_bimbingan', $id_peserta_bimbingan)
            ->findOrFail($id_laporan_bab);

        // Check authorization
        if ($peserta->registrasiMahasiswa->id_mahasiswa !== $mahasiswa->id_mahasiswa) {
            abort(403);
        }

        // Check if can submit
        if (!in_array($bab->status, ['DRAFT', 'NEEDS_REVISION'])) {
            return redirect()->back()->with('error', 'Bab ini tidak dapat disubmit lagi.');
        }

        $request->validate([
            'submission_type' => 'required|in:file,text',
            'file_submission' => 'required_if:submission_type,file|file|mimes:pdf,doc,docx|max:10240',
            'text_submission' => 'required_if:submission_type,text|string'
        ]);

        $data = [
            'id_bimbingan_file' => (string) Str::uuid(),
            'id_peserta_bimbingan' => $id_peserta_bimbingan,
            'id_laporan_bab' => $id_laporan_bab,
            'input_type' => $request->submission_type === 'file' ? 'FILE' : 'TEXT'
        ];

        $safeTitle = Str::slug($bab->judul_bab, '_');

        if ($request->submission_type === 'file') {
            $file = $request->file('file_submission');
            $filename = 'submission_' . $safeTitle . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('laporan/submission', $filename, 'public');
            $data['file_path'] = $filePath;
        } else {
            $data['konten'] = $request->text_submission;
        }

        BimbinganFile::create($data);

        // Update status bab menjadi SUBMITTED
        $bab->update([
            'status' => 'SUBMITTED',
            'submitted_at' => now()
        ]);

        return redirect()->back()->with('success', 'Submission berhasil dikirim ke pembimbing.');
    }

    /**
     * View submissions untuk bab tertentu
     */
    public function viewSubmissions($id_peserta_bimbingan, $id_laporan_bab)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $peserta = PesertaBimbingan::findOrFail($id_peserta_bimbingan);
        $bab = LaporanBab::with(['bimbinganFiles' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($id_laporan_bab);


        // Check authorization
        if ($peserta->registrasiMahasiswa->id_mahasiswa !== $mahasiswa->id_mahasiswa) {
            abort(403);
        }

        $submissions = $bab->bimbinganFiles->map(function ($submission) {
            return [
                'id_bimbingan_file' => $submission->id_bimbingan_file,
                'input_type' => $submission->input_type,
                'file_path' => $submission->file_path,
                'konten' => $submission->konten,
                'created_at' => $submission->created_at
            ];
        });

        return response()->json([
            'bab' => [
                'judul_bab' => $bab->judul_bab,
                'status' => $bab->status_formatted
            ],
            'submissions' => $submissions
        ]);
    }

    /**
     * Enrich peserta data dengan informasi tambahan
     */
    private function enrichPesertaData($peserta, $jenis)
    {
        $data = [
            'id_peserta_bimbingan' => $peserta->id_peserta_bimbingan,
            'mata_kuliah' => $peserta->mataKuliah,
            'jenis' => $jenis,
            'pembimbing_utama' => $peserta->dosenPembimbing,
            'pembimbing_kedua' => $peserta->dosenPembimbing2,
            'detail' => null
        ];

        // Progress laporan
        $totalBab = $peserta->laporanBabs->count();
        $babApproved = $peserta->laporanBabs->where('status', 'APPROVED')->count();
        $babSubmitted = $peserta->laporanBabs->where('status', 'SUBMITTED')->count();
        $babNeedsRevision = $peserta->laporanBabs->where('status', 'NEEDS_REVISION')->count();

        $data['progress'] = [
            'total_bab' => $totalBab,
            'bab_approved' => $babApproved,
            'bab_submitted' => $babSubmitted,
            'bab_needs_revision' => $babNeedsRevision,
            'bab_draft' => $totalBab - $babApproved - $babSubmitted - $babNeedsRevision,
            'progress_percentage' => $totalBab > 0 ? round(($babApproved / $totalBab) * 100) : 0
        ];

        // Data spesifik berdasarkan jenis
        switch ($jenis) {
            case 'KKN':
                if ($peserta->kknDetail) {
                    $periodeMulai = $peserta->kknDetail->kelompokKkn->periode_mulai
                        ? \Carbon\Carbon::parse($peserta->kknDetail->kelompokKkn->periode_mulai)->format('d M Y')
                        : 'Belum ditentukan';

                    $periodeSelesai = $peserta->kknDetail->kelompokKkn->periode_selesai
                        ? \Carbon\Carbon::parse($peserta->kknDetail->kelompokKkn->periode_selesai)->format('d M Y')
                        : 'Belum ditentukan';

                    $data['detail'] = [
                        'kelompok' => $peserta->kknDetail->kelompokKkn->nama_kelompok ?? 'Belum ditentukan',
                        'lokasi' => $peserta->kknDetail->kelompokKkn->lokasi ?? 'Belum ditentukan',
                        'peran' => $peserta->kknDetail->peran_kelompok,
                        'target_program_kerja' => $peserta->kknDetail->kelompokKkn->target_program_kerja,
                        'alamat_lokasi' => $peserta->kknDetail->kelompokKkn->alamat_lokasi,
                        'periode' => $periodeMulai . ' - ' . $periodeSelesai,
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
                        'bidang_penelitian' => $peserta->skripsiDetail->bidang_penelitian ?? 'Belum ditentukan'
                    ];
                }
                break;
        }

        // Last activity
        $lastActivity = $peserta->laporanBabs()
            ->with('bimbinganFiles')
            ->get()
            ->flatMap->bimbinganFiles
            ->max('created_at');

        $data['last_activity'] = $lastActivity ? $lastActivity->diffForHumans() : 'Belum ada aktivitas';

        return (object) $data;
    }

    /**
     * View detail kelompok KKN untuk mahasiswa
     */
    public function viewKelompokDetail($id_peserta_bimbingan)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $peserta = PesertaBimbingan::with([
            'registrasiMahasiswa.mahasiswa.pengguna',
            'mataKuliah',
            'kknDetail.kelompokKkn.dpl.pengguna',
            'kknDetail.kelompokKkn.kknDetails.pesertaBimbingan.registrasiMahasiswa.mahasiswa.pengguna'
        ])->findOrFail($id_peserta_bimbingan);

        // Check authorization
        if ($peserta->registrasiMahasiswa->id_mahasiswa !== $mahasiswa->id_mahasiswa) {
            abort(403, 'Anda tidak memiliki akses ke kelompok ini.');
        }

        // Check if this is KKN
        if ($peserta->mataKuliah->jenis_mata_kuliah !== 'KKN') {
            abort(404, 'Halaman ini hanya untuk mata kuliah KKN.');
        }

        $kknDetail = $peserta->kknDetail;
        if (!$kknDetail) {
            abort(404, 'Detail KKN tidak ditemukan.');
        }

        $kelompok = $kknDetail->kelompokKkn;

        // Get all peserta in this kelompok with their progress
        $peserta_list = $kelompok->kknDetails->map(function ($detail) {
            $pesertaBimbingan = $detail->pesertaBimbingan;
            $mahasiswaData = $pesertaBimbingan->registrasiMahasiswa->mahasiswa;

            // Calculate progress
            $totalBab = $pesertaBimbingan->laporanBabs->count();
            $babApproved = $pesertaBimbingan->laporanBabs->where('status', 'APPROVED')->count();
            $babSubmitted = $pesertaBimbingan->laporanBabs->where('status', 'SUBMITTED')->count();
            $babNeedsRevision = $pesertaBimbingan->laporanBabs->where('status', 'NEEDS_REVISION')->count();

            // Last activity
            $lastActivity = $pesertaBimbingan->laporanBabs()
                ->with('bimbinganFiles')
                ->get()
                ->flatMap->bimbinganFiles
                ->max('created_at');

            return [
                'id_kkn_detail' => $detail->id_kkn_detail,
                'id_peserta_bimbingan' => $pesertaBimbingan->id_peserta_bimbingan,
                'peran' => $detail->peran_kelompok,
                'mahasiswa' => [
                    'id_mahasiswa' => $mahasiswaData->id_mahasiswa,
                    'nim' => $mahasiswaData->nim,
                    'nama' => $mahasiswaData->pengguna->nama,
                    'email' => $mahasiswaData->pengguna->email,
                    'no_hp' => $mahasiswaData->pengguna->no_hp,
                    'foto' => $mahasiswaData->foto,
                    'angkatan' => $mahasiswaData->angkatan
                ],
                'progress' => [
                    'total_bab' => $totalBab,
                    'bab_approved' => $babApproved,
                    'bab_submitted' => $babSubmitted,
                    'bab_needs_revision' => $babNeedsRevision,
                    'progress_percentage' => $totalBab > 0 ? round(($babApproved / $totalBab) * 100) : 0
                ],
                'last_activity' => $lastActivity
            ];
        })->sortByDesc(function ($item) {
            return $item['peran'] === 'KETUA' ? 1 : 0;
        })->values();

        // Get dokumentasi
        $dokumentasi = [
            'images' => \App\Models\KknDokumentasi::where('id_kelompok_kkn', $kelompok->id_kelompok_kkn)
                ->where('file_type', 'IMAGE')
                ->with('uploader.pengguna')
                ->latest()
                ->get(),
            'documents' => \App\Models\KknDokumentasi::where('id_kelompok_kkn', $kelompok->id_kelompok_kkn)
                ->where('file_type', 'DOCUMENT')
                ->with('uploader.pengguna')
                ->latest()
                ->get()
                ->map(function ($doc) {
                    $doc->formatted_file_size = $this->formatFileSize($doc->file_size);
                    return $doc;
                })
        ];

        // Check if current user is ketua
        $isKetua = $kknDetail->peran_kelompok === 'KETUA';

        return view('mahasiswa.bimbingan.kelompok-detail', [
            'kelompok' => $kelompok,
            'peserta' => $peserta,
            'kknDetail' => $kknDetail,
            'peserta_list' => $peserta_list,
            'dokumentasi' => $dokumentasi,
            'isKetua' => $isKetua,
            'mahasiswa' => $mahasiswa
        ]);
    }

    /**
     * Upload dokumentasi KKN (hanya ketua) - Support multiple files
     */
    public function uploadDokumentasi(Request $request, $id_peserta_bimbingan)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $peserta = PesertaBimbingan::with('kknDetail')->findOrFail($id_peserta_bimbingan);

        // Check authorization
        if ($peserta->registrasiMahasiswa->id_mahasiswa !== $mahasiswa->id_mahasiswa) {
            abort(403);
        }

        // Check if ketua
        if ($peserta->kknDetail->peran_kelompok !== 'KETUA') {
            return redirect()->back()->with('error', 'Hanya ketua kelompok yang dapat mengupload dokumentasi.');
        }

        $request->validate([
            'file_type' => 'required|in:IMAGE,DOCUMENT',
            'files' => 'required|array|min:1|max:10', // Max 10 files at once
            'files.*' => 'required|file|max:10240', // 10MB per file
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string'
        ]);

        $fileType = $request->file_type;

        // Validate file types based on selection
        if ($fileType === 'IMAGE') {
            $request->validate([
                'files.*' => 'mimes:jpg,jpeg,png,gif|max:5120' // 5MB for images
            ]);
        } else {
            $request->validate([
                'files.*' => 'mimes:pdf,doc,docx,xls,xlsx|max:10240' // 10MB for documents
            ]);
        }

        $uploadedCount = 0;
        $errors = [];

        foreach ($request->file('files') as $index => $file) {
            try {
                $timestamp = time() . '_' . $index;
                $filename = $timestamp . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('kkn/dokumentasi/' . $peserta->kknDetail->id_kelompok_kkn, $filename, 'public');

                \App\Models\KknDokumentasi::create([
                    'id_kkn_dokumentasi' => (string) Str::uuid(),
                    'id_kelompok_kkn' => $peserta->kknDetail->id_kelompok_kkn,
                    'judul' => $request->judul . ($index > 0 ? ' (' . ($index + 1) . ')' : ''),
                    'deskripsi' => $request->deskripsi,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'original_filename' => $file->getClientOriginalName(),
                    'uploaded_by' => $mahasiswa->id_mahasiswa
                ]);

                $uploadedCount++;
            } catch (\Exception $e) {
                $errors[] = 'Gagal mengupload: ' . $file->getClientOriginalName();
            }
        }

        if ($uploadedCount > 0) {
            $message = $uploadedCount . ' file berhasil diupload.';
            if (count($errors) > 0) {
                $message .= ' ' . count($errors) . ' file gagal diupload.';
            }
            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Semua file gagal diupload.');
        }
    }

    /**
     * Delete dokumentasi KKN (hanya ketua yang upload)
     */
    public function deleteDokumentasi($id_peserta_bimbingan, $id_dokumentasi)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $peserta = PesertaBimbingan::with('kknDetail')->findOrFail($id_peserta_bimbingan);

        // Check authorization
        if ($peserta->registrasiMahasiswa->id_mahasiswa !== $mahasiswa->id_mahasiswa) {
            abort(403);
        }

        // Check if ketua
        if ($peserta->kknDetail->peran_kelompok !== 'KETUA') {
            return redirect()->back()->with('error', 'Hanya ketua kelompok yang dapat menghapus dokumentasi.');
        }

        $dokumentasi = \App\Models\KknDokumentasi::where('id_kelompok_kkn', $peserta->kknDetail->id_kelompok_kkn)
            ->findOrFail($id_dokumentasi);

        // Check if uploader
        if ($dokumentasi->uploaded_by !== $mahasiswa->id_mahasiswa) {
            return redirect()->back()->with('error', 'Anda hanya dapat menghapus dokumentasi yang Anda upload.');
        }

        // Delete file
        if (Storage::disk('public')->exists($dokumentasi->file_path)) {
            Storage::disk('public')->delete($dokumentasi->file_path);
        }

        $dokumentasi->delete();

        return redirect()->back()->with('success', 'Dokumentasi berhasil dihapus.');
    }

    /**
     * Helper: Format file size
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
