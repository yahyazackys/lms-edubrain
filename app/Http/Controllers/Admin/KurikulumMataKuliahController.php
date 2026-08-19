<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KurikulumMataKuliahController extends Controller
{
    /**
     * Display curriculum mata kuliah management page
     */
    public function index(string $kurikulumId): View
    {
        $kurikulum = Kurikulum::with(['programStudi.jenjang'])
            ->findOrFail($kurikulumId);

        $mataKuliahKurikulum = $kurikulum->mataKuliah()
            ->orderBy('kurikulum_mata_kuliah.semester')
            ->orderBy('kurikulum_mata_kuliah.kategori_mata_kuliah')
            ->orderBy('mata_kuliah.kode_mata_kuliah')
            ->get()
            ->groupBy('pivot.semester');

        $statistics = $this->calculateStatistics($kurikulum);

        return view('admin.kurikulum.mata-kuliah', compact(
            'kurikulum',
            'mataKuliahKurikulum',
            'statistics'
        ));
    }

    /**
     * Store new mata kuliah to curriculum
     */
    public function store(Request $request, string $kurikulumId): RedirectResponse
    {
        $kurikulum = Kurikulum::findOrFail($kurikulumId);

        $validated = $request->validate([
            'mata_kuliah' => 'required|array|min:1',
            'mata_kuliah.*' => 'exists:mata_kuliah,id_mata_kuliah',
            'semester' => 'required|integer|min:1|max:14',
            'kategori_mata_kuliah' => 'required|in:MKWUUPT,MKWU,MKWPS,MKP',
        ]);

        DB::beginTransaction();

        try {
            $attachData = [];
            $duplicateMatkulNames = [];

            foreach ($validated['mata_kuliah'] as $mataKuliahId) {
                if ($kurikulum->mataKuliah()->where('mata_kuliah.id_mata_kuliah', $mataKuliahId)->exists()) {
                    $mataKuliah = MataKuliah::find($mataKuliahId);
                    $duplicateMatkulNames[] = $mataKuliah->nama_mata_kuliah;
                    continue;
                }

                $attachData[$mataKuliahId] = [
                    'id' => Str::uuid()->toString(),
                    'semester' => $validated['semester'],
                    'kategori_mata_kuliah' => $validated['kategori_mata_kuliah'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($duplicateMatkulNames)) {
                DB::rollBack();
                $duplicateList = implode(', ', $duplicateMatkulNames);
                return back()->withInput()
                    ->with('error', "Mata kuliah berikut sudah ada dalam kurikulum: {$duplicateList}");
            }

            if (empty($attachData)) {
                DB::rollBack();
                return back()->withInput()
                    ->with('error', 'Tidak ada mata kuliah baru yang dapat ditambahkan.');
            }

            $kurikulum->mataKuliah()->attach($attachData);

            DB::commit();

            $jumlahMataKuliah = count($attachData);
            $kategoriLabel = $this->getKategoriLabel($validated['kategori_mata_kuliah']);

            $message = $jumlahMataKuliah === 1
                ? "1 mata kuliah {$kategoriLabel} berhasil ditambahkan ke semester {$validated['semester']}"
                : "{$jumlahMataKuliah} mata kuliah {$kategoriLabel} berhasil ditambahkan ke semester {$validated['semester']}";

            return redirect()->route('kurikulum.mata-kuliah.index', $kurikulumId)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan mata kuliah. Silakan coba lagi.');
        }
    }

    /**
     * Update mata kuliah in curriculum
     */
    public function update(Request $request, string $kurikulumId, string $mataKuliahId): RedirectResponse
    {
        $kurikulum = Kurikulum::findOrFail($kurikulumId);
        $mataKuliah = MataKuliah::findOrFail($mataKuliahId);

        $validated = $request->validate([
            'semester' => 'required|integer|min:1|max:14',
            'kategori_mata_kuliah' => 'required|in:MKWUUPT,MKWU,MKWPS,MKP',
        ]);

        $pivotData = $kurikulum->mataKuliah()
            ->where('mata_kuliah.id_mata_kuliah', $mataKuliahId)
            ->first();

        if (!$pivotData) {
            return back()->with('error', 'Mata kuliah tidak ditemukan dalam kurikulum ini.');
        }

        $kurikulum->mataKuliah()->updateExistingPivot($mataKuliahId, [
            'semester' => $validated['semester'],
            'kategori_mata_kuliah' => $validated['kategori_mata_kuliah'],
            'updated_at' => now(),
        ]);

        return redirect()->route('kurikulum.mata-kuliah.index', $kurikulumId)
            ->with('success', "Data mata kuliah '{$mataKuliah->nama_mata_kuliah}' berhasil diperbarui.");
    }

    /**
     * Remove mata kuliah from curriculum
     */
    public function destroy(string $kurikulumId, string $mataKuliahId): RedirectResponse
    {
        $kurikulum = Kurikulum::findOrFail($kurikulumId);
        $mataKuliah = MataKuliah::findOrFail($mataKuliahId);

        if (!$kurikulum->mataKuliah()->where('mata_kuliah.id_mata_kuliah', $mataKuliahId)->exists()) {
            return back()->with('error', 'Mata kuliah tidak ditemukan dalam kurikulum ini.');
        }

        $kurikulum->mataKuliah()->detach($mataKuliahId);

        return redirect()->route('kurikulum.mata-kuliah.index', $kurikulumId)
            ->with('success', "Mata kuliah '{$mataKuliah->nama_mata_kuliah}' berhasil dihapus dari kurikulum.");
    }

    /**
     * Export template Excel untuk import
     */
    public function exportTemplate(string $kurikulumId)
    {
        $kurikulum = Kurikulum::findOrFail($kurikulumId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Kode Mata Kuliah', 'Semester', 'Kategori (MKWUUPT/MKWU/MKWPS/MKP)'];

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
            ['MAT101', '1', 'MKWU'],
            ['ALG102', '1', 'MKWPS'],
            ['BD201', '2', 'MKWPS'],
            ['SKRIPSI', '8', 'MKP'],
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

        $sheet->setCellValue('A' . $noteStartRow, '1. Kode Mata Kuliah: Kode mata kuliah yang sudah terdaftar di sistem (wajib diisi)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Contoh: MAT101, ALG102, BD201');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '2. Semester: Semester mata kuliah diajarkan, 1-14 (wajib diisi)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Contoh: 1, 2, 3, 4, 5, 6, 7, 8');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '3. Kategori: Kategori mata kuliah (wajib diisi, huruf KAPITAL)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Pilihan: MKWUUPT, MKWU, MKWPS, MKP');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   - MKWUUPT: MK Wajib UUPT (Pancasila, Kewarganegaraan, Agama)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   - MKWU: MK Wajib Universitas (Bahasa Indonesia, Inggris, dll)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   - MKWPS: MK Wajib Program Studi');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   - MKP: MK Pilihan');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '4. Mata kuliah yang sudah ada di kurikulum akan dilewati');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '5. Hapus 4 baris contoh di atas sebelum mengisi data sebenarnya');
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
        $sheet->getColumnDimension('A')->setWidth(100);

        $filename = 'template_import_mk_' . Str::slug($kurikulum->nama_kurikulum) . '_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export data mata kuliah kurikulum ke Excel
     */
    public function export(string $kurikulumId)
    {
        $kurikulum = Kurikulum::with('programStudi')->findOrFail($kurikulumId);

        $mataKuliahs = $kurikulum->mataKuliah()
            ->orderBy('kurikulum_mata_kuliah.semester')
            ->orderBy('kurikulum_mata_kuliah.kategori_mata_kuliah')
            ->orderBy('mata_kuliah.kode_mata_kuliah')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Kode Mata Kuliah', 'Nama Mata Kuliah', 'SKS', 'Jenis', 'Semester', 'Kategori'];

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
        foreach ($mataKuliahs as $mk) {
            $sheet->setCellValue('A' . $row, $mk->kode_mata_kuliah);
            $sheet->setCellValue('B' . $row, $mk->nama_mata_kuliah);
            $sheet->setCellValue('C' . $row, $mk->sks_mata_kuliah);
            $sheet->setCellValue('D' . $row, $mk->jenis_mata_kuliah);
            $sheet->setCellValue('E' . $row, $mk->pivot->semester);
            $sheet->setCellValue('F' . $row, $this->getKategoriLabel($mk->pivot->kategori_mata_kuliah));
            $row++;
        }

        $filename = 'data_mk_' . Str::slug($kurikulum->nama_kurikulum) . '_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Import mata kuliah kurikulum dari Excel
     */
    public function import(Request $request, string $kurikulumId): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        $kurikulum = Kurikulum::findOrFail($kurikulumId);

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
                    $kodeMK = strtoupper(trim((string)($row[0] ?? '')));
                    $semester = trim($row[1] ?? '');
                    $kategori = strtoupper(trim($row[2] ?? ''));

                    if (empty($kodeMK)) {
                        $errors[] = "Baris {$rowNumber}: Kode mata kuliah tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    if (empty($semester) || !is_numeric($semester)) {
                        $errors[] = "Baris {$rowNumber}: Semester tidak valid";
                        $skippedCount++;
                        continue;
                    }

                    $semester = intval($semester);
                    if ($semester < 1 || $semester > 14) {
                        $errors[] = "Baris {$rowNumber}: Semester harus antara 1-14";
                        $skippedCount++;
                        continue;
                    }

                    if (empty($kategori)) {
                        $errors[] = "Baris {$rowNumber}: Kategori tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    if (!in_array($kategori, ['MKWUUPT', 'MKWU', 'MKWPS', 'MKP'])) {
                        $errors[] = "Baris {$rowNumber}: Kategori tidak valid (harus: MKWUUPT, MKWU, MKWPS, atau MKP)";
                        $skippedCount++;
                        continue;
                    }

                    $mataKuliah = MataKuliah::where('kode_mata_kuliah', $kodeMK)->first();

                    if (!$mataKuliah) {
                        $errors[] = "Baris {$rowNumber}: Kode mata kuliah '{$kodeMK}' tidak ditemukan";
                        $skippedCount++;
                        continue;
                    }

                    $exists = $kurikulum->mataKuliah()
                        ->where('mata_kuliah.id_mata_kuliah', $mataKuliah->id_mata_kuliah)
                        ->exists();

                    if ($exists) {
                        $errors[] = "Baris {$rowNumber}: Mata kuliah '{$kodeMK}' sudah ada dalam kurikulum";
                        $skippedCount++;
                        continue;
                    }

                    $kurikulum->mataKuliah()->attach($mataKuliah->id_mata_kuliah, [
                        'id' => (string) Str::uuid(),
                        'semester' => $semester,
                        'kategori_mata_kuliah' => $kategori,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                    $skippedCount++;
                }
            }

            DB::commit();

            if ($importedCount > 0 && empty($errors)) {
                return redirect()->route('kurikulum.mata-kuliah.index', $kurikulumId)
                    ->with('success', "Berhasil mengimpor {$importedCount} mata kuliah.");
            }

            if ($importedCount > 0 && !empty($errors)) {
                $request->session()->flash('import_errors', $errors);

                return redirect()->route('kurikulum.mata-kuliah.index', $kurikulumId)
                    ->with('warning', "Berhasil mengimpor {$importedCount} mata kuliah. {$skippedCount} data dilewati.");
            }

            if ($importedCount === 0 && !empty($errors)) {
                $request->session()->flash('import_errors', $errors);

                return redirect()->route('kurikulum.mata-kuliah.index', $kurikulumId)
                    ->with('error', 'Import gagal. Tidak ada data yang berhasil diimpor.');
            }

            return redirect()->route('kurikulum.mata-kuliah.index', $kurikulumId)
                ->with('info', 'Tidak ada data yang diimpor. File mungkin kosong.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('kurikulum.mata-kuliah.index', $kurikulumId)
                ->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }

    /**
     * Get available mata kuliah for AJAX search
     */
    public function getMataKuliah(Request $request, string $kurikulumId): JsonResponse
    {
        $kurikulum = Kurikulum::findOrFail($kurikulumId);

        $existingMataKuliahIds = $kurikulum->mataKuliah()
            ->pluck('mata_kuliah.id_mata_kuliah')
            ->toArray();

        $query = MataKuliah::whereNotIn('id_mata_kuliah', $existingMataKuliahIds);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode_mata_kuliah', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('nama_mata_kuliah', 'LIKE', "%{$searchTerm}%");
            });
        }

        $mataKuliahs = $query->orderBy('kode_mata_kuliah')
            ->take(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id_mata_kuliah' => $item->id_mata_kuliah,
                    'kode_mata_kuliah' => $item->kode_mata_kuliah,
                    'nama_mata_kuliah' => $item->nama_mata_kuliah,
                    'sks_mata_kuliah' => $item->sks_mata_kuliah,
                    'jenis_mata_kuliah' => $item->jenis_mata_kuliah,
                ];
            });

        return response()->json($mataKuliahs);
    }

    /**
     * Calculate curriculum statistics
     */
    private function calculateStatistics(Kurikulum $kurikulum): array
    {
        $stats = [
            'total_mata_kuliah' => $kurikulum->mataKuliah()->count(),
            'total_sks' => $kurikulum->mataKuliah()->sum('mata_kuliah.sks_mata_kuliah'),
        ];

        $categories = ['mkwuupt', 'mkwu', 'mkwps', 'mkp'];

        foreach ($categories as $category) {
            $categoryUpper = strtoupper($category);

            $stats["mata_kuliah_{$category}"] = $kurikulum->mataKuliah()
                ->wherePivot('kategori_mata_kuliah', $categoryUpper)
                ->count();

            $stats["sks_{$category}"] = $kurikulum->mataKuliah()
                ->wherePivot('kategori_mata_kuliah', $categoryUpper)
                ->sum('mata_kuliah.sks_mata_kuliah');
        }

        $stats['sks_lulus_minimal'] = $kurikulum->jumlah_sks_lulus;
        $stats['sks_mkwuupt_minimal'] = $kurikulum->sks_mkwuupt_minimal;
        $stats['sks_mkwu_minimal'] = $kurikulum->sks_mkwu_minimal;
        $stats['sks_mkwps_minimal'] = $kurikulum->sks_mkwps_minimal;
        $stats['sks_mkp_minimal'] = $kurikulum->sks_mkp_minimal;

        return $stats;
    }

    /**
     * Get category label in Indonesian
     */
    private function getKategoriLabel(string $kategori): string
    {
        $labels = [
            'MKWUUPT' => 'MK Wajib UUPT',
            'MKWU' => 'MK Wajib Universitas',
            'MKWPS' => 'MK Wajib Program Studi',
            'MKP' => 'MK Pilihan',
        ];

        return $labels[$kategori] ?? $kategori;
    }
}
