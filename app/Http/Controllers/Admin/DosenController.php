<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Pengguna;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DosenController extends Controller
{
    /**
     * Tampilkan halaman daftar dosen dengan filter
     */
    public function index(Request $request): View
    {
        $query = Dosen::with(['pengguna', 'programStudi.jenjang']);

        // Filter berdasarkan program studi
        if ($request->filled('program_studi')) {
            $query->where('id_program_studi', $request->program_studi);
        }

        // Filter berdasarkan status dosen
        if ($request->filled('status_dosen')) {
            $query->where('status_dosen', $request->status_dosen);
        }

        // Filter berdasarkan status kepegawaian
        if ($request->filled('status_kepegawaian')) {
            $query->where('status_kepegawaian', $request->status_kepegawaian);
        }

        // Search berdasarkan NIDN atau nama
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nidn', 'like', "%{$searchTerm}%")
                    ->orWhereHas('pengguna', function ($q2) use ($searchTerm) {
                        $q2->where('nama', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $dosens = $query->orderBy('nidn')->paginate(10);

        // Data untuk filter dropdown
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
            'TETAP' => 'Dosen Tetap',
            'KONTRAK' => 'Dosen Kontrak',
            'HONORER' => 'Dosen Honorer',
        ];

        return view('admin.dosen.index', compact(
            'dosens',
            'programStudis',
            'statusDosenOptions',
            'statusKepegawaianOptions'
        ));
    }

    /**
     * Tampilkan detail dosen dengan semua data (halaman baru)
     */
    public function show(string $id): View
    {
        $dosen = Dosen::with(['pengguna', 'programStudi.jenjang'])
            ->findOrFail($id);

        // Data untuk dropdown di form edit
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
            'TETAP' => 'Dosen Tetap',
            'KONTRAK' => 'Dosen Kontrak',
            'HONORER' => 'Dosen Honorer',
        ];

        return view('admin.dosen.show', compact(
            'dosen',
            'programStudis',
            'statusDosenOptions',
            'statusKepegawaianOptions'
        ));
    }

    /**
     * Simpan dosen baru (hanya data wajib)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nidn' => 'required|string|max:20|unique:dosen,nidn',
            'nama' => 'required|string|max:150',
            'id_program_studi' => 'nullable|exists:program_studi,id_program_studi',
            'status_dosen' => 'required|in:AKTIF,CUTI,KELUAR,NONAKTIF,PENSIUN',
            'status_kepegawaian' => 'required|in:PNS,CPNS,P3K,TETAP,KONTRAK,HONORER',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        DB::beginTransaction();
        try {
            // Buat akun pengguna
            $pengguna = Pengguna::create([
                'id_pengguna' => (string) Str::uuid(),
                'nama' => $validated['nama'],
                'username' => $validated['nidn'], // Username = NIDN
                'password' => Hash::make($validated['nidn']), // Password = NIDN
                'role' => 'dosen',
                'is_active' => true,
            ]);

            // Buat data dosen
            Dosen::create([
                'id_dosen' => (string) Str::uuid(),
                'nidn' => $validated['nidn'],
                'id_program_studi' => $validated['id_program_studi'],
                'status_dosen' => $validated['status_dosen'],
                'status_kepegawaian' => $validated['status_kepegawaian'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'id_pengguna' => $pengguna->id_pengguna,
            ]);

            DB::commit();

            return redirect()->route('dosen.index')
                ->with('success', "Dosen {$validated['nama']} berhasil ditambahkan dengan NIDN: {$validated['nidn']}");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update data wajib dosen (dari modal edit di index)
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $dosen = Dosen::with('pengguna')->findOrFail($id);

        $validated = $request->validate([
            'nidn' => 'required|string|max:20|unique:dosen,nidn,' . $dosen->id_dosen . ',id_dosen',
            'nama' => 'required|string|max:150',
            'id_program_studi' => 'nullable|exists:program_studi,id_program_studi',
            'status_dosen' => 'required|in:AKTIF,CUTI,KELUAR,NONAKTIF,PENSIUN',
            'status_kepegawaian' => 'required|in:PNS,CPNS,P3K,TETAP,KONTRAK,HONORER',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        DB::beginTransaction();
        try {
            // Update data pengguna
            $dosen->pengguna->update([
                'nama' => $validated['nama'],
                'username' => $validated['nidn'], // Username mengikuti NIDN
            ]);

            // Update data dosen
            $dosen->update([
                'nidn' => $validated['nidn'],
                'id_program_studi' => $validated['id_program_studi'],
                'status_dosen' => $validated['status_dosen'],
                'status_kepegawaian' => $validated['status_kepegawaian'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
            ]);

            DB::commit();

            return redirect()->route('dosen.index')
                ->with('success', "Data dosen {$validated['nama']} berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update detail lengkap dosen (dari halaman detail)
     */
    public function updateDetail(Request $request, string $id): RedirectResponse
    {
        $dosen = Dosen::with('pengguna')->findOrFail($id);

        $validated = $request->validate([
            // Data wajib
            'nidn' => 'required|string|max:20|unique:dosen,nidn,' . $dosen->id_dosen . ',id_dosen',
            'nama' => 'required|string|max:150',
            'id_program_studi' => 'nullable|exists:program_studi,id_program_studi',
            'status_dosen' => 'required|in:AKTIF,CUTI,KELUAR,NONAKTIF,PENSIUN',
            'status_kepegawaian' => 'required|in:PNS,CPNS,P3K,TETAP,KONTRAK,HONORER',
            'jenis_kelamin' => 'required|in:L,P',

            // Data pribadi
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

            // Data akun
            'email' => 'nullable|email|unique:pengguna,email,' . $dosen->id_pengguna . ',id_pengguna',
            'no_hp' => 'nullable|string|max:20|unique:pengguna,no_hp,' . $dosen->id_pengguna . ',id_pengguna',

            // Pembimbing Akademik
            'total_kuota_pa' => 'nullable|integer|min:0|max:100',
            'total_kuota_pa_terisi' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update data pengguna
            $dosen->pengguna->update([
                'nama' => $validated['nama'],
                'username' => $validated['nidn'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'],
            ]);

            // Siapkan data dosen (hapus field yang ada di tabel pengguna)
            $dosenData = $validated;
            unset($dosenData['nama'], $dosenData['email'], $dosenData['no_hp']);

            $dosen->update($dosenData);

            DB::commit();

            return redirect()->route('dosen.show', $id)
                ->with('success', "Data dosen {$validated['nama']} berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus dosen dan akun terkait
     */
    public function destroy(string $id): RedirectResponse
    {
        $dosen = Dosen::with('pengguna')->findOrFail($id);
        $dosenName = $dosen->pengguna->nama;
        $nidn = $dosen->nidn;

        DB::beginTransaction();
        try {
            // Hapus data pengguna (akan menghapus dosen juga karena cascade)
            $dosen->pengguna->delete();
            $dosen->delete();

            DB::commit();

            return redirect()->route('dosen.index')
                ->with('success', "Dosen \"{$dosenName}\" (NIDN: {$nidn}) berhasil dihapus.");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('dosen.index')
                ->with('error', 'Terjadi kesalahan saat menghapus dosen: ' . $e->getMessage());
        }
    }

    /**
     * Reset password dosen ke NIDN (dari halaman detail)
     */
    public function resetPassword(string $id): RedirectResponse
    {
        $dosen = Dosen::with('pengguna')->findOrFail($id);

        DB::beginTransaction();
        try {
            $dosen->pengguna->update([
                'password' => Hash::make($dosen->nidn)
            ]);

            DB::commit();

            return redirect()->route('dosen.show', $id)
                ->with('success', "Password dosen {$dosen->pengguna->nama} berhasil direset ke NIDN.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->with('error', 'Terjadi kesalahan saat reset password: ' . $e->getMessage());
        }
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
     * Export template Excel untuk import
     */
    public function exportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headers = [
            'NIDN', 'Nama', 'Jenis Kelamin (L/P)', 'Tempat Lahir', 'Tanggal Lahir (YYYY-MM-DD)',
            'Status Dosen (AKTIF/CUTI/KELUAR/NONAKTIF/PENSIUN)', 'Status Kepegawaian (PNS/CPNS/P3K/TETAP/KONTRAK/HONORER)',
            'Gelar Depan', 'Gelar Belakang', 'NIK (16 digit)', 'NPWP', 'Email', 'No HP',
            'Kode Program Studi',
            'Jalan', 'Dusun', 'RT', 'RW', 'Kelurahan', 'Kode Pos'
        ];

        // Set header
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }

        // Data contoh
        $exampleData = [
            '0123456789', 'Dr. John Doe', 'L', 'Jakarta', '1980-01-01',
            'AKTIF', 'TETAP', 'Dr.', 'M.T.', '1234567890123456', '123456789012345',
            'john.doe@example.com', '081234567890', 'TI',
            'Jl. Sudirman No. 1', 'Kebon Jeruk', '01', '02', 'Kebon Jeruk', '12345'
        ];

        $column = 'A';
        foreach ($exampleData as $data) {
            $sheet->setCellValue($column . '2', $data);
            $column++;
        }

        $filename = 'template_import_dosen_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Import dosen dari Excel
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
            $errors = [];

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 karena index mulai dari 0 dan ada header

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    // Validasi data wajib
                    if (empty($row[0])) {
                        $errors[] = "Baris {$rowNumber}: NIDN tidak boleh kosong";
                        continue;
                    }

                    if (empty($row[1])) {
                        $errors[] = "Baris {$rowNumber}: Nama tidak boleh kosong";
                        continue;
                    }

                    if (empty($row[2]) || !in_array($row[2], ['L', 'P'])) {
                        $errors[] = "Baris {$rowNumber}: Jenis kelamin harus L atau P";
                        continue;
                    }

                    // Cek program studi berdasarkan kode (jika diisi)
                    $programStudi = null;
                    if (!empty($row[13])) {
                        $programStudi = ProgramStudi::where('kode_program_studi', $row[13])->first();
                        if (!$programStudi) {
                            $errors[] = "Baris {$rowNumber}: Kode program studi '{$row[13]}' tidak ditemukan";
                            continue;
                        }
                    }

                    // Cek duplikasi NIDN
                    if (Dosen::where('nidn', $row[0])->exists()) {
                        $errors[] = "Baris {$rowNumber}: NIDN '{$row[0]}' sudah terdaftar";
                        continue;
                    }

                    // Buat akun pengguna
                    $pengguna = Pengguna::create([
                        'id_pengguna' => (string) Str::uuid(),
                        'nama' => $row[1],
                        'username' => $row[0],
                        'password' => Hash::make($row[0]),
                        'email' => !empty($row[11]) ? $row[11] : null,
                        'no_hp' => !empty($row[12]) ? $row[12] : null,
                        'role' => 'dosen',
                        'is_active' => true,
                    ]);

                    // Buat data dosen
                    Dosen::create([
                        'id_dosen' => (string) Str::uuid(),
                        'nidn' => $row[0],
                        'jenis_kelamin' => $row[2],
                        'tempat_lahir' => !empty($row[3]) ? $row[3] : null,
                        'tanggal_lahir' => !empty($row[4]) ? date('Y-m-d', strtotime($row[4])) : null,
                        'status_dosen' => !empty($row[5]) && in_array($row[5], ['AKTIF', 'CUTI', 'KELUAR', 'NONAKTIF', 'PENSIUN']) ? $row[5] : 'AKTIF',
                        'status_kepegawaian' => !empty($row[6]) && in_array($row[6], ['PNS', 'CPNS', 'P3K', 'TETAP', 'KONTRAK', 'HONORER']) ? $row[6] : 'TETAP',
                        'gelar_depan' => !empty($row[7]) ? $row[7] : null,
                        'gelar_belakang' => !empty($row[8]) ? $row[8] : null,
                        'nik' => !empty($row[9]) && strlen($row[9]) == 16 ? $row[9] : null,
                        'npwp' => !empty($row[10]) ? $row[10] : null,
                        'id_program_studi' => $programStudi ? $programStudi->id_program_studi : null,
                        'id_pengguna' => $pengguna->id_pengguna,

                        // Alamat
                        'jalan' => !empty($row[14]) ? $row[14] : null,
                        'dusun' => !empty($row[15]) ? $row[15] : null,
                        'rt' => !empty($row[16]) ? $row[16] : null,
                        'rw' => !empty($row[17]) ? $row[17] : null,
                        'kelurahan' => !empty($row[18]) ? $row[18] : null,
                        'kode_pos' => !empty($row[19]) ? $row[19] : null,
                    ]);

                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            if (empty($errors)) {
                DB::commit();
                return redirect()->route('dosen.index')
                    ->with('success', "Berhasil mengimpor {$importedCount} data dosen.");
            } else {
                DB::rollback();
                $errorMessage = "Import gagal. Kesalahan:\n" . implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $errorMessage .= "\ndan " . (count($errors) - 10) . " kesalahan lainnya...";
                }
                return redirect()->route('dosen.index')
                    ->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('dosen.index')
                ->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }

    /**
     * Upload foto dosen
     */
    public function uploadPhoto(Request $request, string $id)
    {
        $dosen = Dosen::findOrFail($id);

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // 2MB Max
        ]);

        try {
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');

                // Generate filename
                $filename = 'dosen_' . $dosen->nidn . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Store file
                $path = $file->storeAs('public/foto-dosen', $filename);

                // Delete old photo if exists
                if ($dosen->foto) {
                    Storage::delete('public/foto-dosen/' . $dosen->foto);
                }

                // Update dosen record
                $dosen->update([
                    'foto' => $filename
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Foto berhasil diupload',
                    'photo_url' => asset('storage/foto-dosen/' . $filename)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading photo: ' . $e->getMessage()
            ], 500);
        }
    }
}
