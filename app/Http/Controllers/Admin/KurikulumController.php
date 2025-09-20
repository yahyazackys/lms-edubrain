<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kurikulum;
use App\Models\Semester;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KurikulumController extends Controller
{
    /**
     * Tampilkan halaman kurikulum dengan filter semester
     */
    public function index(Request $request): View
    {
        $semesters = Semester::orderBy('kode_semester', 'desc')->get();
        $activeSemester = Semester::where('is_active', true)->first();

        // Default ke semester aktif jika tidak ada filter
        $selectedSemesterId = $request->get('semester');
        $selectedSemester = null;
        $kurikulums = collect();

        if ($selectedSemesterId) {
            $selectedSemester = Semester::find($selectedSemesterId);

            if ($selectedSemester) {
                $query = Kurikulum::with(['programStudi.jenjang', 'semester'])
                    ->where('id_semester', $selectedSemesterId);

                // Filter berdasarkan program studi jika ada
                if ($request->has('program_studi') && $request->program_studi != '') {
                    $query->where('id_program_studi', $request->program_studi);
                }

                $kurikulums = $query->orderBy('nama_kurikulum')->paginate(10);
            }
        }

        $programStudis = ProgramStudi::with('jenjang')
            ->where('status', 'A')
            ->orderBy('nama_program_studi')
            ->get();

        return view('admin.kurikulum.index', compact(
            'semesters',
            'activeSemester',
            'selectedSemester',
            'selectedSemesterId',
            'kurikulums',
            'programStudis'
        ));
    }

    /**
     * Simpan kurikulum baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kurikulum' => 'required|string|max:100',
            'jumlah_sks_lulus' => 'required|integer|min:0|max:200',
            'jumlah_sks_wajib' => 'required|integer|min:0|max:200',
            'jumlah_sks_pilihan' => 'required|integer|min:0|max:200',
            'id_program_studi' => 'required|exists:program_studi,id_program_studi',
            'id_semester' => 'required|exists:semester,id_semester',
        ]);

        // Validasi total SKS
        $totalSks = $validated['jumlah_sks_wajib'] + $validated['jumlah_sks_pilihan'];
        if ($totalSks > $validated['jumlah_sks_lulus']) {
            return back()->withInput()->with('error', 'Total SKS Wajib dan Pilihan tidak boleh melebihi SKS Lulus.');
        }

        // Cek duplikasi kurikulum di semester dan program studi yang sama
        $exists = Kurikulum::where('id_semester', $validated['id_semester'])
            ->where('id_program_studi', $validated['id_program_studi'])
            ->where('nama_kurikulum', $validated['nama_kurikulum'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Kurikulum dengan nama tersebut sudah ada di semester dan program studi ini.');
        }

        Kurikulum::create([
            'id_kurikulum' => (string) Str::uuid(),
            'nama_kurikulum' => $validated['nama_kurikulum'],
            'jumlah_sks_lulus' => $validated['jumlah_sks_lulus'],
            'jumlah_sks_wajib' => $validated['jumlah_sks_wajib'],
            'jumlah_sks_pilihan' => $validated['jumlah_sks_pilihan'],
            'id_program_studi' => $validated['id_program_studi'],
            'id_semester' => $validated['id_semester'],
        ]);

        return redirect()->route('kurikulum.index', ['semester' => $validated['id_semester']])
            ->with('success', 'Kurikulum berhasil ditambahkan.');
    }

    /**
     * Update kurikulum
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $kurikulum = Kurikulum::findOrFail($id);

        $validated = $request->validate([
            'nama_kurikulum' => 'required|string|max:100',
            'jumlah_sks_lulus' => 'required|integer|min:0|max:200',
            'jumlah_sks_wajib' => 'required|integer|min:0|max:200',
            'jumlah_sks_pilihan' => 'required|integer|min:0|max:200',
            'id_program_studi' => 'required|exists:program_studi,id_program_studi',
            'id_semester' => 'required|exists:semester,id_semester',
        ]);

        // Validasi total SKS
        $totalSks = $validated['jumlah_sks_wajib'] + $validated['jumlah_sks_pilihan'];
        if ($totalSks > $validated['jumlah_sks_lulus']) {
            return back()->withInput()->with('error', 'Total SKS Wajib dan Pilihan tidak boleh melebihi SKS Lulus.');
        }

        // Cek duplikasi kurikulum (kecuali data yang sedang diupdate)
        $exists = Kurikulum::where('id_semester', $validated['id_semester'])
            ->where('id_program_studi', $validated['id_program_studi'])
            ->where('nama_kurikulum', $validated['nama_kurikulum'])
            ->where('id_kurikulum', '!=', $kurikulum->id_kurikulum)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Kurikulum dengan nama tersebut sudah ada di semester dan program studi ini.');
        }

        $kurikulum->update($validated);

        return redirect()->route('kurikulum.index', ['semester' => $validated['id_semester']])
            ->with('success', 'Kurikulum berhasil diperbarui.');
    }

    /**
     * Hapus kurikulum
     */
    public function destroy(string $id): RedirectResponse
    {
        $kurikulum = Kurikulum::findOrFail($id);
        $kurikulumName = $kurikulum->nama_kurikulum;
        $semesterId = $kurikulum->id_semester;

        // Cek apakah kurikulum masih digunakan di mata kuliah
        if ($kurikulum->mataKuliah()->exists()) {
            return back()->with('error', 'Kurikulum tidak dapat dihapus karena masih memiliki mata kuliah.');
        }

        $kurikulum->delete();

        return redirect()->route('kurikulum.index', ['semester' => $semesterId])
            ->with('success', "Kurikulum \"{$kurikulumName}\" berhasil dihapus.");
    }

    /**
     * Get program studi untuk AJAX
     */
    public function getProgramStudi()
    {
        $programStudis = ProgramStudi::with('jenjang')
            ->where('status', 'A')
            ->orderBy('nama_program_studi')
            ->get()
            ->map(function ($item) {
                return [
                    'id_program_studi' => $item->id_program_studi,
                    'kode_program_studi' => $item->kode_program_studi,
                    'nama_program_studi' => $item->nama_program_studi,
                    'jenjang' => $item->jenjang->nama_jenjang_pendidikan,
                    'kode_jenjang' => $item->jenjang->kode_jenjang_pendidikan,
                    'nama_lengkap' => $item->nama_program_studi . ' (' . $item->jenjang->kode_jenjang_pendidikan . ')',
                ];
            });

        return response()->json($programStudis);
    }

    /**
     * Statistik kurikulum berdasarkan semester
     */
    public function statistik(string $semesterId)
    {
        $semester = Semester::findOrFail($semesterId);

        $stats = [
            'total_kurikulum' => Kurikulum::where('id_semester', $semesterId)->count(),
            'rata_sks_lulus' => Kurikulum::where('id_semester', $semesterId)->avg('jumlah_sks_lulus'),
            'rata_sks_wajib' => Kurikulum::where('id_semester', $semesterId)->avg('jumlah_sks_wajib'),
            'rata_sks_pilihan' => Kurikulum::where('id_semester', $semesterId)->avg('jumlah_sks_pilihan'),
            'program_studi_count' => Kurikulum::where('id_semester', $semesterId)
                ->distinct('id_program_studi')->count(),
        ];

        return response()->json($stats);
    }
}
