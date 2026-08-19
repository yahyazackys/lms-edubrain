<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\ProgramStudi;
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
     * Tampilkan profil dosen yang sedang login
     */
    public function index(): View
    {
        $dosen = Dosen::with(['pengguna', 'programStudi.jenjang'])
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->first();

        if (Auth::user()->role !== 'dosen') {
            abort(403, 'Halaman ini hanya untuk dosen.');
        }

        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan.');
        }

        $programStudis = ProgramStudi::with('jenjang')
            ->where('status', 'A')
            ->orderBy('nama_program_studi')
            ->get();

        $statusDosenOptions = [
            'AKTIF' => 'Aktif',
            'CUTI' => 'Cuti',
            'KELUAR' => 'Keluar',
            'NONAKTIF' => 'Non Aktif',
            'PENSIUN' => 'Pensiun',
        ];

        $statusKepegawaianOptions = [
            'PNS' => 'PNS',
            'CPNS' => 'CPNS',
            'P3K' => 'P3K',
            'TETAP' => 'Dosen Tetap Non-PNS',
            'KONTRAK' => 'Dosen Kontrak',
            'HONORER' => 'Dosen Honorer',
        ];

        return view('dosen.biodata.index', compact(
            'dosen',
            'programStudis',
            'statusDosenOptions',
            'statusKepegawaianOptions'
        ));
    }

    /**
     * Update data dosen (hanya data yang diizinkan)
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $dosen = Dosen::with('pengguna')
                ->where('id_pengguna', Auth::user()->id_pengguna)
                ->firstOrFail();

            $validated = $request->validate([
                // Data pribadi
                'jenis_kelamin' => 'nullable|in:L,P',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'gelar_depan' => 'nullable|string|max:50',
                'gelar_belakang' => 'nullable|string|max:50',
                'nik' => 'nullable|string|size:16|unique:dosen,nik,' . $dosen->id_dosen . ',id_dosen',
                'npwp' => 'nullable|string|max:20',

                // Alamat
                'jalan' => 'nullable|string|max:255',
                'dusun' => 'nullable|string|max:100',
                'rt' => 'nullable|string|max:3',
                'rw' => 'nullable|string|max:3',
                'kelurahan' => 'nullable|string|max:100',
                'kode_pos' => 'nullable|string|max:10',

                // Data kontak
                'email' => 'nullable|email|unique:pengguna,email,' . $dosen->id_pengguna . ',id_pengguna',
                'no_hp' => 'nullable|string|max:20|unique:pengguna,no_hp,' . $dosen->id_pengguna . ',id_pengguna',
            ]);

            DB::beginTransaction();

            // Update data pengguna (hanya email dan no_hp)
            $dosen->pengguna->update([
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'],
            ]);

            // Siapkan data dosen (hapus field yang ada di tabel pengguna)
            $dosenData = $validated;
            unset($dosenData['email'], $dosenData['no_hp']);

            $dosen->update($dosenData);

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
     * Update password dosen
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $dosen = Dosen::with('pengguna')
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        // Cek password lama
        if (!Hash::check($validated['current_password'], $dosen->pengguna->password)) {
            return back()->withInput()
                ->with('error', 'Password lama tidak sesuai.');
        }

        DB::beginTransaction();
        try {
            $dosen->pengguna->update([
                'password' => Hash::make($validated['new_password'])
            ]);

            DB::commit();

            return redirect()->route('dosen.biodata.index')
                ->with('success', "Password berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->with('error', 'Terjadi kesalahan saat mengubah password: ' . $e->getMessage());
        }
    }

    /**
     * Upload foto profil dosen
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

        $dosen = Dosen::with('pengguna')
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        try {
            $oldFoto = $dosen->foto;

            // Handle file upload
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = 'foto_' . $dosen->nidn . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Store file
                $file->storeAs('foto-dosen', $filename, 'public');

                // Delete old file if exists
                if ($oldFoto && Storage::disk('public')->exists('foto-dosen/' . $oldFoto)) {
                    Storage::disk('public')->delete('foto-dosen/' . $oldFoto);
                }

                // Simpan hanya filename di database
                $dosen->update(['foto' => $filename]);

                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil diperbarui.',
                    'filename' => $filename
                ]);
            }
        } catch (\Exception $e) {
            // Delete new uploaded file if database update fails
            if ($request->hasFile('foto') && isset($filename)) {
                if (Storage::disk('public')->exists('foto-dosen/' . $filename)) {
                    Storage::disk('public')->delete('foto-dosen/' . $filename);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus foto profil dosen
     */
    public function deletePhoto(): JsonResponse
    {
        $dosen = Dosen::with('pengguna')
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        try {
            $oldFoto = $dosen->foto;

            // Delete file if exists
            if ($oldFoto && Storage::disk('public')->exists('foto-dosen/' . $oldFoto)) {
                Storage::disk('public')->delete('foto-dosen/' . $oldFoto);
            }

            // Update database
            $dosen->update(['foto' => null]);

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
     * Get data profil untuk keperluan lain (bisa digunakan untuk API)
     */
    public function getProfileData(): JsonResponse
    {
        $dosen = Dosen::with(['pengguna', 'programStudi.jenjang'])
            ->where('id_pengguna', Auth::user()->id_pengguna)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'nidn' => $dosen->nidn,
                'nama' => $dosen->pengguna->nama,
                'gelar_depan' => $dosen->gelar_depan,
                'gelar_belakang' => $dosen->gelar_belakang,
                'program_studi' => $dosen->programStudi ? $dosen->programStudi->nama_program_studi : null,
                'status_dosen' => $dosen->status_dosen,
                'status_kepegawaian' => $dosen->status_kepegawaian,
                'email' => $dosen->pengguna->email,
                'no_hp' => $dosen->pengguna->no_hp,
                'foto' => $dosen->foto ? asset('storage/foto-dosen/' . $dosen->foto) : null,
            ]
        ]);
    }
}
