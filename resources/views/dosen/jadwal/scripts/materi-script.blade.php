{{-- File: resources/views/partials/scripts/materi-scripts.blade.php --}}

<script>
    /**
     * ========================================
     * MATERI MODAL FUNCTIONS
     * ========================================
     */

    // ============ CREATE MODAL FUNCTIONS ============

    /**
     * Open create materi modal
     */
    function openMateriModal() {
        const modal = document.getElementById('materiModal');
        const form = document.getElementById('materiForm');

        // Reset form
        form.reset();

        // Show modal
        modal.classList.remove('hidden');

        // Focus on first input
        setTimeout(() => {
            const firstInput = form.querySelector('input[name="judul"]');
            if (firstInput) firstInput.focus();
        }, 100);
    }

    /**
     * Close create materi modal
     */
    function closeMateriModal() {
        const modal = document.getElementById('materiModal');
        const form = document.getElementById('materiForm');

        // Hide modal
        modal.classList.add('hidden');

        // Reset form
        form.reset();

        // Clear any validation errors
        clearFormErrors(form);
    }

    // ============ EDIT MODAL FUNCTIONS ============

    /**
     * Open edit materi modal
     * @param {string} materiId - ID of the materi
     * @param {string} judul - Title of the materi
     * @param {string} deskripsi - Description of the materi
     * @param {string} dokumen - Document path of the materi
     */
    function openEditMateriModal(materiId, judul, deskripsi, dokumen) {
        const modal = document.getElementById('editMateriModal');
        const form = document.getElementById('editMateriForm');
        const currentFileContainer = document.getElementById('current_file_container');
        const currentFileName = document.getElementById('current_file_name');
        const currentFileDownload = document.getElementById('current_file_download');

        // Reset form
        form.reset();
        clearFormErrors(form);

        // Set form action
        form.action = `/materi/${materiId}`;

        // Fill form data
        document.getElementById('edit_judul').value = judul || '';
        document.getElementById('edit_deskripsi').value = deskripsi || '';

        // Handle current file display
        if (dokumen && dokumen.trim() !== '') {
            currentFileContainer.classList.remove('hidden');
            const fileName = extractFileName(dokumen);
            currentFileName.textContent = fileName;
            currentFileDownload.href = `/materi/${materiId}/download`;
        } else {
            currentFileContainer.classList.add('hidden');
        }

        // Show modal
        modal.classList.remove('hidden');

        // Focus on first input
        setTimeout(() => {
            const firstInput = document.getElementById('edit_judul');
            if (firstInput) {
                firstInput.focus();
                firstInput.select();
            }
        }, 100);
    }

    /**
     * Close edit materi modal
     */
    function closeEditMateriModal() {
        const modal = document.getElementById('editMateriModal');
        const form = document.getElementById('editMateriForm');
        const currentFileContainer = document.getElementById('current_file_container');

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
     * Open delete materi modal
     * @param {string} materiId - ID of the materi
     * @param {string} judul - Title of the materi
     * @param {string} deskripsi - Description of the materi
     * @param {string} dokumen - Document path of the materi
     */
    function openDeleteMateriModal(materiId, judul, deskripsi, dokumen) {
        const modal = document.getElementById('deleteMateriModal');
        const form = document.getElementById('deleteMateriForm');

        // Set form action
        form.action = `/materi/${materiId}`;

        // Fill materi info for confirmation
        document.getElementById('delete_materi_judul').textContent = judul || 'Tidak ada judul';
        document.getElementById('delete_materi_deskripsi').textContent = deskripsi || 'Tidak ada deskripsi';

        // Handle file info
        const fileElement = document.getElementById('delete_materi_file');
        if (dokumen && dokumen.trim() !== '') {
            const fileName = extractFileName(dokumen);
            fileElement.textContent = `File: ${fileName}`;
        } else {
            fileElement.textContent = 'Tidak ada file';
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
     * Close delete materi modal
     */
    function closeDeleteMateriModal() {
        const modal = document.getElementById('deleteMateriModal');
        modal.classList.add('hidden');
    }

    // ============ UTILITY FUNCTIONS ============

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
     * Validate file input
     * @param {HTMLInputElement} fileInput - File input element
     * @returns {boolean} - Is valid
     */
    function validateFileInput(fileInput) {
        if (!fileInput.files || fileInput.files.length === 0) {
            return true; // No file selected is OK
        }

        const file = fileInput.files[0];
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        const allowedTypes = [
            'application/pdf',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        // Check file size
        if (file.size > maxSize) {
            showFileError(fileInput, 'Ukuran file tidak boleh lebih dari 10MB');
            return false;
        }

        // Check file type
        if (!allowedTypes.includes(file.type)) {
            showFileError(fileInput, 'Format file harus: PDF, PPT, PPTX, DOC, DOCX, XLS, XLSX');
            return false;
        }

        // Clear any previous errors
        clearFileError(fileInput);
        return true;
    }

    /**
     * Show file validation error
     * @param {HTMLInputElement} fileInput - File input element
     * @param {string} message - Error message
     */
    function showFileError(fileInput, message) {
        clearFileError(fileInput);

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
    function clearFileError(fileInput) {
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
        const createFileInput = document.querySelector('#materiModal input[name="dokumen"]');
        if (createFileInput) {
            createFileInput.addEventListener('change', function() {
                validateFileInput(this);
            });
        }

        // File input validation for edit modal
        const editFileInput = document.querySelector('#editMateriModal input[name="dokumen"]');
        if (editFileInput) {
            editFileInput.addEventListener('change', function() {
                validateFileInput(this);
            });
        }

        // Form submission validation for create
        const createForm = document.getElementById('materiForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                const fileInput = this.querySelector('input[name="dokumen"]');
                if (fileInput && !validateFileInput(fileInput)) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Form submission validation for edit
        const editForm = document.getElementById('editMateriForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                const fileInput = this.querySelector('input[name="dokumen"]');
                if (fileInput && !validateFileInput(fileInput)) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                // Close any open materi modals
                closeMateriModal();
                closeEditMateriModal();
                closeDeleteMateriModal();
            }
        });

        // Click outside modal to close
        const modals = ['materiModal', 'editMateriModal', 'deleteMateriModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        // Clicked on backdrop
                        if (modalId === 'materiModal') closeMateriModal();
                        else if (modalId === 'editMateriModal') closeEditMateriModal();
                        else if (modalId === 'deleteMateriModal') closeDeleteMateriModal();
                    }
                });
            }
        });
    });

    // ============ GLOBAL FUNCTION ASSIGNMENTS ============

    // Make functions globally accessible
    window.openMateriModal = openMateriModal;
    window.closeMateriModal = closeMateriModal;
    window.openEditMateriModal = openEditMateriModal;
    window.closeEditMateriModal = closeEditMateriModal;
    window.openDeleteMateriModal = openDeleteMateriModal;
    window.closeDeleteMateriModal = closeDeleteMateriModal;

    /**
     * ========================================
     * END MATERI MODAL FUNCTIONS
     * ========================================
     */
</script>
