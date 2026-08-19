<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KknKelompok;
use App\Models\Dosen;
use App\Models\KknDokumentasi;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KknKelompokController extends Controller
{
    /**
     * Tampilkan halaman kelompok KKN dengan filter semester
     */
    public function index(Request $request): View
    {
        // Get semester yang dipilih
        $selectedSemesterId = $request->get('semester');
        $selectedSemester = null;
        $kelompokKkns = collect();

        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);

            if ($selectedSemester) {
                $query = KknKelompok::with([
                    'dpl.pengguna',
                    'dpl.programStudi',
                    'kknDetails.pesertaBimbingan.registrasiMahasiswa.mahasiswa.pengguna'
                ])->where('id_semester', $selectedSemesterId);

                // Pencarian
                if ($request->has('search') && $request->search != '') {
                    $searchTerm = $request->search;
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('nama_kelompok', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('lokasi', 'LIKE', "%{$searchTerm}%")
                            ->orWhereHas('dpl.pengguna', function ($dq) use ($searchTerm) {
                                $dq->where('nama', 'LIKE', "%{$searchTerm}%");
                            });
                    });
                }

                $kelompokKkns = $query->orderBy('nama_kelompok')->get();
            }
        }

        // Data untuk dropdown
        $semesters = Semester::orderByDesc('tanggal_mulai')->get();

        return view('admin.bimbingan.kkn.kelompok.index', compact(
            'kelompokKkns',
            'semesters',
            'selectedSemester',
            'selectedSemesterId'
        ));
    }

    /**
     * Simpan kelompok KKN baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelompok' => 'required|string|max:100',
            'lokasi' => 'required|string|max:200',
            'alamat_lokasi' => 'nullable|string',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after:periode_mulai',
            'target_program_kerja' => 'nullable|string',
            'id_dpl' => 'required|exists:dosen,id_dosen',
            'id_semester' => 'required|exists:semester,id_semester',
        ], [
            'nama_kelompok.required' => 'Nama kelompok wajib diisi.',
            'nama_kelompok.max' => 'Nama kelompok maksimal 100 karakter.',
            'lokasi.required' => 'Lokasi KKN wajib diisi.',
            'lokasi.max' => 'Lokasi KKN maksimal 200 karakter.',
            'periode_mulai.required' => 'Periode mulai wajib diisi.',
            'periode_mulai.date' => 'Format periode mulai tidak valid.',
            'periode_selesai.required' => 'Periode selesai wajib diisi.',
            'periode_selesai.date' => 'Format periode selesai tidak valid.',
            'periode_selesai.after' => 'Periode selesai harus setelah periode mulai.',
            'id_dpl.required' => 'DPL wajib dipilih.',
            'id_dpl.exists' => 'DPL tidak valid.',
        ]);

        // Cek duplikasi nama kelompok dalam periode yang sama
        $exists = KknKelompok::where('nama_kelompok', $validated['nama_kelompok'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('periode_mulai', [$validated['periode_mulai'], $validated['periode_selesai']])
                    ->orWhereBetween('periode_selesai', [$validated['periode_mulai'], $validated['periode_selesai']])
                    ->orWhere(function ($q2) use ($validated) {
                        $q2->where('periode_mulai', '<=', $validated['periode_mulai'])
                            ->where('periode_selesai', '>=', $validated['periode_selesai']);
                    });
            })
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Nama kelompok sudah ada dalam periode yang berpotongan.');
        }

        KknKelompok::create([
            'id_kelompok_kkn' => (string) Str::uuid(),
            'nama_kelompok' => $validated['nama_kelompok'],
            'lokasi' => $validated['lokasi'],
            'alamat_lokasi' => $validated['alamat_lokasi'],
            'periode_mulai' => $validated['periode_mulai'],
            'periode_selesai' => $validated['periode_selesai'],
            'target_program_kerja' => $validated['target_program_kerja'],
            'id_dpl' => $validated['id_dpl'],
            'id_semester' => $validated['id_semester'],
        ]);

        return redirect()->route('bimbingan.kkn.kelompok.index', ['semester' => $request->id_semester])
            ->with('success', 'Kelompok KKN berhasil ditambahkan.');
    }

    /**
     * Update kelompok KKN
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $kelompok = KknKelompok::findOrFail($id);

        $validated = $request->validate([
            'nama_kelompok' => 'required|string|max:100',
            'lokasi' => 'required|string|max:200',
            'alamat_lokasi' => 'nullable|string',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after:periode_mulai',
            'target_program_kerja' => 'nullable|string',
            'id_dpl' => 'required|exists:dosen,id_dosen',
            'id_semester' => 'required|exists:semester,id_semester',
        ], [
            'nama_kelompok.required' => 'Nama kelompok wajib diisi.',
            'nama_kelompok.max' => 'Nama kelompok maksimal 100 karakter.',
            'lokasi.required' => 'Lokasi KKN wajib diisi.',
            'lokasi.max' => 'Lokasi KKN maksimal 200 karakter.',
            'periode_mulai.required' => 'Periode mulai wajib diisi.',
            'periode_mulai.date' => 'Format periode mulai tidak valid.',
            'periode_selesai.required' => 'Periode selesai wajib diisi.',
            'periode_selesai.date' => 'Format periode selesai tidak valid.',
            'periode_selesai.after' => 'Periode selesai harus setelah periode mulai.',
            'id_dpl.required' => 'DPL wajib dipilih.',
            'id_dpl.exists' => 'DPL tidak valid.',
        ]);

        // Cek duplikasi nama kelompok (kecuali data yang sedang diupdate)
        $exists = KknKelompok::where('nama_kelompok', $validated['nama_kelompok'])
            ->where('id_kelompok_kkn', '!=', $kelompok->id_kelompok_kkn)
            ->where(function ($q) use ($validated) {
                $q->whereBetween('periode_mulai', [$validated['periode_mulai'], $validated['periode_selesai']])
                    ->orWhereBetween('periode_selesai', [$validated['periode_mulai'], $validated['periode_selesai']])
                    ->orWhere(function ($q2) use ($validated) {
                        $q2->where('periode_mulai', '<=', $validated['periode_mulai'])
                            ->where('periode_selesai', '>=', $validated['periode_selesai']);
                    });
            })
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Nama kelompok sudah ada dalam periode yang berpotongan.');
        }

        $kelompok->update($validated);

        return redirect()->route('bimbingan.kkn.kelompok.index', ['semester' => $request->id_semester])
            ->with('success', 'Kelompok KKN berhasil diperbarui.');
    }

    /**
     * Hapus kelompok KKN
     */
    public function destroy(string $id): RedirectResponse
    {
        $kelompok = KknKelompok::findOrFail($id);
        $kelompokName = $kelompok->nama_kelompok;

        // Cek apakah kelompok masih memiliki anggota
        if ($kelompok->kknDetails()->exists()) {
            return back()->with('error', 'Kelompok KKN tidak dapat dihapus karena masih memiliki anggota.');
        }

        $kelompok->delete();

        return back()->with('success', "Kelompok KKN \"{$kelompokName}\" berhasil dihapus.");
    }

    /**
     * Get dosen untuk search AJAX
     */
    public function getDosen(Request $request)
    {
        $query = Dosen::with('pengguna', 'programStudi');

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nidn', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('pengguna', function ($pq) use ($searchTerm) {
                        $pq->where('nama', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('programStudi', function ($psq) use ($searchTerm) {
                        $psq->where('nama_program_studi', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        $dosens = $query->get()
            ->sortBy('pengguna.nama')
            ->map(function ($item) {
                return [
                    'id_dosen' => $item->id_dosen,
                    'nidn' => $item->nidn,
                    'nama' => $item->pengguna->nama,
                    'program_studi' => $item->programStudi->nama_program_studi ?? '',
                    'nama_lengkap' => $item->pengguna->nama . ($item->nidn ? ' (' . $item->nidn . ')' : '') . ($item->programStudi ? ' - ' . $item->programStudi->nama_program_studi : '')
                ];
            })
            ->values();

        return response()->json($dosens);
    }

    /**
     * Get detail dosen
     */
    public function getDosenDetail($id)
    {
        $dosen = Dosen::with('pengguna', 'programStudi')->findOrFail($id);

        return response()->json([
            'id_dosen' => $dosen->id_dosen,
            'nama_lengkap' => $dosen->pengguna->nama . ($dosen->nidn ? ' (' . $dosen->nidn . ')' : '') .
                ($dosen->programStudi ? ' - ' . $dosen->programStudi->nama_program_studi : '')
        ]);
    }
    /**
     * Tampilkan detail kelompok dengan tab peserta dan dokumentasi
     */
    public function show(string $id): View
    {
        $kelompok = KknKelompok::with([
            'semester',
            'dpl.pengguna',
            'kknDetails.pesertaBimbingan.registrasiMahasiswa.mahasiswa.pengguna',
            'kknDetails.pesertaBimbingan.registrasiMahasiswa.mahasiswa.programStudi'
        ])->findOrFail($id);

        // Get dokumentasi
        $dokumentasi = [
            'images' => KknDokumentasi::where('id_kelompok_kkn', $id)
                ->where('file_type', 'image')
                ->orderBy('created_at', 'desc')
                ->get(),
            'documents' => KknDokumentasi::where('id_kelompok_kkn', $id)
                ->where('file_type', 'document')
                ->orderBy('created_at', 'desc')
                ->get()
        ];

        $dokumentasi_count = $dokumentasi['images']->count() + $dokumentasi['documents']->count();

        return view('admin.bimbingan.kkn.kelompok.detail', compact('kelompok', 'dokumentasi', 'dokumentasi_count'));
    }

    /**
     * Get available peserta untuk kelompok
     */
    public function getAvailablePeserta(string $kelompokId)
    {
        $kelompok = KknKelompok::findOrFail($kelompokId);

        // Get peserta yang sudah terdaftar di kelompok ini
        $existingPesertaIds = \App\Models\KknDetail::where('id_kelompok_kkn', $kelompokId)
            ->pluck('id_peserta_bimbingan')
            ->toArray();

        // Get peserta KKN yang belum masuk kelompok untuk semester ini
        $availablePeserta = \App\Models\PesertaBimbingan::with([
            'registrasiMahasiswa.mahasiswa.pengguna',
            'registrasiMahasiswa.mahasiswa.programStudi'
        ])
            ->where('jenis_bimbingan', 'KKN')
            ->whereHas('registrasiMahasiswa', function ($q) use ($kelompok) {
                $q->where('id_semester', $kelompok->id_semester);
            })
            ->whereNotIn('id_peserta_bimbingan', $existingPesertaIds)
            ->get()
            ->map(function ($peserta) {
                return [
                    'id_peserta_bimbingan' => $peserta->id_peserta_bimbingan,
                    'nama' => $peserta->registrasiMahasiswa->mahasiswa->pengguna->nama,
                    'nim' => $peserta->registrasiMahasiswa->mahasiswa->nim,
                    'program_studi' => $peserta->registrasiMahasiswa->mahasiswa->programStudi->nama_program_studi ?? ''
                ];
            });

        return response()->json($availablePeserta);
    }
}