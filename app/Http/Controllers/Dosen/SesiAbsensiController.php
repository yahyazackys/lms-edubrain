<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\SesiAbsensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SesiAbsensiController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_kelas_kuliah' => 'required|exists:kelas_kuliah,id_kelas_kuliah',
            'topik' => 'required|string|max:255',
            'batas_akhir_absensi' => 'required|date|after:now',
        ], [
            'id_kelas_kuliah.required' => 'Kelas kuliah harus dipilih',
            'id_kelas_kuliah.exists' => 'Kelas kuliah tidak valid',
            'topik.required' => 'Topik absensi harus diisi',
            'topik.max' => 'Topik maksimal 255 karakter',
            'batas_akhir_absensi.required' => 'Batas akhir absensi harus diisi',
            'batas_akhir_absensi.date' => 'Format tanggal tidak valid',
            'batas_akhir_absensi.after' => 'Batas akhir absensi harus lebih dari sekarang',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi');
        }

        try {
            $sesiAbsensi = SesiAbsensi::create([
                'id_kelas_kuliah' => $request->id_kelas_kuliah,
                'topik' => $request->topik,
                'batas_akhir_absensi' => $request->batas_akhir_absensi,
                'status' => 'dibuka',
            ]);

            return redirect()->back()->with('success', 'Sesi absensi berhasil dibuat dan dibuka');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat sesi absensi: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $sesiAbsensi = SesiAbsensi::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'topik' => 'required|string|max:255',
            'batas_akhir_absensi' => 'required|date|after:now',
        ], [
            'topik.required' => 'Topik absensi harus diisi',
            'topik.max' => 'Topik maksimal 255 karakter',
            'batas_akhir_absensi.required' => 'Batas akhir absensi harus diisi',
            'batas_akhir_absensi.date' => 'Format tanggal tidak valid',
            'batas_akhir_absensi.after' => 'Batas akhir absensi harus lebih dari sekarang',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi');
        }

        try {
            $sesiAbsensi->update([
                'topik' => $request->topik,
                'batas_akhir_absensi' => $request->batas_akhir_absensi,
            ]);

            return redirect()->back()->with('success', 'Sesi absensi berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui sesi absensi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $sesiAbsensi = SesiAbsensi::findOrFail($id);
            $sesiAbsensi->delete();

            return redirect()->back()->with('success', 'Sesi absensi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus sesi absensi: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status sesi absensi (buka/tutup)
     */
    public function toggleStatus($id)
    {
        try {
            $sesiAbsensi = SesiAbsensi::findOrFail($id);

            $newStatus = $sesiAbsensi->status === 'dibuka' ? 'ditutup' : 'dibuka';
            $sesiAbsensi->update(['status' => $newStatus]);

            $message = $newStatus === 'dibuka' ? 'Sesi absensi dibuka kembali' : 'Sesi absensi ditutup';

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR Code untuk sesi absensi (Real-time, tidak disimpan)
     */
    public function generateQRCode($id)
    {
        try {
            $sesiAbsensi = SesiAbsensi::findOrFail($id);

            // Generate URL untuk absensi mahasiswa - sesuai dengan route di web.php
            $absensiUrl = route('absensi.scan', ['sesi' => $sesiAbsensi->id_sesi_absensi]);

            $qrCode = null;
            $method = 'unknown';
            $debugInfo = [];

            // Method 1: Coba dengan library SimpleSoftwareIO
            try {
                if (class_exists('\SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                    // Generate QR dengan berbagai format untuk testing
                    $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)
                        ->format('svg')
                        ->generate($absensiUrl);

                    $debugInfo['svg_length'] = strlen($qrSvg);
                    $debugInfo['svg_preview'] = substr($qrSvg, 0, 100);
                    $debugInfo['is_string'] = is_string($qrSvg);
                    $debugInfo['is_empty'] = empty($qrSvg);

                    if (is_string($qrSvg) && !empty($qrSvg) && strlen($qrSvg) > 10) {
                        $qrCode = $qrSvg;
                        $method = 'library_svg';
                    }
                }
            } catch (\Exception $e) {
                $debugInfo['library_error'] = $e->getMessage();
            }

            // Method 2: Coba format PNG jika SVG gagal
            if (!$qrCode) {
                try {
                    if (class_exists('\SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                        $qrPng = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)
                            ->format('png')
                            ->generate($absensiUrl);

                        if ($qrPng) {
                            $base64 = base64_encode($qrPng);
                            $qrCode = '<img src="data:image/png;base64,' . $base64 . '" alt="QR Code" style="max-width: 300px; max-height: 300px;" />';
                            $method = 'library_png';
                        }
                    }
                } catch (\Exception $e) {
                    $debugInfo['png_error'] = $e->getMessage();
                }
            }

            // Method 3: Fallback ke QR API online
            if (!$qrCode || empty($qrCode)) {
                $encodedUrl = urlencode($absensiUrl);
                $qrCode = '<img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . $encodedUrl . '" alt="QR Code" style="max-width: 300px; max-height: 300px; border: 1px solid #ddd;" />';
                $method = 'api_fallback';
            }

            return response()->json([
                'success' => true,
                'qrcode' => $qrCode,
                'url' => $absensiUrl,
                'topik' => $sesiAbsensi->topik,
                'batas_akhir' => $sesiAbsensi->batas_akhir_absensi->format('d/m/Y H:i'),
                'status' => $sesiAbsensi->status,
                'is_active' => $sesiAbsensi->status === 'dibuka' && $sesiAbsensi->batas_akhir_absensi > now(),
                'debug' => [
                    'library_exists' => class_exists('\SimpleSoftwareIO\QrCode\Facades\QrCode'),
                    'qr_method' => $method,
                    'url_encoded' => $absensiUrl,
                    'qr_code_length' => strlen($qrCode),
                    'details' => $debugInfo
                ]
            ]);
        } catch (\Exception $e) {
            // Emergency fallback - generate QR menggunakan Google Charts API
            try {
                $sesiAbsensi = SesiAbsensi::findOrFail($id);
                $absensiUrl = route('absensi.scan', ['sesi' => $sesiAbsensi->id_sesi_absensi]);
                $encodedUrl = urlencode($absensiUrl);
                $emergencyQr = '<img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . $encodedUrl . '" alt="QR Code" style="max-width: 300px; max-height: 300px;" />';

                return response()->json([
                    'success' => true,
                    'qrcode' => $emergencyQr,
                    'url' => $absensiUrl,
                    'topik' => $sesiAbsensi->topik,
                    'batas_akhir' => $sesiAbsensi->batas_akhir_absensi->format('d/m/Y H:i'),
                    'status' => $sesiAbsensi->status,
                    'is_active' => $sesiAbsensi->status === 'dibuka' && $sesiAbsensi->batas_akhir_absensi > now(),
                    'debug' => [
                        'method' => 'emergency_fallback',
                        'original_error' => $e->getMessage()
                    ]
                ]);
            } catch (\Exception $fallbackError) {
                return response()->json([
                    'success' => false,
                    'message' => 'Semua metode QR Code gagal',
                    'debug' => [
                        'original_error' => $e->getMessage(),
                        'fallback_error' => $fallbackError->getMessage()
                    ]
                ], 500);
            }
        }
    }

    /**
     * Get sesi absensi status info dengan daftar mahasiswa lengkap
     */
    public function getStatusInfo($id)
    {
        try {
            $sesiAbsensi = SesiAbsensi::with(['absensi.mahasiswa.pengguna'])->findOrFail($id);

            // Get semua peserta kelas
            $pesertaKelas = $sesiAbsensi->kelasKuliah->pesertaKelasKuliah()
                ->with(['registrasiMahasiswa.mahasiswa.pengguna'])
                ->get();

            $totalMahasiswa = $pesertaKelas->count();
            $sudahAbsen = $sesiAbsensi->absensi()->count();
            $belumAbsen = $totalMahasiswa - $sudahAbsen;

            $isExpired = $sesiAbsensi->batas_akhir_absensi < now();
            $isClosed = $sesiAbsensi->status === 'ditutup';

            // Buat array daftar mahasiswa dengan status kehadiran
            $daftarMahasiswa = [];
            foreach ($pesertaKelas as $peserta) {
                $mahasiswa = $peserta->registrasiMahasiswa->mahasiswa;

                // Cari apakah mahasiswa ini sudah absen
                $absensi = $sesiAbsensi->absensi()
                    ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                    ->first();

                $daftarMahasiswa[] = [
                    'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                    'nim' => $mahasiswa->nim,
                    'nama' => $mahasiswa->pengguna->nama,
                    'angkatan' => $mahasiswa->angkatan,
                    'status_kehadiran' => $absensi ? $absensi->status_kehadiran : 'tidak_hadir',
                    'waktu_absen' => $absensi ? $absensi->created_at->format('H:i:s') : null,
                    'sudah_absen' => $absensi ? true : false
                ];
            }

            // Sort berdasarkan nama
            usort($daftarMahasiswa, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'total_mahasiswa' => $totalMahasiswa,
                    'sudah_absen' => $sudahAbsen,
                    'belum_absen' => $belumAbsen,
                    'is_expired' => $isExpired,
                    'is_closed' => $isClosed,
                    'status' => $sesiAbsensi->status,
                    'batas_akhir' => $sesiAbsensi->batas_akhir_absensi->format('d/m/Y H:i'),
                    'persentase_kehadiran' => $totalMahasiswa > 0 ? round(($sudahAbsen / $totalMahasiswa) * 100, 1) : 0,
                    'daftar_mahasiswa' => $daftarMahasiswa,
                    'topik' => $sesiAbsensi->topik
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data absensi'
            ], 500);
        }
    }

    /**
     * Export laporan absensi
     */
    public function exportAbsensi($id)
    {
        try {
            $sesiAbsensi = SesiAbsensi::with([
                'kelasKuliah',
                'absensi.mahasiswa.pengguna'
            ])->findOrFail($id);

            $pesertaKelas = $sesiAbsensi->kelasKuliah->pesertaKelasKuliah()
                ->with('mahasiswa.pengguna')
                ->get();

            return view('dosen.exports.absensi', compact('sesiAbsensi', 'pesertaKelas'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export absensi: ' . $e->getMessage());
        }
    }

    /**
     * Auto close expired sessions
     */
    public function autoCloseExpiredSessions()
    {
        try {
            $expiredSessions = SesiAbsensi::where('status', 'dibuka')
                ->where('batas_akhir_absensi', '<', now())
                ->get();

            foreach ($expiredSessions as $session) {
                $session->update(['status' => 'ditutup']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Auto close completed',
                'closed_sessions' => $expiredSessions->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat auto close'
            ], 500);
        }
    }
}
