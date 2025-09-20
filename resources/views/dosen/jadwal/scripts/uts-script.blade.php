{{-- File: resources/views/partials/scripts/uts-scripts.blade.php --}}

<script>
    /**
     * ========================================
     * UTS MODAL FUNCTIONS
     * ========================================
     */

    // ============ CREATE MODAL FUNCTIONS ============

    /**
     * Open create uts modal
     */
    function openUtsModal() {
        const modal = document.getElementById('utsModal');
        const form = document.getElementById('utsForm');

        // Reset form
        form.reset();
        clearFormErrors(form);

        // Set default deadline (7 days from now for UTS)
        setDefaultUtsDeadline();

        // Show modal
        modal.classList.remove('hidden');

        // Focus on first input
        setTimeout(() => {
            const firstInput = form.querySelector('input[name="judul"]');
            if (firstInput) firstInput.focus();
        }, 100);
    }

    /**
     * Close create uts modal
     */
    function closeUtsModal() {
        const modal = document.getElementById('utsModal');
        const form = document.getElementById('utsForm');

        // Hide modal
        modal.classList.add('hidden');

        // Reset form
        form.reset();

        // Clear any validation errors
        clearFormErrors(form);
    }

    // ============ EDIT MODAL FUNCTIONS ============

    /**
     * Open edit uts modal
     * @param {string} utsId - ID of the uts
     * @param {string} judul - Title of the uts
     * @param {string} deskripsi - Description of the uts
     * @param {string} dokumen - Document path of the uts
     * @param {string} deadline - Deadline in ISO format
     */
    function openEditUtsModal(utsId, judul, deskripsi, dokumen, deadline) {
        const modal = document.getElementById('editUtsModal');
        const form = document.getElementById('editUtsForm');
        const currentFileContainer = document.getElementById('current_uts_file_container');
        const currentFileName = document.getElementById('current_uts_file_name');
        const currentFileDownload = document.getElementById('current_uts_file_download');

        // Reset form
        form.reset();
        clearFormErrors(form);

        // Set form action
        form.action = `/uts/${utsId}`;

        // Fill form data
        document.getElementById('edit_uts_judul').value = judul || '';
        document.getElementById('edit_uts_deskripsi').value = deskripsi || '';

        // Format deadline for datetime-local input
        if (deadline) {
            const formattedDeadline = formatDateTimeForInput(deadline);
            document.getElementById('edit_uts_deadline').value = formattedDeadline;
        }

        // Handle current file display
        if (dokumen && dokumen.trim() !== '') {
            currentFileContainer.classList.remove('hidden');
            const fileName = extractFileName(dokumen);
            currentFileName.textContent = fileName;
            currentFileDownload.href = `/uts/${utsId}/download`;
        } else {
            currentFileContainer.classList.add('hidden');
        }

        // Show modal
        modal.classList.remove('hidden');

        // Focus on first input
        setTimeout(() => {
            const firstInput = document.getElementById('edit_uts_judul');
            if (firstInput) {
                firstInput.focus();
                firstInput.select();
            }
        }, 100);
    }

    /**
     * Close edit uts modal
     */
    function closeEditUtsModal() {
        const modal = document.getElementById('editUtsModal');
        const form = document.getElementById('editUtsForm');
        const currentFileContainer = document.getElementById('current_uts_file_container');

        // Hide modal
        modal.classList.add('hidden');

        // Reset form
        form.reset();
        clearFormErrors(form);

        // Hide current file container
        currentFileContainer.classList.add('hidden');
    }

    // ============ DELETE MODAL FUNCTIONS ============

    /**
     * Open delete uts modal
     * @param {string} utsId - ID of the uts
     * @param {string} judul - Title of the uts
     * @param {string} dokumen - Document path of the uts
     * @param {string} deadline - Deadline in ISO format
     */
    function openDeleteUtsModal(utsId, judul, dokumen, deadline) {
        const modal = document.getElementById('deleteUtsModal');
        const form = document.getElementById('deleteUtsForm');

        // Set form action
        form.action = `/uts/${utsId}`;

        // Fill uts info for confirmation
        document.getElementById('delete_uts_judul').textContent = judul || 'Tidak ada judul';

        // Format deadline for display
        if (deadline) {
            const formattedDeadline = formatDateTimeForDisplay(deadline);
            document.getElementById('delete_uts_deadline').textContent = `Deadline: ${formattedDeadline}`;
        } else {
            document.getElementById('delete_uts_deadline').textContent = 'Tidak ada deadline';
        }

        // Handle file info
        const fileElement = document.getElementById('delete_uts_file');
        if (dokumen && dokumen.trim() !== '') {
            const fileName = extractFileName(dokumen);
            fileElement.textContent = `File: ${fileName}`;
        } else {
            fileElement.textContent = 'Tidak ada file soal';
        }

        // Show modal
        modal.classList.remove('hidden');

        // Focus on cancel button for safety
        setTimeout(() => {
            const cancelButton = modal.querySelector('button[type="button"]');
            if (cancelButton) cancelButton.focus();
        }, 100);
    }

    /**
     * Close delete uts modal
     */
    function closeDeleteUtsModal() {
        const modal = document.getElementById('deleteUtsModal');
        modal.classList.add('hidden');
    }

    // ============ UTILITY FUNCTIONS ============

    /**
     * Set default deadline for UTS (7 days from now)
     */
    function setDefaultUtsDeadline() {
        const defaultDeadlineInput = document.querySelector('#utsModal input[name="batas_akhir_pengumpulan"]');
        if (defaultDeadlineInput) {
            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            nextWeek.setHours(23, 59); // Set to 23:59

            const formattedDate = formatDateTimeForInput(nextWeek.toISOString());
            defaultDeadlineInput.value = formattedDate;
        }
    }

    /**
     * Format datetime for input[type="datetime-local"]
     * @param {string} isoString - ISO datetime string
     * @returns {string} - Formatted datetime for input
     */
    function formatDateTimeForInput(isoString) {
        if (!isoString) return '';

        const date = new Date(isoString);
        if (isNaN(date.getTime())) return '';

        // Format: YYYY-MM-DDTHH:MM
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');

        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    /**
     * Format datetime for display
     * @param {string} isoString - ISO datetime string
     * @returns {string} - Formatted datetime for display
     */
    function formatDateTimeForDisplay(isoString) {
        if (!isoString) return '';

        const date = new Date(isoString);
        if (isNaN(date.getTime())) return '';

        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZone: 'Asia/Jakarta'
        };

        return date.toLocaleDateString('id-ID', options);
    }

    /**
     * Extract filename from path
     * @param {string} path - File path
     * @returns {string} - Filename
     */
    function extractFileName(path) {
        if (!path) return '';

        // Handle both forward and backward slashes
        const parts = path.split(/[/\\]/);
        return parts[parts.length - 1] || path;
    }

    /**
     * Clear form validation errors
     * @param {HTMLFormElement} form - Form element
     */
    function clearFormErrors(form) {
        // Remove error classes
        const errorElements = form.querySelectorAll('.border-red-300, .text-red-600');
        errorElements.forEach(element => {
            element.classList.remove('border-red-300', 'text-red-600');
            element.classList.add('border-gray-300');
        });

        // Remove error messages
        const errorMessages = form.querySelectorAll('.text-red-500:not(.required-asterisk)');
        errorMessages.forEach(element => {
            if (!element.textContent.includes('*')) {
                element.remove();
            }
        });
    }

    /**
     * Validate datetime input (must be in the future)
     * @param {HTMLInputElement} datetimeInput - Datetime input element
     * @returns {boolean} - Is valid
     */
    function validateUtsDateTimeInput(datetimeInput) {
        const value = datetimeInput.value;
        if (!value) {
            showUtsDateTimeError(datetimeInput, 'Batas akhir pengumpulan harus diisi');
            return false;
        }

        const selectedDate = new Date(value);
        const now = new Date();

        // Clear any previous errors
        clearUtsDateTimeError(datetimeInput);
        return true;
    }

    /**
     * Show datetime validation error
     * @param {HTMLInputElement} datetimeInput - DateTime input element
     * @param {string} message - Error message
     */
    function showUtsDateTimeError(datetimeInput, message) {
        clearUtsDateTimeError(datetimeInput);

        datetimeInput.classList.add('border-red-300');

        const errorElement = document.createElement('p');
        errorElement.className = 'text-xs text-red-500 mt-1';
        errorElement.textContent = message;

        datetimeInput.parentNode.appendChild(errorElement);
    }

    /**
     * Clear datetime validation error
     * @param {HTMLInputElement} datetimeInput - DateTime input element
     */
    function clearUtsDateTimeError(datetimeInput) {
        datetimeInput.classList.remove('border-red-300');
        datetimeInput.classList.add('border-gray-300');

        const existingError = datetimeInput.parentNode.querySelector('.text-red-500:not(.required-asterisk)');
        if (existingError && !existingError.textContent.includes('*')) {
            existingError.remove();
        }
    }

    /**
     * Validate file input
     * @param {HTMLInputElement} fileInput - File input element
     * @returns {boolean} - Is valid
     */
    function validateUtsFileInput(fileInput) {
        if (!fileInput.files || fileInput.files.length === 0) {
            return true; // No file selected is OK
        }

        const file = fileInput.files[0];
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        // Check file size
        if (file.size > maxSize) {
            showUtsFileError(fileInput, 'Ukuran file tidak boleh lebih dari 10MB');
            return false;
        }

        // Check file type
        if (!allowedTypes.includes(file.type)) {
            showUtsFileError(fileInput, 'Format file harus: PDF, DOC, DOCX');
            return false;
        }

        // Clear any previous errors
        clearUtsFileError(fileInput);
        return true;
    }

    /**
     * Show file validation error
     * @param {HTMLInputElement} fileInput - File input element
     * @param {string} message - Error message
     */
    function showUtsFileError(fileInput, message) {
        clearUtsFileError(fileInput);

        fileInput.classList.add('border-red-300');

        const errorElement = document.createElement('p');
        errorElement.className = 'text-xs text-red-500 mt-1';
        errorElement.textContent = message;

        fileInput.parentNode.appendChild(errorElement);
    }

    /**
     * Clear file validation error
     * @param {HTMLInputElement} fileInput - File input element
     */
    function clearUtsFileError(fileInput) {
        fileInput.classList.remove('border-red-300');
        fileInput.classList.add('border-gray-300');

        const existingError = fileInput.parentNode.querySelector('.text-red-500:not(.required-asterisk)');
        if (existingError && !existingError.textContent.includes('*')) {
            existingError.remove();
        }
    }

    // ============ EVENT LISTENERS ============

    document.addEventListener('DOMContentLoaded', function() {

        // File input validation for create modal
        const createFileInput = document.querySelector('#utsModal input[name="dokumen"]');
        if (createFileInput) {
            createFileInput.addEventListener('change', function() {
                validateUtsFileInput(this);
            });
        }

        // File input validation for edit modal
        const editFileInput = document.querySelector('#editUtsModal input[name="dokumen"]');
        if (editFileInput) {
            editFileInput.addEventListener('change', function() {
                validateUtsFileInput(this);
            });
        }

        // DateTime validation for create modal
        const createDateTimeInput = document.querySelector('#utsModal input[name="batas_akhir_pengumpulan"]');
        if (createDateTimeInput) {
            createDateTimeInput.addEventListener('change', function() {
                validateUtsDateTimeInput(this);
            });
            createDateTimeInput.addEventListener('blur', function() {
                validateUtsDateTimeInput(this);
            });
        }

        // DateTime validation for edit modal
        const editDateTimeInput = document.querySelector(
            '#editUtsModal input[name="batas_akhir_pengumpulan"]');
        if (editDateTimeInput) {
            editDateTimeInput.addEventListener('change', function() {
                validateUtsDateTimeInput(this);
            });
            editDateTimeInput.addEventListener('blur', function() {
                validateUtsDateTimeInput(this);
            });
        }

        // Form submission validation for create
        const createForm = document.getElementById('utsForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                let isValid = true;

                // Validate file input
                const fileInput = this.querySelector('input[name="dokumen"]');
                if (fileInput && !validateUtsFileInput(fileInput)) {
                    isValid = false;
                }

                // Validate datetime input
                const datetimeInput = this.querySelector('input[name="batas_akhir_pengumpulan"]');
                if (datetimeInput && !validateUtsDateTimeInput(datetimeInput)) {
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Form submission validation for edit
        const editForm = document.getElementById('editUtsForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                let isValid = true;

                // Validate file input
                const fileInput = this.querySelector('input[name="dokumen"]');
                if (fileInput && !validateUtsFileInput(fileInput)) {
                    isValid = false;
                }

                // Validate datetime input
                const datetimeInput = this.querySelector('input[name="batas_akhir_pengumpulan"]');
                if (datetimeInput && !validateUtsDateTimeInput(datetimeInput)) {
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                // Close any open uts modals
                closeUtsModal();
                closeEditUtsModal();
                closeDeleteUtsModal();
            }
        });

        // Click outside modal to close
        const modals = ['utsModal', 'editUtsModal', 'deleteUtsModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        // Clicked on backdrop
                        if (modalId === 'utsModal') closeUtsModal();
                        else if (modalId === 'editUtsModal') closeEditUtsModal();
                        else if (modalId === 'deleteUtsModal') closeDeleteUtsModal();
                    }
                });
            }
        });
    });

    // ============ GLOBAL FUNCTION ASSIGNMENTS ============

    // Make functions globally accessible
    window.openUtsModal = openUtsModal;
    window.closeUtsModal = closeUtsModal;
    window.openEditUtsModal = openEditUtsModal;
    window.closeEditUtsModal = closeEditUtsModal;
    window.openDeleteUtsModal = openDeleteUtsModal;
    window.closeDeleteUtsModal = closeDeleteUtsModal;

    /**
     * ========================================
     * END UTS MODAL FUNCTIONS
     * ========================================
     */
