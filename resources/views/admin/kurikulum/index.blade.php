@extends('layouts.app')

@section('title', 'Manajemen Kurikulum')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Manajemen Kurikulum</h1>
                            <p class="text-sm text-gray-600">Kelola kurikulum berdasarkan semester akademik</p>
                        </div>

                        @if ($selectedSemester)
                            <div class="mt-4 sm:mt-0">
                                <button onclick="openModal('create')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Kurikulum
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Filter Semester & Search -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
                        <!-- Filter Semester -->
                        <div class="flex items-center space-x-3">
                            <label class="text-xs font-medium text-gray-700">Semester berlaku:</label>
                            <select id="semesterFilter" onchange="changeSemester()"
                                class="text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[200px]">
                                <option value="">Pilih Semester</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id_semester }}"
                                        {{ $selectedSemesterId == $semester->id_semester ? 'selected' : '' }}
                                        data-active="{{ $semester->is_active ? '1' : '0' }}">
                                        {{ $semester->nama_semester }}
                                        {{ $semester->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search & Filter Program Studi -->
                        @if ($selectedSemester)
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
                                    <input type="text" id="searchInput" placeholder="Cari nama kurikulum..."
                                        class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                </div>

                                <!-- Filter Program Studi -->
                                <select id="programStudiFilter" onchange="filterByProgramStudi()"
                                    class="text-xs px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Semua Program Studi</option>
                                    @foreach ($programStudis as $prodi)
                                        <option value="{{ $prodi->id_program_studi }}"
                                            {{ request('program_studi') == $prodi->id_program_studi ? 'selected' : '' }}>
                                            {{ $prodi->nama_program_studi }}
                                            ({{ $prodi->jenjang->kode_jenjang_pendidikan }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if (!$selectedSemester)
                <!-- Empty State - Pilih Semester -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-search w-16 h-16 text-gray-400 mb-4 mx-auto text-6xl"></i>
                        <p class="text-lg font-medium text-gray-900 mb-2">Pilih Semester</p>
                        <p class="text-sm text-gray-500 mb-4">Pilih semester dari dropdown di atas untuk mengelola kurikulum
                        </p>
                    </div>
                </div>
            @else
                <!-- Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="kurikulumTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Kurikulum
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Program Studi
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        SKS Lulus
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        SKS Wajib
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        SKS Pilihan
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="kurikulumTableBody">
                                @forelse($kurikulums as $kurikulum)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 kurikulum-row"
                                        data-searchable data-program-studi="{{ $kurikulum->id_program_studi }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="searchable-nama">
                                                <div class="text-xs font-medium text-gray-900">
                                                    {{ $kurikulum->nama_kurikulum }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs text-gray-900">
                                                {{ $kurikulum->programStudi->nama_program_studi }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $kurikulum->programStudi->jenjang->nama_jenjang_pendidikan }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $kurikulum->jumlah_sks_lulus }} SKS
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $kurikulum->jumlah_sks_wajib }} SKS
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ $kurikulum->jumlah_sks_pilihan }} SKS
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                {{-- Tombol Kelola Mata Kuliah --}}
                                                <div class="relative group">
                                                    <a href="{{ route('kurikulum.mata-kuliah.index', $kurikulum->id_kurikulum) }}"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Kelola Mata Kuliah
                                                        <div
                                                            class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- Tombol Edit --}}
                                                <div class="relative group">
                                                    <button
                                                        onclick="openEditModal(
                                                        '{{ $kurikulum->id_kurikulum }}',
                                                        '{{ addslashes($kurikulum->nama_kurikulum) }}',
                                                        '{{ $kurikulum->jumlah_sks_lulus }}',
                                                        '{{ $kurikulum->jumlah_sks_wajib }}',
                                                        '{{ $kurikulum->jumlah_sks_pilihan }}',
                                                        '{{ $kurikulum->id_program_studi }}'
                                                    )"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="text-xs fas fa-edit"></i>
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Edit Kurikulum
                                                        <div
                                                            class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Tombol Hapus --}}
                                                <div class="relative group">
                                                    <button
                                                        onclick="confirmDelete('{{ $kurikulum->id_kurikulum }}', '{{ addslashes($kurikulum->nama_kurikulum) }}')"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Hapus Kurikulum
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
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium text-gray-900 mb-2">Belum ada kurikulum</p>
                                                <p class="text-xs text-gray-500">Tambahkan kurikulum baru untuk semester
                                                    {{ $selectedSemester->nama_semester }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($kurikulums->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $kurikulums->appends(request()->query())->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Create/Edit Modal -->
        @if ($selectedSemester)
            <div id="kurikulumModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

                    <div
                        class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
                        <form id="kurikulumForm" method="POST" action="{{ route('kurikulum.store') }}">
                            @csrf
                            <input type="hidden" name="id_semester" value="{{ $selectedSemester->id_semester }}">

                            <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 id="modalTitle" class="text-lg font-heading font-semibold text-gray-900">
                                        Tambah Kurikulum
                                    </h3>
                                    <button type="button" onclick="closeModal()"
                                        class="sm:hidden text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Nama Kurikulum <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="nama_kurikulum" id="nama_kurikulum"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="Contoh: Kurikulum 2023" maxlength="100" required>
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Program Studi <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text" id="program_studi_search"
                                                placeholder="Cari berdasarkan kode atau nama program studi..."
                                                autocomplete="off"
                                                class="w-full text-xs px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">

                                            <!-- Tombol Clear/Cancel -->
                                            <button type="button" id="clear_program_studi"
                                                onclick="clearProgramStudiSelection()"
                                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6m0 12L6 6"></path>
                                                </svg>
                                            </button>

                                            <!-- Hidden input untuk menyimpan ID yang dipilih -->
                                            <input type="hidden" name="id_program_studi" id="id_program_studi" required>

                                            <!-- Dropdown hasil pencarian -->
                                            <div id="program_studi_dropdown"
                                                class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                                <!-- Results akan dimuat di sini -->
                                            </div>

                                            <!-- Loading indicator -->
                                            <div id="search_loading" class="absolute right-8 top-2 hidden">
                                                <svg class="animate-spin h-4 w-4 text-gray-400" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            SKS Lulus <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="jumlah_sks_lulus" id="jumlah_sks_lulus"
                                            min="0" max="200"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="144" required>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            SKS Wajib <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="jumlah_sks_wajib" id="jumlah_sks_wajib"
                                            min="0" max="200"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="120" required>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            SKS Pilihan <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="jumlah_sks_pilihan" id="jumlah_sks_pilihan"
                                            min="0" max="200"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="24" required>
                                    </div>
                                </div>

                                <!-- Info Semester -->
                                <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-xs text-gray-600">
                                            Kurikulum ini akan ditambahkan ke semester:
                                            <strong>{{ $selectedSemester->nama_semester }}</strong>
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
            // Global variables for program studi search
            let programStudiData = [];
            let searchTimeout;

            // Load program studi data saat halaman dimuat
            document.addEventListener('DOMContentLoaded', function() {
                loadProgramStudiData();
            });

            // Function untuk load data program studi
            function loadProgramStudiData() {
                fetch('/kurikulum/api/program-studi')
                    .then(response => response.json())
                    .then(data => {
                        programStudiData = data;
                    })
                    .catch(error => {
                        console.error('Error loading program studi:', error);
                    });
            }

            // Change semester function
            function changeSemester() {
                const semesterId = document.getElementById('semesterFilter').value;
                if (semesterId) {
                    // Preserve other filters
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.set('semester', semesterId);
                    currentParams.delete('page'); // Reset pagination
                    window.location.href = window.location.pathname + '?' + currentParams.toString();
                } else {
                    // Clear semester filter
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.delete('semester');
                    currentParams.delete('page');
                    window.location.href = window.location.pathname + (currentParams.toString() ? '?' + currentParams
                        .toString() : '');
                }
            }

            @if ($selectedSemester)
                // REALTIME SEARCH & FILTER FUNCTIONALITY
                document.getElementById('searchInput').addEventListener('input', function() {
                    filterTable();
                });

                function filterByProgramStudi() {
                    filterTable();
                }

                function filterTable() {
                    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                    const programStudiFilter = document.getElementById('programStudiFilter').value;
                    const tbody = document.getElementById('kurikulumTableBody');
                    const rows = tbody.querySelectorAll('tr[data-searchable]');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const namaCell = row.querySelector('.searchable-nama');
                        const rowProgramStudi = row.getAttribute('data-program-studi');

                        const namaText = namaCell ? namaCell.textContent.toLowerCase() : '';

                        const matchesSearch = searchTerm === '' || namaText.includes(searchTerm);
                        const matchesProgramStudi = programStudiFilter === '' || rowProgramStudi === programStudiFilter;

                        const isMatch = matchesSearch && matchesProgramStudi;

                        if (isMatch) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    updateEmptyState(visibleCount, searchTerm, programStudiFilter);
                }

                function updateEmptyState(visibleCount, searchTerm, programStudiFilter) {
                    const tbody = document.getElementById('kurikulumTableBody');
                    let emptyRow = tbody.querySelector('tr[data-empty-search]');

                    if (emptyRow) {
                        emptyRow.remove();
                    }

                    if (visibleCount === 0 && (searchTerm !== '' || programStudiFilter !== '')) {
                        emptyRow = document.createElement('tr');
                        emptyRow.setAttribute('data-empty-search', 'true');

                        let message = 'Tidak ada hasil ditemukan';
                        let detail = '';

                        if (searchTerm && programStudiFilter) {
                            const prodiName = document.querySelector(`option[value="${programStudiFilter}"]`).textContent;
                            detail = `untuk pencarian "${searchTerm}" di ${prodiName}`;
                        } else if (searchTerm) {
                            detail = `untuk pencarian "${searchTerm}"`;
                        } else if (programStudiFilter) {
                            const prodiName = document.querySelector(`option[value="${programStudiFilter}"]`).textContent;
                            detail = `di ${prodiName}`;
                        }

                        emptyRow.innerHTML = `
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
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
                // PROGRAM STUDI SEARCH FUNCTIONALITY
                // ===============================================

                // Setup search functionality saat modal dibuka
                function setupProgramStudiSearch() {
                    const searchInput = document.getElementById('program_studi_search');
                    const hiddenInput = document.getElementById('id_program_studi');
                    const dropdown = document.getElementById('program_studi_dropdown');
                    const loadingIndicator = document.getElementById('search_loading');
                    const clearButton = document.getElementById('clear_program_studi');

                    if (!searchInput || !hiddenInput || !dropdown || !loadingIndicator || !clearButton) {
                        console.error('Program studi search elements not found');
                        return;
                    }

                    // Remove existing event listeners to prevent duplicates
                    const newSearchInput = searchInput.cloneNode(true);
                    searchInput.parentNode.replaceChild(newSearchInput, searchInput);

                    // Event listener untuk input search
                    newSearchInput.addEventListener('input', function() {
                        const query = this.value.toLowerCase().trim();

                        // Clear previous timeout
                        if (searchTimeout) {
                            clearTimeout(searchTimeout);
                        }

                        // Show/hide clear button
                        updateClearButton();

                        // Show loading
                        loadingIndicator.classList.remove('hidden');

                        // Debounce search
                        searchTimeout = setTimeout(() => {
                            searchProgramStudi(query);
                            loadingIndicator.classList.add('hidden');
                        }, 300);
                    });

                    // Event listener untuk focus - tampilkan hasil berdasarkan yang sudah dipilih atau semua
                    newSearchInput.addEventListener('focus', function() {
                        const currentValue = hiddenInput.value;

                        if (currentValue && this.value !== '') {
                            // Jika sudah ada yang dipilih, filter berdasarkan current search atau tampilkan yang dipilih
                            const query = this.value.toLowerCase().trim();
                            if (query === '') {
                                showSelectedProgramStudi(currentValue);
                            } else {
                                searchProgramStudi(query);
                            }
                        } else {
                            // Jika belum ada yang dipilih, tampilkan semua
                            showAllProgramStudi();
                        }
                    });

                    // Event listener untuk click - sama seperti focus
                    newSearchInput.addEventListener('click', function() {
                        const currentValue = hiddenInput.value;

                        if (currentValue && this.value !== '') {
                            const query = this.value.toLowerCase().trim();
                            searchProgramStudi(query);
                        } else {
                            showAllProgramStudi();
                        }
                    });

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        if (!newSearchInput.contains(event.target) && !dropdown.contains(event.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                }

                // Function baru untuk menampilkan program studi yang sudah dipilih
                function showSelectedProgramStudi(selectedId) {
                    const selectedData = programStudiData.filter(item => item.id_program_studi === selectedId);
                    displaySearchResults(selectedData.length > 0 ? selectedData : programStudiData);
                }

                // Function untuk update visibility tombol clear
                function updateClearButton() {
                    const searchInput = document.getElementById('program_studi_search');
                    const hiddenInput = document.getElementById('id_program_studi');
                    const clearButton = document.getElementById('clear_program_studi');

                    if (searchInput && hiddenInput && clearButton) {
                        if (hiddenInput.value) {
                            clearButton.classList.remove('hidden');
                        } else {
                            clearButton.classList.add('hidden');
                        }
                    }
                }

                // Function untuk search program studi
                function searchProgramStudi(query) {
                    const dropdown = document.getElementById('program_studi_dropdown');

                    if (query === '') {
                        showAllProgramStudi();
                        return;
                    }

                    // Filter data berdasarkan kode atau nama
                    const filteredData = programStudiData.filter(item => {
                        const kodeMatch = item.kode_program_studi && item.kode_program_studi.toLowerCase().includes(
                            query);
                        const namaMatch = item.nama_program_studi && item.nama_program_studi.toLowerCase().includes(
                            query);
                        const jenjangMatch = item.jenjang && item.jenjang.toLowerCase().includes(query);

                        return kodeMatch || namaMatch || jenjangMatch;
                    });

                    displaySearchResults(filteredData);
                }

                // Function untuk show all program studi
                function showAllProgramStudi() {
                    displaySearchResults(programStudiData);
                }

                // Function untuk display hasil search
                function displaySearchResults(data) {
                    const dropdown = document.getElementById('program_studi_dropdown');

                    if (data.length === 0) {
                        dropdown.innerHTML = `
                        <div class="px-4 py-3 text-center text-xs text-gray-500">
                            <i class="fas fa-search mb-2"></i>
                            <div>Tidak ada program studi ditemukan</div>
                        </div>
                    `;
                    } else {
                        dropdown.innerHTML = data.map(item => `
                        <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                             onclick="selectProgramStudi('${item.id_program_studi}', '${escapeHtml(item.kode_program_studi || '')}', '${escapeHtml(item.nama_program_studi)}', '${escapeHtml(item.jenjang)}')">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-medium text-gray-900">${item.nama_program_studi}</div>
                                    <div class="text-xs text-gray-500">
                                        ${item.kode_program_studi ? `${item.kode_program_studi} • ` : ''}${item.jenjang}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                    }

                    dropdown.classList.remove('hidden');
                }

                // Function untuk select program studi
                function selectProgramStudi(id, kode, nama, jenjang) {
                    const searchInput = document.getElementById('program_studi_search');
                    const hiddenInput = document.getElementById('id_program_studi');
                    const dropdown = document.getElementById('program_studi_dropdown');

                    // Set values
                    searchInput.value = `${kode} - ${jenjang} ${nama}`;
                    hiddenInput.value = id;

                    // Update clear button visibility
                    updateClearButton();

                    // Hide dropdown
                    dropdown.classList.add('hidden');

                    // Add visual feedback
                    searchInput.classList.add('border-green-300', 'bg-green-50');
                    setTimeout(() => {
                        searchInput.classList.remove('border-green-300', 'bg-green-50');
                    }, 1000);
                }

                // Function untuk clear selection
                function clearProgramStudiSelection() {
                    const searchInput = document.getElementById('program_studi_search');
                    const hiddenInput = document.getElementById('id_program_studi');
                    const dropdown = document.getElementById('program_studi_dropdown');
                    const clearButton = document.getElementById('clear_program_studi');

                    if (searchInput) searchInput.value = '';
                    if (hiddenInput) hiddenInput.value = '';
                    if (dropdown) dropdown.classList.add('hidden');
                    if (clearButton) clearButton.classList.add('hidden');

                    // Focus ke search input setelah clear
                    if (searchInput) searchInput.focus();
                }

                // Helper function untuk escape HTML
                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                // ===============================================
                // MODAL FUNCTIONS
                // ===============================================

                // Modal Functions
                function openModal(type) {
                    const modal = document.getElementById('kurikulumModal');
                    const form = document.getElementById('kurikulumForm');
                    const modalTitle = document.getElementById('modalTitle');
                    const submitText = document.getElementById('submitText');

                    form.reset();
                    clearProgramStudiSelection(); // Clear program studi selection

                    if (type === 'create') {
                        form.action = '{{ route('kurikulum.store') }}';
                        form.querySelector('input[name="_method"]')?.remove();
                        modalTitle.textContent = 'Tambah Kurikulum';
                        submitText.textContent = 'Simpan';
                    }

                    modal.classList.remove('hidden');

                    // Setup search functionality
                    setTimeout(() => {
                        setupProgramStudiSearch();
                        updateClearButton(); // Update clear button state
                        document.getElementById('nama_kurikulum').focus();
                    }, 100);
                }

                function openEditModal(id, nama, sksLulus, sksWajib, sksPilihan, programStudiId) {
                    const modal = document.getElementById('kurikulumModal');
                    const form = document.getElementById('kurikulumForm');
                    const modalTitle = document.getElementById('modalTitle');
                    const submitText = document.getElementById('submitText');

                    form.action = `/kurikulum/${id}`;

                    let methodInput = form.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        form.appendChild(methodInput);
                    }
                    methodInput.value = 'PUT';

                    // Set basic values
                    document.getElementById('nama_kurikulum').value = nama || '';
                    document.getElementById('jumlah_sks_lulus').value = sksLulus || '';
                    document.getElementById('jumlah_sks_wajib').value = sksWajib || '';
                    document.getElementById('jumlah_sks_pilihan').value = sksPilihan || '';

                    const selectedProgramStudiId = programStudiId;

                    modalTitle.textContent = 'Edit Kurikulum';
                    submitText.textContent = 'Update';

                    modal.classList.remove('hidden');

                    // Setup search functionality dan set program studi
                    setTimeout(() => {
                        setupProgramStudiSearch();

                        // Set program studi setelah setup
                        if (selectedProgramStudiId && programStudiData.length > 0) {
                            const programStudi = programStudiData.find(p => p.id_program_studi ===
                                selectedProgramStudiId);
                            if (programStudi) {
                                document.getElementById('program_studi_search').value =
                                    `${programStudi.kode_program_studi} - ${programStudi.jenjang} ${programStudi.nama_program_studi}`;
                                document.getElementById('id_program_studi').value = selectedProgramStudiId;

                                // Update clear button visibility
                                updateClearButton();
                            }
                        }

                        document.getElementById('nama_kurikulum').focus();
                    }, 100);
                }

                function closeModal() {
                    document.getElementById('kurikulumModal').classList.add('hidden');
                }

                function confirmDelete(id, name) {
                    if (confirm(`Yakin ingin menghapus kurikulum "${name}"?`)) {
                        const form = document.getElementById('delete-form');
                        form.action = `/kurikulum/${id}`;
                        form.submit();
                    }
                }

                // Close modal with Escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeModal();
                    }
                });

                // Form validation
                document.getElementById('kurikulumForm').addEventListener('submit', function(e) {
                    const requiredFields = ['nama_kurikulum', 'id_program_studi', 'jumlah_sks_lulus',
                        'jumlah_sks_wajib', 'jumlah_sks_pilihan'
                    ];

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

                    // Validate SKS
                    const sksLulus = parseInt(document.getElementById('jumlah_sks_lulus').value) || 0;
                    const sksWajib = parseInt(document.getElementById('jumlah_sks_wajib').value) || 0;
                    const sksPilihan = parseInt(document.getElementById('jumlah_sks_pilihan').value) || 0;
                    const totalSks = sksWajib + sksPilihan;

                    if (totalSks > sksLulus) {
                        e.preventDefault();
                        alert('Total SKS Wajib dan Pilihan tidak boleh melebihi SKS Lulus');
                        document.getElementById('jumlah_sks_lulus').focus();
                        return false;
                    }

                    if (sksLulus <= 0 || sksWajib < 0 || sksPilihan < 0) {
                        e.preventDefault();
                        alert('Jumlah SKS harus berupa angka positif');
                        return false;
                    }
                });

                // Make functions globally accessible
                window.selectProgramStudi = selectProgramStudi;
                window.openModal = openModal;
                window.openEditModal = openEditModal;
                window.closeModal = closeModal;
                window.confirmDelete = confirmDelete;
            @endif

            // Make global functions accessible
            window.changeSemester = changeSemester;
            @if ($selectedSemester)
                window.filterByProgramStudi = filterByProgramStudi;
            @endif
        </script>
    @endpush
@endsection
