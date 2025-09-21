@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-lg font-semibold font-heading text-gray-900">Dashboard Admin</h1>
                            <p class="text-sm text-gray-600">
                                Selamat datang, {{ Auth::user()->nama }}
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

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Mahasiswa -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Mahasiswa Aktif</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_mahasiswa']) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Total Dosen -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Dosen Aktif</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_dosen']) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Program Studi -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Program Studi Aktif</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ number_format($stats['total_program_studi']) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Kelas Semester -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">
                                @if ($selectedSemester)
                                    Kelas {{ $selectedSemester->nama_semester }}
                                @else
                                    Kelas Semester
                                @endif
                            </p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ number_format($stats['total_kelas_semester']) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Distribusi Mahasiswa Chart -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Distribusi Mahasiswa per Program Studi</h3>
                        <p class="text-sm text-gray-500">Total mahasiswa aktif per program studi</p>
                    </div>
                    <div class="p-6">
                        @if ($distribusiMahasiswa->count() > 0)
                            <div class="space-y-4">
                                @foreach ($distribusiMahasiswa->take(8) as $item)
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-sm font-medium text-gray-700">{{ $item['name'] }}</span>
                                                <span class="text-sm text-gray-500">{{ $item['value'] }} mahasiswa</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-gray-900 h-2 rounded-full"
                                                    style="width: {{ $distribusiMahasiswa->max('value') > 0 ? ($item['value'] / $distribusiMahasiswa->max('value')) * 100 : 0 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                                <p class="text-sm">Belum ada data mahasiswa</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status KRS Chart -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Status KRS</h3>
                        <p class="text-sm text-gray-500">
                            @if ($selectedSemester)
                                Data untuk {{ $selectedSemester->nama_semester }}
                            @else
                                Pilih semester untuk melihat status KRS
                            @endif
                        </p>
                    </div>
                    <div class="p-6">
                        @if ($selectedSemester && $statusKrs->count() > 0)
                            <div class="space-y-4">
                                @php
                                    $statusColors = [
                                        'Draft' => [
                                            'dot' => 'bg-gray-400',
                                            'badge' => 'bg-gray-100 text-gray-800',
                                        ],
                                        'Menunggu Persetujuan' => [
                                            'dot' => 'bg-yellow-400',
                                            'badge' => 'bg-yellow-100 text-yellow-800',
                                        ],
                                        'Disetujui' => [
                                            'dot' => 'bg-green-400',
                                            'badge' => 'bg-green-100 text-green-800',
                                        ],
                                        'Ditolak' => [
                                            'dot' => 'bg-red-400',
                                            'badge' => 'bg-red-100 text-red-800',
                                        ],
                                    ];
                                @endphp

                                @foreach ($statusKrs as $item)
                                    @php
                                        $colors = $statusColors[$item['name']] ?? [
                                            'dot' => 'bg-blue-400',
                                            'badge' => 'bg-blue-100 text-blue-800',
                                        ];
                                    @endphp

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full mr-3 {{ $colors['dot'] }}"></div>
                                            <span class="text-sm font-medium text-gray-700">{{ $item['name'] }}</span>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors['badge'] }}">
                                            {{ $item['value'] }}
                                        </span>
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
                                        Pilih semester untuk melihat status KRS
                                    @else
                                        Belum ada data KRS untuk semester ini
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- KRS Pending Approval -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">KRS Pending Approval</h3>
                        <p class="text-sm text-gray-500">
                            @if ($selectedSemester)
                                {{ $selectedSemester->nama_semester }}
                            @else
                                Pilih semester
                            @endif
                        </p>
                    </div>
                    <div class="p-6">
                        @if ($selectedSemester && $pendingKrs->count() > 0)
                            <div class="space-y-4">
                                @foreach ($pendingKrs as $krs)
                                    <div
                                        class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $krs->mahasiswa->pengguna->nama }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ $krs->mahasiswa->programStudi->nama_program_studi }}</p>
                                            <p class="text-xs text-gray-400">{{ $krs->tanggal_submit->diffForHumans() }}
                                            </p>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            @if ($pendingKrs->count() >= 5)
                                <div class="mt-4 text-center">
                                    <a href="#" class="text-sm text-blue-600 hover:text-blue-500">Lihat semua</a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4 text-gray-500">
                                <p class="text-sm">
                                    @if (!$selectedSemester)
                                        Pilih semester untuk melihat KRS pending
                                    @else
                                        Tidak ada KRS pending
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Users Need Activation -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Pengguna Perlu Aktivasi</h3>
                        <p class="text-sm text-gray-500">Akun yang belum diaktifkan</p>
                    </div>
                    <div class="p-6">
                        @if ($pendingUsers->count() > 0)
                            <div class="space-y-4">
                                @foreach ($pendingUsers as $user)
                                    <div
                                        class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $user->nama }}</p>
                                            <p class="text-xs text-gray-500">{{ ucfirst($user->role) }} •
                                                {{ $user->username }}</p>
                                            <p class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            @if ($pendingUsers->count() >= 5)
                                <div class="mt-4 text-center">
                                    <a href="#" class="text-sm text-blue-600 hover:text-blue-500">Lihat semua</a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4 text-gray-500">
                                <p class="text-sm">Semua pengguna aktif</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Kelas Tanpa Dosen -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Kelas Tanpa Dosen</h3>
                        <p class="text-sm text-gray-500">
                            @if ($selectedSemester)
                                {{ $selectedSemester->nama_semester }}
                            @else
                                Pilih semester
                            @endif
                        </p>
                    </div>
                    <div class="p-6">
                        @if ($selectedSemester && $kelasWithoutDosen->count() > 0)
                            <div class="space-y-4">
                                @foreach ($kelasWithoutDosen as $kelas)
                                    <div class="py-2 border-b border-gray-100 last:border-b-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $kelas->nama_kelas_kuliah }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $kelas->kurikulumMataKuliah->mataKuliah->nama_mata_kuliah }}</p>
                                        <p class="text-xs text-gray-400">{{ $kelas->hari }} •
                                            {{ $kelas->jam_mulai }}-{{ $kelas->jam_akhir }}</p>
                                    </div>
                                @endforeach
                            </div>
                            @if ($kelasWithoutDosen->count() >= 5)
                                <div class="mt-4 text-center">
                                    <a href="#" class="text-sm text-blue-600 hover:text-blue-500">Lihat semua</a>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4 text-gray-500">
                                <p class="text-sm">
                                    @if (!$selectedSemester)
                                        Pilih semester untuk melihat kelas tanpa dosen
                                    @else
                                        Semua kelas sudah memiliki dosen
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
