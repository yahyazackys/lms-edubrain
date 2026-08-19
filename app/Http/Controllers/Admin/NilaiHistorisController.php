<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Semester;
use App\Models\RegistrasiMahasiswa;
use App\Models\KelasKuliah;
use App\Models\Kurikulum;
use App\Models\PesertaKelasKuliah;
use App\Models\PesertaBimbingan;
use App\Models\NilaiPerkuliahan;
use App\Models\KurikulumMataKuliah;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NilaiHistorisController extends Controller
{
    /**
     * Export template Excel untuk import
     */
    public function exportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'NIM',
            'Kode Mata Kuliah',
            'Kode Semester',
            'Nilai Huruf',
            'Nilai Angka',
            'Nilai Indeks'
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

        // Format kolom E dan F (Nilai Angka dan Indeks) sebagai number dengan 2 desimal
        $sheet->getStyle('E:E')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('F:F')->getNumberFormat()->setFormatCode('0.00');

        $exampleData = [
            ['2021001', 'MAT101', '20231', 'A', 90.00, 4.00],
            ['2021001', 'ALG102', '20231', 'B', 77.50, 3.00],
            ['2021002', 'BD201', '20232', 'A-', 82.25, 3.50],
            ['2021002', 'SKRIPSI', '20241', 'A', 88.75, 4.00],
        ];

        $row = 2;
        foreach ($exampleData as $data) {
            $column = 'A';
            for ($i = 0; $i < count($data); $i++) {
                // Set nilai dengan tipe yang benar
                if ($i === 4 || $i === 5) { // Kolom Nilai Angka dan Indeks
                    $sheet->setCellValueExplicit($column . $row, $data[$i], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                } else {
                    $sheet->setCellValue($column . $row, $data[$i]);
                }
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

        $sheet->setCellValue('A' . $noteStartRow, '1. NIM: Nomor Induk Mahasiswa yang terdaftar di sistem (wajib diisi)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '2. Kode Mata Kuliah: Kode MK yang terdaftar di sistem (wajib diisi)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Contoh: MAT101, ALG102, SKRIPSI');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '3. Kode Semester: Format YYYYS (YYYY=tahun, S=1 atau 2) (wajib diisi)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '   Contoh: 20231 (Semester 1 tahun 2023), 20232 (Semester 2 tahun 2023)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '4. Nilai Huruf: A, A-, B, B-, C, D, atau E (wajib diisi, huruf KAPITAL)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '5. Nilai Angka: 0-100 dengan format desimal (contoh: 75.50, 88.25) (wajib diisi)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '6. Nilai Indeks: 0.0-4.0 dengan format desimal (contoh: 3.00, 3.50) (wajib diisi)');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '7. Ketiga kolom nilai (Huruf, Angka, Indeks) HARUS konsisten');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '8. Sistem akan auto-create registrasi semester jika belum ada');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '9. Jika nilai sudah ada, sistem tidak akan mengubah nilai');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '');
        $noteStartRow++;
        $sheet->setCellValue('A' . $noteStartRow, '10. Hapus 4 baris contoh di atas sebelum mengisi data sebenarnya');
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

        $filename = 'template_import_nilai_historis_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export data nilai historis
     */
    public function export()
    {
        $nilaiData = NilaiPerkuliahan::with([
            'pesertaKelasKuliah.registrasiMahasiswa.mahasiswa.pengguna',
            'pesertaKelasKuliah.kelasKuliah.semester',
            'pesertaKelasKuliah.kelasKuliah.kurikulumMataKuliah.mataKuliah',
            'pesertaBimbingan.registrasiMahasiswa.mahasiswa.pengguna',
            'pesertaBimbingan.registrasiMahasiswa.semester',
            'pesertaBimbingan.mataKuliah'
        ])->whereNotNull('nilai_huruf')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['NIM', 'Nama Mahasiswa', 'Kode Mata Kuliah', 'Nama Mata Kuliah', 'Kode Semester', 'Nilai Huruf', 'Nilai Angka', 'Nilai Indeks'];

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

        // Format kolom G dan H (Nilai Angka dan Indeks) sebagai number dengan 2 desimal
        $sheet->getStyle('G:G')->getNumberFormat()->setFormatCode('0.00');
        $sheet->getStyle('H:H')->getNumberFormat()->setFormatCode('0.00');

        $row = 2;
        foreach ($nilaiData as $nilai) {
            if ($nilai->jenis_peserta === 'KELAS') {
                $mahasiswa = $nilai->pesertaKelasKuliah->registrasiMahasiswa->mahasiswa;
                $mataKuliah = $nilai->pesertaKelasKuliah->kelasKuliah->kurikulumMataKuliah->mataKuliah;
                $semester = $nilai->pesertaKelasKuliah->kelasKuliah->semester;
            } else {
                $mahasiswa = $nilai->pesertaBimbingan->registrasiMahasiswa->mahasiswa;
                $mataKuliah = $nilai->pesertaBimbingan->mataKuliah;
                $semester = $nilai->pesertaBimbingan->registrasiMahasiswa->semester;
            }

            $sheet->setCellValue('A' . $row, $mahasiswa->nim);
            $sheet->setCellValue('B' . $row, $mahasiswa->pengguna->nama);
            $sheet->setCellValue('C' . $row, $mataKuliah->kode_mata_kuliah);
            $sheet->setCellValue('D' . $row, $mataKuliah->nama_mata_kuliah);
            $sheet->setCellValue('E' . $row, $semester->kode_semester);
            $sheet->setCellValue('F' . $row, $nilai->nilai_huruf);

            // Set nilai angka dan indeks sebagai numeric dengan format desimal
            $sheet->setCellValueExplicit('G' . $row, (float)$nilai->nilai_angka, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->setCellValueExplicit('H' . $row, (float)$nilai->nilai_indeks, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

            $row++;
        }

        $filename = 'data_nilai_historis_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Import nilai historis dari Excel
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120'
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
            $warnings = [];

            DB::beginTransaction();

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    $nim = trim((string)($row[0] ?? ''));
                    $kodeMK = strtoupper(trim((string)($row[1] ?? '')));
                    $kodeSemester = trim((string)($row[2] ?? ''));
                    $nilaiHuruf = strtoupper(trim((string)($row[3] ?? '')));
                    $nilaiAngka = trim($row[4] ?? '');
                    $nilaiIndeks = trim($row[5] ?? '');

                    // Validasi kolom wajib
                    if (empty($nim)) {
                        $errors[] = "Baris {$rowNumber}: NIM tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    if (empty($kodeMK)) {
                        $errors[] = "Baris {$rowNumber}: Kode Mata Kuliah tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    if (empty($kodeSemester)) {
                        $errors[] = "Baris {$rowNumber}: Kode Semester tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    if (empty($nilaiHuruf)) {
                        $errors[] = "Baris {$rowNumber}: Nilai Huruf tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    if ($nilaiAngka === '' || $nilaiAngka === null) {
                        $errors[] = "Baris {$rowNumber}: Nilai Angka tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    if ($nilaiIndeks === '' || $nilaiIndeks === null) {
                        $errors[] = "Baris {$rowNumber}: Nilai Indeks tidak boleh kosong";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi format nilai angka
                    if (!is_numeric($nilaiAngka) || $nilaiAngka < 0 || $nilaiAngka > 100) {
                        $errors[] = "Baris {$rowNumber}: Nilai Angka harus antara 0-100";
                        $skippedCount++;
                        continue;
                    }

                    // Validasi format nilai indeks
                    if (!is_numeric($nilaiIndeks) || $nilaiIndeks < 0 || $nilaiIndeks > 4) {
                        $errors[] = "Baris {$rowNumber}: Nilai Indeks harus antara 0.0-4.0";
                        $skippedCount++;
                        continue;
                    }

                    // Cari mahasiswa
                    $mahasiswa = Mahasiswa::where('nim', $nim)->first();
                    if (!$mahasiswa) {
                        $errors[] = "Baris {$rowNumber}: NIM '{$nim}' tidak ditemukan";
                        $skippedCount++;
                        continue;
                    }

                    // Cari mata kuliah
                    $mataKuliah = MataKuliah::where('kode_mata_kuliah', $kodeMK)->first();
                    if (!$mataKuliah) {
                        $errors[] = "Baris {$rowNumber}: Kode Mata Kuliah '{$kodeMK}' tidak ditemukan";
                        $skippedCount++;
                        continue;
                    }

                    // Cari semester
                    $semester = Semester::where('kode_semester', $kodeSemester)->first();
                    if (!$semester) {
                        $errors[] = "Baris {$rowNumber}: Kode Semester '{$kodeSemester}' tidak ditemukan";
                        $skippedCount++;
                        continue;
                    }

                    // Warning jika MK tidak ada di kurikulum mahasiswa
                    if ($mahasiswa->kurikulum) {
                        $mkDiKurikulum = KurikulumMataKuliah::where('id_kurikulum', $mahasiswa->kurikulum->id_kurikulum)
                            ->where('id_mata_kuliah', $mataKuliah->id_mata_kuliah)
                            ->exists();

                        if (!$mkDiKurikulum) {
                            $warnings[] = "Baris {$rowNumber}: Mata kuliah '{$kodeMK}' tidak ada di kurikulum mahasiswa NIM {$nim}, tetap di-import";
                        }
                    }

                    // Cari/buat registrasi semester
                    $registrasi = RegistrasiMahasiswa::firstOrCreate([
                        'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                        'id_semester' => $semester->id_semester
                    ], [
                        'id_registrasi_mahasiswa' => Str::uuid(),
                        'status_registrasi' => 'APPROVED',
                        'tanggal_registrasi' => now()
                    ]);

                    $jenisMK = $mataKuliah->jenis_mata_kuliah;

                    if (in_array($jenisMK, ['TEORI', 'PRAKTIKUM'])) {
                        // JALUR KELAS

                        // Validasi: Mahasiswa harus punya kurikulum
                        if (!$mahasiswa->kurikulum) {
                            $errors[] = "Baris {$rowNumber}: Mahasiswa NIM {$nim} tidak memiliki kurikulum";
                            $skippedCount++;
                            continue;
                        }

                        // Cari kurikulum mata kuliah
                        $kurikulumMK = KurikulumMataKuliah::where('id_kurikulum', $mahasiswa->kurikulum->id_kurikulum)
                            ->where('id_mata_kuliah', $mataKuliah->id_mata_kuliah)
                            ->first();

                        if (!$kurikulumMK) {
                            $errors[] = "Baris {$rowNumber}: Mata kuliah '{$kodeMK}' tidak ada di kurikulum mahasiswa NIM {$nim}";
                            $skippedCount++;
                            continue;
                        }

                        // Cari/buat kelas kuliah (dummy untuk historis)
                        $kelasKuliah = KelasKuliah::where('id_semester', $semester->id_semester)
                            ->where('id_kurikulum_mata_kuliah', $kurikulumMK->id)
                            ->first();

                        if (!$kelasKuliah) {
                            $kelasKuliah = KelasKuliah::create([
                                'id_kelas_kuliah' => Str::uuid(),
                                'id_semester' => $semester->id_semester,
                                'id_kurikulum_mata_kuliah' => $kurikulumMK->id,
                                'nama_kelas_kuliah' => 'Kelas Historis - ' . $mataKuliah->nama_mata_kuliah,
                                'status' => 'selesai',
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }

                        // Cari/buat peserta kelas
                        $peserta = PesertaKelasKuliah::where('id_kelas_kuliah', $kelasKuliah->id_kelas_kuliah)
                            ->where('id_registrasi_mahasiswa', $registrasi->id_registrasi_mahasiswa)
                            ->where('id_mata_kuliah', $mataKuliah->id_mata_kuliah)
                            ->first();

                        if (!$peserta) {
                            $peserta = PesertaKelasKuliah::create([
                                'id_peserta' => Str::uuid(),
                                'id_kelas_kuliah' => $kelasKuliah->id_kelas_kuliah,
                                'id_registrasi_mahasiswa' => $registrasi->id_registrasi_mahasiswa,
                                'id_mata_kuliah' => $mataKuliah->id_mata_kuliah,
                                'status_mata_kuliah' => 'APPROVED',
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }

                        // Cek apakah nilai sudah ada
                        $existingNilai = NilaiPerkuliahan::where('id_peserta', $peserta->id_peserta)
                            ->where('jenis_peserta', 'KELAS')
                            ->whereNotNull('nilai_huruf')
                            ->first();

                        if ($existingNilai) {
                            $warnings[] = "Baris {$rowNumber}: Nilai untuk NIM {$nim} mata kuliah '{$kodeMK}' semester {$kodeSemester} sudah ada, data dilewati";
                            $skippedCount++;
                            continue;
                        }

                        // Insert nilai baru
                        NilaiPerkuliahan::create([
                            'id_nilai_perkuliahan' => Str::uuid(),
                            'id_peserta' => $peserta->id_peserta,
                            'jenis_peserta' => 'KELAS',
                            'nilai_huruf' => $nilaiHuruf,
                            'nilai_angka' => $nilaiAngka,
                            'nilai_indeks' => $nilaiIndeks,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        $importedCount++;
                    } else {
                        // JALUR BIMBINGAN (KKN, MAGANG, SKRIPSI)

                        // Cari/buat peserta bimbingan
                        $peserta = PesertaBimbingan::where('id_registrasi_mahasiswa', $registrasi->id_registrasi_mahasiswa)
                            ->where('id_mata_kuliah', $mataKuliah->id_mata_kuliah)
                            ->first();

                        if (!$peserta) {
                            $peserta = PesertaBimbingan::create([
                                'id_peserta_bimbingan' => Str::uuid(),
                                'id_registrasi_mahasiswa' => $registrasi->id_registrasi_mahasiswa,
                                'id_mata_kuliah' => $mataKuliah->id_mata_kuliah,
                                'status_mata_kuliah' => 'APPROVED',
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }

                        // Cek apakah nilai sudah ada
                        $existingNilai = NilaiPerkuliahan::where('id_peserta_bimbingan', $peserta->id_peserta_bimbingan)
                            ->where('jenis_peserta', 'BIMBINGAN')
                            ->whereNotNull('nilai_huruf')
                            ->first();

                        if ($existingNilai) {
                            $warnings[] = "Baris {$rowNumber}: Nilai untuk NIM {$nim} mata kuliah '{$kodeMK}' semester {$kodeSemester} sudah ada, data dilewati";
                            $skippedCount++;
                            continue;
                        }

                        // Insert nilai baru
                        NilaiPerkuliahan::create([
                            'id_nilai_perkuliahan' => Str::uuid(),
                            'id_peserta_bimbingan' => $peserta->id_peserta_bimbingan,
                            'jenis_peserta' => 'BIMBINGAN',
                            'nilai_huruf' => $nilaiHuruf,
                            'nilai_angka' => $nilaiAngka,
                            'nilai_indeks' => $nilaiIndeks,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        $importedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                    $skippedCount++;
                }
            }

            DB::commit();

            $message = '';
            if ($importedCount > 0) {
                $message .= "Berhasil mengimpor {$importedCount} nilai baru. ";
            }
            if ($skippedCount > 0) {
                $message .= "{$skippedCount} data dilewati.";
            }

            if (!empty($errors)) {
                $request->session()->flash('import_errors', $errors);
            }

            if (!empty($warnings)) {
                $request->session()->flash('import_warnings', $warnings);
            }

            if ($importedCount === 0 && !empty($errors)) {
                return redirect()->back()->with('error', 'Import gagal. Tidak ada data yang berhasil diimpor.');
            }

            if ($importedCount > 0) {
                return redirect()->back()->with('success', $message);
            }

            return redirect()->back()->with('info', 'Tidak ada data yang diimpor.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }
}
