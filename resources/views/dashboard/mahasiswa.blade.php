@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-lg font-semibold font-heading text-gray-900">Halo,
                                {{ $mahasiswa->pengguna->nama }}</h1>
                            <p class="text-sm text-gray-600">
                                Ringkasan aktivitas akademik Anda
                            </p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center space-x-4">
                                <label for="semester_id" class="text-xs font-medium text-gray-700">Filter Semester:</label>
                                <select name="semester_id" id="semester_id"
                                    class="rounded-lg border-gray-300 text-xs focus:border-blue-500 focus:ring-blue-500"
                                    onchange="this.form.submit()">
                                    <option value="">Pilih Semester</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id_semester }}"
                                            {{ request('semester_id') == $semester->id_semester ? 'selected' : '' }}>
                                            {{ $semester->nama_semester }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Akademik Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 mt-3">
                <!-- IP Terakhir -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">IPK Sementara</p>
                            <p class="text-lg font-semibold text-gray-900">
                                @if ($statusAkademik['ip_terakhir'] > 0)
                                    {{ number_format($statusAkademik['ip_terakhir'], 2) }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total SKS -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">SKS Diambil</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ number_format($statusAkademik['total_sks_diambil']) }}</p>
                            @if (!$selectedSemester)
                                <p class="text-xs text-gray-400">Pilih semester</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Status KRS -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Status KRS</p>
                            <p class="text-[15px] font-semibold text-gray-900
                            ">
                                @if ($statusAkademik['status_krs'] == 'APPROVED')
                                    Disetujui
                                @elseif($statusAkademik['status_krs'] == 'SUBMITTED')
                                    Menunggu Persetujuan
                                @elseif($statusAkademik['status_krs'] == 'REJECTED')
                                    Ditolak
                                @elseif($statusAkademik['status_krs'] == 'DRAFT')
                                    Draft
                                @else
                                    Belum Registrasi
                                @endif
                            </p>
                            @if (!$selectedSemester)
                                <p class="text-xs text-gray-400">Pilih semester</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Pembimbing Akademik -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pembimbing Akademik</p>
                            <p class="text-[15px] font-semibold text-gray-900">
                                @if ($statusAkademik['nama_pa'])
                                    {{ $statusAkademik['nama_pa'] }}
                                @else
                                    Belum Ditentukan
                                @endif
                            </p>
                            @if (!$selectedSemester)
                                <p class="text-xs text-gray-400">Pilih semester</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Jadwal Hari Ini -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Jadwal Hari Ini</h3>
                        <p class="text-sm text-gray-500">
                            {{ now()->format('l, d F Y') }}
                            @if ($selectedSemester)
                                • {{ $selectedSemester->nama_semester }}
                            @endif
                        </p>
                    </div>
                    <div class="p-6">
                        @if ($selectedSemester && $jadwalHariIni->count() > 0)
                            <div class="space-y-4">
                                @foreach ($jadwalHariIni as $jadwal)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-gray-900">
                                                {{ $jadwal->kelasKuliah->kurikulumMataKuliah->mataKuliah->nama_mata_kuliah }}
                                            </h4>
                                            <p class="text-xs text-gray-500">{{ $jadwal->kelasKuliah->nama_kelas_kuliah }}
                                            </p>
                                            <div class="flex items-center mt-1 text-xs text-gray-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $jadwal->kelasKuliah->jam_mulai }} -
                                                {{ $jadwal->kelasKuliah->jam_akhir }}
                                            </div>
                                            <div class="flex items-center mt-1 text-xs text-gray-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $jadwal->kelasKuliah->nama_ruangan }}
                                            </div>
                                            @if ($jadwal->kelasKuliah->dosen)
                                                <div class="flex items-center mt-1 text-xs text-gray-400">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                        </path>
                                                    </svg>
                                                    {{ $jadwal->kelasKuliah->dosen->nama_lengkap }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $jadwal->kelasKuliah->kurikulumMataKuliah->mataKuliah->sks_mata_kuliah }}
                                                SKS
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <p class="text-sm">Tidak ada jadwal hari ini</p>
                                @if (!$selectedSemester)
                                    <p class="text-xs text-gray-400 mt-1">Pilih semester untuk melihat jadwal</p>
                                @elseif ($statusAkademik['status_krs'] != 'APPROVED')
                                    <p class="text-xs text-gray-400 mt-1">Lakukan registrasi KRS terlebih dahulu</p>
                                @else
                                    <p class="text-xs text-gray-400 mt-1">Nikmati hari bebas Anda!</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Deadline Terdekat -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Deadline Terdekat</h3>
                        <p class="text-sm text-gray-500">
                            @if ($selectedSemester)
                                {{ $selectedSemester->nama_semester }}
                            @else
                                Pilih semester untuk detail
                            @endif
                        </p>
                    </div>
                    <div class="p-6">
                        @if ($selectedSemester && $deadlinesTerdekat->count() > 0)
                            <div class="space-y-4">
                                @foreach ($deadlinesTerdekat as $deadline)
                                    <div
                                        class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                                        <div class="flex-1">
                                            @php
                                                $typeColors = [
                                                    'tugas' => 'bg-blue-100 text-blue-800',
                                                    'uts' => 'bg-yellow-100 text-yellow-800',
                                                    'uas' => 'bg-red-100 text-red-800',
                                                ];

                                                $badgeColor =
                                                    $typeColors[$deadline->type] ?? 'bg-gray-100 text-gray-800';
                                            @endphp

                                            <div class="flex items-center">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mr-3 {{ $badgeColor }}">
                                                    {{ strtoupper($deadline->type) }}
                                                </span>
                                                <h4 class="text-sm font-medium text-gray-900">{{ $deadline->judul }}</h4>
                                            </div>

                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $deadline->kelasKuliah->nama_kelas_kuliah }}</p>
                                            <div class="flex items-center mt-1 text-xs text-gray-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $deadline->batas_akhir_pengumpulan->format('d M Y, H:i') }}
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if (isset($deadline->submitted) && $deadline->submitted)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Submitted
                                                </span>
                                            @else
                                                @php
                                                    $timeLeft = now()->diffInHours(
                                                        $deadline->batas_akhir_pengumpulan,
                                                        false,
                                                    );
                                                @endphp
                                                @if ($timeLeft > 0)
                                                    <span class="text-xs text-gray-500">
                                                        {{ $deadline->batas_akhir_pengumpulan->diffForHumans() }}
                                                    </span>
                                                @else
                                                    <span class="text-xs text-red-500 font-medium">Expired</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <p class="text-sm">
                                    @if (!$selectedSemester)
                                        Pilih semester untuk melihat deadline
                                    @else
                                        Tidak ada deadline terdekat
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