</script>

<script>
    /**
     * ========================================
     * UTS STATUS MODAL FUNCTIONS
     * ========================================
     */

    // Global variables
    let currentPengumpulanUtsData = null;
    let currentUtsId = null;

    // ============ STATUS MODAL FUNCTIONS ============

    /**
     * Open status pengumpulan UTS modal
     */
    function openStatusPengumpulanUtsModal(utsId) {
        const modal = document.getElementById('statusPengumpulanUtsModal');

        // Reset modal state
        document.getElementById('pengumpulan-uts-loading').classList.remove('hidden');
        document.getElementById('pengumpulan-uts-content').classList.add('hidden');

        // Show modal
        modal.classList.remove('hidden');

        // Load status data
        loadStatusPengumpulanUts(utsId);
    }

    /**
     * Close status pengumpulan UTS modal
     */
    function closeStatusPengumpulanUtsModal() {
        const modal = document.getElementById('statusPengumpulanUtsModal');
        modal.classList.add('hidden');

        // Reset global variables
        currentPengumpulanUtsData = null;
        currentUtsId = null;
    }

    /**
     * Load status pengumpulan UTS data
     */
    async function loadStatusPengumpulanUts(utsId) {
        try {
            const response = await fetch(`/uts/${utsId}/status`);
            const data = await response.json();

            if (data.success) {
                // Hide loading
                document.getElementById('pengumpulan-uts-loading').classList.add('hidden');

                // Fill basic info
                const statusJudul = document.getElementById('status_uts_judul');
                if (statusJudul) statusJudul.textContent = data.data.judul || 'Tidak ada judul';

                // Update filter counts
                const countAll = document.getElementById('count-all-pengumpulan-uts');
                const countSudahMengumpulkan = document.getElementById('count-sudah-mengumpulkan-uts');
                const countBelumMengumpulkan = document.getElementById('count-belum-mengumpulkan-uts');
                const countSudahDinilai = document.getElementById('count-sudah-dinilai-uts');
                const countBelumDinilai = document.getElementById('count-belum-dinilai-uts');

                if (data.data.daftar_mahasiswa) {
                    const sudahMengumpulkanCount = data.data.daftar_mahasiswa.filter(m => m.sudah_mengumpulkan)
                        .length;
                    const belumMengumpulkanCount = data.data.daftar_mahasiswa.filter(m => !m.sudah_mengumpulkan)
                        .length;
                    const sudahDinilaiCount = data.data.daftar_mahasiswa.filter(m => m.sudah_mengumpulkan && m
                        .nilai !== null).length;
                    const belumDinilaiCount = data.data.daftar_mahasiswa.filter(m => m.sudah_mengumpulkan && m
                        .nilai === null).length;

                    if (countAll) countAll.textContent = data.data.total_mahasiswa;
                    if (countSudahMengumpulkan) countSudahMengumpulkan.textContent = sudahMengumpulkanCount;
                    if (countBelumMengumpulkan) countBelumMengumpulkan.textContent = belumMengumpulkanCount;
                    if (countSudahDinilai) countSudahDinilai.textContent = sudahDinilaiCount;
                    if (countBelumDinilai) countBelumDinilai.textContent = belumDinilaiCount;

                    // Store data globally for filtering
                    currentPengumpulanUtsData = data.data.daftar_mahasiswa;
                    currentUtsId = utsId;

                    // Render student table
                    renderPengumpulanUtsTable(data.data.daftar_mahasiswa);

                    // Setup search functionality
                    setupPengumpulanUtsSearch();
                }

                // Show content
                document.getElementById('pengumpulan-uts-content').classList.remove('hidden');
            } else {
                showPengumpulanUtsError('Gagal memuat data pengumpulan UTS');
            }
        } catch (error) {
            showPengumpulanUtsError('Terjadi kesalahan jaringan');
        }
    }

    /**
     * Render the table of students with their UTS submission status
     */
    function renderPengumpulanUtsTable(daftarMahasiswa, filterType = 'all') {
        const tableBody = document.getElementById('mahasiswa-pengumpulan-uts-table');

        if (!tableBody) {
            return;
        }

        if (!daftarMahasiswa || daftarMahasiswa.length === 0) {
            tableBody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-8 text-gray-500 text-sm">
                    <i class="fas fa-users text-2xl mb-2"></i>
                    <p>Tidak ada data mahasiswa</p>
                </td>
            </tr>
        `;
            return;
        }

        // Filter data based on filter type
        let filteredData = daftarMahasiswa;
        if (filterType === 'sudah_mengumpulkan') {
            filteredData = daftarMahasiswa.filter(m => m.sudah_mengumpulkan);
        } else if (filterType === 'belum_mengumpulkan') {
            filteredData = daftarMahasiswa.filter(m => !m.sudah_mengumpulkan);
        } else if (filterType === 'sudah_dinilai') {
            filteredData = daftarMahasiswa.filter(m => m.sudah_mengumpulkan && m.nilai !== null);
        } else if (filterType === 'belum_dinilai') {
            filteredData = daftarMahasiswa.filter(m => m.sudah_mengumpulkan && m.nilai === null);
        }

        // Sort by name
        filteredData.sort((a, b) => {
            const nameA = (a.nama || '').toLowerCase();
            const nameB = (b.nama || '').toLowerCase();
            return nameA.localeCompare(nameB);
        });

        let html = '';
        filteredData.forEach((mahasiswa, index) => {
            // Safely handle potentially null values
            const nama = mahasiswa.nama || 'Nama tidak tersedia';
            const nim = mahasiswa.nim || 'NIM tidak tersedia';
            const angkatan = mahasiswa.angkatan || 'N/A';
            const waktuSubmit = mahasiswa.waktu_submit || '-';

            const statusClass = mahasiswa.sudah_mengumpulkan ? 'bg-green-100 text-green-800' :
                'bg-red-100 text-red-800';
            const statusText = mahasiswa.sudah_mengumpulkan ? 'Sudah Mengumpulkan' : 'Belum Mengumpulkan';
            const statusIcon = mahasiswa.sudah_mengumpulkan ? 'check' : 'times';

            // File submission
            let fileHtml = '-';
            if (mahasiswa.sudah_mengumpulkan && mahasiswa.file_path) {
                const fileName = extractFileName(mahasiswa.file_path);
                fileHtml = `
                    <button onclick="downloadUtsSubmission('${mahasiswa.id_pengumpulan_uts}')" 
                        class="text-xs text-blue-600 hover:text-blue-800 underline"
                        title="${fileName}">
                        Lihat File
                    </button>
            `;
            }

            // Nilai (Grade)
            let nilaiHtml = '-';
            if (mahasiswa.sudah_mengumpulkan) {
                if (mahasiswa.nilai !== null) {
                    nilaiHtml = `
                        <span class="text-xs text-gray-900">
                            ${mahasiswa.nilai}
                        </span>
                `;
                } else {
                    nilaiHtml = `
                        <span class="text-xs text-gray-500">Belum dinilai</span>
                `;
                }
            }

            // Actions
            let actionsHtml = '-';
            if (mahasiswa.sudah_mengumpulkan) {
                const gradeButtonText = mahasiswa.nilai !== null ? 'Edit Nilai' : 'Beri Nilai';
                const gradeButtonIcon = mahasiswa.nilai !== null ? 'edit' : 'plus';

                actionsHtml = `
                    <button onclick="openQuickGradeUtsModal('${mahasiswa.id_pengumpulan_uts}', '${mahasiswa.nama}', '${mahasiswa.nim}', ${mahasiswa.nilai || 'null'})" 
                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-white-700 bg-gray-100 rounded hover:bg-gray-200 focus:outline-none"
                        title="${gradeButtonText}">
                        <i class="fas fa-${gradeButtonIcon}"></i>
                    </button>
            `;
            }

            html += `
            <tr class="mahasiswa-pengumpulan-uts-row hover:bg-gray-50 transition-colors duration-200" 
                data-nama="${nama.toLowerCase()}" 
                data-nim="${nim.toLowerCase()}" 
                data-angkatan="${angkatan.toLowerCase()}"
                data-status="${mahasiswa.sudah_mengumpulkan ? 'sudah_mengumpulkan' : 'belum_mengumpulkan'}"
                data-nilai-status="${mahasiswa.sudah_mengumpulkan ? (mahasiswa.nilai !== null ? 'sudah_dinilai' : 'belum_dinilai') : 'belum_mengumpulkan'}">
                <td class="px-4 py-3 whitespace-nowrap text-center text-xs text-gray-500">
                    ${index + 1}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-gray-900">
                    ${nim}
                </td>
                <td class="px-4 py-3 text-xs text-gray-900">
                    ${nama}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-700">
                    ${angkatan}
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${statusClass}">
                        <i class="fas fa-${statusIcon} mr-1"></i>
                        ${statusText}
                    </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-700">
                    ${fileHtml}
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-700">
                    ${waktuSubmit}
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    ${nilaiHtml}
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                    ${actionsHtml}
                </td>
            </tr>
        `;
        });

        if (filteredData.length === 0) {
            html = `
            <tr>
                <td colspan="9" class="text-center py-8 text-gray-500 text-sm">
                    <i class="fas fa-search text-2xl mb-2"></i>
                    <p>Tidak ada data yang sesuai dengan filter</p>
                </td>
            </tr>
        `;
        }

        tableBody.innerHTML = html;
    }

    /**
     * Filter mahasiswa by UTS submission status
     */
    function filterMahasiswaPengumpulanUts(filterType) {
        // Update active filter button
        document.querySelectorAll('.filter-pengumpulan-uts-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Find the clicked button and make it active
        const clickedBtn = event.target.closest('.filter-pengumpulan-uts-btn');
        if (clickedBtn) {
            clickedBtn.classList.add('active');
        }

        // Re-render table with filter
        if (currentPengumpulanUtsData) {
            renderPengumpulanUtsTable(currentPengumpulanUtsData, filterType);
        }
    }

    /**
     * Setup search functionality for UTS student table
     */
    function setupPengumpulanUtsSearch() {
        const searchInput = document.getElementById('searchMahasiswaPengumpulanUts');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.mahasiswa-pengumpulan-uts-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const nama = (row.getAttribute('data-nama') || '').toLowerCase();
                    const nim = (row.getAttribute('data-nim') || '').toLowerCase();
                    const angkatan = (row.getAttribute('data-angkatan') || '').toLowerCase();

                    const matches = nama.includes(searchTerm) ||
                        nim.includes(searchTerm) ||
                        angkatan.includes(searchTerm);

                    if (matches) {
                        row.style.display = '';
                        visibleCount++;

                        // Update row number
                        const numberCell = row.querySelector('td:first-child');
                        if (numberCell) {
                            numberCell.textContent = visibleCount;
                        }
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Handle no results message
                const tableBody = document.getElementById('mahasiswa-pengumpulan-uts-table');
                if (tableBody) {
                    const existingNoResults = tableBody.querySelector('.no-results-uts-row');
                    if (existingNoResults) {
                        existingNoResults.remove();
                    }

                    if (visibleCount === 0 && searchTerm.length > 0) {
                        const noResultsRow = document.createElement('tr');
                        noResultsRow.className = 'no-results-uts-row';
                        noResultsRow.innerHTML = `
                        <td colspan="9" class="text-center py-8 text-gray-500 text-sm">
                            <i class="fas fa-search text-2xl mb-2"></i>
                            <p>Tidak ditemukan mahasiswa dengan kata kunci "${searchTerm}"</p>
                        </td>
                    `;
                        tableBody.appendChild(noResultsRow);
                    }
                }
            });
        }
    }

    /**
     * Show error message in UTS status modal
     */
    function showPengumpulanUtsError(message) {
        document.getElementById('pengumpulan-uts-loading').innerHTML = `
        <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
        <p class="text-xs text-red-600">${message}</p>
    `;
    }

    // ============ GRADING FUNCTIONS ============

    /**
     * Open quick grade UTS modal
     */
    function openQuickGradeUtsModal(pengumpulanUtsId, namaMahasiswa, nimMahasiswa, currentNilai) {
        const modal = document.getElementById('quickGradeUtsModal');
        const form = document.getElementById('quickGradeUtsForm');

        // Reset form
        form.reset();

        // Fill student info
        document.getElementById('grade_uts_mahasiswa_nama').textContent = namaMahasiswa;
        document.getElementById('grade_uts_mahasiswa_nim').textContent = nimMahasiswa;
        document.getElementById('grade_uts_pengumpulan_id').value = pengumpulanUtsId;

        // Fill current grade if exists
        if (currentNilai && currentNilai !== 'null') {
            document.getElementById('grade_uts_nilai').value = currentNilai;
        }

        // Show modal
        modal.classList.remove('hidden');

        // Focus on grade input
        setTimeout(() => {
            const gradeInput = document.getElementById('grade_uts_nilai');
            if (gradeInput) {
                gradeInput.focus();
                gradeInput.select();
            }
        }, 100);
    }

    /**
     * Close quick grade UTS modal
     */
    function closeQuickGradeUtsModal() {
        const modal = document.getElementById('quickGradeUtsModal');
        const form = document.getElementById('quickGradeUtsForm');

        // Hide modal
        modal.classList.add('hidden');

        // Reset form
        form.reset();
    }

    /**
     * Submit quick grade UTS
     */
    async function submitQuickGradeUts(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const pengumpulanUtsId = formData.get('pengumpulan_id');
        const nilai = formData.get('nilai');

        // Validate nilai
        if (!nilai || nilai < 0 || nilai > 100) {
            showError('Nilai harus antara 0 dan 100', 'Validasi Gagal');
            return;
        }

        try {
            const response = await fetch(`/uts/pengumpulan/${pengumpulanUtsId}/grade`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: JSON.stringify({
                    nilai: parseFloat(nilai)
                })
            });

            const data = await response.json();

            if (data.success) {
                // Close modal
                closeQuickGradeUtsModal();

                // Reload data
                if (currentUtsId) {
                    loadStatusPengumpulanUts(currentUtsId);
                }

                // Show success message using existing toast system
                showSuccess('Nilai UTS berhasil disimpan');
            } else {
                showError('Gagal menyimpan nilai UTS: ' + (data.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            showError('Terjadi kesalahan saat menyimpan nilai UTS');
        }
    }

    // ============ DOWNLOAD FUNCTIONS ============

    /**
     * Download student UTS submission
     */
    async function downloadUtsSubmission(pengumpulanUtsId) {
        try {
            const response = await fetch(`/detail-kelas/pengumpulan-uts/${pengumpulanUtsId}/download`);

            if (response.ok) {
                const blob = await response.blob();
                const contentDisposition = response.headers.get('Content-Disposition');
                let fileName = 'pengumpulan_uts.pdf';

                if (contentDisposition) {
                    const fileNameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (fileNameMatch && fileNameMatch[1]) {
                        fileName = fileNameMatch[1].replace(/['"]/g, '');
                    }
                }

                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = fileName;
                a.click();
                window.URL.revokeObjectURL(url);

                // Show success message using existing toast system
                showSuccess('File UTS berhasil didownload');
            } else {
                showError('Gagal mendownload file pengumpulan UTS');
            }
        } catch (error) {
            showError('Terjadi kesalahan saat mendownload file UTS');
        }
    }

    /**
     * Export pengumpulan UTS data
     */
    function exportPengumpulanUts() {
        if (currentUtsId) {
            window.open(`/uts/${currentUtsId}/export`, '_blank');
            showInfo('Export data UTS sedang diproses...');
        } else {
            showError('Data UTS tidak tersedia untuk export');
        }
    }

    // ============ UTILITY FUNCTIONS ============

    /**
     * Extract filename from path
     */
    function extractFileName(path) {
        if (!path) return '';
        const parts = path.split(/[/\\]/);
        return parts[parts.length - 1] || path;
    }

    // ============ EVENT LISTENERS ============

    document.addEventListener('DOMContentLoaded', function() {
        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeStatusPengumpulanUtsModal();
                closeQuickGradeUtsModal();
            }
        });

        // Click outside modal to close
        const modals = ['statusPengumpulanUtsModal', 'quickGradeUtsModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        if (modalId === 'statusPengumpulanUtsModal')
                            closeStatusPengumpulanUtsModal();
                        else if (modalId === 'quickGradeUtsModal') closeQuickGradeUtsModal();
                    }
                });
            }
        });
    });

    // ============ GLOBAL FUNCTION ASSIGNMENTS ============

    // Make functions globally accessible
    window.openStatusPengumpulanUtsModal = openStatusPengumpulanUtsModal;
    window.closeStatusPengumpulanUtsModal = closeStatusPengumpulanUtsModal;
    window.filterMahasiswaPengumpulanUts = filterMahasiswaPengumpulanUts;
    window.exportPengumpulanUts = exportPengumpulanUts;
    window.openQuickGradeUtsModal = openQuickGradeUtsModal;
    window.closeQuickGradeUtsModal = closeQuickGradeUtsModal;
    window.submitQuickGradeUts = submitQuickGradeUts;
    window.downloadUtsSubmission = downloadUtsSubmission;

    /**
     * ========================================
     * END UTS STATUS MODAL FUNCTIONS
     * ========================================
     */
</script>
