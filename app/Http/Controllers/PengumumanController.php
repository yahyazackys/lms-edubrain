<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $pengumuman = Pengumuman::latest()->paginate(10);
        return view('pengumuman', compact('pengumuman'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tujuan'    => 'required|in:mahasiswa,dosen,umum',
            'dokumen'   => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('dokumen')) {
            $path = $this->storeFile($request->file('dokumen'), $validated['judul']);
        }

        $pengumuman = Pengumuman::create([
            'id'        => (string) Str::uuid(),
            'judul'     => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'tujuan'    => $validated['tujuan'],
            'dokumen'   => $path,
        ]);

        // Cek apakah ini request AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil disimpan.',
                'data' => $pengumuman
            ]);
        }

        // Fallback normal
        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tujuan'    => 'required|in:mahasiswa,dosen,umum',
            'dokumen'   => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:5120',
        ]);

        if ($request->hasFile('dokumen')) {
            if ($pengumuman->dokumen && Storage::disk('public')->exists($pengumuman->dokumen)) {
                Storage::disk('public')->delete($pengumuman->dokumen);
            }
            $validated['dokumen'] = $this->storeFile($request->file('dokumen'), $validated['judul']);
        }

        $pengumuman->update($validated);

        // Cek apakah ini request AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil diperbarui.',
                'data' => $pengumuman->fresh()
            ]);
        }

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Request $request, Pengumuman $pengumuman)
    {
        if ($pengumuman->dokumen && Storage::disk('public')->exists($pengumuman->dokumen)) {
            Storage::disk('public')->delete($pengumuman->dokumen);
        }

        $pengumuman->delete();

        // Cek apakah ini request AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil dihapus.'
            ]);
        }

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function streamFile(Pengumuman $pengumuman)
    {
        if (!$pengumuman->dokumen || !Storage::disk('public')->exists($pengumuman->dokumen)) {
            abort(404, 'File tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($pengumuman->dokumen);
        return response()->file($path);
    }

    private function storeFile(\Illuminate\Http\UploadedFile $file, string $judul): string
    {
        if (!Storage::disk('public')->exists('pengumuman_dokumen')) {
            Storage::disk('public')->makeDirectory('pengumuman_dokumen');
        }

        $safeName = time() . '_' . Str::slug(Str::limit($judul, 50, '')) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('pengumuman_dokumen', $safeName, 'public');
    }
}
