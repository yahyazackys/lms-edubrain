@extends('layouts.app')

@section('title', 'Kartu Rencana Studi (KRS)')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            @if (!$selectedSemester)
                <!-- Header dengan Filter Semester -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                    <div class="px-6 py-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex flex-col space-y-1">
                                <h1 class="text-lg font-semibold font-heading text-gray-900">Kartu Rencana Studi (KRS)</h1>
                                <p class="text-xs text-gray-600">Kelola Kartu Rencana Studi Anda berdasarkan periode semester
                                </p>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                                <label class="text-xs font-medium text-gray-700">Pilih Periode Semester:</label>
                                <select id="semesterFilter" onchange="changeSemester()"
                                    class="text-xs py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[200px]">
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
                        </div>
                    </div>
                </div>

                <!-- Empty State - Pilih Semester -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-calendar w-16 h-16 text-gray-400 mb-4 mx-auto text-6xl"></i>
                        <p class="text-lg font-medium text-gray-900 mb-2">Pilih Semester</p>
                        <p class="text-sm text-gray-500 mb-4">Pilih semester dari dropdown di atas untuk mengelola KRS</p>
                    </div>
                </div>
            @else
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                    <div class="px-6 py-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Section kiri: Title -->
                            <div class="flex flex-col space-y-1">
                                <h1 class="text-lg font-semibold font-heading text-gray-900">Kartu Rencana Studi (KRS)</h1>
                                <p class="text-xs text-gray-600">Kelola Kartu Rencana Studi Anda berdasarkan periode
                                    semester</p>
                            </div>

                            <!-- Section kanan: Semester selector + Buttons -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <!-- Semester Selector -->
                                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                                    <label class="text-xs font-medium text-gray-700">Pilih Periode Semester:</label>
                                    <select id="semesterFilter" onchange="changeSemester()"
                                        class="text-xs py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[200px]">
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

                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-3">
                                    @php
                                        // PHP logic sama seperti sebelumnya
                                        $hasRejectedCourses = $registrasiKrs
                                            ->pesertaKelasKuliah()
                                            ->where('status_mata_kuliah', 'REJECTED')
                                            ->exists();

                                        // Logika berdasarkan tanggal_submit
                                        $isSubmitted = !is_null($registrasiKrs->tanggal_submit);

                                        $canEditKrs = !$isSubmitted || $registrasiKrs->status_krs === 'REJECTED';

                                        $canSubmitKrs =
                                            is_null($registrasiKrs->tanggal_submit) &&
                                            $registrasiKrs->pesertaKelasKuliah()->count() > 0;
                                    @endphp

                                    @if ($canEditKrs)
                                        <a href="{{ route('krs.pilih-mata-kuliah', $selectedSemester->id_semester) }}"
                                            class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200 whitespace-nowrap">
                                            @if ($hasRejectedCourses)
                                                Pilih Mata Kuliah Pengganti
                                            @else
                                                Pilih Mata Kuliah
                                            @endif
                                        </a>

                                        @if ($mataKuliahTerpilih->count() > 0 && !$hasRejectedCourses)
                                            <a href="{{ route('krs.review', $selectedSemester->id_semester) }}"
                                                class="inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 whitespace-nowrap">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7"></path>
                                                </svg>
                                                Review & Submit
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Info Bar -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Mahasiswa -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-user text-gray-900 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-900">{{ $mahasiswa->pengguna->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ $mahasiswa->nim }}</p>
                                </div>
                            </div>

                            <!-- Semester & Prodi -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-building-columns text-gray-900 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-900">Semester {{ $semesterMahasiswa }}</p>
                                    <p class="text-xs text-gray-500">{{ $mahasiswa->programStudi->nama_program_studi }}</p>
                                </div>
                            </div>

                            <!-- Total SKS -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-layer-group text-gray-900 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-900">
                                        {{ $totalSksSelected }}/{{ $batasSks }}
                                        SKS
                                    </p>
                                    <p class="text-xs text-gray-500">Total Dipilih</p>
                                </div>
                            </div>

                            <!-- Status KRS -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-8 h-8 
                        {{ $registrasiKrs->status_krs === 'APPROVED'
                            ? 'bg-green-100'
                            : ($registrasiKrs->status_krs === 'REJECTED'
                                ? 'bg-red-100'
                                : ($registrasiKrs->tanggal_submit
                                    ? 'bg-yellow-100'
                                    : 'bg-gray-100')) }} 
                        rounded-full flex items-center justify-center">

                                        @if ($registrasiKrs->status_krs === 'APPROVED')
                                            <i class="fa-solid fa-circle-check text-green-600 text-sm"></i>
                                        @elseif($registrasiKrs->status_krs === 'REJECTED')
                                            <i class="fa-solid fa-times-circle text-red-600 text-sm"></i>
                                        @elseif($registrasiKrs->tanggal_submit)
                                            <i class="fa-solid fa-clock text-yellow-600 text-sm"></i>
                                        @else
                                            <i class="fa-solid fa-edit text-gray-600 text-sm"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-900">
                                        @if ($registrasiKrs->status_krs === 'APPROVED')
                                            Disetujui
                                        @elseif($registrasiKrs->status_krs === 'REJECTED')
                                            Ditolak - Perlu Revisi
                                        @elseif($registrasiKrs->tanggal_submit)
                                            Menunggu Approval
                                        @else
                                            Draft
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">Status KRS</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


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

                <!-- Alert Alasan Penolakan KRS -->
                @if ($registrasiKrs->status_krs === 'REJECTED' && $registrasiKrs->alasan_reject)
                    <div class="bg-red-50 border border-red-200 rounded-lg mb-6">
                        <div class="px-6 py-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-sm font-medium text-red-800">
                                        KRS Anda Ditolak oleh Pembimbing Akademik
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <div class="bg-white border border-red-200 rounded p-3 mb-3">
                                            <p class="font-medium text-red-800 mb-1">Alasan Penolakan:</p>
                                            <p class="text-red-700">{{ $registrasiKrs->alasan_reject }}</p>
                                        </div>

                                        <div class="space-y-2">
                                            <p class="font-medium text-red-800">Langkah yang harus dilakukan:</p>
                                            <ol class="list-decimal list-inside space-y-1 text-red-700 ml-2">
                                                <li>Lihat mata kuliah yang ditolak PA (status merah) di tabel di bawah</li>
                                                <li>Hapus mata kuliah yang ditolak dengan tombol hapus di kolom Aksi</li>
                                                <li>Pilih mata kuliah pengganti atau kelas alternatif</li>
                                                <li>Submit ulang KRS untuk review PA</li>
                                            </ol>
                                        </div>

                                        <div class="mt-4 flex items-center space-x-3">
                                            <a href="{{ route('krs.pilih-mata-kuliah', $selectedSemester->id_semester) }}"
                                                class="inline-flex items-center px-3 py-2 border border-red-300 rounded-md shadow-sm text-xs font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Pilih Mata Kuliah Pengganti
                                            </a>

                                            @if ($registrasiKrs->pembimbingAkademik)
                                                <div class="text-xs text-red-600">
                                                    <i class="fa-solid fa-user mr-1"></i>
                                                    PA:
                                                    {{ $registrasiKrs->pembimbingAkademik->dosen->pengguna->nama ?? 'N/A' }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Info Box untuk KRS yang Submitted tapi Pending -->
                @if ($registrasiKrs->status_krs === 'SUBMITTED' && $registrasiKrs->tanggal_submit && !$registrasiKrs->alasan_reject)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg mb-6">
                        <div class="px-6 py-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        KRS Menunggu Verifikasi Pembimbing Akademik
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>KRS Anda telah disubmit pada
                                            {{ $registrasiKrs->tanggal_submit->format('d M Y H:i') }} dan sedang menunggu
                                            review dari PA.</p>
                                        @if ($registrasiKrs->pembimbingAkademik)
                                            <p class="mt-1">
                                                <strong>PA:</strong>
                                                {{ $registrasiKrs->pembimbingAkademik->dosen->pengguna->nama ?? 'N/A' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Info Box untuk KRS yang sudah Approved -->
                @if ($registrasiKrs->status_krs === 'APPROVED')
                    <div class="bg-green-50 border border-green-200 rounded-lg mb-6">
                        <div class="px-6 py-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">
                                        KRS Telah Disetujui
                                    </h3>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p>Selamat! KRS Anda telah disetujui oleh PA pada
                                            {{ $registrasiKrs->tanggal_approval->format('d M Y H:i') }}.</p>
                                        <p class="mt-1">Anda sudah dapat mengikuti perkuliahan sesuai jadwal yang tertera
                                            di bawah.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Main Content -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Mata Kuliah Terpilih -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-900">Mata Kuliah Terpilih</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $mataKuliahTerpilih->count() }} mata kuliah
                                    dipilih
                                </p>
                            </div>

                            <div class="overflow-x-auto">
                                @if ($mataKuliahTerpilih->count() > 0)
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Mata Kuliah</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Kelas</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Dosen</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Waktu</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    SKS</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status</th>
                                                @if (in_array($registrasiKrs->status_krs, ['SUBMITTED', 'REJECTED']))
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Aksi</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($mataKuliahTerpilih as $peserta)
                                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-xs font-medium text-gray-900">
                                                            {{ $peserta->mataKuliah->kode_mata_kuliah }}</div>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $peserta->mataKuliah->nama_mata_kuliah }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-xs font-medium text-gray-900">
                                                            {{ $peserta->kelasKuliah->nama_kelas_kuliah }}</div>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $peserta->kelasKuliah->nama_ruangan }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-xs font-medium text-gray-900">
                                                            {{ $peserta->kelasKuliah->dosen->pengguna->nama ?? 'N/A' }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if ($peserta->kelasKuliah->jam_mulai && $peserta->kelasKuliah->jam_akhir)
                                                            <div class="text-xs font-medium text-gray-900">
                                                                {{ $peserta->kelasKuliah->hari }}</div>
                                                            <div class="text-xs text-gray-500 mt-1">
                                                                {{ \Carbon\Carbon::parse($peserta->kelasKuliah->jam_mulai)->format('H:i') }}
                                                                -
                                                                {{ \Carbon\Carbon::parse($peserta->kelasKuliah->jam_akhir)->format('H:i') }}
                                                            </div>
                                                        @else
                                                            <span class="text-xs text-gray-400">Belum diatur</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ $peserta->mataKuliah->sks_mata_kuliah }} SKS
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                                        @switch($peserta->status_mata_kuliah)
                                                            @case('APPROVED')
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                    <svg class="w-3 h-3 mr-1" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                    Disetujui
                                                                </span>
                                                            @break

                                                            @case('REJECTED')
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                    <svg class="w-3 h-3 mr-1" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                    Ditolak
                                                                </span>
                                                            @break

                                                            @case('SELECTED')

                                                                @default
                                                                    <span
                                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                        <svg class="w-3 h-3 mr-1" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                        </svg>
                                                                        Menunggu
                                                                    </span>
                                                                @break
                                                            @endswitch
                                                        </td>
                                                        @if (in_array($registrasiKrs->status_krs, ['SUBMITTED', 'REJECTED']))
                                                            <td class="px-6 py-4 text-center">
                                                                @if ($peserta->status_mata_kuliah === 'APPROVED' || $peserta->status_mata_kuliah == 'SELECTED')
                                                                    {{-- Mata kuliah sudah disetujui PA - tidak ada aksi --}}
                                                                    <span>
                                                                        -
                                                                    </span>
                                                                @else
                                                                    <button
                                                                        onclick="confirmRemoveMataKuliah('{{ $peserta->id_peserta }}', '{{ $peserta->mataKuliah->nama_mata_kuliah }}')"
                                                                        class="inline-flex items-center p-1 text-red-600 hover:text-red-900 hover:bg-red-50 rounded">
                                                                        <svg class="w-4 h-4" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                            </path>
                                                                        </svg>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="px-6 py-12 text-center">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                                </path>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mata Kuliah Terpilih
                                            </p>
                                            <p class="text-xs text-gray-500 mb-4">Pilih mata kuliah untuk semester ini</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar Info -->
                        <div class="space-y-6">
                            <!-- Status Timeline -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="px-6 py-4 border-b border-gray-100">
                                    <h3 class="text-sm font-semibold text-gray-900">Status Timeline</h3>
                                </div>
                                <div class="px-6 py-4">
                                    <ul class="relative">
                                        {{-- Step 1: KRS Dibuka --}}
                                        <li>
                                            <div class="relative pb-8">
                                                {{-- Garis ke bawah --}}
                                                <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"></span>

                                                <div class="relative flex space-x-3">
                                                    {{-- Icon --}}
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center text-white ring-8 ring-white">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                    {{-- Text --}}
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-xs font-medium text-gray-900">KRS Dibuka</p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $registrasiKrs->created_at->format('d M Y H:i') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        {{-- Step 2: Submit ke PA --}}
                                        <li>
                                            <div class="relative pb-8">
                                                {{-- Garis ke bawah --}}
                                                <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"></span>

                                                <div class="relative flex space-x-3">
                                                    <div
                                                        class="h-8 w-8 rounded-full {{ $registrasiKrs->tanggal_submit ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }} flex items-center justify-center ring-8 ring-white">
                                                        @if ($registrasiKrs->tanggal_submit)
                                                            <i class="fas fa-check"></i>
                                                        @else
                                                            <i class="fa-regular fa-clock"></i>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-xs font-medium text-gray-900">Submit ke PA</p>
                                                        @if ($registrasiKrs->tanggal_submit)
                                                            <p class="text-xs text-gray-500">
                                                                {{ $registrasiKrs->tanggal_submit->format('d M Y H:i') }}
                                                            </p>
                                                        @else
                                                            <p class="text-xs text-gray-400">Belum disubmit</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        {{-- Step 3: Approval PA --}}
                                        <li>
                                            <div class="relative">
                                                <div class="relative flex space-x-3">
                                                    <div
                                                        class="h-8 w-8 rounded-full {{ $registrasiKrs->status_krs === 'APPROVED' ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }} flex items-center justify-center ring-8 ring-white">
                                                        @if ($registrasiKrs->status_krs === 'APPROVED')
                                                            <i class="fas fa-check"></i>
                                                        @else
                                                            <i class="fa-regular fa-clock"></i>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-xs font-medium text-gray-900">Approval PA</p>
                                                        @if ($registrasiKrs->tanggal_approval)
                                                            <p class="text-xs text-gray-500">
                                                                {{ $registrasiKrs->tanggal_approval->format('d M Y H:i') }}
                                                            </p>
                                                        @else
                                                            <p class="text-xs text-gray-400">Menunggu approval</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Info Pembimbing Akademik -->
                            @if ($registrasiKrs->pembimbingAkademik)
                                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                                    <div class="px-6 py-4 border-b border-gray-100">
                                        <h3 class="text-sm font-semibold text-gray-900">Pembimbing Akademik</h3>
                                    </div>
                                    <div class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="h-8 w-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-600">
                                                    {{-- Font Awesome icon --}}
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-xs font-medium text-gray-900">
                                                    {{ $registrasiKrs->pembimbingAkademik->dosen->pengguna->nama ?? 'N/A' }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $registrasiKrs->pembimbingAkademik->dosen->nidn ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @push('scripts')
            <script>
                function changeSemester() {
                    const semesterId = document.getElementById('semesterFilter').value;
                    if (semesterId) {
                        const currentParams = new URLSearchParams(window.location.search);
                        currentParams.set('semester', semesterId);
                        window.location.href = window.location.pathname + '?' + currentParams.toString();
                    } else {
                        window.location.href = window.location.pathname;
                    }
                }

                function confirmRemoveMataKuliah(idPeserta, namaMataKuliah) {
                    if (confirm(`Yakin ingin menghapus mata kuliah "${namaMataKuliah}" dari KRS?`)) {
                        // Create form and submit
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('krs.remove-mata-kuliah') }}';

                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';

                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'id_peserta';
                        idInput.value = idPeserta;

                        form.appendChild(csrfInput);
                        form.appendChild(idInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            </script>
        @endpush
    @endsection
