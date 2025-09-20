<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Models\KelasKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MateriController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_kelas_kuliah' => 'required|exists:kelas_kuliah,id_kelas_kuliah',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'dokumen' => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx,xls,xlsx|max:10240', // Max 10MB
        ], [
            'id_kelas_kuliah.required' => 'Kelas kuliah harus dipilih',
            'id_kelas_kuliah.exists' => 'Kelas kuliah tidak valid',
            'judul.required' => 'Judul materi harus diisi',
            'judul.max' => 'Judul materi maksimal 255 karakter',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter',
            'dokumen.file' => 'Dokumen harus berupa file',
            'dokumen.mimes' => 'Format file harus: PDF, PPT, PPTX, DOC, DOCX, XLS, XLSX',
            'dokumen.max' => 'Ukuran file maksimal 10MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi');
        }

        try {
            $dokumenPath = null;

            // Handle file upload if exists
            if ($request->hasFile('dokumen')) {
                $file = $request->file('dokumen');
                $filename = time() . '_' . $file->getClientOriginalName();
                $dokumenPath = $file->storeAs('materi', $filename, 'public');
            }

            Materi::create([
                'id_kelas_kuliah' => $request->id_kelas_kuliah,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'dokumen' => $dokumenPath,
            ]);

            return redirect()->back()->with('success', 'Materi berhasil ditambahkan');
        } catch (\Exception $e) {
            // Delete uploaded file if database save fails
            if ($dokumenPath && Storage::disk('public')->exists($dokumenPath)) {
                Storage::disk('public')->delete($dokumenPath);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan materi: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $materi = Materi::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'dokumen' => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx,xls,xlsx|max:10240', // Max 10MB
        ], [
            'judul.required' => 'Judul materi harus diisi',
            'judul.max' => 'Judul materi maksimal 255 karakter',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter',
            'dokumen.file' => 'Dokumen harus berupa file',
            'dokumen.mimes' => 'Format file harus: PDF, PPT, PPTX, DOC, DOCX, XLS, XLSX',
            'dokumen.max' => 'Ukuran file maksimal 10MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi');
        }

        try {
            $oldDokumen = $materi->dokumen;
            $dokumenPath = $oldDokumen;

            // Handle file upload if new file is provided
            if ($request->hasFile('dokumen')) {
                $file = $request->file('dokumen');
                $filename = time() . '_' . $file->getClientOriginalName();
                $dokumenPath = $file->storeAs('materi', $filename, 'public');

                // Delete old file if exists
                if ($oldDokumen && Storage::disk('public')->exists($oldDokumen)) {
                    Storage::disk('public')->delete($oldDokumen);
                }
            }

            $materi->update([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'dokumen' => $dokumenPath,
            ]);

            return redirect()->back()->with('success', 'Materi berhasil diperbarui');
        } catch (\Exception $e) {
            // Delete new uploaded file if database update fails
            if ($request->hasFile('dokumen') && $dokumenPath && $dokumenPath !== $oldDokumen) {
                if (Storage::disk('public')->exists($dokumenPath)) {
                    Storage::disk('public')->delete($dokumenPath);
                }
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui materi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $materi = Materi::findOrFail($id);

            // Delete associated file if exists
            if ($materi->dokumen && Storage::disk('public')->exists($materi->dokumen)) {
                Storage::disk('public')->delete($materi->dokumen);
            }

            $materi->delete();

            return redirect()->back()->with('success', 'Materi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus materi: ' . $e->getMessage());
        }
    }

    /**
     * Download the specified materi document.
     */
    public function download($id)
    {
        try {
            $materi = Materi::findOrFail($id);

            if (!$materi->dokumen || !Storage::disk('public')->exists($materi->dokumen)) {
                return redirect()->back()->with('error', 'File tidak ditemukan');
            }

            $filePath = Storage::disk('public')->path($materi->dokumen);
            $fileName = $materi->judul . '.' . pathinfo($materi->dokumen, PATHINFO_EXTENSION);

            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh file: ' . $e->getMessage());
        }
    }
}