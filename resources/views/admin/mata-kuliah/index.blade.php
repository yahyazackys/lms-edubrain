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

                        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row sm:space-x-3 space-y-3 sm:space-y-0">
                            <!-- Import & Export Container -->
                            <div class="flex space-x-3">
                                <!-- Import Excel Button -->
                                <button onclick="openImportModal()"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                        </path>
                                    </svg>
                                    Import Excel
                                </button>

                                <!-- Export Excel Button -->
                                <a href="{{ route('mata-kuliah.export') }}"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Export Excel
                                </a>
                            </div>

                            <!-- Download Template Button -->
                            <a href="{{ route('mata-kuliah.export-template') }}"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Template Excel
                            </a>

                            <!-- Add Button -->
                            <button onclick="openModal('create')"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
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
                        <div class="max-md:w-full">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" placeholder="Cari kode/nama mata kuliah..."
                                    class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[280px]">
                            </div>
                        </div>

                        <!-- Filter SKS dan Jenis -->
                        <div class=" items-center space-x-3 hidden md:flex">
                            <select id="sksFilter"
                                class="text-xs py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua SKS</option>
                                @foreach ($sksOptions as $sks)
                                    <option value="{{ $sks }}">{{ $sks }} SKS</option>
                                @endforeach
                            </select>
                            <select id="jenisFilter"
                                class="text-xs py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua Jenis</option>
                                @foreach ($jenisOptions as $jenis)
                                    <option value="{{ $jenis }}">{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3 md:hidden">
                            <select id="sksFilter"
                                class="w-full text-xs px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua SKS</option>
                                @foreach ($sksOptions as $sks)
                                    <option value="{{ $sks }}">{{ $sks }} SKS</option>
                                @endforeach
                            </select>

                            <select id="jenisFilter"
                                class="w-full text-xs px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua Jenis</option>
                                @foreach ($jenisOptions as $jenis)
                                    <option value="{{ $jenis }}">{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="mataKuliahTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Kode Mata Kuliah
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Nama Mata Kuliah
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    SKS
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    Jenis Mata Kuliah
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
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
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $jenisClass = match ($mataKuliah->jenis_mata_kuliah) {
                                                'TEORI' => 'bg-blue-100 text-blue-800',
                                                'PRAKTIKUM' => 'bg-green-100 text-green-800',
                                                'MAGANG' => 'bg-purple-100 text-purple-800',
                                                'KKN' => 'bg-yellow-100 text-yellow-800',
                                                'SKRIPSI' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $jenisClass }}">
                                            {{ $mataKuliah->jenis_mata_kuliah }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <div class="relative group">
                                                <button
                                                    onclick="openEditModal(
                                                    '{{ $mataKuliah->id_mata_kuliah }}',
                                                    '{{ addslashes($mataKuliah->kode_mata_kuliah) }}',
                                                    '{{ addslashes($mataKuliah->nama_mata_kuliah) }}',
                                                    '{{ $mataKuliah->sks_mata_kuliah }}',
                                                    '{{ $mataKuliah->jenis_mata_kuliah }}'
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
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
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

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Nama Mata Kuliah <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nama_mata_kuliah" id="nama_mata_kuliah"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Contoh: Matematika Diskrit" maxlength="150" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Jenis Mata Kuliah <span class="text-red-500">*</span>
                                    </label>
                                    <select name="jenis_mata_kuliah" id="jenis_mata_kuliah"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="TEORI" selected>Teori</option>
                                        <option value="PRAKTIKUM">Praktikum</option>
                                        <option value="MAGANG">Magang</option>
                                        <option value="KKN">KKN</option>
                                        <option value="SKRIPSI">Skripsi</option>
                                    </select>
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

        <!-- Import Modal -->
        <div id="importModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeImportModal()">
                </div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">
                    <form id="importForm" method="POST" action="{{ route('mata-kuliah.import') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-heading font-semibold text-gray-900">Import Mata Kuliah</h3>
                                <button type="button" onclick="closeImportModal()"
                                    class="sm:hidden text-gray-400 hover:text-gray-600">
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
                                    <p class="text-xs text-gray-500 mt-1">Format: .xlsx atau .xls (max 2MB)</p>
                                </div>

                                <div class="bg-yellow-50 p-3 rounded-lg">
                                    <div class="flex">
                                        <svg class="w-4 h-4 text-yellow-400 mt-0.5 mr-2 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.966-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                            </path>
                                        </svg>
                                        <div>
                                            <p class="text-xs font-medium text-yellow-800 mb-1">Penting!</p>
                                            <ul class="text-xs text-yellow-700 space-y-1">
                                                <li>• Download template Excel terlebih dahulu</li>
                                                <li>• Isi data sesuai format yang ada di template</li>
                                                <li>• Kode mata kuliah harus unik (huruf kapital & angka)</li>
                                                <li>• Data yang error akan dilewati dan dilaporkan</li>
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

        <!-- Import Error Modal -->
        @if (session('import_errors'))
            <div id="import-error-modal" class="fixed inset-0 z-[9999] overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeErrorModal()">
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
                                    <h3 class="text-lg font-heading font-semibold text-gray-900">
                                        Detail Error Import
                                    </h3>
                                </div>
                                <button type="button" onclick="closeErrorModal()"
                                    class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="max-h-96 overflow-y-auto">
                                <div class="space-y-2">
                                    @foreach (session('import_errors') as $error)
                                        <div class="flex items-start p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-sm text-yellow-800">{{ $error }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 sm:px-6 py-4 flex justify-end">
                            <button type="button" onclick="closeErrorModal()"
                                class="inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            // REALTIME SEARCH & FILTER
            document.getElementById('searchInput').addEventListener('input', function() {
                filterTable();
            });

            document.getElementById('sksFilter').addEventListener('change', function() {
                filterTable();
            });

            document.getElementById('jenisFilter').addEventListener('change', function() {
                filterTable();
            });

            function filterTable() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                const sksFilter = document.getElementById('sksFilter').value;
                const jenisFilter = document.getElementById('jenisFilter').value;
                const tbody = document.getElementById('mataKuliahTableBody');
                const rows = tbody.querySelectorAll('tr[data-searchable]');
                let visibleCount = 0;

                rows.forEach(row => {
                    row.removeAttribute('data-pagination-hidden');

                    const kodeCell = row.querySelector('.searchable-kode');
                    const namaCell = row.querySelector('.searchable-nama');
                    const rowSks = row.getAttribute('data-sks');
                    const rowJenis = row.getAttribute('data-jenis');

                    const kodeText = kodeCell ? kodeCell.textContent.toLowerCase() : '';
                    const namaText = namaCell ? namaCell.textContent.toLowerCase() : '';

                    const matchesSearch = searchTerm === '' ||
                        kodeText.includes(searchTerm) ||
                        namaText.includes(searchTerm);
                    const matchesSks = sksFilter === '' || rowSks === sksFilter;
                    const matchesJenis = jenisFilter === '' || rowJenis === jenisFilter;

                    const isMatch = matchesSearch && matchesSks && matchesJenis;

                    if (isMatch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateEmptyState(visibleCount, searchTerm, sksFilte, jenisFilter);

                if (window.pagination) {
                    window.pagination.onFilterChange();
                }
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

            // MODAL FUNCTIONS
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
                setTimeout(() => document.getElementById('kode_mata_kuliah').focus(), 100);
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

                document.getElementById('kode_mata_kuliah').value = kode || '';
                document.getElementById('nama_mata_kuliah').value = nama || '';
                document.getElementById('sks_mata_kuliah').value = sks || '';
                document.getElementById('jenis_mata_kuliah').value = jenis || 'TEORI';

                modalTitle.textContent = 'Edit Mata Kuliah';
                submitText.textContent = 'Update';

                modal.classList.remove('hidden');
                setTimeout(() => document.getElementById('kode_mata_kuliah').focus(), 100);
            }

            function closeModal() {
                document.getElementById('mataKuliahModal').classList.add('hidden');
            }

            function openImportModal() {
                document.getElementById('importModal').classList.remove('hidden');
            }

            function closeImportModal() {
                document.getElementById('importModal').classList.add('hidden');
                document.getElementById('importForm').reset();
            }

            function closeErrorModal() {
                const modal = document.getElementById('import-error-modal');
                if (modal) modal.style.display = 'none';
            }

            function confirmDelete(id, name) {
                if (confirm(`Yakin ingin menghapus mata kuliah "${name}"?`)) {
                    const form = document.getElementById('delete-form');
                    form.action = `/mata-kuliah/${id}`;
                    form.submit();
                }
            }

            // Show error modal if exists
            document.addEventListener('DOMContentLoaded', function() {
                const errorModal = document.getElementById('import-error-modal');
                if (errorModal) errorModal.style.display = 'block';
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeModal();
                    closeImportModal();
                    closeErrorModal();
                }
            });

            document.getElementById('kode_mata_kuliah').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });

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

                const kode = document.getElementById('kode_mata_kuliah').value.trim();
                if (!/^[A-Z0-9]+$/.test(kode)) {
                    e.preventDefault();
                    alert('Kode mata kuliah hanya boleh berisi huruf kapital dan angka');
                    document.getElementById('kode_mata_kuliah').focus();
                    return false;
                }
            });

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
                    const tbody = document.getElementById('mataKuliahTableBody');
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
                        if (startPage > 2) container.appendChild(this.createPaginationButton('...', false));
                    }

                    for (let i = startPage; i <= endPage; i++) {
                        const isActive = i === this.currentPage;
                        container.appendChild(this.createPaginationButton(i.toString(), true, () => {
                            this.currentPage = i;
                            this.updatePagination();
                        }, isActive));
                    }

                    if (endPage < totalPages) {
                        if (endPage < totalPages - 1) container.appendChild(this.createPaginationButton('...', false));
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

            let pagination;
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    pagination = new ClientPagination();
                    window.pagination = pagination;
                }, 200);
            });

            window.openModal = openModal;
            window.openEditModal = openEditModal;
            window.closeModal = closeModal;
            window.openImportModal = openImportModal;
            window.closeImportModal = closeImportModal;
            window.confirmDelete = confirmDelete;
            window.closeErrorModal = closeErrorModal;
        </script>
    @endpush
@endsection
