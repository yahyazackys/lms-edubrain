<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Pengguna;
use App\Models\ProgramStudi;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MahasiswaController extends Controller
{
    /**
     * Tampilkan halaman daftar mahasiswa dengan filter
     */
    public function index(Request $request): View
    {
        $query = Mahasiswa::with(['pengguna', 'programStudi.jenjang', 'kurikulum']);

        // Filter berdasarkan program studi
        if ($request->filled('program_studi')) {
            $query->where('id_program_studi', $request->program_studi);
        }

        // Filter berdasarkan angkatan
        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->angkatan);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_mahasiswa', $request->status);
        }

        // Search berdasarkan NIM atau nama
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nim', 'like', "%{$searchTerm}%")
                    ->orWhereHas('pengguna', function ($q2) use ($searchTerm) {
                        $q2->where('nama', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $mahasiswas = $query->orderBy('nim')->get();

        // Data untuk filter dropdown
        $programStudis = ProgramStudi::with('jenjang')
            ->where('status', 'A')
            ->orderBy('nama_program_studi')
            ->get();

        $angkatans = Mahasiswa::select('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        $statusOptions = [
            'AKTIF' => 'Aktif',
            'CUTI' => 'Cuti',
            'DO' => 'Drop Out',
            'KELUAR' => 'Keluar',
            'LULUS' => 'Lulus',
            'NONAKTIF' => 'Non Aktif',
        ];

        return view('admin.mahasiswa.index', compact(
            'mahasiswas',
            'programStudis',
            'angkatans',
            'statusOptions'
        ));
    }

    /**
     * Tampilkan detail mahasiswa dengan semua data (halaman baru)
     */
    public function show(string $id): View
    {
        $mahasiswa = Mahasiswa::with(['pengguna', 'programStudi.jenjang', 'kurikulum.semester'])
            ->findOrFail($id);

        // Data untuk dropdown di form edit
        $programStudis = ProgramStudi::with('jenjang')
            ->where('status', 'A')
            ->orderBy('nama_program_studi')
            ->get();

        $kurikulums = Kurikulum::where('id_program_studi', $mahasiswa->programStudi->id_program_studi)
            ->get();

        if (!$kurikulums) {
            return back()->withInput()
                ->with('error', 'Kurikulum yang dipilih tidak sesuai dengan program studi.');
        }

        $statusOptions = [
            'AKTIF' => 'Aktif',
            'CUTI' => 'Cuti',
            'DO' => 'Drop Out',
            'KELUAR' => 'Keluar',
            'LULUS' => 'Lulus',
            'NONAKTIF' => 'Non Aktif',
        ];

        return view('admin.mahasiswa.show', compact(
            'mahasiswa',
            'programStudis',
            'kurikulums',
            'statusOptions'
        ));
    }

    /**
     * Simpan mahasiswa baru (hanya data wajib)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:20|unique:mahasiswa,nim',
            'nama' => 'required|string|max:150',
            'id_program_studi' => 'required|exists:program_studi,id_program_studi',
            'id_kurikulum' => 'required|exists:kurikulum,id_kurikulum',
            'angkatan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'status_mahasiswa' => 'required|in:AKTIF,CUTI,DO,KELUAR,LULUS,NONAKTIF',
        ]);

        // Validasi kurikulum harus sesuai dengan program studi
        $kurikulum = Kurikulum::where('id_kurikulum', $validated['id_kurikulum'])
            ->where('id_program_studi', $validated['id_program_studi'])
            ->first();

        if (!$kurikulum) {
            return back()->withInput()
                ->with('error', 'Kurikulum yang dipilih tidak sesuai dengan program studi.');
        }

        DB::beginTransaction();
        try {
            // Buat akun pengguna
            $pengguna = Pengguna::create([
                'id_pengguna' => (string) Str::uuid(),
                'nama' => $validated['nama'],
                'username' => $validated['nim'], // Username = NIM
                'password' => Hash::make($validated['nim']), // Password = NIM
                'role' => 'mahasiswa',
                'is_active' => true,
            ]);

            // Buat data mahasiswa
            Mahasiswa::create([
                'id_mahasiswa' => (string) Str::uuid(),
                'nim' => $validated['nim'],
                'id_program_studi' => $validated['id_program_studi'],
                'id_kurikulum' => $validated['id_kurikulum'],
                'angkatan' => $validated['angkatan'],
                'status_mahasiswa' => $validated['status_mahasiswa'],
                'id_pengguna' => $pengguna->id_pengguna,
            ]);

            DB::commit();

            return redirect()->route('mahasiswa.index')
                ->with('success', "Mahasiswa {$validated['nama']} berhasil ditambahkan dengan NIM: {$validated['nim']}");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update data wajib mahasiswa (dari modal edit di index)
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $mahasiswa = Mahasiswa::with('pengguna')->findOrFail($id);

        $validated = $request->validate([
            'nim' => 'required|string|max:20|unique:mahasiswa,nim,' . $mahasiswa->id_mahasiswa . ',id_mahasiswa',
            'nama' => 'required|string|max:150',
            'id_program_studi' => 'required|exists:program_studi,id_program_studi',
            'id_kurikulum' => 'required|exists:kurikulum,id_kurikulum',
            'angkatan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'status_mahasiswa' => 'required|in:AKTIF,CUTI,DO,KELUAR,LULUS,NONAKTIF',
        ]);

        // Validasi kurikulum harus sesuai dengan program studi
        $kurikulum = Kurikulum::where('id_kurikulum', $validated['id_kurikulum'])
            ->where('id_program_studi', $validated['id_program_studi'])
            ->first();

        if (!$kurikulum) {
            return back()->withInput()
                ->with('error', 'Kurikulum yang dipilih tidak sesuai dengan program studi.');
        }

        DB::beginTransaction();
        try {
            // Update data pengguna
            $mahasiswa->pengguna->update([
                'nama' => $validated['nama'],
                'username' => $validated['nim'], // Username mengikuti NIM
            ]);

            // Update data mahasiswa
            $mahasiswa->update([
                'nim' => $validated['nim'],
                'id_program_studi' => $validated['id_program_studi'],
                'id_kurikulum' => $validated['id_kurikulum'],
                'angkatan' => $validated['angkatan'],
                'status_mahasiswa' => $validated['status_mahasiswa'],
            ]);

            DB::commit();

            return redirect()->route('mahasiswa.index')
                ->with('success', "Data mahasiswa {$validated['nama']} berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update detail lengkap mahasiswa (dari halaman detail)
     */
    public function updateDetail(Request $request, string $id): RedirectResponse
    {
        $mahasiswa = Mahasiswa::with('pengguna')->findOrFail($id);

        $validated = $request->validate([
            // Data wajib
            'nim' => 'required|string|max:20|unique:mahasiswa,nim,' . $mahasiswa->id_mahasiswa . ',id_mahasiswa',
            'nama' => 'required|string|max:150',
            'id_program_studi' => 'required|exists:program_studi,id_program_studi',
            'id_kurikulum' => 'required|exists:kurikulum,id_kurikulum',
            'angkatan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'status_mahasiswa' => 'required|in:AKTIF,CUTI,DO,KELUAR,LULUS,NONAKTIF',

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

            // Data akun
            'email' => 'nullable|email|unique:pengguna,email,' . $mahasiswa->id_pengguna . ',id_pengguna',
            'no_hp' => 'nullable|string|max:20|unique:pengguna,no_hp,' . $mahasiswa->id_pengguna . ',id_pengguna',
        ]);

        // Validasi kurikulum harus sesuai dengan program studi
        $kurikulum = Kurikulum::where('id_kurikulum', $validated['id_kurikulum'])
            ->where('id_program_studi', $validated['id_program_studi'])
            ->first();

        if (!$kurikulum) {
            return back()->withInput()
                ->with('error', 'Kurikulum yang dipilih tidak sesuai dengan program studi.');
        }

        DB::beginTransaction();
        try {
            // Update data pengguna
            $mahasiswa->pengguna->update([
                'nama' => $validated['nama'],
                'username' => $validated['nim'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'],
            ]);

            // Siapkan data mahasiswa (hapus field yang ada di tabel pengguna)
            $mahasiswaData = $validated;
            unset($mahasiswaData['nama'], $mahasiswaData['email'], $mahasiswaData['no_hp']);

            $mahasiswa->update($mahasiswaData);

            DB::commit();

            return redirect()->route('mahasiswa.show', $id)
                ->with('success', "Data mahasiswa {$validated['nama']} berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Upload foto profil mahasiswa (admin)
     */
    public function uploadPhoto(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ], [
            'photo.required' => 'Foto harus dipilih',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format gambar harus: JPG, JPEG, PNG',
            'photo.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $mahasiswa = Mahasiswa::with('pengguna')->findOrFail($id);

        try {
            $oldFoto = $mahasiswa->foto;

            // Handle file upload
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = 'foto_' . $mahasiswa->nim . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Store file dan dapat full path
                $file->storeAs('foto-mahasiswa', $filename, 'public');

                // Delete old file if exists
                if ($oldFoto && Storage::disk('public')->exists('foto-mahasiswa/' . $oldFoto)) {
                    Storage::disk('public')->delete('foto-mahasiswa/' . $oldFoto);
                }

                // Simpan hanya filename di database, bukan full path
                $mahasiswa->update(['foto' => $filename]);

                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil diperbarui.',
                    'filename' => $filename
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diupload'
            ], 400);
        } catch (\Exception $e) {
            // Delete new uploaded file if database update fails
            if ($request->hasFile('photo') && isset($filename)) {
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
     * Hapus foto profil mahasiswa (admin)
     */
    public function deletePhoto(string $id): JsonResponse
    {
        $mahasiswa = Mahasiswa::with('pengguna')->findOrFail($id);

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
     * Hapus mahasiswa dan akun terkait
     */
    public function destroy(string $id): RedirectResponse
    {
        $mahasiswa = Mahasiswa::with('pengguna')->findOrFail($id);
        $mahasiswaName = $mahasiswa->pengguna->nama;
        $nim = $mahasiswa->nim;

        DB::beginTransaction();
        try {
            // Hapus data pengguna (akan menghapus mahasiswa juga karena cascade)
            $mahasiswa->pengguna->delete();
            $mahasiswa->delete();

            DB::commit();

            return redirect()->route('mahasiswa.index')
                ->with('success', "Mahasiswa \"{$mahasiswaName}\" (NIM: {$nim}) berhasil dihapus.");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mahasiswa.index')
                ->with('error', 'Terjadi kesalahan saat menghapus mahasiswa: ' . $e->getMessage());
        }
    }

    /**
     * Reset password mahasiswa ke NIM (dari halaman detail)
     */
    public function resetPassword(string $id): RedirectResponse
    {
        $mahasiswa = Mahasiswa::with('pengguna')->findOrFail($id);

        DB::beginTransaction();
        try {
            $mahasiswa->pengguna->update([
                'password' => Hash::make($mahasiswa->nim)
            ]);

            DB::commit();

            return redirect()->route('mahasiswa.show', $id)
                ->with('success', "Password mahasiswa {$mahasiswa->pengguna->nama} berhasil direset ke NIM.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->with('error', 'Terjadi kesalahan saat reset password: ' . $e->getMessage());
        }
    }

    /**
     * Get kurikulum berdasarkan program studi (AJAX)
     */
    public function getKurikulumByProdi(Request $request)
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
    public function getProgramStudi()
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
     * Export template Excel untuk import (hanya data wajib)
     */
    public function exportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom (hanya data wajib)
        $headers = [
            'NIM',
            'Nama Lengkap',
            'Kode Program Studi',
            'Nama Kurikulum',
            'Angkatan',
            'Status (AKTIF/CUTI/DO/KELUAR/LULUS/NONAKTIF)'
        ];

        // Set header
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $sheet->getStyle($column . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }

        // ✅ KUNCI: Format seluruh kolom A (NIM) sebagai TEXT sebelum isi data
        $sheet->getStyle('A:A')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Data contoh dengan berbagai format NIM
        $exampleData = [
            ['003001', 'John Doe', 'TI', 'Kurikulum 2023', '2023', 'AKTIF'],
            ['20230001', 'Jane Smith', 'SI', 'Kurikulum 2023', '2023', 'AKTIF'],
            ['00456', 'Bob Wilson', 'IF', 'Kurikulum 2024', '2024', 'CUTI'],
        ];

        $row = 2;
        foreach ($exampleData as $data) {
            // Set NIM sebagai string explicit
            $sheet->setCellValueExplicit('A' . $row, $data[0], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            // Kolom lainnya normal
            $column = 'B';
            for ($i = 1; $i < count($data); $i++) {
                $sheet->setCellValue($column . $row, $data[$i]);
                $column++;
            }
            $row++;
        }

        // Add notes
        $noteStartRow = $row + 1;
        $sheet->setCellValue('A' . $noteStartRow, 'CATATAN PENTING:');
        $sheet->getStyle('A' . $noteStartRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $noteStartRow)->getFont()->setSize(12);
        $sheet->getStyle('A' . $noteStartRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF00');

        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;

        $sheet->setCellValue('A' . $noteStartRow, '1. NIM: Nomor Induk Mahasiswa - format bebas (wajib diisi, unik)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   ✓ Kolom NIM sudah diformat sebagai TEXT, langsung ketik saja: 003001, 00456, dll');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   ✓ JANGAN ubah format kolom NIM - biarkan sebagai TEXT');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   ✓ Jika tetap berubah jadi angka, ketik dengan apostrof di depan: \'003001');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '2. Nama Lengkap: Nama lengkap mahasiswa (wajib diisi)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '3. Kode Program Studi: Kode prodi sesuai data di sistem, contoh: TI, SI, IF (wajib diisi)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '4. Nama Kurikulum: Nama kurikulum sesuai program studi (wajib diisi)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Contoh: Kurikulum 2023, Kurikulum 2024');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '5. Angkatan: Tahun angkatan dalam format 4 digit, contoh: 2023, 2024');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '6. Status: Pilihan - AKTIF, CUTI, DO, KELUAR, LULUS, NONAKTIF (huruf kapital)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '7. Username dan password akan dibuat otomatis sesuai dengan NIM');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '8. Pastikan NIM tidak duplikat dengan data yang sudah ada');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '9. Hapus 3 baris contoh di atas sebelum mengisi data sebenarnya');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, 'JANGAN LUPA HAPUS INSTRUKSI KETIKA IMPORT DATA');
        $sheet->getStyle('A' . $noteStartRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $noteStartRow)->getFont()->setSize(12);
        $sheet->getStyle('A' . $noteStartRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF00');

        // Style untuk notes
        $sheet->getStyle('A' . ($row + 1) . ':A' . $noteStartRow)->getAlignment()->setWrapText(true);
        $sheet->getColumnDimension('A')->setWidth(100);

        $filename = 'template_import_mahasiswa_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export data mahasiswa ke Excel (hanya data wajib)
     */
    public function export()
    {
        $mahasiswas = Mahasiswa::with(['pengguna', 'programStudi', 'kurikulum'])
            ->orderBy('nim')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = ['NIM', 'Nama Lengkap', 'Kode Program Studi', 'Nama Kurikulum', 'Angkatan', 'Status'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $sheet->getStyle($column . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE0E0E0');
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }

        // ✅ Format kolom NIM sebagai TEXT
        $sheet->getStyle('A:A')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Data
        $row = 2;
        foreach ($mahasiswas as $mahasiswa) {
            // Set NIM sebagai string explicit
            $sheet->setCellValueExplicit('A' . $row, $mahasiswa->nim, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            // Kolom lainnya
            $sheet->setCellValue('B' . $row, $mahasiswa->pengguna->nama);
            $sheet->setCellValue('C' . $row, $mahasiswa->programStudi->kode_program_studi ?? '');
            $sheet->setCellValue('D' . $row, $mahasiswa->kurikulum->nama_kurikulum ?? '');
            $sheet->setCellValue('E' . $row, $mahasiswa->angkatan);
            $sheet->setCellValue('F' . $row, $mahasiswa->status_mahasiswa);
            $row++;
        }

        $filename = 'data_mahasiswa_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Import mahasiswa dari Excel (hanya data wajib)
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            array_shift($rows);

            $importedCount = 0;
            $skippedCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    // Validasi data wajib
                    $nim = trim((string)($row[0] ?? ''));  // Cast ke string untuk preserve format apapun
                    $nama = trim($row[1] ?? '');
                    $kodeProdi = trim($row[2] ?? '');
                    $namaKurikulum = trim($row[3] ?? '');
                    $angkatan = trim($row[4] ?? '');
                    $status = strtoupper(trim($row[5] ?? ''));

                    // Validasi NIM - hanya cek kosong dan duplikasi, TIDAK ada validasi format
                    if (empty($nim)) {
                        $errors[] = "Baris {$rowNumber}: NIM tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi Nama
                    if (empty($nama)) {
                        $errors[] = "Baris {$rowNumber}: Nama tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi Status
                    if (!in_array($status, ['AKTIF', 'CUTI', 'DO', 'KELUAR', 'LULUS', 'NONAKTIF'])) {
                        $errors[] = "Baris {$rowNumber}: Status tidak valid (harus: AKTIF, CUTI, DO, KELUAR, LULUS, atau NONAKTIF)";
                        $skippedCount++;
                        continue;
                    }

                    // Cek program studi berdasarkan kode
                    $programStudi = ProgramStudi::where('kode_program_studi', $kodeProdi)
                        ->where('status', 'A')
                        ->first();

                    if (!$programStudi) {
                        $errors[] = "Baris {$rowNumber}: Kode program studi '{$kodeProdi}' tidak ditemukan";
                        $skippedCount++;
                        continue;
                    }

                    // Cek kurikulum berdasarkan nama dan program studi
                    $kurikulum = Kurikulum::where('nama_kurikulum', $namaKurikulum)
                        ->where('id_program_studi', $programStudi->id_program_studi)
                        ->first();

                    if (!$kurikulum) {
                        $errors[] = "Baris {$rowNumber}: Kurikulum '{$namaKurikulum}' tidak ditemukan untuk program studi {$kodeProdi}";
                        $skippedCount++;
                        continue;
                    }

                    // Cek duplikasi NIM
                    if (Mahasiswa::where('nim', $nim)->exists()) {
                        $errors[] = "Baris {$rowNumber}: NIM '{$nim}' sudah terdaftar";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi angkatan
                    if (empty($angkatan) || !is_numeric($angkatan)) {
                        $angkatan = date('Y');
                    }

                    // Buat akun pengguna
                    $pengguna = Pengguna::create([
                        'id_pengguna' => (string) Str::uuid(),
                        'nama' => $nama,
                        'username' => $nim,
                        'password' => Hash::make($nim),
                        'role' => 'mahasiswa',
                        'is_active' => true,
                    ]);

                    // Buat data mahasiswa
                    Mahasiswa::create([
                        'id_mahasiswa' => (string) Str::uuid(),
                        'nim' => $nim,
                        'id_program_studi' => $programStudi->id_program_studi,
                        'id_kurikulum' => $kurikulum->id_kurikulum,
                        'angkatan' => $angkatan,
                        'status_mahasiswa' => $status,
                        'id_pengguna' => $pengguna->id_pengguna,
                    ]);

                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                    $skippedCount++;
                }
            }

            DB::commit();

            // Handle hasil import
            if ($importedCount > 0 && empty($errors)) {
                return redirect()->route('mahasiswa.index')
                    ->with('success', "Berhasil mengimpor {$importedCount} data mahasiswa.");
            }

            if ($importedCount > 0 && !empty($errors)) {
                $request->session()->flash('import_errors', $errors);

                return redirect()->route('mahasiswa.index')
                    ->with('warning', "Berhasil mengimpor {$importedCount} data mahasiswa. {$skippedCount} data dilewati. Klik untuk melihat detail error.");
            }

            if ($importedCount === 0 && !empty($errors)) {
                $request->session()->flash('import_errors', $errors);

                return redirect()->route('mahasiswa.index')
                    ->with('error', 'Import gagal. Tidak ada data yang berhasil diimpor. Klik untuk melihat detail error.');
            }

            return redirect()->route('mahasiswa.index')
                ->with('info', 'Tidak ada data yang diimpor. File mungkin kosong.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mahasiswa.index')
                ->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }
}
