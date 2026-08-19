<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class MataKuliahController extends Controller
{
    /**
     * Tampilkan halaman mata kuliah dengan pencarian dan filter
     */
    public function index(Request $request): View
    {
        $query = MataKuliah::query();

        $mataKuliahs = $query->orderBy('kode_mata_kuliah')->get();

        // Get unique SKS values for filter
        $sksOptions = MataKuliah::select('sks_mata_kuliah')
            ->distinct()
            ->orderBy('sks_mata_kuliah')
            ->pluck('sks_mata_kuliah');

        // Jenis mata kuliah options
        $jenisOptions = ['TEORI', 'PRAKTIKUM', 'MAGANG', 'KKN', 'SKRIPSI'];

        return view('admin.mata-kuliah.index', compact('mataKuliahs', 'sksOptions', 'jenisOptions'));
    }

    /**
     * Simpan mata kuliah baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_mata_kuliah' => 'required|string|max:20|unique:mata_kuliah,kode_mata_kuliah',
            'nama_mata_kuliah' => 'required|string|max:150',
            'sks_mata_kuliah' => 'required|numeric|min:0.5|max:8|regex:/^\d+(\.\d{1,2})?$/',
            'jenis_mata_kuliah' => 'required|in:TEORI,PRAKTIKUM,MAGANG,KKN,SKRIPSI',
        ], [
            'kode_mata_kuliah.required' => 'Kode mata kuliah wajib diisi.',
            'kode_mata_kuliah.unique' => 'Kode mata kuliah sudah digunakan.',
            'nama_mata_kuliah.required' => 'Nama mata kuliah wajib diisi.',
            'nama_mata_kuliah.max' => 'Nama mata kuliah maksimal 150 karakter.',
            'sks_mata_kuliah.required' => 'Jumlah SKS wajib diisi.',
            'sks_mata_kuliah.min' => 'Jumlah SKS minimal 0.5.',
            'sks_mata_kuliah.max' => 'Jumlah SKS maksimal 8.',
            'sks_mata_kuliah.regex' => 'Format SKS tidak valid (contoh: 2 atau 2.5).',
            'jenis_mata_kuliah.required' => 'Jenis mata kuliah wajib diisi.',
            'jenis_mata_kuliah.in' => 'Jenis mata kuliah tidak valid.',
        ]);

        MataKuliah::create([
            'id_mata_kuliah' => (string) Str::uuid(),
            'kode_mata_kuliah' => strtoupper($validated['kode_mata_kuliah']),
            'nama_mata_kuliah' => $validated['nama_mata_kuliah'],
            'sks_mata_kuliah' => $validated['sks_mata_kuliah'],
            'jenis_mata_kuliah' => $validated['jenis_mata_kuliah'],
        ]);

        return redirect()->route('mata-kuliah.index')
            ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    /**
     * Update mata kuliah
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $mataKuliah = MataKuliah::findOrFail($id);

        $validated = $request->validate([
            'kode_mata_kuliah' => 'required|string|max:20|unique:mata_kuliah,kode_mata_kuliah,' . $mataKuliah->id_mata_kuliah . ',id_mata_kuliah',
            'nama_mata_kuliah' => 'required|string|max:150',
            'sks_mata_kuliah' => 'required|numeric|min:0.5|max:8|regex:/^\d+(\.\d{1,2})?$/',
            'jenis_mata_kuliah' => 'required|in:TEORI,PRAKTIKUM,MAGANG,KKN,SKRIPSI',
        ], [
            'kode_mata_kuliah.required' => 'Kode mata kuliah wajib diisi.',
            'kode_mata_kuliah.unique' => 'Kode mata kuliah sudah digunakan.',
            'nama_mata_kuliah.required' => 'Nama mata kuliah wajib diisi.',
            'nama_mata_kuliah.max' => 'Nama mata kuliah maksimal 150 karakter.',
            'sks_mata_kuliah.required' => 'Jumlah SKS wajib diisi.',
            'sks_mata_kuliah.min' => 'Jumlah SKS minimal 0.5.',
            'sks_mata_kuliah.max' => 'Jumlah SKS maksimal 8.',
            'sks_mata_kuliah.regex' => 'Format SKS tidak valid (contoh: 2 atau 2.5).',
            'jenis_mata_kuliah.required' => 'Jenis mata kuliah wajib diisi.',
            'jenis_mata_kuliah.in' => 'Jenis mata kuliah tidak valid.',
        ]);

        $mataKuliah->update([
            'kode_mata_kuliah' => strtoupper($validated['kode_mata_kuliah']),
            'nama_mata_kuliah' => $validated['nama_mata_kuliah'],
            'sks_mata_kuliah' => $validated['sks_mata_kuliah'],
            'jenis_mata_kuliah' => $validated['jenis_mata_kuliah'],
        ]);

        return redirect()->route('mata-kuliah.index')
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * Hapus mata kuliah
     */
    public function destroy(string $id): RedirectResponse
    {
        $mataKuliah = MataKuliah::findOrFail($id);
        $mataKuliahName = $mataKuliah->nama_mata_kuliah;

        if ($mataKuliah->kurikulum()->exists()) {
            return back()->with('error', 'Mata kuliah tidak dapat dihapus karena masih digunakan dalam kurikulum.');
        }

        if ($mataKuliah->kelasKuliah()->exists()) {
            return back()->with('error', 'Mata kuliah tidak dapat dihapus karena masih memiliki kelas kuliah.');
        }

        $mataKuliah->delete();

        return redirect()->route('mata-kuliah.index')
            ->with('success', "Mata kuliah \"{$mataKuliahName}\" berhasil dihapus.");
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
            'Kode Mata Kuliah',
            'Nama Mata Kuliah',
            'SKS',
            'Jenis (TEORI/PRAKTIKUM/MAGANG/KKN/SKRIPSI)'
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

        // Data contoh
        $exampleData = [
            ['MAT101', 'Matematika Diskrit', '3', 'TEORI'],
            ['ALG102', 'Algoritma dan Pemrograman', '3', 'TEORI'],
            ['PRAK102', 'Praktikum Algoritma', '1', 'PRAKTIKUM'],
            ['SKRIPSI', 'Skripsi', '6', 'SKRIPSI'],
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

        $sheet->setCellValue('A' . $noteStartRow, '1. Kode Mata Kuliah: Format bebas, hanya huruf KAPITAL dan angka (wajib diisi, unik)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Contoh: MAT101, ALG102, BD201, PEMWEB');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '2. Nama Mata Kuliah: Nama lengkap mata kuliah (wajib diisi, maksimal 150 karakter)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '3. SKS: Jumlah SKS mata kuliah (wajib diisi)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Pilihan: 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 6, 8');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Format: Gunakan angka atau desimal dengan titik (contoh: 2.5)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '4. Jenis: Jenis mata kuliah (wajib diisi, huruf KAPITAL)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Pilihan: TEORI, PRAKTIKUM, MAGANG, KKN, SKRIPSI');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '5. Pastikan kode mata kuliah tidak duplikat dengan data yang sudah ada');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '6. Hapus 4 baris contoh di atas sebelum mengisi data sebenarnya');
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

        $filename = 'template_import_mata_kuliah_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export data mata kuliah ke Excel
     */
    public function export()
    {
        $mataKuliahs = MataKuliah::orderBy('kode_mata_kuliah')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = ['Kode Mata Kuliah', 'Nama Mata Kuliah', 'SKS', 'Jenis'];
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

        // Data
        $row = 2;
        foreach ($mataKuliahs as $mataKuliah) {
            $sheet->setCellValue('A' . $row, $mataKuliah->kode_mata_kuliah);
            $sheet->setCellValue('B' . $row, $mataKuliah->nama_mata_kuliah);
            $sheet->setCellValue('C' . $row, $mataKuliah->sks_mata_kuliah);
            $sheet->setCellValue('D' . $row, $mataKuliah->jenis_mata_kuliah);
            $row++;
        }

        $filename = 'data_mata_kuliah_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Import mata kuliah dari Excel
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
                    // Validasi data
                    $kode = strtoupper(trim((string)($row[0] ?? '')));
                    $nama = trim($row[1] ?? '');
                    $sks = trim($row[2] ?? '');
                    $jenis = strtoupper(trim($row[3] ?? ''));

                    // Validasi Kode
                    if (empty($kode)) {
                        $errors[] = "Baris {$rowNumber}: Kode mata kuliah tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi format kode
                    if (!preg_match('/^[A-Z0-9]+$/', $kode)) {
                        $errors[] = "Baris {$rowNumber}: Kode mata kuliah hanya boleh berisi huruf kapital dan angka";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi Nama
                    if (empty($nama)) {
                        $errors[] = "Baris {$rowNumber}: Nama mata kuliah tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi SKS
                    if (empty($sks) || !is_numeric($sks)) {
                        $errors[] = "Baris {$rowNumber}: SKS tidak valid (harus berupa angka)";
                        $skippedCount++;
                        continue;
                    }

                    $sks = floatval($sks);

                    if ($sks < 0.5 || $sks > 8) {
                        $errors[] = "Baris {$rowNumber}: SKS harus antara 0.5 - 8";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi Jenis
                    if (empty($jenis)) {
                        $jenis = 'TEORI'; // Default
                    }

                    if (!in_array($jenis, ['TEORI', 'PRAKTIKUM', 'MAGANG', 'KKN', 'SKRIPSI'])) {
                        $errors[] = "Baris {$rowNumber}: Jenis mata kuliah tidak valid (harus: TEORI, PRAKTIKUM, MAGANG, KKN, atau SKRIPSI)";
                        $skippedCount++;
                        continue;
                    }

                    // Cek duplikasi kode
                    if (MataKuliah::where('kode_mata_kuliah', $kode)->exists()) {
                        $errors[] = "Baris {$rowNumber}: Kode mata kuliah '{$kode}' sudah terdaftar";
                        $skippedCount++;
                        continue;
                    }

                    // Buat data mata kuliah
                    MataKuliah::create([
                        'id_mata_kuliah' => (string) Str::uuid(),
                        'kode_mata_kuliah' => $kode,
                        'nama_mata_kuliah' => $nama,
                        'sks_mata_kuliah' => $sks,
                        'jenis_mata_kuliah' => $jenis,
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
                return redirect()->route('mata-kuliah.index')
                    ->with('success', "Berhasil mengimpor {$importedCount} data mata kuliah.");
            }

            if ($importedCount > 0 && !empty($errors)) {
                $request->session()->flash('import_errors', $errors);

                return redirect()->route('mata-kuliah.index')
                    ->with('warning', "Berhasil mengimpor {$importedCount} data mata kuliah. {$skippedCount} data dilewati.");
            }

            if ($importedCount === 0 && !empty($errors)) {
                $request->session()->flash('import_errors', $errors);

                return redirect()->route('mata-kuliah.index')
                    ->with('error', 'Import gagal. Tidak ada data yang berhasil diimpor.');
            }

            return redirect()->route('mata-kuliah.index')
                ->with('info', 'Tidak ada data yang diimpor. File mungkin kosong.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mata-kuliah.index')
                ->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }

    /**
     * Get mata kuliah untuk AJAX/API
     */
    public function getMataKuliah(Request $request)
    {
        $query = MataKuliah::query();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kode_mata_kuliah', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('nama_mata_kuliah', 'LIKE', "%{$searchTerm}%");
            });
        }

        $mataKuliahs = $query->orderBy('kode_mata_kuliah')
            ->get()
            ->map(function ($item) {
                return [
                    'id_mata_kuliah' => $item->id_mata_kuliah,
                    'kode_mata_kuliah' => $item->kode_mata_kuliah,
                    'nama_mata_kuliah' => $item->nama_mata_kuliah,
                    'sks_mata_kuliah' => $item->sks_mata_kuliah,
                    'jenis_mata_kuliah' => $item->jenis_mata_kuliah,
                    'nama_lengkap' => $item->kode_mata_kuliah . ' - ' . $item->nama_mata_kuliah . ' (' . $item->sks_mata_kuliah . ' SKS)',
                ];
            });

        return response()->json($mataKuliahs);
    }
}
