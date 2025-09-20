<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;
use App\Models\Semester;
use App\Models\PesertaKelasKuliah;
use Carbon\Carbon;

class JadwalKuliahController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        if (!$mahasiswa) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        // Get semester yang dipilih dari request (tidak ada default selection)
        $selectedSemesterId = $request->get('semester');
        $selectedSemester = null;
        $jadwalPerHari = [];

        // Hanya load jadwal jika semester sudah dipilih
        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);

            if ($selectedSemester) {
                // Ambil jadwal kuliah mahasiswa untuk semester yang dipilih
                $jadwalKuliah = PesertaKelasKuliah::with([
                    'kelasKuliah.mataKuliah',
                    'kelasKuliah.dosen.pengguna',
                    'registrasiMahasiswa'
                ])
                    ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswa, $selectedSemesterId) {
                        $q->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                            ->where('id_semester', $selectedSemesterId);
                    })
                    ->where('status_mata_kuliah', 'APPROVED') // Hanya yang sudah disetujui
                    ->get();

                // Group jadwal berdasarkan hari
                $jadwalPerHari = $this->groupJadwalPerHari($jadwalKuliah);
            }
        }

        // Data untuk dropdown semester - urutkan dari yang terbaru
        $semesters = Semester::orderByDesc('kode_semester')->where('is_active', true)->get();

        return view('mahasiswa.jadwal.index', compact([
            'jadwalPerHari',
            'semesters',
            'selectedSemester',
            'selectedSemesterId',
            'mahasiswa'
        ]));
    }

    private function groupJadwalPerHari($jadwalKuliah)
    {
        $jadwalPerHari = [
            'SENIN' => [],
            'SELASA' => [],
            'RABU' => [],
            'KAMIS' => [],
            'JUMAT' => [],
            'SABTU' => []
        ];

        foreach ($jadwalKuliah as $jadwal) {
            $hari = $jadwal->kelasKuliah->hari;
            if (isset($jadwalPerHari[$hari])) {
                $jadwalPerHari[$hari][] = [
                    'id_kelas_kuliah' => $jadwal->kelasKuliah->id_kelas_kuliah,
                    'kode_mata_kuliah' => $jadwal->kelasKuliah->mataKuliah->kode_mata_kuliah,
                    'nama_mata_kuliah' => $jadwal->kelasKuliah->mataKuliah->nama_mata_kuliah,
                    'nama_kelas' => $jadwal->kelasKuliah->nama_kelas_kuliah,
                    'sks' => $jadwal->kelasKuliah->mataKuliah->sks_mata_kuliah,
                    'jam_mulai' => $jadwal->kelasKuliah->jam_mulai,
                    'jam_akhir' => $jadwal->kelasKuliah->jam_akhir,
                    'ruangan' => $jadwal->kelasKuliah->nama_ruangan,
                    'status' => $jadwal->kelasKuliah->status,
                    'dosen' => $jadwal->kelasKuliah->dosen->pengguna->nama ?? 'N/A',
                    'nidn' => $jadwal->kelasKuliah->dosen->nidn ?? 'N/A'
                ];
            }
        }

        // Sort jadwal per hari berdasarkan jam mulai
        foreach ($jadwalPerHari as $hari => $jadwals) {
            usort($jadwalPerHari[$hari], function ($a, $b) {
                return $a['jam_mulai'] <=> $b['jam_mulai'];
            });
        }

        return $jadwalPerHari;
    }

    public function printJadwal(Request $request)
    {
        // Logic sama dengan index() tapi return view untuk print
        // Implementation untuk print/export jadwal
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_pengguna', $user->id_pengguna)->first();

        if (!$mahasiswa) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        $selectedSemesterId = $request->get('semester');

        if (!$selectedSemesterId) {
            return redirect()->back()->with('error', 'Pilih semester terlebih dahulu untuk mencetak jadwal!');
        }

        $selectedSemester = Semester::find($selectedSemesterId);

        if (!$selectedSemester) {
            return redirect()->back()->with('error', 'Semester tidak ditemukan!');
        }

        // Ambil jadwal kuliah mahasiswa untuk semester yang dipilih
        $jadwalKuliah = PesertaKelasKuliah::with([
            'kelasKuliah.mataKuliah',
            'kelasKuliah.dosen.pengguna',
            'registrasiMahasiswa'
        ])
            ->whereHas('registrasiMahasiswa', function ($q) use ($mahasiswa, $selectedSemesterId) {
                $q->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                    ->where('id_semester', $selectedSemesterId);
            })
            ->where('status_mata_kuliah', 'APPROVED')
            ->get();

        $jadwalPerHari = $this->groupJadwalPerHari($jadwalKuliah);

        return view('mahasiswa.jadwal.print', compact([
            'jadwalPerHari',
            'selectedSemester',
            'mahasiswa'
        ]));
    }
}
