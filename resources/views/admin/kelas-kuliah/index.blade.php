@extends('layouts.app')

@section('title', 'Manajemen Kelas Kuliah')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Manajemen Kelas Kuliah</h1>
                            <p class="text-sm text-gray-600">Kelola kelas kuliah berdasarkan semester akademik</p>
                        </div>

                        @if ($selectedSemester)
                            <div class="mt-4 sm:mt-0">
                                <button onclick="openModal('create')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Kelas Kuliah
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Filter Semester & Search -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
                        <!-- Filter Semester -->
                        <div class="flex items-center space-x-3">
                            <label class="text-xs font-medium text-gray-700">Semester:</label>
                            <select id="semesterFilter" onchange="changeSemester()"
                                class="text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[200px]">
                                <option value="">Pilih Semester</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id_semester }}"
                                        {{ $selectedSemesterId == $semester->id_semester ? 'selected' : '' }}
                                        data-active="{{ $semester->is_active ? '1' : '0' }}">
                                        {{ $semester->nama_semester }}
                                        {{ $semester->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Search & Filter Program Studi -->
                        @if ($selectedSemester)
                            <div class="flex items-center space-x-3">
                                <!-- Search -->
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" id="searchInput" placeholder="Cari kelas kuliah..."
                                        class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                </div>

                                <!-- Filter Program Studi -->
                                <select id="programStudiFilter" onchange="filterByProgramStudi()"
                                    class="text-xs py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Semua Program Studi</option>
                                    @foreach ($programStudis as $prodi)
                                        <option value="{{ $prodi->id_program_studi }}"
                                            {{ request('program_studi') == $prodi->id_program_studi ? 'selected' : '' }}>
                                            {{ $prodi->jenjang->kode_jenjang_pendidikan }} -
                                            {{ $prodi->nama_program_studi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if (!$selectedSemester)
                <!-- Empty State - Pilih Semester -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-calendar-alt w-16 h-16 text-gray-400 mb-4 mx-auto text-6xl"></i>
                        <p class="text-lg font-medium text-gray-900 mb-2">Pilih Semester</p>
                        <p class="text-sm text-gray-500 mb-4">Pilih semester dari dropdown di atas untuk mengelola kelas
                            kuliah</p>
                    </div>
                </div>
            @else
                <!-- Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="kelasKuliahTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kelas
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mata Kuliah
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dosen
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ruangan & Kapasitas
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Waktu
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="kelasKuliahTableBody">
                                @forelse($kelasKuliahs as $kelas)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 kelas-kuliah-row"
                                        data-searchable
                                        data-prodi="{{ $kelas->kurikulumMataKuliah->kurikulum->id_program_studi ?? '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="searchable-kelas">
                                                <div class="text-xs font-semibold text-gray-900">
                                                    {{ $kelas->nama_kelas_kuliah }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $kelas->kurikulumMataKuliah->kurikulum->programStudi->nama_program_studi ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="searchable-matkul">
                                                <div class="text-xs font-medium text-gray-900">
                                                    {{ $kelas->kurikulumMataKuliah->mataKuliah->kode_mata_kuliah ?? 'N/A' }}
                                                    -
                                                    {{ $kelas->kurikulumMataKuliah->mataKuliah->nama_mata_kuliah ?? 'N/A' }}
                                                </div>
                                                <div class="flex items-center space-x-2 mt-2">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
                                                        {{ $kelas->kurikulumMataKuliah->mataKuliah->sks_mata_kuliah ?? 0 }}
                                                        SKS
                                                    </span>
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
                                                        Semester {{ $kelas->kurikulumMataKuliah->semester ?? 'N/A' }}
                                                    </span>
                                                    <!-- ✅ Fix: Tampilkan jenis mata kuliah dari mata_kuliah -->
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                {{ ($kelas->kurikulumMataKuliah->mataKuliah->jenis_mata_kuliah ?? '') == 'TEORI'
                    ? 'bg-blue-100 text-blue-800'
                    : (($kelas->kurikulumMataKuliah->mataKuliah->jenis_mata_kuliah ?? '') == 'PRAKTIKUM'
                        ? 'bg-green-100 text-green-800'
                        : 'bg-purple-100 text-purple-800') }}">
                                                        {{ $kelas->kurikulumMataKuliah->mataKuliah->jenis_mata_kuliah ?? 'N/A' }}
                                                    </span>
                                                    <!-- ✅ Fix: Tampilkan kategori mata kuliah dari kurikulum_mata_kuliah -->
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $kelas->kurikulumMataKuliah->kategori_mata_kuliah ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="searchable-dosen">
                                                <div class="text-xs font-medium text-gray-900">
                                                    {{ $kelas->dosen->pengguna->nama ?? 'N/A' }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $kelas->dosen->nidn ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="searchable-ruangan">
                                                <div class="text-xs font-medium text-gray-900">
                                                    {{ $kelas->nama_ruangan }}
                                                </div>
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-gray-200 text-gray-700 mt-2">
                                                    Kapasitas: {{ $kelas->kapasitas }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs">
                                                @if ($kelas->jam_mulai && $kelas->jam_akhir)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
                                                        {{ \Carbon\Carbon::parse($kelas->jam_mulai)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($kelas->jam_akhir)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">Belum diatur</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                {{-- Tombol Edit --}}
                                                <div class="relative group">
                                                    <button
                                                        onclick="openEditModal(
                                                        '{{ $kelas->id_kelas_kuliah }}',
                                                        '{{ addslashes($kelas->nama_kelas_kuliah) }}',
                                                        '{{ addslashes($kelas->nama_ruangan) }}',
                                                        '{{ $kelas->kapasitas }}',
                                                        '{{ $kelas->jam_mulai }}',
                                                        '{{ $kelas->jam_akhir }}',
                                                        '{{ $kelas->id_kurikulum_mata_kuliah }}',
                                                        '{{ $kelas->id_semester }}',
                                                        '{{ $kelas->id_dosen }}'
                                                    )"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="text-xs fas fa-edit"></i>
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Edit Kelas Kuliah
                                                        <div
                                                            class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Tombol Hapus --}}
                                                <div class="relative group">
                                                    <button
                                                        onclick="confirmDelete('{{ $kelas->id_kelas_kuliah }}', '{{ addslashes($kelas->nama_kelas_kuliah) }}')"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Hapus Kelas Kuliah
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
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m6 0h2m2 0H7m6 0v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v6">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium text-gray-900 mb-2">Belum ada kelas kuliah
                                                </p>
                                                <p class="text-xs text-gray-500">Tambahkan kelas kuliah untuk semester
                                                    {{ $selectedSemester->nama_semester }}</p>
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
            @endif
        </div>

        <!-- Create/Edit Modal -->
        @if ($selectedSemester)
            <div id="kelasKuliahModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

                    <div
                        class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-5xl mx-auto max-h-[90vh] overflow-y-auto">
                        <form id="kelasKuliahForm" method="POST" action="{{ route('kelas-kuliah.store') }}">
                            @csrf
                            <input type="hidden" name="id_semester" value="{{ $selectedSemester->id_semester }}">

                            <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 id="modalTitle" class="text-lg font-heading font-semibold text-gray-900">
                                        Tambah Kelas Kuliah
                                    </h3>
                                    <button type="button" onclick="closeModal()"
                                        class="sm:hidden text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Basic Info -->
                                <div class="md:col-span-2">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-4 border-b pb-2">Informasi Dasar
                                    </h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Nama Kelas <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="nama_kelas_kuliah" id="nama_kelas_kuliah"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="Contoh: A1, B2, Kelas Pagi" maxlength="100" required>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Semester <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" value="{{ $selectedSemester->nama_semester }}"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                                            readonly>
                                    </div>

                                    <!-- Step 1: Program Studi Search -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Program Studi <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text" id="program_studi_search"
                                                placeholder="Cari program studi berdasarkan kode atau nama..."
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

                                            <input type="hidden" id="selected_program_studi">

                                            <div id="program_studi_dropdown"
                                                class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                            </div>

                                            <div id="program_studi_loading" class="absolute right-8 top-2 hidden">
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

                                    <!-- Step 2: Kurikulum Search -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Kurikulum <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text" id="kurikulum_search"
                                                placeholder="Pilih program studi terlebih dahulu..." autocomplete="off"
                                                disabled
                                                class="w-full text-xs px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-100 disabled:text-gray-500">

                                            <button type="button" id="clear_kurikulum"
                                                onclick="clearKurikulumSelection()"
                                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6m0 12L6 6"></path>
                                                </svg>
                                            </button>

                                            <input type="hidden" id="selected_kurikulum">

                                            <div id="kurikulum_dropdown"
                                                class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                            </div>

                                            <div id="kurikulum_loading" class="absolute right-8 top-2 hidden">
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

                                    <!-- Step 3: Mata Kuliah Search -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Mata Kuliah <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text" id="mata_kuliah_search"
                                                placeholder="Pilih kurikulum terlebih dahulu..." autocomplete="off"
                                                disabled
                                                class="w-full text-xs px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-100 disabled:text-gray-500">

                                            <button type="button" id="clear_mata_kuliah"
                                                onclick="clearMataKuliahSelection()"
                                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6m0 12L6 6"></path>
                                                </svg>
                                            </button>

                                            <input type="hidden" name="id_kurikulum_mata_kuliah"
                                                id="id_kurikulum_mata_kuliah" required>

                                            <div id="mata_kuliah_dropdown"
                                                class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                            </div>

                                            <div id="mata_kuliah_loading" class="absolute right-8 top-2 hidden">
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

                                    <!-- Dosen Search -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Dosen Pengampu <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text" id="dosen_search"
                                                placeholder="Cari dosen berdasarkan nama, NIDN, atau program studi..."
                                                autocomplete="off"
                                                class="w-full text-xs px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">

                                            <button type="button" id="clear_dosen" onclick="clearDosenSelection()"
                                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6m0 12L6 6"></path>
                                                </svg>
                                            </button>

                                            <input type="hidden" name="id_dosen" id="id_dosen" required>

                                            <div id="dosen_dropdown"
                                                class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                            </div>

                                            <div id="dosen_loading" class="absolute right-8 top-2 hidden">
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
                                </div>

                                <!-- Room & Time Info -->
                                <div class="md:col-span-2 mt-10">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-4 border-b pb-2">Ruangan & Waktu
                                    </h4>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Nama Ruangan <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="nama_ruangan" id="nama_ruangan"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="Contoh: R.101, Lab Komputer 1" maxlength="100" required>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Kapasitas Kelas <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="kapasitas" id="kapasitas"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="Contoh: 40" min="1" max="200" required>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Jam Mulai
                                        </label>
                                        <input type="time" name="jam_mulai" id="jam_mulai"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Jam Akhir
                                        </label>
                                        <input type="time" name="jam_akhir" id="jam_akhir"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    </div>
                                </div>

                                <!-- Info Semester -->
                                <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-xs text-gray-600">
                                            Kelas kuliah ini akan dibuat untuk semester:
                                            <strong>{{ $selectedSemester->nama_semester }}</strong>
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
        @endif
    </div>

    <!-- Hidden Forms untuk Actions -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            // Global variables for search data
            let programStudiData = [];
            let kurikulumData = [];
            let mataKuliahData = [];
            let dosenData = [];
            let searchTimeouts = {};

            // Load initial data
            document.addEventListener('DOMContentLoaded', function() {
                loadProgramStudiData();
                loadDosenData();
            });

            // Load program studi data
            function loadProgramStudiData() {
                fetch('/kelas-kuliah/api/program-studi')
                    .then(response => response.json())
                    .then(data => {
                        programStudiData = data;
                    })
                    .catch(error => {
                        console.error('Error loading program studi:', error);
                    });
            }

            // Load dosen data
            function loadDosenData() {
                fetch('/kelas-kuliah/api/dosen')
                    .then(response => response.json())
                    .then(data => {
                        dosenData = data;
                    })
                    .catch(error => {
                        console.error('Error loading dosen:', error);
                    });
            }

            // Change semester function
            function changeSemester() {
                const semesterId = document.getElementById('semesterFilter').value;
                if (semesterId) {
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.set('semester', semesterId);
                    currentParams.delete('page');
                    window.location.href = window.location.pathname + '?' + currentParams.toString();
                } else {
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.delete('semester');
                    currentParams.delete('page');
                    window.location.href = window.location.pathname + (currentParams.toString() ? '?' + currentParams
                        .toString() : '');
                }
            }

            @if ($selectedSemester)
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
                    const tbody = document.getElementById('kelasKuliahTableBody');
                    const rows = tbody.querySelectorAll('tr[data-searchable]');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const kelasCell = row.querySelector('.searchable-kelas');
                        const matkulCell = row.querySelector('.searchable-matkul');
                        const dosenCell = row.querySelector('.searchable-dosen');
                        const ruanganCell = row.querySelector('.searchable-ruangan');
                        const rowProdi = row.getAttribute('data-prodi');

                        const kelasText = kelasCell ? kelasCell.textContent.toLowerCase() : '';
                        const matkulText = matkulCell ? matkulCell.textContent.toLowerCase() : '';
                        const dosenText = dosenCell ? dosenCell.textContent.toLowerCase() : '';
                        const ruanganText = ruanganCell ? ruanganCell.textContent.toLowerCase() : '';

                        const matchesSearch = searchTerm === '' ||
                            kelasText.includes(searchTerm) ||
                            matkulText.includes(searchTerm) ||
                            dosenText.includes(searchTerm) ||
                            ruanganText.includes(searchTerm);
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
                }

                function updateEmptyState(visibleCount, searchTerm, prodiFilter) {
                    const tbody = document.getElementById('kelasKuliahTableBody');
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
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
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

                // ===============================================
                // STEP 1: PROGRAM STUDI SEARCH
                // ===============================================

                function setupProgramStudiSearch() {
                    const searchInput = document.getElementById('program_studi_search');
                    if (!searchInput) return;

                    const newSearchInput = searchInput.cloneNode(true);
                    searchInput.parentNode.replaceChild(newSearchInput, searchInput);

                    newSearchInput.addEventListener('input', function() {
                        const query = this.value.toLowerCase().trim();

                        if (searchTimeouts.programStudi) {
                            clearTimeout(searchTimeouts.programStudi);
                        }

                        updateProgramStudiClearButton();
                        document.getElementById('program_studi_loading').classList.remove('hidden');

                        searchTimeouts.programStudi = setTimeout(() => {
                            searchProgramStudi(query);
                            document.getElementById('program_studi_loading').classList.add('hidden');
                        }, 300);
                    });

                    newSearchInput.addEventListener('focus', function() {
                        if (this.value !== '') {
                            const query = this.value.toLowerCase().trim();
                            searchProgramStudi(query);
                        } else {
                            showAllProgramStudi();
                        }
                    });

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        const dropdown = document.getElementById('program_studi_dropdown');
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
                        const kodeMatch = item.kode_program_studi && item.kode_program_studi.toLowerCase().includes(
                            query);
                        const namaMatch = item.nama_program_studi && item.nama_program_studi.toLowerCase().includes(
                            query);
                        const jenjangMatch = item.jenjang && item.jenjang.toLowerCase().includes(query);
                        return kodeMatch || namaMatch || jenjangMatch;
                    });

                    displayProgramStudiResults(filteredData);
                }

                function showAllProgramStudi() {
                    displayProgramStudiResults(programStudiData);
                }

                function displayProgramStudiResults(data) {
                    const dropdown = document.getElementById('program_studi_dropdown');

                    if (data.length === 0) {
                        dropdown.innerHTML = `
                            <div class="px-4 py-3 text-center text-xs text-gray-500">
                                <div>Tidak ada program studi ditemukan</div>
                            </div>
                        `;
                    } else {
                        dropdown.innerHTML = data.map(item => `
                            <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                                 onclick="selectProgramStudi('${item.id_program_studi}', '${escapeHtml(item.nama_lengkap)}')">
                                <div class="text-xs font-medium text-gray-900">${item.nama_program_studi}</div>
                                <div class="text-xs text-gray-500">${item.jenjang} ${item.kode_program_studi ? '• ' + item.kode_program_studi : ''}</div>
                            </div>
                        `).join('');
                    }

                    dropdown.classList.remove('hidden');
                }

                function selectProgramStudi(id, namaLengkap) {
                    const searchInput = document.getElementById('program_studi_search');
                    const hiddenInput = document.getElementById('selected_program_studi');
                    const dropdown = document.getElementById('program_studi_dropdown');

                    searchInput.value = namaLengkap;
                    hiddenInput.value = id;

                    updateProgramStudiClearButton();
                    dropdown.classList.add('hidden');

                    // Reset kurikulum dan mata kuliah
                    clearKurikulumSelection();
                    clearMataKuliahSelection();

                    // Enable kurikulum search
                    const kurikulumInput = document.getElementById('kurikulum_search');
                    kurikulumInput.disabled = false;
                    kurikulumInput.placeholder = 'Cari kurikulum...';

                    // Load kurikulum data
                    loadKurikulumData(id);

                    // Visual feedback
                    searchInput.classList.add('border-green-300', 'bg-green-50');
                    setTimeout(() => {
                        searchInput.classList.remove('border-green-300', 'bg-green-50');
                    }, 1000);
                }

                function clearProgramStudiSelection() {
                    document.getElementById('program_studi_search').value = '';
                    document.getElementById('selected_program_studi').value = '';
                    document.getElementById('program_studi_dropdown').classList.add('hidden');
                    document.getElementById('clear_program_studi').classList.add('hidden');

                    // Reset dan disable kurikulum & mata kuliah
                    clearKurikulumSelection();
                    clearMataKuliahSelection();

                    const kurikulumInput = document.getElementById('kurikulum_search');
                    kurikulumInput.disabled = true;
                    kurikulumInput.placeholder = 'Pilih program studi terlebih dahulu...';
                }

                function updateProgramStudiClearButton() {
                    const hiddenInput = document.getElementById('selected_program_studi');
                    const clearButton = document.getElementById('clear_program_studi');

                    if (hiddenInput.value) {
                        clearButton.classList.remove('hidden');
                    } else {
                        clearButton.classList.add('hidden');
                    }
                }

                // ===============================================
                // STEP 2: KURIKULUM SEARCH
                // ===============================================

                function loadKurikulumData(programStudiId) {
                    fetch(`/kelas-kuliah/api/kurikulum?program_studi=${programStudiId}`)
                        .then(response => response.json())
                        .then(data => {
                            kurikulumData = data;
                        })
                        .catch(error => {
                            console.error('Error loading kurikulum:', error);
                        });
                }

                function setupKurikulumSearch() {
                    const searchInput = document.getElementById('kurikulum_search');
                    if (!searchInput) return;

                    const newSearchInput = searchInput.cloneNode(true);
                    searchInput.parentNode.replaceChild(newSearchInput, searchInput);

                    newSearchInput.addEventListener('input', function() {
                        if (this.disabled) return;

                        const query = this.value.toLowerCase().trim();

                        if (searchTimeouts.kurikulum) {
                            clearTimeout(searchTimeouts.kurikulum);
                        }

                        updateKurikulumClearButton();
                        document.getElementById('kurikulum_loading').classList.remove('hidden');

                        searchTimeouts.kurikulum = setTimeout(() => {
                            searchKurikulum(query);
                            document.getElementById('kurikulum_loading').classList.add('hidden');
                        }, 300);
                    });

                    newSearchInput.addEventListener('focus', function() {
                        if (this.disabled) return;

                        if (this.value !== '') {
                            const query = this.value.toLowerCase().trim();
                            searchKurikulum(query);
                        } else {
                            showAllKurikulum();
                        }
                    });

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        const dropdown = document.getElementById('kurikulum_dropdown');
                        if (!newSearchInput.contains(event.target) && !dropdown.contains(event.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                }

                function searchKurikulum(query) {
                    if (query === '') {
                        showAllKurikulum();
                        return;
                    }

                    const filteredData = kurikulumData.filter(item => {
                        const namaMatch = item.nama_kurikulum && item.nama_kurikulum.toLowerCase().includes(query);
                        return namaMatch;
                    });

                    displayKurikulumResults(filteredData);
                }

                function showAllKurikulum() {
                    displayKurikulumResults(kurikulumData);
                }

                function displayKurikulumResults(data) {
                    const dropdown = document.getElementById('kurikulum_dropdown');

                    if (data.length === 0) {
                        dropdown.innerHTML = `
                            <div class="px-4 py-3 text-center text-xs text-gray-500">
                                <div>Tidak ada kurikulum ditemukan</div>
                            </div>
                        `;
                    } else {
                        dropdown.innerHTML = data.map(item => `
                            <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                                 onclick="selectKurikulum('${item.id_kurikulum}', '${escapeHtml(item.nama_lengkap)}')">
                                <div class="text-xs font-medium text-gray-900">${item.nama_kurikulum}</div>
                                <div class="text-xs text-gray-500">${item.program_studi} • ${item.semester}</div>
                            </div>
                        `).join('');
                    }

                    dropdown.classList.remove('hidden');
                }

                function selectKurikulum(id, namaLengkap) {
                    const searchInput = document.getElementById('kurikulum_search');
                    const hiddenInput = document.getElementById('selected_kurikulum');
                    const dropdown = document.getElementById('kurikulum_dropdown');

                    searchInput.value = namaLengkap;
                    hiddenInput.value = id;

                    updateKurikulumClearButton();
                    dropdown.classList.add('hidden');

                    // Reset mata kuliah
                    clearMataKuliahSelection();

                    // Enable mata kuliah search
                    const mataKuliahInput = document.getElementById('mata_kuliah_search');
                    mataKuliahInput.disabled = false;
                    mataKuliahInput.placeholder = 'Cari mata kuliah...';

                    // Load mata kuliah data
                    loadMataKuliahData(id);

                    // Visual feedback
                    searchInput.classList.add('border-green-300', 'bg-green-50');
                    setTimeout(() => {
                        searchInput.classList.remove('border-green-300', 'bg-green-50');
                    }, 1000);
                }

                function clearKurikulumSelection() {
                    document.getElementById('kurikulum_search').value = '';
                    document.getElementById('selected_kurikulum').value = '';
                    document.getElementById('kurikulum_dropdown').classList.add('hidden');
                    document.getElementById('clear_kurikulum').classList.add('hidden');

                    // Reset dan disable mata kuliah
                    clearMataKuliahSelection();
                    const mataKuliahInput = document.getElementById('mata_kuliah_search');
                    mataKuliahInput.disabled = true;
                    mataKuliahInput.placeholder = 'Pilih kurikulum terlebih dahulu...';
                }

                function updateKurikulumClearButton() {
                    const hiddenInput = document.getElementById('selected_kurikulum');
                    const clearButton = document.getElementById('clear_kurikulum');

                    if (hiddenInput.value) {
                        clearButton.classList.remove('hidden');
                    } else {
                        clearButton.classList.add('hidden');
                    }
                }

                // ===============================================
                // STEP 3: MATA KULIAH SEARCH
                // ===============================================

                function loadMataKuliahData(kurikulumId) {
                    fetch(`/kelas-kuliah/api/kurikulum-mata-kuliah?kurikulum=${kurikulumId}`)
                        .then(response => response.json())
                        .then(data => {
                            mataKuliahData = data;
                        })
                        .catch(error => {
                            console.error('Error loading mata kuliah:', error);
                        });
                }

                function setupMataKuliahSearch() {
                    const searchInput = document.getElementById('mata_kuliah_search');
                    if (!searchInput) return;

                    const newSearchInput = searchInput.cloneNode(true);
                    searchInput.parentNode.replaceChild(newSearchInput, searchInput);

                    newSearchInput.addEventListener('input', function() {
                        if (this.disabled) return;

                        const query = this.value.toLowerCase().trim();

                        if (searchTimeouts.mataKuliah) {
                            clearTimeout(searchTimeouts.mataKuliah);
                        }

                        updateMataKuliahClearButton();
                        document.getElementById('mata_kuliah_loading').classList.remove('hidden');

                        searchTimeouts.mataKuliah = setTimeout(() => {
                            searchMataKuliah(query);
                            document.getElementById('mata_kuliah_loading').classList.add('hidden');
                        }, 300);
                    });

                    newSearchInput.addEventListener('focus', function() {
                        if (this.disabled) return;

                        if (this.value !== '') {
                            const query = this.value.toLowerCase().trim();
                            searchMataKuliah(query);
                        } else {
                            showAllMataKuliah();
                        }
                    });

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        const dropdown = document.getElementById('mata_kuliah_dropdown');
                        if (!newSearchInput.contains(event.target) && !dropdown.contains(event.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                }

                function searchMataKuliah(query) {
                    if (query === '') {
                        showAllMataKuliah();
                        return;
                    }

                    const filteredData = mataKuliahData.filter(item => {
                        const kodeMatch = item.kode_mata_kuliah && item.kode_mata_kuliah.toLowerCase().includes(query);
                        const namaMatch = item.nama_mata_kuliah && item.nama_mata_kuliah.toLowerCase().includes(query);
                        return kodeMatch || namaMatch;
                    });

                    displayMataKuliahResults(filteredData);
                }

                function showAllMataKuliah() {
                    displayMataKuliahResults(mataKuliahData);
                }

                function displayMataKuliahResults(data) {
                    const dropdown = document.getElementById('mata_kuliah_dropdown');

                    if (data.length === 0) {
                        dropdown.innerHTML = `
                            <div class="px-4 py-3 text-center text-xs text-gray-500">
                                <div>Tidak ada mata kuliah ditemukan</div>
                            </div>
                        `;
                    } else {
                        dropdown.innerHTML = data.map(item => `
                            <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                                 onclick="selectMataKuliah('${item.id}', '${escapeHtml(item.nama_lengkap)}')">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="text-xs font-medium text-gray-900">${item.kode_mata_kuliah} - ${item.nama_mata_kuliah}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Semester ${item.semester_mk} • ${item.sks_mata_kuliah} SKS • ${item.jenis_mata_kuliah}
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            ${item.sks_mata_kuliah} SKS
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${item.jenis_mata_kuliah == 'WAJIB' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                            ${item.jenis_mata_kuliah}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    }

                    dropdown.classList.remove('hidden');
                }

                function selectMataKuliah(id, namaLengkap) {
                    const searchInput = document.getElementById('mata_kuliah_search');
                    const hiddenInput = document.getElementById('id_kurikulum_mata_kuliah');
                    const dropdown = document.getElementById('mata_kuliah_dropdown');

                    searchInput.value = namaLengkap;
                    hiddenInput.value = id;

                    updateMataKuliahClearButton();
                    dropdown.classList.add('hidden');

                    // Visual feedback
                    searchInput.classList.add('border-green-300', 'bg-green-50');
                    setTimeout(() => {
                        searchInput.classList.remove('border-green-300', 'bg-green-50');
                    }, 1000);
                }

                function clearMataKuliahSelection() {
                    document.getElementById('mata_kuliah_search').value = '';
                    document.getElementById('id_kurikulum_mata_kuliah').value = '';
                    document.getElementById('mata_kuliah_dropdown').classList.add('hidden');
                    document.getElementById('clear_mata_kuliah').classList.add('hidden');
                }

                function updateMataKuliahClearButton() {
                    const hiddenInput = document.getElementById('id_kurikulum_mata_kuliah');
                    const clearButton = document.getElementById('clear_mata_kuliah');

                    if (hiddenInput.value) {
                        clearButton.classList.remove('hidden');
                    } else {
                        clearButton.classList.add('hidden');
                    }
                }

                // ===============================================
                // DOSEN SEARCH FUNCTIONALITY
                // ===============================================

                function setupDosenSearch() {
                    const searchInput = document.getElementById('dosen_search');
                    if (!searchInput) return;

                    const newSearchInput = searchInput.cloneNode(true);
                    searchInput.parentNode.replaceChild(newSearchInput, searchInput);

                    newSearchInput.addEventListener('input', function() {
                        const query = this.value.toLowerCase().trim();

                        if (searchTimeouts.dosen) {
                            clearTimeout(searchTimeouts.dosen);
                        }

                        updateDosenClearButton();
                        document.getElementById('dosen_loading').classList.remove('hidden');

                        searchTimeouts.dosen = setTimeout(() => {
                            searchDosen(query);
                            document.getElementById('dosen_loading').classList.add('hidden');
                        }, 300);
                    });

                    newSearchInput.addEventListener('focus', function() {
                        if (this.value !== '') {
                            const query = this.value.toLowerCase().trim();
                            searchDosen(query);
                        } else {
                            showAllDosen();
                        }
                    });

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        const dropdown = document.getElementById('dosen_dropdown');
                        if (!newSearchInput.contains(event.target) && !dropdown.contains(event.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                }

                function searchDosen(query) {
                    if (query === '') {
                        showAllDosen();
                        return;
                    }

                    const filteredData = dosenData.filter(item => {
                        const namaMatch = item.nama && item.nama.toLowerCase().includes(query);
                        const nidnMatch = item.nidn && item.nidn.toLowerCase().includes(query);
                        const prodiMatch = item.program_studi && item.program_studi.toLowerCase().includes(query);
                        return namaMatch || nidnMatch || prodiMatch;
                    });

                    displayDosenResults(filteredData);
                }

                function showAllDosen() {
                    displayDosenResults(dosenData);
                }

                function displayDosenResults(data) {
                    const dropdown = document.getElementById('dosen_dropdown');

                    if (data.length === 0) {
                        dropdown.innerHTML = `
                            <div class="px-4 py-3 text-center text-xs text-gray-500">
                                <div>Tidak ada dosen ditemukan</div>
                            </div>
                        `;
                    } else {
                        dropdown.innerHTML = data.map(item => `
                            <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                                 onclick="selectDosen('${item.id_dosen}', '${escapeHtml(item.nama_lengkap)}')">
                                <div class="text-xs font-medium text-gray-900">${item.nama}</div>
                                <div class="text-xs text-gray-500">
                                    ${item.nidn ? `NIDN: ${item.nidn}` : 'Belum ada NIDN'}
                                    ${item.program_studi ? ` • ${item.program_studi}` : ''}
                                </div>
                            </div>
                        `).join('');
                    }

                    dropdown.classList.remove('hidden');
                }

                function selectDosen(id, namaLengkap) {
                    const searchInput = document.getElementById('dosen_search');
                    const hiddenInput = document.getElementById('id_dosen');
                    const dropdown = document.getElementById('dosen_dropdown');

                    searchInput.value = namaLengkap;
                    hiddenInput.value = id;

                    updateDosenClearButton();
                    dropdown.classList.add('hidden');

                    // Visual feedback
                    searchInput.classList.add('border-green-300', 'bg-green-50');
                    setTimeout(() => {
                        searchInput.classList.remove('border-green-300', 'bg-green-50');
                    }, 1000);
                }

                function clearDosenSelection() {
                    document.getElementById('dosen_search').value = '';
                    document.getElementById('id_dosen').value = '';
                    document.getElementById('dosen_dropdown').classList.add('hidden');
                    document.getElementById('clear_dosen').classList.add('hidden');
                }

                function updateDosenClearButton() {
                    const hiddenInput = document.getElementById('id_dosen');
                    const clearButton = document.getElementById('clear_dosen');

                    if (hiddenInput.value) {
                        clearButton.classList.remove('hidden');
                    } else {
                        clearButton.classList.add('hidden');
                    }
                }

                // ===============================================
                // MODAL FUNCTIONS
                // ===============================================

                function openModal(type) {
                    const modal = document.getElementById('kelasKuliahModal');
                    const form = document.getElementById('kelasKuliahForm');
                    const modalTitle = document.getElementById('modalTitle');
                    const submitText = document.getElementById('submitText');

                    form.reset();
                    clearProgramStudiSelection();
                    clearDosenSelection();

                    if (type === 'create') {
                        form.action = '{{ route('kelas-kuliah.store') }}';
                        form.querySelector('input[name="_method"]')?.remove();
                        modalTitle.textContent = 'Tambah Kelas Kuliah';
                        submitText.textContent = 'Simpan';
                    }

                    modal.classList.remove('hidden');

                    setTimeout(() => {
                        setupProgramStudiSearch();
                        setupKurikulumSearch();
                        setupMataKuliahSearch();
                        setupDosenSearch();
                        document.getElementById('nama_kelas_kuliah').focus();
                    }, 100);
                }

                function openEditModal(id, namaKelas, namaRuangan, kapasitas, jamMulai, jamAkhir, idKurikulumMataKuliah,
                    idSemester, idDosen) {
                    const modal = document.getElementById('kelasKuliahModal');
                    const form = document.getElementById('kelasKuliahForm');
                    const modalTitle = document.getElementById('modalTitle');
                    const submitText = document.getElementById('submitText');

                    form.action = `/kelas-kuliah/${id}`;

                    let methodInput = form.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        form.appendChild(methodInput);
                    }
                    methodInput.value = 'PUT';

                    // Set basic values
                    document.getElementById('nama_kelas_kuliah').value = namaKelas || '';
                    document.getElementById('nama_ruangan').value = namaRuangan || '';
                    document.getElementById('kapasitas').value = kapasitas || '';
                    document.getElementById('jam_mulai').value = jamMulai || '';
                    document.getElementById('jam_akhir').value = jamAkhir || '';

                    modalTitle.textContent = 'Edit Kelas Kuliah';
                    submitText.textContent = 'Update';

                    modal.classList.remove('hidden');

                    setTimeout(() => {
                        setupProgramStudiSearch();
                        setupKurikulumSearch();
                        setupMataKuliahSearch();
                        setupDosenSearch();

                        // Load existing data for edit mode
                        if (idKurikulumMataKuliah && idDosen) {
                            loadEditData(idKurikulumMataKuliah, idDosen);
                        }

                        document.getElementById('nama_kelas_kuliah').focus();
                    }, 100);
                }

                // Fungsi baru untuk memuat data edit
                async function loadEditData(idKurikulumMataKuliah, idDosen) {
                    try {
                        // Ambil detail mata kuliah dan cascade data
                        const [mataKuliahResponse, dosenResponse] = await Promise.all([
                            fetch(`/kelas-kuliah/api/kurikulum-mata-kuliah/${idKurikulumMataKuliah}`),
                            fetch(`/kelas-kuliah/api/dosen/${idDosen}`)
                        ]);

                        if (mataKuliahResponse.ok && dosenResponse.ok) {
                            const mataKuliahData = await mataKuliahResponse.json();
                            const dosenData = await dosenResponse.json();

                            // Set Program Studi
                            const programStudiInput = document.getElementById('program_studi_search');
                            const selectedProgramStudi = document.getElementById('selected_program_studi');

                            programStudiInput.value = mataKuliahData.program_studi_lengkap;
                            selectedProgramStudi.value = mataKuliahData.id_program_studi;
                            updateProgramStudiClearButton();

                            // Load dan set Kurikulum
                            await loadKurikulumData(mataKuliahData.id_program_studi);

                            const kurikulumInput = document.getElementById('kurikulum_search');
                            const selectedKurikulum = document.getElementById('selected_kurikulum');

                            kurikulumInput.disabled = false;
                            kurikulumInput.placeholder = 'Cari kurikulum...';
                            kurikulumInput.value = mataKuliahData.kurikulum_lengkap;
                            selectedKurikulum.value = mataKuliahData.id_kurikulum;
                            updateKurikulumClearButton();

                            // Load dan set Mata Kuliah
                            await loadMataKuliahData(mataKuliahData.id_kurikulum);

                            const mataKuliahInput = document.getElementById('mata_kuliah_search');
                            const idKurikulumMataKuliahInput = document.getElementById('id_kurikulum_mata_kuliah');

                            mataKuliahInput.disabled = false;
                            mataKuliahInput.placeholder = 'Cari mata kuliah...';
                            mataKuliahInput.value = mataKuliahData.mata_kuliah_lengkap;
                            idKurikulumMataKuliahInput.value = idKurikulumMataKuliah;
                            updateMataKuliahClearButton();

                            // Set Dosen
                            const dosenInput = document.getElementById('dosen_search');
                            const idDosenInput = document.getElementById('id_dosen');

                            dosenInput.value = dosenData.nama_lengkap;
                            idDosenInput.value = idDosen;
                            updateDosenClearButton();

                        }
                    } catch (error) {
                        console.error('Error loading edit data:', error);
                    }
                }

                function closeModal() {
                    document.getElementById('kelasKuliahModal').classList.add('hidden');
                }

                function confirmDelete(id, name) {
                    if (confirm(`Yakin ingin menghapus kelas kuliah "${name}"?`)) {
                        const form = document.getElementById('delete-form');
                        form.action = `/kelas-kuliah/${id}`;
                        form.submit();
                    }
                }

                // Close modal with Escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeModal();
                    }
                });

                // Validate time inputs
                document.getElementById('jam_akhir').addEventListener('change', function() {
                    const jamMulai = document.getElementById('jam_mulai').value;
                    const jamAkhir = this.value;

                    if (jamMulai && jamAkhir && jamAkhir <= jamMulai) {
                        alert('Jam akhir harus setelah jam mulai');
                        this.value = '';
                    }
                });

                // Form validation
                document.getElementById('kelasKuliahForm').addEventListener('submit', function(e) {
                    const requiredFields = [
                        'nama_kelas_kuliah', 'nama_ruangan', 'kapasitas',
                        'id_kurikulum_mata_kuliah', 'id_dosen'
                    ];

                    for (let fieldName of requiredFields) {
                        const field = document.getElementById(fieldName);
                        if (!field.value.trim()) {
                            e.preventDefault();
                            let fieldLabel = fieldName;

                            switch (fieldName) {
                                case 'id_kurikulum_mata_kuliah':
                                    fieldLabel = 'Mata Kuliah';
                                    break;
                                case 'id_dosen':
                                    fieldLabel = 'Dosen';
                                    break;
                                default:
                                    fieldLabel = fieldName.replace('_', ' ');
                            }

                            alert(`Field ${fieldLabel} tidak boleh kosong`);

                            if (fieldName === 'id_kurikulum_mata_kuliah') {
                                document.getElementById('mata_kuliah_search').focus();
                            } else if (fieldName === 'id_dosen') {
                                document.getElementById('dosen_search').focus();
                            } else {
                                field.focus();
                            }
                            return false;
                        }
                    }

                    // Validate kapasitas
                    const kapasitas = parseInt(document.getElementById('kapasitas').value);
                    if (kapasitas < 1 || kapasitas > 200) {
                        e.preventDefault();
                        alert('Kapasitas kelas harus antara 1 - 200');
                        document.getElementById('kapasitas').focus();
                        return false;
                    }
                });

                // Helper function
                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                // Make functions globally accessible
                window.selectProgramStudi = selectProgramStudi;
                window.selectKurikulum = selectKurikulum;
                window.selectMataKuliah = selectMataKuliah;
                window.selectDosen = selectDosen;
                window.clearProgramStudiSelection = clearProgramStudiSelection;
                window.clearKurikulumSelection = clearKurikulumSelection;
                window.clearMataKuliahSelection = clearMataKuliahSelection;
                window.clearDosenSelection = clearDosenSelection;
                window.openModal = openModal;
                window.openEditModal = openEditModal;
                window.closeModal = closeModal;
                window.confirmDelete = confirmDelete;
            @endif

            // Make global functions accessible
            window.changeSemester = changeSemester;
            @if ($selectedSemester)
                window.filterByProgramStudi = filterByProgramStudi;
            @endif
        </script>

        <script>
            // Fixed Client-side pagination functionality
            class ClientPagination {
                constructor() {
                    this.currentPage = 1;
                    this.itemsPerPage = 10; // ✅ Fix: Match dengan HTML default
                    this.totalItems = 0;
                    this.visibleItems = [];
                    this.allRows = [];

                    this.init();
                }

                init() {
                    // Setup event listeners
                    const itemsPerPageSelect = document.getElementById('items-per-page');
                    if (itemsPerPageSelect) {
                        itemsPerPageSelect.addEventListener('change', (e) => {
                            this.itemsPerPage = parseInt(e.target.value);
                            this.currentPage = 1;
                            this.updatePagination();
                        });
                    }

                    // Initial pagination update
                    this.updatePagination();
                }

                updateRowsData() {
                    const tbody = document.getElementById('kelasKuliahTableBody');
                    if (!tbody) return;

                    this.allRows = Array.from(tbody.querySelectorAll('tr[data-searchable]'));
                    this.updateVisibleRows();
                }

                updateVisibleRows() {
                    // ✅ Fix: Hanya ambil rows yang visible setelah filter (bukan yang hidden oleh pagination)
                    this.visibleItems = this.allRows.filter(row => {
                        const isHiddenByFilter = row.style.display === 'none' && !row.hasAttribute(
                            'data-pagination-hidden');
                        return !isHiddenByFilter;
                    });
                    this.totalItems = this.visibleItems.length;
                }

                updatePagination() {
                    this.updateRowsData(); // ✅ Update data terlebih dahulu

                    // Hide pagination if no items
                    const paginationContainer = document.getElementById('pagination-container');
                    if (!paginationContainer) return;

                    if (this.totalItems === 0) {
                        paginationContainer.style.display = 'none';
                        return;
                    } else {
                        paginationContainer.style.display = 'flex';
                    }

                    // Calculate pagination
                    const totalPages = Math.ceil(this.totalItems / this.itemsPerPage);

                    // Ensure current page is valid
                    if (this.currentPage > totalPages) {
                        this.currentPage = Math.max(1, totalPages);
                    }

                    // Show/hide rows based on current page
                    this.showPageItems();

                    // Update pagination info
                    this.updatePaginationInfo();

                    // Update pagination buttons
                    this.updatePaginationButtons(totalPages);
                }

                showPageItems() {
                    if (this.visibleItems.length === 0) return;

                    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
                    const endIndex = startIndex + this.itemsPerPage;

                    // ✅ Fix: Reset pagination hiding untuk semua visible items dulu
                    this.visibleItems.forEach(row => {
                        row.removeAttribute('data-pagination-hidden');
                        row.style.display = ''; // Show all filtered items first
                    });

                    // ✅ Fix: Hide items yang tidak di halaman saat ini
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

                // Method to be called when search/filter changes
                onFilterChange() {
                    this.currentPage = 1;
                    this.updatePagination();
                }
            }

            // ✅ Fix: Update filterTable function untuk integrasi yang benar
            function filterTable() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                const prodiFilter = document.getElementById('programStudiFilter').value;
                const tbody = document.getElementById('kelasKuliahTableBody');
                const rows = tbody.querySelectorAll('tr[data-searchable]');
                let visibleCount = 0;

                rows.forEach(row => {
                    // ✅ Reset semua attribute pagination dulu
                    row.removeAttribute('data-pagination-hidden');

                    const kelasCell = row.querySelector('.searchable-kelas');
                    const matkulCell = row.querySelector('.searchable-matkul');
                    const dosenCell = row.querySelector('.searchable-dosen');
                    const ruanganCell = row.querySelector('.searchable-ruangan');
                    const rowProdi = row.getAttribute('data-prodi');

                    const kelasText = kelasCell ? kelasCell.textContent.toLowerCase() : '';
                    const matkulText = matkulCell ? matkulCell.textContent.toLowerCase() : '';
                    const dosenText = dosenCell ? dosenCell.textContent.toLowerCase() : '';
                    const ruanganText = ruanganCell ? ruanganCell.textContent.toLowerCase() : '';

                    const matchesSearch = searchTerm === '' ||
                        kelasText.includes(searchTerm) ||
                        matkulText.includes(searchTerm) ||
                        dosenText.includes(searchTerm) ||
                        ruanganText.includes(searchTerm);
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

                // ✅ Update pagination setelah filter selesai
                if (window.pagination) {
                    window.pagination.onFilterChange();
                }
            }

            // ✅ Initialize pagination dengan timing yang benar
            let pagination;
            document.addEventListener('DOMContentLoaded', function() {
                // ✅ Tunggu sedikit untuk memastikan semua elemen sudah ready
                setTimeout(() => {
                    pagination = new ClientPagination();
                    window.pagination = pagination; // Make it globally accessible
                }, 100);
            });
        </script>
    @endpush
@endsection
