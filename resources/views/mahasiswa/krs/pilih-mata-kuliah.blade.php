@extends('layouts.app')

@section('title', 'Pilih Mata Kuliah - KRS')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            @php
                $hasRejectedCourses = $mataKuliahTerpilih->where('status_mata_kuliah', 'REJECTED')->count() > 0;
                $canReviewKrs = $mataKuliahTerpilih->count() > 0 && !$hasRejectedCourses;
                $totalSksTerpilih = $mataKuliahTerpilih->sum(function ($p) {
                    return $p->mataKuliah->sks_mata_kuliah;
                });
            @endphp

            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-lg font-semibold font-heading text-gray-900">Pilih Mata Kuliah</h1>
                            <p class="text-xs text-gray-600">{{ $selectedSemester->nama_semester }} •
                                {{ $mahasiswa->programStudi->nama_program_studi }}</p>
                        </div>

                        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                            <a href="{{ route('krs.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali ke Dashboard
                            </a>

                            @if ($mataKuliahTerpilih->count() > 0)
                                @if ($canReviewKrs)
                                    <a href="{{ route('krs.review', $selectedSemester->id_semester) }}"
                                        class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                        <i class="fas fa-eye mr-2"></i>
                                        Review KRS
                                    </a>
                                @else
                                    <div class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 text-xs font-medium rounded-lg cursor-not-allowed"
                                        title="Selesaikan mata kuliah yang ditolak terlebih dahulu">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Selesaikan Mata Kuliah Ditolak
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Summary Bar -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <p class="text-lg font-semibold text-gray-900" id="totalSelected">
                                {{ $mataKuliahTerpilih->count() }}</p>
                            <p class="text-xs text-gray-500">Mata Kuliah Dipilih</p>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900" id="totalSks">{{ $totalSksTerpilih }}</p>
                            <p class="text-xs text-gray-500">Total SKS</p>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900">{{ $batasSks }}</p>
                            <p class="text-xs text-gray-500">Batas Maksimal SKS</p>
                        </div>
                        <div>
                            @php
                                $totalTersedia = 0;
                                foreach ($mataKuliahTersedia as $semester => $mataKuliahs) {
                                    $totalTersedia += count($mataKuliahs);
                                }
                            @endphp
                            <p class="text-lg font-semibold text-gray-900">{{ $totalTersedia }}</p>
                            <p class="text-xs text-gray-500">Mata Kuliah Tersedia</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @if ($hasRejectedCourses)
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-red-400 mt-0.5 mr-3"></i>
                        <div>
                            <p class="text-xs font-medium">Ada
                                {{ $mataKuliahTerpilih->where('status_mata_kuliah', 'REJECTED')->count() }} mata kuliah yang
                                ditolak oleh Pembimbing Akademik</p>
                            <p class="text-xs mt-1">Silakan hapus mata kuliah yang ditolak dan pilih mata kuliah pengganti
                                sebelum melakukan review KRS.</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <i class="fas fa-check-circle text-green-400 mt-0.5 mr-3"></i>
                        <p class="text-xs font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mt-0.5 mr-3"></i>
                        <p class="text-xs font-medium">{{ session('error') }}</p>
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
                            <select id="kategoriFilter"
                                class="text-xs py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                onchange="filterMataKuliah()">
                                <option value="">Semua Kategori</option>
                                <option value="MKWUUPT">MK Wajib UUPT</option>
                                <option value="MKWU">MK Wajib Universitas</option>
                                <option value="MKWPS">MK Wajib Prodi</option>
                                <option value="MKP">MK Pilihan</option>
                            </select>
                            <select id="jenisFilter"
                                class="text-xs py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                onchange="filterMataKuliah()">
                                <option value="">Semua Jenis</option>
                                <option value="TEORI">Teori</option>
                                <option value="PRAKTIKUM">Praktikum</option>
                                <option value="KKN">KKN</option>
                                <option value="MAGANG">Magang</option>
                                <option value="SKRIPSI">Skripsi</option>
                            </select>
                        </div>

                        <!-- Search -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="searchInput" placeholder="Cari mata kuliah..."
                                onkeyup="filterMataKuliah()"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content - Semester Accordion -->
            @if (count($mataKuliahTersedia) > 0)
                <div class="space-y-4" id="semesterAccordion">
                    @foreach ($mataKuliahTersedia as $semester => $mataKuliahs)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 semester-section"
                            data-semester="{{ $semester }}">

                            <!-- Semester Header (Clickable) -->
                            <div class="px-6 py-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors duration-200"
                                onclick="toggleSemester({{ $semester }})">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-calendar-alt text-slate-600"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Semester {{ $semester }}
                                            </h3>
                                            <p class="text-xs text-gray-500">{{ count($mataKuliahs) }} mata kuliah tersedia
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3">
                                        @php
                                            $semesterTerpilih = $mataKuliahTerpilih->filter(function ($p) use (
                                                $semester,
                                            ) {
                                                return $p->semester == $semester;
                                            });
                                            $sksSemester = $semesterTerpilih->sum(function ($p) {
                                                return $p->mataKuliah->sks_mata_kuliah;
                                            });
                                        @endphp

                                        @if ($semesterTerpilih->count() > 0)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $semesterTerpilih->count() }} dipilih • {{ $sksSemester }} SKS
                                            </span>
                                        @endif

                                        <i class="fas fa-chevron-down text-gray-400 transform transition-transform duration-200"
                                            id="chevron-{{ $semester }}"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Semester Content (Collapsible) -->
                            <div class="semester-content hidden" id="content-{{ $semester }}">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Mata Kuliah</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Jenis</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    SKS</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Kelas/Info</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($mataKuliahs as $kurikulumMk)
                                                @php
                                                    $mataKuliah = $kurikulumMk->mataKuliah;
                                                    $kelasKuliahs = $kurikulumMk->kelasKuliah;
                                                    $isBimbingan = in_array($mataKuliah->jenis_mata_kuliah, [
                                                        'KKN',
                                                        'MAGANG',
                                                        'SKRIPSI',
                                                    ]);
                                                    $hasKelas = $kelasKuliahs->count() > 0;

                                                    // Cek apakah sudah dipilih
                                                    $isSelected = $mataKuliahTerpilih
                                                        ->where(
                                                            'mataKuliah.id_mata_kuliah',
                                                            $mataKuliah->id_mata_kuliah,
                                                        )
                                                        ->first();
                                                    $selectedStatus = $isSelected
                                                        ? $isSelected->status_mata_kuliah
                                                        : null;
                                                @endphp

                                                <tr class="mata-kuliah-row hover:bg-gray-50 transition-colors duration-200 
                                                    {{ $isSelected ? ($selectedStatus === 'APPROVED' ? 'bg-green-50' : ($selectedStatus === 'REJECTED' ? 'bg-red-50' : 'bg-yellow-50')) : '' }}"
                                                    data-kategori="{{ $kurikulumMk->kategori_mata_kuliah }}"
                                                    data-jenis="{{ $mataKuliah->jenis_mata_kuliah }}"
                                                    data-semester="{{ $semester }}"
                                                    data-search="{{ strtolower($mataKuliah->kode_mata_kuliah . ' ' . $mataKuliah->nama_mata_kuliah) }}">

                                                    <!-- Mata Kuliah Info -->
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 mr-3">
                                                                @if ($isBimbingan)
                                                                    <div
                                                                        class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                                        <i
                                                                            class="fas fa-graduation-cap text-blue-600 text-xs"></i>
                                                                    </div>
                                                                @else
                                                                    <div
                                                                        class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                                        <i class="fas fa-book text-green-600 text-xs"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <div class="text-xs font-medium text-gray-900">
                                                                    {{ $mataKuliah->kode_mata_kuliah }}</div>
                                                                <div class="text-xs text-gray-600">
                                                                    {{ $mataKuliah->nama_mata_kuliah }}</div>
                                                                <div class="text-xs text-gray-400 mt-1">
                                                                    {{ $kurikulumMk->kategori_mata_kuliah }}</div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <!-- Jenis -->
                                                    <td class="px-6 py-4">
                                                        @if ($isBimbingan)
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                <i class="fas fa-user-graduate mr-1"></i>
                                                                {{ $mataKuliah->jenis_mata_kuliah }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                                {{ $mataKuliah->jenis_mata_kuliah }}
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <!-- SKS -->
                                                    <td class="px-6 py-4">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ $mataKuliah->sks_mata_kuliah }} SKS
                                                        </span>
                                                    </td>

                                                    <!-- Kelas/Info -->
                                                    <td class="px-6 py-4">
                                                        @if ($isBimbingan)
                                                            <div class="text-xs text-gray-600">
                                                                <div class="flex items-center space-x-1">
                                                                    <i
                                                                        class="fas fa-calendar-check text-blue-500 text-xs"></i>
                                                                    <span>By Appointment</span>
                                                                </div>
                                                                <div class="text-xs text-gray-400 mt-1">Pembimbing akan
                                                                    diatur admin</div>
                                                            </div>
                                                        @elseif($hasKelas)
                                                            @if ($isSelected)
                                                                @php
                                                                    $selectedKelas = $kelasKuliahs
                                                                        ->where(
                                                                            'id_kelas_kuliah',
                                                                            $isSelected->kelasKuliah->id_kelas_kuliah ??
                                                                                '',
                                                                        )
                                                                        ->first();
                                                                @endphp
                                                                @if ($selectedKelas)
                                                                    <div class="text-xs">
                                                                        <div class="font-medium text-gray-900">
                                                                            {{ $selectedKelas->nama_kelas_kuliah }}</div>
                                                                        <div class="text-gray-600">
                                                                            {{ $selectedKelas->nama_ruangan ?: 'Ruangan belum diatur' }}
                                                                        </div>
                                                                        <div class="text-gray-500">
                                                                            {{ $selectedKelas->dosen->pengguna->nama ?? 'Dosen belum diatur' }}
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <button
                                                                    onclick="toggleKelasOptions('{{ $mataKuliah->id_mata_kuliah }}')"
                                                                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                                                                    <i class="fas fa-eye mr-1"></i>
                                                                    Lihat {{ $kelasKuliahs->count() }} Kelas
                                                                    <i class="fas fa-chevron-down ml-1 text-xs"
                                                                        id="kelas-chevron-{{ $mataKuliah->id_mata_kuliah }}"></i>
                                                                </button>
                                                            @endif
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                <i class="fas fa-lock mr-1"></i>
                                                                Belum Dibuka
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <!-- Status -->
                                                    <td class="px-6 py-4 text-center">
                                                        @if ($isSelected)
                                                            @switch($selectedStatus)
                                                                @case('APPROVED')
                                                                    <span
                                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                        <i class="fas fa-check mr-1"></i>
                                                                        Disetujui
                                                                    </span>
                                                                @break

                                                                @case('REJECTED')
                                                                    <span
                                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                        <i class="fas fa-times mr-1"></i>
                                                                        Ditolak
                                                                    </span>
                                                                @break

                                                                @default
                                                                    <span
                                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                        <i class="fas fa-clock mr-1"></i>
                                                                        Dipilih
                                                                    </span>
                                                            @endswitch
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                                <i class="fas fa-plus mr-1"></i>
                                                                Tersedia
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <!-- Aksi -->
                                                    <td class="px-6 py-4 text-center">
                                                        @if ($isSelected)
                                                            @if ($selectedStatus === 'APPROVED')
                                                                <button disabled
                                                                    class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-md text-xs cursor-not-allowed">
                                                                    <i class="fas fa-lock mr-1"></i>
                                                                    Terkunci
                                                                </button>
                                                            @else
                                                                <button
                                                                    onclick="confirmRemoveMataKuliah('{{ $isSelected->id_peserta }}', '{{ $isSelected->jenis }}', '{{ addslashes($mataKuliah->nama_mata_kuliah) }}')"
                                                                    class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-md text-xs hover:bg-red-700 transition-colors duration-200">
                                                                    <i class="fas fa-trash mr-1"></i>
                                                                    Batalkan
                                                                </button>
                                                            @endif
                                                        @else
                                                            @if ($isBimbingan)
                                                                <button
                                                                    onclick="confirmAddMataKuliahBimbingan('{{ $mataKuliah->id_mata_kuliah }}', '{{ addslashes($mataKuliah->nama_mata_kuliah) }}')"
                                                                    class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md text-xs hover:bg-blue-700 transition-colors duration-200">
                                                                    <i class="fas fa-plus mr-1"></i>
                                                                    Pilih
                                                                </button>
                                                            @elseif($hasKelas)
                                                                <button
                                                                    onclick="toggleKelasOptions('{{ $mataKuliah->id_mata_kuliah }}')"
                                                                    class="inline-flex items-center px-3 py-1 bg-gray-900 text-white rounded-md text-xs hover:bg-gray-800 transition-colors duration-200">
                                                                    <i class="fas fa-list mr-1"></i>
                                                                    Pilih Kelas
                                                                </button>
                                                            @else
                                                                <button disabled
                                                                    class="inline-flex items-center px-3 py-1 bg-gray-300 text-gray-500 rounded-md text-xs cursor-not-allowed">
                                                                    <i class="fas fa-ban mr-1"></i>
                                                                    Tidak Tersedia
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>

                                                <!-- Expandable Kelas Options untuk Mata Kuliah Reguler -->
                                                @if (!$isBimbingan && $hasKelas && !$isSelected)
                                                    <tr class="kelas-options hidden"
                                                        id="kelas-{{ $mataKuliah->id_mata_kuliah }}">
                                                        <td colspan="6" class="px-6 py-4 bg-gray-50">
                                                            <div class="space-y-3">
                                                                <h4 class="text-xs font-medium text-gray-900">Pilih Kelas
                                                                    untuk {{ $mataKuliah->nama_mata_kuliah }}:</h4>
                                                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                                                                    @foreach ($kelasKuliahs as $kelas)
                                                                        @php
                                                                            $jumlahPeserta =
                                                                                $kelas->jumlah_peserta ?? 0;
                                                                            $isPenuh = $kelas->isPenuh();
                                                                        @endphp
                                                                        <div
                                                                            class="border border-gray-200 rounded-lg p-3 {{ $isPenuh ? 'bg-red-50 border-red-200' : 'hover:border-gray-300' }} transition-colors duration-200">
                                                                            <div class="flex items-start justify-between">
                                                                                <div class="flex-1">
                                                                                    <h5
                                                                                        class="text-xs font-medium text-gray-900">
                                                                                        {{ $kelas->nama_kelas_kuliah }}
                                                                                    </h5>
                                                                                    <div
                                                                                        class="text-xs text-gray-600 mt-1 space-y-1">
                                                                                        <div><span
                                                                                                class="font-medium">Dosen:</span>
                                                                                            {{ $kelas->dosen->pengguna->nama ?? 'Belum diatur' }}
                                                                                        </div>
                                                                                        <div><span
                                                                                                class="font-medium">Ruangan:</span>
                                                                                            {{ $kelas->nama_ruangan ?: 'Belum diatur' }}
                                                                                        </div>
                                                                                        @if ($kelas->hari && $kelas->jam_mulai && $kelas->jam_akhir)
                                                                                            <div><span
                                                                                                    class="font-medium">Jadwal:</span>
                                                                                                {{ $kelas->hari }},
                                                                                                {{ \Carbon\Carbon::parse($kelas->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($kelas->jam_akhir)->format('H:i') }}
                                                                                            </div>
                                                                                        @else
                                                                                            <div><span
                                                                                                    class="font-medium">Jadwal:</span>
                                                                                                Belum diatur</div>
                                                                                        @endif
                                                                                        <div>
                                                                                            <span
                                                                                                class="font-medium">Kapasitas:</span>
                                                                                            <span
                                                                                                class="{{ $jumlahPeserta >= $kelas->kapasitas * 0.8 ? 'text-orange-600 font-medium' : '' }}">
                                                                                                {{ $jumlahPeserta }}/{{ $kelas->kapasitas }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="ml-3">
                                                                                    @if ($isPenuh)
                                                                                        <button disabled
                                                                                            class="inline-flex items-center px-3 py-1 bg-red-300 text-red-600 rounded text-xs cursor-not-allowed">
                                                                                            <i class="fas fa-ban mr-1"></i>
                                                                                            Penuh
                                                                                        </button>
                                                                                    @else
                                                                                        <button
                                                                                            onclick="confirmAddKelas('{{ $kelas->id_kelas_kuliah }}', '{{ addslashes($mataKuliah->nama_mata_kuliah) }}', '{{ addslashes($kelas->nama_kelas_kuliah) }}')"
                                                                                            class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700 transition-colors duration-200">
                                                                                            <i
                                                                                                class="fas fa-plus mr-1"></i>
                                                                                            Pilih
                                                                                        </button>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-12 text-center">
                        <i class="fas fa-clipboard-list w-12 h-12 text-gray-400 mx-auto mb-4 text-6xl"></i>
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
                            class="inline-flex items-center px-6 py-3 bg-gray-900 text-white text-xs font-medium rounded-full shadow-lg hover:bg-gray-700 hover:shadow-xl transition-all duration-200">
                            <i class="fas fa-eye mr-2"></i>
                            Review KRS ({{ $mataKuliahTerpilih->count() }})
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Semester accordion functionality
            function toggleSemester(semester) {
                const content = document.getElementById(`content-${semester}`);
                const chevron = document.getElementById(`chevron-${semester}`);

                if (content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                    chevron.classList.add('rotate-180');
                } else {
                    content.classList.add('hidden');
                    chevron.classList.remove('rotate-180');
                }
            }

            // Auto open semester with selected courses on page load
            document.addEventListener('DOMContentLoaded', function() {
                // Auto open first semester
                @if (count($mataKuliahTersedia) > 0)
                    @php $firstSemester = array_key_first($mataKuliahTersedia); @endphp
                    toggleSemester({{ $firstSemester }});
                @endif
            });

            // Toggle kelas options
            function toggleKelasOptions(mataKuliahId) {
                const kelasRow = document.getElementById(`kelas-${mataKuliahId}`);
                const chevron = document.getElementById(`kelas-chevron-${mataKuliahId}`);

                if (kelasRow.classList.contains('hidden')) {
                    kelasRow.classList.remove('hidden');
                    if (chevron) chevron.classList.add('rotate-180');
                } else {
                    kelasRow.classList.add('hidden');
                    if (chevron) chevron.classList.remove('rotate-180');
                }
            }

            // Filter functionality
            function filterMataKuliah() {
                const kategoriFilter = document.getElementById('kategoriFilter').value;
                const jenisFilter = document.getElementById('jenisFilter').value;
                const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();

                const semesterSections = document.querySelectorAll('.semester-section');

                semesterSections.forEach(section => {
                    const rows = section.querySelectorAll('.mata-kuliah-row');
                    let visibleRowsInSemester = 0;

                    rows.forEach(row => {
                        const kategori = row.getAttribute('data-kategori');
                        const jenis = row.getAttribute('data-jenis');
                        const searchData = row.getAttribute('data-search');

                        const matchesKategori = kategoriFilter === '' || kategori === kategoriFilter;
                        const matchesJenis = jenisFilter === '' || jenis === jenisFilter;
                        const matchesSearch = searchTerm === '' || searchData.includes(searchTerm);

                        const isMatch = matchesKategori && matchesJenis && matchesSearch;

                        if (isMatch) {
                            row.style.display = '';
                            visibleRowsInSemester++;
                        } else {
                            row.style.display = 'none';
                            // Also hide any open kelas options
                            const kelasRow = row.nextElementSibling;
                            if (kelasRow && kelasRow.classList.contains('kelas-options')) {
                                kelasRow.style.display = 'none';
                            }
                        }
                    });

                    // Hide/show entire semester section based on visible rows
                    if (visibleRowsInSemester === 0) {
                        section.style.display = 'none';
                    } else {
                        section.style.display = '';
                    }
                });
            }

            // Confirmation functions
            function confirmAddKelas(idKelasKuliah, namaMataKuliah, namaKelas) {
                if (confirm(`Pilih kelas "${namaKelas}" untuk mata kuliah "${namaMataKuliah}"?`)) {
                    addMataKuliah({
                        id_kelas_kuliah: idKelasKuliah
                    });
                }
            }

            function confirmAddMataKuliahBimbingan(idMataKuliah, namaMataKuliah) {
                if (confirm(
                        `Pilih mata kuliah bimbingan "${namaMataKuliah}"?\n\nCatatan: Pembimbing akan diatur oleh admin setelah KRS disetujui.`
                    )) {
                    addMataKuliah({
                        id_mata_kuliah: idMataKuliah
                    });
                }
            }

            function confirmRemoveMataKuliah(idPeserta, jenis, namaMataKuliah) {
                if (confirm(`Yakin ingin menghapus mata kuliah "${namaMataKuliah}" dari KRS?`)) {
                    removeMataKuliah(idPeserta, jenis);
                }
            }

            // AJAX functions
            function addMataKuliah(data) {
                showLoading(true);

                fetch('{{ route('krs.add-mata-kuliah') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        showLoading(false);

                        if (data.success) {
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

            function removeMataKuliah(idPeserta, jenis) {
                showLoading(true);

                fetch('{{ route('krs.remove-mata-kuliah') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id_peserta: idPeserta,
                            jenis: jenis
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        showLoading(false);

                        if (data.success) {
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
                if (show) {
                    document.body.style.cursor = 'wait';
                } else {
                    document.body.style.cursor = 'default';
                }
            }
        </script>
    @endpush
@endsection
