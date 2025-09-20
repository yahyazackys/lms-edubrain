<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KurikulumMataKuliahController extends Controller
{
    /**
     * Tampilkan halaman manajemen mata kuliah dalam kurikulum
     * URL: /kurikulum/{kurikulumId}/mata-kuliah
     */
    public function index(Request $request, string $kurikulumId): View
    {
        $kurikulum = Kurikulum::with(['semester', 'programStudi.jenjang'])
            ->findOrFail($kurikulumId);

        // Ambil mata kuliah yang sudah ada dalam kurikulum, grouped by tingkat semester
        $mataKuliahKurikulum = $kurikulum->mataKuliah()
            ->orderBy('kurikulum_mata_kuliah.semester')
            ->orderBy('kurikulum_mata_kuliah.jenis_mata_kuliah')
            ->get()
            ->groupBy('pivot.semester');

        // Ambil semua mata kuliah yang belum ada dalam kurikulum (untuk dropdown)
        $existingMataKuliahIds = $kurikulum->mataKuliah()->pluck('kurikulum_mata_kuliah.id_mata_kuliah');
        $availableMataKuliah = MataKuliah::whereNotIn('id_mata_kuliah', $existingMataKuliahIds)
            ->orderBy('kode_mata_kuliah')
            ->get();

        // Hitung statistik
        $statistics = [
            'total_mata_kuliah' => $kurikulum->mataKuliah()->count(),
            'mata_kuliah_wajib' => $kurikulum->mataKuliah()
                ->wherePivot('jenis_mata_kuliah', 'WAJIB')->count(),
            'mata_kuliah_pilihan' => $kurikulum->mataKuliah()
                ->wherePivot('jenis_mata_kuliah', 'PILIHAN')->count(),
            'total_sks' => $kurikulum->mataKuliah()
                ->sum('mata_kuliah.sks_mata_kuliah'),
            'sks_wajib' => $kurikulum->mataKuliah()
                ->wherePivot('jenis_mata_kuliah', 'WAJIB')
                ->sum('mata_kuliah.sks_mata_kuliah'),
            'sks_pilihan' => $kurikulum->mataKuliah()
                ->wherePivot('jenis_mata_kuliah', 'PILIHAN')
                ->sum('mata_kuliah.sks_mata_kuliah'),
        ];

        return view('admin.kurikulum.mata-kuliah', compact(
            'kurikulum',
            'mataKuliahKurikulum',
            'availableMataKuliah',
            'statistics'
        ));
    }

    /**
     * Tambah mata kuliah ke kurikulum
     */
    public function store(Request $request, string $kurikulumId): RedirectResponse
    {
        $kurikulum = Kurikulum::findOrFail($kurikulumId);

        $validated = $request->validate([
            'mata_kuliah' => 'required|array|min:1',
            'mata_kuliah.*' => 'exists:mata_kuliah,id_mata_kuliah',
            'semester' => 'required|integer|min:1|max:14',
            'jenis_mata_kuliah' => 'required|in:WAJIB,PILIHAN',
        ], [
            'mata_kuliah.required' => 'Pilih minimal satu mata kuliah.',
            'mata_kuliah.*.exists' => 'Mata kuliah tidak valid.',
            'semester.required' => 'Semester wajib dipilih.',
            'semester.min' => 'Semester minimal 1.',
            'semester.max' => 'Semester maksimal 14.',
            'jenis_mata_kuliah.required' => 'Jenis mata kuliah wajib dipilih.',
            'jenis_mata_kuliah.in' => 'Jenis mata kuliah harus WAJIB atau PILIHAN.',
        ]);

        $attachData = [];
        foreach ($validated['mata_kuliah'] as $mataKuliahId) {
            // Cek apakah mata kuliah sudah ada dalam kurikulum
            if ($kurikulum->mataKuliah()->where('kurikulum_mata_kuliah.id_mata_kuliah', $mataKuliahId)->exists()) {
                $mataKuliah = MataKuliah::find($mataKuliahId);
                return back()->withInput()
                    ->with('error', "Mata kuliah {$mataKuliah->nama_mata_kuliah} sudah ada dalam kurikulum ini.");
            }

            $attachData[$mataKuliahId] = [
                'id' => (string) Str::uuid(),
                'semester' => $validated['semester'],
                'jenis_mata_kuliah' => $validated['jenis_mata_kuliah']
            ];
        }

        // Attach mata kuliah ke kurikulum
        $kurikulum->mataKuliah()->attach($attachData);

        $jumlahMataKuliah = count($validated['mata_kuliah']);
        $jenisMataKuliah = strtolower($validated['jenis_mata_kuliah']);

        $message = $jumlahMataKuliah === 1
            ? "Mata kuliah {$jenisMataKuliah} berhasil ditambahkan ke semester {$validated['semester']}"
            : "{$jumlahMataKuliah} mata kuliah {$jenisMataKuliah} berhasil ditambahkan ke semester {$validated['semester']}";

        return redirect()->route('kurikulum.mata-kuliah.index', $kurikulumId)
            ->with('success', $message);
    }

    /**
     * Update semester dan jenis mata kuliah dalam kurikulum
     */
    public function update(Request $request, string $kurikulumId, string $mataKuliahId): RedirectResponse
    {
        $kurikulum = Kurikulum::findOrFail($kurikulumId);

        $validated = $request->validate([
            'semester' => 'required|integer|min:1|max:14',
            'jenis_mata_kuliah' => 'required|in:WAJIB,PILIHAN',
        ], [
            'semester.required' => 'Semester wajib dipilih.',
            'semester.min' => 'Semester minimal 1.',
            'semester.max' => 'Semester maksimal 14.',
            'jenis_mata_kuliah.required' => 'Jenis mata kuliah wajib dipilih.',
            'jenis_mata_kuliah.in' => 'Jenis mata kuliah harus WAJIB atau PILIHAN.',
        ]);

        // Update pivot data
        $kurikulum->mataKuliah()->updateExistingPivot($mataKuliahId, [
            'semester' => $validated['semester'],
            'jenis_mata_kuliah' => $validated['jenis_mata_kuliah']
        ]);

        $mataKuliah = MataKuliah::find($mataKuliahId);
        return redirect()->route('kurikulum.mata-kuliah.index', $kurikulumId)
            ->with('success', "Data mata kuliah {$mataKuliah->nama_mata_kuliah} berhasil diperbarui.");
    }

    /**
     * Hapus mata kuliah dari kurikulum
     */
    public function destroy(string $kurikulumId, string $mataKuliahId): RedirectResponse
    {
        $kurikulum = Kurikulum::findOrFail($kurikulumId);
        $mataKuliah = MataKuliah::findOrFail($mataKuliahId);

        // Detach mata kuliah dari kurikulum
        $kurikulum->mataKuliah()->detach($mataKuliahId);

        return redirect()->route('kurikulum.mata-kuliah.index', $kurikulumId)
            ->with('success', "Mata kuliah {$mataKuliah->nama_mata_kuliah} berhasil dihapus dari kurikulum.");
    }

    /**
     * Get mata kuliah untuk AJAX
     */
    public function getMataKuliah(Request $request, string $kurikulumId)
    {
        $kurikulum = Kurikulum::findOrFail($kurikulumId);

        // Ambil mata kuliah yang belum ada dalam kurikulum
        $existingMataKuliahIds = $kurikulum->mataKuliah()->pluck('mata_kuliah.id_mata_kuliah');

        $query = MataKuliah::whereNotIn('id_mata_kuliah', $existingMataKuliahIds);

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
     * Get statistik kurikulum untuk AJAX
     */
    public function getStatistics(string $kurikulumId)
    {
        $kurikulum = Kurikulum::findOrFail($kurikulumId);

        $statistics = [
            'total_mata_kuliah' => $kurikulum->mataKuliah()->count(),
            'mata_kuliah_wajib' => $kurikulum->mataKuliah()
                ->wherePivot('jenis_mata_kuliah', 'WAJIB')->count(),
            'mata_kuliah_pilihan' => $kurikulum->mataKuliah()
                ->wherePivot('jenis_mata_kuliah', 'PILIHAN')->count(),
            'total_sks' => $kurikulum->mataKuliah()
                ->join('mata_kuliah', 'kurikulum_mata_kuliah.id_mata_kuliah', '=', 'mata_kuliah.id_mata_kuliah')
                ->sum('mata_kuliah.sks_mata_kuliah'),
            'sks_wajib' => $kurikulum->mataKuliah()
                ->join('mata_kuliah', 'kurikulum_mata_kuliah.id_mata_kuliah', '=', 'mata_kuliah.id_mata_kuliah')
                ->where('kurikulum_mata_kuliah.jenis_mata_kuliah', 'WAJIB')
                ->sum('mata_kuliah.sks_mata_kuliah'),
            'sks_pilihan' => $kurikulum->mataKuliah()
                ->join('mata_kuliah', 'kurikulum_mata_kuliah.id_mata_kuliah', '=', 'mata_kuliah.id_mata_kuliah')
                ->where('kurikulum_mata_kuliah.jenis_mata_kuliah', 'PILIHAN')
                ->sum('mata_kuliah.sks_mata_kuliah'),
        ];

        return response()->json($statistics);
    }

    /**
     * Bulk update jenis mata kuliah
     */
    public function bulkUpdateJenis(Request $request, string $kurikulumId): RedirectResponse
    {
        $kurikulum = Kurikulum::findOrFail($kurikulumId);

        $validated = $request->validate([
            'mata_kuliah_ids' => 'required|array|min:1',
            'mata_kuliah_ids.*' => 'exists:mata_kuliah,id_mata_kuliah',
            'jenis_mata_kuliah' => 'required|in:WAJIB,PILIHAN',
        ], [
            'mata_kuliah_ids.required' => 'Pilih minimal satu mata kuliah.',
            'mata_kuliah_ids.*.exists' => 'Mata kuliah tidak valid.',
            'jenis_mata_kuliah.required' => 'Jenis mata kuliah wajib dipilih.',
            'jenis_mata_kuliah.in' => 'Jenis mata kuliah harus WAJIB atau PILIHAN.',
        ]);

        // Update jenis mata kuliah secara bulk
        DB::table('kurikulum_mata_kuliah')
            ->where('id_kurikulum', $kurikulumId)
            ->whereIn('id_mata_kuliah', $validated['mata_kuliah_ids'])
            ->update([
                'jenis_mata_kuliah' => $validated['jenis_mata_kuliah'],
                'updated_at' => now()
            ]);

        $jumlahMataKuliah = count($validated['mata_kuliah_ids']);
        $jenisMataKuliah = strtolower($validated['jenis_mata_kuliah']);

        $message = $jumlahMataKuliah === 1
            ? "1 mata kuliah berhasil diubah menjadi mata kuliah {$jenisMataKuliah}"
            : "{$jumlahMataKuliah} mata kuliah berhasil diubah menjadi mata kuliah {$jenisMataKuliah}";

        return redirect()->route('kurikulum.mata-kuliah.index', $kurikulumId)
            ->with('success', $message);
    }
}
