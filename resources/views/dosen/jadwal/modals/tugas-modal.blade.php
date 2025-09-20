{{-- File: resources/views/partials/modals/tugas-modals.blade.php --}}

<!-- Modal Create Tugas -->
<div id="tugasModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeTugasModal()"></div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
            <form id="tugasForm" method="POST" action="{{ route('tugas.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900">Buat Tugas Baru</h3>
                    </div>

                    <div class="space-y-4">
                        <input type="hidden" name="id_kelas_kuliah" value="{{ $kelasKuliah->id_kelas_kuliah }}">

                        <!-- Judul Tugas -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Judul Tugas <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="judul" required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                placeholder="Masukkan judul tugas">
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Deskripsi Tugas <span class="text-red-500">*</span>
                            </label>
                            <textarea name="deskripsi" rows="4" required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                placeholder="Jelaskan detail tugas yang harus dikerjakan mahasiswa"></textarea>
                        </div>

                        <!-- Upload File Soal -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">File Soal (Opsional)</label>
                            <input type="file" name="dokumen" accept=".pdf,.doc,.docx"
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">
                                Upload file soal jika diperlukan (PDF, DOC, DOCX - Max: 10MB)
                            </p>
                        </div>

                        <!-- Deadline -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Batas Akhir Pengumpulan <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="batas_akhir_pengumpulan" required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">
                                Pilih tanggal dan waktu deadline pengumpulan tugas
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeTugasModal()"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        Batal
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        Simpan Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Tugas -->
<div id="editTugasModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeEditTugasModal()"></div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
            <form id="editTugasForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900">Edit Tugas</h3>
                    </div>

                    <div class="space-y-4">
                        <!-- Judul Tugas -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Judul Tugas <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="judul" id="edit_tugas_judul" required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                placeholder="Masukkan judul tugas">
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Deskripsi Tugas <span class="text-red-500">*</span>
                            </label>
                            <textarea name="deskripsi" id="edit_tugas_deskripsi" rows="4" required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                placeholder="Jelaskan detail tugas yang harus dikerjakan mahasiswa"></textarea>
                        </div>

                        <!-- File Saat Ini -->
                        <div id="current_tugas_file_container" class="hidden">
                            <label class="block text-xs font-medium text-gray-700 mb-2">File Soal Saat Ini</label>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-file text-gray-500 mr-2"></i>
                                    <span id="current_tugas_file_name" class="text-xs text-gray-700"></span>
                                </div>
                                <a id="current_tugas_file_download" href="#" target="_blank"
                                    class="text-xs text-blue-600 hover:text-blue-800 underline">
                                    Download
                                </a>
                            </div>
                        </div>

                        <!-- Upload File Baru -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Ganti File Soal <span class="text-xs text-gray-500">(Opsional)</span>
                            </label>
                            <input type="file" name="dokumen" accept=".pdf,.doc,.docx"
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">
                                Format yang didukung: PDF, DOC, DOCX (Max: 10MB)
                                <br>Biarkan kosong jika tidak ingin mengganti file
                            </p>
                        </div>

                        <!-- Deadline -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Batas Akhir Pengumpulan <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="batas_akhir_pengumpulan" id="edit_tugas_deadline"
                                required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">
                                Pilih tanggal dan waktu deadline pengumpulan tugas
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeEditTugasModal()"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        Batal
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        Update Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Tugas -->
<div id="deleteTugasModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteTugasModal()">
        </div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md mx-auto">
            <form id="deleteTugasForm" method="POST" action="">
                @csrf
                @method('DELETE')

                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900">Hapus Tugas</h3>
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

                        <p class="text-xs text-gray-500 mb-4">Yakin ingin menghapus tugas ini?</p>

                        <!-- Info Tugas yang akan dihapus -->
                        <div class="bg-gray-50 rounded-lg p-3 mb-4">
                            <div class="text-left">
                                <div class="text-xs font-medium text-gray-900" id="delete_tugas_judul"></div>
                                <div class="text-xs text-gray-500 mt-1" id="delete_tugas_deadline"></div>
                                <div class="text-xs text-gray-500 mt-1" id="delete_tugas_file"></div>
                            </div>
                        </div>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-xs text-red-700">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>Perhatian:</strong> Data tugas dan semua pengumpulan mahasiswa akan dihapus
                                permanen.
                                File tugas juga akan dihapus dari server.
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeDeleteTugasModal()"
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

