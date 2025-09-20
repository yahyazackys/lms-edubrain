<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\SesiAbsensi;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    /**
     * Scan QR Code dan tampilkan form absensi
     */
    public function scanQrCode($sesi)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return view('mahasiswa.scan-absensi', [
                'status' => 'auth_required',
                'message' => 'Anda harus login terlebih dahulu untuk melakukan absensi',
                'sesi' => null
            ]);
        }

        try {
            $sesiAbsensi = SesiAbsensi::with(['kelasKuliah.mataKuliah', 'kelasKuliah.dosen.pengguna'])->findOrFail($sesi);
            $user = Auth::user();

            // Ambil data mahasiswa berdasarkan user yang login
            $mahasiswa = Mahasiswa::with('pengguna')->where('id_pengguna', $user->id_pengguna)->first();

            if (!$mahasiswa) {
                return view('mahasiswa.scan-absensi', [
                    'status' => 'error',
                    'message' => 'Data mahasiswa tidak ditemukan. Pastikan Anda login sebagai mahasiswa.',
                    'sesi' => $sesiAbsensi
                ]);
            }

            // Cek apakah mahasiswa terdaftar di kelas ini
            $isPeserta = $sesiAbsensi->kelasKuliah
                ->pesertaKelasKuliah()
                ->whereHas('registrasiMahasiswa', function ($query) use ($mahasiswa) {
                    $query->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
                })
                ->exists();

            if (!$isPeserta) {
                return view('mahasiswa.scan-absensi', [
                    'status' => 'error',
                    'message' => 'Anda tidak terdaftar di kelas ini dan tidak dapat melakukan absensi',
                    'sesi' => $sesiAbsensi
                ]);
            }

            // Cek apakah sesi masih aktif
            if ($sesiAbsensi->status === 'ditutup') {
                return view('mahasiswa.scan-absensi', [
                    'status' => 'error',
                    'message' => 'Sesi absensi sudah ditutup oleh dosen',
                    'sesi' => $sesiAbsensi
                ]);
            }

            // Cek apakah sudah expired
            if ($sesiAbsensi->isExpired()) {
                return view('mahasiswa.scan-absensi', [
                    'status' => 'error',
                    'message' => 'Waktu absensi sudah habis',
                    'sesi' => $sesiAbsensi
                ]);
            }

            // Cek apakah sudah pernah absen
            $sudahAbsen = Absensi::where('id_sesi_absensi', $sesiAbsensi->id_sesi_absensi)
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->first();

            if ($sudahAbsen) {
                return view('mahasiswa.scan-absensi', [
                    'status' => 'success',
                    'message' => 'Absensi Anda sudah tercatat sebelumnya',
                    'sesi' => $sesiAbsensi,
                    'absensi' => $sudahAbsen,
                    'mahasiswa' => $mahasiswa
                ]);
            }

            // Tampilkan form absensi
            return view('mahasiswa.scan-absensi', [
                'status' => 'form',
                'sesi' => $sesiAbsensi,
                'mahasiswa' => $mahasiswa
            ]);
        } catch (\Exception $e) {
            return view('mahasiswa.scan-absensi', [
                'status' => 'error',
                'message' => 'Sesi absensi tidak ditemukan atau tidak valid',
                'error' => $e->getMessage(),
                'sesi' => null
            ]);
        }
    }

    /**
     * Submit absensi mahasiswa
     */
    public function submitAbsensi(Request $request)
    {
        // Check authentication
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu');
        }

        $validator = Validator::make($request->all(), [
            'id_sesi_absensi' => 'required|exists:sesi_absensi,id_sesi_absensi',
        ], [
            'id_sesi_absensi.required' => 'ID sesi absensi diperlukan',
            'id_sesi_absensi.exists' => 'Sesi absensi tidak valid',
        ]);

        if ($validator->fails()) {
            $sesiAbsensi = SesiAbsensi::with(['kelasKuliah.mataKuliah'])->find($request->id_sesi_absensi);
            return view('mahasiswa.scan-absensi', [
                'status' => 'error',
                'message' => 'Data tidak valid: ' . $validator->errors()->first(),
                'sesi' => $sesiAbsensi
            ]);
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $mahasiswa = Mahasiswa::with('pengguna')->where('id_pengguna', $user->id_pengguna)->first();
            $sesiAbsensi = SesiAbsensi::with(['kelasKuliah.mataKuliah', 'kelasKuliah.dosen.pengguna'])->findOrFail($request->id_sesi_absensi);

            // Validasi ulang
            if (!$mahasiswa) {
                throw new \Exception('Data mahasiswa tidak ditemukan');
            }

            // Cek apakah mahasiswa terdaftar di kelas ini
            $isPeserta = $sesiAbsensi->kelasKuliah
                ->pesertaKelasKuliah()
                ->whereHas('registrasiMahasiswa', function ($query) use ($mahasiswa) {
                    $query->where('id_mahasiswa', $mahasiswa->id_mahasiswa);
                })
                ->exists();

            if (!$isPeserta) {
                throw new \Exception('Anda tidak terdaftar di kelas ini');
            }

            if ($sesiAbsensi->status === 'ditutup') {
                throw new \Exception('Sesi absensi sudah ditutup');
            }

            if ($sesiAbsensi->isExpired()) {
                throw new \Exception('Waktu absensi sudah habis');
            }

            // Cek double absensi - Return success view instead of throwing exception
            $existingAbsensi = Absensi::where('id_sesi_absensi', $sesiAbsensi->id_sesi_absensi)
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->first();

            if ($existingAbsensi) {
                DB::rollback();
                return view('mahasiswa.scan-absensi', [
                    'status' => 'success',
                    'message' => 'Absensi Anda sudah tercatat sebelumnya',
                    'sesi' => $sesiAbsensi,
                    'absensi' => $existingAbsensi,
                    'mahasiswa' => $mahasiswa
                ]);
            }

            // Tentukan status kehadiran berdasarkan waktu
            $now = now();
            $isHadir = true;

            // Jika absen lebih dari 15 menit setelah sesi dibuat, masih dianggap hadir
            // tapi bisa ditambahkan logika terlambat jika diperlukan
            $batasWaktu = $sesiAbsensi->created_at->addMinutes(15);

            // Untuk saat ini, selama sesi masih aktif = hadir
            // Bisa disesuaikan logika bisnis sesuai kebutuhan

            // Simpan absensi dengan struktur tabel yang benar
            $absensi = Absensi::create([
                'id_sesi_absensi' => $sesiAbsensi->id_sesi_absensi,
                'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                'is_hadir' => $isHadir,
            ]);

            DB::commit();

            return view('mahasiswa.scan-absensi', [
                'status' => 'success',
                'message' => 'Absensi berhasil disimpan',
                'sesi' => $sesiAbsensi,
                'absensi' => $absensi,
                'mahasiswa' => $mahasiswa
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            $sesiAbsensi = SesiAbsensi::with(['kelasKuliah.mataKuliah'])->find($request->id_sesi_absensi);
            return view('mahasiswa.scan-absensi', [
                'status' => 'error',
                'message' => 'Gagal menyimpan absensi: ' . $e->getMessage(),
                'sesi' => $sesiAbsensi
            ]);
        }
    }
}
