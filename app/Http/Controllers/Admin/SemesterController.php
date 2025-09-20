<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SemesterController extends Controller
{
    /**
     * Tampilkan daftar semester
     */
    public function index(Request $request): View
    {
        $query = Semester::query();

        $semesters = $query->orderByDesc('tanggal_mulai')->paginate(10);

        $hasActiveSemester = Semester::where('is_active', true)->exists();

        return view('admin.semester.index', compact('semesters', 'hasActiveSemester'));
    }

    /**
     * Simpan semester baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_semester'   => 'required|string|max:10|unique:semester,kode_semester',
            'nama_semester'   => 'required|string|max:50',
            'tipe'            => 'required|in:ganjil,genap',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'is_active'       => 'nullable|boolean',
        ]);

        // Jika semester ini dibuat aktif, nonaktifkan yang lain
        if (!empty($validated['is_active']) && $validated['is_active']) {
            Semester::where('is_active', true)->update(['is_active' => false]);
        }

        $semester = Semester::create([
            'id_semester'     => (string) Str::uuid(),
            'kode_semester'   => $validated['kode_semester'],
            'nama_semester'   => $validated['nama_semester'],
            'tipe'            => $validated['tipe'],
            'tanggal_mulai'   => $validated['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'is_active'       => $validated['is_active'] ?? false,
        ]);

        return redirect()->route('semester.index')
            ->with('success', 'Semester berhasil ditambahkan.');
    }

    /**
     * Update semester
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $semester = Semester::findOrFail($id);

        $validated = $request->validate([
            'kode_semester'   => 'required|string|max:10|unique:semester,kode_semester,' . $semester->id_semester . ',id_semester',
            'nama_semester'   => 'required|string|max:50',
            'tipe'            => 'required|in:ganjil,genap',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'is_active'       => 'nullable|boolean',
        ]);

        // Jika update jadi aktif, nonaktifkan semester lain
        if (!empty($validated['is_active']) && $validated['is_active']) {
            Semester::where('is_active', true)
                ->where('id_semester', '!=', $semester->id_semester)
                ->update(['is_active' => false]);
        }

        $semester->update($validated);

        return redirect()->route('semester.index')
            ->with('success', 'Semester berhasil diperbarui.');
    }

    /**
     * Hapus semester
     */
    public function destroy(string $id): RedirectResponse
    {
        $semester = Semester::findOrFail($id);
        $semesterName = $semester->nama_semester;

        $semester->delete();

        return redirect()->route('semester.index')
            ->with('success', "Semester \"{$semesterName}\" berhasil dihapus.");
    }

    /**
     * Aktifkan semester
     */
    public function activate(string $id): RedirectResponse
    {
        $semester = Semester::findOrFail($id);

        $semester->update(['is_active' => true]);

        return redirect()->route('semester.index')
            ->with('success', "Semester {$semester->nama_semester} berhasil diaktifkan.");
    }

    /**
     * Nonaktifkan semester
     */
    public function deactivate(string $id): RedirectResponse
    {
        $semester = Semester::findOrFail($id);

        if (!$semester->is_active) {
            return redirect()->route('semester.index')
                ->with('error', 'Semester ini sudah tidak aktif.');
        }

        $semester->update(['is_active' => false]);

        return redirect()->route('semester.index')
            ->with('success', "Semester {$semester->nama_semester} berhasil dinonaktifkan.");
    }
}
