@extends('layouts.app')

@section('title', 'Data Akademik Mahasiswa')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Nilai Mahasiswa</h1>
                            <p class="text-sm text-gray-600">Lihat dan kelola data akademik seluruh mahasiswa</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                            <!-- Import Button -->
                            <button onclick="openImportModal()"
                                class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                    </path>
                                </svg>
                                Import Nilai Historis
                            </button>

                            <!-- Export Button -->
                            <a href="{{ route('admin.nilai-historis.export') }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Export Nilai
                            </a>

                            <!-- Download Template -->
                            <a href="{{ route('admin.nilai-historis.export-template') }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Template Excel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
                        <!-- Search -->
                        <div class="flex items-center space-x-3">
                            <label class="text-xs font-medium text-gray-700">Pencarian:</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" placeholder="Cari berdasarkan NIM atau nama..."
                                    class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[250px]">
                            </div>
                        </div>

                        <!-- Filter Program Studi -->
                        <div class="flex items-center space-x-3">
                            <label class="text-xs font-medium text-gray-700">Program Studi:</label>
                            <select id="programStudiFilter" onchange="filterByProgramStudi()"
                                class="text-xs py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[200px]">
                                <option value="">Semua Program Studi</option>
                                @foreach ($programStudis as $prodi)
                                    <option value="{{ $prodi->id_program_studi }}"
                                        {{ request('program_studi') == $prodi->id_program_studi ? 'selected' : '' }}>
                                        {{ $prodi->jenjang->kode_jenjang_pendidikan }} - {{ $prodi->nama_program_studi }}
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
                    <table class="min-w-full divide-y divide-gray-200" id="mahasiswaTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    NIM
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Mahasiswa
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Program Studi
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Angkatan
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="mahasiswaTableBody">
                            @forelse($mahasiswaList as $mahasiswa)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 mahasiswa-row" data-searchable
                                    data-prodi="{{ $mahasiswa->id_program_studi }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="searchable-nim">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-medium bg-gray-100 text-gray-800">
                                                {{ $mahasiswa->nim }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="searchable-nama">
                                            <div class="text-xs font-medium text-gray-900">
                                                {{ $mahasiswa->pengguna->nama }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $mahasiswa->pengguna->email ?? 'Email tidak tersedia' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="searchable-prodi">
                                            <div class="text-xs font-medium text-gray-900">
                                                {{ $mahasiswa->programStudi->nama_program_studi }}
                                            </div>
                                            <div class="flex items-center space-x-2 mt-2">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $mahasiswa->programStudi->jenjang->kode_jenjang_pendidikan }}
                                                </span>
                                                @if ($mahasiswa->programStudi->kode_program_studi)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
                                                        {{ $mahasiswa->programStudi->kode_program_studi }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="searchable-angkatan">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $mahasiswa->angkatan }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            {{-- KHS --}}
                                            <div class="relative group">
                                                <a href="{{ route('admin.akademik.khs.mahasiswa.index', ['mahasiswa_id' => $mahasiswa->id_mahasiswa]) }}"
                                                    class="inline-flex text-xs bg-gray-900 items-center px-3 py-1.5 text-white hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                                    KHS
                                                </a>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                    Kartu Hasil Studi (KHS)
                                                    <div
                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Transcript --}}
                                            <div class="relative group">
                                                <a href="{{ route('admin.akademik.transcript.mahasiswa.index', ['mahasiswa_id' => $mahasiswa->id_mahasiswa]) }}"
                                                    class="inline-flex text-xs bg-gray-900 items-center px-3 py-1.5 text-white hover:bg-gray-700 rounded-lg transition-colors duration-200">
                                                    Transkrip
                                                </a>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                    Transcript Akademik
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
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z">
                                                </path>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 mb-2">Belum ada data mahasiswa</p>
                                            <p class="text-xs text-gray-500">Data mahasiswa akan muncul di sini</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Client-Side Pagination Controls -->
                <div id="pagination-container"
                    class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="flex items-center text-xs text-gray-500">
                        <span id="pagination-info">Menampilkan 0 dari 0 data</span>
                        <div class="ml-4 flex items-center space-x-2">
                            <label for="items-per-page" class="text-xs">Per halaman:</label>
                            <select id="items-per-page" class="text-xs border border-gray-300 rounded py-1">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center space-x-1" id="pagination-buttons"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <div id="importModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeImportModal()"></div>

            <div
                class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">
                <form id="importForm" method="POST" action="{{ route('admin.nilai-historis.import') }}"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-heading font-semibold text-gray-900">Import Nilai Historis</h3>
                            <button type="button" onclick="closeImportModal()"
                                class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">
                                    File Excel <span class="text-red-500">*</span>
                                </label>
                                <input type="file" name="file" id="import_file" accept=".xlsx,.xls"
                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                    required>
                                <p class="text-xs text-gray-500 mt-1">Format: .xlsx atau .xls (max 5MB)</p>
                            </div>

                            <div class="bg-blue-50 p-3 rounded-lg">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-2 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-xs font-medium text-blue-800 mb-1">Informasi</p>
                                        <ul class="text-xs text-blue-700 space-y-1">
                                            <li>• Download template Excel terlebih dahulu</li>
                                            <li>• Isi data sesuai format yang ada di template</li>
                                            <li>• Kolom wajib: NIM, Kode MK, Kode Semester, Nilai Huruf, Nilai Angka, Nilai
                                                Indeks</li>
                                            <li>• Sistem akan auto-create registrasi jika belum ada</li>
                                            <li>• Data yang sudah ada akan di-UPDATE</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                        <button type="button" onclick="closeImportModal()"
                            class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                            Batal
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Import Error/Warning Modal --}}
    @if (session('import_errors') || session('import_warnings'))
        <div id="import-result-modal" class="fixed inset-0 z-[9999] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeResultModal()">
                </div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
                    <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.966-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-heading font-semibold text-gray-900">Detail Import</h3>
                            </div>
                            <button type="button" onclick="closeResultModal()"
                                class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="max-h-96 overflow-y-auto space-y-4">
                            @if (session('import_errors'))
                                <div>
                                    <h4 class="text-sm font-semibold text-red-800 mb-2">Errors
                                        ({{ count(session('import_errors')) }})</h4>
                                    <div class="space-y-2">
                                        @foreach (session('import_errors') as $error)
                                            <div class="flex items-start p-3 bg-red-50 border border-red-200 rounded-lg">
                                                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-2 flex-shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <p class="text-xs text-red-800">{{ $error }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if (session('import_warnings'))
                                <div>
                                    <h4 class="text-sm font-semibold text-yellow-800 mb-2">Warnings
                                        ({{ count(session('import_warnings')) }})</h4>
                                    <div class="space-y-2">
                                        @foreach (session('import_warnings') as $warning)
                                            <div
                                                class="flex items-start p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.966-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                    </path>
                                                </svg>
                                                <p class="text-xs text-yellow-800">{{ $warning }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 sm:px-6 py-4 flex justify-end">
                        <button type="button" onclick="closeResultModal()"
                            class="inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            // REALTIME SEARCH & FILTER
            document.getElementById('searchInput').addEventListener('input', function() {
                filterTable();
            });

            function filterByProgramStudi() {
                filterTable();
            }

            function filterTable() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                const prodiFilter = document.getElementById('programStudiFilter').value;
                const tbody = document.getElementById('mahasiswaTableBody');
                const rows = tbody.querySelectorAll('tr[data-searchable]');
                let visibleCount = 0;

                rows.forEach(row => {
                    row.removeAttribute('data-pagination-hidden');

                    const nimCell = row.querySelector('.searchable-nim');
                    const namaCell = row.querySelector('.searchable-nama');
                    const prodiCell = row.querySelector('.searchable-prodi');
                    const angkatanCell = row.querySelector('.searchable-angkatan');
                    const rowProdi = row.getAttribute('data-prodi');

                    const nimText = nimCell ? nimCell.textContent.toLowerCase() : '';
                    const namaText = namaCell ? namaCell.textContent.toLowerCase() : '';
                    const prodiText = prodiCell ? prodiCell.textContent.toLowerCase() : '';
                    const angkatanText = angkatanCell ? angkatanCell.textContent.toLowerCase() : '';

                    const matchesSearch = searchTerm === '' ||
                        nimText.includes(searchTerm) ||
                        namaText.includes(searchTerm) ||
                        prodiText.includes(searchTerm) ||
                        angkatanText.includes(searchTerm);
                    const matchesProdi = prodiFilter === '' || rowProdi === prodiFilter;

                    const isMatch = matchesSearch && matchesProdi;

                    if (isMatch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateEmptyState(visibleCount, searchTerm, prodiFilter);

                if (window.pagination) {
                    window.pagination.onFilterChange();
                }
            }

            function updateEmptyState(visibleCount, searchTerm, prodiFilter) {
                const tbody = document.getElementById('mahasiswaTableBody');
                let emptyRow = tbody.querySelector('tr[data-empty-search]');

                if (emptyRow) {
                    emptyRow.remove();
                }

                if (visibleCount === 0 && (searchTerm !== '' || prodiFilter !== '')) {
                    emptyRow = document.createElement('tr');
                    emptyRow.setAttribute('data-empty-search', 'true');

                    let detail = '';
                    if (searchTerm && prodiFilter) {
                        detail = `untuk pencarian "${searchTerm}" di program studi yang dipilih`;
                    } else if (searchTerm) {
                        detail = `untuk pencarian "${searchTerm}"`;
                    } else if (prodiFilter) {
                        detail = `di program studi yang dipilih`;
                    }

                    emptyRow.innerHTML = `
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-900 mb-2">Tidak ada hasil ditemukan</p>
                                <p class="text-xs text-gray-500">${detail}</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                }
            }

            // CLIENT-SIDE PAGINATION
            class ClientPagination {
                constructor() {
                    this.currentPage = 1;
                    this.itemsPerPage = 10;
                    this.totalItems = 0;
                    this.visibleItems = [];
                    this.allRows = [];
                    this.init();
                }

                init() {
                    const itemsPerPageSelect = document.getElementById('items-per-page');
                    if (itemsPerPageSelect) {
                        itemsPerPageSelect.addEventListener('change', (e) => {
                            this.itemsPerPage = parseInt(e.target.value);
                            this.currentPage = 1;
                            this.updatePagination();
                        });
                    }
                    setTimeout(() => this.updatePagination(), 100);
                }

                updateRowsData() {
                    const tbody = document.getElementById('mahasiswaTableBody');
                    if (!tbody) return;
                    this.allRows = Array.from(tbody.querySelectorAll('tr[data-searchable]'));
                    this.updateVisibleRows();
                }

                updateVisibleRows() {
                    this.visibleItems = this.allRows.filter(row => {
                        const isHiddenByFilter = row.style.display === 'none' && !row.hasAttribute(
                            'data-pagination-hidden');
                        return !isHiddenByFilter;
                    });
                    this.totalItems = this.visibleItems.length;
                }

                updatePagination() {
                    this.updateRowsData();
                    const paginationContainer = document.getElementById('pagination-container');
                    if (!paginationContainer) return;

                    if (this.totalItems === 0) {
                        paginationContainer.style.display = 'none';
                        return;
                    } else {
                        paginationContainer.style.display = 'flex';
                    }

                    const totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
                    if (this.currentPage > totalPages) {
                        this.currentPage = Math.max(1, totalPages);
                    }

                    this.showPageItems();
                    this.updatePaginationInfo();
                    this.updatePaginationButtons(totalPages);
                }

                showPageItems() {
                    if (this.visibleItems.length === 0) return;
                    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
                    const endIndex = startIndex + this.itemsPerPage;

                    this.visibleItems.forEach(row => {
                        row.removeAttribute('data-pagination-hidden');
                        row.style.display = '';
                    });

                    this.visibleItems.forEach((row, index) => {
                        if (index < startIndex || index >= endIndex) {
                            row.style.display = 'none';
                            row.setAttribute('data-pagination-hidden', 'true');
                        }
                    });
                }

                updatePaginationInfo() {
                    const paginationInfo = document.getElementById('pagination-info');
                    if (!paginationInfo) return;
                    const startItem = this.totalItems === 0 ? 0 : (this.currentPage - 1) * this.itemsPerPage + 1;
                    const endItem = Math.min(this.currentPage * this.itemsPerPage, this.totalItems);
                    paginationInfo.textContent = `Menampilkan ${startItem}-${endItem} dari ${this.totalItems} data`;
                }

                updatePaginationButtons(totalPages) {
                    const container = document.getElementById('pagination-buttons');
                    if (!container) return;
                    container.innerHTML = '';
                    if (totalPages <= 1) return;

                    const prevBtn = this.createPaginationButton('Sebelumnya', this.currentPage > 1, () => {
                        if (this.currentPage > 1) {
                            this.currentPage--;
                            this.updatePagination();
                        }
                    });
                    container.appendChild(prevBtn);

                    const startPage = Math.max(1, this.currentPage - 2);
                    const endPage = Math.min(totalPages, this.currentPage + 2);

                    if (startPage > 1) {
                        container.appendChild(this.createPaginationButton('1', true, () => {
                            this.currentPage = 1;
                            this.updatePagination();
                        }));
                        if (startPage > 2) {
                            container.appendChild(this.createPaginationButton('...', false));
                        }
                    }

                    for (let i = startPage; i <= endPage; i++) {
                        const isActive = i === this.currentPage;
                        container.appendChild(this.createPaginationButton(i.toString(), true, () => {
                            this.currentPage = i;
                            this.updatePagination();
                        }, isActive));
                    }

                    if (endPage < totalPages) {
                        if (endPage < totalPages - 1) {
                            container.appendChild(this.createPaginationButton('...', false));
                        }
                        container.appendChild(this.createPaginationButton(totalPages.toString(), true, () => {
                            this.currentPage = totalPages;
                            this.updatePagination();
                        }));
                    }

                    const nextBtn = this.createPaginationButton('Selanjutnya', this.currentPage < totalPages, () => {
                        if (this.currentPage < totalPages) {
                            this.currentPage++;
                            this.updatePagination();
                        }
                    });
                    container.appendChild(nextBtn);
                }

                createPaginationButton(text, enabled, clickHandler = null, isActive = false) {
                    const button = document.createElement('button');
                    button.textContent = text;
                    button.className = `px-3 py-1 text-xs border rounded transition-colors duration-200 ${
                        isActive ? 'bg-gray-900 text-white border-gray-900' : 
                        enabled ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' : 
                        'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'}`;

                    if (enabled && clickHandler) {
                        button.addEventListener('click', clickHandler);
                    } else {
                        button.disabled = !enabled;
                    }
                    return button;
                }

                onFilterChange() {
                    this.currentPage = 1;
                    this.updatePagination();
                }
            }

            // MODAL FUNCTIONS
            function openImportModal() {
                document.getElementById('importModal').classList.remove('hidden');
            }

            function closeImportModal() {
                document.getElementById('importModal').classList.add('hidden');
                document.getElementById('importForm').reset();
            }

            function closeResultModal() {
                const modal = document.getElementById('import-result-modal');
                if (modal) modal.style.display = 'none';
            }

            // Initialize
            let pagination;
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    pagination = new ClientPagination();
                    window.pagination = pagination;
                }, 200);

                // Show result modal if exists
                const resultModal = document.getElementById('import-result-modal');
                if (resultModal) resultModal.style.display = 'block';

                // ESC key to close modals
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeImportModal();
                        closeResultModal();
                    }
                });
            });
        </script>
    @endpush
@endsection
