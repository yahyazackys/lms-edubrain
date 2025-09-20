@extends('layouts.app')

@section('title', 'Manajemen Mata Kuliah')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="w-full">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Manajemen Mata Kuliah</h1>
                            <p class="text-sm text-gray-600">Kelola mata kuliah dalam sistem akademik</p>
                        </div>

                        <div class="mt-4 sm:mt-0">
                            <button onclick="openModal('create')"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Mata Kuliah
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
                        <!-- Search -->
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" placeholder="Cari kode/nama mata kuliah..."
                                    value="{{ request('search') }}"
                                    class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[280px]">
                            </div>
                        </div>

                        <!-- Filter SKS -->
                        <div class="flex items-center space-x-3">
                            <label class="text-xs font-medium text-gray-700">Filter SKS:</label>
                            <select id="sksFilter" onchange="filterBySks()"
                                class="text-xs px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua SKS</option>
                                @foreach ($sksOptions as $sks)
                                    <option value="{{ $sks }}"
                                        {{ request('sks_filter') == $sks ? 'selected' : '' }}>
                                        {{ $sks }} SKS
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed divide-y divide-gray-200" id="mataKuliahTable">
                        <colgroup>
                            <col class="w-1/5"> <!-- Kode Mata Kuliah -->
                            <col class="w-2/5"> <!-- Nama Mata Kuliah -->
                            <col class="w-1/6"> <!-- SKS -->
                            <col class="w-1/6"> <!-- Aksi -->
                        </colgroup>
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode Mata Kuliah
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Mata Kuliah
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    SKS
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="mataKuliahTableBody">
                            @forelse($mataKuliahs as $mataKuliah)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 mata-kuliah-row" data-searchable
                                    data-sks="{{ $mataKuliah->sks_mata_kuliah }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="searchable-kode">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-medium bg-gray-100 text-gray-800">
                                                {{ $mataKuliah->kode_mata_kuliah }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="searchable-nama">
                                            <div class="text-xs font-medium text-gray-900">
                                                {{ $mataKuliah->nama_mata_kuliah }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $mataKuliah->sks_mata_kuliah }} SKS
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            {{-- Tombol Edit --}}
                                            <div class="relative group">
                                                <button
                                                    onclick="openEditModal(
                                                    '{{ $mataKuliah->id_mata_kuliah }}',
                                                    '{{ addslashes($mataKuliah->kode_mata_kuliah) }}',
                                                    '{{ addslashes($mataKuliah->nama_mata_kuliah) }}',
                                                    '{{ $mataKuliah->sks_mata_kuliah }}'
                                                )"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                    <i class="text-xs fas fa-edit"></i>
                                                </button>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                    Edit Mata Kuliah
                                                    <div
                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Tombol Hapus --}}
                                            <div class="relative group">
                                                <button
                                                    onclick="confirmDelete('{{ $mataKuliah->id_mata_kuliah }}', '{{ addslashes($mataKuliah->nama_mata_kuliah) }}')"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                    Hapus Mata Kuliah
                                                    <div
                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="empty-state-row">
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                </path>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 mb-2">Belum ada mata kuliah</p>
                                            <p class="text-xs text-gray-500">Tambahkan mata kuliah baru untuk sistem
                                                akademik</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($mataKuliahs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $mataKuliahs->appends(request()->query())->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div id="mataKuliahModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
                    <form id="mataKuliahForm" method="POST" action="{{ route('mata-kuliah.store') }}">
                        @csrf

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 id="modalTitle" class="text-lg font-heading font-semibold text-gray-900">
                                    Tambah Mata Kuliah
                                </h3>
                                <button type="button" onclick="closeModal()"
                                    class="sm:hidden text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Kode Mata Kuliah <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="kode_mata_kuliah" id="kode_mata_kuliah"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent font-mono"
                                        placeholder="Contoh: MAT101" maxlength="20" required
                                        style="text-transform: uppercase;">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        SKS <span class="text-red-500">*</span>
                                    </label>
                                    <select name="sks_mata_kuliah" id="sks_mata_kuliah"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        required>
                                        <option value="">Pilih SKS</option>
                                        <option value="0.50">0.5 SKS</option>
                                        <option value="1.00">1 SKS</option>
                                        <option value="1.50">1.5 SKS</option>
                                        <option value="2.00">2 SKS</option>
                                        <option value="2.50">2.5 SKS</option>
                                        <option value="3.00">3 SKS</option>
                                        <option value="3.50">3.5 SKS</option>
                                        <option value="4.00">4 SKS</option>
                                        <option value="4.50">4.5 SKS</option>
                                        <option value="5.00">5 SKS</option>
                                        <option value="6.00">6 SKS</option>
                                        <option value="8.00">8 SKS</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Nama Mata Kuliah <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nama_mata_kuliah" id="nama_mata_kuliah"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Contoh: Matematika Diskrit" maxlength="150" required>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                            <button type="button" onclick="closeModal()"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                Batal
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                <span id="submitText">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms untuk Actions -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            // REALTIME SEARCH & FILTER FUNCTIONALITY
            document.getElementById('searchInput').addEventListener('input', function() {
                filterTable();
            });

            function filterBySks() {
                filterTable();
            }

            function filterTable() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                const sksFilter = document.getElementById('sksFilter').value;
                const tbody = document.getElementById('mataKuliahTableBody');
                const rows = tbody.querySelectorAll('tr[data-searchable]');
                let visibleCount = 0;

                rows.forEach(row => {
                    const kodeCell = row.querySelector('.searchable-kode');
                    const namaCell = row.querySelector('.searchable-nama');
                    const rowSks = row.getAttribute('data-sks');

                    const kodeText = kodeCell ? kodeCell.textContent.toLowerCase() : '';
                    const namaText = namaCell ? namaCell.textContent.toLowerCase() : '';

                    const matchesSearch = searchTerm === '' ||
                        kodeText.includes(searchTerm) ||
                        namaText.includes(searchTerm);
                    const matchesSks = sksFilter === '' || rowSks === sksFilter;

                    const isMatch = matchesSearch && matchesSks;

                    if (isMatch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateEmptyState(visibleCount, searchTerm, sksFilter);
            }

            function updateEmptyState(visibleCount, searchTerm, sksFilter) {
                const tbody = document.getElementById('mataKuliahTableBody');
                let emptyRow = tbody.querySelector('tr[data-empty-search]');

                if (emptyRow) {
                    emptyRow.remove();
                }

                if (visibleCount === 0 && (searchTerm !== '' || sksFilter !== '')) {
                    emptyRow = document.createElement('tr');
                    emptyRow.setAttribute('data-empty-search', 'true');

                    let message = 'Tidak ada hasil ditemukan';
                    let detail = '';

                    if (searchTerm && sksFilter) {
                        detail = `untuk pencarian "${searchTerm}" dengan ${sksFilter} SKS`;
                    } else if (searchTerm) {
                        detail = `untuk pencarian "${searchTerm}"`;
                    } else if (sksFilter) {
                        detail = `dengan ${sksFilter} SKS`;
                    }

                    emptyRow.innerHTML = `
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-900 mb-2">${message}</p>
                                <p class="text-xs text-gray-500">${detail}</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                }
            }

            // ===============================================
            // MODAL FUNCTIONS
            // ===============================================

            // Modal Functions
            function openModal(type) {
                const modal = document.getElementById('mataKuliahModal');
                const form = document.getElementById('mataKuliahForm');
                const modalTitle = document.getElementById('modalTitle');
                const submitText = document.getElementById('submitText');

                form.reset();

                if (type === 'create') {
                    form.action = '{{ route('mata-kuliah.store') }}';
                    form.querySelector('input[name="_method"]')?.remove();
                    modalTitle.textContent = 'Tambah Mata Kuliah';
                    submitText.textContent = 'Simpan';
                }

                modal.classList.remove('hidden');

                setTimeout(() => {
                    document.getElementById('kode_mata_kuliah').focus();
                }, 100);
            }

            function openEditModal(id, kode, nama, sks) {
                const modal = document.getElementById('mataKuliahModal');
                const form = document.getElementById('mataKuliahForm');
                const modalTitle = document.getElementById('modalTitle');
                const submitText = document.getElementById('submitText');

                form.action = `/mata-kuliah/${id}`;

                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';

                // Set values
                document.getElementById('kode_mata_kuliah').value = kode || '';
                document.getElementById('nama_mata_kuliah').value = nama || '';
                document.getElementById('sks_mata_kuliah').value = sks || '';

                modalTitle.textContent = 'Edit Mata Kuliah';
                submitText.textContent = 'Update';

                modal.classList.remove('hidden');

                setTimeout(() => {
                    document.getElementById('kode_mata_kuliah').focus();
                }, 100);
            }

            function closeModal() {
                document.getElementById('mataKuliahModal').classList.add('hidden');
            }

            function confirmDelete(id, name) {
                if (confirm(`Yakin ingin menghapus mata kuliah "${name}"?`)) {
                    const form = document.getElementById('delete-form');
                    form.action = `/mata-kuliah/${id}`;
                    form.submit();
                }
            }

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });

            // Auto uppercase for kode mata kuliah
            document.getElementById('kode_mata_kuliah').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

            // Form validation
            document.getElementById('mataKuliahForm').addEventListener('submit', function(e) {
                const requiredFields = ['kode_mata_kuliah', 'nama_mata_kuliah', 'sks_mata_kuliah'];

                for (let fieldName of requiredFields) {
                    const field = document.getElementById(fieldName);
                    if (!field.value.trim()) {
                        e.preventDefault();
                        alert(
                            `Field ${field.previousElementSibling.textContent.replace(' *', '')} tidak boleh kosong`
                        );
                        field.focus();
                        return false;
                    }
                }

                // Validate kode mata kuliah format
                const kode = document.getElementById('kode_mata_kuliah').value.trim();
                if (!/^[A-Z0-9]+$/.test(kode)) {
                    e.preventDefault();
                    alert('Kode mata kuliah hanya boleh berisi huruf kapital dan angka');
                    document.getElementById('kode_mata_kuliah').focus();
                    return false;
                }
            });

            // Make functions globally accessible
            window.openModal = openModal;
            window.openEditModal = openEditModal;
            window.closeModal = closeModal;
            window.confirmDelete = confirmDelete;
        </script>
    @endpush
@endsection
