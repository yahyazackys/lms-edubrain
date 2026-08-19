@extends('layouts.app')

@section('title', 'Manajemen Kurikulum')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col">
                            <h1 class="text-lg font-semibold font-heading text-gray-900">Manajemen Kurikulum</h1>
                            <p class="text-sm text-gray-600">Kelola kurikulum berdasarkan program studi dan semester berlaku
                            </p>
                        </div>

                        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
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
                                <a href="{{ route('kurikulum.export') }}"
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
                            <a href="{{ route('kurikulum.export-template') }}"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Template Excel
                            </a>

                            <button onclick="openModal('create')"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Kurikulum
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter & Search -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <!-- Mobile Layout -->
                    <div class="flex flex-col space-y-3 lg:hidden">
                        <!-- Search - Full width -->
                        <div class="w-full">
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
                        </div>

                        <!-- Filters - 50/50 -->
                        <div class="grid grid-cols-2 gap-3">
                            <select id="semesterFilter-mobile"
                                class="w-full text-xs px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua Semester</option>
                                @foreach ($usedSemesters as $semester)
                                    <option value="{{ $semester->id_semester }}">{{ $semester->nama_semester }}</option>
                                @endforeach
                            </select>

                            <select id="programStudiFilter-mobile"
                                class="w-full text-xs px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua Prodi</option>
                                @foreach ($programStudis as $prodi)
                                    <option value="{{ $prodi->id_program_studi }}">
                                        {{ $prodi->nama_program_studi }} ({{ $prodi->jenjang->kode_jenjang_pendidikan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Desktop Layout -->
                    <div class="hidden lg:flex lg:items-center lg:justify-between">
                        <div class="flex items-center space-x-3">
                            <label class="text-xs font-medium text-gray-700">Semester berlaku:</label>
                            <select id="semesterFilter-desktop"
                                class="text-xs px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[200px]">
                                <option value="">Semua Semester</option>
                                @foreach ($usedSemesters as $semester)
                                    <option value="{{ $semester->id_semester }}">{{ $semester->nama_semester }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="searchInput-desktop" placeholder="Cari nama kurikulum..."
                                    class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            </div>

                            <select id="programStudiFilter-desktop"
                                class="text-xs py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua Program Studi</option>
                                @foreach ($programStudis as $prodi)
                                    <option value="{{ $prodi->id_program_studi }}">
                                        {{ $prodi->nama_program_studi }} ({{ $prodi->jenjang->kode_jenjang_pendidikan }})
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
                    <table class="min-w-full divide-y divide-gray-200" id="kurikulumTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Kurikulum
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Program Studi
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Semester Berlaku
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    SKS Lulus
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    MKWUUPT
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    MKWU
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    MKWPS
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    MKP
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="kurikulumTableBody">
                            @forelse($kurikulums as $kurikulum)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 kurikulum-row" data-searchable
                                    data-program-studi="{{ $kurikulum->id_program_studi }}"
                                    data-semester="{{ $kurikulum->id_semester }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="searchable-nama">
                                            <div class="text-xs font-medium text-gray-900">
                                                {{ $kurikulum->nama_kurikulum }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs text-gray-900">
                                            {{ $kurikulum->programStudi->nama_program_studi }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $kurikulum->programStudi->jenjang->nama_jenjang_pendidikan }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $kurikulum->semester->nama_semester }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $kurikulum->jumlah_sks_lulus }} SKS
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            {{ $kurikulum->sks_mkwuupt_minimal }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                                            {{ $kurikulum->sks_mkwu_minimal }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $kurikulum->sks_mkwps_minimal }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ $kurikulum->sks_mkp_minimal }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
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

                                            <div class="relative group">
                                                <button
                                                    onclick="openEditModal(
                                                        '{{ $kurikulum->id_kurikulum }}',
                                                        '{{ addslashes($kurikulum->nama_kurikulum) }}',
                                                        '{{ $kurikulum->jumlah_sks_lulus }}',
                                                        '{{ $kurikulum->sks_mkwuupt_minimal }}',
                                                        '{{ $kurikulum->sks_mkwu_minimal }}',
                                                        '{{ $kurikulum->sks_mkwps_minimal }}',
                                                        '{{ $kurikulum->sks_mkp_minimal }}',
                                                        '{{ $kurikulum->id_program_studi }}',
                                                        '{{ $kurikulum->id_semester }}'
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
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 mb-2">Belum ada kurikulum</p>
                                            <p class="text-xs text-gray-500">Tambahkan kurikulum baru atau import dari
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
        <div id="kurikulumModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-4xl mx-auto">
                    <form id="kurikulumForm" method="POST" action="{{ route('kurikulum.store') }}">
                        @csrf

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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Nama Kurikulum -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Nama Kurikulum <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nama_kurikulum" id="nama_kurikulum"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Contoh: Kurikulum 2023" maxlength="100" required>
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

                                        <div id="program_studi_dropdown"
                                            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                        </div>

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

                                <!-- Semester Berlaku -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Semester Berlaku <span class="text-red-500">*</span>
                                    </label>
                                    <select name="id_semester" id="id_semester" required
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                        <option value="">Pilih Semester</option>
                                        @foreach ($allSemesters as $semester)
                                            <option value="{{ $semester->id_semester }}">{{ $semester->nama_semester }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- SKS Lulus -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        SKS Lulus <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="jumlah_sks_lulus" id="jumlah_sks_lulus" min="1"
                                        max="200"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="144" required>
                                </div>

                                <!-- SKS Categories -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        SKS MKWUUPT Minimal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="sks_mkwuupt_minimal" id="sks_mkwuupt_minimal"
                                        min="0" max="200"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="8" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        SKS MKWU Minimal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="sks_mkwu_minimal" id="sks_mkwu_minimal" min="0"
                                        max="200"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="12" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        SKS MKWPS Minimal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="sks_mkwps_minimal" id="sks_mkwps_minimal" min="0"
                                        max="200"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="108" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        SKS MKP Minimal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="sks_mkp_minimal" id="sks_mkp_minimal" min="0"
                                        max="200"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="16" required>
                                </div>
                            </div>

                            <!-- SKS Validation Info -->
                            <div class="mt-4 bg-amber-50 p-3 rounded-lg border border-amber-200">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-amber-500 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                    <span class="text-xs text-amber-800">
                                        <strong>Penting:</strong> Total SKS dari semua kategori (MKWUUPT + MKWU + MKWPS +
                                        MKP) harus sama persis dengan SKS Lulus
                                    </span>
                                </div>
                                <div class="mt-2 text-xs text-amber-700">
                                    Total SKS saat ini: <span id="currentTotal" class="font-medium">0</span> /
                                    SKS Lulus: <span id="sksLulusDisplay" class="font-medium">0</span>
                                    <span id="difference" class="ml-2"></span>
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
                    <form id="importForm" method="POST" action="{{ route('kurikulum.import') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-heading font-semibold text-gray-900">Import Kurikulum</h3>
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
                                                <li>• Total SKS kategori HARUS sama dengan SKS Lulus</li>
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
                                    <h3 class="text-lg font-heading font-semibold text-gray-900">Detail Error Import</h3>
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
            // Global variables
            let programStudiData = [];
            let searchTimeout;

            document.addEventListener('DOMContentLoaded', function() {
                loadProgramStudiData();
                setupSKSCalculator();
                setupFilters();
            });

            // Setup filters with sync between mobile and desktop
            function setupFilters() {
                const searchMobile = document.getElementById('searchInput');
                const searchDesktop = document.getElementById('searchInput-desktop');
                const semesterMobile = document.getElementById('semesterFilter-mobile');
                const semesterDesktop = document.getElementById('semesterFilter-desktop');
                const prodiMobile = document.getElementById('programStudiFilter-mobile');
                const prodiDesktop = document.getElementById('programStudiFilter-desktop');

                // Sync search inputs
                if (searchMobile && searchDesktop) {
                    searchMobile.addEventListener('input', function() {
                        searchDesktop.value = this.value;
                        filterTable();
                    });
                    searchDesktop.addEventListener('input', function() {
                        searchMobile.value = this.value;
                        filterTable();
                    });
                }

                // Sync semester filters
                if (semesterMobile && semesterDesktop) {
                    semesterMobile.addEventListener('change', function() {
                        semesterDesktop.value = this.value;
                        filterTable();
                    });
                    semesterDesktop.addEventListener('change', function() {
                        semesterMobile.value = this.value;
                        filterTable();
                    });
                }

                // Sync prodi filters
                if (prodiMobile && prodiDesktop) {
                    prodiMobile.addEventListener('change', function() {
                        prodiDesktop.value = this.value;
                        filterTable();
                    });
                    prodiDesktop.addEventListener('change', function() {
                        prodiMobile.value = this.value;
                        filterTable();
                    });
                }
            }

            function filterTable() {
                const searchTerm = (document.getElementById('searchInput')?.value || document.getElementById(
                    'searchInput-desktop')?.value || '').toLowerCase().trim();
                const semesterFilter = document.getElementById('semesterFilter-mobile')?.value || document.getElementById(
                    'semesterFilter-desktop')?.value || '';
                const prodiFilter = document.getElementById('programStudiFilter-mobile')?.value || document.getElementById(
                    'programStudiFilter-desktop')?.value || '';

                const tbody = document.getElementById('kurikulumTableBody');
                const rows = tbody.querySelectorAll('tr[data-searchable]');
                let visibleCount = 0;

                rows.forEach(row => {
                    row.removeAttribute('data-pagination-hidden');

                    const namaCell = row.querySelector('.searchable-nama');
                    const rowSemester = row.getAttribute('data-semester');
                    const rowProdi = row.getAttribute('data-program-studi');

                    const namaText = namaCell ? namaCell.textContent.toLowerCase() : '';

                    const matchesSearch = searchTerm === '' || namaText.includes(searchTerm);
                    const matchesSemester = semesterFilter === '' || rowSemester === semesterFilter;
                    const matchesProdi = prodiFilter === '' || rowProdi === prodiFilter;

                    const isMatch = matchesSearch && matchesSemester && matchesProdi;

                    if (isMatch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateEmptyState(visibleCount, searchTerm, semesterFilter, prodiFilter);

                if (window.pagination) {
                    window.pagination.onFilterChange();
                }
            }

            function updateEmptyState(visibleCount, searchTerm, semesterFilter, prodiFilter) {
                const tbody = document.getElementById('kurikulumTableBody');
                let emptyRow = tbody.querySelector('tr[data-empty-search]');

                if (emptyRow) {
                    emptyRow.remove();
                }

                if (visibleCount === 0 && (searchTerm !== '' || semesterFilter !== '' || prodiFilter !== '')) {
                    emptyRow = document.createElement('tr');
                    emptyRow.setAttribute('data-empty-search', 'true');

                    emptyRow.innerHTML = `
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-900 mb-2">Tidak ada hasil ditemukan</p>
                                <p class="text-xs text-gray-500">dengan kriteria filter yang dipilih</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                }
            }

            function setupSKSCalculator() {
                const sksInputs = ['jumlah_sks_lulus', 'sks_mkwuupt_minimal', 'sks_mkwu_minimal', 'sks_mkwps_minimal',
                    'sks_mkp_minimal'
                ];
                sksInputs.forEach(inputId => {
                    const input = document.getElementById(inputId);
                    if (input) {
                        input.addEventListener('input', updateSKSCalculation);
                    }
                });
            }

            function updateSKSCalculation() {
                const sksLulus = parseInt(document.getElementById('jumlah_sks_lulus').value) || 0;
                const sksMKWUUPT = parseInt(document.getElementById('sks_mkwuupt_minimal').value) || 0;
                const sksMKWU = parseInt(document.getElementById('sks_mkwu_minimal').value) || 0;
                const sksMKWPS = parseInt(document.getElementById('sks_mkwps_minimal').value) || 0;
                const sksMKP = parseInt(document.getElementById('sks_mkp_minimal').value) || 0;
                const totalSks = sksMKWUUPT + sksMKWU + sksMKWPS + sksMKP;

                document.getElementById('currentTotal').textContent = totalSks;
                document.getElementById('sksLulusDisplay').textContent = sksLulus;

                const differenceEl = document.getElementById('difference');
                const difference = totalSks - sksLulus;

                if (difference === 0 && totalSks > 0) {
                    differenceEl.innerHTML = '<span class="text-green-600 font-medium">✓ Sesuai</span>';
                } else if (difference > 0) {
                    differenceEl.innerHTML = `<span class="text-red-600 font-medium">Kelebihan ${difference} SKS</span>`;
                } else if (difference < 0) {
                    differenceEl.innerHTML = `<span class="text-red-600 font-medium">Kurang ${Math.abs(difference)} SKS</span>`;
                } else {
                    differenceEl.innerHTML = '';
                }
            }

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

            function setupProgramStudiSearch() {
                const searchInput = document.getElementById('program_studi_search');
                const hiddenInput = document.getElementById('id_program_studi');
                const dropdown = document.getElementById('program_studi_dropdown');
                const loadingIndicator = document.getElementById('search_loading');

                if (!searchInput) return;

                const newSearchInput = searchInput.cloneNode(true);
                searchInput.parentNode.replaceChild(newSearchInput, searchInput);

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

                newSearchInput.addEventListener('focus', function() {
                    const currentValue = hiddenInput.value;
                    if (currentValue && this.value !== '') {
                        const query = this.value.toLowerCase().trim();
                        searchProgramStudi(query);
                    } else {
                        showAllProgramStudi();
                    }
                });

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
                    dropdown.innerHTML =
                        '<div class="px-4 py-3 text-center text-xs text-gray-500">Tidak ada program studi ditemukan</div>';
                } else {
                    dropdown.innerHTML = data.map(item => `
                        <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                             onclick="selectProgramStudi('${item.id_program_studi}', '${escapeHtml(item.kode_program_studi || '')}', '${escapeHtml(item.nama_program_studi)}', '${escapeHtml(item.jenjang)}', '${escapeHtml(item.kode_jenjang)}')">
                            <div class="text-xs font-medium text-gray-900">${item.nama_program_studi}</div>
                            <div class="text-xs text-gray-500">${item.kode_program_studi ? `${item.kode_program_studi} • ` : ''}${item.jenjang}</div>
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

                if (searchInput) searchInput.value = '';
                if (hiddenInput) hiddenInput.value = '';
                if (dropdown) dropdown.classList.add('hidden');
                if (clearButton) clearButton.classList.add('hidden');

                if (searchInput) searchInput.focus();
            }

            function updateClearButton() {
                const hiddenInput = document.getElementById('id_program_studi');
                const clearButton = document.getElementById('clear_program_studi');

                if (hiddenInput && clearButton) {
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

            function openModal(type) {
                const modal = document.getElementById('kurikulumModal');
                const form = document.getElementById('kurikulumForm');
                const modalTitle = document.getElementById('modalTitle');
                const submitText = document.getElementById('submitText');

                form.reset();
                clearProgramStudiSelection();
                updateSKSCalculation();

                if (type === 'create') {
                    form.action = '{{ route('kurikulum.store') }}';
                    form.querySelector('input[name="_method"]')?.remove();
                    modalTitle.textContent = 'Tambah Kurikulum';
                    submitText.textContent = 'Simpan';
                }

                modal.classList.remove('hidden');

                setTimeout(() => {
                    setupProgramStudiSearch();
                    updateClearButton();
                    document.getElementById('nama_kurikulum').focus();
                }, 100);
            }

            function openEditModal(id, nama, sksLulus, sksMKWUUPT, sksMKWU, sksMKWPS, sksMKP, programStudiId, semesterId) {
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

                document.getElementById('nama_kurikulum').value = nama || '';
                document.getElementById('jumlah_sks_lulus').value = sksLulus || '';
                document.getElementById('sks_mkwuupt_minimal').value = sksMKWUUPT || '';
                document.getElementById('sks_mkwu_minimal').value = sksMKWU || '';
                document.getElementById('sks_mkwps_minimal').value = sksMKWPS || '';
                document.getElementById('sks_mkp_minimal').value = sksMKP || '';
                document.getElementById('id_semester').value = semesterId || '';

                updateSKSCalculation();

                modalTitle.textContent = 'Edit Kurikulum';
                submitText.textContent = 'Update';

                modal.classList.remove('hidden');

                setTimeout(() => {
                    setupProgramStudiSearch();

                    if (programStudiId && programStudiData.length > 0) {
                        const programStudi = programStudiData.find(p => p.id_program_studi === programStudiId);
                        if (programStudi) {
                            document.getElementById('program_studi_search').value =
                                `${programStudi.kode_program_studi} - ${programStudi.kode_jenjang} ${programStudi.nama_program_studi}`;
                            document.getElementById('id_program_studi').value = programStudiId;
                            updateClearButton();
                        }
                    }

                    document.getElementById('nama_kurikulum').focus();
                }, 100);
            }

            function closeModal() {
                document.getElementById('kurikulumModal').classList.add('hidden');
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
                if (confirm(`Yakin ingin menghapus kurikulum "${name}"?`)) {
                    const form = document.getElementById('delete-form');
                    form.action = `/kurikulum/${id}`;
                    form.submit();
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const errorModal = document.getElementById('import-error-modal');
                if (errorModal) errorModal.style.display = 'block';
            });

            document.getElementById('kurikulumForm').addEventListener('submit', function(e) {
                const requiredFields = ['nama_kurikulum', 'id_program_studi', 'id_semester', 'jumlah_sks_lulus',
                    'sks_mkwuupt_minimal', 'sks_mkwu_minimal', 'sks_mkwps_minimal', 'sks_mkp_minimal'
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

                const sksLulus = parseInt(document.getElementById('jumlah_sks_lulus').value) || 0;
                const sksMKWUUPT = parseInt(document.getElementById('sks_mkwuupt_minimal').value) || 0;
                const sksMKWU = parseInt(document.getElementById('sks_mkwu_minimal').value) || 0;
                const sksMKWPS = parseInt(document.getElementById('sks_mkwps_minimal').value) || 0;
                const sksMKP = parseInt(document.getElementById('sks_mkp_minimal').value) || 0;
                const totalSksMinimal = sksMKWUUPT + sksMKWU + sksMKWPS + sksMKP;

                if (totalSksMinimal !== sksLulus) {
                    e.preventDefault();
                    alert(
                        `Total SKS dari semua kategori harus sama dengan SKS Lulus.\nSaat ini: ${totalSksMinimal} SKS\nSKS Lulus: ${sksLulus} SKS\nSelisih: ${Math.abs(totalSksMinimal - sksLulus)} SKS ${totalSksMinimal > sksLulus ? 'kelebihan' : 'kurang'}`
                    );
                    document.getElementById('jumlah_sks_lulus').focus();
                    return false;
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeModal();
                    closeImportModal();
                    closeErrorModal();
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
                    const tbody = document.getElementById('kurikulumTableBody');
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
            window.selectProgramStudi = selectProgramStudi;
            window.clearProgramStudiSelection = clearProgramStudiSelection;
            window.closeErrorModal = closeErrorModal;
        </script>
    @endpush
@endsection
