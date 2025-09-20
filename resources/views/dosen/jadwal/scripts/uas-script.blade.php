{{-- File: resources/views/partials/scripts/uas-scripts.blade.php --}}

<script>
    /**
     * ========================================
     * UAS MODAL FUNCTIONS
     * ========================================
     */

    // ============ CREATE MODAL FUNCTIONS ============

    /**
     * Open create uas modal
     */
    function openUasModal() {
        const modal = document.getElementById('uasModal');
        const form = document.getElementById('uasForm');

        // Reset form
        form.reset();
        clearFormErrors(form);

        // Set default deadline (14 days from now for UAS)
        setDefaultUasDeadline();

        // Show modal
        modal.classList.remove('hidden');

        // Focus on first input
        setTimeout(() => {
            const firstInput = form.querySelector('input[name="judul"]');
            if (firstInput) firstInput.focus();
        }, 100);
    }

    /**
     * Close create uas modal
     */
    function closeUasModal() {
        const modal = document.getElementById('uasModal');
        const form = document.getElementById('uasForm');

        // Hide modal
        modal.classList.add('hidden');

        // Reset form
        form.reset();

        // Clear any validation errors
        clearFormErrors(form);
    }

    // ============ EDIT MODAL FUNCTIONS ============

    /**
     * Open edit uas modal
     * @param {string} uasId - ID of the uas
     * @param {string} judul - Title of the uas
     * @param {string} deskripsi - Description of the uas
     * @param {string} dokumen - Document path of the uas
     * @param {string} deadline - Deadline in ISO format
     */
    function openEditUasModal(uasId, judul, deskripsi, dokumen, deadline) {
        const modal = document.getElementById('editUasModal');
        const form = document.getElementById('editUasForm');
        const currentFileContainer = document.getElementById('current_uas_file_container');
        const currentFileName = document.getElementById('current_uas_file_name');
        const currentFileDownload = document.getElementById('current_uas_file_download');

        // Reset form
        form.reset();
        clearFormErrors(form);

        // Set form action
        form.action = `/uas/${uasId}`;

        // Fill form data
        document.getElementById('edit_uas_judul').value = judul || '';
        document.getElementById('edit_uas_deskripsi').value = deskripsi || '';

        // Format deadline for datetime-local input
        if (deadline) {
            const formattedDeadline = formatDateTimeForInput(deadline);
            document.getElementById('edit_uas_deadline').value = formattedDeadline;
        }

        // Handle current file display
        if (dokumen && dokumen.trim() !== '') {
            currentFileContainer.classList.remove('hidden');
            const fileName = extractFileName(dokumen);
            currentFileName.textContent = fileName;
            currentFileDownload.href = `/uas/${uasId}/download`;
        } else {
            currentFileContainer.classList.add('hidden');
        }

        // Show modal
        modal.classList.remove('hidden');

        // Focus on first input
        setTimeout(() => {
            const firstInput = document.getElementById('edit_uas_judul');
            if (firstInput) {
                firstInput.focus();
                firstInput.select();
            }
        }, 100);
    }

    /**
     * Close edit uas modal
     */
    function closeEditUasModal() {
        const modal = document.getElementById('editUasModal');
        const form = document.getElementById('editUasForm');
        const currentFileContainer = document.getElementById('current_uas_file_container');

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
     * Open delete uas modal
     * @param {string} uasId - ID of the uas
     * @param {string} judul - Title of the uas
     * @param {string} dokumen - Document path of the uas
     * @param {string} deadline - Deadline in ISO format
     */
    function openDeleteUasModal(uasId, judul, dokumen, deadline) {
        const modal = document.getElementById('deleteUasModal');
        const form = document.getElementById('deleteUasForm');

        // Set form action
        form.action = `/uas/${uasId}`;

        // Fill uas info for confirmation
        document.getElementById('delete_uas_judul').textContent = judul || 'Tidak ada judul';

        // Format deadline for display
        if (deadline) {
            const formattedDeadline = formatDateTimeForDisplay(deadline);
            document.getElementById('delete_uas_deadline').textContent = `Deadline: ${formattedDeadline}`;
        } else {
            document.getElementById('delete_uas_deadline').textContent = 'Tidak ada deadline';
        }

        // Handle file info
        const fileElement = document.getElementById('delete_uas_file');
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
     * Close delete uas modal
     */
    function closeDeleteUasModal() {
        const modal = document.getElementById('deleteUasModal');
        modal.classList.add('hidden');
    }

    // ============ UTILITY FUNCTIONS ============

    /**
     * Set default deadline for UAS (14 days from now)
     */
    function setDefaultUasDeadline() {
        const defaultDeadlineInput = document.querySelector('#uasModal input[name="batas_akhir_pengumpulan"]');
        if (defaultDeadlineInput) {
            const twoWeeks = new Date();
            twoWeeks.setDate(twoWeeks.getDate() + 14);
            twoWeeks.setHours(23, 59); // Set to 23:59

            const formattedDate = formatDateTimeForInput(twoWeeks.toISOString());
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
    function validateUasDateTimeInput(datetimeInput) {
        const value = datetimeInput.value;
        if (!value) {
            showUasDateTimeError(datetimeInput, 'Batas akhir pengumpulan harus diisi');
            return false;
        }

        const selectedDate = new Date(value);
        const now = new Date();

        // Clear any previous errors
        clearUasDateTimeError(datetimeInput);
        return true;
    }

    /**
     * Show datetime validation error
     * @param {HTMLInputElement} datetimeInput - DateTime input element
     * @param {string} message - Error message
     */
    function showUasDateTimeError(datetimeInput, message) {
        clearUasDateTimeError(datetimeInput);

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
    function clearUasDateTimeError(datetimeInput) {
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
    function validateUasFileInput(fileInput) {
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
            showUasFileError(fileInput, 'Ukuran file tidak boleh lebih dari 10MB');
            return false;
        }

        // Check file type
        if (!allowedTypes.includes(file.type)) {
            showUasFileError(fileInput, 'Format file harus: PDF, DOC, DOCX');
            return false;
        }

        // Clear any previous errors
        clearUasFileError(fileInput);
        return true;
    }

    /**
     * Show file validation error
     * @param {HTMLInputElement} fileInput - File input element
     * @param {string} message - Error message
     */
    function showUasFileError(fileInput, message) {
        clearUasFileError(fileInput);

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
    function clearUasFileError(fileInput) {
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
        const createFileInput = document.querySelector('#uasModal input[name="dokumen"]');
        if (createFileInput) {
            createFileInput.addEventListener('change', function() {
                validateUasFileInput(this);
            });
        }

        // File input validation for edit modal
        const editFileInput = document.querySelector('#editUasModal input[name="dokumen"]');
        if (editFileInput) {
            editFileInput.addEventListener('change', function() {
                validateUasFileInput(this);
            });
        }

        // DateTime validation for create modal
        const createDateTimeInput = document.querySelector('#uasModal input[name="batas_akhir_pengumpulan"]');
        if (createDateTimeInput) {
            createDateTimeInput.addEventListener('change', function() {
                validateUasDateTimeInput(this);
            });
            createDateTimeInput.addEventListener('blur', function() {
                validateUasDateTimeInput(this);
            });
        }

        // DateTime validation for edit modal
        const editDateTimeInput = document.querySelector(
            '#editUasModal input[name="batas_akhir_pengumpulan"]');
        if (editDateTimeInput) {
            editDateTimeInput.addEventListener('change', function() {
                validateUasDateTimeInput(this);
            });
            editDateTimeInput.addEventListener('blur', function() {
                validateUasDateTimeInput(this);
            });
        }

        // Form submission validation for create
        const createForm = document.getElementById('uasForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                let isValid = true;

                // Validate file input
                const fileInput = this.querySelector('input[name="dokumen"]');
                if (fileInput && !validateUasFileInput(fileInput)) {
                    isValid = false;
                }

                // Validate datetime input
                const datetimeInput = this.querySelector('input[name="batas_akhir_pengumpulan"]');
                if (datetimeInput && !validateUasDateTimeInput(datetimeInput)) {
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Form submission validation for edit
        const editForm = document.getElementById('editUasForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                let isValid = true;

                // Validate file input
                const fileInput = this.querySelector('input[name="dokumen"]');
                if (fileInput && !validateUasFileInput(fileInput)) {
                    isValid = false;
                }

                // Validate datetime input
                const datetimeInput = this.querySelector('input[name="batas_akhir_pengumpulan"]');
                if (datetimeInput && !validateUasDateTimeInput(datetimeInput)) {
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
                // Close any open uas modals
                closeUasModal();
                closeEditUasModal();
                closeDeleteUasModal();
            }
        });

        // Click outside modal to close
        const modals = ['uasModal', 'editUasModal', 'deleteUasModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        // Clicked on backdrop
                        if (modalId === 'uasModal') closeUasModal();
                        else if (modalId === 'editUasModal') closeEditUasModal();
                        else if (modalId === 'deleteUasModal') closeDeleteUasModal();
                    }
                });
            }
        });
    });

    // ============ GLOBAL FUNCTION ASSIGNMENTS ============

    // Make functions globally accessible
    window.openUasModal = openUasModal;
    window.closeUasModal = closeUasModal;
    window.openEditUasModal = openEditUasModal;
    window.closeEditUasModal = closeEditUasModal;
    window.openDeleteUasModal = openDeleteUasModal;
    window.closeDeleteUasModal = closeDeleteUasModal;

    /**
     * ========================================
     * END UAS MODAL FUNCTIONS
     * ========================================
     */
</script>

<script>
    /**
     * ========================================
     * UAS STATUS MODAL FUNCTIONS
     * ========================================
     */

    // Global variables
    let currentPengumpulanUasData = null;
    let currentUasId = null;

    // ============ STATUS MODAL FUNCTIONS ============

    /**
     * Open status pengumpulan UAS modal
     */
    function openStatusPengumpulanUasModal(uasId) {
        const modal = document.getElementById('statusPengumpulanUasModal');

        // Reset modal state
        document.getElementById('pengumpulan-uas-loading').classList.remove('hidden');
        document.getElementById('pengumpulan-uas-content').classList.add('hidden');

        // Show modal
        modal.classList.remove('hidden');

        // Load status data
        loadStatusPengumpulanUas(uasId);
    }

    /**
     * Close status pengumpulan UAS modal
     */
    function closeStatusPengumpulanUasModal() {
        const modal = document.getElementById('statusPengumpulanUasModal');
        modal.classList.add('hidden');

        // Reset global variables
        currentPengumpulanUasData = null;
        currentUasId = null;
    }

    /**
     * Load status pengumpulan UAS data
     */
    async function loadStatusPengumpulanUas(uasId) {
        try {
            const response = await fetch(`/uas/${uasId}/status`);
            const data = await response.json();

            if (data.success) {
                // Hide loading
                document.getElementById('pengumpulan-uas-loading').classList.add('hidden');

                // Fill basic info
                const statusJudul = document.getElementById('status_uas_judul');
                if (statusJudul) statusJudul.textContent = data.data.judul || 'Tidak ada judul';

                // Update filter counts
                const countAll = document.getElementById('count-all-pengumpulan-uas');
                const countSudahMengumpulkan = document.getElementById('count-sudah-mengumpulkan-uas');
                const countBelumMengumpulkan = document.getElementById('count-belum-mengumpulkan-uas');
                const countSudahDinilai = document.getElementById('count-sudah-dinilai-uas');
                const countBelumDinilai = document.getElementById('count-belum-dinilai-uas');

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
                    currentPengumpulanUasData = data.data.daftar_mahasiswa;
                    currentUasId = uasId;

                    // Render student table
                    renderPengumpulanUasTable(data.data.daftar_mahasiswa);

                    // Setup search functionality
                    setupPengumpulanUasSearch();
                }

                // Show content
                document.getElementById('pengumpulan-uas-content').classList.remove('hidden');
            } else {
                showPengumpulanUasError('Gagal memuat data pengumpulan UAS');
            }
        } catch (error) {
            console.error('Error loading pengumpulan UAS status:', error);
            showPengumpulanUasError('Terjadi kesalahan jaringan');
        }
    }

    /**
     * Render the table of students with their UAS submission status
     */
    function renderPengumpulanUasTable(daftarMahasiswa, filterType = 'all') {
        const tableBody = document.getElementById('mahasiswa-pengumpulan-uas-table');

        if (!tableBody) {
            console.warn('mahasiswa-pengumpulan-uas-table element not found');
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
                    <button onclick="downloadUasSubmission('${mahasiswa.id_pengumpulan_uas}')" 
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
                    <button onclick="openQuickGradeUasModal('${mahasiswa.id_pengumpulan_uas}', '${mahasiswa.nama}', '${mahasiswa.nim}', ${mahasiswa.nilai || 'null'})" 
                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-white-700 bg-gray-100 rounded hover:bg-gray-200 focus:outline-none"
                        title="${gradeButtonText}">
                        <i class="fas fa-${gradeButtonIcon}"></i>
                    </button>
            `;
            }

            html += `
            <tr class="mahasiswa-pengumpulan-uas-row hover:bg-gray-50 transition-colors duration-200" 
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
     * Filter mahasiswa by UAS submission status
     */
    function filterMahasiswaPengumpulanUas(filterType) {
        // Update active filter button
        document.querySelectorAll('.filter-pengumpulan-uas-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Find the clicked button and make it active
        const clickedBtn = event.target.closest('.filter-pengumpulan-uas-btn');
        if (clickedBtn) {
            clickedBtn.classList.add('active');
        }

        // Re-render table with filter
        if (currentPengumpulanUasData) {
            renderPengumpulanUasTable(currentPengumpulanUasData, filterType);
        }
    }

    /**
     * Setup search functionality for UAS student table
     */
    function setupPengumpulanUasSearch() {
        const searchInput = document.getElementById('searchMahasiswaPengumpulanUas');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.mahasiswa-pengumpulan-uas-row');
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
                const tableBody = document.getElementById('mahasiswa-pengumpulan-uas-table');
                if (tableBody) {
                    const existingNoResults = tableBody.querySelector('.no-results-uas-row');
                    if (existingNoResults) {
                        existingNoResults.remove();
                    }

                    if (visibleCount === 0 && searchTerm.length > 0) {
                        const noResultsRow = document.createElement('tr');
                        noResultsRow.className = 'no-results-uas-row';
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
     * Show error message in UAS status modal
     */
    function showPengumpulanUasError(message) {
        document.getElementById('pengumpulan-uas-loading').innerHTML = `
        <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
        <p class="text-xs text-red-600">${message}</p>
    `;
    }

    // ============ GRADING FUNCTIONS ============

    /**
     * Open quick grade UAS modal
     */
    function openQuickGradeUasModal(pengumpulanUasId, namaMahasiswa, nimMahasiswa, currentNilai) {
        const modal = document.getElementById('quickGradeUasModal');
        const form = document.getElementById('quickGradeUasForm');

        // Reset form
        form.reset();

        // Fill student info
        document.getElementById('grade_uas_mahasiswa_nama').textContent = namaMahasiswa;
        document.getElementById('grade_uas_mahasiswa_nim').textContent = nimMahasiswa;
        document.getElementById('grade_uas_pengumpulan_id').value = pengumpulanUasId;

        // Fill current grade if exists
        if (currentNilai && currentNilai !== 'null') {
            document.getElementById('grade_uas_nilai').value = currentNilai;
        }

        // Show modal
        modal.classList.remove('hidden');

        // Focus on grade input
        setTimeout(() => {
            const gradeInput = document.getElementById('grade_uas_nilai');
            if (gradeInput) {
                gradeInput.focus();
                gradeInput.select();
            }
        }, 100);
    }

    /**
     * Close quick grade UAS modal
     */
    function closeQuickGradeUasModal() {
        const modal = document.getElementById('quickGradeUasModal');
        const form = document.getElementById('quickGradeUasForm');

        // Hide modal
        modal.classList.add('hidden');

        // Reset form
        form.reset();
    }

    /**
     * Submit quick grade UAS
     */
    async function submitQuickGradeUas(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const pengumpulanUasId = formData.get('pengumpulan_id');
        const nilai = formData.get('nilai');

        // Validate nilai
        if (!nilai || nilai < 0 || nilai > 100) {
            showError('Nilai harus antara 0 dan 100', 'Validasi Gagal');
            return;
        }

        try {
            const response = await fetch(`/uas/pengumpulan/${pengumpulanUasId}/grade`, {
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
                closeQuickGradeUasModal();

                // Reload data
                if (currentUasId) {
                    loadStatusPengumpulanUas(currentUasId);
                }

                // Show success message using existing toast system
                showSuccess('Nilai UAS berhasil disimpan');
            } else {
                showError('Gagal menyimpan nilai UAS: ' + (data.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            console.error('Error submitting UAS grade:', error);
            showError('Terjadi kesalahan saat menyimpan nilai UAS');
        }
    }

    // ============ DOWNLOAD FUNCTIONS ============

    /**
     * Download student UAS submission
     */
    async function downloadUasSubmission(pengumpulanUasId) {
        try {
            const response = await fetch(`/detail-kelas/pengumpulan-uas/${pengumpulanUasId}/download`);

            if (response.ok) {
                const blob = await response.blob();
                const contentDisposition = response.headers.get('Content-Disposition');
                let fileName = 'pengumpulan_uas.pdf';

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
                showSuccess('File UAS berhasil didownload');
            } else {
                showError('Gagal mendownload file pengumpulan UAS');
            }
        } catch (error) {
            console.error('Error downloading UAS submission:', error);
            showError('Terjadi kesalahan saat mendownload file UAS');
        }
    }

    /**
     * Export pengumpulan UAS data
     */
    function exportPengumpulanUas() {
        if (currentUasId) {
            window.open(`/uas/${currentUasId}/export`, '_blank');
            showInfo('Export data UAS sedang diproses...');
        } else {
            showError('Data UAS tidak tersedia untuk export');
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
                closeStatusPengumpulanUasModal();
                closeQuickGradeUasModal();
            }
        });

        // Click outside modal to close
        const modals = ['statusPengumpulanUasModal', 'quickGradeUasModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        if (modalId === 'statusPengumpulanUasModal')
                            closeStatusPengumpulanUasModal();
                        else if (modalId === 'quickGradeUasModal') closeQuickGradeUasModal();
                    }
                });
            }
        });
    });

    // ============ GLOBAL FUNCTION ASSIGNMENTS ============

    // Make functions globally accessible
    window.openStatusPengumpulanUasModal = openStatusPengumpulanUasModal;
    window.closeStatusPengumpulanUasModal = closeStatusPengumpulanUasModal;
    window.filterMahasiswaPengumpulanUas = filterMahasiswaPengumpulanUas;
    window.exportPengumpulanUas = exportPengumpulanUas;
    window.openQuickGradeUasModal = openQuickGradeUasModal;
    window.closeQuickGradeUasModal = closeQuickGradeUasModal;
    window.submitQuickGradeUas = submitQuickGradeUas;
    window.downloadUasSubmission = downloadUasSubmission;

    /**
     * ========================================
     * END UAS STATUS MODAL FUNCTIONS
     * ========================================
     */
</script>
