<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramStudi;
use App\Models\JenjangPendidikan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProgramStudiController extends Controller
{
    /**
     * Tampilkan halaman program studi dengan filter jenjang pendidikan
     */
    public function index(Request $request): View
    {
        $jenjangs = JenjangPendidikan::orderBy('kode_jenjang_pendidikan')->get();

        // Ambil jenjang yang dipilih
        $selectedJenjangId = $request->get('jenjang');
        $selectedJenjang = null;
        $programStudis = collect();

        if ($selectedJenjangId) {
            $selectedJenjang = JenjangPendidikan::find($selectedJenjangId);

            if ($selectedJenjang) {
                $query = ProgramStudi::with('jenjang')
                    ->where('id_jenjang_pendidikan', $selectedJenjangId);

                // Filter berdasarkan status jika ada
                if ($request->has('status') && $request->status != '') {
                    $query->where('status', $request->status);
                }

                $programStudis = $query->orderBy('nama_program_studi')->paginate(10);
            }
        }

        return view('admin.program-studi.index', compact(
            'jenjangs',
            'selectedJenjang',
            'selectedJenjangId',
            'programStudis'
        ));
    }

    /**
     * Simpan program studi baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_program_studi' => 'required|string|max:20|unique:program_studi,kode_program_studi',
            'nama_program_studi' => 'required|string|max:100',
            'status' => 'required|in:A,N',
            'id_jenjang_pendidikan' => 'required|exists:jenjang_pendidikan,id_jenjang_pendidikan',
        ], [
            'kode_program_studi.required' => 'Kode program studi harus diisi.',
            'kode_program_studi.unique' => 'Kode program studi sudah digunakan.',
            'nama_program_studi.required' => 'Nama program studi harus diisi.',
            'nama_program_studi.max' => 'Nama program studi maksimal 100 karakter.',
            'status.required' => 'Status harus dipilih.',
            'status.in' => 'Status harus Aktif atau Non-aktif.',
            'id_jenjang_pendidikan.required' => 'Jenjang pendidikan harus dipilih.',
            'id_jenjang_pendidikan.exists' => 'Jenjang pendidikan tidak valid.',
        ]);

        // Cek duplikasi nama program studi di jenjang yang sama
        $exists = ProgramStudi::where('id_jenjang_pendidikan', $validated['id_jenjang_pendidikan'])
            ->where('nama_program_studi', $validated['nama_program_studi'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Program studi dengan nama tersebut sudah ada di jenjang pendidikan ini.');
        }

        ProgramStudi::create([
            'id_program_studi' => (string) Str::uuid(),
            'kode_program_studi' => strtoupper($validated['kode_program_studi']),
            'nama_program_studi' => $validated['nama_program_studi'],
            'status' => $validated['status'],
            'id_jenjang_pendidikan' => $validated['id_jenjang_pendidikan'],
        ]);

        return redirect()->route('program-studi.index', ['jenjang' => $validated['id_jenjang_pendidikan']])
            ->with('success', 'Program studi berhasil ditambahkan.');
    }

    /**
     * Update program studi
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $programStudi = ProgramStudi::findOrFail($id);

        $validated = $request->validate([
            'kode_program_studi' => 'required|string|max:20|unique:program_studi,kode_program_studi,' . $id . ',id_program_studi',
            'nama_program_studi' => 'required|string|max:100',
            'status' => 'required|in:A,N',
            'id_jenjang_pendidikan' => 'required|exists:jenjang_pendidikan,id_jenjang_pendidikan',
        ], [
            'kode_program_studi.required' => 'Kode program studi harus diisi.',
            'kode_program_studi.unique' => 'Kode program studi sudah digunakan.',
            'nama_program_studi.required' => 'Nama program studi harus diisi.',
            'nama_program_studi.max' => 'Nama program studi maksimal 100 karakter.',
            'status.required' => 'Status harus dipilih.',
            'status.in' => 'Status harus Aktif atau Non-aktif.',
            'id_jenjang_pendidikan.required' => 'Jenjang pendidikan harus dipilih.',
            'id_jenjang_pendidikan.exists' => 'Jenjang pendidikan tidak valid.',
        ]);

        // Cek duplikasi nama program studi di jenjang yang sama (kecuali data yang sedang diupdate)
        $exists = ProgramStudi::where('id_jenjang_pendidikan', $validated['id_jenjang_pendidikan'])
            ->where('nama_program_studi', $validated['nama_program_studi'])
            ->where('id_program_studi', '!=', $programStudi->id_program_studi)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Program studi dengan nama tersebut sudah ada di jenjang pendidikan ini.');
        }

        $programStudi->update([
            'kode_program_studi' => strtoupper($validated['kode_program_studi']),
            'nama_program_studi' => $validated['nama_program_studi'],
            'status' => $validated['status'],
            'id_jenjang_pendidikan' => $validated['id_jenjang_pendidikan'],
        ]);

        return redirect()->route('program-studi.index', ['jenjang' => $validated['id_jenjang_pendidikan']])
            ->with('success', 'Program studi berhasil diperbarui.');
    }

    /**
     * Hapus program studi
     */
    public function destroy(string $id): RedirectResponse
    {
        $programStudi = ProgramStudi::findOrFail($id);
        $programStudiName = $programStudi->nama_program_studi;
        $jenjangId = $programStudi->id_jenjang_pendidikan;

        // Cek apakah program studi masih digunakan di kurikulum
        if ($programStudi->kurikulum()->exists()) {
            return back()->with('error', 'Program studi tidak dapat dihapus karena masih memiliki kurikulum.');
        }

        // Cek apakah program studi masih digunakan di mahasiswa
        if ($programStudi->mahasiswa()->exists()) {
            return back()->with('error', 'Program studi tidak dapat dihapus karena masih memiliki mahasiswa.');
        }

        $programStudi->delete();

        return redirect()->route('program-studi.index', ['jenjang' => $jenjangId])
            ->with('success', "Program studi \"{$programStudiName}\" berhasil dihapus.");
    }

    /**
     * Get jenjang pendidikan untuk AJAX
     */
    public function getJenjang()
    {
        $jenjangs = JenjangPendidikan::orderBy('kode_jenjang_pendidikan')
            ->get()
            ->map(function ($item) {
                return [
                    'id_jenjang_pendidikan' => $item->id_jenjang_pendidikan,
                    'kode_jenjang_pendidikan' => $item->kode_jenjang_pendidikan,
                    'nama_jenjang_pendidikan' => $item->nama_jenjang_pendidikan,
                    'nama_lengkap' => $item->kode_jenjang_pendidikan . ' - ' . $item->nama_jenjang_pendidikan,
                ];
            });

        return response()->json($jenjangs);
    }

    /**
     * Statistik program studi berdasarkan jenjang
     */
    public function statistik(string $jenjangId)
    {
        $jenjang = JenjangPendidikan::findOrFail($jenjangId);

        $stats = [
            'total_program_studi' => ProgramStudi::where('id_jenjang_pendidikan', $jenjangId)->count(),
            'program_studi_aktif' => ProgramStudi::where('id_jenjang_pendidikan', $jenjangId)
                ->where('status', 'A')->count(),
            'program_studi_nonaktif' => ProgramStudi::where('id_jenjang_pendidikan', $jenjangId)
                ->where('status', 'N')->count(),
            'total_kurikulum' => ProgramStudi::where('id_jenjang_pendidikan', $jenjangId)
                ->withCount('kurikulum')
                ->get()
                ->sum('kurikulum_count'),
            'total_mahasiswa' => ProgramStudi::where('id_jenjang_pendidikan', $jenjangId)
                ->withCount('mahasiswa')
                ->get()
                ->sum('mahasiswa_count'),
        ];

        return response()->json($stats);
    }
}
