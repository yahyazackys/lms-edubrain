{{-- File: resources/views/partials/modals/pengaturan-modals.blade.php --}}

<!-- Modal Edit Bobot Penilaian -->
<div id="bobotModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeBobotModal()"></div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md mx-auto">
            <form id="bobotForm">
                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900">Edit Bobot Penilaian</h3>
                        <button type="button" onclick="closeBobotModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Bobot Absensi (%) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="bobot_absensi" name="bobot_absensi"
                                value="{{ $kelasKuliah->bobot_absensi }}" min="0" max="100" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Bobot Tugas (%) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="bobot_tugas" name="bobot_tugas"
                                value="{{ $kelasKuliah->bobot_tugas }}" min="0" max="100" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Bobot UTS (%) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="bobot_uts" name="bobot_uts" value="{{ $kelasKuliah->bobot_uts }}"
                                min="0" max="100" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Bobot UAS (%) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="bobot_uas" name="bobot_uas" value="{{ $kelasKuliah->bobot_uas }}"
                                min="0" max="100" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Total Display -->
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Total Bobot:</span>
                                <span id="totalBobot" class="text-lg font-bold text-gray-900">100%</span>
                            </div>
                            <div id="bobotWarning" class="hidden mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Total bobot harus 100%
                            </div>
                        </div>

                        <!-- Validation Info -->
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-600 mt-1 mr-2"></i>
                                <div class="text-sm text-blue-700">
                                    <strong>Catatan:</strong> Total bobot penilaian harus tepat 100%.
                                    Pastikan pembagian bobot sesuai dengan kebijakan akademik.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeBobotModal()"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Batal
                    </button>
                    <button type="submit" id="bobotSubmitBtn"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Akhiri Kelas -->
<div id="akhiriKelasModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAkhiriKelasModal()"></div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">
            <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-heading font-semibold text-gray-900">Konfirmasi Akhiri Kelas</h3>
                    <button type="button" onclick="closeAkhiriKelasModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="text-center">
                    <!-- Warning Icon -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-stop-circle text-red-600 text-2xl"></i>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-2">Yakin Ingin Mengakhiri Kelas?</h3>

                    <p class="text-sm text-gray-600 mb-4">
                        Setelah kelas diakhiri, Anda tidak akan dapat lagi:
                    </p>

                    <!-- List of restrictions -->
                    <div class="text-left bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <ul class="text-sm text-red-700 space-y-1">
                            <li class="flex items-start">
                                <i class="fas fa-times-circle mr-2 mt-1 text-xs"></i>
                                Menambah, mengedit, atau menghapus materi pembelajaran
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-times-circle mr-2 mt-1 text-xs"></i>
                                Mengelola tugas, UTS, dan UAS
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-times-circle mr-2 mt-1 text-xs"></i>
                                Membuat atau mengelola sesi absensi
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-times-circle mr-2 mt-1 text-xs"></i>
                                Mengubah bobot penilaian
                            </li>
                        </ul>
                    </div>

                    <!-- Class Info -->
                    <div class="bg-gray-50 rounded-lg p-3 mb-4">
                        <div class="text-left text-sm">
                            <div class="font-medium text-gray-900">
                                {{ $kelasKuliah->mataKuliah->kode_mata_kuliah }} -
                                {{ $kelasKuliah->mataKuliah->nama_mata_kuliah }}
                            </div>
                            <div class="text-gray-600 mt-1">
                                {{ $kelasKuliah->nama_kelas_kuliah }} • {{ $kelasKuliah->semester->nama_semester }}
                            </div>
                        </div>
                    </div>

                    <!-- Final Warning -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-2"></i>
                            <p class="text-sm text-yellow-700 text-left">
                                <strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan.
                                Pastikan semua data pembelajaran sudah lengkap dan benar.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                <button type="button" onclick="closeAkhiriKelasModal()"
                    class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Batal
                </button>
                <button type="button" onclick="executeAkhiriKelas()" id="akhiriKelasBtn"
                    class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <i class="fas fa-stop-circle mr-2"></i>
                    Ya, Akhiri Kelas
                </button>
            </div>
        </div>
    </div>
</div>
