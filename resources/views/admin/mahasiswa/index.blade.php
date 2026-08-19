@extends('layouts.app')

@section('title', 'Manajemen Mahasiswa')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Manajemen Mahasiswa</h1>
                            <p class="text-sm text-gray-600">Kelola data mahasiswa dan akun pengguna</p>
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
                                <a href="{{ route('mahasiswa.export') }}"
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
                            <a href="{{ route('mahasiswa.export-template') }}"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Template Excel
                            </a>

                            <!-- Add Student Button -->
                            <button onclick="openModal('create')"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Mahasiswa
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" placeholder="Cari NIM atau nama mahasiswa..."
                                    class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Program Studi Filter -->
                        <div>
                            <select id="programStudiFilter"
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua Program Studi</option>
                                @foreach ($programStudis as $prodi)
                                    <option value="{{ $prodi->id_program_studi }}">
                                        {{ $prodi->nama_program_studi }}
                                        ({{ $prodi->jenjang->kode_jenjang_pendidikan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Angkatan Filter -->
                        <div>
                            <select id="angkatanFilter"
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua Angkatan</option>
                                @foreach ($angkatans as $angkatan)
                                    <option value="{{ $angkatan }}">{{ $angkatan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <select id="statusFilter"
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua Status</option>
                                @foreach ($statusOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
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
                                    NIM / Nama
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Program Studi
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kurikulum
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Angkatan
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="mahasiswaTableBody">
                            @forelse($mahasiswas as $mahasiswa)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 mahasiswa-row" data-searchable
                                    data-nim="{{ $mahasiswa->nim }}" data-nama="{{ $mahasiswa->pengguna->nama }}"
                                    data-program-studi="{{ $mahasiswa->id_program_studi }}"
                                    data-angkatan="{{ $mahasiswa->angkatan }}"
                                    data-status="{{ $mahasiswa->status_mahasiswa }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ strtoupper(substr($mahasiswa->pengguna->nama, 0, 2)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4 searchable-content">
                                                <div class="text-xs font-medium text-gray-900 searchable-nim">
                                                    {{ $mahasiswa->nim }}</div>
                                                <div class="text-xs text-gray-500 searchable-nama">
                                                    {{ $mahasiswa->pengguna->nama }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs text-gray-900">
                                            {{ $mahasiswa->programStudi->nama_program_studi }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $mahasiswa->programStudi->jenjang->nama_jenjang_pendidikan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs text-gray-900">
                                            {{ $mahasiswa->kurikulum->nama_kurikulum ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $mahasiswa->angkatan }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = match ($mahasiswa->status_mahasiswa) {
                                                'AKTIF' => 'bg-green-100 text-green-800',
                                                'CUTI' => 'bg-yellow-100 text-yellow-800',
                                                'DO' => 'bg-red-100 text-red-800',
                                                'KELUAR' => 'bg-gray-100 text-gray-800',
                                                'LULUS' => 'bg-purple-100 text-purple-800',
                                                'NONAKTIF' => 'bg-orange-100 text-orange-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $statusOptions[$mahasiswa->status_mahasiswa] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <!-- Detail Button -->
                                            <div class="relative group">
                                                <a href="{{ route('mahasiswa.show', $mahasiswa->id_mahasiswa) }}"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                    Detail Mahasiswa
                                                    <div
                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Edit Button -->
                                            <div class="relative group">
                                                <button
                                                    onclick="openEditModal(
                                                    '{{ $mahasiswa->id_mahasiswa }}',
                                                    '{{ addslashes($mahasiswa->nim) }}',
                                                    '{{ addslashes($mahasiswa->pengguna->nama) }}',
                                                    '{{ $mahasiswa->id_program_studi }}',
                                                    '{{ $mahasiswa->id_kurikulum }}',
                                                    '{{ $mahasiswa->angkatan }}',
                                                    '{{ $mahasiswa->status_mahasiswa }}'
                                                )"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                    Edit Data Wajib
                                                    <div
                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Delete Button -->
                                            <div class="relative group">
                                                <button
                                                    onclick="confirmDelete('{{ $mahasiswa->id_mahasiswa }}', '{{ addslashes($mahasiswa->pengguna->nama) }}', '{{ $mahasiswa->nim }}')"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                    Hapus Mahasiswa
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
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 mb-2">Belum ada mahasiswa</p>
                                            <p class="text-xs text-gray-500">Tambahkan mahasiswa baru atau import dari
                                                Excel</p>
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

        <!-- Create/Edit Modal -->
        <div id="mahasiswaModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
                    <form id="mahasiswaForm" method="POST" action="{{ route('mahasiswa.store') }}">
                        @csrf

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 id="modalTitle" class="text-lg font-heading font-semibold text-gray-900">
                                    Tambah Mahasiswa
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
                                <!-- NIM -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        NIM <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nim" id="nim"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="20230001" maxlength="20" required>
                                </div>

                                <!-- Nama -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nama" id="nama"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="John Doe" maxlength="150" required>
                                </div>

                                <!-- Program Studi -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Program Studi <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="program_studi_search"
                                            placeholder="Cari berdasarkan kode atau nama program studi..."
                                            autocomplete="off"
                                            class="w-full text-xs px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">

                                        <!-- Clear Button -->
                                        <button type="button" id="clear_program_studi"
                                            onclick="clearProgramStudiSelection()"
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6m0 12L6 6"></path>
                                            </svg>
                                        </button>

                                        <input type="hidden" name="id_program_studi" id="id_program_studi" required>

                                        <!-- Dropdown -->
                                        <div id="program_studi_dropdown"
                                            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                        </div>

                                        <!-- Loading -->
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

                                <!-- Kurikulum -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Kurikulum <span class="text-red-500">*</span>
                                    </label>
                                    <select name="id_kurikulum" id="id_kurikulum"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        required>
                                        <option value="">Pilih kurikulum...</option>
                                    </select>
                                </div>

                                <!-- Angkatan -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Angkatan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="angkatan" id="angkatan" min="2000"
                                        max="{{ date('Y') + 1 }}" value="{{ date('Y') }}"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        required>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Status Mahasiswa <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status_mahasiswa" id="status_mahasiswa"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        required>
                                        @foreach ($statusOptions as $key => $label)
                                            <option value="{{ $key }}" {{ $key === 'AKTIF' ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Info Akun -->
                            <div class="mt-4 bg-blue-50 p-3 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-blue-400 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-xs text-blue-700">
                                        Akun pengguna akan dibuat otomatis dengan username dan password = NIM
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

        <!-- Import Modal -->
        <div id="importModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeImportModal()">
                </div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">
                    <form id="importForm" method="POST" action="{{ route('mahasiswa.import') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-heading font-semibold text-gray-900">Import Mahasiswa</h3>
                                <button type="button" onclick="closeImportModal()"
                                    class="sm:hidden text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <!-- File Input -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        File Excel <span class="text-red-500">*</span>
                                    </label>
                                    <input type="file" name="file" id="import_file" accept=".xlsx,.xls"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        required>
                                    <p class="text-xs text-gray-500 mt-1">Format: .xlsx atau .xls (max 2MB)</p>
                                </div>

                                <!-- Info -->
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
                                                <li>• Username dan password otomatis = NIM</li>
                                                <li>• Kode Program Studi harus sesuai dengan data di sistem</li>
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

    <!-- Hidden Forms -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            // Global variables
            let programStudiData = [];
            let kurikulumData = [];
            let searchTimeout;

            // Load data saat halaman dimuat
            document.addEventListener('DOMContentLoaded', function() {
                loadProgramStudiData();
                setupRealtimeFilters();
            });

            // Setup realtime filters
            function setupRealtimeFilters() {
                // Search input
                document.getElementById('searchInput').addEventListener('input', function() {
                    filterTable();
                });

                // Filter dropdowns
                document.getElementById('programStudiFilter').addEventListener('change', function() {
                    filterTable();
                });

                document.getElementById('angkatanFilter').addEventListener('change', function() {
                    filterTable();
                });

                document.getElementById('statusFilter').addEventListener('change', function() {
                    filterTable();
                });
            }

            // Filter table function
            function filterTable() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                const programStudiFilter = document.getElementById('programStudiFilter').value;
                const angkatanFilter = document.getElementById('angkatanFilter').value;
                const statusFilter = document.getElementById('statusFilter').value;

                const tbody = document.getElementById('mahasiswaTableBody');
                const rows = tbody.querySelectorAll('tr[data-searchable]');
                let visibleCount = 0;

                rows.forEach(row => {
                    // Reset pagination attribute
                    row.removeAttribute('data-pagination-hidden');

                    const nim = row.getAttribute('data-nim').toLowerCase();
                    const nama = row.getAttribute('data-nama').toLowerCase();
                    const programStudi = row.getAttribute('data-program-studi');
                    const angkatan = row.getAttribute('data-angkatan');
                    const status = row.getAttribute('data-status');

                    // Check search match (NIM or nama)
                    const matchesSearch = searchTerm === '' ||
                        nim.includes(searchTerm) ||
                        nama.includes(searchTerm);

                    // Check filter matches
                    const matchesProgramStudi = programStudiFilter === '' || programStudi === programStudiFilter;
                    const matchesAngkatan = angkatanFilter === '' || angkatan === angkatanFilter;
                    const matchesStatus = statusFilter === '' || status === statusFilter;

                    const isMatch = matchesSearch && matchesProgramStudi && matchesAngkatan && matchesStatus;

                    if (isMatch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateEmptyState(visibleCount, searchTerm, programStudiFilter, angkatanFilter, statusFilter);

                // Update pagination setelah filter
                if (window.pagination) {
                    window.pagination.onFilterChange();
                }
            }

            // Update empty state
            function updateEmptyState(visibleCount, searchTerm, programStudiFilter, angkatanFilter, statusFilter) {
                const tbody = document.getElementById('mahasiswaTableBody');
                let emptyRow = tbody.querySelector('tr[data-empty-search]');

                if (emptyRow) {
                    emptyRow.remove();
                }

                if (visibleCount === 0 && (searchTerm !== '' || programStudiFilter !== '' || angkatanFilter !== '' ||
                        statusFilter !== '')) {
                    emptyRow = document.createElement('tr');
                    emptyRow.setAttribute('data-empty-search', 'true');

                    let message = 'Tidak ada mahasiswa ditemukan';
                    let detail = 'dengan kriteria filter yang dipilih';

                    emptyRow.innerHTML = `
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-900 mb-2">${message}</p>
                                <p class="text-xs text-gray-500">${detail}</p>
                                <button onclick="clearAllFilters()" class="mt-3 text-xs text-blue-600 hover:text-blue-800 underline">
                                    Reset semua filter
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                }
            }

            // Clear all filters
            function clearAllFilters() {
                document.getElementById('searchInput').value = '';
                document.getElementById('programStudiFilter').value = '';
                document.getElementById('angkatanFilter').value = '';
                document.getElementById('statusFilter').value = '';
                filterTable();
            }

            // Load program studi data
            function loadProgramStudiData() {
                fetch('/mahasiswa/api/program-studi')
                    .then(response => response.json())
                    .then(data => {
                        programStudiData = data;
                    })
                    .catch(error => {
                        console.error('Error loading program studi:', error);
                    });
            }

            // Load kurikulum berdasarkan program studi
            function loadKurikulumData(programStudiId, selectedKurikulumId = null) {
                if (!programStudiId) {
                    document.getElementById('id_kurikulum').innerHTML = '<option value="">Pilih kurikulum...</option>';
                    return;
                }

                fetch(`/mahasiswa/api/kurikulum?program_studi_id=${programStudiId}`)
                    .then(response => response.json())
                    .then(data => {
                        const kurikulumSelect = document.getElementById('id_kurikulum');
                        kurikulumSelect.innerHTML = '<option value="">Pilih kurikulum...</option>';

                        data.forEach(kurikulum => {
                            const isSelected = selectedKurikulumId && kurikulum.id_kurikulum ===
                                selectedKurikulumId ? 'selected' : '';
                            kurikulumSelect.innerHTML +=
                                `<option value="${kurikulum.id_kurikulum}" ${isSelected}>${kurikulum.display_name}</option>`;
                        });
                    })
                    .catch(error => {
                        console.error('Error loading kurikulum:', error);
                        document.getElementById('id_kurikulum').innerHTML =
                            '<option value="">Error loading kurikulum...</option>';
                    });
            }

            // Modal functions
            function openModal(type) {
                const modal = document.getElementById('mahasiswaModal');
                const form = document.getElementById('mahasiswaForm');
                const modalTitle = document.getElementById('modalTitle');
                const submitText = document.getElementById('submitText');

                form.reset();
                clearProgramStudiSelection();

                if (type === 'create') {
                    form.action = '{{ route('mahasiswa.store') }}';
                    form.querySelector('input[name="_method"]')?.remove();
                    modalTitle.textContent = 'Tambah Mahasiswa';
                    submitText.textContent = 'Simpan';
                    document.getElementById('angkatan').value = '{{ date('Y') }}';
                }

                modal.classList.remove('hidden');

                setTimeout(() => {
                    setupProgramStudiSearch();
                    document.getElementById('nim').focus();
                }, 100);
            }

            function openEditModal(id, nim, nama, programStudiId, kurikulumId, angkatan, status) {
                const modal = document.getElementById('mahasiswaModal');
                const form = document.getElementById('mahasiswaForm');
                const modalTitle = document.getElementById('modalTitle');
                const submitText = document.getElementById('submitText');

                form.action = `/mahasiswa/${id}`;

                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';

                // Set values
                document.getElementById('nim').value = nim;
                document.getElementById('nama').value = nama;
                document.getElementById('angkatan').value = angkatan;
                document.getElementById('status_mahasiswa').value = status;

                modalTitle.textContent = 'Edit Mahasiswa';
                submitText.textContent = 'Update';

                modal.classList.remove('hidden');

                setTimeout(() => {
                    setupProgramStudiSearch();

                    // Set program studi
                    if (programStudiId && programStudiData.length > 0) {
                        const programStudi = programStudiData.find(p => p.id_program_studi === programStudiId);
                        if (programStudi) {
                            document.getElementById('program_studi_search').value =
                                `${programStudi.kode_program_studi} - ${programStudi.kode_jenjang} ${programStudi.nama_program_studi}`;
                            document.getElementById('id_program_studi').value = programStudiId;
                            updateClearButton();

                            // Load kurikulum dan set selected
                            loadKurikulumData(programStudiId, kurikulumId);
                        }
                    }

                    document.getElementById('nim').focus();
                }, 100);
            }

            function closeModal() {
                document.getElementById('mahasiswaModal').classList.add('hidden');
            }

            // Import modal functions
            function openImportModal() {
                document.getElementById('importModal').classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('import_file').focus();
                }, 100);
            }

            function closeImportModal() {
                document.getElementById('importModal').classList.add('hidden');
                document.getElementById('importForm').reset();
            }

            // Error modal functions
            function closeErrorModal() {
                const modal = document.getElementById('import-error-modal');
                if (modal) {
                    modal.style.display = 'none';
                }
            }

            // Show error modal if exists
            document.addEventListener('DOMContentLoaded', function() {
                const errorModal = document.getElementById('import-error-modal');
                if (errorModal) {
                    errorModal.style.display = 'block';
                }
            });

            // Delete function
            function confirmDelete(id, nama, nim) {
                if (confirm(
                        `Yakin ingin menghapus mahasiswa "${nama}" (NIM: ${nim})?\n\nData mahasiswa dan akun pengguna akan dihapus permanen.`
                    )) {
                    const form = document.getElementById('delete-form');
                    form.action = `/mahasiswa/${id}`;
                    form.submit();
                }
            }

            // Program Studi Search Functions
            function setupProgramStudiSearch() {
                const searchInput = document.getElementById('program_studi_search');
                const hiddenInput = document.getElementById('id_program_studi');
                const dropdown = document.getElementById('program_studi_dropdown');
                const loadingIndicator = document.getElementById('search_loading');
                const clearButton = document.getElementById('clear_program_studi');

                if (!searchInput) return;

                // Remove existing listeners
                const newSearchInput = searchInput.cloneNode(true);
                searchInput.parentNode.replaceChild(newSearchInput, searchInput);

                // Input event
                newSearchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();

                    if (searchTimeout) clearTimeout(searchTimeout);
                    updateClearButton();
                    loadingIndicator.classList.remove('hidden');

                    searchTimeout = setTimeout(() => {
                        searchProgramStudi(query);
                        loadingIndicator.classList.add('hidden');
                    }, 300);
                });

                // Focus event
                newSearchInput.addEventListener('focus', function() {
                    const currentValue = hiddenInput.value;
                    if (currentValue && this.value !== '') {
                        const query = this.value.toLowerCase().trim();
                        searchProgramStudi(query);
                    } else {
                        showAllProgramStudi();
                    }
                });

                // Click outside to close
                document.addEventListener('click', function(event) {
                    if (!newSearchInput.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }

            function searchProgramStudi(query) {
                if (query === '') {
                    showAllProgramStudi();
                    return;
                }

                const filteredData = programStudiData.filter(item => {
                    const kodeMatch = item.kode_program_studi && item.kode_program_studi.toLowerCase().includes(query);
                    const namaMatch = item.nama_program_studi && item.nama_program_studi.toLowerCase().includes(query);
                    const jenjangMatch = item.jenjang && item.jenjang.toLowerCase().includes(query);
                    return kodeMatch || namaMatch || jenjangMatch;
                });

                displaySearchResults(filteredData);
            }

            function showAllProgramStudi() {
                displaySearchResults(programStudiData);
            }

            function displaySearchResults(data) {
                const dropdown = document.getElementById('program_studi_dropdown');

                if (data.length === 0) {
                    dropdown.innerHTML = `
                        <div class="px-4 py-3 text-center text-xs text-gray-500">
                            <svg class="w-6 h-6 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <div>Tidak ada program studi ditemukan</div>
                        </div>
                    `;
                } else {
                    dropdown.innerHTML = data.map(item => `
                        <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                             onclick="selectProgramStudi('${item.id_program_studi}', '${escapeHtml(item.kode_program_studi || '')}', '${escapeHtml(item.nama_program_studi)}', '${escapeHtml(item.jenjang)}', '${escapeHtml(item.kode_jenjang)}')">
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

            function selectProgramStudi(id, kode, nama, jenjang, kodeJenjang) {
                const searchInput = document.getElementById('program_studi_search');
                const hiddenInput = document.getElementById('id_program_studi');
                const dropdown = document.getElementById('program_studi_dropdown');

                searchInput.value = `${kode} - ${kodeJenjang} ${nama}`;
                hiddenInput.value = id;

                updateClearButton();
                dropdown.classList.add('hidden');

                // Load kurikulum untuk program studi yang dipilih
                loadKurikulumData(id);

                // Visual feedback
                searchInput.classList.add('border-green-300', 'bg-green-50');
                setTimeout(() => {
                    searchInput.classList.remove('border-green-300', 'bg-green-50');
                }, 1000);
            }

            function clearProgramStudiSelection() {
                const searchInput = document.getElementById('program_studi_search');
                const hiddenInput = document.getElementById('id_program_studi');
                const dropdown = document.getElementById('program_studi_dropdown');
                const clearButton = document.getElementById('clear_program_studi');
                const kurikulumSelect = document.getElementById('id_kurikulum');

                if (searchInput) searchInput.value = '';
                if (hiddenInput) hiddenInput.value = '';
                if (dropdown) dropdown.classList.add('hidden');
                if (clearButton) clearButton.classList.add('hidden');
                if (kurikulumSelect) kurikulumSelect.innerHTML = '<option value="">Pilih kurikulum...</option>';

                if (searchInput) searchInput.focus();
            }

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

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Form validation
            document.getElementById('mahasiswaForm').addEventListener('submit', function(e) {
                const requiredFields = ['nim', 'nama', 'id_program_studi', 'id_kurikulum', 'angkatan',
                    'status_mahasiswa'
                ];

                for (let fieldName of requiredFields) {
                    const field = document.getElementById(fieldName);
                    if (!field.value.trim()) {
                        e.preventDefault();
                        alert(`Field ${field.previousElementSibling.textContent.replace(' *', '')} tidak boleh kosong`);
                        field.focus();
                        return false;
                    }
                }
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeModal();
                    closeImportModal();
                    closeErrorModal();
                }
            });

            // ============================================
            // CLIENT-SIDE PAGINATION FUNCTIONALITY
            // ============================================

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

                    setTimeout(() => {
                        this.updatePagination();
                    }, 100);
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
                }, 200);
            });

            // Make functions global
            window.openModal = openModal;
            window.openEditModal = openEditModal;
            window.closeModal = closeModal;
            window.openImportModal = openImportModal;
            window.closeImportModal = closeImportModal;
            window.confirmDelete = confirmDelete;
            window.selectProgramStudi = selectProgramStudi;
            window.clearProgramStudiSelection = clearProgramStudiSelection;
            window.clearAllFilters = clearAllFilters;
            window.closeErrorModal = closeErrorModal;
        </script>
    @endpush
@endsection
