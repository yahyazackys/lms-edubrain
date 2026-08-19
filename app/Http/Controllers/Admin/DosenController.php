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

        $dosens = $query->orderBy('nidn')->get();

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
     * Export template Excel untuk import (data wajib saja)
     */
    public function exportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom (hanya data wajib)
        $headers = [
            'NIDN',
            'Nama Lengkap',
            'Jenis Kelamin (L/P)',
            'Kode Program Studi',
            'Status Dosen',
            'Status Kepegawaian'
        ];

        // Set header dengan styling
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

        // FORMAT KOLOM NIDN (A) SEBAGAI TEXT - INI YANG PENTING
        $sheet->getStyle('A:A')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Data contoh dengan explicit text format
        $exampleData = [
            ['0123456789', 'Dr. John Doe', 'L', 'TI', 'AKTIF', 'TETAP'],
            ['0987654321', 'Dr. Jane Smith', 'P', 'SI', 'AKTIF', 'PNS'],
            ['0020012345', 'Prof. Ahmad Ibrahim', 'L', 'IF', 'AKTIF', 'PNS'],
        ];

        $row = 2;
        foreach ($exampleData as $data) {
            // Set NIDN sebagai string explicit dengan setValueExplicit
            $sheet->setCellValueExplicit('A' . $row, $data[0], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            // Kolom lainnya normal
            $column = 'B';
            for ($i = 1; $i < count($data); $i++) {
                $sheet->setCellValue($column . $row, $data[$i]);
                $column++;
            }
            $row++;
        }

        // Instruksi di sheet kedua
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Instruksi');

        $instructions = [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT DOSEN'],
            [''],
            ['Kolom yang wajib diisi:'],
            ['1. NIDN - Nomor Induk Dosen Nasional (contoh: 0123456789)'],
            ['   PENTING: Kolom NIDN sudah diformat sebagai TEXT'],
            ['   Jika angka 0 di depan hilang, ketik tanda petik (\') dulu sebelum NIDN'],
            ['   Contoh: \'002001 akan tetap menjadi 002001'],
            [''],
            ['2. Nama Lengkap - Nama lengkap dosen tanpa gelar'],
            ['3. Jenis Kelamin - L untuk Laki-laki, P untuk Perempuan'],
            ['4. Kode Program Studi - Kode program studi (contoh: TI, SI, IF)'],
            ['5. Status Dosen - Pilihan: AKTIF, CUTI, KELUAR, NONAKTIF, PENSIUN'],
            ['6. Status Kepegawaian - Pilihan: PNS, CPNS, P3K, TETAP, KONTRAK, HONORER'],
            [''],
            ['PENTING:'],
            ['- Username dan password akan dibuat otomatis menggunakan NIDN'],
            ['- Kode Program Studi harus sesuai dengan data di sistem'],
            ['- Jika kode program studi tidak ditemukan, baris akan dilewati'],
            ['- Data yang error akan dilaporkan setelah import'],
            [''],
            ['TIPS EXCEL:'],
            ['- Kolom NIDN sudah diformat sebagai TEXT otomatis'],
            ['- Jika tetap bermasalah, ketik apostrof (\') sebelum angka'],
            ['- Contoh: \'0020012345'],
            [''],
            ['JANGAN LUPA HAPUS INSTRUKSI KETIKA IMPORT DATA'],
        ];

        $row = 1;
        foreach ($instructions as $instruction) {
            $instructionSheet->setCellValue('A' . $row, $instruction[0]);
            if ($row == 1) {
                $instructionSheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
            } elseif (strpos($instruction[0], 'PENTING') !== false || strpos($instruction[0], 'TIPS') !== false) {
                $instructionSheet->getStyle('A' . $row)->getFont()->setBold(true);
            }
            $row++;
        }
        $instructionSheet->getColumnDimension('A')->setWidth(80);

        // Set active sheet kembali ke sheet pertama
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'template_import_dosen_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

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

            array_shift($rows); // Skip header

            $importedCount = 0;
            $skippedCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    $nidn = trim((string)($row[0] ?? ''));
                    $nama = trim($row[1] ?? '');
                    $jenisKelamin = strtoupper(trim($row[2] ?? ''));
                    $kodeProdi = trim($row[3] ?? '');
                    $statusDosen = strtoupper(trim($row[4] ?? ''));
                    $statusKepegawaian = strtoupper(trim($row[5] ?? ''));

                    // Validasi NIDN
                    if (empty($nidn)) {
                        $errors[] = "Baris {$rowNumber}: NIDN tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi Nama
                    if (empty($nama)) {
                        $errors[] = "Baris {$rowNumber}: Nama tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi Jenis Kelamin
                    if (!in_array($jenisKelamin, ['L', 'P'])) {
                        $errors[] = "Baris {$rowNumber}: Jenis kelamin harus L atau P";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi Status Dosen
                    if (!in_array($statusDosen, ['AKTIF', 'CUTI', 'KELUAR', 'NONAKTIF', 'PENSIUN'])) {
                        $errors[] = "Baris {$rowNumber}: Status dosen tidak valid (harus: AKTIF, CUTI, KELUAR, NONAKTIF, atau PENSIUN)";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi Status Kepegawaian
                    if (!in_array($statusKepegawaian, ['PNS', 'CPNS', 'P3K', 'TETAP', 'KONTRAK', 'HONORER'])) {
                        $errors[] = "Baris {$rowNumber}: Status kepegawaian tidak valid (harus: PNS, CPNS, P3K, TETAP, KONTRAK, atau HONORER)";
                        $skippedCount++;
                        continue;
                    }

                    // Cek duplikasi NIDN
                    if (Dosen::where('nidn', $nidn)->exists()) {
                        $errors[] = "Baris {$rowNumber}: NIDN '{$nidn}' sudah terdaftar";
                        $skippedCount++;
                        continue;
                    }

                    // Cari program studi
                    $programStudiId = null;
                    if (!empty($kodeProdi)) {
                        $programStudi = ProgramStudi::where('kode_program_studi', $kodeProdi)
                            ->where('status', 'A')
                            ->first();

                        if (!$programStudi) {
                            $errors[] = "Baris {$rowNumber}: Kode program studi '{$kodeProdi}' tidak ditemukan";
                            $skippedCount++;
                            continue;
                        }
                        $programStudiId = $programStudi->id_program_studi;
                    }

                    // Buat akun pengguna
                    $pengguna = Pengguna::create([
                        'id_pengguna' => (string) Str::uuid(),
                        'nama' => $nama,
                        'username' => $nidn,
                        'password' => Hash::make($nidn),
                        'role' => 'dosen',
                        'is_active' => true,
                    ]);

                    // Buat data dosen
                    Dosen::create([
                        'id_dosen' => (string) Str::uuid(),
                        'nidn' => $nidn,
                        'jenis_kelamin' => $jenisKelamin,
                        'status_dosen' => $statusDosen,
                        'status_kepegawaian' => $statusKepegawaian,
                        'id_program_studi' => $programStudiId,
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
                return redirect()->route('dosen.index')
                    ->with('success', "Berhasil mengimpor {$importedCount} data dosen.");
            }

            if ($importedCount > 0 && !empty($errors)) {
                // GUNAKAN request()->session() untuk menghindari warning Intelephense
                $request->session()->flash('import_errors', $errors);

                return redirect()->route('dosen.index')
                    ->with('warning', "Berhasil mengimpor {$importedCount} data dosen. {$skippedCount} data dilewati. Klik untuk melihat detail error.");
            }

            if ($importedCount === 0 && !empty($errors)) {
                $request->session()->flash('import_errors', $errors);

                return redirect()->route('dosen.index')
                    ->with('error', 'Import gagal. Tidak ada data yang berhasil diimpor. Klik untuk melihat detail error.');
            }

            return redirect()->route('dosen.index')
                ->with('info', 'Tidak ada data yang diimpor. File mungkin kosong.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('dosen.index')
                ->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }

    /**
     * Export semua data dosen ke Excel
     */
    public function export()
    {
        $dosens = Dosen::with(['pengguna', 'programStudi.jenjang'])->orderBy('nidn')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Dosen');

        // Header kolom (semua data)
        $headers = [
            'NIDN',
            'Nama Lengkap',
            'Gelar Depan',
            'Gelar Belakang',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'NIK',
            'NPWP',
            'Email',
            'No HP',
            'Program Studi',
            'Jenjang',
            'Status Dosen',
            'Status Kepegawaian',
            'Alamat Jalan',
            'Dusun',
            'RT',
            'RW',
            'Kelurahan',
            'Kode Pos',
            'Total Kuota PA',
            'Username',
            'Status Akun'
        ];

        // Set header dengan styling
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

        // Data dosen
        $row = 2;
        foreach ($dosens as $dosen) {
            $data = [
                $dosen->nidn,
                $dosen->pengguna->nama,
                $dosen->gelar_depan ?? '',
                $dosen->gelar_belakang ?? '',
                $dosen->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                $dosen->tempat_lahir ?? '',
                $dosen->tanggal_lahir ?? '',
                $dosen->nik ?? '',
                $dosen->npwp ?? '',
                $dosen->pengguna->email ?? '',
                $dosen->pengguna->no_hp ?? '',
                $dosen->programStudi->nama_program_studi ?? '',
                $dosen->programStudi->jenjang->nama_jenjang_pendidikan ?? '',
                $dosen->status_dosen,
                $dosen->status_kepegawaian,
                $dosen->jalan ?? '',
                $dosen->dusun ?? '',
                $dosen->rt ?? '',
                $dosen->rw ?? '',
                $dosen->kelurahan ?? '',
                $dosen->kode_pos ?? '',
                $dosen->total_kuota_pa ?? 0,
                $dosen->pengguna->username,
                $dosen->pengguna->is_active ? 'Aktif' : 'Tidak Aktif'
            ];

            $column = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'data_dosen_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
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