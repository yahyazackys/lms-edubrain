@extends('layouts.app')

@section('title', 'Manajemen Kelompok KKN')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Manajemen Kelompok KKN</h1>
                            <p class="text-sm text-gray-600">Kelola kelompok KKN dan assign DPL</p>
                        </div>

                        @if ($selectedSemester)
                            <div class="mt-4 sm:mt-0">
                                <button onclick="openModal('create')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Kelompok KKN
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

                        <!-- Search -->
                        @if ($selectedSemester)
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" id="searchInput" placeholder="Cari kelompok KKN..."
                                        class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                </div>
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
                        <p class="text-sm text-gray-500 mb-4">Pilih semester dari dropdown di atas untuk mengelola kelompok
                            KKN</p>
                    </div>
                </div>
            @else
                <!-- Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="kelompokKknTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kelompok
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Lokasi & Periode
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        DPL
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Anggota
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Alamat Lengkap Lokasi
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="kelompokKknTableBody">
                                @forelse($kelompokKkns as $kelompok)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 kelompok-kkn-row"
                                        data-searchable>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="searchable-kelompok">
                                                <div class="text-xs font-semibold text-gray-900">
                                                    {{ $kelompok->nama_kelompok }}
                                                </div>
                                                @if ($kelompok->target_program_kerja)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ Str::limit($kelompok->target_program_kerja, 50) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="searchable-lokasi">
                                                <div class="text-xs font-medium text-gray-900">
                                                    {{ $kelompok->lokasi }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ \Carbon\Carbon::parse($kelompok->periode_mulai)->format('d M Y') }} -
                                                    {{ \Carbon\Carbon::parse($kelompok->periode_selesai)->format('d M Y') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="searchable-dpl">
                                                <div class="text-xs font-medium text-gray-900">
                                                    {{ $kelompok->dpl->pengguna->nama ?? 'N/A' }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $kelompok->dpl->nidn ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs">
                                                @php
                                                    $totalAnggota = $kelompok->kknDetails->count();
                                                    $ketua = $kelompok->kknDetails
                                                        ->where('peran_kelompok', 'KETUA')
                                                        ->first();
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $totalAnggota }} Anggota
                                                </span>
                                                @if ($ketua)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Ketua:
                                                        {{ $ketua->pesertaBimbingan->registrasiMahasiswa->mahasiswa->pengguna->nama ?? 'N/A' }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="searchable-dpl">
                                                <div class="text-xs font-medium text-gray-900">
                                                    {{ $kelompok->alamat_lokasi ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">

                                                <div class="relative group">
                                                    {{-- <a href="{{ route('bimbingan.kkn.peserta.index', [
                                                        'semester' => $selectedSemester->id_semester,
                                                        'kelompok' => $kelompok->id_kelompok_kkn,
                                                    ]) }}"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="text-xs fas fa-users"></i>
                                                    </a> --}}
                                                    <a href="{{ route('bimbingan.kkn.kelompok.show', $kelompok->id_kelompok_kkn) }}"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="text-xs fas fa-users"></i>
                                                    </a>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Kelola Anggota
                                                        <div
                                                            class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Tombol Edit --}}
                                                <div class="relative group">
                                                    <button
                                                        onclick="openEditModal(
                                                        '{{ $kelompok->id_kelompok_kkn }}',
                                                        '{{ addslashes($kelompok->nama_kelompok) }}',
                                                        '{{ addslashes($kelompok->lokasi) }}',
                                                        '{{ addslashes($kelompok->alamat_lokasi) }}',
                                                        '{{ $kelompok->periode_mulai ? \Carbon\Carbon::parse($kelompok->periode_mulai)->format('Y-m-d') : '' }}',
                                                        '{{ $kelompok->periode_selesai ? \Carbon\Carbon::parse($kelompok->periode_selesai)->format('Y-m-d') : '' }}',
                                                        '{{ addslashes($kelompok->target_program_kerja) }}',
                                                        '{{ $kelompok->id_dpl }}',
                                                        '{{ $kelompok->id_semester }}',
                                                    )"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="text-xs fas fa-edit"></i>
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Edit Kelompok
                                                        <div
                                                            class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Tombol Hapus --}}
                                                <div class="relative group">
                                                    <button
                                                        onclick="confirmDelete('{{ $kelompok->id_kelompok_kkn }}', '{{ addslashes($kelompok->nama_kelompok) }}')"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Hapus Kelompok
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
                                                <p class="text-lg font-medium text-gray-900 mb-2">Belum ada kelompok KKN
                                                </p>
                                                <p class="text-xs text-gray-500">Tambahkan kelompok KKN untuk semester
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
            <div id="kelompokKknModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

                    <div
                        class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-4xl mx-auto max-h-[90vh] overflow-y-auto">
                        <form id="kelompokKknForm" method="POST" action="{{ route('bimbingan.kkn.kelompok.store') }}">
                            @csrf
                            <input type="hidden" name="id_semester" value="{{ $selectedSemester->id_semester }}">


                            <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 id="modalTitle" class="text-lg font-heading font-semibold text-gray-900">
                                        Tambah Kelompok KKN
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
                                    <h4 class="text-sm font-semibold text-gray-900 mb-4 border-b pb-2">Informasi Kelompok
                                    </h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Nama Kelompok <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="nama_kelompok" id="nama_kelompok"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="Contoh: Kelompok 1, Tim A" maxlength="100" required>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Lokasi KKN <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="lokasi" id="lokasi"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="Contoh: Desa Sukamaju, Kec. Sukasari" maxlength="200" required>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Alamat Lengkap Lokasi
                                        </label>
                                        <textarea name="alamat_lokasi" id="alamat_lokasi" rows="3"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="Alamat lengkap lokasi KKN..."></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Periode Mulai <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="periode_mulai" id="periode_mulai"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            required>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Periode Selesai <span class="text-red-500">*</span>
                                        </label>
                                        <input type="date" name="periode_selesai" id="periode_selesai"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            required>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Target Program Kerja
                                        </label>
                                        <textarea name="target_program_kerja" id="target_program_kerja" rows="3"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="Jelaskan target program kerja yang akan dilaksanakan..."></textarea>
                                    </div>

                                    <!-- DPL Search -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Dosen Pembimbing Lapangan (DPL) <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text" id="dpl_search"
                                                placeholder="Cari DPL berdasarkan nama, NIDN, atau program studi..."
                                                autocomplete="off"
                                                class="w-full text-xs px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">

                                            <button type="button" id="clear_dpl" onclick="clearDplSelection()"
                                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6m0 12L6 6"></path>
                                                </svg>
                                            </button>

                                            <input type="hidden" name="id_dpl" id="id_dpl" required>

                                            <div id="dpl_dropdown"
                                                class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                            </div>

                                            <div id="dpl_loading" class="absolute right-8 top-2 hidden">
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

                                <!-- Info Semester -->
                                <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-xs text-gray-600">
                                            Kelompok KKN ini akan dibuat untuk semester:
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
            let dplData = [];
            let searchTimeouts = {};

            // Load initial data
            document.addEventListener('DOMContentLoaded', function() {
                loadDplData();
            });

            // Load DPL data
            function loadDplData() {
                fetch('/bimbingan/kkn/kelompok/api/dosen')
                    .then(response => response.json())
                    .then(data => {
                        dplData = data;
                    })
                    .catch(error => {
                        console.error('Error loading DPL:', error);
                    });
            }

            // Change semester function
            function changeSemester() {
                const semesterId = document.getElementById('semesterFilter').value;
                if (semesterId) {
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.set('semester', semesterId);
                    window.location.href = window.location.pathname + '?' + currentParams.toString();
                } else {
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.delete('semester');
                    window.location.href = window.location.pathname + (currentParams.toString() ? '?' + currentParams
                        .toString() : '');
                }
            }

            @if ($selectedSemester)
                // REALTIME SEARCH & FILTER FUNCTIONALITY
                document.getElementById('searchInput').addEventListener('input', function() {
                    filterTable();
                });

                function filterTable() {
                    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                    const tbody = document.getElementById('kelompokKknTableBody');
                    const rows = tbody.querySelectorAll('tr[data-searchable]');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const kelompokCell = row.querySelector('.searchable-kelompok');
                        const lokasiCell = row.querySelector('.searchable-lokasi');
                        const dplCell = row.querySelector('.searchable-dpl');

                        const kelompokText = kelompokCell ? kelompokCell.textContent.toLowerCase() : '';
                        const lokasiText = lokasiCell ? lokasiCell.textContent.toLowerCase() : '';
                        const dplText = dplCell ? dplCell.textContent.toLowerCase() : '';

                        const matchesSearch = searchTerm === '' ||
                            kelompokText.includes(searchTerm) ||
                            lokasiText.includes(searchTerm) ||
                            dplText.includes(searchTerm);

                        if (matchesSearch) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    updateEmptyState(visibleCount, searchTerm);
                    if (window.pagination) {
                        window.pagination.onFilterChange();
                    }
                }

                function updateEmptyState(visibleCount, searchTerm) {
                    const tbody = document.getElementById('kelompokKknTableBody');
                    let emptyRow = tbody.querySelector('tr[data-empty-search]');

                    if (emptyRow) {
                        emptyRow.remove();
                    }

                    if (visibleCount === 0 && searchTerm !== '') {
                        emptyRow = document.createElement('tr');
                        emptyRow.setAttribute('data-empty-search', 'true');

                        emptyRow.innerHTML = `
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900 mb-2">Tidak ada hasil ditemukan</p>
                                    <p class="text-xs text-gray-500">untuk pencarian "${searchTerm}"</p>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(emptyRow);
                    }
                }

                // ===============================================
                // DPL SEARCH FUNCTIONALITY
                // ===============================================

                function setupDplSearch() {
                    const searchInput = document.getElementById('dpl_search');
                    if (!searchInput) return;

                    const newSearchInput = searchInput.cloneNode(true);
                    searchInput.parentNode.replaceChild(newSearchInput, searchInput);

                    newSearchInput.addEventListener('input', function() {
                        const query = this.value.toLowerCase().trim();

                        if (searchTimeouts.dpl) {
                            clearTimeout(searchTimeouts.dpl);
                        }

                        updateDplClearButton();
                        document.getElementById('dpl_loading').classList.remove('hidden');

                        searchTimeouts.dpl = setTimeout(() => {
                            searchDpl(query);
                            document.getElementById('dpl_loading').classList.add('hidden');
                        }, 300);
                    });

                    newSearchInput.addEventListener('focus', function() {
                        if (this.value !== '') {
                            const query = this.value.toLowerCase().trim();
                            searchDpl(query);
                        } else {
                            showAllDpl();
                        }
                    });

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        const dropdown = document.getElementById('dpl_dropdown');
                        if (!newSearchInput.contains(event.target) && !dropdown.contains(event.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                }

                function searchDpl(query) {
                    if (query === '') {
                        showAllDpl();
                        return;
                    }

                    const filteredData = dplData.filter(item => {
                        const namaMatch = item.nama && item.nama.toLowerCase().includes(query);
                        const nidnMatch = item.nidn && item.nidn.toLowerCase().includes(query);
                        const prodiMatch = item.program_studi && item.program_studi.toLowerCase().includes(query);
                        return namaMatch || nidnMatch || prodiMatch;
                    });

                    displayDplResults(filteredData);
                }

                function showAllDpl() {
                    displayDplResults(dplData);
                }

                function displayDplResults(data) {
                    const dropdown = document.getElementById('dpl_dropdown');

                    if (data.length === 0) {
                        dropdown.innerHTML = `
                            <div class="px-4 py-3 text-center text-xs text-gray-500">
                                <div>Tidak ada dosen ditemukan</div>
                            </div>
                        `;
                    } else {
                        dropdown.innerHTML = data.map(item => `
                            <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                                 onclick="selectDpl('${item.id_dosen}', '${escapeHtml(item.nama_lengkap)}')">
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

                function selectDpl(id, namaLengkap) {
                    const searchInput = document.getElementById('dpl_search');
                    const hiddenInput = document.getElementById('id_dpl');
                    const dropdown = document.getElementById('dpl_dropdown');

                    searchInput.value = namaLengkap;
                    hiddenInput.value = id;

                    updateDplClearButton();
                    dropdown.classList.add('hidden');

                    // Visual feedback
                    searchInput.classList.add('border-green-300', 'bg-green-50');
                    setTimeout(() => {
                        searchInput.classList.remove('border-green-300', 'bg-green-50');
                    }, 1000);
                }

                function clearDplSelection() {
                    document.getElementById('dpl_search').value = '';
                    document.getElementById('id_dpl').value = '';
                    document.getElementById('dpl_dropdown').classList.add('hidden');
                    document.getElementById('clear_dpl').classList.add('hidden');
                }

                function updateDplClearButton() {
                    const hiddenInput = document.getElementById('id_dpl');
                    const clearButton = document.getElementById('clear_dpl');

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
                    const modal = document.getElementById('kelompokKknModal');
                    const form = document.getElementById('kelompokKknForm');
                    const modalTitle = document.getElementById('modalTitle');
                    const submitText = document.getElementById('submitText');

                    form.reset();
                    clearDplSelection();

                    if (type === 'create') {
                        form.action = '{{ route('bimbingan.kkn.kelompok.store') }}';
                        form.querySelector('input[name="_method"]')?.remove();
                        modalTitle.textContent = 'Tambah Kelompok KKN';
                        submitText.textContent = 'Simpan';
                    }

                    modal.classList.remove('hidden');

                    setTimeout(() => {
                        setupDplSearch();
                        document.getElementById('nama_kelompok').focus();
                    }, 100);
                }

                function openEditModal(id, namaKelompok, lokasi, alamatLokasi, periodeMulai, periodeSelesai, targetProgram,
                    idDpl, idSemester) {
                    const modal = document.getElementById('kelompokKknModal');
                    const form = document.getElementById('kelompokKknForm');
                    const modalTitle = document.getElementById('modalTitle');
                    const submitText = document.getElementById('submitText');

                    form.action = `/bimbingan/kkn/kelompok/${id}`;

                    let methodInput = form.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        form.appendChild(methodInput);
                    }
                    methodInput.value = 'PUT';

                    // Set values
                    document.getElementById('nama_kelompok').value = namaKelompok || '';
                    document.getElementById('lokasi').value = lokasi || '';
                    document.getElementById('alamat_lokasi').value = alamatLokasi || '';
                    document.getElementById('periode_mulai').value = periodeMulai || '';
                    document.getElementById('periode_selesai').value = periodeSelesai || '';
                    document.getElementById('target_program_kerja').value = targetProgram || '';

                    modalTitle.textContent = 'Edit Kelompok KKN';
                    submitText.textContent = 'Update';

                    modal.classList.remove('hidden');

                    setTimeout(() => {
                        setupDplSearch();

                        // Load existing DPL data for edit mode
                        if (idDpl) {
                            loadEditDplData(idDpl);
                        }

                        document.getElementById('nama_kelompok').focus();
                    }, 100);
                }

                // Load DPL data untuk edit
                async function loadEditDplData(idDpl) {
                    try {
                        const response = await fetch(`/bimbingan/kkn/kelompok/api/dosen/${idDpl}`);
                        if (response.ok) {
                            const dplData = await response.json();

                            const dplInput = document.getElementById('dpl_search');
                            const idDplInput = document.getElementById('id_dpl');

                            dplInput.value = dplData.nama_lengkap;
                            idDplInput.value = idDpl;
                            updateDplClearButton();
                        }
                    } catch (error) {
                        console.error('Error loading DPL data:', error);
                    }
                }

                function closeModal() {
                    document.getElementById('kelompokKknModal').classList.add('hidden');
                }

                function confirmDelete(id, name) {
                    if (confirm(`Yakin ingin menghapus kelompok KKN "${name}"?`)) {
                        const form = document.getElementById('delete-form');
                        form.action = `/bimbingan/kkn/kelompok/${id}`;
                        form.submit();
                    }
                }

                // Close modal with Escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeModal();
                    }
                });

                // Validate date inputs
                document.getElementById('periode_selesai').addEventListener('change', function() {
                    const periodeMulai = document.getElementById('periode_mulai').value;
                    const periodeSelesai = this.value;

                    if (periodeMulai && periodeSelesai && periodeSelesai <= periodeMulai) {
                        alert('Periode selesai harus setelah periode mulai');
                        this.value = '';
                    }
                });

                // Form validation
                document.getElementById('kelompokKknForm').addEventListener('submit', function(e) {
                    const requiredFields = [
                        'nama_kelompok', 'lokasi', 'periode_mulai', 'periode_selesai', 'id_dpl'
                    ];

                    for (let fieldName of requiredFields) {
                        const field = document.getElementById(fieldName);
                        if (!field.value.trim()) {
                            e.preventDefault();
                            let fieldLabel = fieldName;

                            switch (fieldName) {
                                case 'id_dpl':
                                    fieldLabel = 'DPL';
                                    break;
                                default:
                                    fieldLabel = fieldName.replace('_', ' ');
                            }

                            alert(`Field ${fieldLabel} tidak boleh kosong`);

                            if (fieldName === 'id_dpl') {
                                document.getElementById('dpl_search').focus();
                            } else {
                                field.focus();
                            }
                            return false;
                        }
                    }
                });

                // Helper function
                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                // Make functions globally accessible
                window.selectDpl = selectDpl;
                window.clearDplSelection = clearDplSelection;
                window.openModal = openModal;
                window.openEditModal = openEditModal;
                window.closeModal = closeModal;
                window.confirmDelete = confirmDelete;
            @endif

            // Make global functions accessible
            window.changeSemester = changeSemester;
        </script>

        <script>
            // Client-side pagination functionality (same as kelas kuliah)
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
                    const tbody = document.getElementById('kelompokKknTableBody');
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

                    // Page numbers
                    const startPage = Math.max(1, this.currentPage - 2);
                    const endPage = Math.min(totalPages, this.currentPage + 2);

                    if (startPage > 1) {
                        container.appendChild(this.createPaginationButton('1', true, () => {
                            this.currentPage = 1;
                            this.updatePagination();
                        }));

                        if (startPage > 2) {
                            container.appendChild(this.createPaginationButton('...', false));
                        }
                    }

                    for (let i = startPage; i <= endPage; i++) {
                        const isActive = i === this.currentPage;
                        container.appendChild(this.createPaginationButton(i.toString(), true, () => {
                            this.currentPage = i;
                            this.updatePagination();
                        }, isActive));
                    }

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
