{{-- File: resources/views/partials/scripts/sesi-absensi-scripts.blade.php --}}

<script>
    /**
     * ========================================
     * SESI ABSENSI MODAL FUNCTIONS
     * ========================================
     */

    // Global variables
    let currentMahasiswaData = null;
    let currentSesiId = null;
    let currentQRCode = null;

    // ============ CREATE MODAL FUNCTIONS ============

    /**
     * Open create sesi absensi modal
     */
    function openSesiAbsensiModal() {
        const modal = document.getElementById('sesiAbsensiModal');
        const form = document.getElementById('sesiAbsensiForm');

        // Reset form
        form.reset();
        clearFormErrors(form);

        // Set default deadline (2 hours from now)
        setDefaultAbsensiDeadline();

        // Show modal
        modal.classList.remove('hidden');

        // Focus on first input
        setTimeout(() => {
            const firstInput = form.querySelector('input[name="topik"]');
            if (firstInput) firstInput.focus();
        }, 100);
    }

    /**
     * Close create sesi absensi modal
     */
    function closeSesiAbsensiModal() {
        const modal = document.getElementById('sesiAbsensiModal');
        const form = document.getElementById('sesiAbsensiForm');

        // Hide modal
        modal.classList.add('hidden');

        // Reset form
        form.reset();
        clearFormErrors(form);
    }

    // ============ EDIT MODAL FUNCTIONS ============

    /**
     * Open edit sesi absensi modal
     */
    function openEditSesiAbsensiModal(sesiId, topik, deadline) {
        const modal = document.getElementById('editSesiAbsensiModal');
        const form = document.getElementById('editSesiAbsensiForm');

        // Reset form
        form.reset();
        clearFormErrors(form);

        // Set form action
        form.action = `/sesi-absensi/${sesiId}`;

        // Fill form data
        document.getElementById('edit_sesi_topik').value = topik || '';

        // Format deadline for datetime-local input
        if (deadline) {
            const formattedDeadline = formatDateTimeForInput(deadline);
            document.getElementById('edit_sesi_deadline').value = formattedDeadline;
        }

        // Show modal
        modal.classList.remove('hidden');

        // Focus on first input
        setTimeout(() => {
            const firstInput = document.getElementById('edit_sesi_topik');
            if (firstInput) {
                firstInput.focus();
                firstInput.select();
            }
        }, 100);
    }

    /**
     * Close edit sesi absensi modal
     */
    function closeEditSesiAbsensiModal() {
        const modal = document.getElementById('editSesiAbsensiModal');
        const form = document.getElementById('editSesiAbsensiForm');

        // Hide modal
        modal.classList.add('hidden');

        // Reset form
        form.reset();
        clearFormErrors(form);
    }

    // ============ DELETE MODAL FUNCTIONS ============

    /**
     * Open delete sesi absensi modal
     */
    function openDeleteSesiAbsensiModal(sesiId, topik, deadline, status) {
        const modal = document.getElementById('deleteSesiAbsensiModal');
        const form = document.getElementById('deleteSesiAbsensiForm');

        // Set form action
        form.action = `/sesi-absensi/${sesiId}`;

        // Fill sesi info for confirmation
        document.getElementById('delete_sesi_topik').textContent = topik || 'Tidak ada topik';

        // Format deadline for display
        if (deadline) {
            const formattedDeadline = formatDateTimeForDisplay(deadline);
            document.getElementById('delete_sesi_deadline').textContent = `Batas akhir: ${formattedDeadline}`;
        } else {
            document.getElementById('delete_sesi_deadline').textContent = 'Tidak ada batas waktu';
        }

        // Status display
        const statusElement = document.getElementById('delete_sesi_status');
        const statusClass = status === 'dibuka' ? 'text-green-600' : 'text-red-600';
        const statusText = status === 'dibuka' ? 'Sesi Dibuka' : 'Sesi Ditutup';
        statusElement.innerHTML = `<span class="${statusClass}">${statusText}</span>`;

        // Show modal
        modal.classList.remove('hidden');

        // Focus on cancel button for safety
        setTimeout(() => {
            const cancelButton = modal.querySelector('button[type="button"]');
            if (cancelButton) cancelButton.focus();
        }, 100);
    }

    /**
     * Close delete sesi absensi modal
     */
    function closeDeleteSesiAbsensiModal() {
        const modal = document.getElementById('deleteSesiAbsensiModal');
        modal.classList.add('hidden');
    }

    // ============ QR CODE MODAL FUNCTIONS ============

    /**
     * Open QR Code modal and generate QR code
     */
    function openQrCodeModal(sesiId) {
        const modal = document.getElementById('qrCodeModal');

        // Reset modal state
        document.getElementById('qr-code-placeholder').classList.remove('hidden');
        document.getElementById('qr-code-display').classList.add('hidden');

        const downloadSection = document.getElementById('qr-download-section');
        if (downloadSection) {
            downloadSection.classList.add('hidden');
        }

        // Clear previous data
        currentQRCode = null;

        // Show modal
        modal.classList.remove('hidden');

        // Generate QR Code
        generateQrCode(sesiId);
    }

    /**
     * Close QR Code modal
     */
    function closeQrCodeModal() {
        const modal = document.getElementById('qrCodeModal');
        modal.classList.add('hidden');
    }

    /**
     * Generate QR Code
     */
    async function generateQrCode(sesiId) {
        try {
            const response = await fetch(`/sesi-absensi/${sesiId}/qr-code`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // Hide placeholder
                document.getElementById('qr-code-placeholder').classList.add('hidden');

                // Show QR Code
                const qrDisplay = document.getElementById('qr-code-display');
                if (data.qrcode && typeof data.qrcode === 'string') {
                    qrDisplay.innerHTML = data.qrcode;
                } else {
                    qrDisplay.innerHTML = `
                        <div class="text-center">
                            <p class="text-red-500">Error: Invalid QR code format</p>
                            <p class="text-xs text-gray-500">Response: ${JSON.stringify(data.qrcode)}</p>
                        </div>
                    `;
                }
                qrDisplay.classList.remove('hidden');

                // Store QR data globally for download
                currentQRCode = data.qrcode;

                // Show download section if exists
                const downloadSection = document.getElementById('qr-download-section');
                if (downloadSection) {
                    downloadSection.classList.remove('hidden');
                }

            } else {
                // Show error
                document.getElementById('qr-code-placeholder').innerHTML = `
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                    <p class="text-xs text-red-600">Gagal membuat QR Code</p>
                    <p class="text-xs text-red-500">${data.message || 'Unknown error'}</p>
                `;
            }
        } catch (error) {
            document.getElementById('qr-code-placeholder').innerHTML = `
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                <p class="text-xs text-red-600">Terjadi kesalahan jaringan</p>
                <p class="text-xs text-red-500">${error.message}</p>
            `;
        }
    }

    /**
     * Download QR Code - detects format automatically
     */
    function downloadQRCode() {
        if (!currentQRCode) {
            alert('QR Code belum tersedia');
            return;
        }

        const qrContent = currentQRCode;

        // Check QR format and download accordingly
        if (qrContent.includes('<svg')) {
            // Format: SVG string
            downloadSVGQR(qrContent);
        } else if (qrContent.includes('data:image/png;base64,')) {
            // Format: Base64 PNG data URL
            downloadBase64QR(qrContent);
        } else if (qrContent.includes('<img src="http')) {
            // Format: External image URL
            downloadExternalQR(qrContent);
        } else {
            alert('Format QR Code tidak didukung untuk download');
        }
    }

    /**
     * Download SVG QR code
     */
    function downloadSVGQR(svgContent) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = svgContent;
        const svgElement = tempDiv.querySelector('svg');

        if (!svgElement) {
            alert('SVG tidak valid');
            return;
        }

        // Create canvas
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 400;
        canvas.height = 400;

        // White background
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, 400, 400);

        // Convert SVG to image
        const svgData = new XMLSerializer().serializeToString(svgElement);
        const svgBlob = new Blob([svgData], {
            type: 'image/svg+xml'
        });
        const svgUrl = URL.createObjectURL(svgBlob);

        const img = new Image();
        img.onload = function() {
            ctx.drawImage(img, 0, 0, 400, 400);
            canvas.toBlob(function(blob) {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `qr-code-absensi-${getTimestamp()}.png`;
                a.click();
                URL.revokeObjectURL(url);
                URL.revokeObjectURL(svgUrl);
            }, 'image/png');
        };
        img.src = svgUrl;
    }

    /**
     * Download Base64 PNG QR code
     */
    function downloadBase64QR(htmlContent) {
        const match = htmlContent.match(/data:image\/png;base64,([^"]+)/);
        if (!match) {
            alert('Base64 data tidak ditemukan');
            return;
        }

        // Convert base64 to blob
        const base64Data = match[1];
        const byteCharacters = atob(base64Data);
        const byteNumbers = new Array(byteCharacters.length);

        for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
        }

        const byteArray = new Uint8Array(byteNumbers);
        const blob = new Blob([byteArray], {
            type: 'image/png'
        });

        // Download
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `qr-code-absensi-${getTimestamp()}.png`;
        a.click();
        URL.revokeObjectURL(url);
    }

    /**
     * Download External image QR code
     */
    async function downloadExternalQR(htmlContent) {
        const match = htmlContent.match(/src="([^"]+)"/);
        if (!match) {
            alert('URL gambar tidak ditemukan');
            return;
        }

        const imageUrl = match[1];

        try {
            const response = await fetch(imageUrl);
            const blob = await response.blob();

            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `qr-code-absensi-${getTimestamp()}.png`;
            a.click();
            URL.revokeObjectURL(url);
        } catch (error) {
            alert('Gagal mendownload QR Code dari server external');
        }
    }

    // ============ STATUS MODAL FUNCTIONS ============

    /**
     * Open status absensi modal
     */
    function openStatusAbsensiModal(sesiId) {
        const modal = document.getElementById('statusAbsensiModal');

        // Reset modal state
        document.getElementById('status-loading').classList.remove('hidden');
        document.getElementById('status-content').classList.add('hidden');

        // Show modal
        modal.classList.remove('hidden');

        // Load status data
        loadStatusAbsensi(sesiId);
    }

    /**
     * Close status absensi modal
     */
    function closeStatusAbsensiModal() {
        const modal = document.getElementById('statusAbsensiModal');
        modal.classList.add('hidden');
    }

    /**
     * Load status absensi data
     */
    async function loadStatusAbsensi(sesiId) {
        try {
            const response = await fetch(`/sesi-absensi/${sesiId}/status`);
            const data = await response.json();

            if (data.success) {
                // Hide loading
                document.getElementById('status-loading').classList.add('hidden');

                // Fill basic stats
                const statusDeadline = document.getElementById('status_deadline');
                const statusTotal = document.getElementById('status_total');
                const statusTopik = document.getElementById('status_topik');

                if (statusDeadline) statusDeadline.textContent = data.data.batas_akhir;
                if (statusTotal) statusTotal.textContent = data.data.total_mahasiswa;
                if (statusTopik) statusTopik.textContent = data.data.topik || 'Tidak ada topik';

                // Status badge
                const statusBadge = document.getElementById('status_sesi_badge');
                if (statusBadge) {
                    if (data.data.status === 'dibuka' && !data.data.is_expired) {
                        statusBadge.className =
                            'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
                        statusBadge.textContent = 'Aktif';
                    } else {
                        statusBadge.className =
                            'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
                        statusBadge.textContent = data.data.is_expired ? 'Expired' : 'Ditutup';
                    }
                }

                // Update filter counts if exists
                const countAll = document.getElementById('count-all');
                const countHadir = document.getElementById('count-hadir');
                const countTidakHadir = document.getElementById('count-tidak-hadir');

                if (data.data.daftar_mahasiswa) {
                    const hadirCount = data.data.daftar_mahasiswa.filter(m => m.sudah_absen).length;
                    const tidakHadirCount = data.data.daftar_mahasiswa.filter(m => !m.sudah_absen).length;

                    if (countAll) countAll.textContent = data.data.total_mahasiswa;
                    if (countHadir) countHadir.textContent = hadirCount;
                    if (countTidakHadir) countTidakHadir.textContent = tidakHadirCount;

                    // Store data globally for filtering
                    currentMahasiswaData = data.data.daftar_mahasiswa;
                    currentSesiId = sesiId;

                    // Render student table if function exists
                    if (typeof renderMahasiswaTable === 'function') {
                        renderMahasiswaTable(data.data.daftar_mahasiswa);
                    }

                    // Setup search functionality
                    setupStatusSearch();
                }

                // Show content
                document.getElementById('status-content').classList.remove('hidden');
            } else {
                showStatusError('Gagal memuat data kehadiran');
            }
        } catch (error) {
            console.error('Error loading status:', error);
            showStatusError('Terjadi kesalahan jaringan');
        }
    }

    /**
     * Render the table of students with their attendance status
     */
    function renderMahasiswaTable(daftarMahasiswa, filterType = 'all') {
        const tableBody = document.getElementById('mahasiswa-status-table');

        if (!tableBody) {
            console.warn('mahasiswa-status-table element not found');
            return;
        }

        if (!daftarMahasiswa || daftarMahasiswa.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500 text-sm">
                        <i class="fas fa-users text-2xl mb-2"></i>
                        <p>Tidak ada data mahasiswa</p>
                    </td>
                </tr>
            `;
            return;
        }

        // Filter data based on filter type
        let filteredData = daftarMahasiswa;
        if (filterType === 'hadir') {
            filteredData = daftarMahasiswa.filter(m => m.sudah_absen);
        } else if (filterType === 'tidak_hadir') {
            filteredData = daftarMahasiswa.filter(m => !m.sudah_absen);
        }

        // Sort by name
        filteredData.sort((a, b) => a.nama.localeCompare(b.nama));

        let html = '';
        filteredData.forEach((mahasiswa, index) => {
            const statusClass = mahasiswa.sudah_absen ? 'bg-green-100 text-green-800' :
                'bg-red-100 text-red-800';
            const statusText = mahasiswa.sudah_absen ? 'Hadir' : 'Tidak Hadir';
            const statusIcon = mahasiswa.sudah_absen ? 'check' : 'times';
            const waktuAbsen = mahasiswa.waktu_absen || '-';
            const angkatan = mahasiswa.angkatan || 'N/A';

            html += `
                <tr class="mahasiswa-row hover:bg-gray-50 transition-colors duration-200" 
                    data-nama="${mahasiswa.nama.toLowerCase()}" 
                    data-nim="${mahasiswa.nim}" 
                    data-angkatan="${angkatan.toLowerCase()}"
                    data-status="${mahasiswa.sudah_absen ? 'hadir' : 'tidak_hadir'}">
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                        ${index + 1}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-gray-900">
                        ${mahasiswa.nim}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-900">
                        ${mahasiswa.nama}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-700 text-center">
                        ${angkatan}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${statusClass}">
                            <i class="fas fa-${statusIcon} mr-1"></i>
                            ${statusText}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-700 text-center">
                        ${waktuAbsen}
                    </td>
                </tr>
            `;
        });

        if (filteredData.length === 0) {
            html = `
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500 text-sm">
                        <i class="fas fa-search text-2xl mb-2"></i>
                        <p>Tidak ada data yang sesuai dengan filter</p>
                    </td>
                </tr>
            `;
        }

        tableBody.innerHTML = html;
    }

    /**
     * Filter mahasiswa by status
     */
    function filterMahasiswaStatus(filterType) {
        // Update active filter button
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Find the clicked button and make it active
        const clickedBtn = event.target.closest('.filter-btn');
        if (clickedBtn) {
            clickedBtn.classList.add('active');
        }

        // Re-render table with filter
        if (currentMahasiswaData) {
            renderMahasiswaTable(currentMahasiswaData, filterType);
        }
    }

    /**
     * Setup search functionality for student table
     */
    function setupStatusSearch() {
        const searchInput = document.getElementById('searchMahasiswaStatus');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.mahasiswa-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const nama = row.getAttribute('data-nama') || '';
                    const nim = row.getAttribute('data-nim') || '';
                    const angkatan = row.getAttribute('data-angkatan') || '';

                    const matches = nama.includes(searchTerm) ||
                        nim.toLowerCase().includes(searchTerm) ||
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
                const tableBody = document.getElementById('mahasiswa-status-table');
                if (tableBody) {
                    const existingNoResults = tableBody.querySelector('.no-results-row');
                    if (existingNoResults) {
                        existingNoResults.remove();
                    }

                    if (visibleCount === 0 && searchTerm.length > 0) {
                        const noResultsRow = document.createElement('tr');
                        noResultsRow.className = 'no-results-row';
                        noResultsRow.innerHTML = `
                            <td colspan="6" class="text-center py-8 text-gray-500 text-sm">
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
    function showStatusError(message) {
        document.getElementById('status-loading').innerHTML = `
            <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
            <p class="text-xs text-red-600">${message}</p>
        `;
    }

    /**
     * Export attendance data
     */
    function exportStatusAbsensi() {
        if (currentSesiId) {
            window.open(`/sesi-absensi/${currentSesiId}/export`, '_blank');
        } else {
            alert('Data sesi tidak tersedia untuk export');
        }
    }

    // ============ TOGGLE STATUS FUNCTIONS ============

    /**
     * Toggle sesi absensi status (buka/tutup)
     */
    async function toggleSesiStatus(sesiId, currentStatus) {
        const action = currentStatus === 'dibuka' ? 'menutup' : 'membuka';

        if (!confirm(`Yakin ingin ${action} sesi absensi ini?`)) {
            return;
        }

        try {
            const response = await fetch(`/sesi-absensi/${sesiId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                }
            });

            if (response.ok) {
                location.reload();
            } else {
                alert('Terjadi kesalahan saat mengubah status sesi');
            }
        } catch (error) {
            alert('Terjadi kesalahan saat mengubah status sesi');
        }
    }

    // ============ UTILITY FUNCTIONS ============

    /**
     * Get timestamp for filename
     */
    function getTimestamp() {
        const now = new Date();
        return now.getFullYear() +
            String(now.getMonth() + 1).padStart(2, '0') +
            String(now.getDate()).padStart(2, '0') +
            '-' +
            String(now.getHours()).padStart(2, '0') +
            String(now.getMinutes()).padStart(2, '0');
    }

    /**
     * Set default deadline for absensi (2 hours from now)
     */
    function setDefaultAbsensiDeadline() {
        const defaultDeadlineInput = document.querySelector('#sesiAbsensiModal input[name="batas_akhir_absensi"]');
        if (defaultDeadlineInput) {
            const twoHours = new Date();
            twoHours.setHours(twoHours.getHours() + 2);

            const formattedDate = formatDateTimeForInput(twoHours.toISOString());
            defaultDeadlineInput.value = formattedDate;
        }
    }

    /**
     * Format datetime for input[type="datetime-local"]
     */
    function formatDateTimeForInput(isoString) {
        if (!isoString) return '';

        const date = new Date(isoString);
        if (isNaN(date.getTime())) return '';

        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');

        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    /**
     * Format datetime for display
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
     * Clear form validation errors
     */
    function clearFormErrors(form) {
        const errorElements = form.querySelectorAll('.border-red-300, .text-red-600');
        errorElements.forEach(element => {
            element.classList.remove('border-red-300', 'text-red-600');
            element.classList.add('border-gray-300');
        });

        const errorMessages = form.querySelectorAll('.text-red-500:not(.required-asterisk)');
        errorMessages.forEach(element => {
            if (!element.textContent.includes('*')) {
                element.remove();
            }
        });
    }

    /**
     * Validate datetime input (must be in the future)
     */
    function validateAbsensiDateTimeInput(datetimeInput) {
        const value = datetimeInput.value;
        if (!value) {
            showAbsensiDateTimeError(datetimeInput, 'Batas akhir absensi harus diisi');
            return false;
        }

        const selectedDate = new Date(value);
        const now = new Date();

        clearAbsensiDateTimeError(datetimeInput);
        return true;
    }

    /**
     * Show datetime validation error
     */
    function showAbsensiDateTimeError(datetimeInput, message) {
        clearAbsensiDateTimeError(datetimeInput);

        datetimeInput.classList.add('border-red-300');

        const errorElement = document.createElement('p');
        errorElement.className = 'text-xs text-red-500 mt-1';
        errorElement.textContent = message;

        datetimeInput.parentNode.appendChild(errorElement);
    }

    /**
     * Clear datetime validation error
     */
    function clearAbsensiDateTimeError(datetimeInput) {
        datetimeInput.classList.remove('border-red-300');
        datetimeInput.classList.add('border-gray-300');

        const existingError = datetimeInput.parentNode.querySelector('.text-red-500:not(.required-asterisk)');
        if (existingError && !existingError.textContent.includes('*')) {
            existingError.remove();
        }
    }

    // ============ EVENT LISTENERS ============

    document.addEventListener('DOMContentLoaded', function() {
        // DateTime validation for create modal
        const createDateTimeInput = document.querySelector(
            '#sesiAbsensiModal input[name="batas_akhir_absensi"]');
        if (createDateTimeInput) {
            createDateTimeInput.addEventListener('change', function() {
                validateAbsensiDateTimeInput(this);
            });
            createDateTimeInput.addEventListener('blur', function() {
                validateAbsensiDateTimeInput(this);
            });
        }

        // DateTime validation for edit modal
        const editDateTimeInput = document.querySelector(
            '#editSesiAbsensiModal input[name="batas_akhir_absensi"]');
        if (editDateTimeInput) {
            editDateTimeInput.addEventListener('change', function() {
                validateAbsensiDateTimeInput(this);
            });
            editDateTimeInput.addEventListener('blur', function() {
                validateAbsensiDateTimeInput(this);
            });
        }

        // Form submission validation for create
        const createForm = document.getElementById('sesiAbsensiForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                let isValid = true;

                const datetimeInput = this.querySelector('input[name="batas_akhir_absensi"]');
                if (datetimeInput && !validateAbsensiDateTimeInput(datetimeInput)) {
                    isValid = false;
                }

                const topikInput = this.querySelector('input[name="topik"]');
                if (topikInput && topikInput.value.trim().length === 0) {
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Form submission validation for edit
        const editForm = document.getElementById('editSesiAbsensiForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                let isValid = true;

                const datetimeInput = this.querySelector('input[name="batas_akhir_absensi"]');
                if (datetimeInput && !validateAbsensiDateTimeInput(datetimeInput)) {
                    isValid = false;
                }

                const topikInput = this.querySelector('input[name="topik"]');
                if (topikInput && topikInput.value.trim().length === 0) {
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
                closeSesiAbsensiModal();
                closeEditSesiAbsensiModal();
                closeDeleteSesiAbsensiModal();
                closeQrCodeModal();
                closeStatusAbsensiModal();
            }
        });

        // Click outside modal to close
        const modals = ['sesiAbsensiModal', 'editSesiAbsensiModal', 'deleteSesiAbsensiModal', 'qrCodeModal',
            'statusAbsensiModal'
        ];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        if (modalId === 'sesiAbsensiModal') closeSesiAbsensiModal();
                        else if (modalId === 'editSesiAbsensiModal')
                            closeEditSesiAbsensiModal();
                        else if (modalId === 'deleteSesiAbsensiModal')
                            closeDeleteSesiAbsensiModal();
                        else if (modalId === 'qrCodeModal') closeQrCodeModal();
                        else if (modalId === 'statusAbsensiModal') closeStatusAbsensiModal();
                    }
                });
            }
        });
    });

    // ============ GLOBAL FUNCTION ASSIGNMENTS ============

    // Make functions globally accessible
    window.openSesiAbsensiModal = openSesiAbsensiModal;
    window.closeSesiAbsensiModal = closeSesiAbsensiModal;
    window.openEditSesiAbsensiModal = openEditSesiAbsensiModal;
    window.closeEditSesiAbsensiModal = closeEditSesiAbsensiModal;
    window.openDeleteSesiAbsensiModal = openDeleteSesiAbsensiModal;
    window.closeDeleteSesiAbsensiModal = closeDeleteSesiAbsensiModal;
    window.openQrCodeModal = openQrCodeModal;
    window.closeQrCodeModal = closeQrCodeModal;
    window.downloadQRCode = downloadQRCode;
    window.openStatusAbsensiModal = openStatusAbsensiModal;
    window.closeStatusAbsensiModal = closeStatusAbsensiModal;
    window.toggleSesiStatus = toggleSesiStatus;
    window.filterMahasiswaStatus = filterMahasiswaStatus;
    window.exportStatusAbsensi = exportStatusAbsensi;

    /**
     * ========================================
     * END SESI ABSENSI MODAL FUNCTIONS
     * ========================================
     */
</script>
