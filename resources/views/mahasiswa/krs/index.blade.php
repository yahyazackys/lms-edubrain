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
                                    class="text-xs py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[200px]">
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
                        <i class="fas fa-calendar-alt w-16 h-16 text-gray-400 mb-4 mx-auto text-6xl"></i>
                        <p class="text-lg font-medium text-gray-900 mb-2">Pilih Semester</p>
                        <p class="text-xs text-gray-500 mb-4">Pilih semester dari dropdown di atas untuk mengelola KRS</p>
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
                                <p class="text-xs text-gray-600">{{ $selectedSemester->nama_semester }} •
                                    {{ $mahasiswa->programStudi->nama_program_studi }}</p>
                            </div>

                            <!-- Section kanan: Semester selector + Buttons -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                <!-- Semester Selector -->
                                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                                    <label class="text-xs font-medium text-gray-700">Periode:</label>
                                    <select id="semesterFilter" onchange="changeSemester()"
                                        class="text-xs py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent min-w-[200px]">
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
                                        $hasRejectedCourses =
                                            $mataKuliahTerpilih->where('status_mata_kuliah', 'REJECTED')->count() > 0;
                                        $isSubmitted = !is_null($registrasiKrs->tanggal_submit);
                                        $canEditKrs = !$isSubmitted || $registrasiKrs->status_krs === 'REJECTED';
                                        $canSubmitKrs =
                                            is_null($registrasiKrs->tanggal_submit) && $mataKuliahTerpilih->count() > 0;
                                    @endphp

                                    @if ($canEditKrs)
                                        <a href="{{ route('krs.pilih-mata-kuliah', $selectedSemester->id_semester) }}"
                                            class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200 whitespace-nowrap">
                                            <i class="fas fa-plus mr-2"></i>
                                            @if ($hasRejectedCourses)
                                                Pilih Mata Kuliah Pengganti
                                            @else
                                                Pilih Mata Kuliah
                                            @endif
                                        </a>

                                        @if ($mataKuliahTerpilih->count() > 0 && !$hasRejectedCourses)
                                            <a href="{{ route('krs.review', $selectedSemester->id_semester) }}"
                                                class="inline-flex items-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 whitespace-nowrap">
                                                <i class="fas fa-eye mr-2"></i>
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
                                        <i class="fa-solid fa-user text-gray-900 text-xs"></i>
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
                                        <i class="fa-solid fa-building-columns text-gray-900 text-xs"></i>
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
                                        <i class="fa-solid fa-layer-group text-gray-900 text-xs"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-900">
                                        {{ $totalSksSelected }}/{{ $batasSks }} SKS
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
                                            <i class="fa-solid fa-circle-check text-green-600 text-xs"></i>
                                        @elseif($registrasiKrs->status_krs === 'REJECTED')
                                            <i class="fa-solid fa-times-circle text-red-600 text-xs"></i>
                                        @elseif($registrasiKrs->tanggal_submit)
                                            <i class="fa-solid fa-clock text-yellow-600 text-xs"></i>
                                        @else
                                            <i class="fa-solid fa-edit text-gray-600 text-xs"></i>
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

                <!-- Alert Alasan Penolakan KRS -->
                @if ($registrasiKrs->status_krs === 'REJECTED' && $registrasiKrs->alasan_reject)
                    <div class="bg-red-50 border border-red-200 rounded-lg mb-6">
                        <div class="px-6 py-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-xs font-medium text-red-800">
                                        KRS Anda Ditolak oleh Pembimbing Akademik
                                    </h3>
                                    <div class="mt-2 text-xs text-red-700">
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
                                                <i class="fas fa-plus mr-2"></i>
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
                                    <i class="fas fa-clock text-yellow-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-xs font-medium text-yellow-800">
                                        KRS Menunggu Verifikasi Pembimbing Akademik
                                    </h3>
                                    <div class="mt-2 text-xs text-yellow-700">
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
                                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-xs font-medium text-green-800">KRS Telah Disetujui</h3>
                                    <div class="mt-2 text-xs text-green-700">
                                        <p>Selamat! KRS Anda telah disetujui oleh PA pada
                                            {{ $registrasiKrs->tanggal_approval->format('d M Y H:i') }}.</p>
                                        <p class="mt-1">Anda sudah dapat mengikuti perkuliahan sesuai jadwal yang tertera
                                            di bawah.</p>

                                        @php
                                            $mataKuliahBimbingan = $mataKuliahTerpilih->where('jenis', 'bimbingan');
                                        @endphp

                                        @if ($mataKuliahBimbingan->count() > 0)
                                            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                                <p class="text-xs text-blue-800">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Anda memiliki {{ $mataKuliahBimbingan->count() }} mata kuliah bimbingan
                                                    yang telah disetujui.
                                                    Silakan cek menu <strong>Bimbingan</strong> untuk melihat pembimbing
                                                    yang telah ditugaskan.
                                                </p>
                                            </div>
                                        @endif
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
                                <h3 class="text-xs font-semibold text-gray-900">Mata Kuliah Terpilih</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $mataKuliahTerpilih->count() }} mata kuliah
                                    dipilih ({{ $mataKuliahTerpilih->where('jenis', 'reguler')->count() }} reguler,
                                    {{ $mataKuliahTerpilih->where('jenis', 'bimbingan')->count() }} bimbingan)</p>
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
                                                    Jenis</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Kelas/Pembimbing</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Jadwal</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    SKS</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status</th>
                                                {{-- @if (in_array($registrasiKrs->status_krs, ['SUBMITTED', 'REJECTED']))
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Aksi</th>
                                                @endif --}}
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($mataKuliahTerpilih as $peserta)
                                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                    <!-- Mata Kuliah -->
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 mr-3">
                                                                @if ($peserta->jenis === 'bimbingan')
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
                                                                    {{ $peserta->mataKuliah->kode_mata_kuliah }}</div>
                                                                <div class="text-xs text-gray-500">
                                                                    {{ $peserta->mataKuliah->nama_mata_kuliah }}</div>
                                                                <div class="text-xs text-gray-400 mt-1">
                                                                    {{ $peserta->kategori_mata_kuliah }}</div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <!-- Jenis -->
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if ($peserta->jenis === 'bimbingan')
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                <i class="fas fa-user-graduate mr-1"></i>
                                                                {{ $peserta->mataKuliah->jenis_mata_kuliah }}
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                                {{ $peserta->mataKuliah->jenis_mata_kuliah }}
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <!-- Kelas/Pembimbing -->
                                                    <td class="px-6 py- whitespace-nowrap">
                                                        @if ($peserta->jenis === 'bimbingan')
                                                            <div class="text-xs font-medium text-gray-900">
                                                                @if ($peserta->dosen !== 'Belum diatur')
                                                                    {{ $peserta->dosen }}
                                                                    @if ($peserta->dosen_pembimbing_2)
                                                                        <div class="text-xs text-gray-500">Pembimbing 2:
                                                                            {{ $peserta->dosen_pembimbing_2 }}</div>
                                                                    @endif
                                                                @else
                                                                    <span class="text-gray-400">Pembimbing belum
                                                                        diatur</span>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <div class="text-xs font-medium text-gray-900">
                                                                {{ $peserta->kelas }}</div>
                                                            <div class="text-xs text-gray-500">
                                                                {{ $peserta->ruangan ?: 'Ruangan belum diatur' }}</div>
                                                            <div class="text-xs text-gray-500">{{ $peserta->dosen }}</div>
                                                        @endif
                                                    </td>

                                                    <!-- Jadwal -->
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if ($peserta->jenis === 'bimbingan')
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                                                <i class="fas fa-calendar-check mr-1"></i>
                                                                By Appointment
                                                            </span>
                                                        @else
                                                            @if ($peserta->hari && $peserta->jam_mulai && $peserta->jam_akhir)
                                                                <div class="text-xs font-medium text-gray-900">
                                                                    {{ $peserta->hari }}</div>
                                                                <div class="text-xs text-gray-500">
                                                                    {{ \Carbon\Carbon::parse($peserta->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($peserta->jam_akhir)->format('H:i') }}
                                                                </div>
                                                            @else
                                                                <span class="text-xs text-gray-400">Jadwal belum
                                                                    diatur</span>
                                                            @endif
                                                        @endif
                                                    </td>

                                                    <!-- SKS -->
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ $peserta->mataKuliah->sks_mata_kuliah }} SKS
                                                        </span>
                                                    </td>

                                                    <!-- Status -->
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @switch($peserta->status_mata_kuliah)
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
                                                                    Menunggu
                                                                </span>
                                                        @endswitch
                                                    </td>

                                                    <!-- Aksi -->
                                                    {{-- @if (in_array($registrasiKrs->status_krs, ['SUBMITTED', 'REJECTED']))
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            @if ($peserta->status_mata_kuliah === 'APPROVED')
                                                                <span class="text-gray-400">-</span>
                                                            @else
                                                                <button
                                                                    onclick="confirmRemoveMataKuliah('{{ $peserta->id_peserta }}', '{{ $peserta->jenis }}', '{{ addslashes($peserta->mataKuliah->nama_mata_kuliah) }}')"
                                                                    class="inline-flex items-center p-1 text-red-600 hover:text-red-900 hover:bg-red-50 rounded"
                                                                    title="Hapus mata kuliah">
                                                                    <i class="fas fa-trash text-xs"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    @endif --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="px-6 py-12 text-center">
                                        <i class="fas fa-clipboard-list w-12 h-12 text-gray-400 mx-auto mb-4 text-6xl"></i>
                                        <p class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mata Kuliah Terpilih
                                        </p>
                                        <p class="text-xs text-gray-500 mb-4">Pilih mata kuliah untuk semester ini</p>
                                        <a href="{{ route('krs.pilih-mata-kuliah', $selectedSemester->id_semester) }}"
                                            class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                            <i class="fas fa-plus mr-2"></i>
                                            Pilih Mata Kuliah
                                        </a>
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
                                <h3 class="text-xs font-semibold text-gray-900">Status Timeline</h3>
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
                                    <h3 class="text-xs font-semibold text-gray-900">Pembimbing Akademik</h3>
                                </div>
                                <div class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600">
                                                <i class="fas fa-user text-lg"></i>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-xs font-medium text-gray-900">
                                                {{ $registrasiKrs->pembimbingAkademik->dosen->pengguna->nama ?? 'N/A' }}
                                            </p>
                                            <p class="text-xs text-gray-500">NIDN:
                                                {{ $registrasiKrs->pembimbingAkademik->dosen->nidn ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Ringkasan SKS -->
                        @if ($sksPerKategori)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="px-6 py-4 border-b border-gray-100">
                                    <h3 class="text-xs font-semibold text-gray-900">Ringkasan SKS per Kategori</h3>
                                </div>
                                <div class="px-6 py-4 space-y-3">
                                    @foreach ($sksPerKategori as $kategori => $sks)
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600">{{ $kategori }}:</span>
                                            <span class="text-xs font-medium text-gray-900">{{ $sks }} SKS</span>
                                        </div>
                                    @endforeach
                                    <div class="border-t border-gray-200 pt-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-medium text-gray-900">Total:</span>
                                            <span class="text-lg font-bold text-gray-900">{{ $totalSksSelected }}
                                                SKS</span>
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

            function confirmRemoveMataKuliah(idPeserta, jenis, namaMataKuliah) {
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

                    const jenisInput = document.createElement('input');
                    jenisInput.type = 'hidden';
                    jenisInput.name = 'jenis';
                    jenisInput.value = jenis;

                    form.appendChild(csrfInput);
                    form.appendChild(idInput);
                    form.appendChild(jenisInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        </script>
    @endpush
@endsection
