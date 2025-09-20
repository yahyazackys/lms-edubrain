@extends('layouts.app')

@section('title', 'Laporan KRS - Detail Report')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Laporan Detail KRS</h1>
                            <p class="text-sm text-gray-600">Laporan lengkap KRS -
                                {{ $selectedSemester->nama_semester ?? 'Pilih Semester' }}</p>
                        </div>

                        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                            <a href="{{ route('admin.krs.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Dashboard
                            </a>

                            @if ($selectedSemester)
                                <button onclick="printReport()"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                        </path>
                                    </svg>
                                    Print Laporan
                                </button>

                                <button onclick="exportReport()"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Export Excel
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <form method="GET" id="filterForm">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <!-- Semester Filter -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                                <select name="semester" onchange="document.getElementById('filterForm').submit()"
                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Pilih Semester</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id_semester }}"
                                            {{ $selectedSemesterId == $semester->id_semester ? 'selected' : '' }}>
                                            {{ $semester->nama_semester }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if ($selectedSemester)
                                <!-- Status Filter -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status" onchange="document.getElementById('filterForm').submit()"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                        <option value="">Semua Status</option>
                                        <option value="SUBMITTED" {{ request('status') == 'SUBMITTED' ? 'selected' : '' }}>
                                            Submitted</option>
                                        <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>
                                            Approved</option>
                                        <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>Active
                                        </option>
                                    </select>
                                </div>

                                <!-- Program Studi Filter -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Program Studi</label>
                                    <select name="program_studi" onchange="document.getElementById('filterForm').submit()"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
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

                                <!-- PA Filter -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Pembimbing Akademik</label>
                                    <select name="pembimbing_akademik"
                                        onchange="document.getElementById('filterForm').submit()"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                        <option value="">Semua PA</option>
                                        @foreach ($pembimbingAkademiks as $pa)
                                            <option value="{{ $pa->id_pembimbing_akademik }}"
                                                {{ request('pembimbing_akademik') == $pa->id_pembimbing_akademik ? 'selected' : '' }}>
                                                {{ $pa->dosen->pengguna->nama ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Sort By -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Urutkan</label>
                                    <select name="sort" onchange="document.getElementById('filterForm').submit()"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>
                                            Tanggal Dibuat</option>
                                        <option value="nama" {{ request('sort') == 'nama' ? 'selected' : '' }}>Nama
                                            Mahasiswa</option>
                                        <option value="nim" {{ request('sort') == 'nim' ? 'selected' : '' }}>NIM
                                        </option>
                                        <option value="tanggal_submit"
                                            {{ request('sort') == 'tanggal_submit' ? 'selected' : '' }}>Tanggal Submit
                                        </option>
                                        <option value="tanggal_approval"
                                            {{ request('sort') == 'tanggal_approval' ? 'selected' : '' }}>Tanggal Approval
                                        </option>
                                    </select>
                                </div>

                                <!-- Search -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Cari</label>
                                    <div class="relative">
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            placeholder="Cari nama/NIM..."
                                            class="w-full text-xs px-3 py-2 pl-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                            <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <button type="submit" class="absolute inset-y-0 right-0 pr-2 flex items-center">
                                            <svg class="h-3 w-3 text-gray-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 9l3 3-3 3m-6-3h9"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            @if (!$selectedSemester)
                <!-- Empty State - Pilih Semester -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-calendar-alt w-16 h-16 text-gray-400 mb-4 mx-auto text-6xl"></i>
                        <p class="text-lg font-medium text-gray-900 mb-2">Pilih Semester</p>
                        <p class="text-sm text-gray-500 mb-4">Pilih semester dari dropdown di atas untuk melihat laporan
                            KRS</p>
                    </div>
                </div>
            @else
                <!-- Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <!-- Table Header with Summary -->
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900">Detail Laporan KRS</h3>
                            <div class="flex items-center space-x-4 text-xs text-gray-600">
                                <span>{{ $laporanData->total() }} total mahasiswa</span>
                                <span>•</span>
                                <span>Halaman {{ $laporanData->currentPage() }} dari {{ $laporanData->lastPage() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="laporanTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mahasiswa
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Program Studi
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pembimbing Akademik
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total SKS
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Timeline
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($laporanData as $index => $krs)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                            {{ ($laporanData->currentPage() - 1) * $laporanData->perPage() + $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-gray-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-xs font-medium text-gray-900">
                                                        {{ $krs->mahasiswa->pengguna->nama ?? 'N/A' }}</div>
                                                    <div class="text-xs text-gray-500">{{ $krs->mahasiswa->nim ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs font-medium text-gray-900">
                                                {{ $krs->mahasiswa->programStudi->nama_program_studi ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $krs->mahasiswa->programStudi->jenjang->kode_jenjang_pendidikan ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs font-medium text-gray-900">
                                                {{ $krs->pembimbingAkademik->dosen->pengguna->nama ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $krs->pembimbingAkademik->dosen->nidn ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $krs->total_sks ?? 0 }} SKS
                                                </span>
                                                <span
                                                    class="ml-2 text-xs text-gray-500">({{ $krs->pesertaKelasKuliah->where('status_mata_kuliah', '!=', 'REJECTED')->count() }}
                                                    MK)</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($krs->status_krs === 'ACTIVE')
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                    </svg>
                                                    Aktif
                                                </span>
                                            @elseif($krs->status_krs === 'APPROVED')
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Disetujui
                                                </span>
                                            @elseif($krs->tanggal_submit)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Menunggu Approval
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                    Belum Submit
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs space-y-1">
                                                <div class="flex items-center">
                                                    <span class="w-16 text-gray-500">Dibuat:</span>
                                                    <span
                                                        class="text-gray-900">{{ $krs->created_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                                @if ($krs->tanggal_submit)
                                                    <div class="flex items-center">
                                                        <span class="w-16 text-gray-500">Submit:</span>
                                                        <span
                                                            class="text-gray-900">{{ $krs->tanggal_submit->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                @endif
                                                @if ($krs->tanggal_approval)
                                                    <div class="flex items-center">
                                                        <span class="w-16 text-gray-500">Approve:</span>
                                                        <span
                                                            class="text-gray-900">{{ $krs->tanggal_approval->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <!-- View Detail -->
                                                <div class="relative group">
                                                    <a href="{{ route('admin.krs.detail', $krs->id_registrasi_mahasiswa) }}"
                                                        class="inline-flex items-center p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors duration-200">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Lihat Detail
                                                        <div
                                                            class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Quick Actions based on status -->
                                                @if ($krs->status_krs === 'APPROVED')
                                                    <div class="relative group">
                                                        <button
                                                            onclick="quickActivate('{{ $krs->id_registrasi_mahasiswa }}', '{{ $krs->mahasiswa->pengguna->nama ?? '' }}')"
                                                            class="inline-flex items-center p-2 text-green-600 hover:text-green-900 hover:bg-green-50 rounded-lg transition-colors duration-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                            </svg>
                                                        </button>
                                                        <div
                                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                            Aktivasi KRS
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
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium text-gray-900 mb-2">Tidak ada data KRS</p>
                                                <p class="text-xs text-gray-500">Tidak ada data KRS yang sesuai dengan
                                                    filter</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($laporanData->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $laporanData->appends(request()->query())->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>

                <!-- Summary Stats for Current Filter -->
                @if ($laporanData->count() > 0)
                    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">Ringkasan Laporan</h3>
                            <p class="text-xs text-gray-500 mt-1">Berdasarkan filter yang dipilih</p>
                        </div>
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                @php
                                    $totalKrs = $laporanData->count();
                                    $submitted = $laporanData
                                        ->filter(function ($krs) {
                                            return $krs->tanggal_submit;
                                        })
                                        ->count();
                                    $approved = $laporanData
                                        ->filter(function ($krs) {
                                            return $krs->status_krs === 'APPROVED';
                                        })
                                        ->count();
                                    $active = $laporanData
                                        ->filter(function ($krs) {
                                            return $krs->status_krs === 'ACTIVE';
                                        })
                                        ->count();
                                    $avgSks = $laporanData->avg('total_sks');
                                @endphp

                                <div class="text-center">
                                    <div class="text-lg font-semibold text-gray-900">{{ $totalKrs }}</div>
                                    <div class="text-xs text-gray-500">Total KRS</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-semibold text-yellow-600">{{ $submitted }}</div>
                                    <div class="text-xs text-gray-500">Submitted</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-semibold text-blue-600">{{ $approved }}</div>
                                    <div class="text-xs text-gray-500">Approved</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-semibold text-green-600">{{ $active }}</div>
                                    <div class="text-xs text-gray-500">Active</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-semibold text-purple-600">{{ number_format($avgSks, 1) }}
                                    </div>
                                    <div class="text-xs text-gray-500">Rata-rata SKS</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Quick Activate Modal -->
    <div id="quickActivateModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeQuickActivateModal()">
            </div>

            <div
                class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">
                <form id="quickActivateForm" method="POST">
                    @csrf

                    <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                        <div class="flex items-center mb-4">
                            <div
                                class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-heading font-semibold text-gray-900">
                                    Aktivasi KRS
                                </h3>
                                <p class="text-sm text-gray-500 mt-1" id="quickActivateText">
                                    <!-- Will be populated by JavaScript -->
                                </p>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex">
                                <svg class="w-4 h-4 text-blue-400 mt-0.5 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-xs text-blue-800">
                                    <p class="font-medium">Informasi:</p>
                                    <p class="mt-1">KRS akan diubah statusnya dari "APPROVED" menjadi "ACTIVE". Mahasiswa
                                        dapat mulai mengikuti perkuliahan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                        <button type="button" onclick="closeQuickActivateModal()"
                            class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                            Batal
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Aktivasi KRS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Print function
            function printReport() {
                window.print();
            }

            // Export function
            function exportReport() {
                const params = new URLSearchParams(window.location.search);
                params.set('format', 'excel');
                window.location.href = '{{ route('admin.krs.export') }}?' + params.toString();
            }

            // Quick activate
            function quickActivate(registrasiId, namaMahasiswa) {
                document.getElementById('quickActivateForm').action = `/admin/krs/aktivasi-mass`;
                document.getElementById('quickActivateText').textContent = `Aktivasi KRS mahasiswa ${namaMahasiswa}?`;

                // Add hidden input for single activation
                const form = document.getElementById('quickActivateForm');
                const existingInput = form.querySelector('input[name="single_id"]');
                if (existingInput) {
                    existingInput.remove();
                }

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'single_id';
                hiddenInput.value = registrasiId;
                form.appendChild(hiddenInput);

                document.getElementById('quickActivateModal').classList.remove('hidden');
            }

            function closeQuickActivateModal() {
                document.getElementById('quickActivateModal').classList.add('hidden');
            }

            // Auto submit on search input change with debounce
            let searchTimeout;
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        document.getElementById('filterForm').submit();
                    }, 500);
                });
            }

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeQuickActivateModal();
                }
            });
        </script>
    @endpush
@endsection
