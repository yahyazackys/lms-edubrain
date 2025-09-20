<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Mahasiswa;
use App\Models\KelasKuliah;
use App\Models\PesertaKelasKuliah;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\Uts;
use App\Models\Uas;
use App\Models\PengumpulanTugas;
use App\Models\PengumpulanUts;
use App\Models\PengumpulanUas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DetailKelasController extends Controller
{
    public function show($kelasId)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        if (!$mahasiswa) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        // Cek apakah mahasiswa terdaftar di kelas ini
        $pesertaKelas = PesertaKelasKuliah::where('id_kelas_kuliah', $kelasId)
            ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswa) {
                $q->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
            })
            ->where('status_mata_kuliah', 'APPROVED')
            ->first();

        if (!$pesertaKelas) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar di kelas ini!');
        }

        $kelasKuliah = KelasKuliah::with([
            'mataKuliah',
            'dosen.pengguna',
            'semester'
        ])->findOrFail($kelasId);

        // Ambil semua materi
        $materis = Materi::where('id_kelas_kuliah', $kelasId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Ambil semua tugas dengan status pengumpulan mahasiswa
        $tugas = Tugas::where('id_kelas_kuliah', $kelasId)
            ->with(['pengumpulanTugas' => function ($q) use ($mahasiswa) {
                $q->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
            }])
            ->orderBy('created_at', 'asc')
            ->get();

        // Ambil semua UTS dengan status pengumpulan mahasiswa
        $uts = Uts::where('id_kelas_kuliah', $kelasId)
            ->with(['pengumpulanUts' => function ($q) use ($mahasiswa) {
                $q->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
            }])
            ->orderBy('created_at', 'asc')
            ->get();

        // Ambil semua UAS dengan status pengumpulan mahasiswa
        $uas = Uas::where('id_kelas_kuliah', $kelasId)
            ->with(['pengumpulanUas' => function ($q) use ($mahasiswa) {
                $q->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
            }])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('mahasiswa.jadwal.detail-kelas', compact(
            'kelasKuliah',
            'materis',
            'tugas',
            'uts',
            'uas',
            'mahasiswa'
        ));
    }

    public function downloadMateri($materiId)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        $materi = Materi::findOrFail($materiId);

        // Cek apakah mahasiswa terdaftar di kelas ini
        $pesertaKelas = PesertaKelasKuliah::where('id_kelas_kuliah', $materi->id_kelas_kuliah)
            ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswa) {
                $q->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
            })
            ->where('status_mata_kuliah', 'APPROVED')
            ->first();

        if (!$pesertaKelas) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke materi ini!');
        }

        if (!$materi->dokumen || !Storage::disk('public')->exists($materi->dokumen)) {
            return redirect()->back()->with('error', 'File materi tidak ditemukan!');
        }

        // ✅ FIXED: Konsisten menggunakan disk('public')
        $filePath = Storage::disk('public')->path($materi->dokumen);
        $fileName = basename($materi->dokumen);

        return response()->file($filePath, [
            'Content-Type' => File::mimeType($filePath),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

    public function submitTugas(Request $request, $tugasId)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        $tugas = Tugas::findOrFail($tugasId);

        // Cek deadline
        if ($tugas->batas_akhir_pengumpulan < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Batas waktu pengumpulan sudah berakhir!'
            ], 400);
        }

        // Cek apakah mahasiswa terdaftar di kelas ini
        $pesertaKelas = PesertaKelasKuliah::where('id_kelas_kuliah', $tugas->id_kelas_kuliah)
            ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswa) {
                $q->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
            })
            ->where('status_mata_kuliah', 'APPROVED')
            ->first();

        if (!$pesertaKelas) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengumpulkan tugas ini!'
            ], 403);
        }

        $request->validate([
            'dokumen' => 'required|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx'
        ]);

        try {
            // Cek apakah sudah ada pengumpulan sebelumnya
            $pengumpulan = PengumpulanTugas::where('id_tugas', $tugasId)
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->first();

            $oldDokumen = null;
            if ($pengumpulan && $pengumpulan->dokumen) {
                $oldDokumen = $pengumpulan->dokumen;
            }

            // ✅ FIXED: Konsisten menggunakan disk('public') seperti dosen
            $file = $request->file('dokumen');
            $filename = $file->getClientOriginalName();
            $dokumenPath = $file->storeAs('pengumpulan/tugas', $filename, 'public');

            DB::beginTransaction();

            if ($pengumpulan) {
                // Update pengumpulan yang sudah ada
                $pengumpulan->update([
                    'dokumen' => $dokumenPath
                ]);

                // ✅ FIXED: Hapus file lama menggunakan disk('public')
                if ($oldDokumen && Storage::disk('public')->exists($oldDokumen)) {
                    Storage::disk('public')->delete($oldDokumen);
                }
            } else {
                // Buat pengumpulan baru
                PengumpulanTugas::create([
                    'id_tugas' => $tugasId,
                    'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                    'dokumen' => $dokumenPath
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tugas berhasil dikumpulkan!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            // ✅ FIXED: Cleanup menggunakan disk('public')
            if (isset($dokumenPath) && Storage::disk('public')->exists($dokumenPath)) {
                Storage::disk('public')->delete($dokumenPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submitUts(Request $request, $utsId)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        $uts = Uts::findOrFail($utsId);

        // Cek deadline
        if ($uts->batas_akhir_pengumpulan < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Batas waktu pengumpulan sudah berakhir!'
            ], 400);
        }

        // Cek akses mahasiswa
        $pesertaKelas = PesertaKelasKuliah::where('id_kelas_kuliah', $uts->id_kelas_kuliah)
            ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswa) {
                $q->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
            })
            ->where('status_mata_kuliah', 'APPROVED')
            ->first();

        if (!$pesertaKelas) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengumpulkan UTS ini!'
            ], 403);
        }

        $request->validate([
            'dokumen' => 'required|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx'
        ]);

        try {
            // Cek apakah sudah ada pengumpulan sebelumnya
            $pengumpulan = PengumpulanUts::where('id_uts', $utsId)
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->first();

            $oldDokumen = null;
            if ($pengumpulan && $pengumpulan->dokumen) {
                $oldDokumen = $pengumpulan->dokumen;
            }

            // ✅ FIXED: Konsisten menggunakan disk('public') seperti submitTugas
            $file = $request->file('dokumen');
            $filename = $file->getClientOriginalName();
            $dokumenPath = $file->storeAs('pengumpulan/uts', $filename, 'public');

            DB::beginTransaction();

            if ($pengumpulan) {
                // Update pengumpulan yang sudah ada
                $pengumpulan->update([
                    'dokumen' => $dokumenPath
                ]);

                // ✅ FIXED: Hapus file lama menggunakan disk('public')
                if ($oldDokumen && Storage::disk('public')->exists($oldDokumen)) {
                    Storage::disk('public')->delete($oldDokumen);
                }
            } else {
                // Buat pengumpulan baru
                PengumpulanUts::create([
                    'id_uts' => $utsId,
                    'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                    'dokumen' => $dokumenPath
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'UTS berhasil dikumpulkan!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            // ✅ FIXED: Cleanup menggunakan disk('public')
            if (isset($dokumenPath) && Storage::disk('public')->exists($dokumenPath)) {
                Storage::disk('public')->delete($dokumenPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submitUas(Request $request, $uasId)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        $uas = Uas::findOrFail($uasId);

        // Cek deadline
        if ($uas->batas_akhir_pengumpulan < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Batas waktu pengumpulan sudah berakhir!'
            ], 400);
        }

        // Cek akses mahasiswa
        $pesertaKelas = PesertaKelasKuliah::where('id_kelas_kuliah', $uas->id_kelas_kuliah)
            ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswa) {
                $q->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
            })
            ->where('status_mata_kuliah', 'APPROVED')
            ->first();

        if (!$pesertaKelas) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengumpulkan UAS ini!'
            ], 403);
        }

        $request->validate([
            'dokumen' => 'required|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx'
        ]);

        try {
            // Cek apakah sudah ada pengumpulan sebelumnya
            $pengumpulan = PengumpulanUas::where('id_uas', $uasId)
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->first();

            $oldDokumen = null;
            if ($pengumpulan && $pengumpulan->dokumen) {
                $oldDokumen = $pengumpulan->dokumen;
            }

            // ✅ FIXED: Konsisten menggunakan disk('public') seperti submitTugas
            $file = $request->file('dokumen');
            $filename = $file->getClientOriginalName();
            $dokumenPath = $file->storeAs('pengumpulan/uas', $filename, 'public');

            DB::beginTransaction();

            if ($pengumpulan) {
                // Update pengumpulan yang sudah ada
                $pengumpulan->update([
                    'dokumen' => $dokumenPath
                ]);

                // ✅ FIXED: Hapus file lama menggunakan disk('public')
                if ($oldDokumen && Storage::disk('public')->exists($oldDokumen)) {
                    Storage::disk('public')->delete($oldDokumen);
                }
            } else {
                // Buat pengumpulan baru
                PengumpulanUas::create([
                    'id_uas' => $uasId,
                    'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                    'dokumen' => $dokumenPath
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'UAS berhasil dikumpulkan!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            // ✅ FIXED: Cleanup menggunakan disk('public')
            if (isset($dokumenPath) && Storage::disk('public')->exists($dokumenPath)) {
                Storage::disk('public')->delete($dokumenPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTugas($tugasId)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        $tugas = Tugas::findOrFail($tugasId);

        // Cek akses mahasiswa
        $pesertaKelas = PesertaKelasKuliah::where('id_kelas_kuliah', $tugas->id_kelas_kuliah)
            ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswa) {
                $q->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
            })
            ->where('status_mata_kuliah', 'APPROVED')
            ->first();

        if (!$pesertaKelas) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke tugas ini!');
        }

        if (!$tugas->dokumen || !Storage::disk('public')->exists($tugas->dokumen)) {
            return redirect()->back()->with('error', 'File tugas tidak ditemukan!');
        }

        // ✅ FIXED: Konsisten menggunakan disk('public')
        $filePath = Storage::disk('public')->path($tugas->dokumen);
        $fileName = basename($tugas->dokumen);

        return response()->file($filePath, [
            'Content-Type' => File::mimeType($filePath),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

    public function downloadUts($utsId)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        $uts = Uts::findOrFail($utsId);

        // Cek akses mahasiswa
        $pesertaKelas = PesertaKelasKuliah::where('id_kelas_kuliah', $uts->id_kelas_kuliah)
            ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswa) {
                $q->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
            })
            ->where('status_mata_kuliah', 'APPROVED')
            ->first();

        if (!$pesertaKelas) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke UTS ini!');
        }

        if (!$uts->dokumen || !Storage::disk('public')->exists($uts->dokumen)) {
            return redirect()->back()->with('error', 'File UTS tidak ditemukan!');
        }

        // ✅ FIXED: Konsisten seperti downloadTugas
        $filePath = Storage::disk('public')->path($uts->dokumen);
        $fileName = basename($uts->dokumen);

        return response()->file($filePath, [
            'Content-Type' => File::mimeType($filePath),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

    public function downloadUas($uasId)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        $uas = Uas::findOrFail($uasId);

        // Cek akses mahasiswa
        $pesertaKelas = PesertaKelasKuliah::where('id_kelas_kuliah', $uas->id_kelas_kuliah)
            ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswa) {
                $q->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
            })
            ->where('status_mata_kuliah', 'APPROVED')
            ->first();

        if (!$pesertaKelas) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke UAS ini!');
        }

        if (!$uas->dokumen || !Storage::disk('public')->exists($uas->dokumen)) {
            return redirect()->back()->with('error', 'File UAS tidak ditemukan!');
        }

        // ✅ FIXED: Konsisten seperti downloadTugas
        $filePath = Storage::disk('public')->path($uas->dokumen);
        $fileName = basename($uas->dokumen);

        return response()->file($filePath, [
            'Content-Type' => File::mimeType($filePath),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

    public function downloadJawabanTugas($pengumpulanId)
    {
        $pengumpulan = PengumpulanTugas::with('tugas')->findOrFail($pengumpulanId);

        // ✅ FIXED: Konsisten menggunakan disk('public')
        if (!$pengumpulan->dokumen || !Storage::disk('public')->exists($pengumpulan->dokumen)) {
            return redirect()->back()->with('error', 'File jawaban tidak ditemukan!');
        }

        $filePath = Storage::disk('public')->path($pengumpulan->dokumen);
        $fileName = basename($pengumpulan->dokumen);

        return response()->file($filePath, [
            'Content-Type' => File::mimeType($filePath),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

    public function downloadJawabanUts($pengumpulanId)
    {
        $pengumpulan = PengumpulanUts::with('uts')->findOrFail($pengumpulanId);

        // ✅ FIXED: Konsisten menggunakan disk('public')
        if (!$pengumpulan->dokumen || !Storage::disk('public')->exists($pengumpulan->dokumen)) {
            return redirect()->back()->with('error', 'File jawaban UTS tidak ditemukan!');
        }

        $filePath = Storage::disk('public')->path($pengumpulan->dokumen);
        $fileName = basename($pengumpulan->dokumen);

        return response()->file($filePath, [
            'Content-Type' => File::mimeType($filePath),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

    public function downloadJawabanUas($pengumpulanId)
    {
        $pengumpulan = PengumpulanUas::with('uas')->findOrFail($pengumpulanId);

        // ✅ FIXED: Konsisten menggunakan disk('public')
        if (!$pengumpulan->dokumen || !Storage::disk('public')->exists($pengumpulan->dokumen)) {
            return redirect()->back()->with('error', 'File jawaban UAS tidak ditemukan!');
        }

        $filePath = Storage::disk('public')->path($pengumpulan->dokumen);
        $fileName = basename($pengumpulan->dokumen);

        return response()->file($filePath, [
            'Content-Type' => File::mimeType($filePath),
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }
}
