@extends('layouts.app')

@section('title', 'Manajemen Dosen')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Manajemen Dosen</h1>
                            <p class="text-sm text-gray-600">Kelola data dosen dan akun pengguna</p>
                        </div>

                        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row sm:space-x-3 space-y-3 sm:space-y-0">
                            <!-- Import & Export Container (Berdampingan di mobile) -->
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

                                <!-- Download Template Button -->
                                <a href="{{ route('dosen.export-template') }}"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Template Excel Neo Feeder
                                </a>
                            </div>

                            <!-- Add Dosen Button (Terpisah, full width di mobile) -->
                            <button onclick="openModal('create')"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Dosen
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" id="searchInput" placeholder="Cari NIDN atau nama dosen..."
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

                        <!-- Status Dosen Filter -->
                        <div>
                            <select id="statusDosenFilter"
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua Status</option>
                                @foreach ($statusDosenOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Kepegawaian Filter -->
                        <div>
                            <select id="statusKepegawaianFilter"
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua Kepegawaian</option>
                                @foreach ($statusKepegawaianOptions as $key => $label)
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
                    <table class="min-w-full divide-y divide-gray-200" id="dosenTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    NIDN / Nama
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Program Studi
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status Dosen
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status Kepegawaian
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="dosenTableBody">
                            @forelse($dosens as $dosen)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 dosen-row" data-searchable
                                    data-nidn="{{ $dosen->nidn }}" data-nama="{{ $dosen->pengguna->nama }}"
                                    data-program-studi="{{ $dosen->id_program_studi }}"
                                    data-status-dosen="{{ $dosen->status_dosen }}"
                                    data-status-kepegawaian="{{ $dosen->status_kepegawaian }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ strtoupper(substr($dosen->pengguna->nama, 0, 2)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4 searchable-content">
                                                <div class="text-xs font-medium text-gray-900 searchable-nidn">
                                                    {{ $dosen->nidn }}</div>
                                                <div class="text-xs text-gray-500 searchable-nama">
                                                    @if ($dosen->gelar_depan || $dosen->gelar_belakang)
                                                        {{ trim($dosen->gelar_depan . ' ' . $dosen->pengguna->nama . ' ' . $dosen->gelar_belakang) }}
                                                    @else
                                                        {{ $dosen->pengguna->nama }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($dosen->programStudi)
                                            <div class="text-xs text-gray-900">
                                                {{ $dosen->programStudi->nama_program_studi }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $dosen->programStudi->jenjang->nama_jenjang_pendidikan }}</div>
                                        @else
                                            <span class="text-xs text-gray-400">Belum ditentukan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = match ($dosen->status_dosen) {
                                                'AKTIF' => 'bg-green-100 text-green-800',
                                                'CUTI' => 'bg-yellow-100 text-yellow-800',
                                                'KELUAR' => 'bg-gray-100 text-gray-800',
                                                'NONAKTIF' => 'bg-orange-100 text-orange-800',
                                                'PENSIUN' => 'bg-purple-100 text-purple-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $statusDosenOptions[$dosen->status_dosen] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $kepegawaianClass = match ($dosen->status_kepegawaian) {
                                                'PNS' => 'bg-blue-100 text-blue-800',
                                                'CPNS' => 'bg-cyan-100 text-cyan-800',
                                                'P3K' => 'bg-teal-100 text-teal-800',
                                                'TETAP' => 'bg-indigo-100 text-indigo-800',
                                                'KONTRAK' => 'bg-amber-100 text-amber-800',
                                                'HONORER' => 'bg-rose-100 text-rose-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $kepegawaianClass }}">
                                            {{ $statusKepegawaianOptions[$dosen->status_kepegawaian] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <!-- Detail Button -->
                                            <div class="relative group">
                                                <a href="{{ route('dosen.show', $dosen->id_dosen) }}"
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
                                                    Detail Dosen
                                                    <div
                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Edit Button -->
                                            <div class="relative group">
                                                <button
                                                    onclick="openEditModal(
                                                    '{{ $dosen->id_dosen }}',
                                                    '{{ addslashes($dosen->nidn) }}',
                                                    '{{ addslashes($dosen->pengguna->nama) }}',
                                                    '{{ $dosen->id_program_studi }}',
                                                    '{{ $dosen->status_dosen }}',
                                                    '{{ $dosen->status_kepegawaian }}',
                                                    '{{ $dosen->jenis_kelamin }}'
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
                                                    onclick="confirmDelete('{{ $dosen->id_dosen }}', '{{ addslashes($dosen->pengguna->nama) }}', '{{ $dosen->nidn }}')"
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
                                                    Hapus Dosen
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
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 mb-2">Belum ada dosen</p>
                                            <p class="text-xs text-gray-500">Tambahkan dosen baru atau import dari Excel
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info -->
                @if ($dosens->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-xs text-gray-700">
                                Menampilkan data dosen (filter realtime)
                            </div>
                            <div class="text-xs text-gray-500">
                                Total: {{ $dosens->total() }} dosen
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div id="dosenModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
                    <form id="dosenForm" method="POST" action="{{ route('dosen.store') }}">
                        @csrf

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 id="modalTitle" class="text-lg font-heading font-semibold text-gray-900">
                                    Tambah Dosen
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
                                <!-- NIDN -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        NIDN <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nidn" id="nidn"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="0123456789" maxlength="20" required>
                                </div>

                                <!-- Nama -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nama" id="nama"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Dr. John Doe" maxlength="150" required>
                                </div>

                                <!-- Jenis Kelamin -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Jenis Kelamin <span class="text-red-500">*</span>
                                    </label>
                                    <select name="jenis_kelamin" id="jenis_kelamin"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        required>
                                        <option value="">Pilih jenis kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>

                                <!-- Program Studi -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Program Studi
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

                                        <input type="hidden" name="id_program_studi" id="id_program_studi">

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

                                <!-- Status Dosen -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Status Dosen <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status_dosen" id="status_dosen"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        required>
                                        @foreach ($statusDosenOptions as $key => $label)
                                            <option value="{{ $key }}" {{ $key === 'AKTIF' ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status Kepegawaian -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Status Kepegawaian <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status_kepegawaian" id="status_kepegawaian"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        required>
                                        @foreach ($statusKepegawaianOptions as $key => $label)
                                            <option value="{{ $key }}" {{ $key === 'TETAP' ? 'selected' : '' }}>
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
                                        Akun pengguna akan dibuat otomatis dengan username dan password = NIDN
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
                    <form id="importForm" method="POST" action="{{ route('dosen.import') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-heading font-semibold text-gray-900">Import Dosen</h3>
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
                                                <li>• Pastikan format file sesuai dengan template Excel</li>
                                                <li>• Username dan password akan dibuat otomatis sesuai NIDN</li>
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

                document.getElementById('statusDosenFilter').addEventListener('change', function() {
                    filterTable();
                });

                document.getElementById('statusKepegawaianFilter').addEventListener('change', function() {
                    filterTable();
                });
            }

            // Filter table function
            function filterTable() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                const programStudiFilter = document.getElementById('programStudiFilter').value;
                const statusDosenFilter = document.getElementById('statusDosenFilter').value;
                const statusKepegawaianFilter = document.getElementById('statusKepegawaianFilter').value;

                const tbody = document.getElementById('dosenTableBody');
                const rows = tbody.querySelectorAll('tr[data-searchable]');
                let visibleCount = 0;

                rows.forEach(row => {
                    const nidn = row.getAttribute('data-nidn').toLowerCase();
                    const nama = row.getAttribute('data-nama').toLowerCase();
                    const programStudi = row.getAttribute('data-program-studi');
                    const statusDosen = row.getAttribute('data-status-dosen');
                    const statusKepegawaian = row.getAttribute('data-status-kepegawaian');

                    // Check search match (NIDN or nama)
                    const matchesSearch = searchTerm === '' ||
                        nidn.includes(searchTerm) ||
                        nama.includes(searchTerm);

                    // Check filter matches
                    const matchesProgramStudi = programStudiFilter === '' || programStudi === programStudiFilter;
                    const matchesStatusDosen = statusDosenFilter === '' || statusDosen === statusDosenFilter;
                    const matchesStatusKepegawaian = statusKepegawaianFilter === '' || statusKepegawaian ===
                        statusKepegawaianFilter;

                    const isMatch = matchesSearch && matchesProgramStudi && matchesStatusDosen &&
                        matchesStatusKepegawaian;

                    if (isMatch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateEmptyState(visibleCount, searchTerm, programStudiFilter, statusDosenFilter, statusKepegawaianFilter);
            }

            // Update empty state
            function updateEmptyState(visibleCount, searchTerm, programStudiFilter, statusDosenFilter,
                statusKepegawaianFilter) {
                const tbody = document.getElementById('dosenTableBody');
                let emptyRow = tbody.querySelector('tr[data-empty-search]');

                if (emptyRow) {
                    emptyRow.remove();
                }

                if (visibleCount === 0 && (searchTerm !== '' || programStudiFilter !== '' || statusDosenFilter !== '' ||
                        statusKepegawaianFilter !== '')) {
                    emptyRow = document.createElement('tr');
                    emptyRow.setAttribute('data-empty-search', 'true');

                    let message = 'Tidak ada dosen ditemukan';
                    let detail = 'dengan kriteria filter yang dipilih';

                    emptyRow.innerHTML = `
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
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
                document.getElementById('statusDosenFilter').value = '';
                document.getElementById('statusKepegawaianFilter').value = '';
                filterTable();
            }

            // Load program studi data
            function loadProgramStudiData() {
                fetch('/dosen/api/program-studi')
                    .then(response => response.json())
                    .then(data => {
                        programStudiData = data;
                    })
                    .catch(error => {
                        console.error('Error loading program studi:', error);
                    });
            }

            // Modal functions
            function openModal(type) {
                const modal = document.getElementById('dosenModal');
                const form = document.getElementById('dosenForm');
                const modalTitle = document.getElementById('modalTitle');
                const submitText = document.getElementById('submitText');

                form.reset();
                clearProgramStudiSelection();

                if (type === 'create') {
                    form.action = '{{ route('dosen.store') }}';
                    form.querySelector('input[name="_method"]')?.remove();
                    modalTitle.textContent = 'Tambah Dosen';
                    submitText.textContent = 'Simpan';
                }

                modal.classList.remove('hidden');

                setTimeout(() => {
                    setupProgramStudiSearch();
                    document.getElementById('nidn').focus();
                }, 100);
            }

            function openEditModal(id, nidn, nama, programStudiId, statusDosen, statusKepegawaian, jenisKelamin) {
                const modal = document.getElementById('dosenModal');
                const form = document.getElementById('dosenForm');
                const modalTitle = document.getElementById('modalTitle');
                const submitText = document.getElementById('submitText');

                form.action = `/dosen/${id}`;

                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';

                // Set values
                document.getElementById('nidn').value = nidn;
                document.getElementById('nama').value = nama;
                document.getElementById('status_dosen').value = statusDosen;
                document.getElementById('status_kepegawaian').value = statusKepegawaian;
                document.getElementById('jenis_kelamin').value = jenisKelamin;

                modalTitle.textContent = 'Edit Dosen';
                submitText.textContent = 'Update';

                modal.classList.remove('hidden');

                setTimeout(() => {
                    setupProgramStudiSearch();

                    // Set program studi if exists
                    if (programStudiId && programStudiData.length > 0) {
                        const programStudi = programStudiData.find(p => p.id_program_studi === programStudiId);
                        if (programStudi) {
                            document.getElementById('program_studi_search').value =
                                `${programStudi.kode_program_studi} - ${programStudi.kode_jenjang} ${programStudi.nama_program_studi}`;
                            document.getElementById('id_program_studi').value = programStudiId;
                            updateClearButton();
                        }
                    }

                    document.getElementById('nidn').focus();
                }, 100);
            }

            function closeModal() {
                document.getElementById('dosenModal').classList.add('hidden');
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

            // Delete function
            function confirmDelete(id, nama, nidn) {
                if (confirm(
                        `Yakin ingin menghapus dosen "${nama}" (NIDN: ${nidn})?\n\nData dosen dan akun pengguna akan dihapus permanen.`
                    )) {
                    const form = document.getElementById('delete-form');
                    form.action = `/dosen/${id}`;
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

                if (searchInput) searchInput.value = '';
                if (hiddenInput) hiddenInput.value = '';
                if (dropdown) dropdown.classList.add('hidden');
                if (clearButton) clearButton.classList.add('hidden');

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
            document.getElementById('dosenForm').addEventListener('submit', function(e) {
                const requiredFields = ['nidn', 'nama', 'jenis_kelamin', 'status_dosen', 'status_kepegawaian'];

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
                }
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
        </script>
    @endpush
@endsection
