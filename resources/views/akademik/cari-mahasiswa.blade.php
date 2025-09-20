{{-- resources/views/akademik/cari-mahasiswa.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Akademik Mahasiswa')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col space-y-1">
                        <h1 class="text-xl font-semibold font-heading text-gray-900">Data Akademik Mahasiswa</h1>
                        <p class="text-sm text-gray-600">Lihat dan kelola data akademik seluruh mahasiswa</p>
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
                                            {{-- Dashboard --}}
                                            {{-- <div class="relative group">
                                                <a href="{{ route('admin.akademik.dashboard.mahasiswa', ['mahasiswa_id' => $mahasiswa->id_mahasiswa]) }}"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                    <i class="fas fa-chart-line text-xs"></i>
                                                </a>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                    Dashboard Akademik
                                                    <div
                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                    </div>
                                                </div>
                                            </div> --}}

                                            {{-- KHS --}}
                                            <div class="relative group">
                                                <a href="{{ route('admin.akademik.khs.mahasiswa.index', ['mahasiswa_id' => $mahasiswa->id_mahasiswa]) }}"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                    <i class="fas fa-file-alt text-xs"></i>
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
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                    <i class="fas fa-certificate text-xs"></i>
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
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
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
                <div id="pagination-container" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <!-- Info pagination -->
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

                    <!-- Pagination buttons -->
                    <div class="flex items-center space-x-1" id="pagination-buttons">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // REALTIME SEARCH & FILTER FUNCTIONALITY
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

                // Update pagination setelah filter
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

                    let message = 'Tidak ada hasil ditemukan';
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
                                <p class="text-lg font-medium text-gray-900 mb-2">${message}</p>
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

                    this.updatePagination();
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

                    // Previous button
                    const prevBtn = this.createPaginationButton('Sebelumnya', this.currentPage > 1, () => {
                        if (this.currentPage > 1) {
                            this.currentPage--;
                            this.updatePagination();
                        }
                    });
                    container.appendChild(prevBtn);

                    // Page number buttons
                    const startPage = Math.max(1, this.currentPage - 2);
                    const endPage = Math.min(totalPages, this.currentPage + 2);

                    // First page if not in range
                    if (startPage > 1) {
                        container.appendChild(this.createPaginationButton('1', true, () => {
                            this.currentPage = 1;
                            this.updatePagination();
                        }));

                        if (startPage > 2) {
                            container.appendChild(this.createPaginationButton('...', false));
                        }
                    }

                    // Page numbers
                    for (let i = startPage; i <= endPage; i++) {
                        const isActive = i === this.currentPage;
                        container.appendChild(this.createPaginationButton(i.toString(), true, () => {
                            this.currentPage = i;
                            this.updatePagination();
                        }, isActive));
                    }

                    // Last page if not in range
                    if (endPage < totalPages) {
                        if (endPage < totalPages - 1) {
                            container.appendChild(this.createPaginationButton('...', false));
                        }

                        container.appendChild(this.createPaginationButton(totalPages.toString(), true, () => {
                            this.currentPage = totalPages;
                            this.updatePagination();
                        }));
                    }

                    // Next button
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
                        isActive 
                            ? 'bg-gray-900 text-white border-gray-900' 
                            : enabled 
                                ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' 
                                : 'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                    }`;

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

            // Initialize pagination
            let pagination;
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    pagination = new ClientPagination();
                    window.pagination = pagination;
                }, 100);
            });
        </script>
    @endpush
@endsection
