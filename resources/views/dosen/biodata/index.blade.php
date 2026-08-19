@extends('layouts.app')

@section('title', 'Profil Saya - ' . $dosen->pengguna->nama)

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
                                <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center relative cursor-pointer overflow-hidden"
                                    onclick="openPhotoUpload()">
                                    @if ($dosen->foto)
                                        <img src="{{ asset('storage/foto-dosen/' . $dosen->foto) }}"
                                            alt="{{ $dosen->pengguna->nama }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-2xl font-bold text-gray-700">
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
                                    @if ($dosen->gelar_depan)
                                        {{ $dosen->gelar_depan }}.
                                    @endif
                                    {{ $dosen->pengguna->nama }}
                                    @if ($dosen->gelar_belakang)
                                        , {{ $dosen->gelar_belakang }}
                                    @endif
                                </h1>
                                <div class="flex sm:flex-row flex-col items-start sm:items-center gap-x-4 gap-y-1">
                                    <div class="flex items-center text-sm text-gray-600 gap-1">
                                        <i class="fa-solid fa-id-card text-gray-500"></i>
                                        {{ $dosen->nidn }}
                                    </div>
                                    @if ($dosen->programStudi)
                                        <div class="flex items-center text-sm text-gray-600 gap-1">
                                            <i class="fa-solid fa-building text-gray-500"></i>
                                            {{ $dosen->programStudi->nama_program_studi }}
                                        </div>
                                    @endif
                                    <div class="flex items-center text-sm text-gray-600 gap-1">
                                        <i class="fa-solid fa-briefcase text-gray-500"></i>
                                        {{ $statusKepegawaianOptions[$dosen->status_kepegawaian] }}
                                    </div>
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
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 lg:mt-0">
                            <div class="flex flex-wrap gap-3">
                                <!-- Edit Mode Toggle -->
                                <button id="toggleEditBtn" onclick="toggleEditMode()"
                                    class="flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                    <span id="toggleEditText">Ubah Profil</span>
                                </button>

                                <button onclick="openPasswordModal()"
                                    class="flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2 bg-yellow-600 text-white text-xs font-medium rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                                    Ubah Password
                                </button>
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
                                <button onclick="switchTab('kontak')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-kontak">
                                    <i class="fas fa-address-book w-4 h-4 inline mr-2"></i>
                                    Kontak
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
                        <button onclick="switchTab('kontak')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-kontak-desktop">
                            <i class="fas fa-address-book w-4 h-4 inline mr-2"></i>
                            Kontak
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <form id="profileForm" method="POST" action="{{ route('dosen.biodata.update') }}">
                    @csrf
                    @method('PUT')

                    <!-- Tab 1: Data Utama (Read Only) -->
                    <div id="content-data-utama" class="tab-content active">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-base font-medium text-gray-900">Data Utama</h3>
                                <p class="text-xs text-gray-500">Data ini tidak dapat diubah</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- NIDN -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">NIDN</label>
                                    <div
                                        class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-900">
                                        {{ $dosen->nidn }}
                                    </div>
                                </div>

                                <!-- Nama Lengkap -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                    <div
                                        class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-900">
                                        {{ $dosen->pengguna->nama }}
                                    </div>
                                </div>

                                <!-- Program Studi -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Program Studi</label>
                                    <div
                                        class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-900">
                                        @if ($dosen->programStudi)
                                            {{ $dosen->programStudi->nama_program_studi }}
                                            ({{ $dosen->programStudi->jenjang->kode_jenjang_pendidikan }})
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>

                                <!-- Status Dosen -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Status Dosen</label>
                                    <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ match ($dosen->status_dosen) {
                                                'AKTIF' => 'bg-green-100 text-green-800',
                                                'CUTI' => 'bg-yellow-100 text-yellow-800',
                                                'KELUAR' => 'bg-gray-100 text-gray-800',
                                                'NONAKTIF' => 'bg-orange-100 text-orange-800',
                                                'PENSIUN' => 'bg-purple-100 text-purple-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            } }}">
                                            {{ $statusDosenOptions[$dosen->status_dosen] }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Status Kepegawaian -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Status Kepegawaian</label>
                                    <div
                                        class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-900">
                                        {{ $statusKepegawaianOptions[$dosen->status_kepegawaian] }}
                                    </div>
                                </div>

                                <!-- Total Kuota PA -->
                                @if ($dosen->total_kuota_pa)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Kuota Pembimbing
                                            Akademik</label>
                                        <div
                                            class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-900">
                                            {{ $dosen->total_kuota_pa }} Mahasiswa
                                        </div>
                                    </div>
                                @endif
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
                                    <!-- Gelar Depan -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Gelar Depan</label>
                                        <input type="text" name="gelar_depan" id="gelar_depan"
                                            value="{{ $dosen->gelar_depan }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="50" placeholder="Contoh: Dr" disabled>
                                    </div>

                                    <!-- Gelar Belakang -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Gelar Belakang</label>
                                        <input type="text" name="gelar_belakang" id="gelar_belakang"
                                            value="{{ $dosen->gelar_belakang }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="50" placeholder="Contoh: M.Kom" disabled>
                                    </div>

                                    <!-- Jenis Kelamin -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" id="jenis_kelamin"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            disabled>
                                            <option value="">Pilih jenis kelamin</option>
                                            <option value="L" {{ $dosen->jenis_kelamin === 'L' ? 'selected' : '' }}>
                                                Laki-laki</option>
                                            <option value="P" {{ $dosen->jenis_kelamin === 'P' ? 'selected' : '' }}>
                                                Perempuan</option>
                                        </select>
                                    </div>

                                    <!-- Tempat Lahir -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" id="tempat_lahir"
                                            value="{{ $dosen->tempat_lahir }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Jakarta" disabled>
                                    </div>

                                    <!-- Tanggal Lahir -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                                        <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                                            value="{{ $dosen->tanggal_lahir }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            disabled>
                                    </div>

                                    <!-- NIK -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">NIK (16 digit)</label>
                                        <input type="text" name="nik" id="nik" value="{{ $dosen->nik }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="16" minlength="16" pattern="[0-9]{16}"
                                            placeholder="Contoh: 1234567890123456" disabled>
                                    </div>

                                    <!-- NPWP -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">NPWP</label>
                                        <input type="text" name="npwp" id="npwp" value="{{ $dosen->npwp }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
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
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="255" placeholder="Contoh: Jl. Sudirman No. 123" disabled>
                                    </div>

                                    <!-- Dusun -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Dusun</label>
                                        <input type="text" name="dusun" id="dusun" value="{{ $dosen->dusun }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Kebon Jeruk" disabled>
                                    </div>

                                    <!-- RT -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">RT</label>
                                        <input type="text" name="rt" id="rt" value="{{ $dosen->rt }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="3" placeholder="Contoh: 001" disabled>
                                    </div>

                                    <!-- RW -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">RW</label>
                                        <input type="text" name="rw" id="rw" value="{{ $dosen->rw }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="3" placeholder="Contoh: 002" disabled>
                                    </div>

                                    <!-- Kelurahan -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Kelurahan</label>
                                        <input type="text" name="kelurahan" id="kelurahan"
                                            value="{{ $dosen->kelurahan }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="100" placeholder="Contoh: Kebon Jeruk" disabled>
                                    </div>

                                    <!-- Kode Pos -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Kode Pos</label>
                                        <input type="text" name="kode_pos" id="kode_pos"
                                            value="{{ $dosen->kode_pos }}"
                                            class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                            maxlength="10" placeholder="Contoh: 12345" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Kontak -->
                    <div id="content-kontak" class="tab-content hidden">
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
                                                {{ $dosen->pengguna->username }}</div>
                                            <p class="text-xs text-gray-500 mt-1">Username tidak dapat diubah (sama dengan
                                                NIDN)</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Status Akun</label>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dosen->pengguna->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $dosen->pengguna->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
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
                                                value="{{ $dosen->pengguna->email }}"
                                                class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                                placeholder="Contoh: john.doe@example.com" disabled>
                                        </div>

                                        <!-- No HP -->
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-2">No HP</label>
                                            <input type="tel" name="no_hp" id="no_hp"
                                                value="{{ $dosen->pengguna->no_hp }}"
                                                class="form-input w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                                placeholder="Contoh: 081234567890" maxlength="20" disabled>
                                        </div>
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
                            class="inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                        <label class="block text-xs font-medium text-gray-700 mb-2">Pilih Foto</label>
                        <input type="file" id="photo-input" name="foto" accept="image/*"
                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG. Maksimal: 2MB</p>
                    </div>

                    <!-- Preview -->
                    <div id="photo-preview" class="mb-4 hidden">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Preview</label>
                        <img id="preview-image"
                            class="w-32 h-32 rounded-full object-cover mx-auto border-4 border-gray-200">
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closePhotoUpload()"
                            class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-xs font-medium text-white bg-gray-600 rounded-lg hover:bg-gray-700">
                            Upload
                        </button>
                        @if ($dosen->foto)
                            <button type="button" onclick="deletePhoto()"
                                class="px-4 py-2 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                                Hapus Foto
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Change Modal -->
    <div id="password-modal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[9999] hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Ubah Password</h3>
                    <button type="button" onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="password-form" method="POST" action="{{ route('dosen.biodata.update-password') }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <!-- Password Lama -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Password Lama</label>
                            <input type="password" name="current_password" id="current_password" required
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500">
                        </div>

                        <!-- Password Baru -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Password Baru</label>
                            <input type="password" name="new_password" id="new_password" required minlength="6"
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500">
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                required minlength="6"
                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closePasswordModal()"
                            class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-xs font-medium text-white bg-gray-600 rounded-lg hover:bg-gray-700">
                            Ubah Password
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
            let isEditMode = false;
            let currentTab = 'data-utama';

            // Initialize page
            document.addEventListener('DOMContentLoaded', function() {
                setupPhotoUpload();
                setupFormHandlers();
                setupMobileTabs();
                initializeTab();
            });

            // Utility function to get URL parameters
            function getUrlParameter(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }

            // Utility function to update URL parameter without page refresh
            function updateUrlParameter(key, value) {
                const url = new URL(window.location);
                url.searchParams.set(key, value);
                window.history.pushState({
                    path: url.href
                }, '', url.href);
            }

            // Check if tab name is valid
            function isValidTab(tabName) {
                const validTabs = ['data-utama', 'data-pribadi', 'kontak'];
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

            // Enhanced tab switching with URL update
            function switchTab(tabName) {
                updateUrlParameter('tab', tabName);
                switchTabWithoutUrlUpdate(tabName);
            }

            // Tab switching without URL update (for internal use)
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

            // Mobile tabs setup
            function setupMobileTabs() {
                const mobileTabsContainer = document.getElementById('mobile-tabs');

                if (mobileTabsContainer) {
                    function updateScrollIndicators() {
                        // Add any scroll indicator logic here if needed
                    }

                    mobileTabsContainer.addEventListener('scroll', updateScrollIndicators);
                    window.addEventListener('resize', updateScrollIndicators);
                    updateScrollIndicators();
                }
            }

            // Setup AJAX form handlers
            function setupFormHandlers() {
                // Handle biodata form
                const profileForm = document.getElementById('profileForm');
                if (profileForm) {
                    profileForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        handleBiodataUpdate();
                    });
                }

                // Handle password form
                const passwordForm = document.getElementById('password-form');
                if (passwordForm) {
                    passwordForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        handlePasswordUpdate();
                    });
                }
            }

            // Handle biodata update with AJAX
            function handleBiodataUpdate() {
                const form = document.getElementById('profileForm');
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Disable submit button and show loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Menyimpan...
    `;

                // Clear previous errors
                clearFormErrors();

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccess(data.message);
                            toggleEditMode();
                        } else {
                            showError(data.message || 'Terjadi kesalahan saat menyimpan data.');
                            if (data.errors) {
                                displayFormErrors(data.errors);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('Terjadi kesalahan saat menyimpan data.');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            }

            // Handle password update with AJAX
            function handlePasswordUpdate() {
                const form = document.getElementById('password-form');
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;

                submitBtn.disabled = true;
                submitBtn.textContent = 'Mengubah Password...';

                clearPasswordErrors();

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccess(data.message);
                            form.reset();
                            closePasswordModal();
                        } else {
                            showError(data.message || 'Terjadi kesalahan saat mengubah password.');
                            if (data.errors) {
                                displayPasswordErrors(data.errors);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('Terjadi kesalahan saat mengubah password.');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    });
            }

            // Clear form errors
            function clearFormErrors() {
                const errorElements = document.querySelectorAll('.error-message');
                errorElements.forEach(el => el.remove());

                const inputsWithErrors = document.querySelectorAll('.border-red-500');
                inputsWithErrors.forEach(input => {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-gray-300');
                });
            }

            // Display form errors
            function displayFormErrors(errors) {
                Object.keys(errors).forEach(fieldName => {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (field) {
                        field.classList.remove('border-gray-300');
                        field.classList.add('border-red-500');

                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'error-message text-xs text-red-600 mt-1';
                        errorDiv.textContent = errors[fieldName][0];

                        field.parentNode.appendChild(errorDiv);
                    }
                });
            }

            // Clear password errors
            function clearPasswordErrors() {
                const passwordModal = document.getElementById('password-modal');
                const errorElements = passwordModal.querySelectorAll('.error-message');
                errorElements.forEach(el => el.remove());

                const inputsWithErrors = passwordModal.querySelectorAll('.border-red-500');
                inputsWithErrors.forEach(input => {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-gray-300');
                });
            }

            // Display password errors
            function displayPasswordErrors(errors) {
                const passwordModal = document.getElementById('password-modal');
                Object.keys(errors).forEach(fieldName => {
                    const field = passwordModal.querySelector(`[name="${fieldName}"]`);
                    if (field) {
                        field.classList.remove('border-gray-300');
                        field.classList.add('border-red-500');

                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'error-message text-xs text-red-600 mt-1';
                        errorDiv.textContent = errors[fieldName][0];

                        field.parentNode.appendChild(errorDiv);
                    }
                });
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
                } else {
                    toggleBtn.className =
                        'flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200';
                    toggleText.textContent = 'Ubah Profil';
                    formActions.classList.add('hidden');

                    formInputs.forEach(input => {
                        input.disabled = true;
                        input.classList.add('disabled:bg-gray-50', 'disabled:text-gray-500');
                    });
                }
            }

            function cancelEditMode() {
                if (isEditMode) {
                    toggleEditMode();
                    const form = document.getElementById('profileForm');
                    form.reset();
                    clearFormErrors();
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
                submitBtn.textContent = 'Mengupload...';
                submitBtn.disabled = true;

                fetch('{{ route('dosen.biodata.upload-photo') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccess(data.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showError(data.message || 'Gagal mengupload foto.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('Gagal mengupload foto.');
                    })
                    .finally(() => {
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                        closePhotoUpload();
                    });
            }

            function deletePhoto() {
                if (confirm('Yakin ingin menghapus foto profil?')) {
                    fetch('{{ route('dosen.biodata.delete-photo') }}', {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showSuccess(data.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                showError(data.message || 'Gagal menghapus foto.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showError('Gagal menghapus foto.');
                        });
                }
            }

            // Password functions
            function openPasswordModal() {
                document.getElementById('password-modal').classList.remove('hidden');
            }

            function closePasswordModal() {
                document.getElementById('password-modal').classList.add('hidden');
                document.getElementById('password-form').reset();
                clearPasswordErrors();
            }

            // Keyboard shortcuts
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closePhotoUpload();
                    closePasswordModal();
                }
            });

            // Make functions global
            window.switchTab = switchTab;
            window.toggleEditMode = toggleEditMode;
            window.cancelEditMode = cancelEditMode;
            window.openPhotoUpload = openPhotoUpload;
            window.closePhotoUpload = closePhotoUpload;
            window.deletePhoto = deletePhoto;
            window.openPasswordModal = openPasswordModal;
            window.closePasswordModal = closePasswordModal;
        </script>
    @endpush
@endsection
