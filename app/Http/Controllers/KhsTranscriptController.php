<?php

namespace App\Http\Controllers;

use App\Services\KhsTranscriptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;
use App\Models\Semester;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class KhsTranscriptController extends Controller
{
    protected $khsTranscriptService;

    public function __construct(KhsTranscriptService $khsTranscriptService)
    {
        $this->khsTranscriptService = $khsTranscriptService;
    }

    /**
     * Halaman utama KHS - pilih semester
     */
    public function indexKhs(Request $request)
    {
        $user = Auth::user();
        $mahasiswaId = $request->get('mahasiswa_id');

        // Jika admin/dosen dan ada parameter mahasiswa_id
        if ($mahasiswaId && $user->hasRole(['admin', 'dosen'])) {
            $mahasiswa = Mahasiswa::with('pengguna')->findOrFail($mahasiswaId);
        } else {
            // Mahasiswa login sendiri
            if (!$user->mahasiswa) {
                return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan');
            }
            $mahasiswa = $user->mahasiswa;
            $mahasiswaId = $mahasiswa->id_mahasiswa;
        }

        // Get daftar semester yang pernah diambil
        $semesterList = $this->khsTranscriptService->getSemesterMahasiswa($mahasiswaId);

        return view('akademik.khs.index', compact('mahasiswa', 'semesterList'));
    }

    /**
     * Tampilkan KHS untuk semester tertentu
     */
    public function showKhs(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|uuid|exists:semester,id_semester',
            'mahasiswa_id' => 'nullable|uuid|exists:mahasiswa,id_mahasiswa'
        ]);

        try {
            $user = Auth::user();
            $mahasiswaId = $request->get('mahasiswa_id');

            // Validasi akses
            if ($mahasiswaId && !$user->hasRole(['admin', 'dosen'])) {
                return redirect()->back()->with('error', 'Tidak memiliki akses untuk melihat data mahasiswa lain');
            }

            if (!$mahasiswaId) {
                if (!$user->mahasiswa) {
                    return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan');
                }
                $mahasiswaId = $user->mahasiswa->id_mahasiswa;
            }

            $semesterId = $request->get('semester_id');
            $khsData = $this->khsTranscriptService->generateKhs($mahasiswaId, $semesterId);

            return view('akademik.khs.show', compact('khsData'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Transcript
     */
    public function indexTranscript(Request $request)
    {
        try {
            $user = Auth::user();
            $mahasiswaId = $request->get('mahasiswa_id');

            // Jika admin/dosen dan ada parameter mahasiswa_id
            if ($mahasiswaId && $user->hasRole(['admin', 'dosen'])) {
                $mahasiswa = Mahasiswa::with('pengguna')->findOrFail($mahasiswaId);
            } else {
                // Mahasiswa login sendiri
                if (!$user->mahasiswa) {
                    return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan');
                }
                $mahasiswa = $user->mahasiswa;
                $mahasiswaId = $mahasiswa->id_mahasiswa;
            }

            $transcriptData = $this->khsTranscriptService->generateTranscript($mahasiswaId);

            return view('akademik.transcript.index', compact('transcriptData'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download KHS PDF
     */
    public function downloadKhsPdf(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|uuid|exists:semester,id_semester',
            'mahasiswa_id' => 'nullable|uuid|exists:mahasiswa,id_mahasiswa'
        ]);

        try {
            $user = Auth::user();
            $mahasiswaId = $request->get('mahasiswa_id');

            // Validasi akses
            if ($mahasiswaId && !$user->hasRole(['admin', 'dosen'])) {
                return redirect()->back()->with('error', 'Tidak memiliki akses untuk download data mahasiswa lain');
            }

            if (!$mahasiswaId) {
                if (!$user->mahasiswa) {
                    return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan');
                }
                $mahasiswaId = $user->mahasiswa->id_mahasiswa;
            }

            $semesterId = $request->get('semester_id');
            $khsData = $this->khsTranscriptService->generateKhs($mahasiswaId, $semesterId);

            $pdf = PDF::loadView('pdf.khs', compact('khsData'));
            $filename = 'KHS_' . $khsData['mahasiswa']['nim'] . '_' . $khsData['semester']['kode'] . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download Transcript PDF
     */
    public function downloadTranscriptPdf(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'nullable|uuid|exists:mahasiswa,id_mahasiswa'
        ]);

        try {
            $user = Auth::user();
            $mahasiswaId = $request->get('mahasiswa_id');

            // Validasi akses
            if ($mahasiswaId && !$user->hasRole(['admin', 'dosen'])) {
                return redirect()->back()->with('error', 'Tidak memiliki akses untuk download data mahasiswa lain');
            }

            if (!$mahasiswaId) {
                if (!$user->mahasiswa) {
                    return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan');
                }
                $mahasiswaId = $user->mahasiswa->id_mahasiswa;
            }

            $transcriptData = $this->khsTranscriptService->generateTranscript($mahasiswaId);

            $pdf = PDF::loadView('pdf.transcript', compact('transcriptData'));
            $filename = 'Transcript_' . $transcriptData['mahasiswa']['nim'] . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Dashboard ringkasan akademik
     */
    public function dashboardAkademik(Request $request)
    {
        try {
            $user = Auth::user();
            $mahasiswaId = $request->get('mahasiswa_id');

            // Jika admin/dosen dan ada parameter mahasiswa_id
            if ($mahasiswaId && $user->hasRole(['admin', 'dosen'])) {
                $mahasiswa = Mahasiswa::with('pengguna')->findOrFail($mahasiswaId);
            } else {
                // Mahasiswa login sendiri
                if (!$user->mahasiswa) {
                    return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan');
                }
                $mahasiswa = $user->mahasiswa;
                $mahasiswaId = $mahasiswa->id_mahasiswa;
            }

            // Get data transcript untuk ringkasan
            $transcriptData = $this->khsTranscriptService->generateTranscript($mahasiswaId);

            // Get semester list untuk chart IP per semester
            $semesterList = $this->khsTranscriptService->getSemesterMahasiswa($mahasiswaId);

            $ipPerSemester = [];
            foreach ($semesterList as $semester) {
                $khsData = $this->khsTranscriptService->generateKhs($mahasiswaId, $semester->id_semester);
                $ipPerSemester[] = [
                    'semester' => $semester->nama_semester,
                    'ip' => $khsData['ringkasan']['ip_semester']
                ];
            }

            $ringkasanData = [
                'mahasiswa' => $transcriptData['mahasiswa'],
                'ringkasan_keseluruhan' => $transcriptData['ringkasan'],
                'ip_per_semester' => $ipPerSemester,
                'total_mata_kuliah_lulus' => count($transcriptData['mata_kuliah']),
                'semester_list' => $semesterList
            ];

            return view('akademik.dashboard', compact('ringkasanData'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman cari mahasiswa (untuk admin/dosen)
     */
    public function cariMahasiswa(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'dosen'])) {
            return redirect()->back()->with('error', 'Tidak memiliki akses');
        }

        // Ambil semua program studi untuk filter
        $programStudis = \App\Models\ProgramStudi::with('jenjang')
            ->orderBy('nama_program_studi')
            ->get();

        // Query builder untuk mahasiswa
        $query = Mahasiswa::with(['pengguna', 'programStudi.jenjang'])
            ->orderBy('nim');

        // Filter berdasarkan program studi jika ada
        if ($request->filled('program_studi')) {
            $query->where('id_program_studi', $request->program_studi);
        }

        // Search berdasarkan NIM atau nama jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhereHas('pengguna', function ($subQ) use ($search) {
                        $subQ->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        // Ambil semua data untuk client-side pagination
        $mahasiswaList = $query->get();

        return view('akademik.cari-mahasiswa', compact('mahasiswaList', 'programStudis'));
    }
}
