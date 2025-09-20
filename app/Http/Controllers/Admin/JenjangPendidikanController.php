<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenjangPendidikan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JenjangPendidikanController extends Controller
{
    /**
     * Tampilkan daftar jenjang pendidikan
     */
    public function index(Request $request): View
    {
        $query = JenjangPendidikan::query();

        $jenjangs = $query->orderBy('kode_jenjang_pendidikan')->paginate(10);

        return view('admin.jenjang.index', compact('jenjangs'));
    }

    /**
     * Simpan jenjang pendidikan baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_jenjang_pendidikan' => 'required|string|max:50|unique:jenjang_pendidikan,kode_jenjang_pendidikan',
            'nama_jenjang_pendidikan' => 'required|string|max:50',
        ]);

        JenjangPendidikan::create([
            'id_jenjang_pendidikan'   => (string) Str::uuid(),
            'kode_jenjang_pendidikan' => $validated['kode_jenjang_pendidikan'],
            'nama_jenjang_pendidikan' => $validated['nama_jenjang_pendidikan'],
        ]);

        return redirect()->route('jenjang.index')
            ->with('success', 'Jenjang Pendidikan berhasil ditambahkan.');
    }

    /**
     * Update jenjang pendidikan
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $jenjang = JenjangPendidikan::findOrFail($id);

        $validated = $request->validate([
            'kode_jenjang_pendidikan' => 'required|string|max:50|unique:jenjang_pendidikan,kode_jenjang_pendidikan,' . $jenjang->id_jenjang_pendidikan . ',id_jenjang_pendidikan',
            'nama_jenjang_pendidikan' => 'required|string|max:50',
        ]);

        $jenjang->update($validated);

        return redirect()->route('jenjang.index')
            ->with('success', 'Jenjang Pendidikan berhasil diperbarui.');
    }

    /**
     * Hapus jenjang pendidikan
     */
    public function destroy(string $id): RedirectResponse
    {
        $jenjang = JenjangPendidikan::findOrFail($id);
        $jenjangName = $jenjang->nama_jenjang_pendidikan;

        $jenjang->delete();

        return redirect()->route('jenjang.index')
            ->with('success', "Jenjang Pendidikan \"{$jenjangName}\" berhasil dihapus.");
    }
}
