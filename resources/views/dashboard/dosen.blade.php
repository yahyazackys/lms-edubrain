@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-lg font-semibold font-heading text-gray-900">Halo, {{ $dosen->nama_lengkap }}
                            </h1>
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
                                    <option value="">Semua Semester</option>
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

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 mt-2.5">
                <!-- Total Kelas -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">
                                @if ($selectedSemester)
                                    Kelas Semester Ini
                                @else
                                    Total Kelas Aktif
                                @endif
                            </p>
                            <p class="text-lg font-semibold text-gray-900">
                                @if ($selectedSemester)
                                    {{ number_format($stats['total_kelas_semester']) }}
                                @else
                                    {{ number_format($stats['total_kelas_all']) }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Mahasiswa Bimbingan -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Mahasiswa Bimbingan</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ number_format($stats['total_mahasiswa_bimbingan']) }}
                            </p>
                            @if (!$selectedSemester)
                                <p class="text-xs text-gray-400">Pilih semester untuk detail</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Total Peserta Kelas -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Peserta</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ number_format($stats['total_peserta_kelas']) }}
                            </p>
                            @if (!$selectedSemester)
                                <p class="text-xs text-gray-400">Pilih semester untuk detail</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tugas Pending -->
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
                            <p class="text-sm font-medium text-gray-600">Pending Penilaian</p>
                            <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['tugas_pending']) }}
                            </p>
                            @if (!$selectedSemester)
                                <p class="text-xs text-gray-400">Pilih semester untuk detail</p>
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
                        @if ($jadwalHariIni->count() > 0)
                            <div class="space-y-4">
                                @foreach ($jadwalHariIni as $jadwal)
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-gray-900">
                                                {{ $jadwal->kurikulumMataKuliah->mataKuliah->nama_mata_kuliah }}
                                            </h4>
                                            <p class="text-xs text-gray-500">{{ $jadwal->nama_kelas_kuliah }}</p>
                                            <div class="flex items-center mt-1 text-xs text-gray-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_akhir }}
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
                                                {{ $jadwal->nama_ruangan }}
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $jadwal->kurikulumMataKuliah->mataKuliah->sks_mata_kuliah }} SKS
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
                                    <p class="text-xs text-gray-400 mt-1">Pilih semester untuk filter jadwal</p>
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

            <!-- Notifications & Updates -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sesi Absensi Terbuka -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Sesi Absensi Terbuka</h3>
                        <p class="text-sm text-gray-500">
                            @if ($selectedSemester)
                                {{ $selectedSemester->nama_semester }}
                            @else
                                Pilih semester untuk detail
                            @endif
                        </p>
                    </div>
                    <div class="p-6">
                        @if ($selectedSemester && $sesiAbsensiTerbuka->count() > 0)
                            <div class="space-y-4">
                                @foreach ($sesiAbsensiTerbuka as $sesi)
                                    <div
                                        class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $sesi->topik }}</h4>
                                            <p class="text-xs text-gray-500">
                                                {{ $sesi->kelasKuliah->kurikulumMataKuliah->mataKuliah->nama_mata_kuliah }}
                                            </p>
                                            <div class="flex items-center mt-1 text-xs text-gray-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Berakhir: {{ $sesi->batas_akhir_absensi->format('H:i') }}
                                            </div>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-gray-500">
                                <p class="text-sm">
                                    @if (!$selectedSemester)
                                        Pilih semester untuk melihat sesi absensi
                                    @else
                                        Tidak ada sesi absensi terbuka
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Mahasiswa Bimbingan Belum KRS -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Mahasiswa Bimbingan Belum KRS</h3>
                        <p class="text-sm text-gray-500">
                            @if ($selectedSemester)
                                {{ $selectedSemester->nama_semester }}
                            @else
                                Pilih semester untuk detail
                            @endif
                        </p>
                    </div>
                    <div class="p-6">
                        @if ($selectedSemester && $mahasiswaBelumKrs->count() > 0)
                            <div class="space-y-4">
                                @foreach ($mahasiswaBelumKrs as $pa)
                                    <div
                                        class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">
                                                {{ $pa->mahasiswa->pengguna->nama }}</h4>
                                            <p class="text-xs text-gray-500">{{ $pa->mahasiswa->nim }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ $pa->mahasiswa->programStudi->nama_program_studi }}</p>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Belum KRS
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            @if ($mahasiswaBelumKrs->count() >= 5)
                                <div class="mt-4 text-center">
                                    <a href="#" class="text-sm text-blue-600 hover:text-blue-500">Lihat semua</a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4 text-gray-500">
                                <p class="text-sm">
                                    @if (!$selectedSemester)
                                        Pilih semester untuk melihat status KRS mahasiswa bimbingan
                                    @else
                                        Semua mahasiswa bimbingan sudah KRS
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
