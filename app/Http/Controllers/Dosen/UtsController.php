<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanUts;
use App\Models\Uts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UtsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_kelas_kuliah' => 'required|exists:kelas_kuliah,id_kelas_kuliah',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:5000',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
            'batas_akhir_pengumpulan' => 'required|date|after:now',
        ], [
            'id_kelas_kuliah.required' => 'Kelas kuliah harus dipilih',
            'id_kelas_kuliah.exists' => 'Kelas kuliah tidak valid',
            'judul.required' => 'Judul UTS harus diisi',
            'judul.max' => 'Judul UTS maksimal 255 karakter',
            'deskripsi.required' => 'Deskripsi UTS harus diisi',
            'deskripsi.max' => 'Deskripsi maksimal 5000 karakter',
            'dokumen.file' => 'Dokumen harus berupa file',
            'dokumen.mimes' => 'Format file harus: PDF, DOC, DOCX',
            'dokumen.max' => 'Ukuran file maksimal 10MB',
            'batas_akhir_pengumpulan.required' => 'Batas akhir pengumpulan harus diisi',
            'batas_akhir_pengumpulan.date' => 'Format tanggal tidak valid',
            'batas_akhir_pengumpulan.after' => 'Batas akhir pengumpulan harus lebih dari sekarang',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi');
        }

        try {
            $dokumenPath = null;

            // Handle file upload if exists
            if ($request->hasFile('dokumen')) {
                $file = $request->file('dokumen');
                $filename = time() . '_' . $file->getClientOriginalName();
                $dokumenPath = $file->storeAs('uts', $filename, 'public');
            }

            // Combine date and time for deadline
            $deadline = $request->batas_akhir_pengumpulan;

            Uts::create([
                'id_kelas_kuliah' => $request->id_kelas_kuliah,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'dokumen' => $dokumenPath,
                'batas_akhir_pengumpulan' => $deadline,
            ]);

            return redirect()->back()->with('success', 'UTS berhasil ditambahkan');
        } catch (\Exception $e) {
            // Delete uploaded file if database save fails
            if ($dokumenPath && Storage::disk('public')->exists($dokumenPath)) {
                Storage::disk('public')->delete($dokumenPath);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan UTS: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $uts = Uts::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:5000',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
            'batas_akhir_pengumpulan' => 'required|date|after:now',
        ], [
            'judul.required' => 'Judul UTS harus diisi',
            'judul.max' => 'Judul UTS maksimal 255 karakter',
            'deskripsi.required' => 'Deskripsi UTS harus diisi',
            'deskripsi.max' => 'Deskripsi maksimal 5000 karakter',
            'dokumen.file' => 'Dokumen harus berupa file',
            'dokumen.mimes' => 'Format file harus: PDF, DOC, DOCX',
            'dokumen.max' => 'Ukuran file maksimal 10MB',
            'batas_akhir_pengumpulan.required' => 'Batas akhir pengumpulan harus diisi',
            'batas_akhir_pengumpulan.date' => 'Format tanggal tidak valid',
            'batas_akhir_pengumpulan.after' => 'Batas akhir pengumpulan harus lebih dari sekarang',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi');
        }

        try {
            $oldDokumen = $uts->dokumen;
            $dokumenPath = $oldDokumen;

            // Handle file upload if new file is provided
            if ($request->hasFile('dokumen')) {
                $file = $request->file('dokumen');
                $filename = time() . '_' . $file->getClientOriginalName();
                $dokumenPath = $file->storeAs('uts', $filename, 'public');

                // Delete old file if exists
                if ($oldDokumen && Storage::disk('public')->exists($oldDokumen)) {
                    Storage::disk('public')->delete($oldDokumen);
                }
            }

            $deadline = $request->batas_akhir_pengumpulan;

            $uts->update([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'dokumen' => $dokumenPath,
                'batas_akhir_pengumpulan' => $deadline,
            ]);

            return redirect()->back()->with('success', 'UTS berhasil diperbarui');
        } catch (\Exception $e) {
            // Delete new uploaded file if database update fails
            if ($request->hasFile('dokumen') && $dokumenPath && $dokumenPath !== $oldDokumen) {
                if (Storage::disk('public')->exists($dokumenPath)) {
                    Storage::disk('public')->delete($dokumenPath);
                }
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui UTS: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $uts = Uts::findOrFail($id);

            // Delete associated file if exists
            if ($uts->dokumen && Storage::disk('public')->exists($uts->dokumen)) {
                Storage::disk('public')->delete($uts->dokumen);
            }

            $uts->delete();

            return redirect()->back()->with('success', 'UTS berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus UTS: ' . $e->getMessage());
        }
    }

    /**
     * Download the specified uts document.
     */
    public function download($id)
    {
        try {
            $uts = Uts::findOrFail($id);

            if (!$uts->dokumen || !Storage::disk('public')->exists($uts->dokumen)) {
                return redirect()->back()->with('error', 'File tidak ditemukan');
            }

            $filePath = Storage::disk('public')->path($uts->dokumen);
            $fileName = $uts->judul . '.' . pathinfo($uts->dokumen, PATHINFO_EXTENSION);

            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh file: ' . $e->getMessage());
        }
    }

    /**
     * Get UTS status info
     */
    public function status($id)
    {
        try {
            // Load UTS dengan relasi ke pengumpulan
            $uts = Uts::with(['pengumpulanUts.mahasiswa.pengguna'])->findOrFail($id);

            // Get semua peserta kelas (sama seperti method absensi yang berhasil)
            $pesertaKelas = $uts->kelasKuliah->pesertaKelasKuliah()
                ->with(['registrasiMahasiswa.mahasiswa.pengguna'])
                ->get();

            $totalMahasiswa = $pesertaKelas->count();
            $sudahMengumpulkan = $uts->pengumpulanUts()->count();
            $belumMengumpulkan = $totalMahasiswa - $sudahMengumpulkan;

            $isExpired = $uts->batas_akhir_pengumpulan < now();

            // Buat array daftar mahasiswa dengan status pengumpulan (mirip dengan absensi)
            $daftarMahasiswa = [];
            foreach ($pesertaKelas as $peserta) {
                $mahasiswa = $peserta->registrasiMahasiswa->mahasiswa;

                // Cari apakah mahasiswa ini sudah mengumpulkan UTS
                $pengumpulan = $uts->pengumpulanUts()
                    ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                    ->first();

                $daftarMahasiswa[] = [
                    'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                    'nim' => $mahasiswa->nim,
                    'nama' => $mahasiswa->pengguna->nama, // Kunci utama: nama dari tabel pengguna
                    'angkatan' => $mahasiswa->angkatan,
                    'sudah_mengumpulkan' => $pengumpulan ? true : false,
                    'id_pengumpulan_uts' => $pengumpulan?->id_pengumpulan_uts,
                    'file_path' => $pengumpulan?->dokumen,
                    'nilai' => $pengumpulan?->nilai,
                    'waktu_submit' => $pengumpulan ? $pengumpulan->created_at->format('d/m/Y H:i') : null,
                    'is_late' => $pengumpulan ? ($pengumpulan->created_at > $uts->batas_akhir_pengumpulan) : false
                ];
            }

            // Sort berdasarkan nama (sama seperti method absensi)
            usort($daftarMahasiswa, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'id_uts' => $uts->id_uts,
                    'judul' => $uts->judul,
                    'deskripsi' => $uts->deskripsi,
                    'deadline' => $uts->batas_akhir_pengumpulan->format('d/m/Y H:i'),
                    'is_expired' => $isExpired,
                    'total_mahasiswa' => $totalMahasiswa,
                    'sudah_mengumpulkan' => $sudahMengumpulkan,
                    'belum_mengumpulkan' => $belumMengumpulkan,
                    'persentase_pengumpulan' => $totalMahasiswa > 0 ? round(($sudahMengumpulkan / $totalMahasiswa) * 100, 1) : 0,
                    'daftar_mahasiswa' => $daftarMahasiswa
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data pengumpulan UTS: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Grade UTS submission
     */
    public function gradeUts(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100'
        ]);

        try {
            $pengumpulan = PengumpulanUts::findOrFail($id);
            $pengumpulan->update([
                'nilai' => $request->nilai
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nilai UTS berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan nilai UTS: ' . $e->getMessage()
            ], 500);
        }
    }
}
