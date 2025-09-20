{{-- File: resources/views/partials/scripts/pengaturan-scripts.blade.php --}}

<script>
    /**
     * ========================================
     * PENGATURAN KELAS MODAL FUNCTIONS
     * ========================================
     */

    // Global variables for pengaturan
    let currentBobotData = {
        absensi: {{ $kelasKuliah->bobot_absensi }},
        tugas: {{ $kelasKuliah->bobot_tugas }},
        uts: {{ $kelasKuliah->bobot_uts }},
        uas: {{ $kelasKuliah->bobot_uas }}
    };

    // Set kelasId for JavaScript use
    window.kelasId = '{{ $kelasKuliah->id_kelas_kuliah }}';

    // ============ BOBOT MODAL FUNCTIONS ============

    /**
     * Open bobot penilaian modal
     */
    function openBobotModal() {
        const modal = document.getElementById('bobotModal');
        const form = document.getElementById('bobotForm');

        // Reset form to current values
        resetBobotForm();
        clearBobotFormErrors(form);

        // Show modal
        modal.classList.remove('hidden');

        // Calculate initial total
        calculateBobotTotal();

        // Focus on first input
        setTimeout(() => {
            const firstInput = document.getElementById('bobot_absensi');
            if (firstInput) {
                firstInput.focus();
                firstInput.select();
            }
        }, 100);
    }

    /**
     * Close bobot penilaian modal
     */
    function closeBobotModal() {
        const modal = document.getElementById('bobotModal');
        const form = document.getElementById('bobotForm');

        // Hide modal
        modal.classList.add('hidden');

        // Reset form to original values
        resetBobotForm();

        // Clear any validation errors
        clearBobotFormErrors(form);
    }

    /**
     * Reset bobot form to original values
     */
    function resetBobotForm() {
        document.getElementById('bobot_absensi').value = currentBobotData.absensi;
        document.getElementById('bobot_tugas').value = currentBobotData.tugas;
        document.getElementById('bobot_uts').value = currentBobotData.uts;
        document.getElementById('bobot_uas').value = currentBobotData.uas;
    }

    // ============ GRADE PREVIEW FUNCTIONS ============

    /**
     * Confirm akhiri kelas with grade preview
     */
    function confirmAkhiriKelas() {
        // First, preview the grades to check completeness
        previewGradeCalculation()
            .then(response => {
                if (response.success) {
                    showGradePreviewModal(response);
                } else {
                    showNotificationMessage('Gagal memuat preview perhitungan nilai: ' + response.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotificationMessage('Terjadi kesalahan saat memuat preview nilai.', 'error');
            });
    }

    /**
     * Preview grade calculation before finalizing
     */
    async function previewGradeCalculation() {
        const kelasId = window.kelasId;

        try {
            showLoadingOverlay('Menghitung preview nilai...');

            const response = await fetch(`/kelas/${kelasId}/preview-grades`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                }
            });

            const result = await response.json();
            hideLoadingOverlay();

            return result;
        } catch (error) {
            hideLoadingOverlay();
            throw error;
        }
    }

    /**
     * Show grade preview modal with table
     */
    function showGradePreviewModal(data) {
        const modal = createGradePreviewModal(data);
        document.body.appendChild(modal);
        modal.classList.remove('hidden');
    }

    /**
     * Create grade preview modal with table display
     */
    function createGradePreviewModal(data) {
        const modal = document.createElement('div');
        modal.id = 'gradePreviewModal';
        modal.className = 'fixed inset-0 z-[9999] overflow-y-auto';

        const hasWarnings = !data.validation.is_complete;
        const stats = data.statistics; // Could be null if no students
        const hasStudents = data.grades_preview && data.grades_preview.length > 0;

        modal.innerHTML = `
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeGradePreviewModal()"></div>
            
            <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-6xl mx-auto max-h-[90vh] overflow-y-auto">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900">
                            Preview Perhitungan Nilai Akhir
                        </h3>
                        <button onclick="closeGradePreviewModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    
                    <!-- Class Info -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <h4 class="font-medium text-gray-900 mb-2">${data.kelas.kode} - ${data.kelas.nama}</h4>
                        <p class="text-sm text-gray-600">Kelas: ${data.kelas.nama_kelas}</p>
                    </div>
                    
                    <!-- Warnings Section -->
                    ${hasWarnings ? `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-yellow-800 mb-2">Peringatan Penilaian</h4>
                                    <ul class="text-sm text-yellow-700 space-y-1">
                                        ${data.validation.warnings.map(warning => `<li>• ${warning}</li>`).join('')}
                                    </ul>
                                    <p class="text-sm text-yellow-700 mt-2">
                                        <strong>Catatan:</strong> Pengumpulan yang belum dinilai akan diberi nilai 0, 
                                        dan komponen yang tidak ada akan diberi nilai penuh (100).
                                    </p>
                                </div>
                            </div>
                        </div>
                    ` : ''}
                    
                    ${hasStudents ? `
                        <!-- Statistics (only show if has students) -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-900">${stats.total_mahasiswa}</div>
                                <div class="text-sm text-blue-600">Total Mahasiswa</div>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-900">${stats.rata_rata}</div>
                                <div class="text-sm text-green-600">Rata-rata</div>
                            </div>
                            <div class="text-center p-3 bg-purple-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-900">${stats.tingkat_kelulusan.lulus}</div>
                                <div class="text-sm text-purple-600">Lulus</div>
                            </div>
                            <div class="text-center p-3 bg-red-50 rounded-lg">
                                <div class="text-2xl font-bold text-red-900">${stats.tingkat_kelulusan.tidak_lulus}</div>
                                <div class="text-sm text-red-600">Tidak Lulus</div>
                            </div>
                        </div>
                        
                        <!-- Grade Details Table (only show if has students) -->
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-900 mb-3">Detail Nilai Mahasiswa</h4>
                            <div class="max-h-96 overflow-y-auto border rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mahasiswa</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Absensi</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Tugas</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">UTS</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">UAS</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Nilai Angka</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Nilai Indeks</th>
                                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Nilai Huruf</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        ${data.grades_preview.map((mahasiswa, index) => `
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 py-2 text-xs">${index + 1}</td>
                                                <td class="px-3 py-2">
                                                    <div class="text-xs font-medium text-gray-900">${mahasiswa.mahasiswa}</div>
                                                    <div class="text-xs text-gray-500">${mahasiswa.nim}</div>
                                                </td>
                                                <td class="px-3 py-2 text-xs text-center">${mahasiswa.komponen.absensi}</td>
                                                <td class="px-3 py-2 text-xs text-center">${mahasiswa.komponen.tugas}</td>
                                                <td class="px-3 py-2 text-xs text-center">${mahasiswa.komponen.uts}</td>
                                                <td class="px-3 py-2 text-xs text-center">${mahasiswa.komponen.uas}</td>
                                                <td class="px-3 py-2 text-xs text-center font-medium">${mahasiswa.nilai_akhir}</td>
                                                <td class="px-3 py-2 text-xs text-center font-medium">${mahasiswa.nilai_indeks}</td>
                                                <td class="px-3 py-2 text-center">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getGradeColorClass(mahasiswa.nilai_huruf)}">
                                                        ${mahasiswa.nilai_huruf}
                                                    </span>
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    ` : `
                        <!-- No Students Message -->
                        <div class="mb-6 text-center py-16">
                            <div class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-user-graduate text-gray-600 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mahasiswa</h3>
                            <p class="text-sm text-gray-500 mb-4">Tidak ada mahasiswa yang terdaftar di kelas ini.</p>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-2"></i>
                                    <div class="text-sm text-blue-700 text-left">
                                        <strong>Informasi:</strong> Kelas masih dapat diakhiri meskipun belum ada mahasiswa. 
                                        Tidak akan ada nilai yang dihitung.
                                    </div>
                                </div>
                            </div>
                        </div>
                    `}
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeGradePreviewModal()" 
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="button" onclick="proceedWithFinalization(${hasWarnings})" 
                        class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700">
                        <i class="fas fa-check mr-2"></i>
                        ${hasStudents ? (hasWarnings ? 'Akhiri Tetap' : 'Akhiri Kelas') : 'Akhiri Kelas Kosong'}
                    </button>
                </div>
            </div>
        </div>
    `;

        return modal;
    }

    /**
     * Get color class for grade badge
     */
    function getGradeColorClass(grade) {
        const colorMap = {
            'A': 'bg-green-100 text-green-800',
            'A-': 'bg-green-100 text-green-800',
            'B+': 'bg-gray-100 text-gray-800',
            'B': 'bg-gray-100 text-gray-800',
            'B-': 'bg-gray-100 text-gray-800',
            'C+': 'bg-yellow-100 text-yellow-800',
            'C': 'bg-yellow-100 text-yellow-800',
            'C-': 'bg-orange-100 text-orange-800',
            'D': 'bg-red-100 text-red-800',
            'E': 'bg-red-100 text-red-800'
        };
        return colorMap[grade] || 'bg-gray-100 text-gray-800';
    }

    /**
     * Close grade preview modal
     */
    function closeGradePreviewModal() {
        const modal = document.getElementById('gradePreviewModal');
        if (modal) {
            modal.remove();
        }
    }

    /**
     * Proceed with class finalization
     */
    async function proceedWithFinalization(hasWarnings) {
        const kelasId = window.kelasId;

        try {
            showLoadingOverlay('Mengakhiri kelas dan menghitung nilai...');

            const response = await fetch(`/kelas/${kelasId}/akhiri`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: JSON.stringify({
                    force_calculation: hasWarnings
                })
            });

            const result = await response.json();
            hideLoadingOverlay();

            if (result.success) {
                closeGradePreviewModal();
                showSuccessModal(result);

                // Update status display immediately
                updateStatusDisplay('selesai');

                // Refresh page to show finished state after 3 seconds
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            } else {
                if (result.requires_confirmation) {
                    // This shouldn't happen since we already showed preview
                    showNotificationMessage('Konfirmasi diperlukan.', 'warning');
                } else {
                    showNotificationMessage(result.message, 'error');
                }
            }

        } catch (error) {
            hideLoadingOverlay();
            console.error('Error:', error);
            showNotificationMessage('Terjadi kesalahan saat mengakhiri kelas.', 'error');
        }
    }

    /**
     * Show success modal after class finalization
     */
    function showSuccessModal(result) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-[10000] overflow-y-auto';

        // Handle case where there are no students
        const hasStudents = result.data && result.data.total_mahasiswa > 0;

        modal.innerHTML = `
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            
            <div class="relative bg-white rounded-lg text-center p-6 shadow-xl max-w-md mx-auto">
                <div class="mb-4">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Kelas Berhasil Diakhiri</h3>
                    <p class="text-sm text-gray-500 mb-4">${result.message}</p>
                    
                    ${hasStudents ? `
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600 space-y-1">
                                <div>Total Mahasiswa: <span class="font-medium">${result.data.total_mahasiswa}</span></div>
                                <div>Rata-rata Kelas: <span class="font-medium">${result.data.statistics.rata_rata}</span></div>
                                <div>Tingkat Kelulusan: <span class="font-medium">${result.data.statistics.tingkat_kelulusan.lulus}/${result.data.total_mahasiswa}</span></div>
                            </div>
                        </div>
                    ` : `
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-user-graduate text-gray-400 mr-2"></i>
                                    <span>Kelas diakhiri tanpa mahasiswa</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Tidak ada nilai yang dihitung</p>
                            </div>
                        </div>
                    `}
                </div>
                
                <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                    <i class="fas fa-check mr-2"></i>
                    Tutup
                </button>
            </div>
        </div>
    `;

        document.body.appendChild(modal);
    }

    // ============ CALCULATION FUNCTIONS ============

    /**
     * Calculate total bobot and update display
     */
    function calculateBobotTotal() {
        const absensi = parseInt(document.getElementById('bobot_absensi').value) || 0;
        const tugas = parseInt(document.getElementById('bobot_tugas').value) || 0;
        const uts = parseInt(document.getElementById('bobot_uts').value) || 0;
        const uas = parseInt(document.getElementById('bobot_uas').value) || 0;

        const total = absensi + tugas + uts + uas;

        // Update total display
        const totalElement = document.getElementById('totalBobot');
        const warningElement = document.getElementById('bobotWarning');
        const submitButton = document.getElementById('bobotSubmitBtn');

        if (totalElement) {
            totalElement.textContent = total + '%';
        }

        // Show/hide warning and enable/disable submit button
        if (total !== 100) {
            if (warningElement) {
                warningElement.classList.remove('hidden');
            }
            if (totalElement) {
                totalElement.classList.add('text-red-600');
                totalElement.classList.remove('text-gray-900');
            }
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
        } else {
            if (warningElement) {
                warningElement.classList.add('hidden');
            }
            if (totalElement) {
                totalElement.classList.remove('text-red-600');
                totalElement.classList.add('text-gray-900');
            }
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        return total;
    }

    // ============ VALIDATION FUNCTIONS ============

    /**
     * Validate bobot input values
     * @param {Object} bobotData - Object containing bobot values
     * @returns {Object} - Validation result with isValid and errors
     */
    function validateBobotData(bobotData) {
        const errors = [];

        // Check individual values
        Object.keys(bobotData).forEach(key => {
            const value = bobotData[key];
            if (value < 0 || value > 100 || isNaN(value)) {
                errors.push(`Bobot ${key} harus antara 0-100`);
            }
        });

        // Check total
        const total = Object.values(bobotData).reduce((sum, val) => sum + val, 0);
        if (total !== 100) {
            errors.push(`Total bobot harus 100% (saat ini: ${total}%)`);
        }

        return {
            isValid: errors.length === 0,
            errors: errors,
            total: total
        };
    }

    /**
     * Clear bobot form validation errors
     * @param {HTMLFormElement} form - Form element
     */
    function clearBobotFormErrors(form) {
        // Remove error classes from inputs
        const inputs = form.querySelectorAll('input[type="number"]');
        inputs.forEach(input => {
            input.classList.remove('border-red-300', 'focus:ring-red-500');
            input.classList.add('border-gray-300', 'focus:ring-blue-500');
        });

        // Remove error messages
        const errorMessages = form.querySelectorAll('.error-message');
        errorMessages.forEach(element => element.remove());
    }

    /**
     * Show bobot form validation errors
     * @param {HTMLFormElement} form - Form element
     * @param {Array} errors - Array of error messages
     */
    function showBobotFormErrors(form, errors) {
        clearBobotFormErrors(form);

        if (errors.length > 0) {
            // Show first error as a general message
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message text-sm text-red-600 mt-2';
                errorDiv.innerHTML = `<i class="fas fa-exclamation-circle mr-1"></i>${errors[0]}`;
                submitButton.parentNode.insertBefore(errorDiv, submitButton);
            }
        }
    }

    // ============ UI UPDATE FUNCTIONS ============

    /**
     * Update status display when class is ended
     * @param {string} newStatus - New status ('selesai' or 'aktif')
     */
    function updateStatusDisplay(newStatus) {
        if (newStatus !== 'selesai') return;

        try {
            // Update status text and icon
            const statusContainer = document.querySelector('#content-pengaturan .flex.items-center.gap-2');
            if (statusContainer) {
                statusContainer.innerHTML = `
                    <i class="fas fa-check-circle text-gray-600"></i>
                    <span class="text-gray-900 font-medium">Status: Kelas Selesai</span>
                `;
            }

            // Update description text
            const descriptionText = document.querySelector('#content-pengaturan p.text-sm.text-gray-600');
            if (descriptionText) {
                descriptionText.textContent = 'Kelas telah diakhiri. Semua fitur pengelolaan dinonaktifkan.';
            }

            // Hide "Akhiri Kelas" button
            const akhiriButton = document.querySelector('button[onclick="confirmAkhiriKelas()"]');
            if (akhiriButton) {
                akhiriButton.style.display = 'none';
            }

            // Hide "Edit Bobot" button
            const editBobotButton = document.querySelector('button[onclick="openBobotModal()"]');
            if (editBobotButton) {
                editBobotButton.style.display = 'none';
            }

        } catch (error) {
            console.error('Error updating status display:', error);
            showNotificationMessage('Kelas berhasil diakhiri. Silakan refresh untuk melihat perubahan lengkap.',
                'success');
        }
    }

    /**
     * Update bobot display in UI without page reload
     * @param {Object} bobotData - New bobot values
     */
    function updateBobotDisplay(bobotData) {
        // Update individual bobot cards
        const bobotCards = document.querySelectorAll('#content-pengaturan .grid .text-center');

        bobotCards.forEach(card => {
            const labelElement = card.querySelector('.text-sm.text-gray-600');
            const valueElement = card.querySelector('.text-2xl.font-bold');

            if (labelElement && valueElement) {
                const labelText = labelElement.textContent.trim().toLowerCase();

                switch (labelText) {
                    case 'absensi':
                        valueElement.textContent = `${bobotData.absensi}%`;
                        break;
                    case 'tugas':
                        valueElement.textContent = `${bobotData.tugas}%`;
                        break;
                    case 'uts':
                        valueElement.textContent = `${bobotData.uts}%`;
                        break;
                    case 'uas':
                        valueElement.textContent = `${bobotData.uas}%`;
                        break;
                }

                // Add animation to show change
                valueElement.style.transform = 'scale(1.1)';
                valueElement.style.transition = 'transform 0.3s ease';
                setTimeout(() => {
                    valueElement.style.transform = 'scale(1)';
                }, 300);
            }
        });

        // Update total bobot
        const totalBobotElement = document.querySelector('#content-pengaturan .text-lg.font-bold.text-gray-900');
        if (totalBobotElement) {
            const total = bobotData.absensi + bobotData.tugas + bobotData.uts + bobotData.uas;
            totalBobotElement.textContent = `${total}%`;
        }
    }

    // ============ AJAX FUNCTIONS ============

    /**
     * Submit bobot penilaian update
     * @param {Event} event - Form submit event
     */
    async function submitBobotUpdate(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const bobotData = {
            absensi: parseInt(formData.get('bobot_absensi')),
            tugas: parseInt(formData.get('bobot_tugas')),
            uts: parseInt(formData.get('bobot_uts')),
            uas: parseInt(formData.get('bobot_uas'))
        };

        // Client-side validation
        const validation = validateBobotData(bobotData);
        if (!validation.isValid) {
            showBobotFormErrors(form, validation.errors);
            return;
        }

        // Show loading state
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

        try {
            const response = await fetch(`/kelas/${window.kelasId}/update-bobot`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: JSON.stringify({
                    bobot_absensi: bobotData.absensi,
                    bobot_tugas: bobotData.tugas,
                    bobot_uts: bobotData.uts,
                    bobot_uas: bobotData.uas
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update current data
                currentBobotData = {
                    absensi: bobotData.absensi,
                    tugas: bobotData.tugas,
                    uts: bobotData.uts,
                    uas: bobotData.uas
                };

                // Update UI display immediately
                updateBobotDisplay(bobotData);

                // Close modal
                closeBobotModal();

                // Show success message
                showNotificationMessage('Bobot penilaian berhasil diperbarui', 'success');

            } else {
                // Show server validation errors
                const errors = data.errors ? Object.values(data.errors).flat() : [data.message ||
                    'Terjadi kesalahan'
                ];
                showBobotFormErrors(form, errors);
            }

        } catch (error) {
            showBobotFormErrors(form, ['Terjadi kesalahan jaringan. Silakan coba lagi.']);
        } finally {
            // Reset button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    }

    // ============ UTILITY FUNCTIONS ============

    /**
     * Show loading overlay
     */
    function showLoadingOverlay(message = 'Loading...') {
        const loading = document.createElement('div');
        loading.id = 'loading-overlay';
        loading.className = 'fixed inset-0 z-[10001] bg-gray-900 bg-opacity-50 flex items-center justify-center';

        loading.innerHTML = `
            <div class="bg-white rounded-lg p-6 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto mb-4"></div>
                <p class="text-gray-700">${message}</p>
            </div>
        `;

        document.body.appendChild(loading);
    }

    /**
     * Hide loading overlay
     */
    function hideLoadingOverlay() {
        const loading = document.getElementById('loading-overlay');
        if (loading) {
            loading.remove();
        }
    }

    /**
     * Show notification message
     * @param {string} message - Message to display
     * @param {string} type - Type of notification (success, error, warning, info)
     */
    function showNotificationMessage(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className =
            `fixed top-4 right-4 z-[10000] max-w-sm p-4 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out ${getNotificationClasses(type)}`;

        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas ${getNotificationIcon(type)} mr-3"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                            class="inline-flex text-current opacity-70 hover:opacity-100 focus:outline-none">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>
        `;

        // Add to page
        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    /**
     * Get notification CSS classes based on type
     */
    function getNotificationClasses(type) {
        const classes = {
            'success': 'bg-green-100 text-green-800 border border-green-200',
            'error': 'bg-red-100 text-red-800 border border-red-200',
            'warning': 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            'info': 'bg-blue-100 text-blue-800 border border-blue-200'
        };
        return classes[type] || classes.info;
    }

    /**
     * Get notification icon based on type
     */
    function getNotificationIcon(type) {
        const icons = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        };
        return icons[type] || icons.info;
    }

    // ============ EVENT LISTENERS ============

    document.addEventListener('DOMContentLoaded', function() {

        // Bobot input event listeners
        const bobotInputs = ['bobot_absensi', 'bobot_tugas', 'bobot_uts', 'bobot_uas'];
        bobotInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                // Real-time calculation on input
                input.addEventListener('input', function() {
                    // Ensure value is within range
                    if (this.value < 0) this.value = 0;
                    if (this.value > 100) this.value = 100;

                    calculateBobotTotal();
                });

                // Validation on blur
                input.addEventListener('blur', function() {
                    const value = parseInt(this.value);
                    if (isNaN(value) || value < 0) {
                        this.value = 0;
                    } else if (value > 100) {
                        this.value = 100;
                    }
                    calculateBobotTotal();
                });
            }
        });

        // Form submission for bobot
        const bobotForm = document.getElementById('bobotForm');
        if (bobotForm) {
            bobotForm.addEventListener('submit', submitBobotUpdate);
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeBobotModal();
                closeGradePreviewModal();
            }
        });

        // Click outside modal to close
        const modals = ['bobotModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        if (modalId === 'bobotModal') closeBobotModal();
                    }
                });
            }
        });

        // Initialize total calculation on page load
        setTimeout(() => {
            if (document.getElementById('bobotModal')) {
                calculateBobotTotal();
            }
        }, 100);
    });

    // ============ GLOBAL FUNCTION ASSIGNMENTS ============

    // Make functions globally accessible
    window.openBobotModal = openBobotModal;
    window.closeBobotModal = closeBobotModal;
    window.confirmAkhiriKelas = confirmAkhiriKelas;
    window.calculateBobotTotal = calculateBobotTotal;

    /**
     * ========================================
     * END PENGATURAN KELAS MODAL FUNCTIONS
     * ========================================
     */
</script>
