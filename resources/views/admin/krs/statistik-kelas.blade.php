@extends('layouts.app')

@section('title', 'Statistik Kelas - Admin KRS')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Statistik Kelas Kuliah</h1>
                            <p class="text-sm text-gray-600">Analisis kapasitas dan utilisasi kelas -
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
                                <button onclick="printStatistik()"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                        </path>
                                    </svg>
                                    Print Statistik
                                </button>

                                <button onclick="exportStatistik()"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Export Data
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <form method="GET" id="filterForm">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Semester Filter -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Semester</label>
                                <select name="semester" onchange="document.getElementById('filterForm').submit()"
                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Pilih Semester</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id_semester }}"
                                            {{ request('semester') == $semester->id_semester ? 'selected' : '' }}>
                                            {{ $semester->nama_semester }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if ($selectedSemester)
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

                                <!-- Filter by Status -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Filter Kapasitas</label>
                                    <select name="status_kapasitas"
                                        onchange="document.getElementById('filterForm').submit()"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                        <option value="">Semua Kelas</option>
                                        <option value="penuh"
                                            {{ request('status_kapasitas') == 'penuh' ? 'selected' : '' }}>Kelas Penuh
                                            (100%)</option>
                                        <option value="hampir_penuh"
                                            {{ request('status_kapasitas') == 'hampir_penuh' ? 'selected' : '' }}>Hampir
                                            Penuh (≥80%)</option>
                                        <option value="sedang"
                                            {{ request('status_kapasitas') == 'sedang' ? 'selected' : '' }}>Sedang (50-79%)
                                        </option>
                                        <option value="kosong"
                                            {{ request('status_kapasitas') == 'kosong' ? 'selected' : '' }}>Masih Kosong (
                                            <50%)< /option>
                                    </select>
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
                        <p class="text-sm text-gray-500 mb-4">Pilih semester dari dropdown di atas untuk melihat statistik
                            kelas</p>
                    </div>
                </div>
            @else
                <!-- Summary Cards -->
                @if (count($statistikKelas) > 0)
                    @php
                        $totalKelas = count($statistikKelas);
                        $kelasPenuh = collect($statistikKelas)->where('status_kapasitas', 'penuh')->count();
                        $kelasHampirPenuh = collect($statistikKelas)
                            ->where('status_kapasitas', 'hampir_penuh')
                            ->count();
                        $totalKapasitas = collect($statistikKelas)->sum('kapasitas');
                        $totalPeserta = collect($statistikKelas)->sum('jumlah_peserta');
                        $rataUtilisasi = $totalKapasitas > 0 ? round(($totalPeserta / $totalKapasitas) * 100, 1) : 0;
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m6 0h2m2 0H7m6 0v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v6">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Kelas</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $totalKelas }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Kelas Penuh</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $kelasPenuh }}</dd>
                                    </dl>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="text-xs text-gray-500">
                                    {{ $totalKelas > 0 ? round(($kelasPenuh / $totalKelas) * 100, 1) : 0 }}% dari total
                                    kelas
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Hampir Penuh</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $kelasHampirPenuh }}</dd>
                                    </dl>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="text-xs text-gray-500">
                                    {{ $totalKelas > 0 ? round(($kelasHampirPenuh / $totalKelas) * 100, 1) : 0 }}% dari
                                    total kelas
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Utilisasi</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $rataUtilisasi }}%</dd>
                                    </dl>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="text-xs text-gray-500">
                                    {{ $totalPeserta }}/{{ $totalKapasitas }} peserta
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Detailed Statistics Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900">Detail Statistik Kelas</h3>
                            <div class="text-xs text-gray-600">
                                {{ count($statistikKelas) }} kelas ditemukan
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="statistikTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mata Kuliah
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kelas
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Program Studi
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dosen
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kapasitas
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Peserta
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Utilisasi
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($statistikKelas as $index => $kelas)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs font-medium text-gray-900">
                                                {{ $kelas['kode_mata_kuliah'] }} - {{ $kelas['mata_kuliah'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs font-medium text-gray-900">{{ $kelas['nama_kelas'] }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs text-gray-900">{{ $kelas['program_studi'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-xs text-gray-900">{{ $kelas['dosen'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="text-xs font-medium text-gray-900">{{ $kelas['kapasitas'] }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="text-xs font-medium text-gray-900">{{ $kelas['jumlah_peserta'] }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center">
                                                <span
                                                    class="text-xs font-medium text-gray-900">{{ $kelas['persentase_kapasitas'] }}%</span>
                                                <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                    <div class="h-2 rounded-full {{ $kelas['persentase_kapasitas'] >= 100 ? 'bg-red-500' : ($kelas['persentase_kapasitas'] >= 80 ? 'bg-yellow-500' : ($kelas['persentase_kapasitas'] >= 50 ? 'bg-blue-500' : 'bg-green-500')) }}"
                                                        style="width: {{ min($kelas['persentase_kapasitas'], 100) }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($kelas['status_kapasitas'] === 'penuh')
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Penuh
                                                </span>
                                            @elseif($kelas['status_kapasitas'] === 'hampir_penuh')
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z">
                                                        </path>
                                                    </svg>
                                                    Hampir Penuh
                                                </span>
                                            @elseif($kelas['status_kapasitas'] === 'sedang')
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                    Sedang
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Tersedia
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m6 0h2m2 0H7m6 0v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v6">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium text-gray-900 mb-2">Tidak ada data kelas</p>
                                                <p class="text-xs text-gray-500">Tidak ada kelas yang sesuai dengan filter
                                                    yang dipilih</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Additional Analytics -->
                @if (count($statistikKelas) > 0)
                    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Top Program Studi by Utilization -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-900">Utilisasi per Program Studi</h3>
                            </div>
                            <div class="p-6">
                                @php
                                    $prodiStats = collect($statistikKelas)
                                        ->groupBy('program_studi')
                                        ->map(function ($kelasPerProdi, $prodi) {
                                            $totalKapasitas = $kelasPerProdi->sum('kapasitas');
                                            $totalPeserta = $kelasPerProdi->sum('jumlah_peserta');
                                            $utilisasi =
                                                $totalKapasitas > 0
                                                    ? round(($totalPeserta / $totalKapasitas) * 100, 1)
                                                    : 0;

                                            return [
                                                'program_studi' => $prodi,
                                                'total_kelas' => $kelasPerProdi->count(),
                                                'total_kapasitas' => $totalKapasitas,
                                                'total_peserta' => $totalPeserta,
                                                'utilisasi' => $utilisasi,
                                            ];
                                        })
                                        ->sortByDesc('utilisasi');
                                @endphp

                                <div class="space-y-4">
                                    @foreach ($prodiStats->take(5) as $stat)
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2">
                                                    <h4 class="text-xs font-medium text-gray-900">
                                                        {{ $stat['program_studi'] }}</h4>
                                                </div>
                                                <div class="mt-2 flex items-center space-x-4 text-xs text-gray-600">
                                                    <span>{{ $stat['total_kelas'] }} kelas</span>
                                                    <span>{{ $stat['total_peserta'] }}/{{ $stat['total_kapasitas'] }}</span>
                                                </div>
                                                <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                                                    <div class="bg-blue-600 h-1.5 rounded-full"
                                                        style="width: {{ min($stat['utilisasi'], 100) }}%"></div>
                                                </div>
                                            </div>
                                            <div class="ml-4 text-right">
                                                <div class="text-xs font-medium text-gray-900">
                                                    {{ $stat['utilisasi'] }}%
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Status Distribution -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-900">Distribusi Status Kapasitas</h3>
                            </div>
                            <div class="p-6">
                                @php
                                    $statusDistribution = collect($statistikKelas)
                                        ->groupBy('status_kapasitas')
                                        ->map(function ($items, $status) {
                                            return [
                                                'status' => $status,
                                                'count' => $items->count(),
                                                'percentage' =>
                                                    count($GLOBALS['statistikKelas']) > 0
                                                        ? round(
                                                            ($items->count() / count($GLOBALS['statistikKelas'])) * 100,
                                                            1,
                                                        )
                                                        : 0,
                                            ];
                                        });
                                    $GLOBALS['statistikKelas'] = $statistikKelas;
                                @endphp

                                <div class="space-y-4">
                                    @if ($statusDistribution->has('penuh'))
                                        <div
                                            class="flex items-center justify-between p-3 border border-red-200 rounded-lg bg-red-50">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-xs font-medium text-red-900">Kelas Penuh</p>
                                                    <p class="text-xs text-red-700">100% kapasitas</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-red-900">
                                                    {{ $statusDistribution['penuh']['count'] }}</p>
                                                <p class="text-xs text-red-700">
                                                    {{ $statusDistribution['penuh']['percentage'] }}%</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($statusDistribution->has('hampir_penuh'))
                                        <div
                                            class="flex items-center justify-between p-3 border border-yellow-200 rounded-lg bg-yellow-50">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-xs font-medium text-yellow-900">Hampir Penuh</p>
                                                    <p class="text-xs text-yellow-700">80-99% kapasitas</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-yellow-900">
                                                    {{ $statusDistribution['hampir_penuh']['count'] }}</p>
                                                <p class="text-xs text-yellow-700">
                                                    {{ $statusDistribution['hampir_penuh']['percentage'] }}%</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($statusDistribution->has('sedang'))
                                        <div
                                            class="flex items-center justify-between p-3 border border-blue-200 rounded-lg bg-blue-50">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-xs font-medium text-blue-900">Sedang</p>
                                                    <p class="text-xs text-blue-700">50-79% kapasitas</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-blue-900">
                                                    {{ $statusDistribution['sedang']['count'] }}</p>
                                                <p class="text-xs text-blue-700">
                                                    {{ $statusDistribution['sedang']['percentage'] }}%</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($statusDistribution->has('kosong'))
                                        <div
                                            class="flex items-center justify-between p-3 border border-green-200 rounded-lg bg-green-50">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-xs font-medium text-green-900">Masih Kosong</p>
                                                    <p class="text-xs text-green-700">
                                                        < 50% kapasitas</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-green-900">
                                                    {{ $statusDistribution['kosong']['count'] }}</p>
                                                <p class="text-xs text-green-700">
                                                    {{ $statusDistribution['kosong']['percentage'] }}%</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Print function
            function printStatistik() {
                window.print();
            }

            // Export function
            function exportStatistik() {
                const params = new URLSearchParams(window.location.search);
                params.set('format', 'excel');
                window.location.href = '{{ route('admin.krs.export') }}?' + params.toString();
            }

            // Auto submit on filter change with debounce
            let filterTimeout;
            const filterInputs = document.querySelectorAll('select[name="status_kapasitas"]');
            filterInputs.forEach(input => {
                input.addEventListener('change', function() {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(function() {
                        document.getElementById('filterForm').submit();
                    }, 300);
                });
            });

            // Add sorting functionality to table headers (simple client-side sorting)
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.getElementById('statistikTable');
                if (table) {
                    const headers = table.querySelectorAll('th[scope="col"]');
                    headers.forEach((header, index) => {
                        if (index > 0 && index !== 8) { // Skip "No" and "Status" columns
                            header.style.cursor = 'pointer';
                            header.addEventListener('click', function() {
                                sortTable(index);
                            });
                        }
                    });
                }
            });

            function sortTable(columnIndex) {
                const table = document.getElementById('statistikTable');
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));

                const sortedRows = rows.sort((a, b) => {
                    const aValue = a.cells[columnIndex].textContent.trim();
                    const bValue = b.cells[columnIndex].textContent.trim();

                    // Check if values are numeric
                    if (!isNaN(aValue) && !isNaN(bValue)) {
                        return parseFloat(bValue) - parseFloat(aValue);
                    }

                    return aValue.localeCompare(bValue);
                });

                // Clear and re-append sorted rows
                tbody.innerHTML = '';
                sortedRows.forEach((row, index) => {
                    // Update row numbers
                    row.cells[0].textContent = index + 1;
                    tbody.appendChild(row);
                });
            }
        </script>
    @endpush
@endsection
