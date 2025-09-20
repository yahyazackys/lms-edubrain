@extends('layouts.app')

@section('title', 'Manajemen Program Studi')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Manajemen Program Studi</h1>
                            <p class="text-sm text-gray-600">Kelola program studi berdasarkan jenjang pendidikan</p>
                        </div>

                        @if ($selectedJenjang)
                            <div class="mt-4 sm:mt-0">
                                <button onclick="openModal('create')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Program Studi
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Filter Jenjang & Search -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
                        <!-- Filter Jenjang -->
                        <div class="flex items-center space-x-3">
                            <label class="text-xs font-medium text-gray-700">Jenjang Pendidikan:</label>
                            <select id="jenjangFilter" onchange="changeJenjang()"
                                class="text-xs px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[200px]">
                                <option value="">Pilih Jenjang Pendidikan</option>
                                @foreach ($jenjangs as $jenjang)
                                    <option value="{{ $jenjang->id_jenjang_pendidikan }}"
                                        {{ $selectedJenjangId == $jenjang->id_jenjang_pendidikan ? 'selected' : '' }}>
                                        {{ $jenjang->kode_jenjang_pendidikan }} - {{ $jenjang->nama_jenjang_pendidikan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search & Filter Status -->
                        @if ($selectedJenjang)
                            <div class="flex items-center space-x-3">
                                <!-- Search -->
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" id="searchInput" placeholder="Cari kode/nama program studi..."
                                        class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                </div>

                                <!-- Filter Status -->
                                <select id="statusFilter" onchange="filterByStatus()"
                                    class="text-xs py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Semua Status</option>
                                    <option value="A" {{ request('status') == 'A' ? 'selected' : '' }}>Aktif</option>
                                    <option value="N" {{ request('status') == 'N' ? 'selected' : '' }}>Non-aktif
                                    </option>
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if (!$selectedJenjang)
                <!-- Empty State - Pilih Jenjang -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-search w-16 h-16 text-gray-400 mb-4 mx-auto text-6xl"></i>
                        <p class="text-lg font-medium text-gray-900 mb-2">Pilih Jenjang Pendidikan</p>
                        <p class="text-sm text-gray-500 mb-4">Pilih jenjang pendidikan dari dropdown di atas untuk mengelola
                            program studi</p>

                        @if ($jenjangs->count() == 0)
                            <a href="{{ route('jenjang-pendidikan.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Kelola Jenjang Pendidikan
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <!-- Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="programStudiTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Kode Program Studi
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Nama Program Studi
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Jenjang Pendidikan
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Status
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="programStudiTableBody">
                                @forelse($programStudis as $programStudi)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 program-studi-row"
                                        data-searchable data-status="{{ $programStudi->status }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="searchable-kode">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-medium bg-gray-100 text-gray-800">
                                                    {{ $programStudi->kode_program_studi }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="searchable-nama">
                                                <div class="text-xs font-medium text-gray-900">
                                                    {{ $programStudi->nama_program_studi }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs text-gray-900">
                                                {{ $programStudi->jenjang->nama_jenjang_pendidikan }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $programStudi->jenjang->kode_jenjang_pendidikan }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($programStudi->status == 'A')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-1.5 h-1.5 mr-1.5 fill-current" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3"></circle>
                                                    </svg>
                                                    Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="w-1.5 h-1.5 mr-1.5 fill-current" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3"></circle>
                                                    </svg>
                                                    Non-aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                {{-- Tombol Edit --}}
                                                <div class="relative group">
                                                    <button
                                                        onclick="openEditModal(
                                                        '{{ $programStudi->id_program_studi }}',
                                                        '{{ addslashes($programStudi->kode_program_studi) }}',
                                                        '{{ addslashes($programStudi->nama_program_studi) }}',
                                                        '{{ $programStudi->status }}',
                                                        '{{ $programStudi->id_jenjang_pendidikan }}'
                                                    )"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="text-xs fas fa-edit"></i>
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Edit Program Studi
                                                        <div
                                                            class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Tombol Hapus --}}
                                                <div class="relative group">
                                                    <button
                                                        onclick="confirmDelete('{{ $programStudi->id_program_studi }}', '{{ addslashes($programStudi->nama_program_studi) }}')"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Hapus Program Studi
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
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                                </svg>
                                                <p class="text-lg font-medium text-gray-900 mb-2">Belum ada program studi
                                                </p>
                                                <p class="text-xs text-gray-500">Tambahkan program studi baru untuk jenjang
                                                    {{ $selectedJenjang->nama_jenjang_pendidikan }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($programStudis->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $programStudis->appends(request()->query())->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Create/Edit Modal -->
        @if ($selectedJenjang)
            <div id="programStudiModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

                    <div
                        class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
                        <form id="programStudiForm" method="POST" action="{{ route('program-studi.store') }}">
                            @csrf
                            <input type="hidden" name="id_jenjang_pendidikan"
                                value="{{ $selectedJenjang->id_jenjang_pendidikan }}">

                            <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 id="modalTitle" class="text-lg font-heading font-semibold text-gray-900">
                                        Tambah Program Studi
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
                                            Kode Program Studi <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="kode_program_studi" id="kode_program_studi"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent font-mono"
                                            placeholder="Contoh: IF" maxlength="20" required
                                            style="text-transform: uppercase;">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <select name="status" id="status"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            required>
                                            <option value="">Pilih Status</option>
                                            <option value="A">Aktif</option>
                                            <option value="N">Non-aktif</option>
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Nama Program Studi <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="nama_program_studi" id="nama_program_studi"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="Contoh: Teknik Informatika" maxlength="100" required>
                                    </div>
                                </div>

                                <!-- Info Jenjang -->
                                <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-xs text-gray-600">
                                            Program studi ini akan ditambahkan ke jenjang:
                                            <strong>{{ $selectedJenjang->nama_jenjang_pendidikan }}</strong>
                                        </span>
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
        @endif
    </div>

    <!-- Hidden Forms untuk Actions -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            // Change jenjang function
            function changeJenjang() {
                const jenjangId = document.getElementById('jenjangFilter').value;
                if (jenjangId) {
                    // Preserve other filters
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.set('jenjang', jenjangId);
                    currentParams.delete('page'); // Reset pagination
                    window.location.href = window.location.pathname + '?' + currentParams.toString();
                } else {
                    // Clear jenjang filter
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.delete('jenjang');
                    currentParams.delete('page');
                    window.location.href = window.location.pathname + (currentParams.toString() ? '?' + currentParams
                        .toString() : '');
                }
            }

            @if ($selectedJenjang)
                // REALTIME SEARCH & FILTER FUNCTIONALITY
                document.getElementById('searchInput').addEventListener('input', function() {
                    filterTable();
                });

                function filterByStatus() {
                    filterTable();
                }

                function filterTable() {
                    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                    const statusFilter = document.getElementById('statusFilter').value;
                    const tbody = document.getElementById('programStudiTableBody');
                    const rows = tbody.querySelectorAll('tr[data-searchable]');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const kodeCell = row.querySelector('.searchable-kode');
                        const namaCell = row.querySelector('.searchable-nama');
                        const rowStatus = row.getAttribute('data-status');

                        const kodeText = kodeCell ? kodeCell.textContent.toLowerCase() : '';
                        const namaText = namaCell ? namaCell.textContent.toLowerCase() : '';

                        const matchesSearch = searchTerm === '' ||
                            kodeText.includes(searchTerm) ||
                            namaText.includes(searchTerm);
                        const matchesStatus = statusFilter === '' || rowStatus === statusFilter;

                        const isMatch = matchesSearch && matchesStatus;

                        if (isMatch) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    updateEmptyState(visibleCount, searchTerm, statusFilter);
                }

                function updateEmptyState(visibleCount, searchTerm, statusFilter) {
                    const tbody = document.getElementById('programStudiTableBody');
                    let emptyRow = tbody.querySelector('tr[data-empty-search]');

                    if (emptyRow) {
                        emptyRow.remove();
                    }

                    if (visibleCount === 0 && (searchTerm !== '' || statusFilter !== '')) {
                        emptyRow = document.createElement('tr');
                        emptyRow.setAttribute('data-empty-search', 'true');

                        let message = 'Tidak ada hasil ditemukan';
                        let detail = '';

                        if (searchTerm && statusFilter) {
                            const statusName = statusFilter === 'A' ? 'Aktif' : 'Non-aktif';
                            detail = `untuk pencarian "${searchTerm}" dengan status ${statusName}`;
                        } else if (searchTerm) {
                            detail = `untuk pencarian "${searchTerm}"`;
                        } else if (statusFilter) {
                            const statusName = statusFilter === 'A' ? 'Aktif' : 'Non-aktif';
                            detail = `dengan status ${statusName}`;
                        }

                        emptyRow.innerHTML = `
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
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
                    const modal = document.getElementById('programStudiModal');
                    const form = document.getElementById('programStudiForm');
                    const modalTitle = document.getElementById('modalTitle');
                    const submitText = document.getElementById('submitText');

                    form.reset();

                    if (type === 'create') {
                        form.action = '{{ route('program-studi.store') }}';
                        form.querySelector('input[name="_method"]')?.remove();
                        modalTitle.textContent = 'Tambah Program Studi';
                        submitText.textContent = 'Simpan';
                    }

                    modal.classList.remove('hidden');

                    setTimeout(() => {
                        document.getElementById('kode_program_studi').focus();
                    }, 100);
                }

                function openEditModal(id, kode, nama, status, jenjangId) {
                    const modal = document.getElementById('programStudiModal');
                    const form = document.getElementById('programStudiForm');
                    const modalTitle = document.getElementById('modalTitle');
                    const submitText = document.getElementById('submitText');

                    form.action = `/program-studi/${id}`;

                    let methodInput = form.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        form.appendChild(methodInput);
                    }
                    methodInput.value = 'PUT';

                    // Set values
                    document.getElementById('kode_program_studi').value = kode || '';
                    document.getElementById('nama_program_studi').value = nama || '';
                    document.getElementById('status').value = status || '';

                    modalTitle.textContent = 'Edit Program Studi';
                    submitText.textContent = 'Update';

                    modal.classList.remove('hidden');

                    setTimeout(() => {
                        document.getElementById('kode_program_studi').focus();
                    }, 100);
                }

                function closeModal() {
                    document.getElementById('programStudiModal').classList.add('hidden');
                }

                function confirmDelete(id, name) {
                    if (confirm(`Yakin ingin menghapus program studi "${name}"?`)) {
                        const form = document.getElementById('delete-form');
                        form.action = `/program-studi/${id}`;
                        form.submit();
                    }
                }

                // Close modal with Escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeModal();
                    }
                });

                // Auto uppercase for kode program studi
                document.getElementById('kode_program_studi').addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });

                // Form validation
                document.getElementById('programStudiForm').addEventListener('submit', function(e) {
                    const requiredFields = ['kode_program_studi', 'nama_program_studi', 'status'];

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

                    // Validate kode program studi format
                    const kode = document.getElementById('kode_program_studi').value.trim();
                    if (!/^[A-Z0-9]+$/.test(kode)) {
                        e.preventDefault();
                        alert('Kode program studi hanya boleh berisi huruf kapital dan angka');
                        document.getElementById('kode_program_studi').focus();
                        return false;
                    }
                });

                // Make functions globally accessible
                window.openModal = openModal;
                window.openEditModal = openEditModal;
                window.closeModal = closeModal;
                window.confirmDelete = confirmDelete;
            @endif

            // Make global functions accessible
            window.changeJenjang = changeJenjang;
            @if ($selectedJenjang)
                window.filterByStatus = filterByStatus;
            @endif
        </script>
    @endpush
@endsection
