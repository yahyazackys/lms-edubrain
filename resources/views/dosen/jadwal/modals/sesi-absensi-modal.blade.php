{{-- File: resources/views/partials/modals/sesi-absensi-modals.blade.php --}}

<!-- Modal Create Sesi Absensi -->
<div id="sesiAbsensiModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeSesiAbsensiModal()"></div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">
            <form id="sesiAbsensiForm" method="POST" action="{{ route('sesi-absensi.store') }}">
                @csrf

                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900">Buat Sesi Absensi Baru</h3>
                    </div>

                    <div class="space-y-4">
                        <input type="hidden" name="id_kelas_kuliah" value="{{ $kelasKuliah->id_kelas_kuliah }}">

                        <!-- Topik Absensi -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Topik Perkuliahan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="topik" required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                placeholder="Masukkan topik perkuliahan hari ini">
                        </div>

                        <!-- Batas Akhir Absensi -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Batas Akhir Absensi <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="batas_akhir_absensi" required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">
                                Tentukan batas waktu mahasiswa dapat melakukan absensi
                            </p>
                        </div>

                        <!-- Info QR Code -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-qrcode text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-xs font-medium text-blue-800">QR Code Absensi</h3>
                                    <div class="mt-1 text-xs text-blue-700">
                                        <p>QR Code akan dibuat otomatis setelah sesi absensi dibuat. Mahasiswa dapat
                                            memindai QR Code untuk melakukan absensi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeSesiAbsensiModal()"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        Batal
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        <i class="fas fa-play mr-2"></i>
                        Buat & Buka Sesi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Sesi Absensi -->
<div id="editSesiAbsensiModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeEditSesiAbsensiModal()">
        </div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">
            <form id="editSesiAbsensiForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900">Edit Sesi Absensi</h3>
                    </div>

                    <div class="space-y-4">
                        <!-- Topik Absensi -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Topik Perkuliahan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="topik" id="edit_sesi_topik" required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                placeholder="Masukkan topik perkuliahan">
                        </div>

                        <!-- Batas Akhir Absensi -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                Batas Akhir Absensi <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="batas_akhir_absensi" id="edit_sesi_deadline" required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeEditSesiAbsensiModal()"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        Batal
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                        Update Sesi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Sesi Absensi -->
<div id="deleteSesiAbsensiModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteSesiAbsensiModal()">
        </div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md mx-auto">
            <form id="deleteSesiAbsensiForm" method="POST" action="">
                @csrf
                @method('DELETE')

                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900">Hapus Sesi Absensi</h3>
                    </div>

                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                        </div>

                        <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Penghapusan</h3>

                        <p class="text-xs text-gray-500 mb-4">Yakin ingin menghapus sesi absensi ini?</p>

                        <!-- Info Sesi yang akan dihapus -->
                        <div class="bg-gray-50 rounded-lg p-3 mb-4">
                            <div class="text-left">
                                <div class="text-xs font-medium text-gray-900" id="delete_sesi_topik"></div>
                                <div class="text-xs text-gray-500 mt-1" id="delete_sesi_deadline"></div>
                                <div class="text-xs text-gray-500 mt-1" id="delete_sesi_status"></div>
                            </div>
                        </div>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-xs text-red-700">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>Perhatian:</strong> Data sesi absensi dan semua rekam kehadiran mahasiswa akan
                                dihapus permanen.
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeDeleteSesiAbsensiModal()"
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

<!-- Modal QR Code -->
<div id="qrCodeModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeQrCodeModal()"></div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md mx-auto">
            <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-heading font-semibold text-gray-900">QR Code Absensi</h3>
                    <button type="button" onclick="closeQrCodeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="text-center">
                    <!-- QR Code Container -->
                    <div id="qr-code-container" class="mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <div id="qr-code-placeholder" class="text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p class="text-xs">Memuat QR Code...</p>
                        </div>
                        <div id="qr-code-display" class="hidden"></div>
                    </div>

                    <!-- Petunjuk -->
                    <div class="text-left">
                        <h4 class="text-xs font-medium text-gray-900 mb-2">Petunjuk untuk Mahasiswa:</h4>
                        <ol class="text-xs text-gray-600 space-y-1 list-decimal list-inside">
                            <li>Buka aplikasi kamera atau scanner QR Code</li>
                            <li>Arahkan kamera ke QR Code ini</li>
                            <li>Ikuti link yang muncul untuk melakukan absensi</li>
                            <li>Pastikan absensi dilakukan sebelum batas waktu</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-4 sm:px-6 py-4 flex justify-end gap-4">
                <button type="button" onclick="closeQrCodeModal()"
                    class="inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                    Tutup
                </button>
                <button id="qr-download-section" onclick="downloadQRCode()"
                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-download mr-2"></i>
                    Download QR Code
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Status Absensi -->
<div id="statusAbsensiModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeStatusAbsensiModal()">
        </div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-6xl mx-auto">
            <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-heading font-semibold text-gray-900">Status Kehadiran:
                        <span id="status_topik">Topik Sesi</span>
                    </h3>
                    <button type="button" onclick="closeStatusAbsensiModal()"
                        class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- Loading State -->
                    <div id="status-loading" class="text-center">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                        <p class="text-xs text-gray-500">Memuat data kehadiran...</p>
                    </div>

                    <!-- Status Content -->
                    <div id="status-content" class="hidden">
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
                                    <input type="text" id="searchMahasiswaStatus"
                                        placeholder="Cari nama, NIM, atau angkatan..."
                                        class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button onclick="filterMahasiswaStatus('all')"
                                class="filter-btn active px-3 py-1 text-xs font-medium rounded-full border border-gray-300 hover:bg-gray-50 focus:outline-none">
                                Semua (<span id="count-all">0</span>)
                            </button>
                            <button onclick="filterMahasiswaStatus('hadir')"
                                class="filter-btn px-3 py-1 text-xs font-medium rounded-full border border-green-300 text-green-700 hover:bg-green-50 focus:outline-none">
                                Hadir (<span id="count-hadir">0</span>)
                            </button>
                            <button onclick="filterMahasiswaStatus('tidak_hadir')"
                                class="filter-btn px-3 py-1 text-xs font-medium rounded-full border border-red-300 text-red-700 hover:bg-red-50 focus:outline-none">
                                Tidak Hadir (<span id="count-tidak-hadir">0</span>)
                            </button>
                        </div>

                        <!-- Table Container with Horizontal Scroll -->
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900">Daftar Kehadiran Mahasiswa</h4>
                            </div>

                            <!-- Scrollable Table Container -->
                            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0 z-10">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                No
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                NIM
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap min-w-[200px]">
                                                Nama Mahasiswa
                                            </th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                Angkatan
                                            </th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                Status Kehadiran
                                            </th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                Waktu Absen
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="mahasiswa-status-table">
                                        <tr>
                                            <td colspan="7" class="text-center py-8 text-gray-500 text-sm">
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

            <div class="bg-gray-50 px-4 sm:px-6 py-4 flex justify-center">
                <button type="button" onclick="closeStatusAbsensiModal()"
                    class="inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
