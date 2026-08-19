@extends('layouts.app')

@section('title', 'Detail Mahasiswa - ' . $mahasiswa->pengguna->nama)

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header dengan Info Mahasiswa -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <!-- Info Mahasiswa -->
                        <div class="flex items-center space-x-4 font-heading">
                            <!-- Avatar dengan Upload Feature -->
                            <div class="flex-shrink-0 relative group">
                                <!-- Foto Profile -->
                                <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center relative cursor-pointer overflow-hidden"
                                    onclick="openPhotoUpload()">
                                    @if ($mahasiswa->foto)
                                        <img src="{{ asset('storage/foto-mahasiswa/' . $mahasiswa->foto) }}"
                                            alt="{{ $mahasiswa->pengguna->nama }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-2xl font-bold text-gray-700">
                                            {{ strtoupper(substr($mahasiswa->pengguna->nama, 0, 2)) }}
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
                                <div class="absolute -bottom-1 -right-1 bg-gray-600 rounded-full p-2 border-2 border-white z-10 cursor-pointer"
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
                                <h1 class="text-lg font-semibold font-heading text-gray-900 truncate">
                                    {{ $mahasiswa->pengguna->nama }}
                                </h1>
                                <div class="flex sm:flex-row flex-col items-start sm:items-center gap-x-4 gap-y-1">
                                    <div class="flex items-center text-sm text-gray-600 gap-1">
                                        <i class="fa-solid fa-id-card text-gray-500"></i>
                                        {{ $mahasiswa->nim }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600 gap-1">
                                        <i class="fa-solid fa-graduation-cap text-gray-500"></i>
                                        {{ $mahasiswa->programStudi->nama_program_studi }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600 gap-1">
                                        <i class="fa-solid fa-calendar text-gray-500"></i>
                                        Angkatan {{ $mahasiswa->angkatan }}
                                    </div>
                                    <div>
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
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $statusOptions[$mahasiswa->status_mahasiswa] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 lg:mt-0">
                            <div class="flex flex-wrap gap-3">
                                <!-- Container untuk tombol Edit dan Reset yang sejajar di mobile -->
                                <div class="flex gap-3 w-full lg:w-auto">
                                    <!-- Edit Mode Toggle -->
                                    <button id="toggleEditBtn" onclick="toggleEditMode()"
                                        class="flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        <span id="toggleEditText">Ubah Data</span>
                                    </button>

                                    <button onclick="resetPassword('{{ $mahasiswa->id_mahasiswa }}')"
                                        class="flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2 bg-yellow-600 text-white text-xs font-medium rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2h-6m-4 0H7a2 2 0 00-2 2v8a2 2 0 002 2h4m4-10V7a2 2 0 00-2-2H7a2 2 0 00-2 2v4">
                                            </path>
                                        </svg>
                                        Reset Password
                                    </button>
                                </div>

                                <!-- Tombol Kembali -->
                                <a href="{{ route('mahasiswa.index') }}"
                                    class="w-full lg:w-auto inline-flex justify-center items-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Kembali ke Daftar
                                </a>
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
                                    <i class="fas fa-user w-4 h-4 inline mr-2"></i>
                                    Data Utama
                                </button>
                                <button onclick="switchTab('data-pribadi')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-data-pribadi">
                                    <i class="fas fa-id-card w-4 h-4 inline mr-2"></i>
                                    Data Pribadi
                                </button>
                                <button onclick="switchTab('data-orangtua')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-data-orangtua">
                                    <i class="fas fa-users w-4 h-4 inline mr-2"></i>
                                    Data Orangtua
                                </button>
                                <button onclick="switchTab('data-wali')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-data-wali">
                                    <i class="fas fa-user-tie w-4 h-4 inline mr-2"></i>
                                    Data Wali
                                </button>
                                <button onclick="switchTab('akun-pengguna')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-akun-pengguna">
                                    <i class="fas fa-address-book w-4 h-4 inline mr-2"></i>
                                    Akun Pengguna
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Tab Navigation -->
                    <nav class="hidden sm:flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="switchTab('data-utama')"
                            class="tab-button active whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-data-utama-desktop">
                            <i class="fas fa-user w-4 h-4 inline mr-2"></i>
                            Data Utama
                        </button>
                        <button onclick="switchTab('data-pribadi')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-data-pribadi-desktop">
                            <i class="fas fa-id-card w-4 h-4 inline mr-2"></i>
                            Data Pribadi
                        </button>
                        <button onclick="switchTab('data-orangtua')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-data-orangtua-desktop">
                            <i class="fas fa-users w-4 h-4 inline mr-2"></i>
                            Data Orangtua
                        </button>
                        <button onclick="switchTab('data-wali')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-data-wali-desktop">
                            <i class="fas fa-user-tie w-4 h-4 inline mr-2"></i>
                            Data Wali
                        </button>
                        <button onclick="switchTab('akun-pengguna')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-akun-pengguna-desktop">
                            <i class="fas fa-address-book w-4 h-4 inline mr-2"></i>
                            Akun Pengguna
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <form id="detailForm" method="POST"
                    action="{{ route('mahasiswa.update-detail', $mahasiswa->id_mahasiswa) }}">
                    @csrf
                    @method('PUT')

                    <!-- Tab 1: Data Utama -->
                    <div id="content-data-utama" class="tab-content active">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-base font-medium text-gray-900">Data Utama Mahasiswa</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- NIM -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        NIM <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nim" id="nim" value="{{ $mahasiswa->nim }}"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        maxlength="20" required disabled>
                                </div>

                                <!-- Nama -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nama" id="nama"
                                        value="{{ $mahasiswa->pengguna->nama }}"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        maxlength="150" required disabled>
                                </div>

                                <!-- Program Studi -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Program Studi <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="program_studi_search"
                                            value="{{ $mahasiswa->programStudi->kode_program_studi }} - {{ $mahasiswa->programStudi->jenjang->kode_jenjang_pendidikan }} {{ $mahasiswa->programStudi->nama_program_studi }}"
                                            placeholder="Cari program studi..." autocomplete="off"
                                            class="form-input w-full text-xs px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            disabled>

                                        <button type="button" id="clear_program_studi"
                                            onclick="clearProgramStudiSelection()"
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 disabled:opacity-50"
                                            disabled>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6m0 12L6 6"></path>
                                            </svg>
                                        </button>

                                        <input type="hidden" name="id_program_studi" id="id_program_studi"
                                            value="{{ $mahasiswa->id_program_studi }}" required>

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

                                <!-- Kurikulum -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Kurikulum <span class="text-red-500">*</span>
                                    </label>
                                    <select name="id_kurikulum" id="id_kurikulum"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        required disabled>
                                        <option value="">Pilih kurikulum...</option>
                                        @foreach ($kurikulums as $kurikulum)
                                            <option value="{{ $kurikulum->id_kurikulum }}"
                                                {{ $mahasiswa->id_kurikulum == $kurikulum->id_kurikulum ? 'selected' : '' }}>
                                                {{ $kurikulum->nama_kurikulum }}
                                                ({{ $kurikulum->semester->nama_semester }}) -
                                                {{ $kurikulum->jumlah_sks_lulus }} SKS
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Angkatan -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Angkatan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="angkatan" id="angkatan"
                                        value="{{ $mahasiswa->angkatan }}" min="2000" max="{{ date('Y') + 1 }}"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        required disabled>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Status Mahasiswa <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status_mahasiswa" id="status_mahasiswa"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        required disabled>
                                        @foreach ($statusOptions as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ $mahasiswa->status_mahasiswa === $key ? 'selected' : '' }}>
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
                            <h3 class="text-base font-medium text-gray-900 mb-6">Data Pribadi & Alamat</h3>

                            <!-- Biodata -->
                            <div class="mb-8">
                                <h4 class="text-sm font-medium text-gray-900 mb-4">Biodata</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- Jenis Kelamin -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" id="jenis_kelamin"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            disabled>
                                            <option value="">Pilih jenis kelamin</option>
                                            <option value="L"
                                                {{ $mahasiswa->jenis_kelamin === 'L' ? 'selected' : '' }}>
                                                Laki-laki</option>
                                            <option value="P"
                                                {{ $mahasiswa->jenis_kelamin === 'P' ? 'selected' : '' }}>
                                                Perempuan</option>
                                        </select>
                                    </div>

                                    <!-- Tempat Lahir -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" id="tempat_lahir"
                                            value="{{ $mahasiswa->tempat_lahir }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Jakarta" disabled>
                                    </div>

                                    <!-- Tanggal Lahir -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                                        <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                            value="{{ $mahasiswa->tanggal_lahir }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            disabled>
                                    </div>

                                    <!-- NIK -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">NIK (16 digit)</label>
                                        <input type="text" name="nik" id="nik"
                                            value="{{ $mahasiswa->nik }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="16" minlength="16" pattern="[0-9]{16}"
                                            placeholder="Contoh: 1234567890123456" disabled>
                                    </div>

                                    <!-- NISN -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">NISN</label>
                                        <input type="text" name="nisn" id="nisn"
                                            value="{{ $mahasiswa->nisn }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="20" placeholder="Contoh: 1234567890" disabled>
                                    </div>

                                    <!-- NPWP -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">NPWP</label>
                                        <input type="text" name="npwp" id="npwp"
                                            value="{{ $mahasiswa->npwp }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="20" placeholder="Contoh: 123456789012345" disabled>
                                    </div>

                                    <!-- Agama -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Agama</label>
                                        <input type="text" name="agama" id="agama"
                                            value="{{ $mahasiswa->agama }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="50" placeholder="Contoh: Islam" disabled>
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
                                        <input type="text" name="jalan" id="jalan"
                                            value="{{ $mahasiswa->jalan }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="255" placeholder="Contoh: Jl. Sudirman No. 123" disabled>
                                    </div>

                                    <!-- Dusun -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Dusun</label>
                                        <input type="text" name="dusun" id="dusun"
                                            value="{{ $mahasiswa->dusun }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Kebon Jeruk" disabled>
                                    </div>

                                    <!-- RT -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">RT</label>
                                        <input type="text" name="rt" id="rt"
                                            value="{{ $mahasiswa->rt }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="3" placeholder="Contoh: 001" disabled>
                                    </div>

                                    <!-- RW -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">RW</label>
                                        <input type="text" name="rw" id="rw"
                                            value="{{ $mahasiswa->rw }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="3" placeholder="Contoh: 002" disabled>
                                    </div>

                                    <!-- Kelurahan -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Kelurahan</label>
                                        <input type="text" name="kelurahan" id="kelurahan"
                                            value="{{ $mahasiswa->kelurahan }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Kebon Jeruk" disabled>
                                    </div>

                                    <!-- Kode Pos -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Kode Pos</label>
                                        <input type="text" name="kode_pos" id="kode_pos"
                                            value="{{ $mahasiswa->kode_pos }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="10" placeholder="Contoh: 12345" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Data Orangtua -->
                    <div id="content-data-orangtua" class="tab-content hidden">
                        <div class="p-4 sm:p-6">
                            <h3 class="text-base font-medium text-gray-900 mb-6">Data Orangtua</h3>

                            <!-- Data Ayah -->
                            <div class="mb-8">
                                <h4 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-male text-blue-600 mr-2"></i>
                                    Data Ayah
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- NIK Ayah -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">NIK Ayah</label>
                                        <input type="text" name="nik_ayah" value="{{ $mahasiswa->nik_ayah }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="16" minlength="16" pattern="[0-9]{16}"
                                            placeholder="Contoh: 1234567890123456" disabled>
                                    </div>

                                    <!-- Nama Ayah -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Nama Ayah</label>
                                        <input type="text" name="nama_ayah" value="{{ $mahasiswa->nama_ayah }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="150" placeholder="Contoh: Budi Santoso" disabled>
                                    </div>

                                    <!-- Tempat Lahir Ayah -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Tempat Lahir
                                            Ayah</label>
                                        <input type="text" name="tempat_lahir_ayah"
                                            value="{{ $mahasiswa->tempat_lahir_ayah }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Bandung" disabled>
                                    </div>

                                    <!-- Tanggal Lahir Ayah -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Tanggal Lahir
                                            Ayah</label>
                                        <input type="date" name="tanggal_lahir_ayah"
                                            value="{{ $mahasiswa->tanggal_lahir_ayah }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            disabled>
                                    </div>

                                    <!-- Pendidikan Ayah -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Pendidikan Ayah</label>
                                        <input type="text" name="nama_pendidikan_ayah"
                                            value="{{ $mahasiswa->nama_pendidikan_ayah }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: SMA" disabled>
                                    </div>

                                    <!-- Pekerjaan Ayah -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Pekerjaan Ayah</label>
                                        <input type="text" name="nama_pekerjaan_ayah"
                                            value="{{ $mahasiswa->nama_pekerjaan_ayah }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Wiraswasta" disabled>
                                    </div>

                                    <!-- Penghasilan Ayah -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Penghasilan
                                            Ayah</label>
                                        <input type="text" name="nama_penghasilan_ayah" id="penghasilan_ayah"
                                            value="{{ $mahasiswa->nama_penghasilan_ayah }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Rp 5.000.000" disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Ibu -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-female text-pink-600 mr-2"></i>
                                    Data Ibu
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- NIK Ibu -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">NIK Ibu</label>
                                        <input type="text" name="nik_ibu" value="{{ $mahasiswa->nik_ibu }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="16" minlength="16" pattern="[0-9]{16}"
                                            placeholder="Contoh: 1234567890123456" disabled>
                                    </div>

                                    <!-- Nama Ibu -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Nama Ibu</label>
                                        <input type="text" name="nama_ibu" value="{{ $mahasiswa->nama_ibu }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="150" placeholder="Contoh: Siti Aminah" disabled>
                                    </div>

                                    <!-- Tempat Lahir Ibu -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Tempat Lahir
                                            Ibu</label>
                                        <input type="text" name="tempat_lahir_ibu"
                                            value="{{ $mahasiswa->tempat_lahir_ibu }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Surabaya" disabled>
                                    </div>

                                    <!-- Tanggal Lahir Ibu -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Tanggal Lahir
                                            Ibu</label>
                                        <input type="date" name="tanggal_lahir_ibu"
                                            value="{{ $mahasiswa->tanggal_lahir_ibu }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            disabled>
                                    </div>

                                    <!-- Pendidikan Ibu -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Pendidikan Ibu</label>
                                        <input type="text" name="nama_pendidikan_ibu"
                                            value="{{ $mahasiswa->nama_pendidikan_ibu }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: SMA" disabled>
                                    </div>

                                    <!-- Pekerjaan Ibu -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Pekerjaan Ibu</label>
                                        <input type="text" name="nama_pekerjaan_ibu"
                                            value="{{ $mahasiswa->nama_pekerjaan_ibu }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Ibu Rumah Tangga" disabled>
                                    </div>

                                    <!-- Penghasilan Ibu -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Penghasilan Ibu</label>
                                        <input type="text" name="nama_penghasilan_ibu" id="penghasilan_ibu"
                                            value="{{ $mahasiswa->nama_penghasilan_ibu }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Rp 2.000.000" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Data Wali -->
                    <div id="content-data-wali" class="tab-content hidden">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-base font-medium text-gray-900">Data Wali</h3>
                                <p class="text-xs text-gray-500">Data wali (opsional)</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Nama Wali -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Nama Wali</label>
                                    <input type="text" name="nama_wali" value="{{ $mahasiswa->nama_wali }}"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        maxlength="150" placeholder="Contoh: Ahmad Supriyadi" disabled>
                                </div>

                                <!-- Tempat Lahir Wali -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Tempat Lahir Wali</label>
                                    <input type="text" name="tempat_lahir_wali"
                                        value="{{ $mahasiswa->tempat_lahir_wali }}"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        maxlength="100" placeholder="Contoh: Medan" disabled>
                                </div>

                                <!-- Tanggal Lahir Wali -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Tanggal Lahir Wali</label>
                                    <input type="date" name="tanggal_lahir_wali"
                                        value="{{ $mahasiswa->tanggal_lahir_wali }}"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        disabled>
                                </div>

                                <!-- Pendidikan Wali -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Pendidikan Wali</label>
                                    <input type="text" name="nama_pendidikan_wali"
                                        value="{{ $mahasiswa->nama_pendidikan_wali }}"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        maxlength="100" placeholder="Contoh: S1" disabled>
                                </div>

                                <!-- Pekerjaan Wali -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Pekerjaan Wali</label>
                                    <input type="text" name="nama_pekerjaan_wali"
                                        value="{{ $mahasiswa->nama_pekerjaan_wali }}"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        maxlength="100" placeholder="Contoh: PNS" disabled>
                                </div>

                                <!-- Penghasilan Wali -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Penghasilan Wali</label>
                                    <input type="text" name="nama_penghasilan_wali" id="penghasilan_wali"
                                        value="{{ $mahasiswa->nama_penghasilan_wali }}"
                                        class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                        maxlength="100" placeholder="Contoh: Rp 3.500.000" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 5: Akun Pengguna -->
                    <div id="content-akun-pengguna" class="tab-content hidden">
                        <div class="p-4 sm:p-6">
                            <h3 class="text-base font-medium text-gray-900 mb-6">Data Kontak & Akun</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Info Login -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                                        <i class="fas fa-lock text-gray-500 mr-2"></i>
                                        Informasi Login
                                    </h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Username</label>
                                            <div class="text-xs text-gray-900 bg-white p-2 rounded border">
                                                {{ $mahasiswa->pengguna->username }}
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Username tidak dapat diubah (mengikuti
                                                NIM)</p>
                                        </div>
                                        <div class="flex gap-4">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Role</label>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst($mahasiswa->pengguna->role) }}
                                                </span>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Status
                                                    Akun</label>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $mahasiswa->pengguna->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $mahasiswa->pengguna->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Kontak -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                                        <i class="fas fa-address-book text-green-500 mr-2"></i>
                                        Data Kontak
                                    </h4>
                                    <div class="space-y-4">
                                        <!-- Email -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-2">Email</label>
                                            <input type="email" name="email" id="email"
                                                value="{{ $mahasiswa->pengguna->email }}"
                                                class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                                placeholder="Contoh: john.doe@example.com" disabled>
                                        </div>

                                        <!-- No HP -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-2">No HP</label>
                                            <input type="tel" name="no_hp" id="no_hp"
                                                value="{{ $mahasiswa->pengguna->no_hp }}"
                                                class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
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
                                            Untuk mereset password mahasiswa, gunakan tombol "Reset Password" di bagian atas
                                            halaman. Password akan direset sesuai dengan NIM yang aktif.
                                        </p>
                                        <p class="text-xs text-yellow-700">
                                            <strong>Password saat ini:</strong> {{ $mahasiswa->nim }} (sesuai NIM)
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
                            class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 text-xs font-medium rounded-lg border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                            Batal
                        </button>
                        <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Update Data Mahasiswa
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
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Ubah Foto Profil</h3>
                    <button type="button" onclick="closePhotoUpload()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="photo-upload-form" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Foto</label>
                        <input type="file" id="photo-input" name="photo" accept="image/*"
                            class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500">
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
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded-lg hover:bg-gray-700">
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
            let currentTab = 'data-utama';

            // Load data saat halaman dimuat
            document.addEventListener('DOMContentLoaded', function() {
                loadProgramStudiData();
                setupProgramStudiSearch();
                setupMobileTabs();
                setupCurrencyFormatting();
                setupPhotoUpload();
                initializeTab();
            });

            // Utility functions for URL parameters
            function getUrlParameter(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }

            function updateUrlParameter(key, value) {
                const url = new URL(window.location);
                url.searchParams.set(key, value);
                window.history.pushState({
                    path: url.href
                }, '', url.href);
            }

            function isValidTab(tabName) {
                const validTabs = ['data-utama', 'data-pribadi', 'data-orangtua', 'data-wali', 'akun-pengguna'];
                return validTabs.includes(tabName);
            }

            // Initialize tab based on URL query parameter
            function initializeTab() {
                const urlTab = getUrlParameter('tab');
                const initialTab = urlTab && isValidTab(urlTab) ? urlTab : 'data-utama';
                switchTab(initialTab);
            }

            // Handle browser back/forward navigation
            window.addEventListener('popstate', function(event) {
                const urlTab = getUrlParameter('tab');
                const targetTab = urlTab && isValidTab(urlTab) ? urlTab : 'data-utama';
                switchTabWithoutUrlUpdate(targetTab);
            });

            // Setup mobile tabs
            function setupMobileTabs() {
                const mobileTabsContainer = document.getElementById('mobile-tabs');

                if (mobileTabsContainer) {
                    function updateScrollIndicators() {
                        // Add scroll indicator logic if needed
                    }

                    mobileTabsContainer.addEventListener('scroll', updateScrollIndicators);
                    window.addEventListener('resize', updateScrollIndicators);
                    updateScrollIndicators();
                }
            }

            // Setup currency formatting
            function setupCurrencyFormatting() {
                const currencyFields = ['penghasilan_ayah', 'penghasilan_ibu', 'penghasilan_wali'];

                currencyFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field && field.value) {
                        field.value = formatCurrency(field.value);
                    }

                    if (field) {
                        field.addEventListener('input', function() {
                            if (!this.disabled) {
                                const cursorPosition = this.selectionStart;
                                const oldValue = this.value;
                                const newValue = formatCurrencyInput(this.value);
                                this.value = newValue;

                                // Adjust cursor position
                                const diff = newValue.length - oldValue.length;
                                this.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
                            }
                        });

                        field.addEventListener('blur', function() {
                            if (!this.disabled && this.value && !this.value.startsWith('Rp ')) {
                                this.value = formatCurrency(this.value);
                            }
                        });
                    }
                });
            }

            // Format currency functions
            function formatCurrency(value) {
                if (!value) return '';
                if (value.toString().startsWith('Rp ')) return value;

                let numStr = value.toString().replace(/[^\d,.-]/g, '');
                if (!isNaN(numStr.replace(/[,.]/g, ''))) {
                    const num = parseFloat(numStr.replace(/[,.]/g, ''));
                    if (num > 0) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
                    }
                }
                return value;
            }

            function formatCurrencyInput(value) {
                if (!value) return '';

                let numStr = value.replace(/Rp\s*/g, '').replace(/\./g, '');
                numStr = numStr.replace(/[^\d]/g, '');

                if (numStr === '') return '';

                const formatted = new Intl.NumberFormat('id-ID').format(parseInt(numStr));
                return 'Rp ' + formatted;
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

            // Photo functions
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

                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Uploading...';
                submitBtn.disabled = true;

                fetch(`/mahasiswa/{{ $mahasiswa->id_mahasiswa }}/upload-photo`, {
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
                    toggleBtn.className =
                        'flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200';
                    toggleText.textContent = 'Mode Edit Aktif';
                    formActions.classList.remove('hidden');
                    formActions.classList.add('flex');

                    formInputs.forEach(input => {
                        input.disabled = false;
                        input.classList.remove('disabled:bg-gray-50', 'disabled:text-gray-500');
                    });

                    const clearBtn = document.getElementById('clear_program_studi');
                    if (clearBtn) clearBtn.disabled = false;
                } else {
                    toggleBtn.className =
                        'flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200';
                    toggleText.textContent = 'Ubah Data';
                    formActions.classList.add('hidden');

                    formInputs.forEach(input => {
                        input.disabled = true;
                        input.classList.add('disabled:bg-gray-50', 'disabled:text-gray-500');
                    });

                    const clearBtn = document.getElementById('clear_program_studi');
                    if (clearBtn) clearBtn.disabled = true;
                }
            }

            function cancelEditMode() {
                location.reload();
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

            // Enhanced tab switching with URL update
            function switchTab(tabName) {
                updateUrlParameter('tab', tabName);
                switchTabWithoutUrlUpdate(tabName);
            }

            // Tab switching without URL update
            function switchTabWithoutUrlUpdate(tabName) {
                currentTab = tabName;

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
            function resetPassword(mahasiswaId) {
                if (confirm(
                        'Yakin ingin mereset password mahasiswa ini?\n\nPassword akan direset sesuai dengan NIM yang aktif.'
                    )) {
                    const form = document.getElementById('reset-password-form');
                    form.action = `/mahasiswa/${mahasiswaId}/reset-password`;
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

                const currentKurikulumId = document.getElementById('id_kurikulum').value;
                loadKurikulumData(id, currentKurikulumId);

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
            document.getElementById('detailForm').addEventListener('submit', function(e) {
                const requiredFields = ['nim', 'nama', 'id_program_studi', 'id_kurikulum', 'angkatan',
                    'status_mahasiswa'
                ];

                for (let fieldName of requiredFields) {
                    const field = document.getElementById(fieldName);
                    if (!field || !field.value.trim()) {
                        e.preventDefault();

                        if (['nim', 'nama', 'id_program_studi', 'id_kurikulum', 'angkatan', 'status_mahasiswa']
                            .includes(fieldName)) {
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
                const nikFields = ['nik', 'nik_ayah', 'nik_ibu'];
                for (let fieldName of nikFields) {
                    const field = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
                    if (field && field.value && field.value.length !== 16) {
                        e.preventDefault();
                        alert(`${fieldName.toUpperCase()} harus 16 digit`);
                        field.focus();

                        if (fieldName === 'nik') switchTab('data-pribadi');
                        else switchTab('data-orangtua');

                        return false;
                    }
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
