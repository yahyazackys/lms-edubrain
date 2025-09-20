@extends('layouts.app')

@section('title', 'Monitoring KRS - Admin Dashboard')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Monitoring KRS</h1>
                            <p class="text-sm text-gray-600">Dashboard Administrasi Sistem KRS -
                                {{ $selectedSemester->nama_semester ?? 'Pilih Semester' }}</p>
                        </div>

                        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                            @if ($selectedSemester)
                                <!-- Quick Actions -->
                                <button onclick="openMassActivationModal()"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Aktivasi Mass KRS
                                </button>

                                <a href="{{ route('admin.krs.laporan', ['semester' => $selectedSemester->id_semester]) }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Detail Laporan
                                </a>

                                <a href="{{ route('admin.krs.statistik-kelas', ['semester' => $selectedSemester->id_semester]) }}"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                    Statistik Kelas
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Filter Semester -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="flex items-center space-x-3">
                        <label class="text-xs font-medium text-gray-700">Semester:</label>
                        <select id="semesterFilter" onchange="changeSemester()"
                            class="text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[200px]">
                            <option value="">Pilih Semester</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id_semester }}"
                                    {{ request('semester') == $semester->id_semester ? 'selected' : '' }}
                                    data-active="{{ $semester->is_active ? '1' : '0' }}">
                                    {{ $semester->nama_semester }}
                                    {{ $semester->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            @if (!$selectedSemester)
                <!-- Empty State - Pilih Semester -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-calendar-alt w-16 h-16 text-gray-400 mb-4 mx-auto text-6xl"></i>
                        <p class="text-lg font-medium text-gray-900 mb-2">Pilih Semester</p>
                        <p class="text-sm text-gray-500 mb-4">Pilih semester dari dropdown di atas untuk melihat monitoring
                            KRS</p>
                    </div>
                </div>
            @else
                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                        <div class="flex">
                            <svg class="w-4 h-4 text-green-400 mt-0.5 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Statistics Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Mahasiswa</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $statistikUmum['total_mahasiswa'] }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Progress</span>
                                <span>{{ $statistikUmum['total_mahasiswa'] > 0 ? round(($statistikUmum['submitted'] / $statistikUmum['total_mahasiswa']) * 100) : 0 }}%</span>
                            </div>
                            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full"
                                    style="width: {{ $statistikUmum['total_mahasiswa'] > 0 ? min(($statistikUmum['submitted'] / $statistikUmum['total_mahasiswa']) * 100, 100) : 0 }}%">
                                </div>
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
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Belum Submit</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $statistikUmum['belum_submit'] }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-500">
                                {{ $statistikUmum['total_mahasiswa'] > 0 ? round(($statistikUmum['belum_submit'] / $statistikUmum['total_mahasiswa']) * 100) : 0 }}%
                                dari total mahasiswa
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
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Sudah Disetujui</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $statistikUmum['approved'] }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-500">
                                {{ $statistikUmum['submitted'] > 0 ? round(($statistikUmum['approved'] / $statistikUmum['submitted']) * 100) : 0 }}%
                                dari yang submit
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Rata-rata SKS</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $statistikUmum['avg_sks'] }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-xs text-gray-500">
                                Per mahasiswa yang submit
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Statistik per Program Studi -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">Statistik per Program Studi</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @forelse($statistikProdi as $prodi)
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <h4 class="text-xs font-medium text-gray-900">
                                                    {{ $prodi->nama_program_studi }}</h4>
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $prodi->kode_jenjang_pendidikan }}
                                                </span>
                                            </div>
                                            <div class="mt-2 flex items-center space-x-4 text-xs text-gray-600">
                                                <span>{{ $prodi->total_mahasiswa }} mahasiswa</span>
                                                <span>{{ $prodi->submitted }} submit</span>
                                                <span>{{ $prodi->approved }} disetujui</span>
                                            </div>
                                            <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-green-600 h-1.5 rounded-full"
                                                    style="width: {{ $prodi->total_mahasiswa > 0 ? min(($prodi->approved / $prodi->total_mahasiswa) * 100, 100) : 0 }}%">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-4 text-right">
                                            <div class="text-xs font-medium text-gray-900">
                                                {{ $prodi->total_mahasiswa > 0 ? round(($prodi->approved / $prodi->total_mahasiswa) * 100) : 0 }}%
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6">
                                        <p class="text-sm text-gray-500">Tidak ada data program studi</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Top Mata Kuliah -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">Top Mata Kuliah Dipilih</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @forelse($topMataKuliah as $index => $mk)
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-8 h-8 {{ $index < 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' }} rounded-full flex items-center justify-center">
                                                <span class="text-xs font-bold">{{ $index + 1 }}</span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-900 truncate">
                                                {{ $mk->kode_mata_kuliah }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ $mk->nama_mata_kuliah }}</p>
                                        </div>
                                        <div class="flex-shrink-0 text-right">
                                            <p class="text-xs font-medium text-gray-900">{{ $mk->jumlah_peminat }}</p>
                                            <p class="text-xs text-gray-500">{{ $mk->sks_mata_kuliah }} SKS</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6">
                                        <p class="text-sm text-gray-500">Tidak ada data mata kuliah</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Statistik per PA -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">Performance Pembimbing Akademik</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4 max-h-80 overflow-y-auto">
                                @forelse($statistikPA as $pa)
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <h4 class="text-xs font-medium text-gray-900">{{ $pa->nama_dosen }}</h4>
                                                @if ($pa->nidn)
                                                    <span class="text-xs text-gray-500">({{ $pa->nidn }})</span>
                                                @endif
                                            </div>
                                            <div class="mt-2 flex items-center space-x-4 text-xs text-gray-600">
                                                <span>{{ $pa->total_mahasiswa }} mahasiswa</span>
                                                <span>{{ $pa->submitted }} submit</span>
                                                <span>{{ $pa->approved }} approved</span>
                                            </div>
                                            <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-blue-600 h-1.5 rounded-full"
                                                    style="width: {{ $pa->submitted > 0 ? min(($pa->approved / $pa->submitted) * 100, 100) : 0 }}%">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-4 text-right">
                                            <div class="text-xs font-medium text-gray-900">
                                                {{ $pa->submitted > 0 ? round(($pa->approved / $pa->submitted) * 100) : 0 }}%
                                            </div>
                                            <div class="text-xs text-gray-500">completion</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6">
                                        <p class="text-sm text-gray-500">Tidak ada data pembimbing akademik</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Progress Timeline -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">Progress Harian (30 Hari Terakhir)</h3>
                        </div>
                        <div class="p-6">
                            @if (count($progressHarian) > 0)
                                <div class="space-y-3">
                                    @foreach ($progressHarian->take(10) as $progress)
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                                <span
                                                    class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($progress->tanggal)->format('d M Y') }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span
                                                    class="text-xs font-medium text-gray-900">{{ $progress->jumlah_submit }}</span>
                                                <span class="text-xs text-gray-500">submit</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Simple Chart Visualization -->
                                <div class="mt-6 h-32 flex items-end justify-between space-x-1">
                                    @php
                                        $maxSubmit = $progressHarian->max('jumlah_submit') ?: 1;
                                    @endphp
                                    @foreach ($progressHarian->take(15) as $progress)
                                        <div class="flex-1 bg-blue-200 hover:bg-blue-300 transition-colors duration-200 rounded-t"
                                            style="height: {{ ($progress->jumlah_submit / $maxSubmit) * 100 }}%"
                                            title="{{ \Carbon\Carbon::parse($progress->tanggal)->format('d M') }}: {{ $progress->jumlah_submit }} submit">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <p class="text-sm text-gray-500">Tidak ada data progress harian</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Mass Activation Modal -->
    @if ($selectedSemester)
        <div id="massActivationModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    onclick="closeMassActivationModal()"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">
                    <form method="POST" action="{{ route('admin.krs.aktivasi-mass') }}">
                        @csrf
                        <input type="hidden" name="semester_id" value="{{ $selectedSemester->id_semester }}">

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
                                        Aktivasi Mass KRS
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Aktifkan KRS yang sudah disetujui untuk {{ $selectedSemester->nama_semester }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Filter Program Studi (Opsional)
                                    </label>
                                    <div class="space-y-2 max-h-32 overflow-y-auto">
                                        @foreach ($programStudis as $prodi)
                                            <label class="flex items-center">
                                                <input type="checkbox" name="program_studi_ids[]"
                                                    value="{{ $prodi->id_program_studi }}"
                                                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                                <span
                                                    class="ml-2 text-xs text-gray-700">{{ $prodi->jenjang->kode_jenjang_pendidikan }}
                                                    - {{ $prodi->nama_program_studi }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Kosongkan untuk memilih semua program studi</p>
                                </div>

                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <div class="flex">
                                        <svg class="w-4 h-4 text-yellow-400 mt-0.5 mr-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z">
                                            </path>
                                        </svg>
                                        <div class="text-xs text-yellow-800">
                                            <p class="font-medium">Perhatian:</p>
                                            <p class="mt-1">Hanya KRS dengan status "APPROVED" yang akan diaktivasi.
                                                Proses ini akan mengubah status KRS menjadi "ACTIVE".</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                            <button type="button" onclick="closeMassActivationModal()"
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
    @endif

    @push('scripts')
        <script>
            // Change semester function
            function changeSemester() {
                const semesterId = document.getElementById('semesterFilter').value;
                if (semesterId) {
                    window.location.href = '{{ route('admin.krs.index') }}?semester=' + semesterId;
                } else {
                    window.location.href = '{{ route('admin.krs.index') }}';
                }
            }

            // Mass activation modal
            function openMassActivationModal() {
                document.getElementById('massActivationModal').classList.remove('hidden');
            }

            function closeMassActivationModal() {
                document.getElementById('massActivationModal').classList.add('hidden');
            }

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeMassActivationModal();
                }
            });

            // Auto refresh every 5 minutes for real-time monitoring
            @if ($selectedSemester)
                setInterval(function() {
                    location.reload();
                }, 300000); // 5 minutes
            @endif
        </script>
    @endpush
@endsection
