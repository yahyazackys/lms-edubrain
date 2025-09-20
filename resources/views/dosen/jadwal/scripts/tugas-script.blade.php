{{-- File: resources/views/partials/scripts/tugas-scripts.blade.php --}}

<script>
    /**
     * ========================================
     * TUGAS MODAL FUNCTIONS
     * ========================================
     */

    // ============ CREATE MODAL FUNCTIONS ============

    /**
     * Open create tugas modal
     */
    function openTugasModal() {
        const modal = document.getElementById('tugasModal');
        const form = document.getElementById('tugasForm');

        // Reset form
        form.reset();
        clearFormErrors(form);

        // Set default deadline (24 hours from now)
        setDefaultDeadline();

        // Show modal
        modal.classList.remove('hidden');

        // Focus on first input
        setTimeout(() => {
            const firstInput = form.querySelector('input[name="judul"]');
            if (firstInput) firstInput.focus();
        }, 100);
    }

    /**
     * Close create tugas modal
     */
    function closeTugasModal() {
        const modal = document.getElementById('tugasModal');
        const form = document.getElementById('tugasForm');

        // Hide modal
        modal.classList.add('hidden');

        // Reset form
        form.reset();

        // Clear any validation errors
        clearFormErrors(form);
    }

    // ============ EDIT MODAL FUNCTIONS ============

    /**
     * Open edit tugas modal
     * @param {string} tugasId - ID of the tugas
     * @param {string} judul - Title of the tugas
     * @param {string} deskripsi - Description of the tugas
     * @param {string} dokumen - Document path of the tugas
     * @param {string} deadline - Deadline in ISO format
     */
    function openEditTugasModal(tugasId, judul, deskripsi, dokumen, deadline) {
        const modal = document.getElementById('editTugasModal');
        const form = document.getElementById('editTugasForm');
        const currentFileContainer = document.getElementById('current_tugas_file_container');
        const currentFileName = document.getElementById('current_tugas_file_name');
        const currentFileDownload = document.getElementById('current_tugas_file_download');

        // Reset form
        form.reset();
        clearFormErrors(form);

        // Set form action
        form.action = `/tugas/${tugasId}`;

        // Fill form data
        document.getElementById('edit_tugas_judul').value = judul || '';
        document.getElementById('edit_tugas_deskripsi').value = deskripsi || '';

        // Format deadline for datetime-local input
        if (deadline) {
            const formattedDeadline = formatDateTimeForInput(deadline);
            document.getElementById('edit_tugas_deadline').value = formattedDeadline;
        }

        // Handle current file display
        if (dokumen && dokumen.trim() !== '') {
            currentFileContainer.classList.remove('hidden');
            const fileName = extractFileName(dokumen);
            currentFileName.textContent = fileName;
            currentFileDownload.href = `/tugas/${tugasId}/download`;
        } else {
            currentFileContainer.classList.add('hidden');
        }

        // Show modal
        modal.classList.remove('hidden');

        // Focus on first input
        setTimeout(() => {
            const firstInput = document.getElementById('edit_tugas_judul');
            if (firstInput) {
                firstInput.focus();
                firstInput.select();
            }
        }, 100);
    }

    /**
     * Close edit tugas modal
     */
    function closeEditTugasModal() {
        const modal = document.getElementById('editTugasModal');
        const form = document.getElementById('editTugasForm');
        const currentFileContainer = document.getElementById('current_tugas_file_container');

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
     * Open delete tugas modal
     * @param {string} tugasId - ID of the tugas
     * @param {string} judul - Title of the tugas
     * @param {string} dokumen - Document path of the tugas
     * @param {string} deadline - Deadline in ISO format
     */
    function openDeleteTugasModal(tugasId, judul, dokumen, deadline) {
        const modal = document.getElementById('deleteTugasModal');
        const form = document.getElementById('deleteTugasForm');

        // Set form action
        form.action = `/tugas/${tugasId}`;

        // Fill tugas info for confirmation
        document.getElementById('delete_tugas_judul').textContent = judul || 'Tidak ada judul';

        // Format deadline for display
        if (deadline) {
            const formattedDeadline = formatDateTimeForDisplay(deadline);
            document.getElementById('delete_tugas_deadline').textContent = `Deadline: ${formattedDeadline}`;
        } else {
            document.getElementById('delete_tugas_deadline').textContent = 'Tidak ada deadline';
        }

        // Handle file info
        const fileElement = document.getElementById('delete_tugas_file');
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
     * Close delete tugas modal
     */
    function closeDeleteTugasModal() {
        const modal = document.getElementById('deleteTugasModal');
        modal.classList.add('hidden');
    }

    // ============ UTILITY FUNCTIONS ============

    /**
     * Set default deadline (24 hours from now)
     */
    function setDefaultDeadline() {
        const defaultDeadlineInput = document.querySelector('#tugasModal input[name="batas_akhir_pengumpulan"]');
        if (defaultDeadlineInput) {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            tomorrow.setHours(23, 59); // Set to 23:59

            const formattedDate = formatDateTimeForInput(tomorrow.toISOString());
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
    function validateDateTimeInput(datetimeInput) {
        const value = datetimeInput.value;
        if (!value) {
            showDateTimeError(datetimeInput, 'Batas akhir pengumpulan harus diisi');
            return false;
        }

        const selectedDate = new Date(value);
        const now = new Date();

        // Clear any previous errors
        clearDateTimeError(datetimeInput);
        return true;
    }

    /**
     * Show datetime validation error
     * @param {HTMLInputElement} datetimeInput - DateTime input element
     * @param {string} message - Error message
     */
    function showDateTimeError(datetimeInput, message) {
        clearDateTimeError(datetimeInput);

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
    function clearDateTimeError(datetimeInput) {
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
    function validateTugasFileInput(fileInput) {
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
            showTugasFileError(fileInput, 'Ukuran file tidak boleh lebih dari 10MB');
            return false;
        }

        // Check file type
        if (!allowedTypes.includes(file.type)) {
            showTugasFileError(fileInput, 'Format file harus: PDF, DOC, DOCX');
            return false;
        }

        // Clear any previous errors
        clearTugasFileError(fileInput);
        return true;
    }

    /**
     * Show file validation error
     * @param {HTMLInputElement} fileInput - File input element
     * @param {string} message - Error message
     */
    function showTugasFileError(fileInput, message) {
        clearTugasFileError(fileInput);

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
    function clearTugasFileError(fileInput) {
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
        const createFileInput = document.querySelector('#tugasModal input[name="dokumen"]');
        if (createFileInput) {
            createFileInput.addEventListener('change', function() {
                validateTugasFileInput(this);
            });
        }

        // File input validation for edit modal
        const editFileInput = document.querySelector('#editTugasModal input[name="dokumen"]');
        if (editFileInput) {
            editFileInput.addEventListener('change', function() {
                validateTugasFileInput(this);
            });
        }

        // DateTime validation for create modal
        const createDateTimeInput = document.querySelector('#tugasModal input[name="batas_akhir_pengumpulan"]');
        if (createDateTimeInput) {
            createDateTimeInput.addEventListener('change', function() {
                validateDateTimeInput(this);
            });
            createDateTimeInput.addEventListener('blur', function() {
                validateDateTimeInput(this);
            });
        }

        // DateTime validation for edit modal
        const editDateTimeInput = document.querySelector(
            '#editTugasModal input[name="batas_akhir_pengumpulan"]');
        if (editDateTimeInput) {
            editDateTimeInput.addEventListener('change', function() {
                validateDateTimeInput(this);
            });
            editDateTimeInput.addEventListener('blur', function() {
                validateDateTimeInput(this);
            });
        }

        // Form submission validation for create
        const createForm = document.getElementById('tugasForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                let isValid = true;

                // Validate file input
                const fileInput = this.querySelector('input[name="dokumen"]');
                if (fileInput && !validateTugasFileInput(fileInput)) {
                    isValid = false;
                }

                // Validate datetime input
                const datetimeInput = this.querySelector('input[name="batas_akhir_pengumpulan"]');
                if (datetimeInput && !validateDateTimeInput(datetimeInput)) {
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Form submission validation for edit
        const editForm = document.getElementById('editTugasForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                let isValid = true;

                // Validate file input
                const fileInput = this.querySelector('input[name="dokumen"]');
                if (fileInput && !validateTugasFileInput(fileInput)) {
                    isValid = false;
                }

                // Validate datetime input
                const datetimeInput = this.querySelector('input[name="batas_akhir_pengumpulan"]');
                if (datetimeInput && !validateDateTimeInput(datetimeInput)) {
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
                // Close any open tugas modals
                closeTugasModal();
                closeEditTugasModal();
                closeDeleteTugasModal();
            }
        });

        // Click outside modal to close
        const modals = ['tugasModal', 'editTugasModal', 'deleteTugasModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        // Clicked on backdrop
                        if (modalId === 'tugasModal') closeTugasModal();
                        else if (modalId === 'editTugasModal') closeEditTugasModal();
                        else if (modalId === 'deleteTugasModal') closeDeleteTugasModal();
                    }
                });
            }
        });
    });

    // ============ GLOBAL FUNCTION ASSIGNMENTS ============

    // Make functions globally accessible
    window.openTugasModal = openTugasModal;
    window.closeTugasModal = closeTugasModal;
    window.openEditTugasModal = openEditTugasModal;
    window.closeEditTugasModal = closeEditTugasModal;
    window.openDeleteTugasModal = openDeleteTugasModal;
    window.closeDeleteTugasModal = closeDeleteTugasModal;

    /**
     * ========================================
     * END TUGAS MODAL FUNCTIONS
     * ========================================
     */
</script>

<script>
    /**
     * ========================================
     * TUGAS STATUS MODAL FUNCTIONS
     * ========================================
     */

    // Global variables
    let currentPengumpulanData = null;
    let currentTugasId = null;

    // ============ STATUS MODAL FUNCTIONS ============

    /**
     * Open status pengumpulan modal
     */
    function openStatusPengumpulanModal(tugasId) {
        const modal = document.getElementById('statusPengumpulanModal');

        // Reset modal state
        document.getElementById('pengumpulan-loading').classList.remove('hidden');
        document.getElementById('pengumpulan-content').classList.add('hidden');

        // Show modal
        modal.classList.remove('hidden');

        // Load status data
        loadStatusPengumpulan(tugasId);
    }

    /**
     * Close status pengumpulan modal
     */
    function closeStatusPengumpulanModal() {
        const modal = document.getElementById('statusPengumpulanModal');
        modal.classList.add('hidden');

        // Reset global variables
        currentPengumpulanData = null;
        currentTugasId = null;
    }

    /**
     * Load status pengumpulan data
     */
    async function loadStatusPengumpulan(tugasId) {
        try {
            const response = await fetch(`/tugas/${tugasId}/status`);
            const data = await response.json();

            if (data.success) {
                // Hide loading
                document.getElementById('pengumpulan-loading').classList.add('hidden');

                // Fill basic info
                const statusJudul = document.getElementById('status_tugas_judul');
                if (statusJudul) statusJudul.textContent = data.data.judul || 'Tidak ada judul';

                // Update filter counts
                const countAll = document.getElementById('count-all-pengumpulan');
                const countSudahMengumpulkan = document.getElementById('count-sudah-mengumpulkan');
                const countBelumMengumpulkan = document.getElementById('count-belum-mengumpulkan');
                const countSudahDinilai = document.getElementById('count-sudah-dinilai');
                const countBelumDinilai = document.getElementById('count-belum-dinilai');

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
                    currentPengumpulanData = data.data.daftar_mahasiswa;
                    currentTugasId = tugasId;

                    // Render student table
                    renderPengumpulanTable(data.data.daftar_mahasiswa);

                    // Setup search functionality
                    setupPengumpulanSearch();
                }

                // Show content
                document.getElementById('pengumpulan-content').classList.remove('hidden');
            } else {
                showPengumpulanError('Gagal memuat data pengumpulan');
            }
        } catch (error) {
            showPengumpulanError('Terjadi kesalahan jaringan');
        }
    }

    /**
     * Render the table of students with their submission status
     */
    function renderPengumpulanTable(daftarMahasiswa, filterType = 'all') {
        const tableBody = document.getElementById('mahasiswa-pengumpulan-table');

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
                    <button onclick="downloadSubmission('${mahasiswa.id_pengumpulan_tugas}')" 
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
                    <button onclick="openQuickGradeModal('${mahasiswa.id_pengumpulan_tugas}', '${mahasiswa.nama}', '${mahasiswa.nim}', ${mahasiswa.nilai || 'null'})" 
                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-white-700 bg-gray-100 rounded hover:bg-gray-200 focus:outline-none"
                        title="${gradeButtonText}">
                        <i class="fas fa-${gradeButtonIcon}"></i>
                    </button>
            `;
            }

            html += `
            <tr class="mahasiswa-pengumpulan-row hover:bg-gray-50 transition-colors duration-200" 
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
     * Filter mahasiswa by submission status
     */
    function filterMahasiswaPengumpulan(filterType) {
        // Update active filter button
        document.querySelectorAll('.filter-pengumpulan-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Find the clicked button and make it active
        const clickedBtn = event.target.closest('.filter-pengumpulan-btn');
        if (clickedBtn) {
            clickedBtn.classList.add('active');
        }

        // Re-render table with filter
        if (currentPengumpulanData) {
            renderPengumpulanTable(currentPengumpulanData, filterType);
        }
    }

    /**
     * Setup search functionality for student table
     */
    function setupPengumpulanSearch() {
        const searchInput = document.getElementById('searchMahasiswaPengumpulan');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.mahasiswa-pengumpulan-row');
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
                const tableBody = document.getElementById('mahasiswa-pengumpulan-table');
                if (tableBody) {
                    const existingNoResults = tableBody.querySelector('.no-results-row');
                    if (existingNoResults) {
                        existingNoResults.remove();
                    }

                    if (visibleCount === 0 && searchTerm.length > 0) {
                        const noResultsRow = document.createElement('tr');
                        noResultsRow.className = 'no-results-row';
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
     * Show error message in status modal
     */
    function showPengumpulanError(message) {
        document.getElementById('pengumpulan-loading').innerHTML = `
        <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
        <p class="text-xs text-red-600">${message}</p>
    `;
    }

    // ============ GRADING FUNCTIONS ============

    /**
     * Open quick grade modal
     */
    function openQuickGradeModal(pengumpulanId, namaMahasiswa, nimMahasiswa, currentNilai) {
        const modal = document.getElementById('quickGradeModal');
        const form = document.getElementById('quickGradeForm');

        // Reset form
        form.reset();

        // Fill student info
        document.getElementById('grade_mahasiswa_nama').textContent = namaMahasiswa;
        document.getElementById('grade_mahasiswa_nim').textContent = nimMahasiswa;
        document.getElementById('grade_pengumpulan_id').value = pengumpulanId;

        // Fill current grade if exists
        if (currentNilai && currentNilai !== 'null') {
            document.getElementById('grade_nilai').value = currentNilai;
        }

        // Show modal
        modal.classList.remove('hidden');

        // Focus on grade input
        setTimeout(() => {
            const gradeInput = document.getElementById('grade_nilai');
            if (gradeInput) {
                gradeInput.focus();
                gradeInput.select();
            }
        }, 100);
    }

    /**
     * Close quick grade modal
     */
    function closeQuickGradeModal() {
        const modal = document.getElementById('quickGradeModal');
        const form = document.getElementById('quickGradeForm');

        // Hide modal
        modal.classList.add('hidden');

        // Reset form
        form.reset();
    }

    /**
     * Submit quick grade
     */
    async function submitQuickGrade(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const pengumpulanId = formData.get('pengumpulan_id');
        const nilai = formData.get('nilai');

        // Validate nilai
        if (!nilai || nilai < 0 || nilai > 100) {
            showError('Nilai harus antara 0 dan 100', 'Validasi Gagal');
            return;
        }

        try {
            const response = await fetch(`/tugas/pengumpulan/${pengumpulanId}/grade`, {
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
                closeQuickGradeModal();

                // Reload data
                if (currentTugasId) {
                    loadStatusPengumpulan(currentTugasId);
                }

                // Show success message using existing toast system
                showSuccess('Nilai berhasil disimpan');
            } else {
                showError('Gagal menyimpan nilai: ' + (data.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            showError('Terjadi kesalahan saat menyimpan nilai');
        }
    }

    // ============ DOWNLOAD FUNCTIONS ============

    /**
     * Download student submission
     */
    async function downloadSubmission(pengumpulanId) {
        try {
            const response = await fetch(`/detail-kelas/pengumpulan-tugas/${pengumpulanId}/download`);

            if (response.ok) {
                const blob = await response.blob();
                const contentDisposition = response.headers.get('Content-Disposition');
                let fileName = 'pengumpulan.pdf';

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
                showSuccess('File berhasil didownload');
            } else {
                showError('Gagal mendownload file pengumpulan');
            }
        } catch (error) {
            showError('Terjadi kesalahan saat mendownload file');
        }
    }

    /**
     * Export pengumpulan data
     */
    function exportPengumpulanTugas() {
        if (currentTugasId) {
            window.open(`/tugas/${currentTugasId}/export`, '_blank');
            showInfo('Export data sedang diproses...');
        } else {
            showError('Data tugas tidak tersedia untuk export');
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
                closeStatusPengumpulanModal();
                closeQuickGradeModal();
            }
        });

        // Click outside modal to close
        const modals = ['statusPengumpulanModal', 'quickGradeModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        if (modalId === 'statusPengumpulanModal') closeStatusPengumpulanModal();
                        else if (modalId === 'quickGradeModal') closeQuickGradeModal();
                    }
                });
            }
        });
    });

    // ============ GLOBAL FUNCTION ASSIGNMENTS ============

    // Make functions globally accessible
    window.openStatusPengumpulanModal = openStatusPengumpulanModal;
    window.closeStatusPengumpulanModal = closeStatusPengumpulanModal;
    window.filterMahasiswaPengumpulan = filterMahasiswaPengumpulan;
    window.exportPengumpulanTugas = exportPengumpulanTugas;
    window.openQuickGradeModal = openQuickGradeModal;
    window.closeQuickGradeModal = closeQuickGradeModal;
    window.submitQuickGrade = submitQuickGrade;
    window.downloadSubmission = downloadSubmission;

    /**
     * ========================================
     * END TUGAS STATUS MODAL FUNCTIONS
     * ========================================
     */
</script>
