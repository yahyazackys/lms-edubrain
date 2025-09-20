<?php

namespace App\Services;

use App\Models\KelasKuliah;
use App\Models\PesertaKelasKuliah;
use App\Models\NilaiPerkuliahan;
use App\Models\Absensi;
use App\Models\PengumpulanTugas;
use App\Models\PengumpulanUts;
use App\Models\PengumpulanUas;
use App\Models\SesiAbsensi;
use App\Models\Tugas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerhitunganNilaiAkhirService
{
    /**
     * Hitung nilai akhir semua mahasiswa dalam kelas
     * 
     * @param string $kelasId
     * @return array
     */
    public function calculateFinalGrades($kelasId)
    {
        $kelasKuliah = KelasKuliah::findOrFail($kelasId);
        $pesertaList = PesertaKelasKuliah::where('id_kelas_kuliah', $kelasId)
            ->where('status_mata_kuliah', 'APPROVED')
            ->with(['registrasiMahasiswa.mahasiswa.pengguna'])
            ->get();

        $results = [];

        foreach ($pesertaList as $peserta) {
            $mahasiswaId = $peserta->registrasiMahasiswa->id_mahasiswa;

            // Hitung komponen nilai
            $nilaiAbsensi = $this->calculateAttendanceGrade($kelasId, $mahasiswaId);
            $nilaiTugas = $this->calculateAssignmentGrade($kelasId, $mahasiswaId);
            $nilaiUts = $this->calculateUtsGrade($kelasId, $mahasiswaId);
            $nilaiUas = $this->calculateUasGrade($kelasId, $mahasiswaId);

            // Hitung nilai akhir
            $nilaiAkhir = $this->calculateWeightedGrade(
                $kelasKuliah,
                $nilaiAbsensi,
                $nilaiTugas,
                $nilaiUts,
                $nilaiUas
            );

            // Konversi ke indeks dan huruf
            $nilaiIndeks = $this->convertToGradePoint($nilaiAkhir);
            $nilaiHuruf = $this->convertToLetterGrade($nilaiAkhir);

            $results[] = [
                'id_peserta' => $peserta->id_peserta,
                'mahasiswa' => $peserta->registrasiMahasiswa->mahasiswa->pengguna->nama,
                'nim' => $peserta->registrasiMahasiswa->mahasiswa->nim,
                'komponen' => [
                    'absensi' => round($nilaiAbsensi, 2),
                    'tugas' => round($nilaiTugas, 2),
                    'uts' => round($nilaiUts, 2),
                    'uas' => round($nilaiUas, 2),
                ],
                'nilai_akhir' => round($nilaiAkhir, 2),
                'nilai_indeks' => $nilaiIndeks,
                'nilai_huruf' => $nilaiHuruf,
            ];
        }

        return $results;
    }

    /**
     * Hitung nilai absensi berdasarkan persentase kehadiran
     * 
     * @param string $kelasId
     * @param string $mahasiswaId
     * @return float
     */
    public function calculateAttendanceGrade($kelasId, $mahasiswaId)
    {
        // Hitung total sesi absensi
        $totalSesi = SesiAbsensi::where('id_kelas_kuliah', $kelasId)->count();

        if ($totalSesi == 0) {
            // Jika tidak ada sesi absensi, beri nilai penuh
            return 100.0;
        }

        // Hitung kehadiran mahasiswa
        $jumlahHadir = Absensi::whereHas('sesiAbsensi', function ($query) use ($kelasId) {
            $query->where('id_kelas_kuliah', $kelasId);
        })
            ->where('id_mahasiswa', $mahasiswaId)
            ->count();

        // Hitung persentase kehadiran
        $persentaseKehadiran = ($jumlahHadir / $totalSesi) * 100;

        return round($persentaseKehadiran, 2);
    }

    /**
     * Hitung rata-rata nilai tugas
     * 
     * @param string $kelasId
     * @param string $mahasiswaId
     * @return float
     */
    public function calculateAssignmentGrade($kelasId, $mahasiswaId)
    {
        // Ambil semua tugas di kelas ini
        $tugasList = Tugas::where('id_kelas_kuliah', $kelasId)->pluck('id_tugas');

        if ($tugasList->isEmpty()) {
            // Jika tidak ada tugas, beri nilai penuh
            return 100.0;
        }

        // Ambil pengumpulan mahasiswa yang sudah dinilai
        $pengumpulanList = PengumpulanTugas::whereIn('id_tugas', $tugasList)
            ->where('id_mahasiswa', $mahasiswaId)
            ->whereNotNull('nilai')
            ->pluck('nilai');

        if ($pengumpulanList->isEmpty()) {
            // Jika tidak ada pengumpulan atau semua belum dinilai
            return 0.0;
        }

        // Hitung rata-rata nilai tugas yang sudah dikumpulkan
        return round($pengumpulanList->average(), 2);
    }

    /**
     * Hitung nilai UTS
     * 
     * @param string $kelasId
     * @param string $mahasiswaId
     * @return float
     */
    public function calculateUtsGrade($kelasId, $mahasiswaId)
    {
        $utsId = DB::table('uts')
            ->where('id_kelas_kuliah', $kelasId)
            ->value('id_uts');

        if (!$utsId) {
            // Jika tidak ada UTS, beri nilai penuh
            return 100.0;
        }

        $pengumpulan = PengumpulanUts::where('id_uts', $utsId)
            ->where('id_mahasiswa', $mahasiswaId)
            ->first();

        if (!$pengumpulan || is_null($pengumpulan->nilai)) {
            // Jika tidak mengumpulkan atau belum dinilai
            return 0.0;
        }

        return (float) $pengumpulan->nilai;
    }

    /**
     * Hitung nilai UAS
     * 
     * @param string $kelasId
     * @param string $mahasiswaId
     * @return float
     */
    public function calculateUasGrade($kelasId, $mahasiswaId)
    {
        $uasId = DB::table('uas')
            ->where('id_kelas_kuliah', $kelasId)
            ->value('id_uas');

        if (!$uasId) {
            // Jika tidak ada UAS, beri nilai penuh
            return 100.0;
        }

        $pengumpulan = PengumpulanUas::where('id_uas', $uasId)
            ->where('id_mahasiswa', $mahasiswaId)
            ->first();

        if (!$pengumpulan || is_null($pengumpulan->nilai)) {
            // Jika tidak mengumpulkan atau belum dinilai
            return 0.0;
        }

        return (float) $pengumpulan->nilai;
    }

    /**
     * Hitung nilai tertimbang berdasarkan bobot
     * 
     * @param KelasKuliah $kelasKuliah
     * @param float $nilaiAbsensi
     * @param float $nilaiTugas
     * @param float $nilaiUts
     * @param float $nilaiUas
     * @return float
     */
    private function calculateWeightedGrade($kelasKuliah, $nilaiAbsensi, $nilaiTugas, $nilaiUts, $nilaiUas)
    {
        $totalBobot = $kelasKuliah->bobot_absensi + $kelasKuliah->bobot_tugas +
            $kelasKuliah->bobot_uts + $kelasKuliah->bobot_uas;

        if ($totalBobot == 0) {
            return 0.0;
        }

        $nilaiTertimbang = (
            ($nilaiAbsensi * $kelasKuliah->bobot_absensi / 100) +
            ($nilaiTugas * $kelasKuliah->bobot_tugas / 100) +
            ($nilaiUts * $kelasKuliah->bobot_uts / 100) +
            ($nilaiUas * $kelasKuliah->bobot_uas / 100)
        ) * (100 / $totalBobot);

        return $nilaiTertimbang;
    }

    /**
     * Konversi nilai angka ke indeks (skala 4.0)
     * 
     * @param float $nilaiAngka
     * @return float
     */
    private function convertToGradePoint($nilaiAngka)
    {
        if ($nilaiAngka >= 85) return 4.0;
        if ($nilaiAngka >= 80) return 3.7;
        if ($nilaiAngka >= 75) return 3.3;
        if ($nilaiAngka >= 70) return 3.0;
        if ($nilaiAngka >= 65) return 2.7;
        if ($nilaiAngka >= 60) return 2.3;
        if ($nilaiAngka >= 55) return 2.0;
        if ($nilaiAngka >= 50) return 1.7;
        if ($nilaiAngka >= 40) return 1.0;
        return 0.0;
    }

    /**
     * Konversi nilai angka ke huruf
     * 
     * @param float $nilaiAngka
     * @return string
     */
    private function convertToLetterGrade($nilaiAngka)
    {
        if ($nilaiAngka >= 85) return 'A';
        if ($nilaiAngka >= 80) return 'A-';
        if ($nilaiAngka >= 75) return 'B+';
        if ($nilaiAngka >= 70) return 'B';
        if ($nilaiAngka >= 65) return 'B-';
        if ($nilaiAngka >= 60) return 'C+';
        if ($nilaiAngka >= 55) return 'C';
        if ($nilaiAngka >= 50) return 'C-';
        if ($nilaiAngka >= 40) return 'D';
        return 'E';
    }

    /**
     * Simpan nilai ke database
     * 
     * @param string $idPeserta
     * @param float $nilaiAngka
     * @param float $nilaiIndeks
     * @param string $nilaiHuruf
     * @return void
     */
    public function saveGradeToDatabase($idPeserta, $nilaiAngka, $nilaiIndeks, $nilaiHuruf)
    {
        NilaiPerkuliahan::updateOrCreate(
            ['id_peserta' => $idPeserta],
            [
                'nilai_angka' => round($nilaiAngka, 2),
                'nilai_indeks' => $nilaiIndeks,
                'nilai_huruf' => $nilaiHuruf,
            ]
        );
    }

    /**
     * Validasi kelengkapan penilaian sebelum menghitung
     * 
     * @param string $kelasId
     * @return array
     */
    public function validateGradingCompleteness($kelasId)
    {
        $warnings = [];

        // Cek tugas yang belum dinilai
        $tugasBelumDinilai = DB::table('pengumpulan_tugas')
            ->join('tugas', 'pengumpulan_tugas.id_tugas', '=', 'tugas.id_tugas')
            ->where('tugas.id_kelas_kuliah', $kelasId)
            ->whereNull('pengumpulan_tugas.nilai')
            ->count();

        if ($tugasBelumDinilai > 0) {
            $warnings[] = "Ada {$tugasBelumDinilai} pengumpulan tugas yang belum dinilai";
        }

        // Cek UTS yang belum dinilai
        $utsBelumDinilai = DB::table('pengumpulan_uts')
            ->join('uts', 'pengumpulan_uts.id_uts', '=', 'uts.id_uts')
            ->where('uts.id_kelas_kuliah', $kelasId)
            ->whereNull('pengumpulan_uts.nilai')
            ->count();

        if ($utsBelumDinilai > 0) {
            $warnings[] = "Ada {$utsBelumDinilai} pengumpulan UTS yang belum dinilai";
        }

        // Cek UAS yang belum dinilai
        $uasBelumDinilai = DB::table('pengumpulan_uas')
            ->join('uas', 'pengumpulan_uas.id_uas', '=', 'uas.id_uas')
            ->where('uas.id_kelas_kuliah', $kelasId)
            ->whereNull('pengumpulan_uas.nilai')
            ->count();

        if ($uasBelumDinilai > 0) {
            $warnings[] = "Ada {$uasBelumDinilai} pengumpulan UAS yang belum dinilai";
        }

        return [
            'is_complete' => empty($warnings),
            'warnings' => $warnings
        ];
    }
}
