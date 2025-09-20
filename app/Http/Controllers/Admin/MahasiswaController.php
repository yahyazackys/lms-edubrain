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

        $mahasiswas = $query->orderBy('nim')->paginate(10);

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
     * Export template Excel untuk import
     */
    public function exportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headers = [
            'NIM', 'Nama', 'Jenis Kelamin (L/P)', 'Tempat Lahir', 'Tanggal Lahir (YYYY-MM-DD)',
            'Angkatan', 'Status (AKTIF/CUTI/DO/KELUAR/LULUS/NONAKTIF)',
            'NIK (16 digit)', 'NISN', 'NPWP', 'Agama', 'Email', 'No HP',
            'Kode Program Studi', 'Nama Kurikulum',
            'Jalan', 'Dusun', 'RT', 'RW', 'Kelurahan', 'Kode Pos',

            // Data Ayah
            'NIK Ayah', 'Nama Ayah', 'Tempat Lahir Ayah', 'Tanggal Lahir Ayah (YYYY-MM-DD)',
            'Pendidikan Ayah', 'Pekerjaan Ayah', 'Penghasilan Ayah',

            // Data Ibu  
            'NIK Ibu', 'Nama Ibu', 'Tempat Lahir Ibu', 'Tanggal Lahir Ibu (YYYY-MM-DD)',
            'Pendidikan Ibu', 'Pekerjaan Ibu', 'Penghasilan Ibu',

            // Data Wali
            'Nama Wali', 'Tempat Lahir Wali', 'Tanggal Lahir Wali (YYYY-MM-DD)',
            'Pendidikan Wali', 'Pekerjaan Wali', 'Penghasilan Wali'
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
            '20230001', 'John Doe', 'L', 'Jakarta', '2000-01-01',
            '2023', 'AKTIF', '1234567890123456', '1234567890', '123456789012345',
            'Islam', 'john@example.com', '081234567890',
            'TI', 'Kurikulum 2023',
            'Jl. Sudirman No. 1', 'Kebon Jeruk', '01', '02', 'Kebon Jeruk', '12345',
            '1234567890123456', 'Budi Santoso', 'Bandung', '1970-01-01',
            'SMA', 'Wiraswasta', '5-10 Juta',
            '1234567890123456', 'Siti Aminah', 'Surabaya', '1975-01-01',
            'SMA', 'Ibu Rumah Tangga', '< 2 Juta',
            '', '', '', '', '', ''
        ];

        $column = 'A';
        foreach ($exampleData as $data) {
            $sheet->setCellValue($column . '2', $data);
            $column++;
        }

        $filename = 'template_import_mahasiswa_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Import mahasiswa dari Excel
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
                        $errors[] = "Baris {$rowNumber}: NIM tidak boleh kosong";
                        continue;
                    }

                    if (empty($row[1])) {
                        $errors[] = "Baris {$rowNumber}: Nama tidak boleh kosong";
                        continue;
                    }

                    // Cek program studi berdasarkan kode
                    $programStudi = ProgramStudi::where('kode_program_studi', $row[13])->first();
                    if (!$programStudi) {
                        $errors[] = "Baris {$rowNumber}: Kode program studi '{$row[13]}' tidak ditemukan";
                        continue;
                    }

                    // Cek kurikulum berdasarkan nama dan program studi
                    $kurikulum = Kurikulum::where('nama_kurikulum', $row[14])
                        ->where('id_program_studi', $programStudi->id_program_studi)
                        ->first();
                    if (!$kurikulum) {
                        $errors[] = "Baris {$rowNumber}: Kurikulum '{$row[14]}' tidak ditemukan untuk program studi {$row[13]}";
                        continue;
                    }

                    // Cek duplikasi NIM
                    if (Mahasiswa::where('nim', $row[0])->exists()) {
                        $errors[] = "Baris {$rowNumber}: NIM '{$row[0]}' sudah terdaftar";
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
                        'role' => 'mahasiswa',
                        'is_active' => true,
                    ]);

                    // Buat data mahasiswa
                    Mahasiswa::create([
                        'id_mahasiswa' => (string) Str::uuid(),
                        'nim' => $row[0],
                        'jenis_kelamin' => !empty($row[2]) && in_array($row[2], ['L', 'P']) ? $row[2] : null,
                        'tempat_lahir' => !empty($row[3]) ? $row[3] : null,
                        'tanggal_lahir' => !empty($row[4]) ? date('Y-m-d', strtotime($row[4])) : null,
                        'angkatan' => !empty($row[5]) ? $row[5] : date('Y'),
                        'status_mahasiswa' => !empty($row[6]) && in_array($row[6], ['AKTIF', 'CUTI', 'DO', 'KELUAR', 'LULUS', 'NONAKTIF']) ? $row[6] : 'AKTIF',
                        'nik' => !empty($row[7]) && strlen($row[7]) == 16 ? $row[7] : null,
                        'nisn' => !empty($row[8]) ? $row[8] : null,
                        'npwp' => !empty($row[9]) ? $row[9] : null,
                        'agama' => !empty($row[10]) ? $row[10] : null,
                        'id_program_studi' => $programStudi->id_program_studi,
                        'id_kurikulum' => $kurikulum->id_kurikulum,
                        'id_pengguna' => $pengguna->id_pengguna,

                        // Alamat
                        'jalan' => !empty($row[15]) ? $row[15] : null,
                        'dusun' => !empty($row[16]) ? $row[16] : null,
                        'rt' => !empty($row[17]) ? $row[17] : null,
                        'rw' => !empty($row[18]) ? $row[18] : null,
                        'kelurahan' => !empty($row[19]) ? $row[19] : null,
                        'kode_pos' => !empty($row[20]) ? $row[20] : null,

                        // Data Ayah
                        'nik_ayah' => !empty($row[21]) && strlen($row[21]) == 16 ? $row[21] : null,
                        'nama_ayah' => !empty($row[22]) ? $row[22] : null,
                        'tempat_lahir_ayah' => !empty($row[23]) ? $row[23] : null,
                        'tanggal_lahir_ayah' => !empty($row[24]) ? date('Y-m-d', strtotime($row[24])) : null,
                        'nama_pendidikan_ayah' => !empty($row[25]) ? $row[25] : null,
                        'nama_pekerjaan_ayah' => !empty($row[26]) ? $row[26] : null,
                        'nama_penghasilan_ayah' => !empty($row[27]) ? $row[27] : null,

                        // Data Ibu
                        'nik_ibu' => !empty($row[28]) && strlen($row[28]) == 16 ? $row[28] : null,
                        'nama_ibu' => !empty($row[29]) ? $row[29] : null,
                        'tempat_lahir_ibu' => !empty($row[30]) ? $row[30] : null,
                        'tanggal_lahir_ibu' => !empty($row[31]) ? date('Y-m-d', strtotime($row[31])) : null,
                        'nama_pendidikan_ibu' => !empty($row[32]) ? $row[32] : null,
                        'nama_pekerjaan_ibu' => !empty($row[33]) ? $row[33] : null,
                        'nama_penghasilan_ibu' => !empty($row[34]) ? $row[34] : null,

                        // Data Wali
                        'nama_wali' => !empty($row[35]) ? $row[35] : null,
                        'tempat_lahir_wali' => !empty($row[36]) ? $row[36] : null,
                        'tanggal_lahir_wali' => !empty($row[37]) ? date('Y-m-d', strtotime($row[37])) : null,
                        'nama_pendidikan_wali' => !empty($row[38]) ? $row[38] : null,
                        'nama_pekerjaan_wali' => !empty($row[39]) ? $row[39] : null,
                        'nama_penghasilan_wali' => !empty($row[40]) ? $row[40] : null,
                    ]);

                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            if (empty($errors)) {
                DB::commit();
                return redirect()->route('mahasiswa.index')
                    ->with('success', "Berhasil mengimpor {$importedCount} data mahasiswa.");
            } else {
                DB::rollback();
                $errorMessage = "Import gagal. Kesalahan:\n" . implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $errorMessage .= "\ndan " . (count($errors) - 10) . " kesalahan lainnya...";
                }
                return redirect()->route('mahasiswa.index')
                    ->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mahasiswa.index')
                ->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }
}
