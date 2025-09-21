@extends('layouts.app')

@section('title', 'Pengumuman')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="w-full">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Pengumuman</h1>
                            <p class="text-sm text-gray-600">
                                @role('admin')
                                    Kelola pengumuman untuk mahasiswa, dosen, dan umum
                                @else
                                    Pantau informasi di portal pengumuman
                                @endrole
                            </p>
                        </div>

                        @role('admin')
                            <div class="mt-4 sm:mt-0">
                                <button onclick="openModal('create')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Pengumuman
                                </button>
                            </div>
                        @endrole
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
                                <input type="text" id="searchInput" placeholder="Cari judul pengumuman..."
                                    value="{{ request('search') }}"
                                    class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[280px]">
                            </div>
                        </div>

                        @role('admin')
                            <!-- Filter Tujuan -->
                            <div class="flex items-center space-x-3">
                                <label class="text-xs font-medium text-gray-700">Filter Tujuan:</label>
                                <select id="tujuanFilter" onchange="filterByTujuan()"
                                    class="text-xs py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Semua Tujuan</option>
                                    <option value="mahasiswa">Mahasiswa</option>
                                    <option value="dosen">Dosen</option>
                                    <option value="umum">Umum</option>
                                </select>
                            </div>
                        @endrole
                    </div>
                </div>
            </div>

            @role('admin')
                <!-- Desktop Table View -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full table-fixed divide-y divide-gray-200" id="pengumumanTable">
                            <colgroup>
                                <col class="w-2/5"> <!-- Judul -->
                                <col class="w-1/6"> <!-- Tujuan -->
                                <col class="w-1/6"> <!-- Tanggal -->
                                <col class="w-1/6"> <!-- Aksi -->
                            </colgroup>
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Judul
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tujuan
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="pengumumanTableBody">
                                @forelse($pengumuman as $item)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 pengumuman-row" data-searchable
                                        data-tujuan="{{ $item->tujuan }}">
                                        <td class="px-6 py-4">
                                            <div class="searchable-judul">
                                                <div class="text-xs font-medium text-gray-900">
                                                    {{ $item->judul }}
                                                </div>
                                                @if (!empty($item->deskripsi))
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ Str::limit(strip_tags($item->deskripsi), 80) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span @class([
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                'bg-blue-100 text-blue-800' => $item->tujuan === 'mahasiswa',
                                                'bg-green-100 text-green-800' => $item->tujuan === 'dosen',
                                                'bg-purple-100 text-purple-800' => $item->tujuan === 'umum',
                                            ])>
                                                {{ ucfirst($item->tujuan) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs text-gray-900">
                                                {{ $item->created_at->format('d M Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <!-- View Button -->
                                                <div class="relative group">
                                                    <button onclick="showDetail('{{ $item->id }}')"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="text-xs fas fa-eye"></i>
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Lihat Detail
                                                        <div
                                                            class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Edit Button -->
                                                <div class="relative group">
                                                    <button
                                                        onclick="openEditModal('{{ $item->id }}', '{{ addslashes($item->judul) }}', '{{ addslashes($item->deskripsi) }}', '{{ $item->tujuan }}', '{{ $item->dokumen ? addslashes($item->dokumen) : '' }}')"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="text-xs fas fa-edit"></i>
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Edit Pengumuman
                                                        <div
                                                            class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Delete Button -->
                                                <div class="relative group">
                                                    <button
                                                        onclick="confirmDelete('{{ $item->id }}', '{{ addslashes($item->judul) }}')"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Hapus Pengumuman
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
                                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                </svg>
                                                <p class="text-lg font-medium text-gray-900 mb-2">Belum ada pengumuman</p>
                                                <p class="text-xs text-gray-500">Tambahkan pengumuman baru untuk sistem</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($pengumuman->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $pengumuman->appends(request()->query())->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            @else
                <!-- Card View for Non-Admin Users -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="pengumumanCards">
                    @forelse($pengumuman as $item)
                        <div class="pengumuman-card bg-white border border-gray-100 shadow-sm rounded-lg transition-all duration-200 hover:shadow-md cursor-pointer"
                            onclick="showDetail('{{ $item->id }}')" data-searchable>
                            <div class="p-4 border-b border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3
                                            class="text-sm font-semibold text-gray-900 leading-tight line-clamp-2 searchable-judul">
                                            {{ $item->judul }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                @if (!empty($item->deskripsi))
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-600 leading-relaxed line-clamp-2">
                                            {{ Str::limit(strip_tags($item->deskripsi), 100) }}
                                        </p>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ $item->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full border border-gray-100 shadow-sm bg-white rounded-lg">
                            <div class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-600 mb-1">Belum ada pengumuman untuk anda</h3>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            @endrole
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @role('admin')
        <div id="pengumumanModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-4xl mx-auto">
                    <form id="pengumumanForm" method="POST" action="{{ route('pengumuman.store') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 id="modalTitle" class="text-lg font-heading font-semibold text-gray-900">
                                    Tambah Pengumuman
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
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Judul Pengumuman <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="judul" id="judul"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Masukkan judul pengumuman" maxlength="255" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Tujuan <span class="text-red-500">*</span>
                                    </label>
                                    <select name="tujuan" id="tujuan" required
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                        <option value="">Pilih Tujuan</option>
                                        <option value="mahasiswa">Mahasiswa</option>
                                        <option value="dosen">Dosen</option>
                                        <option value="umum">Umum</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Dokumen Pendukung
                                    </label>
                                    <input type="file" name="dokumen" id="dokumen" accept=".pdf,.doc,.docx,.ppt,.pptx"
                                        class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                                    <p class="mt-1 text-xs text-gray-500">
                                        PDF, DOC, DOCX, PPT, PPTX (Max: 5MB)
                                    </p>
                                    <div id="currentFile" class="mt-2 hidden">
                                        <p class="text-xs text-gray-600">File saat ini: <span id="currentFileName"></span></p>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Deskripsi <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="deskripsi" id="deskripsi" rows="8"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Masukkan deskripsi pengumuman" required></textarea>
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
    @endrole

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDetailModal()"></div>

            <div
                class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-4xl mx-auto max-h-[90vh] overflow-y-auto">
                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-heading font-semibold text-gray-900">Detail Pengumuman</h3>
                        <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div id="detailContent" class="space-y-4">
                        <!-- Content will be loaded here -->
                    </div>
                </div>

                <div class="bg-gray-50 px-4 sm:px-6 py-4 flex justify-end">
                    <button type="button" onclick="closeDetailModal()"
                        class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            // Data pengumuman untuk detail view
            let pengumumanData = @json($pengumuman->items());

            // SEARCH & FILTER FUNCTIONALITY
            document.getElementById('searchInput').addEventListener('input', function() {
                filterTable();
            });

            function filterByTujuan() {
                filterTable();
            }

            function filterTable() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                const tujuanFilter = document.getElementById('tujuanFilter') ? document.getElementById('tujuanFilter').value :
                    '';

                @role('admin')
                    const tbody = document.getElementById('pengumumanTableBody');
                    const rows = tbody.querySelectorAll('tr[data-searchable]');
                @else
                    const container = document.getElementById('pengumumanCards');
                    const rows = container.querySelectorAll('.pengumuman-card[data-searchable]');
                @endrole

                let visibleCount = 0;

                rows.forEach(row => {
                    const judulCell = row.querySelector('.searchable-judul');
                    const rowTujuan = row.getAttribute('data-tujuan');

                    const judulText = judulCell ? judulCell.textContent.toLowerCase() : '';

                    const matchesSearch = searchTerm === '' || judulText.includes(searchTerm);
                    const matchesTujuan = tujuanFilter === '' || rowTujuan === tujuanFilter;

                    const isMatch = matchesSearch && matchesTujuan;

                    if (isMatch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateEmptyState(visibleCount, searchTerm, tujuanFilter);
            }

            function updateEmptyState(visibleCount, searchTerm, tujuanFilter) {
                @role('admin')
                    const tbody = document.getElementById('pengumumanTableBody');
                    let emptyRow = tbody.querySelector('tr[data-empty-search]');

                    if (emptyRow) {
                        emptyRow.remove();
                    }

                    if (visibleCount === 0 && (searchTerm !== '' || tujuanFilter !== '')) {
                        emptyRow = document.createElement('tr');
                        emptyRow.setAttribute('data-empty-search', 'true');

                        let message = 'Tidak ada hasil ditemukan';
                        let detail = '';

                        if (searchTerm && tujuanFilter) {
                            detail = `untuk pencarian "${searchTerm}" dengan tujuan ${tujuanFilter}`;
                        } else if (searchTerm) {
                            detail = `untuk pencarian "${searchTerm}"`;
                        } else if (tujuanFilter) {
                            detail = `dengan tujuan ${tujuanFilter}`;
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
                @endrole
            }

            // MODAL FUNCTIONS
            @role('admin')
                function openModal(type) {
                    const modal = document.getElementById('pengumumanModal');
                    const form = document.getElementById('pengumumanForm');
                    const modalTitle = document.getElementById('modalTitle');
                    const submitText = document.getElementById('submitText');

                    form.reset();
                    document.getElementById('currentFile').classList.add('hidden');

                    if (type === 'create') {
                        form.action = '{{ route('pengumuman.store') }}';
                        form.querySelector('input[name="_method"]')?.remove();
                        modalTitle.textContent = 'Tambah Pengumuman';
                        submitText.textContent = 'Simpan';
                    }

                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        document.getElementById('judul').focus();
                    }, 100);
                }

                function openEditModal(id, judul, deskripsi, tujuan, dokumen) {
                    const modal = document.getElementById('pengumumanModal');
                    const form = document.getElementById('pengumumanForm');
                    const modalTitle = document.getElementById('modalTitle');
                    const submitText = document.getElementById('submitText');

                    form.action = `/pengumuman/${id}`;

                    let methodInput = form.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        form.appendChild(methodInput);
                    }
                    methodInput.value = 'PUT';

                    // Set values
                    document.getElementById('judul').value = judul || '';
                    document.getElementById('deskripsi').value = deskripsi || '';
                    document.getElementById('tujuan').value = tujuan || '';

                    // Show current file if exists
                    if (dokumen) {
                        document.getElementById('currentFile').classList.remove('hidden');
                        document.getElementById('currentFileName').textContent = dokumen.split('/').pop();
                    } else {
                        document.getElementById('currentFile').classList.add('hidden');
                    }

                    modalTitle.textContent = 'Edit Pengumuman';
                    submitText.textContent = 'Update';

                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        document.getElementById('judul').focus();
                    }, 100);
                }

                function closeModal() {
                    document.getElementById('pengumumanModal').classList.add('hidden');
                }
            @endrole

            // DETAIL MODAL
            function showDetail(id) {
                const pengumuman = pengumumanData.find(p => p.id == id);
                if (!pengumuman) {
                    alert('Pengumuman tidak ditemukan');
                    return;
                }

                const modal = document.getElementById('detailModal');
                const content = document.getElementById('detailContent');

                const tujuanBadge = pengumuman.tujuan === 'mahasiswa' ? 'bg-blue-100 text-blue-800' :
                    pengumuman.tujuan === 'dosen' ? 'bg-green-100 text-green-800' :
                    'bg-purple-100 text-purple-800';

                const createdAt = new Date(pengumuman.created_at).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                content.innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="text-lg font-semibold text-gray-900">${pengumuman.judul}</h4>
                                @role('admin')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${tujuanBadge}">
                                    ${pengumuman.tujuan.charAt(0).toUpperCase() + pengumuman.tujuan.slice(1)}
                                </span>
                                @endrole
                            </div>
                            <p class="text-sm text-gray-500 mb-4">Dipublikasikan: ${createdAt}</p>
                        </div>

                        <div class="prose max-w-none text-sm text-gray-700">
                            ${pengumuman.deskripsi.replace(/\n/g, '<br>')}
                        </div>

                        ${pengumuman.dokumen ? `
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="font-medium text-gray-900 text-sm">Dokumen Pendukung</p>
                                                            <p class="text-sm text-gray-600">${pengumuman.dokumen.split('/').pop()}</p>
                                                        </div>
                                                    </div>
                                                    <a href="/pengumuman/${pengumuman.id}/download" target="_blank"
                                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                        Download
                                                    </a>
                                                </div>
                                            </div>
                                        ` : ''}
                    </div>
                `;

                modal.classList.remove('hidden');
            }

            function closeDetailModal() {
                document.getElementById('detailModal').classList.add('hidden');
            }

            function confirmDelete(id, name) {
                if (confirm(`Yakin ingin menghapus pengumuman "${name}"?`)) {
                    const form = document.getElementById('delete-form');
                    form.action = `/pengumuman/${id}`;
                    form.submit();
                }
            }

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    @role('admin')
                        closeModal();
                    @endrole
                    closeDetailModal();
                }
            });

            // Form validation
            @role('admin')
                document.getElementById('pengumumanForm').addEventListener('submit', function(e) {
                    const requiredFields = ['judul', 'deskripsi', 'tujuan'];

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
                });
            @endrole

            // Make functions globally accessible
            @role('admin')
                window.openModal = openModal;
                window.openEditModal = openEditModal;
                window.closeModal = closeModal;
            @endrole
            window.showDetail = showDetail;
            window.closeDetailModal = closeDetailModal;
            window.confirmDelete = confirmDelete;
        </script>
    @endpush
@endsection
