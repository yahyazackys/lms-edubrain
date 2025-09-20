@extends('layouts.app')

@section('title', 'Pilih Mata Kuliah - KRS')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">

            @php
                // Cek apakah ada mata kuliah yang ditolak
                $hasRejectedCourses = $mataKuliahTerpilih->where('status_mata_kuliah', 'REJECTED')->count() > 0;

                // Cek apakah bisa review KRS
                $canReviewKrs = $mataKuliahTerpilih->count() > 0 && !$hasRejectedCourses;
            @endphp

            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Pilih Mata Kuliah</h1>
                            <p class="text-sm text-gray-600">Semester {{ $selectedSemester->nama_semester }} •
                                {{ $mahasiswa->programStudi->nama_program_studi }}</p>
                        </div>

                        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                            <a href="{{ route('krs.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Dashboard
                            </a>

                            @if ($mataKuliahTerpilih->count() > 0)
                                @if ($canReviewKrs)
                                    <a href="{{ route('krs.review', $selectedSemester->id_semester) }}"
                                        class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                        Review KRS
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                @else
                                    <div class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 text-xs font-medium rounded-lg cursor-not-allowed"
                                        title="Selesaikan mata kuliah yang ditolak terlebih dahulu">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Selesaikan Mata Kuliah Ditolak
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <p class="text-lg font-semibold text-gray-900" id="totalSelected">
                                {{ $mataKuliahTerpilih->count() }}</p>
                            <p class="text-xs text-gray-500">Mata Kuliah Dipilih</p>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900" id="totalSks">
                                {{ $mataKuliahTerpilih->sum(function ($p) {return $p->mataKuliah->sks_mata_kuliah;}) }}</p>
                            <p class="text-xs text-gray-500">Total SKS</p>
                        </div>
                        <div>
                            {{-- Gunakan batas SKS yang dinamis berdasarkan IPK --}}
                            <p class="text-lg font-semibold text-gray-900">{{ $batasSks ?? 21 }}</p>
                            <p class="text-xs text-gray-500">Batas Maksimal SKS</p>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900">{{ $mataKuliahTersedia->count() }}</p>
                            <p class="text-xs text-gray-500">Mata Kuliah Tersedia</p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($hasRejectedCourses)
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <svg class="w-4 h-4 text-red-400 mt-0.5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium">Ada
                                {{ $mataKuliahTerpilih->where('status_mata_kuliah', 'REJECTED')->count() }} mata kuliah yang
                                ditolak oleh Pembimbing Akademik</p>
                            <p class="text-xs mt-1">Silakan hapus mata kuliah yang ditolak dan pilih kelas pengganti sebelum
                                melakukan review KRS.</p>
                        </div>
                    </div>
                </div>
            @endif

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

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <svg class="w-4 h-4 text-red-400 mt-0.5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Filter & Search -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
                        <!-- Filter -->
                        <div class="flex items-center space-x-3">
                            <label class="text-xs font-medium text-gray-700">Filter:</label>
                            <select id="jenisFilter" onchange="filterMataKuliah()"
                                class="text-xs py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">Semua Jenis</option>
                                <option value="WAJIB">Mata Kuliah Wajib</option>
                                <option value="PILIHAN">Mata Kuliah Pilihan</option>
                            </select>
                        </div>

                        <!-- Search -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" id="searchInput" placeholder="Cari mata kuliah..."
                                onkeyup="filterMataKuliah()"
                                class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <!-- Main Content -->
            @if ($mataKuliahTersedia->count() > 0)
                <!-- Grid Container - 1 kolom mobile, 2 kolom desktop -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="mataKuliahContainer">
                    @foreach ($mataKuliahTersedia as $idMataKuliah => $kurikulumMataKuliahs)
                        @php
                            $kurikulumMataKuliah = $kurikulumMataKuliahs->first();
                            $mataKuliah = $kurikulumMataKuliah->mataKuliah;
                            $kelasKuliahs = $kurikulumMataKuliah->kelasKuliah;
                            $hasKelas = $kelasKuliahs->count() > 0;
                        @endphp

                        @php
                            // ✅ CHECK: Apakah mata kuliah ini sudah memiliki kelas yang dipilih?
                            $selectedKelasForMataKuliah = $mataKuliahTerpilih
                                ->where('id_mata_kuliah', $mataKuliah->id_mata_kuliah)
                                ->first();
                            $hasSelectedKelas = $selectedKelasForMataKuliah !== null;
                            $selectedKelasId = $hasSelectedKelas ? $selectedKelasForMataKuliah->id_kelas_kuliah : null;
                            $selectedKelasName = $hasSelectedKelas
                                ? $selectedKelasForMataKuliah->kelasKuliah->nama_kelas_kuliah ?? 'Kelas yang dipilih'
                                : '';

                            // ✅ NEW: Cek status mata kuliah yang sudah dipilih
                            $selectedStatus = $hasSelectedKelas
                                ? $selectedKelasForMataKuliah->status_mata_kuliah
                                : null;
                            $isApproved = $selectedStatus === 'APPROVED';
                            $isRejected = $selectedStatus === 'REJECTED';
                            $isPending = $selectedStatus === 'SELECTED';
                        @endphp

                        <div class="mata-kuliah-card bg-white rounded-lg shadow-sm border border-gray-100 h-fit"
                            data-jenis="{{ $kurikulumMataKuliah->jenis_mata_kuliah }}"
                            data-semester="{{ $kurikulumMataKuliah->semester }}"
                            data-search="{{ strtolower($mataKuliah->kode_mata_kuliah . ' ' . $mataKuliah->nama_mata_kuliah) }}">

                            <!-- Header Mata Kuliah -->
                            <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
                                <div class="space-y-3">
                                    <!-- Info Mata Kuliah -->
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $mataKuliah->kode_mata_kuliah }}
                                        </h3>
                                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">
                                            {{ $mataKuliah->nama_mata_kuliah }}
                                        </p>
                                    </div>

                                    <!-- Badges -->
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 whitespace-nowrap">
                                            {{ $mataKuliah->sks_mata_kuliah }} SKS
                                        </span>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 whitespace-nowrap">
                                            Semester {{ $kurikulumMataKuliah->semester }}
                                        </span>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium whitespace-nowrap {{ $kurikulumMataKuliah->jenis_mata_kuliah === 'WAJIB' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $kurikulumMataKuliah->jenis_mata_kuliah }}
                                        </span>

                                        {{-- ✅ NEW: Badge status mata kuliah jika sudah dipilih --}}
                                        @if ($hasSelectedKelas)
                                            @if ($isApproved)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 whitespace-nowrap">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Disetujui PA
                                                </span>
                                            @elseif ($isRejected)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 whitespace-nowrap">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    Ditolak PA
                                                </span>
                                            @elseif ($isPending)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 whitespace-nowrap">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Menunggu Verifikasi
                                                </span>
                                            @endif
                                        @endif

                                        <!-- Status Badge -->
                                        @if (!$hasKelas)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 whitespace-nowrap">
                                                Belum Dibuka
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Kelas atau Status Belum Dibuka -->
                            <!-- Daftar Kelas atau Status Belum Dibuka -->
                            <div class="px-4 sm:px-6 py-4">
                                @if ($hasKelas)
                                    @php
                                        // ✅ CHECK: Apakah mata kuliah ini sudah memiliki kelas yang dipilih?
                                        $selectedKelasForMataKuliah = $mataKuliahTerpilih
                                            ->where('id_mata_kuliah', $mataKuliah->id_mata_kuliah)
                                            ->first();
                                        $hasSelectedKelas = $selectedKelasForMataKuliah !== null;
                                        $selectedKelasId = $hasSelectedKelas
                                            ? $selectedKelasForMataKuliah->id_kelas_kuliah
                                            : null;
                                        $selectedKelasName = $hasSelectedKelas
                                            ? $selectedKelasForMataKuliah->kelasKuliah->nama_kelas_kuliah ??
                                                'Kelas yang dipilih'
                                            : '';

                                        // ✅ Cek status mata kuliah yang sudah dipilih
                                        $selectedStatus = $hasSelectedKelas
                                            ? $selectedKelasForMataKuliah->status_mata_kuliah
                                            : null;
                                        $isApproved = $selectedStatus === 'APPROVED';
                                        $isRejected = $selectedStatus === 'REJECTED';
                                        $isPending = $selectedStatus === 'SELECTED';
                                    @endphp

                                    {{-- ✅ JIKA MATA KULIAH DITOLAK: Hanya tampilkan kelas yang ditolak dengan opsi hapus --}}
                                    {{-- ✅ JIKA MATA KULIAH DITOLAK: Tampilkan kelas yang ditolak dengan layout konsisten --}}
                                    @if ($isRejected)
                                        {{-- Tampilkan hanya kelas yang ditolak dengan tombol hapus dalam layout grid yang konsisten --}}
                                        <h4 class="text-xs font-medium text-red-700 mb-3">
                                            Kelas yang Ditolak (Hapus untuk memilih pengganti):
                                        </h4>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            @foreach ($kelasKuliahs as $kelas)
                                                @if ($kelas->id_kelas_kuliah == $selectedKelasId)
                                                    <div
                                                        class="border-2 border-red-500 bg-red-50 rounded-lg p-3 transition-colors duration-200">
                                                        <!-- Header Kelas -->
                                                        <div class="flex items-start justify-between mb-2">
                                                            <div class="min-w-0 flex-1">
                                                                <h5 class="text-xs font-medium text-red-900 truncate mb-1">
                                                                    {{ $kelas->nama_kelas_kuliah ?? ($kelas->nama_kelas ?? 'Kelas ' . $loop->iteration) }}
                                                                </h5>

                                                                <span
                                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                                    <svg class="w-2.5 h-2.5 mr-1" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                    Ditolak PA
                                                                </span>
                                                            </div>

                                                            <!-- Button Action -->
                                                            <div class="flex-shrink-0 ml-2">
                                                                <button
                                                                    onclick="confirmRemoveRejectedKelas('{{ $selectedKelasForMataKuliah->id_peserta }}', '{{ addslashes($mataKuliah->nama_mata_kuliah) }}', '{{ addslashes($selectedKelasName) }}')"
                                                                    class="inline-flex items-center justify-center w-6 h-6 bg-red-600 text-white rounded hover:bg-red-700 transition-colors duration-200"
                                                                    title="Hapus kelas yang ditolak">
                                                                    <svg class="w-3 h-3" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                        </path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Info Detail Kelas -->
                                                        <div class="space-y-1 text-xs text-red-700">
                                                            <div class="flex justify-between">
                                                                <span class="text-red-500">Dosen:</span>
                                                                <span
                                                                    class="font-medium truncate ml-2 max-w-[70%]">{{ $kelas->dosen->pengguna->nama ?? 'Belum diatur' }}</span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-red-500">Ruangan:</span>
                                                                <span
                                                                    class="font-medium truncate ml-2 max-w-[70%]">{{ $kelas->nama_ruangan ?: 'Belum diatur' }}</span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-red-500">Hari:</span>
                                                                <span
                                                                    class="font-medium text-right ml-2 max-w-[70%]">{{ $kelas->hari ?? 'Belum diatur' }}</span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-red-500">Waktu:</span>
                                                                <span class="font-medium text-right ml-2 max-w-[70%]">
                                                                    @if ($kelas->jam_mulai && $kelas->jam_akhir)
                                                                        {{ \Carbon\Carbon::parse($kelas->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($kelas->jam_akhir)->format('H:i') }}
                                                                    @else
                                                                        Belum diatur
                                                                    @endif
                                                                </span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-red-500">Kapasitas:</span>
                                                                <span class="font-medium">
                                                                    {{ $kelas->jumlah_peserta ?? 0 }}/{{ $kelas->kapasitas ?? 0 }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        {{-- ✅ JIKA TIDAK ADA YANG DITOLAK: Tampilkan semua kelas normal --}}
                                    @else
                                        <h4 class="text-xs font-medium text-gray-700 mb-3">
                                            @if ($hasSelectedKelas)
                                                @if ($isApproved)
                                                    Kelas Terpilih (Sudah Disetujui PA):
                                                @else
                                                    Kelas Terpilih (Menunggu Verifikasi PA):
                                                @endif
                                            @else
                                                Pilih Kelas:
                                            @endif
                                        </h4>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            @foreach ($kelasKuliahs as $kelas)
                                                @php
                                                    $isSelected = $kelas->id_kelas_kuliah == $selectedKelasId;
                                                    $jumlahPeserta = $kelas->jumlah_peserta ?? 0;
                                                    $isPenuh = $kelas->isPenuh();

                                                    // Logic untuk disabled berdasarkan status
                                                    if ($isApproved && !$isSelected) {
                                                        $isDisabledDueToApproval = true;
                                                        $isDisabledDueToOtherSelection = false;
                                                    } elseif ($hasSelectedKelas && !$isSelected) {
                                                        $isDisabledDueToApproval = false;
                                                        $isDisabledDueToOtherSelection = true;
                                                    } else {
                                                        $isDisabledDueToApproval = false;
                                                        $isDisabledDueToOtherSelection = false;
                                                    }

                                                    $isDisabled =
                                                        $isPenuh ||
                                                        $isDisabledDueToOtherSelection ||
                                                        $isDisabledDueToApproval;
                                                @endphp

                                                <div
                                                    class="border border-gray-200 rounded-lg p-3 {{ $isSelected ? ($isApproved ? 'border-green-500 bg-green-50' : 'border-yellow-500 bg-yellow-50') : ($isDisabled ? 'border-gray-200 bg-gray-50 opacity-75' : 'hover:border-gray-300') }} transition-colors duration-200">

                                                    <!-- Header Kelas -->
                                                    <div class="flex items-start justify-between mb-2">
                                                        <div class="min-w-0 flex-1">
                                                            <h5
                                                                class="text-xs font-medium {{ $isDisabled && !$isSelected ? 'text-gray-500' : 'text-gray-900' }} truncate mb-1">
                                                                {{ $kelas->nama_kelas_kuliah ?? ($kelas->nama_kelas ?? 'Kelas ' . $loop->iteration) }}
                                                            </h5>

                                                            @if ($isSelected)
                                                                @if ($isApproved)
                                                                    <span
                                                                        class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                        <svg class="w-2.5 h-2.5 mr-1" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M9 12l2 2 4-4"></path>
                                                                        </svg>
                                                                        Disetujui PA
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                        <svg class="w-2.5 h-2.5 mr-1" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M12 8v4l3 3"></path>
                                                                        </svg>
                                                                        Dipilih
                                                                    </span>
                                                                @endif
                                                            @elseif($isPenuh)
                                                                <span
                                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                                    Penuh
                                                                </span>
                                                            @elseif($isDisabledDueToApproval)
                                                                <span
                                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                    <svg class="w-2.5 h-2.5 mr-1" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M12 15v2m-4-2h8m-8 0V9a4 4 0 118 0v4M7 19h10a2 2 0 002-2v-6a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2z">
                                                                        </path>
                                                                    </svg>
                                                                    Terkunci
                                                                </span>
                                                            @elseif($isDisabledDueToOtherSelection)
                                                                <span
                                                                    class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                                    Tidak Tersedia
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <!-- Button Action -->
                                                        <div class="flex-shrink-0 ml-2">
                                                            @if ($isSelected && $isApproved)
                                                                {{-- Mata kuliah sudah disetujui PA - tidak bisa dihapus --}}
                                                                <button disabled
                                                                    class="inline-flex items-center justify-center w-6 h-6 bg-green-200 text-green-600 rounded cursor-not-allowed"
                                                                    title="Sudah disetujui PA">
                                                                    <svg class="w-3 h-3" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M12 15v2m-4-2h8m-8 0V9a4 4 0 118 0v4M7 19h10a2 2 0 002-2v-6a2 2 0 00-2-2H7a2 2 0 00-2 2v6a2 2 0 002 2z">
                                                                        </path>
                                                                    </svg>
                                                                </button>
                                                            @elseif ($isSelected)
                                                                {{-- Mata kuliah pending - bisa dihapus --}}
                                                                <button
                                                                    onclick="confirmRemoveKelas('{{ $selectedKelasForMataKuliah->id_peserta }}', '{{ addslashes($mataKuliah->nama_mata_kuliah) }}', '{{ addslashes($kelas->nama_kelas_kuliah ?? 'Kelas') }}')"
                                                                    class="inline-flex items-center justify-center w-6 h-6 bg-red-600 text-white rounded hover:bg-red-700 transition-colors duration-200"
                                                                    title="Batalkan pilihan">
                                                                    <svg class="w-3 h-3" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            @elseif($isPenuh)
                                                                <button disabled
                                                                    class="inline-flex items-center justify-center w-6 h-6 bg-gray-300 text-gray-500 rounded cursor-not-allowed"
                                                                    title="Kelas Penuh">
                                                                    <svg class="w-3 h-3" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728">
                                                                        </path>
                                                                    </svg>
                                                                </button>
                                                            @elseif($isDisabled)
                                                                <button disabled
                                                                    class="inline-flex items-center justify-center w-6 h-6 bg-gray-300 text-gray-500 rounded cursor-not-allowed">
                                                                    <svg class="w-3 h-3" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                                    </svg>
                                                                </button>
                                                            @else
                                                                <button
                                                                    onclick="confirmAddKelas('{{ $kelas->id_kelas_kuliah }}', '{{ addslashes($mataKuliah->nama_mata_kuliah) }}', '{{ addslashes($kelas->nama_kelas_kuliah ?? 'Kelas') }}')"
                                                                    class="inline-flex items-center justify-center w-6 h-6 bg-gray-900 text-white rounded hover:bg-gray-800 transition-colors duration-200"
                                                                    title="Pilih">
                                                                    <svg class="w-3 h-3" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Info Detail Kelas -->
                                                    <div
                                                        class="space-y-1 text-xs {{ $isDisabled && !$isSelected ? 'text-gray-500' : 'text-gray-600' }}">
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-500">Dosen:</span>
                                                            <span
                                                                class="font-medium truncate ml-2 max-w-[70%]">{{ $kelas->dosen->pengguna->nama ?? 'Belum diatur' }}</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-500">Ruangan:</span>
                                                            <span
                                                                class="font-medium truncate ml-2 max-w-[70%]">{{ $kelas->nama_ruangan ?: 'Belum diatur' }}</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-500">Hari:</span>
                                                            <span
                                                                class="font-medium text-right ml-2 max-w-[70%]">{{ $kelas->hari ?? 'Belum diatur' }}</span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-500">Waktu:</span>
                                                            <span class="font-medium text-right ml-2 max-w-[70%]">
                                                                @if ($kelas->jam_mulai && $kelas->jam_akhir)
                                                                    {{ \Carbon\Carbon::parse($kelas->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($kelas->jam_akhir)->format('H:i') }}
                                                                @else
                                                                    Belum diatur
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-500">Kapasitas:</span>
                                                            <span
                                                                class="font-medium {{ $jumlahPeserta >= ($kelas->kapasitas ?? 0) * 0.8 ? 'text-orange-600' : '' }}">
                                                                {{ $jumlahPeserta }}/{{ $kelas->kapasitas ?? 0 }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <!-- Status Belum Dibuka -->
                                    <div class="text-center py-6">
                                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m0 0v2m0-2h2m-2 0h-2m4-6a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        <p class="text-sm font-medium text-gray-700 mb-1">Kelas Belum Dibuka</p>
                                        <p class="text-xs text-gray-500">Mata kuliah ini tersedia tapi belum ada kelas yang
                                            dibuka untuk semester ini.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-4 sm:px-6 py-12 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 7h.01M9 16h.01">
                            </path>
                        </svg>
                        <p class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Mata Kuliah Tersedia</p>
                        <p class="text-xs text-gray-500">Semua mata kuliah untuk semester ini sudah Anda ambil atau belum
                            memenuhi prasyarat.</p>
                    </div>
                </div>
            @endif

            <!-- Floating Action Button untuk Review -->
            @if ($mataKuliahTerpilih->count() > 0)
                <div class="fixed bottom-6 right-6">
                    @if ($canReviewKrs)
                        <a href="{{ route('krs.review', $selectedSemester->id_semester) }}"
                            class="inline-flex items-center px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-full shadow-lg hover:bg-gray-700 hover:shadow-xl transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                            Review KRS ({{ $mataKuliahTerpilih->count() }})
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmRemoveRejectedKelas(idPeserta, namaMataKuliah, namaKelas) {
                if (confirm(
                        `Hapus kelas "${namaKelas}" yang ditolak PA untuk mata kuliah "${namaMataKuliah}"?\n\nSetelah dihapus, Anda dapat memilih kelas pengganti.`
                    )) {
                    removeKelas(idPeserta);
                }
            }
        </script>
        <script>
            // Filter functionality
            function filterMataKuliah() {
                const jenisFilter = document.getElementById('jenisFilter').value;
                const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
                const cards = document.querySelectorAll('.mata-kuliah-card');

                let visibleCount = 0;

                cards.forEach(card => {
                    const jenis = card.getAttribute('data-jenis');
                    const searchData = card.getAttribute('data-search');

                    const matchesJenis = jenisFilter === '' || jenis === jenisFilter;
                    const matchesSearch = searchTerm === '' || searchData.includes(searchTerm);

                    const isMatch = matchesJenis && matchesSearch;

                    if (isMatch) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide empty state
                updateEmptyState(visibleCount, jenisFilter, searchTerm);
            }

            function clearAllFilters() {
                document.getElementById('jenisFilter').value = '';
                document.getElementById('searchInput').value = '';
                filterMataKuliah();
            }

            function updateEmptyState(visibleCount, jenisFilter, searchTerm) {
                // Remove existing empty state
                const existingEmpty = document.getElementById('filter-empty-state');
                if (existingEmpty) {
                    existingEmpty.remove();
                }

                // Only show empty state if there are filters applied and no results
                if (visibleCount === 0 && (jenisFilter !== '' || searchTerm !== '')) {
                    const container = document.getElementById('mataKuliahContainer');

                    if (container) {
                        const emptyState = document.createElement('div');
                        emptyState.id = 'filter-empty-state';
                        emptyState.className = 'bg-white rounded-lg shadow-sm border border-gray-100 mt-6';

                        let message = 'Tidak ada mata kuliah ditemukan';
                        let detail = '';

                        if (searchTerm && jenisFilter) {
                            detail = `untuk pencarian "${searchTerm}" dengan jenis ${jenisFilter}`;
                        } else if (searchTerm) {
                            detail = `untuk pencarian "${searchTerm}"`;
                        } else if (jenisFilter) {
                            detail = `dengan jenis ${jenisFilter}`;
                        }

                        emptyState.innerHTML = `
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <p class="text-lg font-medium text-gray-900 mb-2">${message}</p>
                    <p class="text-xs text-gray-500 mb-4">${detail}</p>
                    <button onclick="clearAllFilters()" class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset Filter
                    </button>
                </div>
            `;

                        container.parentNode.insertBefore(emptyState, container.nextSibling);
                    }
                }
            }

            function confirmAddKelas(idKelasKuliah, namaMataKuliah, namaKelas) {
                if (confirm(`Pilih kelas "${namaKelas}" untuk mata kuliah "${namaMataKuliah}"?`)) {
                    addKelas(idKelasKuliah);
                }
            }

            function confirmRemoveKelas(idPeserta, namaMataKuliah, namaKelas) {
                if (confirm(`Batalkan pilihan kelas "${namaKelas}" untuk mata kuliah "${namaMataKuliah}"?`)) {
                    removeKelas(idPeserta);
                }
            }

            function addKelas(idKelasKuliah) {
                // Show loading state
                showLoading(true);

                fetch('{{ route('krs.add-mata-kuliah') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id_kelas_kuliah: idKelasKuliah
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        showLoading(false);

                        if (data.success) {
                            // Reload page to update UI
                            location.reload();
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat menambah mata kuliah');
                        }
                    })
                    .catch(error => {
                        showLoading(false);
                        console.error('Error:', error);
                        alert('Terjadi kesalahan jaringan');
                    });
            }

            function removeKelas(idPeserta) {
                // Show loading state
                showLoading(true);

                fetch('{{ route('krs.remove-mata-kuliah') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id_peserta: idPeserta
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        showLoading(false);

                        if (data.success) {
                            // Reload page to update UI
                            location.reload();
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat menghapus mata kuliah');
                        }
                    })
                    .catch(error => {
                        showLoading(false);
                        console.error('Error:', error);
                        alert('Terjadi kesalahan jaringan');
                    });
            }

            function showLoading(show) {
                // Simple loading indicator
                if (show) {
                    document.body.style.cursor = 'wait';
                } else {
                    document.body.style.cursor = 'default';
                }
            }
        </script>
    @endpush
@endsection
