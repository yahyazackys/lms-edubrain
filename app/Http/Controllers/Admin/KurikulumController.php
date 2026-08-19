<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kurikulum;
use App\Models\Semester;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class KurikulumController extends Controller
{
    /**
     * Display kurikulum management page
     */
    public function index(Request $request): View
    {
        $allSemesters = Semester::orderBy('kode_semester', 'desc')->get();
        $usedSemesters = Semester::whereIn(
            'id_semester',
            Kurikulum::distinct('id_semester')->pluck('id_semester')
        )->orderBy('kode_semester', 'desc')->get();

        $query = Kurikulum::with(['programStudi.jenjang', 'semester']);

        $kurikulums = $query->orderBy('nama_kurikulum')->paginate(10);

        $programStudis = ProgramStudi::with('jenjang')
            ->where('status', 'A')
            ->orderBy('nama_program_studi')
            ->get();

        return view('admin.kurikulum.index', compact(
            'kurikulums',
            'allSemesters',
            'usedSemesters',
            'programStudis'
        ));
    }

    /**
     * Store new kurikulum
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kurikulum' => 'required|string|max:100',
            'jumlah_sks_lulus' => 'required|integer|min:1|max:200',
            'sks_mkwuupt_minimal' => 'required|integer|min:0|max:200',
            'sks_mkwu_minimal' => 'required|integer|min:0|max:200',
            'sks_mkwps_minimal' => 'required|integer|min:0|max:200',
            'sks_mkp_minimal' => 'required|integer|min:0|max:200',
            'id_program_studi' => 'required|exists:program_studi,id_program_studi',
            'id_semester' => 'required|exists:semester,id_semester',
        ]);

        $totalSksMinimal = $validated['sks_mkwuupt_minimal'] + $validated['sks_mkwu_minimal'] +
            $validated['sks_mkwps_minimal'] + $validated['sks_mkp_minimal'];

        if ($totalSksMinimal != $validated['jumlah_sks_lulus']) {
            return back()->withInput()->with(
                'error',
                "Total SKS dari semua kategori harus sama dengan SKS lulus. Saat ini: {$totalSksMinimal}, SKS lulus: {$validated['jumlah_sks_lulus']}"
            );
        }

        $exists = Kurikulum::where('id_semester', $validated['id_semester'])
            ->where('id_program_studi', $validated['id_program_studi'])
            ->where('nama_kurikulum', $validated['nama_kurikulum'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with(
                'error',
                'Kurikulum dengan nama tersebut sudah ada di semester dan program studi ini.'
            );
        }

        Kurikulum::create([
            'id_kurikulum' => Str::uuid()->toString(),
            'nama_kurikulum' => $validated['nama_kurikulum'],
            'jumlah_sks_lulus' => $validated['jumlah_sks_lulus'],
            'sks_mkwuupt_minimal' => $validated['sks_mkwuupt_minimal'],
            'sks_mkwu_minimal' => $validated['sks_mkwu_minimal'],
            'sks_mkwps_minimal' => $validated['sks_mkwps_minimal'],
            'sks_mkp_minimal' => $validated['sks_mkp_minimal'],
            'id_program_studi' => $validated['id_program_studi'],
            'id_semester' => $validated['id_semester'],
        ]);

        return redirect()->route('kurikulum.index')
            ->with('success', 'Kurikulum berhasil ditambahkan.');
    }

    /**
     * Update kurikulum
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $kurikulum = Kurikulum::findOrFail($id);

        $validated = $request->validate([
            'nama_kurikulum' => 'required|string|max:100',
            'jumlah_sks_lulus' => 'required|integer|min:1|max:200',
            'sks_mkwuupt_minimal' => 'required|integer|min:0|max:200',
            'sks_mkwu_minimal' => 'required|integer|min:0|max:200',
            'sks_mkwps_minimal' => 'required|integer|min:0|max:200',
            'sks_mkp_minimal' => 'required|integer|min:0|max:200',
            'id_program_studi' => 'required|exists:program_studi,id_program_studi',
            'id_semester' => 'required|exists:semester,id_semester',
        ]);

        $totalSksMinimal = $validated['sks_mkwuupt_minimal'] + $validated['sks_mkwu_minimal'] +
            $validated['sks_mkwps_minimal'] + $validated['sks_mkp_minimal'];

        if ($totalSksMinimal != $validated['jumlah_sks_lulus']) {
            return back()->withInput()->with(
                'error',
                "Total SKS dari semua kategori harus sama dengan SKS lulus. Saat ini: {$totalSksMinimal}, SKS lulus: {$validated['jumlah_sks_lulus']}"
            );
        }

        $exists = Kurikulum::where('id_semester', $validated['id_semester'])
            ->where('id_program_studi', $validated['id_program_studi'])
            ->where('nama_kurikulum', $validated['nama_kurikulum'])
            ->where('id_kurikulum', '!=', $kurikulum->id_kurikulum)
            ->exists();

        if ($exists) {
            return back()->withInput()->with(
                'error',
                'Kurikulum dengan nama tersebut sudah ada di semester dan program studi ini.'
            );
        }

        $kurikulum->update($validated);

        return redirect()->route('kurikulum.index')
            ->with('success', 'Kurikulum berhasil diperbarui.');
    }

    /**
     * Delete kurikulum
     */
    public function destroy(string $id): RedirectResponse
    {
        $kurikulum = Kurikulum::findOrFail($id);
        $kurikulumName = $kurikulum->nama_kurikulum;

        if ($kurikulum->mataKuliah()->exists()) {
            return back()->with(
                'error',
                'Kurikulum tidak dapat dihapus karena masih memiliki mata kuliah.'
            );
        }

        $kurikulum->delete();

        return redirect()->route('kurikulum.index')
            ->with('success', "Kurikulum \"{$kurikulumName}\" berhasil dihapus.");
    }

    /**
     * Export template Excel untuk import
     */
    public function exportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'Kode Program Studi',
            'Kode Semester',
            'Nama Kurikulum',
            'SKS Lulus',
            'SKS MKWUUPT',
            'SKS MKWU',
            'SKS MKWPS',
            'SKS MKP'
        ];

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

        $exampleData = [
            ['TI', '20231', 'Kurikulum 2023', '144', '8', '12', '108', '16'],
            ['SI', '20231', 'Kurikulum 2023', '144', '8', '12', '108', '16'],
            ['IF', '20241', 'Kurikulum 2024', '150', '10', '14', '110', '16'],
        ];

        $row = 2;
        foreach ($exampleData as $data) {
            $column = 'A';
            for ($i = 0; $i < count($data); $i++) {
                $sheet->setCellValue($column . $row, $data[$i]);
                $column++;
            }
            $row++;
        }

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

        $sheet->setCellValue('A' . $noteStartRow, '1. Kode Program Studi: Kode prodi yang sesuai dengan data di sistem (wajib diisi)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Contoh: TI, SI, IF, MI');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '2. Kode Semester: Kode semester berlaku (wajib diisi)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Contoh: 20231, 20232, 20241 (Format: YYYYS dimana S = 1 atau 2)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '3. Nama Kurikulum: Nama kurikulum (wajib diisi, maksimal 100 karakter)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Contoh: Kurikulum 2023, Kurikulum 2024');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '4. SKS Lulus: Total SKS untuk lulus (wajib diisi, 1-200)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '5. SKS MKWUUPT: SKS MK Wajib UUPT - Pancasila, Kewarganegaraan, Agama (wajib diisi, 0-200)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '6. SKS MKWU: SKS MK Wajib Universitas - Bahasa Indonesia, Inggris, dll (wajib diisi, 0-200)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '7. SKS MKWPS: SKS MK Wajib Program Studi (wajib diisi, 0-200)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '8. SKS MKP: SKS MK Pilihan (wajib diisi, 0-200)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '9. PENTING: Total (MKWUUPT + MKWU + MKWPS + MKP) HARUS SAMA dengan SKS Lulus');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Contoh: 8 + 12 + 108 + 16 = 144');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '10. Hapus 3 baris contoh di atas sebelum mengisi data sebenarnya');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, 'JANGAN LUPA HAPUS INSTRUKSI KETIKA IMPORT DATA');
        $sheet->getStyle('A' . $noteStartRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $noteStartRow)->getFont()->setSize(12);
        $sheet->getStyle('A' . $noteStartRow)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF00');

        $sheet->getStyle('A' . ($row + 1) . ':A' . $noteStartRow)->getAlignment()->setWrapText(true);
        $sheet->getColumnDimension('A')->setWidth(120);

        $filename = 'template_import_kurikulum_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export data kurikulum ke Excel
     */
    public function export()
    {
        $kurikulums = Kurikulum::with(['programStudi', 'semester'])
            ->orderBy('nama_kurikulum')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'Kode Program Studi',
            'Kode Semester',
            'Nama Kurikulum',
            'SKS Lulus',
            'SKS MKWUUPT',
            'SKS MKWU',
            'SKS MKWPS',
            'SKS MKP'
        ];

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

        $row = 2;
        foreach ($kurikulums as $kurikulum) {
            $sheet->setCellValue('A' . $row, $kurikulum->programStudi->kode_program_studi ?? '');
            $sheet->setCellValue('B' . $row, $kurikulum->semester->kode_semester ?? '');
            $sheet->setCellValue('C' . $row, $kurikulum->nama_kurikulum);
            $sheet->setCellValue('D' . $row, $kurikulum->jumlah_sks_lulus);
            $sheet->setCellValue('E' . $row, $kurikulum->sks_mkwuupt_minimal);
            $sheet->setCellValue('F' . $row, $kurikulum->sks_mkwu_minimal);
            $sheet->setCellValue('G' . $row, $kurikulum->sks_mkwps_minimal);
            $sheet->setCellValue('H' . $row, $kurikulum->sks_mkp_minimal);
            $row++;
        }

        $filename = 'data_kurikulum_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Import kurikulum dari Excel
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

            array_shift($rows);

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
                    $kodeProdi = strtoupper(trim((string)($row[0] ?? '')));
                    $kodeSemester = trim((string)($row[1] ?? ''));
                    $namaKurikulum = trim($row[2] ?? '');
                    $sksLulus = trim($row[3] ?? '');
                    $sksMKWUUPT = trim($row[4] ?? '');
                    $sksMKWU = trim($row[5] ?? '');
                    $sksMKWPS = trim($row[6] ?? '');
                    $sksMKP = trim($row[7] ?? '');

                    if (empty($kodeProdi)) {
                        $errors[] = "Baris {$rowNumber}: Kode program studi tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    if (empty($kodeSemester)) {
                        $errors[] = "Baris {$rowNumber}: Kode semester tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    if (empty($namaKurikulum)) {
                        $errors[] = "Baris {$rowNumber}: Nama kurikulum tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    $programStudi = ProgramStudi::where('kode_program_studi', $kodeProdi)
                        ->where('status', 'A')
                        ->first();

                    if (!$programStudi) {
                        $errors[] = "Baris {$rowNumber}: Kode program studi '{$kodeProdi}' tidak ditemukan";
                        $skippedCount++;
                        continue;
                    }

                    $semester = Semester::where('kode_semester', $kodeSemester)->first();

                    if (!$semester) {
                        $errors[] = "Baris {$rowNumber}: Kode semester '{$kodeSemester}' tidak ditemukan";
                        $skippedCount++;
                        continue;
                    }

                    if (empty($sksLulus) || !is_numeric($sksLulus)) {
                        $errors[] = "Baris {$rowNumber}: SKS Lulus tidak valid";
                        $skippedCount++;
                        continue;
                    }

                    $sksLulus = intval($sksLulus);
                    $sksMKWUUPT = intval($sksMKWUUPT);
                    $sksMKWU = intval($sksMKWU);
                    $sksMKWPS = intval($sksMKWPS);
                    $sksMKP = intval($sksMKP);

                    if ($sksLulus < 1 || $sksLulus > 200) {
                        $errors[] = "Baris {$rowNumber}: SKS Lulus harus antara 1-200";
                        $skippedCount++;
                        continue;
                    }

                    $totalSks = $sksMKWUUPT + $sksMKWU + $sksMKWPS + $sksMKP;
                    if ($totalSks != $sksLulus) {
                        $errors[] = "Baris {$rowNumber}: Total SKS kategori ({$totalSks}) tidak sama dengan SKS Lulus ({$sksLulus})";
                        $skippedCount++;
                        continue;
                    }

                    $exists = Kurikulum::where('id_semester', $semester->id_semester)
                        ->where('id_program_studi', $programStudi->id_program_studi)
                        ->where('nama_kurikulum', $namaKurikulum)
                        ->exists();

                    if ($exists) {
                        $errors[] = "Baris {$rowNumber}: Kurikulum '{$namaKurikulum}' sudah ada untuk prodi {$kodeProdi} semester {$kodeSemester}";
                        $skippedCount++;
                        continue;
                    }

                    Kurikulum::create([
                        'id_kurikulum' => (string) Str::uuid(),
                        'nama_kurikulum' => $namaKurikulum,
                        'jumlah_sks_lulus' => $sksLulus,
                        'sks_mkwuupt_minimal' => $sksMKWUUPT,
                        'sks_mkwu_minimal' => $sksMKWU,
                        'sks_mkwps_minimal' => $sksMKWPS,
                        'sks_mkp_minimal' => $sksMKP,
                        'id_program_studi' => $programStudi->id_program_studi,
                        'id_semester' => $semester->id_semester,
                    ]);

                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                    $skippedCount++;
                }
            }

            DB::commit();

            if ($importedCount > 0 && empty($errors)) {
                return redirect()->route('kurikulum.index')
                    ->with('success', "Berhasil mengimpor {$importedCount} data kurikulum.");
            }

            if ($importedCount > 0 && !empty($errors)) {
                $request->session()->flash('import_errors', $errors);

                return redirect()->route('kurikulum.index')
                    ->with('warning', "Berhasil mengimpor {$importedCount} data kurikulum. {$skippedCount} data dilewati.");
            }

            if ($importedCount === 0 && !empty($errors)) {
                $request->session()->flash('import_errors', $errors);

                return redirect()->route('kurikulum.index')
                    ->with('error', 'Import gagal. Tidak ada data yang berhasil diimpor.');
            }

            return redirect()->route('kurikulum.index')
                ->with('info', 'Tidak ada data yang diimpor. File mungkin kosong.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('kurikulum.index')
                ->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }

    /**
     * Get program studi for AJAX
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
                ];
            });

        return response()->json($programStudis);
    }
}
