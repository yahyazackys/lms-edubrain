@extends('layouts.app')

@section('title', 'Manajemen Pembimbing Akademik')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Manajemen Pembimbing Akademik</h1>
                            <p class="text-sm text-gray-600">Kelola penugasan pembimbing akademik mahasiswa per semester</p>
                        </div>
                    </div>
                </div>

                <!-- Semester Selection -->
                <div class="px-6 py-4 bg-gray-100 border-t border-blue-100">
                    <form method="GET" action="{{ route('pembimbing-akademik.index') }}" id="semesterForm">
                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                            <label class="text-xs font-medium">Pilih Periode Semester:</label>
                            <select name="semester" id="semesterSelect"
                                class="w-full sm:w-auto py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent bg-white"
                                onchange="document.getElementById('semesterForm').submit()">
                                <option value="">-- Pilih Semester --</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id_semester }}"
                                        {{ request('semester') == $semester->id_semester ? 'selected' : '' }}>
                                        {{ $semester->nama_semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                @if ($selectedSemester)
                    <!-- Filters -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                                    <input type="text" id="searchInput"
                                        placeholder="Cari NIM/NIDN atau nama mahasiswa/dosen..."
                                        class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                </div>
                            </div>

                            <!-- Status PA Filter -->
                            <div>
                                <select id="statusPaFilter"
                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Semua Status PA</option>
                                    @foreach ($statusPaOptions as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
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
                        </div>
                    </div>
                @endif
            </div>

            @if (!$selectedSemester)
                <!-- Empty State - No Semester Selected -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-search w-16 h-16 text-gray-400 mb-4 mx-auto text-6xl"></i>
                            <p class="text-lg font-medium text-gray-900 mb-2">Pilih Periode Semester</p>
                            <p class="text-sm text-gray-500">Pilih periode semester terlebih dahulu untuk melihat data
                                pembimbing akademik</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="paTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mahasiswa
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Angkatan
                                    </th>
                                    {{-- <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Semester
                                    </th> --}}
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pembimbing Akademik
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        SK & Tanggal
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status PA
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="paTableBody">
                                @forelse($mahasiswas as $mahasiswa)
                                    @php
                                        $pa = $mahasiswa->pa_info;
                                        $statusPa = $pa ? $pa->status_pa : 'BELUM';
                                        // Add null checks for dosen relationship
                                        $dosen = $pa && $pa->dosen ? $pa->dosen : null;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 pa-row" data-searchable
                                        data-nim="{{ $mahasiswa->nim }}" data-nama="{{ $mahasiswa->pengguna->nama }}"
                                        data-nidn="{{ $dosen ? $dosen->nidn : '' }}"
                                        data-namaDosen="{{ $dosen && $dosen->pengguna ? $dosen->pengguna->nama : '' }}"
                                        data-angkatan="{{ $mahasiswa->angkatan }}" data-status-pa="{{ $statusPa }}">

                                        <!-- Mahasiswa -->
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
                                                <div class="ml-4">
                                                    <div class="text-xs font-medium text-gray-900">{{ $mahasiswa->nim }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ $mahasiswa->pengguna->nama }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $mahasiswa->programStudi->nama_program_studi }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Angkatan -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs font-medium text-gray-900">{{ $mahasiswa->angkatan }}</div>
                                        </td>

                                        <!-- Semester -->
                                        {{-- <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs font-medium text-gray-900">
                                                {{ $mahasiswa->semester_mahasiswa }}
                                            </div>
                                        </td> --}}

                                        <!-- Pembimbing Akademik -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($pa && $dosen)
                                                <div>
                                                    <div class="text-xs font-medium text-gray-900">
                                                        {{ trim(($dosen->gelar_depan ? $dosen->gelar_depan . ' ' : '') . ($dosen->pengguna ? $dosen->pengguna->nama : 'Nama tidak tersedia') . ($dosen->gelar_belakang ? ', ' . $dosen->gelar_belakang : '')) }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">NIDN: {{ $dosen->nidn }}</div>
                                                </div>
                                            @elseif ($pa && !$dosen)
                                                <div class="text-xs text-gray-500">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        Data Dosen Tidak Ditemukan
                                                    </span>
                                                </div>
                                            @else
                                                <div class="text-xs text-gray-500">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Belum Ditentukan
                                                    </span>
                                                </div>
                                            @endif
                                        </td>

                                        <!-- SK & Tanggal -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($pa)
                                                <div class="text-xs text-gray-900">
                                                    {{ $pa->nomor_sk ?? '' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $pa->tanggal_sk ? \Carbon\Carbon::parse($pa->tanggal_sk)->format('d/m/Y') : '' }}
                                                </div>
                                            @else
                                                <div class="text-xs text-gray-500">-</div>
                                            @endif
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($pa)
                                                @php
                                                    $statusClass =
                                                        $pa->status_pa === 'AKTIF'
                                                            ? 'bg-green-100 text-green-800'
                                                            : 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusClass }} mt-1">
                                                    {{ $pa->status_pa ? $statusPaOptions[$pa->status_pa] : '' }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Aksi -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                @if ($pa)
                                                    <!-- Edit PA Button -->
                                                    <div class="relative group">
                                                        <button
                                                            onclick="openEditPaModal(
                                                                '{{ $pa->id_pembimbing_akademik }}',
                                                                '{{ $mahasiswa->id_mahasiswa }}',
                                                                '{{ addslashes($mahasiswa->pengguna->nama) }}',
                                                                '{{ $mahasiswa->nim }}',
                                                                '{{ $dosen ? $dosen->id_dosen : '' }}',
                                                                '{{ addslashes($pa->nomor_sk ?? '') }}',
                                                                '{{ $pa->tanggal_sk }}',
                                                                '{{ $pa->status_pa }}'
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
                                                            Edit PA
                                                            <div
                                                                class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Delete PA Button -->
                                                    <div class="relative group">
                                                        <button
                                                            onclick="confirmDeletePa('{{ $pa->id_pembimbing_akademik }}', '{{ addslashes($mahasiswa->pengguna->nama) }}', '{{ $mahasiswa->nim }}')"
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
                                                            Hapus PA
                                                            <div
                                                                class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <!-- Assign PA Button -->
                                                    <div class="relative group">
                                                        <button
                                                            onclick="openAssignPaModal('{{ $mahasiswa->id_mahasiswa }}', '{{ addslashes($mahasiswa->pengguna->nama) }}', '{{ $mahasiswa->nim }}')"
                                                            class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                                            Assign PA
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-state-row">
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium text-gray-900 mb-2">Belum ada data mahasiswa
                                                </p>
                                                <p class="text-xs text-gray-500">Tidak ada mahasiswa untuk semester yang
                                                    dipilih</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Info -->
                    @if ($mahasiswas->count() > 0)
                        <div class="px-6 py-4 border-t border-gray-200">
                            <div class="flex items-center justify-between text-xs text-gray-700">
                                <div>Total Mahasiswa: {{ $mahasiswas->count() }}</div>
                                <div class="flex space-x-4">
                                    @php
                                        $sudahPa = $mahasiswas
                                            ->filter(function ($m) {
                                                return $m->pa_info;
                                            })
                                            ->count();
                                        $belumPa = $mahasiswas->count() - $sudahPa;
                                    @endphp
                                    <span>Sudah PA: {{ $sudahPa }}</span>
                                    <span>Belum PA: {{ $belumPa }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- PA Modal -->
        <div id="paModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closePaModal()"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
                    <form id="paForm" method="POST" action="{{ route('pembimbing-akademik.store') }}">
                        @csrf

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 id="paModalTitle" class="text-lg font-heading font-semibold text-gray-900">
                                    Assign Pembimbing Akademik
                                </h3>
                                <button type="button" onclick="closePaModal()"
                                    class="sm:hidden text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Mahasiswa Info -->
                            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                <div class="text-xs font-medium" id="mahasiswaInfo">-</div>
                            </div>

                            <div class="space-y-4">
                                <!-- Hidden Fields -->
                                <input type="hidden" name="id_mahasiswa" id="id_mahasiswa">
                                <input type="hidden" name="id_semester" id="id_semester"
                                    value="{{ request('semester') }}">

                                <!-- Dosen PA -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Pilih Dosen Pembimbing Akademik <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="dosen_search"
                                            placeholder="Cari berdasarkan NIDN atau nama dosen..." autocomplete="off"
                                            class="w-full text-xs px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">

                                        <!-- Clear Button -->
                                        <button type="button" id="clear_dosen" onclick="clearDosenSelection()"
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6m0 12L6 6"></path>
                                            </svg>
                                        </button>

                                        <input type="hidden" name="id_dosen" id="id_dosen" required>

                                        <!-- Dropdown -->
                                        <div id="dosen_dropdown"
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

                                <!-- Nomor SK -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Nomor SK
                                    </label>
                                    <input type="text" name="nomor_sk" id="nomor_sk"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Nomor SK penugasan PA" maxlength="100">
                                </div>

                                <!-- Tanggal SK -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Tanggal SK
                                    </label>
                                    <input type="date" name="tanggal_sk" id="tanggal_sk"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                </div>

                                <!-- Status PA (hanya untuk edit) -->
                                <div id="statusPaContainer" class="hidden">
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Status PA <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status_pa" id="status_pa"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                        @foreach ($statusPaOptions as $key => $label)
                                            @if ($key !== 'BELUM')
                                                <option value="{{ $key }}"
                                                    {{ $key === 'AKTIF' ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                            <button type="button" onclick="closePaModal()"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                Batal
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                <span id="paSubmitText">Assign PA</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms -->
    <form id="delete-pa-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            // Global variables
            let dosenData = [];
            let searchTimeout;
            let currentEditId = null;

            // Setup realtime filters
            document.addEventListener('DOMContentLoaded', function() {
                setupRealtimeFilters();
            });

            function setupRealtimeFilters() {
                // Search input
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        filterTable();
                    });
                }

                // Filter dropdowns
                const statusFilter = document.getElementById('statusPaFilter');
                if (statusFilter) {
                    statusFilter.addEventListener('change', function() {
                        filterTable();
                    });
                }

                const angkatanFilter = document.getElementById('angkatanFilter');
                if (angkatanFilter) {
                    angkatanFilter.addEventListener('change', function() {
                        filterTable();
                    });
                }
            }

            // Filter table function
            function filterTable() {
                const searchTerm = document.getElementById('searchInput')?.value.toLowerCase().trim() || '';
                const statusPaFilter = document.getElementById('statusPaFilter')?.value || '';
                const angkatanFilter = document.getElementById('angkatanFilter')?.value || '';

                const tbody = document.getElementById('paTableBody');
                if (!tbody) return;

                const rows = tbody.querySelectorAll('tr[data-searchable]');
                let visibleCount = 0;

                rows.forEach(row => {
                    const nim = row.getAttribute('data-nim').toLowerCase();
                    const nama = row.getAttribute('data-nama').toLowerCase();
                    const nidn = row.getAttribute('data-nidn').toLowerCase();
                    const namaDosen = row.getAttribute('data-namaDosen').toLowerCase();
                    const angkatan = row.getAttribute('data-angkatan');
                    const statusPa = row.getAttribute('data-status-pa');

                    // Check search match (NIM or nama)
                    const matchesSearch = searchTerm === '' ||
                        nim.includes(searchTerm) ||
                        nidn.includes(searchTerm) ||
                        namaDosen.includes(searchTerm) ||
                        nama.includes(searchTerm);

                    // Check filter matches
                    const matchesStatusPa = statusPaFilter === '' || statusPa === statusPaFilter;
                    const matchesAngkatan = angkatanFilter === '' || angkatan === angkatanFilter;

                    const isMatch = matchesSearch && matchesStatusPa && matchesAngkatan;

                    if (isMatch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateEmptyState(visibleCount, searchTerm, statusPaFilter, angkatanFilter);
            }

            // Update empty state
            function updateEmptyState(visibleCount, searchTerm, statusPaFilter, angkatanFilter) {
                const tbody = document.getElementById('paTableBody');
                if (!tbody) return;

                let emptyRow = tbody.querySelector('tr[data-empty-search]');

                if (emptyRow) {
                    emptyRow.remove();
                }

                if (visibleCount === 0 && (searchTerm !== '' || statusPaFilter !== '' || angkatanFilter !== '')) {
                    emptyRow = document.createElement('tr');
                    emptyRow.setAttribute('data-empty-search', 'true');

                    let message = 'Tidak ada data ditemukan';
                    let detail = 'dengan kriteria filter yang dipilih';

                    emptyRow.innerHTML = `
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
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
                const searchInput = document.getElementById('searchInput');
                const statusFilter = document.getElementById('statusPaFilter');
                const angkatanFilter = document.getElementById('angkatanFilter');

                if (searchInput) searchInput.value = '';
                if (statusFilter) statusFilter.value = '';
                if (angkatanFilter) angkatanFilter.value = '';

                filterTable();
            }

            // Modal functions
            function openAssignPaModal(mahasiswaId, nama, nim) {
                const modal = document.getElementById('paModal');
                const form = document.getElementById('paForm');
                const modalTitle = document.getElementById('paModalTitle');
                const submitText = document.getElementById('paSubmitText');
                const mahasiswaInfo = document.getElementById('mahasiswaInfo');
                const statusContainer = document.getElementById('statusPaContainer');

                // Reset form
                form.reset();
                clearDosenSelection();
                currentEditId = null;

                // Setup form for create
                form.action = '{{ route('pembimbing-akademik.store') }}';
                form.querySelector('input[name="_method"]')?.remove();

                // Set values
                document.getElementById('id_mahasiswa').value = mahasiswaId;
                mahasiswaInfo.textContent = `${nama} (${nim})`;
                modalTitle.textContent = 'Assign Pembimbing Akademik';
                submitText.textContent = 'Assign PA';

                // Hide status container for new assignment
                statusContainer.classList.add('hidden');

                modal.classList.remove('hidden');

                setTimeout(() => {
                    setupDosenSearch();
                    document.getElementById('dosen_search').focus();
                }, 100);
            }

            function openEditPaModal(paId, mahasiswaId, nama, nim, dosenId, nomorSk, tanggalSk, statusPa) {
                const modal = document.getElementById('paModal');
                const form = document.getElementById('paForm');
                const modalTitle = document.getElementById('paModalTitle');
                const submitText = document.getElementById('paSubmitText');
                const mahasiswaInfo = document.getElementById('mahasiswaInfo');
                const statusContainer = document.getElementById('statusPaContainer');

                // Reset form
                form.reset();
                clearDosenSelection();
                currentEditId = paId;

                // Setup form for update
                form.action = `/pembimbing-akademik/${paId}`;

                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';

                // Set values
                document.getElementById('id_mahasiswa').value = mahasiswaId;
                document.getElementById('nomor_sk').value = nomorSk || '';
                document.getElementById('tanggal_sk').value = tanggalSk || '';
                document.getElementById('status_pa').value = statusPa || 'AKTIF';

                mahasiswaInfo.textContent = `${nama} (${nim})`;
                modalTitle.textContent = 'Edit Pembimbing Akademik';
                submitText.textContent = 'Update PA';

                // Show status container for edit
                statusContainer.classList.remove('hidden');

                modal.classList.remove('hidden');

                setTimeout(() => {
                    setupDosenSearch();

                    // Set selected dosen (will be handled by dosen search setup)
                    if (dosenId) {
                        document.getElementById('id_dosen').value = dosenId;
                        // You might want to also set the display name here
                    }

                    document.getElementById('dosen_search').focus();
                }, 100);
            }

            function closePaModal() {
                document.getElementById('paModal').classList.add('hidden');
                currentEditId = null;
            }

            // Delete function
            function confirmDeletePa(paId, nama, nim) {
                if (confirm(`Yakin ingin menghapus assignment PA untuk mahasiswa "${nama}" (NIM: ${nim})?`)) {
                    const form = document.getElementById('delete-pa-form');
                    form.action = `/pembimbing-akademik/${paId}`;
                    form.submit();
                }
            }

            // Dosen Search Functions
            function setupDosenSearch() {
                const searchInput = document.getElementById('dosen_search');
                const hiddenInput = document.getElementById('id_dosen');
                const dropdown = document.getElementById('dosen_dropdown');
                const loadingIndicator = document.getElementById('search_loading');
                const clearButton = document.getElementById('clear_dosen');

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
                        searchDosen(query);
                        loadingIndicator.classList.add('hidden');
                    }, 300);
                });

                // Focus event
                newSearchInput.addEventListener('focus', function() {
                    const currentValue = hiddenInput.value;
                    if (currentValue && this.value !== '') {
                        const query = this.value.toLowerCase().trim();
                        searchDosen(query);
                    } else {
                        loadAllDosen();
                    }
                });

                // Click outside to close
                document.addEventListener('click', function(event) {
                    if (!newSearchInput.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }

            function searchDosen(query) {
                const semesterId = document.getElementById('id_semester').value;

                if (!semesterId) {
                    document.getElementById('dosen_dropdown').innerHTML = `
                        <div class="px-4 py-3 text-center text-xs text-red-500">
                            Semester belum dipilih
                        </div>
                    `;
                    document.getElementById('dosen_dropdown').classList.remove('hidden');
                    return;
                }

                const url = new URL('/pembimbing-akademik/api/dosen-kuota', window.location.origin);
                url.searchParams.append('semester_id', semesterId);
                url.searchParams.append('search', query);

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        displayDosenResults(data);
                    })
                    .catch(error => {
                        console.error('Error searching dosen:', error);
                        document.getElementById('dosen_dropdown').innerHTML = `
                            <div class="px-4 py-3 text-center text-xs text-red-500">
                                Error loading dosen data
                            </div>
                        `;
                        document.getElementById('dosen_dropdown').classList.remove('hidden');
                    });
            }

            function loadAllDosen() {
                searchDosen('');
            }

            function displayDosenResults(data) {
                const dropdown = document.getElementById('dosen_dropdown');

                if (data.length === 0) {
                    dropdown.innerHTML = `
                        <div class="px-4 py-3 text-center text-xs text-gray-500">
                            <svg class="w-6 h-6 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <div>Tidak ada dosen dengan kuota tersisa</div>
                        </div>
                    `;
                } else {
                    dropdown.innerHTML = data.map(item => `
                        <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                             onclick="selectDosen('${item.id_dosen}', '${escapeHtml(item.nama_lengkap)}', '${escapeHtml(item.nidn)}', ${item.kuota_tersisa}, ${item.kuota_terpakai}, ${item.total_kuota})">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-medium text-gray-900">${item.nama_lengkap}</div>
                                    <div class="text-xs text-gray-500">NIDN: ${item.nidn}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-medium text-green-600">Sisa: ${item.kuota_tersisa}</div>
                                    <div class="text-xs text-gray-500">${item.kuota_terpakai}/${item.total_kuota}</div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }

                dropdown.classList.remove('hidden');
            }

            function selectDosen(id, namaLengkap, nidn, kuotaTersisa, kuotaTerpakai, totalKuota) {
                const searchInput = document.getElementById('dosen_search');
                const hiddenInput = document.getElementById('id_dosen');
                const dropdown = document.getElementById('dosen_dropdown');

                searchInput.value = `${namaLengkap} (NIDN: ${nidn}) - Sisa: ${kuotaTersisa}`;
                hiddenInput.value = id;

                updateClearButton();
                dropdown.classList.add('hidden');

                // Visual feedback
                searchInput.classList.add('border-green-300', 'bg-green-50');
                setTimeout(() => {
                    searchInput.classList.remove('border-green-300', 'bg-green-50');
                }, 1000);
            }

            function clearDosenSelection() {
                const searchInput = document.getElementById('dosen_search');
                const hiddenInput = document.getElementById('id_dosen');
                const dropdown = document.getElementById('dosen_dropdown');
                const clearButton = document.getElementById('clear_dosen');

                if (searchInput) searchInput.value = '';
                if (hiddenInput) hiddenInput.value = '';
                if (dropdown) dropdown.classList.add('hidden');
                if (clearButton) clearButton.classList.add('hidden');

                if (searchInput) searchInput.focus();
            }

            function updateClearButton() {
                const searchInput = document.getElementById('dosen_search');
                const hiddenInput = document.getElementById('id_dosen');
                const clearButton = document.getElementById('clear_dosen');

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
            document.getElementById('paForm').addEventListener('submit', function(e) {
                const dosenId = document.getElementById('id_dosen').value;

                if (!dosenId.trim()) {
                    e.preventDefault();
                    alert('Silakan pilih dosen pembimbing akademik');
                    document.getElementById('dosen_search').focus();
                    return false;
                }
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closePaModal();
                }
            });

            // Make functions global
            window.openAssignPaModal = openAssignPaModal;
            window.openEditPaModal = openEditPaModal;
            window.closePaModal = closePaModal;
            window.confirmDeletePa = confirmDeletePa;
            window.selectDosen = selectDosen;
            window.clearDosenSelection = clearDosenSelection;
            window.clearAllFilters = clearAllFilters;
        </script>
    @endpush
@endsection
