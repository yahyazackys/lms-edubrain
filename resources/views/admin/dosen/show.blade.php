@extends('layouts.app')

@section('title', 'Detail Dosen - ' . $dosen->pengguna->nama)

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header dengan Info Dosen -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <!-- Info Dosen -->
                        <div class="flex items-center space-x-4 font-heading">
                            <!-- Avatar dengan Upload Feature -->
                            <div class="flex-shrink-0 relative group">
                                <!-- Foto Profile -->
                                <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center relative cursor-pointer overflow-hidden"
                                    onclick="openPhotoUpload()">
                                    @if ($dosen->foto)
                                        <img src="{{ asset('storage/foto-dosen/' . $dosen->foto) }}"
                                            alt="{{ $dosen->pengguna->nama }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xl font-bold text-gray-700">
                                            {{ strtoupper(substr($dosen->pengguna->nama, 0, 2)) }}
                                        </span>
                                    @endif

                                    <!-- Hover Overlay -->
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center">
                                        <span
                                            class="text-white text-xs font-medium opacity-0 group-hover:opacity-100 transition-all duration-200">
                                            Ubah
                                        </span>
                                    </div>
                                </div>

                                <!-- Camera Icon -->
                                <div class="absolute -bottom-1 -right-1 bg-gray-700 rounded-full p-1.5 border-2 border-white z-10 cursor-pointer"
                                    onclick="openPhotoUpload()">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z">
                                        </path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Detail -->
                            <div class="flex-1 min-w-0">
                                <h1 class="text-xl font-semibold font-heading text-gray-900 truncate">
                                    @if ($dosen->gelar_depan || $dosen->gelar_belakang)
                                        {{ trim($dosen->gelar_depan . ' ' . $dosen->pengguna->nama . ' ' . $dosen->gelar_belakang) }}
                                    @else
                                        {{ $dosen->pengguna->nama }}
                                    @endif
                                </h1>
                                <div class="flex sm:flex-row flex-col items-start sm:items-center gap-x-4 gap-y-1">
                                    <div class="flex items-center text-sm text-gray-600 gap-1">
                                        <i class="fa-solid fa-circle text-[8px]"></i>
                                        NIDN: {{ $dosen->nidn }}
                                    </div>
                                    @if ($dosen->programStudi)
                                        <div class="flex items-center text-sm text-gray-600 gap-1">
                                            <i class="fa-solid fa-circle text-[8px]"></i>
                                            {{ $dosen->programStudi->nama_program_studi }}
                                        </div>
                                    @endif
                                    <div>
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
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $statusDosenOptions[$dosen->status_dosen] }}
                                        </span>
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
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $kepegawaianClass }}">
                                            {{ $statusKepegawaianOptions[$dosen->status_kepegawaian] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 lg:mt-0">
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('dosen.index') }}"
                                    class="w-full lg:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Kembali ke Daftar
                                </a>
                                <div class="flex gap-3 w-full lg:w-auto">
                                    <!-- Edit Mode Toggle -->
                                    <button id="toggleEditBtn" onclick="toggleEditMode()"
                                        class="flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        <span id="toggleEditText">Ubah Data</span>
                                    </button>

                                    <button onclick="resetPassword('{{ $dosen->id_dosen }}')"
                                        class="flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2 bg-yellow-600 text-white text-xs font-medium rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2h-6m-4 0H7a2 2 0 00-2 2v8a2 2 0 002 2h4m4-10V7a2 2 0 00-2-2H7a2 2 0 00-2 2v4">
                                            </path>
                                        </svg>
                                        Reset Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation dan Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 font-heading">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 overflow-hidden">
                    <!-- Mobile Tab Navigation -->
                    <div class="block sm:hidden">
                        <div class="relative">
                            <div id="mobile-tabs" class="flex space-x-1 px-4 overflow-x-auto scrollbar-hide touch-pan-x"
                                style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch;">
                                <button onclick="switchTab('data-utama')"
                                    class="tab-button active whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-data-utama">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Data Utama
                                </button>
                                <button onclick="switchTab('data-pribadi')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-data-pribadi">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Data Pribadi
                                </button>
                                <button onclick="switchTab('akun-pengguna')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-akun-pengguna">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    Akun Pengguna
                                </button>
                                <button onclick="switchTab('pembimbing-akademik')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-pembimbing-akademik">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z">
                                        </path>
                                    </svg>
                                    Pembimbing Akademik
                                </button>
                            </div>

                            <!-- Scroll Indicators -->
                            <div id="scroll-left"
                                class="absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-r from-white to-transparent pointer-events-none opacity-0 transition-opacity duration-200">
                            </div>
                            <div id="scroll-right"
                                class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-white to-transparent pointer-events-none transition-opacity duration-200">
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Tab Navigation -->
                    <nav class="hidden sm:flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="switchTab('data-utama')"
                            class="tab-button active whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-data-utama-desktop">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Data Utama
                        </button>
                        <button onclick="switchTab('data-pribadi')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-data-pribadi-desktop">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Data Pribadi
                        </button>
                        <button onclick="switchTab('akun-pengguna')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-akun-pengguna-desktop">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Akun Pengguna
                        </button>
                        <button onclick="switchTab('pembimbing-akademik')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-pembimbing-akademik-desktop">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z">
                                </path>
                            </svg>
                            Pembimbing Akademik
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <form id="detailForm" method="POST" action="{{ route('dosen.update-detail', $dosen->id_dosen) }}">
                    @csrf
                    @method('PUT')

                    <!-- Tab 1: Data Utama -->
                    <div id="content-data-utama" class="tab-content active">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Data Utama Dosen</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- NIDN -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        NIDN <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nidn" id="nidn" value="{{ $dosen->nidn }}"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        maxlength="20" required disabled>
                                </div>

                                <!-- Nama -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nama" id="nama"
                                        value="{{ $dosen->pengguna->nama }}"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        maxlength="150" required disabled>
                                </div>

                                <!-- Jenis Kelamin -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Jenis Kelamin <span class="text-red-500">*</span>
                                    </label>
                                    <select name="jenis_kelamin" id="jenis_kelamin"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        required disabled>
                                        <option value="">Pilih jenis kelamin</option>
                                        <option value="L" {{ $dosen->jenis_kelamin === 'L' ? 'selected' : '' }}>
                                            Laki-laki
                                        </option>
                                        <option value="P" {{ $dosen->jenis_kelamin === 'P' ? 'selected' : '' }}>
                                            Perempuan
                                        </option>
                                    </select>
                                </div>

                                <!-- Program Studi -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Program Studi
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="program_studi_search"
                                            value="@if ($dosen->programStudi) {{ $dosen->programStudi->kode_program_studi }} - {{ $dosen->programStudi->jenjang->kode_jenjang_pendidikan }} {{ $dosen->programStudi->nama_program_studi }} @endif"
                                            placeholder="Cari program studi..." autocomplete="off"
                                            class="form-input w-full text-xs px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            disabled>

                                        <button type="button" id="clear_program_studi"
                                            onclick="clearProgramStudiSelection()"
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 disabled:opacity-50 @if (!$dosen->programStudi) hidden @endif"
                                            disabled>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6m0 12L6 6"></path>
                                            </svg>
                                        </button>

                                        <input type="hidden" name="id_program_studi" id="id_program_studi"
                                            value="{{ $dosen->id_program_studi }}">

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

                                <!-- Status Dosen -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Status Dosen <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status_dosen" id="status_dosen"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        required disabled>
                                        @foreach ($statusDosenOptions as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ $dosen->status_dosen === $key ? 'selected' : '' }}>
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
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        required disabled>
                                        @foreach ($statusKepegawaianOptions as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ $dosen->status_kepegawaian === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Data Pribadi -->
                    <div id="content-data-pribadi" class="tab-content hidden">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Data Pribadi</h3>
                            </div>

                            <!-- Biodata -->
                            <div class="mb-8">
                                <h4 class="text-sm font-medium text-gray-900 mb-4">Biodata</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- Gelar Depan -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Gelar Depan</label>
                                        <input type="text" name="gelar_depan" id="gelar_depan"
                                            value="{{ $dosen->gelar_depan }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="50" placeholder="Contoh: Dr." disabled>
                                    </div>

                                    <!-- Gelar Belakang -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Gelar Belakang</label>
                                        <input type="text" name="gelar_belakang" id="gelar_belakang"
                                            value="{{ $dosen->gelar_belakang }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="50" placeholder="Contoh: M.T., Ph.D" disabled>
                                    </div>

                                    <!-- Tempat Lahir -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" id="tempat_lahir"
                                            value="{{ $dosen->tempat_lahir }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Jakarta" disabled>
                                    </div>

                                    <!-- Tanggal Lahir -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                                        <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                            value="{{ $dosen->tanggal_lahir }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            disabled>
                                    </div>

                                    <!-- NIK -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">NIK (16 digit)</label>
                                        <input type="text" name="nik" id="nik" value="{{ $dosen->nik }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="16" minlength="16" pattern="[0-9]{16}"
                                            placeholder="Contoh: 1234567890123456" disabled>
                                    </div>

                                    <!-- NPWP -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">NPWP</label>
                                        <input type="text" name="npwp" id="npwp" value="{{ $dosen->npwp }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="20" placeholder="Contoh: 123456789012345" disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-4">Alamat</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- Jalan -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Alamat Jalan</label>
                                        <input type="text" name="jalan" id="jalan" value="{{ $dosen->jalan }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="255" placeholder="Contoh: Jl. Sudirman No. 123" disabled>
                                    </div>

                                    <!-- Dusun -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Dusun</label>
                                        <input type="text" name="dusun" id="dusun" value="{{ $dosen->dusun }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Kebon Jeruk" disabled>
                                    </div>

                                    <!-- RT -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">RT</label>
                                        <input type="text" name="rt" id="rt" value="{{ $dosen->rt }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="3" placeholder="Contoh: 001" disabled>
                                    </div>

                                    <!-- RW -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">RW</label>
                                        <input type="text" name="rw" id="rw" value="{{ $dosen->rw }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="3" placeholder="Contoh: 002" disabled>
                                    </div>

                                    <!-- Kelurahan -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Kelurahan</label>
                                        <input type="text" name="kelurahan" id="kelurahan"
                                            value="{{ $dosen->kelurahan }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Kebon Jeruk" disabled>
                                    </div>

                                    <!-- Kode Pos -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Kode Pos</label>
                                        <input type="text" name="kode_pos" id="kode_pos"
                                            value="{{ $dosen->kode_pos }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="10" placeholder="Contoh: 12345" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Akun Pengguna -->
                    <div id="content-akun-pengguna" class="tab-content hidden">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Akun Pengguna</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Info Login -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                            </path>
                                        </svg>
                                        Informasi Login
                                    </h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Username</label>
                                            <div class="text-xs text-gray-900 bg-white p-2 rounded border">
                                                {{ $dosen->pengguna->username }}</div>
                                            <p class="text-xs text-gray-500 mt-1">Username tidak dapat diubah (mengikuti
                                                NIDN)</p>
                                        </div>
                                        <div class="flex gap-4">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Role</label>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    {{ ucfirst($dosen->pengguna->role) }}
                                                </span>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Status
                                                    Akun</label>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dosen->pengguna->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $dosen->pengguna->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kontak -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Data Kontak
                                    </h4>
                                    <div class="space-y-4">
                                        <!-- Email -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-2">Email</label>
                                            <input type="email" name="email" id="email"
                                                value="{{ $dosen->pengguna->email }}"
                                                class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                                placeholder="Contoh: dr.john@university.ac.id" disabled>
                                        </div>

                                        <!-- No HP -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-2">No HP</label>
                                            <input type="tel" name="no_hp" id="no_hp"
                                                value="{{ $dosen->pengguna->no_hp }}"
                                                class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                                placeholder="Contoh: 081234567890" maxlength="20" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Reset Info -->
                            <div class="mt-6 bg-yellow-50 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-yellow-400 mt-0.5 mr-3 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.966-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-yellow-800 mb-1">Reset Password</h4>
                                        <p class="text-xs text-yellow-700 mb-2">
                                            Untuk mereset password dosen, gunakan tombol "Reset Password" di bagian atas
                                            halaman.
                                            Password akan direset sesuai dengan NIDN yang aktif.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Pembimbing Akademik -->
                    <div id="content-pembimbing-akademik" class="tab-content hidden">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Pembimbing Akademik</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Total Kuota PA -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Total Kuota PA <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="total_kuota_pa" id="total_kuota_pa"
                                        value="{{ $dosen->total_kuota_pa }}"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        min="0" max="100" placeholder="Contoh: 25" disabled>
                                    <p class="text-xs text-gray-500 mt-1">Maksimal jumlah mahasiswa yang dapat dibimbing
                                        sebagai PA. Kosongkan atau isi 0 jika dosen tidak dapat menjadi PA.</p>
                                </div>
                            </div>

                            <!-- Info Box -->
                            <div class="mt-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-blue-800 mb-1">Informasi Kuota PA</h4>
                                        <p class="text-xs text-blue-700">
                                            Kuota PA menentukan berapa banyak mahasiswa yang dapat dibimbing oleh dosen ini
                                            sebagai Pembimbing Akademik.
                                            Jika diisi 0 atau kosong, dosen tidak dapat ditugaskan sebagai PA.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div id="form-actions"
                        class="bg-gray-50 px-4 sm:px-6 py-4 border-t border-gray-200 flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 hidden">
                        <button type="button" onclick="cancelEditMode()"
                            class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 text-xs font-medium rounded-lg border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 transition-colors duration-200">
                            Batal
                        </button>
                        <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Update Data Dosen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Forms -->
    <form id="reset-password-form" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Photo Upload Modal -->
    <div id="photo-upload-modal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[9999] hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="font-heading">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Ubah Foto Profil</h3>
                </div>

                <form id="photo-upload-form" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Foto</label>
                        <input type="file" id="photo-input" name="photo" accept="image/*"
                            class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG. Maksimal: 2MB</p>
                    </div>

                    <!-- Preview -->
                    <div id="photo-preview" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                        <img id="preview-image"
                            class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-gray-200">
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closePhotoUpload()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }

            .tab-button.active {
                @apply border-gray-900 text-gray-900;
            }

            .tab-button:not(.active) {
                @apply border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300;
            }

            .tab-content {
                display: none;
            }

            .tab-content.active {
                display: block;
            }

            /* Mobile tab animation */
            #mobile-tabs {
                scroll-snap-type: x mandatory;
            }

            #mobile-tabs button {
                scroll-snap-align: start;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Global variables
            let programStudiData = [];
            let searchTimeout;
            let isEditMode = false;

            // Load data saat halaman dimuat
            document.addEventListener('DOMContentLoaded', function() {
                loadProgramStudiData();
                setupProgramStudiSearch();
                setupMobileTabs();
                setupPhotoUpload();

                switchTab('data-utama');
            });

            // Setup mobile tabs
            function setupMobileTabs() {
                const mobileTabsContainer = document.getElementById('mobile-tabs');
                const scrollLeft = document.getElementById('scroll-left');
                const scrollRight = document.getElementById('scroll-right');

                if (mobileTabsContainer) {
                    // Update scroll indicators
                    function updateScrollIndicators() {
                        const canScrollLeft = mobileTabsContainer.scrollLeft > 0;
                        const canScrollRight = mobileTabsContainer.scrollLeft <
                            (mobileTabsContainer.scrollWidth - mobileTabsContainer.clientWidth);

                        if (scrollLeft) scrollLeft.style.opacity = canScrollLeft ? '1' : '0';
                        if (scrollRight) scrollRight.style.opacity = canScrollRight ? '1' : '0';
                    }

                    mobileTabsContainer.addEventListener('scroll', updateScrollIndicators);
                    window.addEventListener('resize', updateScrollIndicators);
                    updateScrollIndicators();

                    // Touch gestures for better mobile UX
                    let startX = 0;
                    let scrollLeftStart = 0;

                    mobileTabsContainer.addEventListener('touchstart', function(e) {
                        startX = e.touches[0].pageX;
                        scrollLeftStart = this.scrollLeft;
                    });

                    mobileTabsContainer.addEventListener('touchmove', function(e) {
                        e.preventDefault();
                        const x = e.touches[0].pageX;
                        const walk = (x - startX) * 2;
                        this.scrollLeft = scrollLeftStart - walk;
                    });
                }
            }

            // Setup photo upload
            function setupPhotoUpload() {
                const photoInput = document.getElementById('photo-input');
                const previewDiv = document.getElementById('photo-preview');
                const previewImage = document.getElementById('preview-image');
                const form = document.getElementById('photo-upload-form');

                if (photoInput) {
                    photoInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewImage.src = e.target.result;
                                previewDiv.classList.remove('hidden');
                            };
                            reader.readAsDataURL(file);
                        } else {
                            previewDiv.classList.add('hidden');
                        }
                    });
                }

                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        uploadPhoto();
                    });
                }
            }

            // Photo upload functions
            function openPhotoUpload() {
                document.getElementById('photo-upload-modal').classList.remove('hidden');
            }

            function closePhotoUpload() {
                document.getElementById('photo-upload-modal').classList.add('hidden');
                document.getElementById('photo-input').value = '';
                document.getElementById('photo-preview').classList.add('hidden');
            }

            function uploadPhoto() {
                const form = document.getElementById('photo-upload-form');
                const formData = new FormData(form);

                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Uploading...';
                submitBtn.disabled = true;

                fetch(`/dosen/{{ $dosen->id_dosen }}/upload-photo`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error uploading photo: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error uploading photo');
                    })
                    .finally(() => {
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                        closePhotoUpload();
                    });
            }

            // Edit mode functions
            function toggleEditMode() {
                isEditMode = !isEditMode;
                const toggleBtn = document.getElementById('toggleEditBtn');
                const toggleText = document.getElementById('toggleEditText');
                const formActions = document.getElementById('form-actions');
                const formInputs = document.querySelectorAll('.form-input');

                if (isEditMode) {
                    // Enable edit mode
                    toggleBtn.className =
                        'flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200';
                    toggleText.textContent = 'Batal Ubah Data';
                    formActions.classList.remove('hidden');
                    formActions.classList.add('flex');

                    formInputs.forEach(input => {
                        input.disabled = false;
                        input.classList.remove('disabled:bg-gray-50', 'disabled:text-gray-500');
                    });

                    // Enable program studi search button
                    const clearBtn = document.getElementById('clear_program_studi');
                    if (clearBtn) clearBtn.disabled = false;

                } else {
                    // Disable edit mode
                    toggleBtn.className =
                        'flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200';
                    toggleText.textContent = 'Ubah Data';
                    formActions.classList.add('hidden');

                    formInputs.forEach(input => {
                        input.disabled = true;
                        input.classList.add('disabled:bg-gray-50', 'disabled:text-gray-500');
                    });

                    // Disable program studi search button
                    const clearBtn = document.getElementById('clear_program_studi');
                    if (clearBtn) clearBtn.disabled = true;
                }
            }

            function cancelEditMode() {
                location.reload();
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

            // Tab switching functionality
            function switchTab(tabName) {
                // Hide all tab contents
                const tabContents = document.querySelectorAll('.tab-content');
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                    content.classList.remove('active');
                });

                // Remove active class from all tab buttons
                const tabButtons = document.querySelectorAll('.tab-button');
                tabButtons.forEach(button => {
                    button.classList.remove('active', 'border-gray-900', 'text-gray-900');
                    button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700',
                        'hover:border-gray-300');
                });

                // Show selected tab content
                const selectedContent = document.getElementById(`content-${tabName}`);
                if (selectedContent) {
                    selectedContent.classList.remove('hidden');
                    selectedContent.classList.add('active');
                }

                // Add active class to selected tab buttons
                const mobileButton = document.getElementById(`tab-${tabName}`);
                const desktopButton = document.getElementById(`tab-${tabName}-desktop`);

                [mobileButton, desktopButton].forEach(button => {
                    if (button) {
                        button.classList.add('active', 'border-gray-900', 'text-gray-900');
                        button.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700',
                            'hover:border-gray-300');
                    }
                });

                // Scroll mobile tab into view
                if (mobileButton && window.innerWidth < 640) {
                    mobileButton.scrollIntoView({
                        behavior: 'smooth',
                        inline: 'center',
                        block: 'nearest'
                    });
                }
            }

            // Reset password function
            function resetPassword(dosenId) {
                if (confirm(
                        'Yakin ingin mereset password dosen ini?\n\nPassword akan direset sesuai dengan NIDN yang aktif.')) {
                    const form = document.getElementById('reset-password-form');
                    form.action = `/dosen/${dosenId}/reset-password`;
                    form.submit();
                }
            }

            // Program Studi Search Functions
            function setupProgramStudiSearch() {
                const searchInput = document.getElementById('program_studi_search');
                const hiddenInput = document.getElementById('id_program_studi');
                const dropdown = document.getElementById('program_studi_dropdown');
                const loadingIndicator = document.getElementById('search_loading');

                if (!searchInput) return;

                // Input event
                searchInput.addEventListener('input', function() {
                    if (this.disabled) return;

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
                searchInput.addEventListener('focus', function() {
                    if (this.disabled) return;

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
                    if (!searchInput.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.classList.add('hidden');
                    }
                });

                updateClearButton();
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
            document.getElementById('detailForm').addEventListener('submit', function(e) {
                const requiredFields = ['nidn', 'nama', 'jenis_kelamin', 'status_dosen', 'status_kepegawaian'];

                for (let fieldName of requiredFields) {
                    const field = document.getElementById(fieldName);
                    if (!field || !field.value.trim()) {
                        e.preventDefault();

                        // Switch to appropriate tab
                        if (requiredFields.includes(fieldName)) {
                            switchTab('data-utama');
                        }

                        alert(
                            `Field ${field ? field.previousElementSibling.textContent.replace(' *', '') : fieldName} tidak boleh kosong`
                        );
                        if (field) field.focus();
                        return false;
                    }
                }

                // Validate NIK if filled
                const nikField = document.getElementById('nik');
                if (nikField && nikField.value && nikField.value.length !== 16) {
                    e.preventDefault();
                    alert('NIK harus 16 digit');
                    nikField.focus();
                    switchTab('data-pribadi');
                    return false;
                }
            });

            // Make functions global for onclick handlers
            window.switchTab = switchTab;
            window.resetPassword = resetPassword;
            window.selectProgramStudi = selectProgramStudi;
            window.clearProgramStudiSelection = clearProgramStudiSelection;
            window.toggleEditMode = toggleEditMode;
            window.cancelEditMode = cancelEditMode;
            window.openPhotoUpload = openPhotoUpload;
            window.closePhotoUpload = closePhotoUpload;
        </script>
    @endpush
@endsection
