<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class BiodataController extends Controller
{
    /**
     * Tampilkan profil mahasiswa yang sedang login
     */
    public function index(): View
    {
        $mahasiswa = Mahasiswa::with(['pengguna', 'programStudi.jenjang', 'kurikulum.semester'])
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->first();

        if (Auth::user()->role !== 'mahasiswa') {
            abort(403, 'Halaman ini hanya untuk mahasiswa.');
        }

        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan.');
        }

        $programStudis = ProgramStudi::with('jenjang')
            ->where('status', 'A')
            ->orderBy('nama_program_studi')
            ->get();

        $kurikulums = $mahasiswa->programStudi
            ? Kurikulum::where('id_program_studi', $mahasiswa->programStudi->id_program_studi)->get()
            : collect();

        $statusOptions = [
            'AKTIF' => 'Aktif',
            'CUTI' => 'Cuti',
            'DO' => 'Drop Out',
            'KELUAR' => 'Keluar',
            'LULUS' => 'Lulus',
            'NONAKTIF' => 'Non Aktif',
        ];

        return view('mahasiswa.biodata.index', compact(
            'mahasiswa',
            'programStudis',
            'kurikulums',
            'statusOptions'
        ));
    }


    /**
     * Update data mahasiswa (hanya data yang diizinkan)
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $mahasiswa = Mahasiswa::with('pengguna')
                ->where('id_pengguna', Auth::user()->id_pengguna)
                ->firstOrFail();

            $validated = $request->validate([
                // Data pribadi
                'jenis_kelamin' => 'nullable|in:L,P',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'nik' => 'nullable|string|size:16|unique:mahasiswa,nik,' . $mahasiswa->id_mahasiswa . ',id_mahasiswa',
                'nisn' => 'nullable|string|max:20',
                'npwp' => 'nullable|string|max:20',
                'agama' => 'nullable|string|max:50',

                // Alamat
                'jalan' => 'nullable|string|max:255',
                'dusun' => 'nullable|string|max:100',
                'rt' => 'nullable|string|max:3',
                'rw' => 'nullable|string|max:3',
                'kelurahan' => 'nullable|string|max:100',
                'kode_pos' => 'nullable|string|max:10',

                // Data Ayah
                'nik_ayah' => 'nullable|string|size:16',
                'nama_ayah' => 'nullable|string|max:150',
                'tempat_lahir_ayah' => 'nullable|string|max:100',
                'tanggal_lahir_ayah' => 'nullable|date',
                'nama_pendidikan_ayah' => 'nullable|string|max:100',
                'nama_pekerjaan_ayah' => 'nullable|string|max:100',
                'nama_penghasilan_ayah' => 'nullable|string|max:100',

                // Data Ibu
                'nik_ibu' => 'nullable|string|size:16',
                'nama_ibu' => 'nullable|string|max:150',
                'tempat_lahir_ibu' => 'nullable|string|max:100',
                'tanggal_lahir_ibu' => 'nullable|date',
                'nama_pendidikan_ibu' => 'nullable|string|max:100',
                'nama_pekerjaan_ibu' => 'nullable|string|max:100',
                'nama_penghasilan_ibu' => 'nullable|string|max:100',

                // Data Wali
                'nama_wali' => 'nullable|string|max:150',
                'tempat_lahir_wali' => 'nullable|string|max:100',
                'tanggal_lahir_wali' => 'nullable|date',
                'nama_pendidikan_wali' => 'nullable|string|max:100',
                'nama_pekerjaan_wali' => 'nullable|string|max:100',
                'nama_penghasilan_wali' => 'nullable|string|max:100',

                // Data kontak
                'email' => 'nullable|email|unique:pengguna,email,' . $mahasiswa->id_pengguna . ',id_pengguna',
                'no_hp' => 'nullable|string|max:20|unique:pengguna,no_hp,' . $mahasiswa->id_pengguna . ',id_pengguna',
            ]);

            DB::beginTransaction();

            // Update data pengguna (hanya email dan no_hp)
            $mahasiswa->pengguna->update([
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'],
            ]);

            // Siapkan data mahasiswa (hapus field yang ada di tabel pengguna)
            $mahasiswaData = $validated;
            unset($mahasiswaData['email'], $mahasiswaData['no_hp']);

            $mahasiswa->update($mahasiswaData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profil Anda berhasil diperbarui.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update password mahasiswa
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $mahasiswa = Mahasiswa::with('pengguna')
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        // Cek password lama
        if (!Hash::check($validated['current_password'], $mahasiswa->pengguna->password)) {
            return back()->withInput()
                ->with('error', 'Password lama tidak sesuai.');
        }

        DB::beginTransaction();
        try {
            $mahasiswa->pengguna->update([
                'password' => Hash::make($validated['new_password'])
            ]);

            DB::commit();

            // FIX: Ganti dari mahasiswa.biodata.index ke mahasiswa.biodata.index
            return redirect()->route('mahasiswa.biodata.index')
                ->with('success', "Password berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->with('error', 'Terjadi kesalahan saat mengubah password: ' . $e->getMessage());
        }
    }

    /**
     * Upload foto profil mahasiswa
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ], [
            'foto.required' => 'Foto harus dipilih',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format gambar harus: JPG, JPEG, PNG',
            'foto.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $mahasiswa = Mahasiswa::with('pengguna')
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        try {
            $oldFoto = $mahasiswa->foto;

            // Handle file upload
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = 'foto_' . $mahasiswa->nim . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Store file dan dapat full path
                $file->storeAs('foto-mahasiswa', $filename, 'public');

                // Delete old file if exists
                if ($oldFoto && Storage::disk('public')->exists('foto-mahasiswa/' . $oldFoto)) {
                    Storage::disk('public')->delete('foto-mahasiswa/' . $oldFoto);
                }

                // ✅ Simpan hanya filename di database, bukan full path
                $mahasiswa->update(['foto' => $filename]);

                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil diperbarui.',
                    'filename' => $filename
                ]);
            }
        } catch (\Exception $e) {
            // Delete new uploaded file if database update fails
            if ($request->hasFile('foto') && isset($filename)) {
                if (Storage::disk('public')->exists('foto-mahasiswa/' . $filename)) {
                    Storage::disk('public')->delete('foto-mahasiswa/' . $filename);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus foto profil mahasiswa
     */
    public function deletePhoto(): JsonResponse
    {
        $mahasiswa = Mahasiswa::with('pengguna')
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        try {
            $oldFoto = $mahasiswa->foto;

            // Delete file if exists
            if ($oldFoto && Storage::disk('public')->exists('foto-mahasiswa/' . $oldFoto)) {
                Storage::disk('public')->delete('foto-mahasiswa/' . $oldFoto);
            }

            // Update database
            $mahasiswa->update(['foto' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kurikulum berdasarkan program studi (AJAX)
     */
    public function getKurikulumByProdi(Request $request): JsonResponse
    {
        $programStudiId = $request->get('program_studi_id');

        if (!$programStudiId) {
            return response()->json([]);
        }

        $kurikulums = Kurikulum::with('semester')
            ->where('id_program_studi', $programStudiId)
            ->orderBy('nama_kurikulum')
            ->get()
            ->map(function ($item) {
                return [
                    'id_kurikulum' => $item->id_kurikulum,
                    'nama_kurikulum' => $item->nama_kurikulum,
                    'semester' => $item->semester->nama_semester,
                    'sks_lulus' => $item->jumlah_sks_lulus,
                    'display_name' => $item->nama_kurikulum . ' (' . $item->semester->nama_semester . ') - ' . $item->jumlah_sks_lulus . ' SKS',
                ];
            });

        return response()->json($kurikulums);
    }

    /**
     * Get program studi untuk AJAX
     */
    public function getProgramStudi(): JsonResponse
    {
        $programStudis = ProgramStudi::with('jenjang')
            ->where('status', 'A')
            ->orderBy('nama_program_studi')
            ->get()
            ->map(function ($item) {
                return [
                    'id_program_studi' => $item->id_program_studi,
                    'kode_program_studi' => $item->kode_program_studi,
                    'nama_program_studi' => $item->nama_program_studi,
                    'jenjang' => $item->jenjang->nama_jenjang_pendidikan,
                    'kode_jenjang' => $item->jenjang->kode_jenjang_pendidikan,
                    'nama_lengkap' => $item->nama_program_studi . ' (' . $item->jenjang->kode_jenjang_pendidikan . ')',
                ];
            });

        return response()->json($programStudis);
    }

    /**
     * Get data profil untuk keperluan lain (bisa digunakan untuk API)
     */
    public function getProfileData(): JsonResponse
    {
        $mahasiswa = Mahasiswa::with(['pengguna', 'programStudi.jenjang', 'kurikulum.semester'])
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->pengguna->nama,
                'program_studi' => $mahasiswa->programStudi->nama_program_studi,
                'jenjang' => $mahasiswa->programStudi->jenjang->nama_jenjang_pendidikan,
                'angkatan' => $mahasiswa->angkatan,
                'status' => $mahasiswa->status_mahasiswa,
                'email' => $mahasiswa->pengguna->email,
                'no_hp' => $mahasiswa->pengguna->no_hp,
                'foto' => $mahasiswa->foto ? asset('storage/foto-mahasiswa/' . $mahasiswa->foto) : null,
            ]
        ]);
    }
}
