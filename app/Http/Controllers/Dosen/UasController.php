<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanUas;
use App\Models\Uas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UasController extends Controller
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
            'judul.required' => 'Judul UAS harus diisi',
            'judul.max' => 'Judul UAS maksimal 255 karakter',
            'deskripsi.required' => 'Deskripsi UAS harus diisi',
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
                $dokumenPath = $file->storeAs('uas', $filename, 'public');
            }

            // Combine date and time for deadline
            $deadline = $request->batas_akhir_pengumpulan;

            Uas::create([
                'id_kelas_kuliah' => $request->id_kelas_kuliah,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'dokumen' => $dokumenPath,
                'batas_akhir_pengumpulan' => $deadline,
            ]);

            return redirect()->back()->with('success', 'UAS berhasil ditambahkan');
        } catch (\Exception $e) {
            // Delete uploaded file if database save fails
            if ($dokumenPath && Storage::disk('public')->exists($dokumenPath)) {
                Storage::disk('public')->delete($dokumenPath);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan UAS: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $uas = Uas::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:5000',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
            'batas_akhir_pengumpulan' => 'required|date|after:now',
        ], [
            'judul.required' => 'Judul UAS harus diisi',
            'judul.max' => 'Judul UAS maksimal 255 karakter',
            'deskripsi.required' => 'Deskripsi UAS harus diisi',
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
            $oldDokumen = $uas->dokumen;
            $dokumenPath = $oldDokumen;

            // Handle file upload if new file is provided
            if ($request->hasFile('dokumen')) {
                $file = $request->file('dokumen');
                $filename = time() . '_' . $file->getClientOriginalName();
                $dokumenPath = $file->storeAs('uas', $filename, 'public');

                // Delete old file if exists
                if ($oldDokumen && Storage::disk('public')->exists($oldDokumen)) {
                    Storage::disk('public')->delete($oldDokumen);
                }
            }

            $deadline = $request->batas_akhir_pengumpulan;

            $uas->update([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'dokumen' => $dokumenPath,
                'batas_akhir_pengumpulan' => $deadline,
            ]);

            return redirect()->back()->with('success', 'UAS berhasil diperbarui');
        } catch (\Exception $e) {
            // Delete new uploaded file if database update fails
            if ($request->hasFile('dokumen') && $dokumenPath && $dokumenPath !== $oldDokumen) {
                if (Storage::disk('public')->exists($dokumenPath)) {
                    Storage::disk('public')->delete($dokumenPath);
                }
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui UAS: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $uas = Uas::findOrFail($id);

            // Delete associated file if exists
            if ($uas->dokumen && Storage::disk('public')->exists($uas->dokumen)) {
                Storage::disk('public')->delete($uas->dokumen);
            }

            $uas->delete();

            return redirect()->back()->with('success', 'UAS berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus UAS: ' . $e->getMessage());
        }
    }

    /**
     * Download the specified uas document.
     */
    public function download($id)
    {
        try {
            $uas = Uas::findOrFail($id);

            if (!$uas->dokumen || !Storage::disk('public')->exists($uas->dokumen)) {
                return redirect()->back()->with('error', 'File tidak ditemukan');
            }

            $filePath = Storage::disk('public')->path($uas->dokumen);
            $fileName = $uas->judul . '.' . pathinfo($uas->dokumen, PATHINFO_EXTENSION);

            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh file: ' . $e->getMessage());
        }
    }

    /**
     * Get UAS status info
     */
    public function status($id)
    {
        try {
            // Load UAS dengan relasi ke pengumpulan
            $uas = Uas::with(['pengumpulanUas.mahasiswa.pengguna'])->findOrFail($id);

            // Get semua peserta kelas (sama seperti method absensi yang berhasil)
            $pesertaKelas = $uas->kelasKuliah->pesertaKelasKuliah()
                ->with(['registrasiMahasiswa.mahasiswa.pengguna'])
                ->get();

            $totalMahasiswa = $pesertaKelas->count();
            $sudahMengumpulkan = $uas->pengumpulanUas()->count();
            $belumMengumpulkan = $totalMahasiswa - $sudahMengumpulkan;

            $isExpired = $uas->batas_akhir_pengumpulan < now();

            // Buat array daftar mahasiswa dengan status pengumpulan (mirip dengan absensi)
            $daftarMahasiswa = [];
            foreach ($pesertaKelas as $peserta) {
                $mahasiswa = $peserta->registrasiMahasiswa->mahasiswa;

                // Cari apakah mahasiswa ini sudah mengumpulkan UAS
                $pengumpulan = $uas->pengumpulanUas()
                    ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                    ->first();

                $daftarMahasiswa[] = [
                    'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                    'nim' => $mahasiswa->nim,
                    'nama' => $mahasiswa->pengguna->nama, // Kunci utama: nama dari tabel pengguna
                    'angkatan' => $mahasiswa->angkatan,
                    'sudah_mengumpulkan' => $pengumpulan ? true : false,
                    'id_pengumpulan_uas' => $pengumpulan?->id_pengumpulan_uas,
                    'file_path' => $pengumpulan?->dokumen,
                    'nilai' => $pengumpulan?->nilai,
                    'waktu_submit' => $pengumpulan ? $pengumpulan->created_at->format('d/m/Y H:i') : null,
                    'is_late' => $pengumpulan ? ($pengumpulan->created_at > $uas->batas_akhir_pengumpulan) : false
                ];
            }

            // Sort berdasarkan nama (sama seperti method absensi)
            usort($daftarMahasiswa, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'id_uas' => $uas->id_uas,
                    'judul' => $uas->judul,
                    'deskripsi' => $uas->deskripsi,
                    'deadline' => $uas->batas_akhir_pengumpulan->format('d/m/Y H:i'),
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
                'message' => 'Terjadi kesalahan saat mengambil data pengumpulan UAS: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Grade UAS submission
     */
    public function gradeUas(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100'
        ]);

        try {
            $pengumpulan = PengumpulanUas::findOrFail($id);
            $pengumpulan->update([
                'nilai' => $request->nilai
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nilai UAS berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan nilai UAS: ' . $e->getMessage()
            ], 500);
        }
    }
}
