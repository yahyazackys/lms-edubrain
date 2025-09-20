{{-- File: resources/views/partials/modals/materi-modals.blade.php --}}

<!-- Modal Create Materi -->
<div id="materiModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeMateriModal()"></div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
            <form id="materiForm" method="POST" action="{{ route('materi.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900">Tambah Materi Pembelajaran</h3>
                    </div>

                    <div class="space-y-4">
                        <input type="hidden" name="id_kelas_kuliah" value="{{ $kelasKuliah->id_kelas_kuliah }}">

                        <!-- Judul Materi -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Judul Materi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="judul" required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                placeholder="Masukkan judul materi pembelajaran">
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="deskripsi" rows="4"
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                placeholder="Deskripsi singkat tentang materi ini"></textarea>
                        </div>

                        <!-- Upload File -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">File Materi</label>
                            <input type="file" name="dokumen" accept=".pdf,.ppt,.pptx,.doc,.docx,.xls,.xlsx"
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">
                                Format yang didukung: PDF, PPT, PPTX, DOC, DOCX, XLS, XLSX (Max: 10MB)
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeMateriModal()"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        Batal
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        Simpan Materi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Materi -->
<div id="editMateriModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeEditMateriModal()"></div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
            <form id="editMateriForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900">Edit Materi Pembelajaran</h3>
                    </div>

                    <div class="space-y-4">
                        <!-- Judul Materi -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Judul Materi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="judul" id="edit_judul" required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                placeholder="Masukkan judul materi pembelajaran">
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="deskripsi" id="edit_deskripsi" rows="4"
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                placeholder="Deskripsi singkat tentang materi ini"></textarea>
                        </div>

                        <!-- File Saat Ini -->
                        <div id="current_file_container" class="hidden">
                            <label class="block text-xs font-medium text-gray-700 mb-2">File Saat Ini</label>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-file text-gray-500 mr-2"></i>
                                    <span id="current_file_name" class="text-xs text-gray-700"></span>
                                </div>
                                <a id="current_file_download" href="#" target="_blank"
                                    class="text-xs text-blue-600 hover:text-blue-800 underline">
                                    Download
                                </a>
                            </div>
                        </div>

                        <!-- Upload File Baru -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Ganti File Materi <span class="text-xs text-gray-500">(Opsional)</span>
                            </label>
                            <input type="file" name="dokumen" accept=".pdf,.ppt,.pptx,.doc,.docx,.xls,.xlsx"
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">
                                Format yang didukung: PDF, PPT, PPTX, DOC, DOCX, XLS, XLSX (Max: 10MB)
                                <br>Biarkan kosong jika tidak ingin mengganti file
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeEditMateriModal()"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        Batal
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        Update Materi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Materi -->
<div id="deleteMateriModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteMateriModal()">
        </div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md mx-auto">
            <form id="deleteMateriForm" method="POST" action="">
                @csrf
                @method('DELETE')

                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900">Hapus Materi</h3>
                    </div>

                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                        </div>

                        <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Penghapusan</h3>

                        <p class="text-xs text-gray-500 mb-4">Yakin ingin menghapus materi ini?</p>

                        <!-- Info Materi yang akan dihapus -->
                        <div class="bg-gray-50 rounded-lg p-3 mb-4">
                            <div class="text-left">
                                <div class="text-xs font-medium text-gray-900" id="delete_materi_judul"></div>
                                <div class="text-xs text-gray-500 mt-1" id="delete_materi_deskripsi"></div>
                                <div class="text-xs text-gray-500 mt-1" id="delete_materi_file"></div>
                            </div>
                        </div>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-xs text-red-700">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>Perhatian:</strong> Data yang sudah dihapus tidak dapat dikembalikan.
                                File materi juga akan dihapus dari server.
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeDeleteMateriModal()"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <i class="fas fa-trash mr-2"></i>
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
