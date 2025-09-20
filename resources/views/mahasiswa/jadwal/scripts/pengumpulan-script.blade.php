{{-- File: resources/views/mahasiswa/jadwal/scripts/pengumpulan-script.blade.php --}}

<script>
    /**
     * ========================================
     * PENGUMPULAN SCRIPT FUNCTIONS
     * ========================================
     */

    // Global variables
    let isSubmitting = false;

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        setupPengumpulanForm();
        setupFileValidation();
        setupModalEventListeners();
    });

    /**
     * Setup pengumpulan form submission
     */
    function setupPengumpulanForm() {
        const form = document.getElementById('pengumpulanForm');
        if (!form) return;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (isSubmitting) return;

            const fileInput = document.getElementById('dokumen');
            if (!fileInput.files || fileInput.files.length === 0) {
                showError('Silakan pilih file untuk dikumpulkan.');
                return;
            }

            // Validate file
            if (!validateFile(fileInput.files[0])) {
                return;
            }

            // Show loading state
            setSubmitLoading(true);
            isSubmitting = true;

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Success
                    closePengumpulanModal();
                    showSuccess(result.message || 'File berhasil dikumpulkan!');

                    // Reload page after showing success message
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    // Error from server
                    showError(result.message || 'Terjadi kesalahan saat mengupload file.');
                }

            } catch (error) {
                console.error('Submission error:', error);
                showError('Terjadi kesalahan jaringan. Silakan coba lagi.');
            } finally {
                setSubmitLoading(false);
                isSubmitting = false;
            }
        });
    }

    /**
     * Setup file validation
     */
    function setupFileValidation() {
        const fileInput = document.getElementById('dokumen');
        if (!fileInput) return;

        fileInput.addEventListener('change', function() {
            clearFileError();

            if (this.files && this.files.length > 0) {
                validateFile(this.files[0]);
            }
        });
    }

    /**
     * Validate uploaded file
     * @param {File} file - The file to validate
     * @returns {boolean} - Is file valid
     */
    function validateFile(file) {
        // Check file size (10MB max)
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if (file.size > maxSize) {
            showFileError('Ukuran file tidak boleh lebih dari 10MB.');
            return false;
        }

        // Check file type
        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        if (!allowedTypes.includes(file.type)) {
            showFileError('Format file tidak didukung. Gunakan: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX.');
            return false;
        }

        clearFileError();
        return true;
    }

    /**
     * Show file validation error
     * @param {string} message - Error message
     */
    function showFileError(message) {
        clearFileError();

        const fileInput = document.getElementById('dokumen');
        fileInput.classList.add('border-red-300');

        const errorElement = document.createElement('p');
        errorElement.className = 'text-xs text-red-500 mt-1 file-error';
        errorElement.textContent = message;

        fileInput.parentNode.appendChild(errorElement);
    }

    /**
     * Clear file validation error
     */
    function clearFileError() {
        const fileInput = document.getElementById('dokumen');
        fileInput.classList.remove('border-red-300');
        fileInput.classList.add('border-gray-300');

        const existingError = document.querySelector('.file-error');
        if (existingError) {
            existingError.remove();
        }
    }

    /**
     * Set submit button loading state
     * @param {boolean} loading - Is loading
     */
    function setSubmitLoading(loading) {
        const submitButton = document.getElementById('submit-button');
        const submitText = submitButton.querySelector('.submit-text');
        const loadingSpinner = submitButton.querySelector('.loading-spinner');

        if (loading) {
            submitButton.disabled = true;
            submitText.classList.add('hidden');
            loadingSpinner.classList.remove('hidden');
        } else {
            submitButton.disabled = false;
            submitText.classList.remove('hidden');
            loadingSpinner.classList.add('hidden');
        }
    }

    /**
     * Setup modal event listeners
     */
    function setupModalEventListeners() {
        // Close modal when clicking backdrop
        const modals = ['pengumpulanModal', 'successModal', 'errorModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        if (modalId === 'pengumpulanModal') closePengumpulanModal();
                        else if (modalId === 'successModal') closeSuccessModal();
                        else if (modalId === 'errorModal') closeErrorModal();
                    }
                });
            }
        });

        // Add event listeners for pengumpulan buttons using data attributes
        const pengumpulanButtons = document.querySelectorAll('.pengumpulan-btn');
        pengumpulanButtons.forEach(button => {
            button.addEventListener('click', function() {
                const type = this.dataset.type;
                const id = this.dataset.id;
                const title = this.dataset.title;
                const isUpdate = this.dataset.isUpdate === 'true';

                openPengumpulanModal(type, id, title, isUpdate);
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePengumpulanModal();
                closeSuccessModal();
                closeErrorModal();
            }
        });
    }

    /**
     * Open pengumpulan modal
     * @param {string} type - Type (tugas, uts, uas)
     * @param {number} id - ID of the item
     * @param {string} title - Title of the item
     * @param {boolean} isUpdate - Is this an update
     */
    function openPengumpulanModal(type, id, title, isUpdate) {
        const modal = document.getElementById('pengumpulanModal');
        const form = document.getElementById('pengumpulanForm');
        const modalTitle = document.getElementById('modal-title');
        const submitButton = document.getElementById('submit-button');
        const submitText = submitButton.querySelector('.submit-text');

        // Reset form and states
        form.reset();
        clearFileError();
        setSubmitLoading(false);
        isSubmitting = false;

        // Set form action based on type using the correct URL structure
        let actionUrl = '';
        if (type === 'tugas') {
            actionUrl = `/detail-kelas/submit-tugas/${id}`;
        } else if (type === 'uts') {
            actionUrl = `/detail-kelas/submit-uts/${id}`;
        } else if (type === 'uas') {
            actionUrl = `/detail-kelas/submit-uas/${id}`;
        }

        form.action = actionUrl;

        // Update modal content
        const actionText = isUpdate ? 'Update' : 'Kumpulkan';
        const typeText = type.toUpperCase();

        modalTitle.textContent = `${actionText} ${typeText}: ${title}`;
        submitText.textContent = `${actionText} ${typeText}`;

        // Update submit button color based on action
        submitButton.classList.remove(
            'bg-gray-900', 'hover:bg-gray-700',
            'bg-gray-900', 'hover:bg-gray-700'
        );

        if (isUpdate) {
            submitButton.classList.add('bg-gray-900', 'hover:bg-gray-700');
        } else {
            submitButton.classList.add('bg-gray-600', 'hover:bg-gray-700');
        }

        // Show modal
        modal.classList.remove('hidden');

        // Focus on file input
        setTimeout(() => {
            const fileInput = document.getElementById('dokumen');
            if (fileInput) fileInput.focus();
        }, 100);
    }

    /**
     * Close pengumpulan modal
     */
    function closePengumpulanModal() {
        const modal = document.getElementById('pengumpulanModal');
        const form = document.getElementById('pengumpulanForm');

        modal.classList.add('hidden');
        form.reset();
        clearFileError();
        setSubmitLoading(false);
        isSubmitting = false;
    }

    /**
     * Show success modal
     * @param {string} message - Success message
     */
    function showSuccess(message) {
        const modal = document.getElementById('successModal');
        const messageElement = document.getElementById('success-message');

        messageElement.textContent = message;
        modal.classList.remove('hidden');
    }

    /**
     * Close success modal
     */
    function closeSuccessModal() {
        const modal = document.getElementById('successModal');
        modal.classList.add('hidden');
    }

    /**
     * Show error modal
     * @param {string} message - Error message
     */
    function showError(message) {
        const modal = document.getElementById('errorModal');
        const messageElement = document.getElementById('error-message');

        messageElement.textContent = message;
        modal.classList.remove('hidden');
    }

    /**
     * Close error modal
     */
    function closeErrorModal() {
        const modal = document.getElementById('errorModal');
        modal.classList.add('hidden');
    }

    /**
     * Format file size for display
     * @param {number} bytes - File size in bytes
     * @returns {string} - Formatted file size
     */
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    /**
     * Get file extension from filename
     * @param {string} filename - Filename
     * @returns {string} - File extension
     */
    function getFileExtension(filename) {
        return filename.split('.').pop().toLowerCase();
    }

    /**
     * Get file icon class based on extension
     * @param {string} extension - File extension
     * @returns {string} - Icon class
     */
    function getFileIconClass(extension) {
        const iconMap = {
            'pdf': 'fa-file-pdf text-red-600',
            'doc': 'fa-file-word text-blue-600',
            'docx': 'fa-file-word text-blue-600',
            'ppt': 'fa-file-powerpoint text-orange-600',
            'pptx': 'fa-file-powerpoint text-orange-600',
            'xls': 'fa-file-excel text-green-600',
            'xlsx': 'fa-file-excel text-green-600'
        };

        return iconMap[extension] || 'fa-file text-gray-600';
    }

    // Make functions global for backward compatibility (if needed)
    window.openPengumpulanModal = openPengumpulanModal;
    window.closePengumpulanModal = closePengumpulanModal;
    window.closeSuccessModal = closeSuccessModal;
    window.closeErrorModal = closeErrorModal;

    /**
     * ========================================
     * END PENGUMPULAN SCRIPT FUNCTIONS
     * ========================================
     */
</script>
