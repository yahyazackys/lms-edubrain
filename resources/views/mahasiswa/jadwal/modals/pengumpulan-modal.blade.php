{{-- File: resources/views/mahasiswa/jadwal/modals/pengumpulan-modal.blade.php --}}

<!-- Modal Pengumpulan -->
<div id="pengumpulanModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closePengumpulanModal()"></div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">
            <form id="pengumpulanForm" method="POST" action="" enctype="multipart/form-data">
                @csrf

                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 id="modal-title" class="text-lg font-heading font-semibold text-gray-900">
                            Kumpulkan Tugas
                        </h3>
                        <button type="button" onclick="closePengumpulanModal()"
                            class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Upload File -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                File Pengumpulan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="dokumen" id="dokumen" required
                                    accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx"
                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Format yang didukung: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX (Maksimal 10MB)
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closePengumpulanModal()"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit" id="submit-button"
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="submit-text">Kumpulkan</span>
                        <span class="loading-spinner hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Mengupload...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 z-[10000] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md mx-auto">
            <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-2">Berhasil!</h3>
                    <p id="success-message" class="text-xs text-gray-600">File berhasil dikumpulkan.</p>
                </div>
            </div>

            <div class="bg-gray-50 px-4 sm:px-6 py-4 flex justify-center">
                <button type="button" onclick="closeSuccessModal()"
                    class="inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 z-[10000] overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md mx-auto">
            <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-2">Terjadi Kesalahan</h3>
                    <p id="error-message" class="text-xs text-gray-600">Terjadi kesalahan saat mengupload file.</p>
                </div>
            </div>

            <div class="bg-gray-50 px-4 sm:px-6 py-4 flex justify-center">
                <button type="button" onclick="closeErrorModal()"
                    class="inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>