<!-- Modal Status Pengumpulan Tugas -->
<div id="statusPengumpulanModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            onclick="closeStatusPengumpulanModal()">
        </div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-7xl mx-auto">
            <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-heading font-semibold text-gray-900">Status Pengumpulan:
                        <span id="status_tugas_judul">Judul Tugas</span>
                    </h3>
                    <button type="button" onclick="closeStatusPengumpulanModal()"
                        class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- Loading State -->
                    <div id="pengumpulan-loading" class="text-center">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                        <p class="text-xs text-gray-500">Memuat data pengumpulan...</p>
                    </div>

                    <!-- Status Content -->
                    <div id="pengumpulan-content" class="hidden">
                        <!-- Search and Filters -->
                        <div class="flex flex-col sm:flex-row gap-3 mb-4">
                            <!-- Search Bar -->
                            <div class="flex-1">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" id="searchMahasiswaPengumpulan"
                                        placeholder="Cari nama, NIM, atau angkatan..."
                                        class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button onclick="filterMahasiswaPengumpulan('all')"
                                class="filter-pengumpulan-btn active px-3 py-1 text-xs font-medium rounded-full border border-gray-300 hover:bg-gray-50 focus:outline-none">
                                Semua (<span id="count-all-pengumpulan">0</span>)
                            </button>
                            <button onclick="filterMahasiswaPengumpulan('sudah_mengumpulkan')"
                                class="filter-pengumpulan-btn px-3 py-1 text-xs font-medium rounded-full border border-green-300 text-green-700 hover:bg-green-50 focus:outline-none">
                                Sudah Mengumpulkan (<span id="count-sudah-mengumpulkan">0</span>)
                            </button>
                            <button onclick="filterMahasiswaPengumpulan('belum_mengumpulkan')"
                                class="filter-pengumpulan-btn px-3 py-1 text-xs font-medium rounded-full border border-red-300 text-red-700 hover:bg-red-50 focus:outline-none">
                                Belum Mengumpulkan (<span id="count-belum-mengumpulkan">0</span>)
                            </button>
                            <button onclick="filterMahasiswaPengumpulan('sudah_dinilai')"
                                class="filter-pengumpulan-btn px-3 py-1 text-xs font-medium rounded-full border border-blue-300 text-blue-700 hover:bg-blue-50 focus:outline-none">
                                Sudah Dinilai (<span id="count-sudah-dinilai">0</span>)
                            </button>
                            <button onclick="filterMahasiswaPengumpulan('belum_dinilai')"
                                class="filter-pengumpulan-btn px-3 py-1 text-xs font-medium rounded-full border border-orange-300 text-orange-700 hover:bg-orange-50 focus:outline-none">
                                Belum Dinilai (<span id="count-belum-dinilai">0</span>)
                            </button>
                        </div>

                        <!-- Table Container with Horizontal Scroll -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900">Daftar Pengumpulan Tugas Mahasiswa</h4>
                            </div>

                            <!-- Scrollable Table Container -->
                            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0 z-10">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                No
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                NIM
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                Nama Mahasiswa
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                Angkatan
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                Status
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                File Jawaban
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                Waktu Submit
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                Nilai
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="mahasiswa-pengumpulan-table">
                                        <tr>
                                            <td colspan="9" class="text-center py-8 text-gray-500 text-sm">
                                                <i class="fas fa-users text-2xl mb-2"></i>
                                                <p>Memuat daftar mahasiswa...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-4 sm:px-6 py-4 flex justify-end">
                <button type="button" onclick="closeStatusPengumpulanModal()"
                    class="inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Quick Grade -->
<div id="quickGradeModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeQuickGradeModal()">
        </div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md mx-auto">
            <form id="quickGradeForm" onsubmit="submitQuickGrade(event)">
                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900">Berikan Nilai</h3>
                        <button type="button" onclick="closeQuickGradeModal()"
                            class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Student Info -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="text-xs">
                                <div class="font-medium text-gray-900" id="grade_mahasiswa_nama">Nama Mahasiswa</div>
                                <div class="text-gray-500" id="grade_mahasiswa_nim">NIM</div>
                            </div>
                        </div>

                        <!-- Grade Input -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Nilai <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="grade_nilai" name="nilai" min="0" max="100"
                                step="0.1" required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                placeholder="Masukkan nilai (0-100)">
                            <p class="text-xs text-gray-500 mt-1">Rentang nilai: 0 - 100</p>
                        </div>

                        <!-- Hidden fields -->
                        <input type="hidden" id="grade_pengumpulan_id" name="pengumpulan_id">
                    </div>
                </div>

                <div
                    class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeQuickGradeModal()"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        Batal
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
