<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MataKuliahController extends Controller
{
    /**
     * Tampilkan halaman mata kuliah dengan pencarian dan filter
     */
    public function index(Request $request): View
    {
        $query = MataKuliah::query();

        $mataKuliahs = $query->orderBy('kode_mata_kuliah')->paginate(10);

        // Get unique SKS values for filter
        $sksOptions = MataKuliah::select('sks_mata_kuliah')
            ->distinct()
            ->orderBy('sks_mata_kuliah')
            ->pluck('sks_mata_kuliah');

        return view('admin.mata-kuliah.index', compact('mataKuliahs', 'sksOptions'));
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
        ], [
            'kode_mata_kuliah.required' => 'Kode mata kuliah wajib diisi.',
            'kode_mata_kuliah.unique' => 'Kode mata kuliah sudah digunakan.',
            'nama_mata_kuliah.required' => 'Nama mata kuliah wajib diisi.',
            'nama_mata_kuliah.max' => 'Nama mata kuliah maksimal 150 karakter.',
            'sks_mata_kuliah.required' => 'Jumlah SKS wajib diisi.',
            'sks_mata_kuliah.min' => 'Jumlah SKS minimal 0.5.',
            'sks_mata_kuliah.max' => 'Jumlah SKS maksimal 8.',
            'sks_mata_kuliah.regex' => 'Format SKS tidak valid (contoh: 2 atau 2.5).',
        ]);

        MataKuliah::create([
            'id_mata_kuliah' => (string) Str::uuid(),
            'kode_mata_kuliah' => strtoupper($validated['kode_mata_kuliah']),
            'nama_mata_kuliah' => $validated['nama_mata_kuliah'],
            'sks_mata_kuliah' => $validated['sks_mata_kuliah'],
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
        ], [
            'kode_mata_kuliah.required' => 'Kode mata kuliah wajib diisi.',
            'kode_mata_kuliah.unique' => 'Kode mata kuliah sudah digunakan.',
            'nama_mata_kuliah.required' => 'Nama mata kuliah wajib diisi.',
            'nama_mata_kuliah.max' => 'Nama mata kuliah maksimal 150 karakter.',
            'sks_mata_kuliah.required' => 'Jumlah SKS wajib diisi.',
            'sks_mata_kuliah.min' => 'Jumlah SKS minimal 0.5.',
            'sks_mata_kuliah.max' => 'Jumlah SKS maksimal 8.',
            'sks_mata_kuliah.regex' => 'Format SKS tidak valid (contoh: 2 atau 2.5).',
        ]);

        $mataKuliah->update([
            'kode_mata_kuliah' => strtoupper($validated['kode_mata_kuliah']),
            'nama_mata_kuliah' => $validated['nama_mata_kuliah'],
            'sks_mata_kuliah' => $validated['sks_mata_kuliah'],
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

        // Cek apakah mata kuliah masih digunakan di kurikulum
        if ($mataKuliah->kurikulum()->exists()) {
            return back()->with('error', 'Mata kuliah tidak dapat dihapus karena masih digunakan dalam kurikulum.');
        }

        // Cek apakah mata kuliah masih digunakan di kelas kuliah
        if ($mataKuliah->kelasKuliah()->exists()) {
            return back()->with('error', 'Mata kuliah tidak dapat dihapus karena masih memiliki kelas kuliah.');
        }

        $mataKuliah->delete();

        return redirect()->route('mata-kuliah.index')
            ->with('success', "Mata kuliah \"{$mataKuliahName}\" berhasil dihapus.");
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
                    'nama_lengkap' => $item->kode_mata_kuliah . ' - ' . $item->nama_mata_kuliah . ' (' . $item->sks_mata_kuliah . ' SKS)',
                ];
            });

        return response()->json($mataKuliahs);
    }

    /**
     * Statistik mata kuliah
     */
    public function statistik()
    {
        $stats = [
            'total_mata_kuliah' => MataKuliah::count(),
            'rata_sks' => MataKuliah::avg('sks_mata_kuliah'),
            'total_sks' => MataKuliah::sum('sks_mata_kuliah'),
            'distribusi_sks' => MataKuliah::selectRaw('sks_mata_kuliah, COUNT(*) as jumlah')
                ->groupBy('sks_mata_kuliah')
                ->orderBy('sks_mata_kuliah')
                ->get(),
        ];

        return response()->json($stats);
    }
}
