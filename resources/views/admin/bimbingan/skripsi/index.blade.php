@extends('layouts.app')

@section('title', 'Manajemen Bimbingan Skripsi')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Manajemen Bimbingan Skripsi</h1>
                            <p class="text-sm text-gray-600">Kelola assign pembimbing dan track progress skripsi</p>
                        </div>

                        {{-- @if ($selectedSemester)
                            <div class="mt-4 sm:mt-0 flex space-x-2">
                                @if ($pesertaSkripsis->whereNull('id_dosen_pembimbing')->count() > 0)
                                    <button onclick="openAssignModal()"
                                        class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Assign Pembimbing
                                    </button>
                                @endif
                            </div>
                        @endif --}}
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

                        <!-- Search & Filter Status -->
                        @if ($selectedSemester)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <!-- Search -->
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" id="searchInput"
                                        placeholder="Cari mahasiswa, pembimbing, atau judul..."
                                        class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                </div>

                                <!-- Filter Status -->
                                <select id="statusFilter" onchange="filterByStatus()"
                                    class="text-xs py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Semua Status</option>
                                    <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>
                                        Lengkap (2 Pembimbing)
                                    </option>
                                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>
                                        Sebagian (1 Pembimbing)
                                    </option>
                                    <option value="unassigned" {{ request('status') == 'unassigned' ? 'selected' : '' }}>
                                        Belum Ada Pembimbing
                                    </option>
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
                        <p class="text-sm text-gray-500 mb-4">Pilih semester dari dropdown di atas untuk mengelola bimbingan
                            skripsi</p>
                    </div>
                </div>
            @else
                <!-- Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 my-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-xs font-medium text-gray-500">Total Mahasiswa Skripsi</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $pesertaSkripsis->count() }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-xs font-medium text-gray-500">Lengkap (2 Pembimbing)</div>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $pesertaSkripsis->filter(function ($p) {return $p->id_dosen_pembimbing && $p->id_dosen_pembimbing_2;})->count() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L5.082 15.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-xs font-medium text-gray-500">Sebagian (1 Pembimbing)</div>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $pesertaSkripsis->filter(function ($p) {return $p->id_dosen_pembimbing && !$p->id_dosen_pembimbing_2;})->count() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-xs font-medium text-gray-500">Belum Ada Pembimbing</div>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $pesertaSkripsis->whereNull('id_dosen_pembimbing')->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-none rounded-lg shadow-sm border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="skripsiTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mahasiswa
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pembimbing
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nilai Angka
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nilai Huruf
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nilai Indeks
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="skripsiTableBody">
                                @forelse($pesertaSkripsis as $peserta)
                                    @php
                                        $mahasiswa = $peserta->registrasiMahasiswa->mahasiswa;
                                        $detail = $peserta->skripsiDetail;
                                        $hasPembimbing1 = $peserta->id_dosen_pembimbing;
                                        $hasPembimbing2 = $peserta->id_dosen_pembimbing_2;

                                        $status = 'unassigned';
                                        if ($hasPembimbing1 && $hasPembimbing2) {
                                            $status = 'complete';
                                        } elseif ($hasPembimbing1) {
                                            $status = 'partial';
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 skripsi-row" data-searchable
                                        data-status="{{ $status }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="searchable-mahasiswa">
                                                <div class="text-xs font-semibold text-gray-900">
                                                    {{ $mahasiswa->pengguna->nama }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $mahasiswa->nim }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $mahasiswa->programStudi->nama_program_studi ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="searchable-pembimbing space-y-2">
                                                @if ($peserta->dosenPembimbing)
                                                    <div class="border border-gray-200 rounded p-2">
                                                        <div class="text-xs font-medium text-gray-900">
                                                            Pembimbing 1: {{ $peserta->dosenPembimbing->pengguna->nama }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $peserta->dosenPembimbing->nidn ?? 'N/A' }}
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($peserta->dosenPembimbing2)
                                                    <div class="border border-gray-200 rounded p-2">
                                                        <div class="text-xs font-medium text-gray-900">
                                                            Pembimbing 2: {{ $peserta->dosenPembimbing2->pengguna->nama }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $peserta->dosenPembimbing2->nidn ?? 'N/A' }}
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!$peserta->dosenPembimbing)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Belum Ada Pembimbing
                                                    </span>
                                                @elseif(!$peserta->dosenPembimbing2)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Perlu Pembimbing 2
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Lengkap
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        {{-- <td class="px-6 py-4">
                                            <div class="searchable-judul">
                                                @if ($detail && $detail->judul)
                                                    <div class="text-xs font-medium text-gray-900 mb-2">
                                                        {{ Str::limit($detail->judul, 100) }}
                                                    </div>
                                                    @if ($detail->bidang_penelitian)
                                                        <div class="text-xs text-gray-500 mb-2">
                                                            Bidang: {{ $detail->bidang_penelitian }}
                                                        </div>
                                                    @endif

                                                    <!-- Status Proposal -->
                                                    <div class="flex items-center space-x-2 mb-2">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                                            @switch($detail->status_proposal)
                                                                @case('DRAFT') bg-gray-100 text-gray-800 @break
                                                                @case('SUBMITTED') bg-blue-100 text-blue-800 @break
                                                                @case('APPROVED') bg-green-100 text-green-800 @break
                                                                @case('REJECTED') bg-red-100 text-red-800 @break
                                                                @default bg-gray-100 text-gray-800
                                                            @endswitch">
                                                            {{ $detail->status_proposal }}
                                                        </span>
                                                    </div>

                                                    <!-- Jadwal -->
                                                    <div class="space-y-1">
                                                        @if ($detail->tanggal_seminar_proposal)
                                                            <div class="text-xs text-gray-500">
                                                                🎯 Seminar:
                                                                {{ \Carbon\Carbon::parse($detail->tanggal_seminar_proposal)->format('d M Y') }}
                                                            </div>
                                                        @endif
                                                        @if ($detail->tanggal_sidang_skripsi)
                                                            <div class="text-xs text-gray-500">
                                                                🎓 Sidang:
                                                                {{ \Carbon\Carbon::parse($detail->tanggal_sidang_skripsi)->format('d M Y') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-xs text-gray-400 italic">
                                                        Belum ada detail skripsi
                                                    </div>
                                                @endif
                                            </div>
                                        </td> --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs font-semibold text-gray-900">
                                                {{ $peserta->nilaiPerkuliahan->nilai_angka }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs font-semibold text-gray-900">
                                                {{ $peserta->nilaiPerkuliahan->nilai_huruf }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs font-semibold text-gray-900">
                                                {{ $peserta->nilaiPerkuliahan->nilai_indeks }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                @if ($peserta->dosenPembimbing)
                                                    {{-- Tombol Edit --}}
                                                    <div class="relative group">
                                                        <button
                                                            onclick="openEditModal(
                                                            '{{ $peserta->id_peserta_bimbingan }}',
                                                            '{{ $peserta->id_dosen_pembimbing }}',
                                                            '{{ $peserta->id_dosen_pembimbing_2 }}',
                                                            '{{ addslashes($detail->judul ?? '') }}',
                                                            '{{ addslashes($detail->bidang_penelitian ?? '') }}',
                                                            '{{ addslashes($mahasiswa->pengguna->nama) }}'
                                                        )"
                                                            class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                            <i class="text-xs fas fa-edit"></i>
                                                        </button>
                                                        <div
                                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                            Edit Pembimbing
                                                            <div
                                                                class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Tombol Set Jadwal --}}
                                                    @if ($detail)
                                                        <div class="relative group">
                                                            <button
                                                                onclick="openJadwalModal(
                                                                '{{ $peserta->id_peserta_bimbingan }}',
                                                                '{{ $detail->tanggal_seminar_proposal }}',
                                                                '{{ $detail->tanggal_sidang_skripsi }}',
                                                                '{{ addslashes($mahasiswa->pengguna->nama) }}'
                                                            )"
                                                                class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                <i class="text-xs fas fa-calendar"></i>
                                                            </button>
                                                            <div
                                                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                Set Jadwal
                                                                <div
                                                                    class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @else
                                                    {{-- Tombol Quick Assign --}}
                                                    <div class="relative group">
                                                        <button
                                                            onclick="quickAssign('{{ $peserta->id_peserta_bimbingan }}', '{{ addslashes($mahasiswa->pengguna->nama) }}')"
                                                            class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                            <i class="text-xs fas fa-plus"></i>
                                                        </button>
                                                        <div
                                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                            Assign Pembimbing
                                                            <div
                                                                class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
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
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium text-gray-900 mb-2">Belum ada mahasiswa
                                                    skripsi</p>
                                                <p class="text-xs text-gray-500">Mahasiswa akan muncul setelah KRS
                                                    diapprove</p>
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

        <!-- Assign/Edit Modal -->
        @if ($selectedSemester)
            <div id="assignModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAssignModal()">
                    </div>

                    <div
                        class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-4xl mx-auto">
                        <form id="assignForm" method="POST"
                            action="{{ route('bimbingan.skripsi.assign-pembimbing') }}">
                            @csrf
                            <input type="hidden" name="semester" value="{{ $selectedSemester->id_semester }}">
                            <input type="hidden" name="id_peserta_bimbingan" id="id_peserta_bimbingan">

                            <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 id="modalTitle" class="text-lg font-heading font-semibold text-gray-900">
                                        Assign Pembimbing Skripsi
                                    </h3>
                                    <button type="button" onclick="closeAssignModal()"
                                        class="sm:hidden text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <!-- Info Mahasiswa -->
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div class="text-xs font-medium text-gray-700 mb-1">Mahasiswa:</div>
                                        <div id="mahasiswa_info" class="text-xs text-gray-900">-</div>
                                    </div>

                                    <!-- Pilih Pembimbing 1 -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Pembimbing 1 <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text" id="pembimbing1_search"
                                                placeholder="Cari pembimbing 1..." autocomplete="off"
                                                class="w-full text-xs px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">

                                            <button type="button" id="clear_pembimbing1"
                                                onclick="clearPembimbing1Selection()"
                                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6m0 12L6 6"></path>
                                                </svg>
                                            </button>

                                            <input type="hidden" name="id_dosen_pembimbing" id="id_dosen_pembimbing"
                                                required>

                                            <div id="pembimbing1_dropdown"
                                                class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                            </div>

                                            <div id="pembimbing1_loading" class="absolute right-8 top-2 hidden">
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

                                    <!-- Pilih Pembimbing 2 -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">
                                            Pembimbing 2 (Opsional)
                                        </label>
                                        <div class="relative">
                                            <input type="text" id="pembimbing2_search"
                                                placeholder="Cari pembimbing 2..." autocomplete="off"
                                                class="w-full text-xs px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">

                                            <button type="button" id="clear_pembimbing2"
                                                onclick="clearPembimbing2Selection()"
                                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6m0 12L6 6"></path>
                                                </svg>
                                            </button>

                                            <input type="hidden" name="id_dosen_pembimbing_2"
                                                id="id_dosen_pembimbing_2">

                                            <div id="pembimbing2_dropdown"
                                                class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                            </div>

                                            <div id="pembimbing2_loading" class="absolute right-8 top-2 hidden">
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

                                    <!-- Detail Skripsi -->
                                    <div class="border-t pt-4">
                                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Detail Skripsi (Opsional)</h4>

                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-2">
                                                    Judul Skripsi
                                                </label>
                                                <textarea name="judul" id="judul" rows="3"
                                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                                    placeholder="Masukkan judul skripsi..." maxlength="500"></textarea>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-2">
                                                    Bidang Penelitian
                                                </label>
                                                <input type="text" name="bidang_penelitian" id="bidang_penelitian"
                                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                                    placeholder="Contoh: Machine Learning, Web Development"
                                                    maxlength="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                                <button type="button" onclick="closeAssignModal()"
                                    class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                    <span id="submitText">Assign</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Jadwal Modal -->
            <div id="jadwalModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeJadwalModal()">
                    </div>

                    <div
                        class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
                        <form id="jadwalForm" method="POST">
                            @csrf
                            <input type="hidden" name="id_peserta_bimbingan" id="jadwal_id_peserta_bimbingan">

                            <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-heading font-semibold text-gray-900">
                                        Set Jadwal Seminar & Sidang
                                    </h3>
                                    <button type="button" onclick="closeJadwalModal()"
                                        class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <!-- Info Mahasiswa -->
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <div class="text-xs font-medium text-gray-700 mb-1">Mahasiswa:</div>
                                        <div id="jadwal_mahasiswa_info" class="text-xs text-gray-900">-</div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                                Tanggal Seminar Proposal
                                            </label>
                                            <input type="date" name="tanggal_seminar_proposal"
                                                id="tanggal_seminar_proposal"
                                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-2">
                                                Tanggal Sidang Skripsi
                                            </label>
                                            <input type="date" name="tanggal_sidang_skripsi"
                                                id="tanggal_sidang_skripsi"
                                                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                        </div>
                                    </div>

                                    <div class="bg-blue-50 p-3 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-blue-400 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-xs text-blue-700">
                                                Seminar proposal harus dilakukan sebelum sidang skripsi
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                                <button type="button" onclick="closeJadwalModal()"
                                    class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                    Simpan Jadwal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Global variables
            let pembimbingData = [];
            let searchTimeouts = {};

            // Load initial data
            document.addEventListener('DOMContentLoaded', function() {
                loadPembimbingData();
            });

            function loadPembimbingData() {
                fetch('/bimbingan/skripsi/api/dosen')
                    .then(response => response.json())
                    .then(data => {
                        pembimbingData = data;
                    })
                    .catch(error => {
                        console.error('Error loading pembimbing:', error);
                    });
            }

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

                function filterByStatus() {
                    filterTable();
                }

                function filterTable() {
                    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                    const statusFilter = document.getElementById('statusFilter').value;
                    const tbody = document.getElementById('skripsiTableBody');
                    const rows = tbody.querySelectorAll('tr[data-searchable]');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const mahasiswaCell = row.querySelector('.searchable-mahasiswa');
                        const pembimbingCell = row.querySelector('.searchable-pembimbing');
                        const judulCell = row.querySelector('.searchable-judul');
                        const rowStatus = row.getAttribute('data-status');

                        const mahasiswaText = mahasiswaCell ? mahasiswaCell.textContent.toLowerCase() : '';
                        const pembimbingText = pembimbingCell ? pembimbingCell.textContent.toLowerCase() : '';
                        const judulText = judulCell ? judulCell.textContent.toLowerCase() : '';

                        const matchesSearch = searchTerm === '' ||
                            mahasiswaText.includes(searchTerm) ||
                            pembimbingText.includes(searchTerm) ||
                            judulText.includes(searchTerm);
                        const matchesStatus = statusFilter === '' || rowStatus === statusFilter;

                        const isMatch = matchesSearch && matchesStatus;

                        if (isMatch) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    updateEmptyState(visibleCount, searchTerm, statusFilter);
                    if (window.pagination) {
                        window.pagination.onFilterChange();
                    }
                }

                function updateEmptyState(visibleCount, searchTerm, statusFilter) {
                    const tbody = document.getElementById('skripsiTableBody');
                    let emptyRow = tbody.querySelector('tr[data-empty-search]');

                    if (emptyRow) {
                        emptyRow.remove();
                    }

                    if (visibleCount === 0 && (searchTerm !== '' || statusFilter !== '')) {
                        emptyRow = document.createElement('tr');
                        emptyRow.setAttribute('data-empty-search', 'true');

                        let message = 'Tidak ada hasil ditemukan';
                        let detail = '';

                        if (searchTerm && statusFilter) {
                            detail = `untuk pencarian "${searchTerm}" dengan status ${statusFilter}`;
                        } else if (searchTerm) {
                            detail = `untuk pencarian "${searchTerm}"`;
                        } else if (statusFilter) {
                            detail = `dengan status ${statusFilter}`;
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

                // PEMBIMBING SEARCH FUNCTIONS
                function setupPembimbingSearch(type) {
                    const searchInput = document.getElementById(`pembimbing${type}_search`);
                    if (!searchInput) return;

                    const newSearchInput = searchInput.cloneNode(true);
                    searchInput.parentNode.replaceChild(newSearchInput, searchInput);

                    newSearchInput.addEventListener('input', function() {
                        const query = this.value.toLowerCase().trim();

                        if (searchTimeouts[`pembimbing${type}`]) {
                            clearTimeout(searchTimeouts[`pembimbing${type}`]);
                        }

                        updatePembimbingClearButton(type);
                        document.getElementById(`pembimbing${type}_loading`).classList.remove('hidden');

                        searchTimeouts[`pembimbing${type}`] = setTimeout(() => {
                            searchPembimbing(query, type);
                            document.getElementById(`pembimbing${type}_loading`).classList.add('hidden');
                        }, 300);
                    });

                    newSearchInput.addEventListener('focus', function() {
                        if (this.value !== '') {
                            const query = this.value.toLowerCase().trim();
                            searchPembimbing(query, type);
                        } else {
                            showAllPembimbing(type);
                        }
                    });

                    document.addEventListener('click', function(event) {
                        const dropdown = document.getElementById(`pembimbing${type}_dropdown`);
                        if (!newSearchInput.contains(event.target) && !dropdown.contains(event.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                }

                function searchPembimbing(query, type) {
                    if (query === '') {
                        showAllPembimbing(type);
                        return;
                    }

                    // Filter out already selected pembimbing
                    const otherType = type === '1' ? '2' : '1';
                    const otherSelected = document.getElementById(`id_dosen_pembimbing${otherType === '1' ? '' : '_2'}`).value;

                    const filteredData = pembimbingData.filter(item => {
                        const namaMatch = item.nama && item.nama.toLowerCase().includes(query);
                        const nidnMatch = item.nidn && item.nidn.toLowerCase().includes(query);
                        const prodiMatch = item.program_studi && item.program_studi.toLowerCase().includes(query);
                        const notSelected = item.id_dosen !== otherSelected;
                        return (namaMatch || nidnMatch || prodiMatch) && notSelected;
                    });

                    displayPembimbingResults(filteredData, type);
                }

                function showAllPembimbing(type) {
                    // Filter out already selected pembimbing
                    const otherType = type === '1' ? '2' : '1';
                    const otherSelected = document.getElementById(`id_dosen_pembimbing${otherType === '1' ? '' : '_2'}`).value;

                    const filteredData = pembimbingData.filter(item => item.id_dosen !== otherSelected);
                    displayPembimbingResults(filteredData, type);
                }

                function displayPembimbingResults(data, type) {
                    const dropdown = document.getElementById(`pembimbing${type}_dropdown`);

                    if (data.length === 0) {
                        dropdown.innerHTML = `
                            <div class="px-4 py-3 text-center text-xs text-gray-500">
                                <div>Tidak ada dosen ditemukan</div>
                            </div>
                        `;
                    } else {
                        dropdown.innerHTML = data.map(item => `
                            <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                                 onclick="selectPembimbing('${item.id_dosen}', '${escapeHtml(item.nama_lengkap)}', '${type}')">
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

                function selectPembimbing(id, namaLengkap, type) {
                    const searchInput = document.getElementById(`pembimbing${type}_search`);
                    const hiddenInput = document.getElementById(`id_dosen_pembimbing${type === '1' ? '' : '_2'}`);
                    const dropdown = document.getElementById(`pembimbing${type}_dropdown`);

                    searchInput.value = namaLengkap;
                    hiddenInput.value = id;

                    updatePembimbingClearButton(type);
                    dropdown.classList.add('hidden');

                    // Refresh other dropdown to exclude this selection
                    const otherType = type === '1' ? '2' : '1';
                    const otherSearchInput = document.getElementById(`pembimbing${otherType}_search`);
                    if (otherSearchInput && otherSearchInput.value === '') {
                        showAllPembimbing(otherType);
                    }

                    searchInput.classList.add('border-green-300', 'bg-green-50');
                    setTimeout(() => {
                        searchInput.classList.remove('border-green-300', 'bg-green-50');
                    }, 1000);
                }

                function clearPembimbing1Selection() {
                    document.getElementById('pembimbing1_search').value = '';
                    document.getElementById('id_dosen_pembimbing').value = '';
                    document.getElementById('pembimbing1_dropdown').classList.add('hidden');
                    document.getElementById('clear_pembimbing1').classList.add('hidden');

                    // Refresh pembimbing 2 dropdown
                    showAllPembimbing('2');
                }

                function clearPembimbing2Selection() {
                    document.getElementById('pembimbing2_search').value = '';
                    document.getElementById('id_dosen_pembimbing_2').value = '';
                    document.getElementById('pembimbing2_dropdown').classList.add('hidden');
                    document.getElementById('clear_pembimbing2').classList.add('hidden');

                    // Refresh pembimbing 1 dropdown
                    showAllPembimbing('1');
                }

                function updatePembimbingClearButton(type) {
                    const hiddenInput = document.getElementById(`id_dosen_pembimbing${type === '1' ? '' : '_2'}`);
                    const clearButton = document.getElementById(`clear_pembimbing${type}`);

                    if (hiddenInput.value) {
                        clearButton.classList.remove('hidden');
                    } else {
                        clearButton.classList.add('hidden');
                    }
                }

                // MODAL FUNCTIONS
                function openAssignModal() {
                    const modal = document.getElementById('assignModal');
                    const form = document.getElementById('assignForm');

                    form.reset();
                    clearPembimbing1Selection();
                    clearPembimbing2Selection();
                    document.getElementById('mahasiswa_info').textContent = '-';
                    document.getElementById('modalTitle').textContent = 'Assign Pembimbing Skripsi';
                    document.getElementById('submitText').textContent = 'Assign';
                    form.action = '{{ route('bimbingan.skripsi.assign-pembimbing') }}';

                    // Remove method input if exists
                    form.querySelector('input[name="_method"]')?.remove();

                    modal.classList.remove('hidden');

                    setTimeout(() => {
                        setupPembimbingSearch('1');
                        setupPembimbingSearch('2');
                        document.getElementById('pembimbing1_search').focus();
                    }, 100);
                }

                function quickAssign(idPesertaBimbingan, namaMahasiswa) {
                    openAssignModal();
                    setTimeout(() => {
                        document.getElementById('id_peserta_bimbingan').value = idPesertaBimbingan;
                        document.getElementById('mahasiswa_info').textContent = namaMahasiswa;
                    }, 200);
                }

                function openEditModal(idPesertaBimbingan, idPembimbing1, idPembimbing2, judul, bidangPenelitian,
                    namaMahasiswa) {
                    const modal = document.getElementById('assignModal');
                    const form = document.getElementById('assignForm');

                    form.action = `/bimbingan/skripsi/${idPesertaBimbingan}/update-pembimbing`;

                    // Add method input for PUT
                    let methodInput = form.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        form.appendChild(methodInput);
                    }
                    methodInput.value = 'PUT';

                    // Set values
                    document.getElementById('id_peserta_bimbingan').value = idPesertaBimbingan;
                    document.getElementById('mahasiswa_info').textContent = namaMahasiswa;
                    document.getElementById('judul').value = judul || '';
                    document.getElementById('bidang_penelitian').value = bidangPenelitian || '';
                    document.getElementById('modalTitle').textContent = 'Edit Pembimbing Skripsi';
                    document.getElementById('submitText').textContent = 'Update';

                    modal.classList.remove('hidden');

                    setTimeout(() => {
                        setupPembimbingSearch('1');
                        setupPembimbingSearch('2');

                        // Load existing pembimbing data
                        if (idPembimbing1) {
                            loadEditPembimbingData(idPembimbing1, '1');
                        }
                        if (idPembimbing2) {
                            loadEditPembimbingData(idPembimbing2, '2');
                        }
                    }, 100);
                }

                async function loadEditPembimbingData(idDosen, type) {
                    try {
                        const pembimbing = pembimbingData.find(p => p.id_dosen === idDosen);

                        if (pembimbing) {
                            const pembimbingInput = document.getElementById(`pembimbing${type}_search`);
                            const idDosenInput = document.getElementById(`id_dosen_pembimbing${type === '1' ? '' : '_2'}`);

                            pembimbingInput.value = pembimbing.nama_lengkap;
                            idDosenInput.value = idDosen;
                            updatePembimbingClearButton(type);
                        }
                    } catch (error) {
                        console.error('Error loading pembimbing data:', error);
                    }
                }

                function closeAssignModal() {
                    document.getElementById('assignModal').classList.add('hidden');
                }

                function openJadwalModal(idPesertaBimbingan, tanggalSeminar, tanggalSidang, namaMahasiswa) {
                    const modal = document.getElementById('jadwalModal');
                    const form = document.getElementById('jadwalForm');

                    form.action = `/bimbingan/skripsi/${idPesertaBimbingan}/set-jadwal`;

                    // Set values
                    document.getElementById('jadwal_id_peserta_bimbingan').value = idPesertaBimbingan;
                    document.getElementById('jadwal_mahasiswa_info').textContent = namaMahasiswa;
                    document.getElementById('tanggal_seminar_proposal').value = tanggalSeminar || '';
                    document.getElementById('tanggal_sidang_skripsi').value = tanggalSidang || '';

                    modal.classList.remove('hidden');
                }

                function closeJadwalModal() {
                    document.getElementById('jadwalModal').classList.add('hidden');
                }

                // Close modals with Escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeAssignModal();
                        closeJadwalModal();
                    }
                });

                // Helper function
                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                // Make functions globally accessible
                window.selectPembimbing = selectPembimbing;
                window.clearPembimbing1Selection = clearPembimbing1Selection;
                window.clearPembimbing2Selection = clearPembimbing2Selection;
                window.openAssignModal = openAssignModal;
                window.quickAssign = quickAssign;
                window.openEditModal = openEditModal;
                window.closeAssignModal = closeAssignModal;
                window.openJadwalModal = openJadwalModal;
                window.closeJadwalModal = closeJadwalModal;
            @endif

            // Make global functions accessible
            window.changeSemester = changeSemester;
            @if ($selectedSemester)
                window.filterByStatus = filterByStatus;
            @endif
        </script>

        <!-- Pagination Script (same as others) -->
        <script>
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
                    const tbody = document.getElementById('skripsiTableBody');
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
