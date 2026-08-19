@extends('layouts.app')

@section('title', 'Review KRS - ' . $registrasiMahasiswa->mahasiswa->pengguna->nama)

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Review KRS Mahasiswa</h1>
                            <p class="text-xs text-gray-600">Review dan setujui KRS
                                {{ $registrasiMahasiswa->mahasiswa->pengguna->nama }}</p>
                        </div>

                        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                            <a href="{{ route('krs.approval.index', ['semester' => $registrasiMahasiswa->id_semester]) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Dashboard
                            </a>

                            {{-- Hitung apakah semua mata kuliah sudah diverifikasi --}}
                            @php
                                $totalMataKuliah = $mataKuliahTerpilih->count();
                                $mataKuliahBelumVerifikasi = $mataKuliahTerpilih
                                    ->where('status_mata_kuliah', 'SELECTED')
                                    ->count();
                                $allMataKuliahVerified = $totalMataKuliah > 0 && $mataKuliahBelumVerifikasi == 0;
                            @endphp

                            {{-- Tombol hanya muncul jika: KRS submitted, ada tanggal submit, belum ditolak, dan semua mata kuliah sudah diverifikasi --}}
                            @if (
                                $registrasiMahasiswa->status_krs === 'SUBMITTED' &&
                                    $registrasiMahasiswa->tanggal_submit &&
                                    !$registrasiMahasiswa->alasan_reject &&
                                    $allMataKuliahVerified)
                                <button onclick="openApproveModal()"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Setujui KRS
                                </button>

                                <button onclick="openRejectModal()"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Tolak KRS
                                </button>

                                {{-- Tampilkan pesan jika belum semua mata kuliah diverifikasi --}}
                            @elseif (
                                $registrasiMahasiswa->status_krs === 'SUBMITTED' &&
                                    $registrasiMahasiswa->tanggal_submit &&
                                    !$registrasiMahasiswa->alasan_reject &&
                                    !$allMataKuliahVerified)
                                <div
                                    class="inline-flex items-center px-4 py-2 bg-amber-100 text-amber-800 text-xs font-medium rounded-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                    Verifikasi semua mata kuliah terlebih dahulu ({{ $mataKuliahBelumVerifikasi }} tersisa)
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Summary Bar -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    @php
                        $mataKuliahReguler = $mataKuliahTerpilih->where('jenis', 'reguler');
                        $mataKuliahBimbingan = $mataKuliahTerpilih->where('jenis', 'bimbingan');
                    @endphp

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-center">
                        <div>
                            <p class="text-base font-semibold text-gray-900">
                                {{ $registrasiMahasiswa->mahasiswa->pengguna->nama }}</p>
                            <p class="text-xs text-gray-500">{{ $registrasiMahasiswa->mahasiswa->nim }}</p>
                        </div>
                        <div>
                            <p class="text-base font-semibold text-gray-900">
                                {{ $registrasiMahasiswa->semester->nama_semester }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $registrasiMahasiswa->mahasiswa->programStudi->nama_program_studi }}</p>
                        </div>
                        <div>
                            <p class="text-base font-semibold text-gray-900">
                                {{ $mataKuliahTerpilih->count() }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Total ({{ $mataKuliahReguler->count() }}R + {{ $mataKuliahBimbingan->count() }}B)
                            </p>
                        </div>
                        <div>
                            <p class="text-base font-semibold text-gray-900">{{ $totalSks }}</p>
                            <p class="text-xs text-gray-500">Total SKS</p>
                        </div>
                        <div>
                            @if ($registrasiMahasiswa->alasan_reject)
                                <p class="text-base font-semibold text-red-600">Ditolak</p>
                                <p class="text-xs text-gray-500">Menunggu Revisi Mahasiswa</p>
                            @elseif ($registrasiMahasiswa->status_krs === 'SUBMITTED' && $registrasiMahasiswa->tanggal_submit)
                                <p class="text-base font-semibold text-yellow-600">Menunggu Review</p>
                                <p class="text-xs text-gray-500">
                                    {{ $registrasiMahasiswa->tanggal_submit->format('d M Y H:i') }}
                                </p>
                            @elseif ($registrasiMahasiswa->status_krs === 'APPROVED')
                                <p class="text-base font-semibold text-green-600">Disetujui</p>
                                <p class="text-xs text-gray-500">
                                    {{ $registrasiMahasiswa->tanggal_approval->format('d M Y H:i') }}
                                </p>
                            @else
                                <p class="text-base font-semibold text-gray-400">Draft</p>
                                <p class="text-xs text-gray-500">Belum Submit</p>
                            @endif
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
                        <p class="text-xs font-medium">{{ session('success') }}</p>
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
                        <p class="text-xs font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Rekomendasi PA -->
            @if (!empty($rekomendasi))
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-xs font-semibold text-gray-900">Rekomendasi Pembimbing Akademik</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        @foreach ($rekomendasi as $item)
                            <div
                                class="flex items-start space-x-3 p-3 rounded-lg {{ $item['type'] === 'error' ? 'bg-red-50 border border-red-200' : ($item['type'] === 'warning' ? 'bg-yellow-50 border border-yellow-200' : 'bg-blue-50 border border-blue-200') }}">
                                <div class="flex-shrink-0">
                                    @if ($item['type'] === 'error')
                                        <svg class="w-4 h-4 text-red-400 mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif ($item['type'] === 'warning')
                                        <svg class="w-4 h-4 text-yellow-400 mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z">
                                            </path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-blue-400 mt-0.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <p
                                    class="text-xs {{ $item['type'] === 'error' ? 'text-red-800' : ($item['type'] === 'warning' ? 'text-yellow-800' : 'text-blue-800') }}">
                                    {{ $item['message'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content - Daftar Mata Kuliah -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Mata Kuliah Reguler -->
                    @if ($mataKuliahReguler->count() > 0)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-chalkboard-teacher text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-semibold text-gray-900">Mata Kuliah Reguler</h3>
                                        <p class="text-xs text-gray-500">{{ $mataKuliahReguler->count() }} mata kuliah
                                            dengan jadwal tetap</p>
                                    </div>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
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
                                                Jadwal</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                SKS</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            @if (
                                                $registrasiMahasiswa->status_krs === 'SUBMITTED' &&
                                                    $registrasiMahasiswa->tanggal_submit &&
                                                    !$registrasiMahasiswa->alasan_reject)
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($mataKuliahReguler as $index => $peserta)
                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                                    {{ $index + 1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-xs font-medium text-gray-900">
                                                        {{ $peserta->mataKuliah->kode_mata_kuliah }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $peserta->mataKuliah->nama_mata_kuliah }}</div>
                                                    <div class="flex items-center space-x-2 mt-2">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $peserta->mataKuliah->jenis_mata_kuliah === 'TEORI' ? 'bg-blue-100 text-blue-800' : ($peserta->mataKuliah->jenis_mata_kuliah === 'PRAKTIKUM' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                                            {{ $peserta->mataKuliah->jenis_mata_kuliah }}
                                                        </span>
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            Semester {{ $peserta->semester }}
                                                        </span>
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ $peserta->kategori_mata_kuliah }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-xs font-medium text-gray-900">
                                                        {{ $peserta->kelas }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $peserta->ruangan }}</div>
                                                    <div class="text-xs text-gray-400 mt-1">
                                                        Kapasitas:
                                                        {{ $peserta->kelasKuliah->jumlah_peserta ?? '0' }}/{{ $peserta->kelasKuliah->kapasitas }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-xs font-medium text-gray-900">
                                                        {{ $peserta->dosen }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if ($peserta->hari && $peserta->jam_mulai && $peserta->jam_akhir)
                                                        <div class="text-xs font-medium text-gray-900">
                                                            {{ $peserta->hari }}</div>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ \Carbon\Carbon::parse($peserta->jam_mulai)->format('H:i') }}
                                                            -
                                                            {{ \Carbon\Carbon::parse($peserta->jam_akhir)->format('H:i') }}
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-400">Belum diatur</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $peserta->mataKuliah->sks_mata_kuliah }}
                                                    </span>
                                                </td>
                                                <!-- Status Mata Kuliah -->
                                                <td class="px-6 py-4 text-center">
                                                    @switch($peserta->status_mata_kuliah)
                                                        @case('APPROVED')
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                                Disetujui
                                                            </span>
                                                        @break

                                                        @case('REJECTED')
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
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
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                    Menunggu
                                                                </span>
                                                            @break
                                                        @endswitch
                                                    </td>

                                                    <!-- Aksi (hanya tampil jika KRS belum ditolak dan masih dalam status review) -->
                                                    @if (
                                                        $registrasiMahasiswa->status_krs === 'SUBMITTED' &&
                                                            $registrasiMahasiswa->tanggal_submit &&
                                                            !$registrasiMahasiswa->alasan_reject)
                                                        <td class="px-6 py-4 text-center">
                                                            @if ($peserta->status_mata_kuliah === 'SELECTED')
                                                                <div class="flex items-center justify-center space-x-1">
                                                                    <button
                                                                        onclick="updateMataKuliah('{{ $peserta->id_peserta }}', 'reguler', 'approve')"
                                                                        class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors duration-200"
                                                                        title="Setujui">
                                                                        <i class="fa-solid fa-check"></i>
                                                                    </button>
                                                                    <button
                                                                        onclick="updateMataKuliah('{{ $peserta->id_peserta }}', 'reguler', 'reject')"
                                                                        class="inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors duration-200"
                                                                        title="Tolak">
                                                                        <i class="fa-solid fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            @else
                                                                <span class="text-xs text-gray-400">-</span>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td colspan="{{ $registrasiMahasiswa->status_krs === 'SUBMITTED' && $registrasiMahasiswa->tanggal_submit ? '7' : '6' }}"
                                                    class="px-6 py-3 text-right text-xs font-medium text-gray-900">
                                                    Subtotal SKS Reguler:
                                                </td>
                                                <td class="px-6 py-3 text-center">
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $mataKuliahReguler->sum(function ($p) {return $p->mataKuliah->sks_mata_kuliah;}) }}
                                                    </span>
                                                </td>
                                                @if ($registrasiMahasiswa->status_krs === 'SUBMITTED' && $registrasiMahasiswa->tanggal_submit)
                                                    <td></td>
                                                @endif
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Mata Kuliah Bimbingan -->
                        @if ($mataKuliahBimbingan->count() > 0)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="px-6 py-4 border-b border-gray-100">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-graduation-cap text-purple-600"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-xs font-semibold text-gray-900">Mata Kuliah Bimbingan</h3>
                                            <p class="text-xs text-gray-500">{{ $mataKuliahBimbingan->count() }} mata kuliah
                                                dengan sistem bimbingan</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    No</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Mata Kuliah</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Jenis</th>
                                                <th
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Pembimbing</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    SKS</th>
                                                <th
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status</th>
                                                @if (
                                                    $registrasiMahasiswa->status_krs === 'SUBMITTED' &&
                                                        $registrasiMahasiswa->tanggal_submit &&
                                                        !$registrasiMahasiswa->alasan_reject)
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Aksi</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($mataKuliahBimbingan as $index => $peserta)
                                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                                        {{ $index + 1 }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-xs font-medium text-gray-900">
                                                            {{ $peserta->mataKuliah->kode_mata_kuliah }}</div>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $peserta->mataKuliah->nama_mata_kuliah }}</div>
                                                        <div class="flex items-center space-x-2 mt-2">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                Semester {{ $peserta->semester }}
                                                            </span>
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                {{ $peserta->kategori_mata_kuliah }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        {{ $peserta->mataKuliah->jenis_mata_kuliah === 'KKN'
                                                            ? 'bg-green-100 text-green-800'
                                                            : ($peserta->mataKuliah->jenis_mata_kuliah === 'MAGANG'
                                                                ? 'bg-blue-100 text-blue-800'
                                                                : 'bg-purple-100 text-purple-800') }}">
                                                            <i
                                                                class="fas fa-{{ $peserta->mataKuliah->jenis_mata_kuliah === 'KKN' ? 'users' : ($peserta->mataKuliah->jenis_mata_kuliah === 'MAGANG' ? 'briefcase' : 'book') }} mr-1"></i>
                                                            {{ $peserta->mataKuliah->jenis_mata_kuliah }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if ($peserta->dosen !== 'Belum diatur')
                                                            <div class="text-xs font-medium text-gray-900">
                                                                {{ $peserta->dosen }}</div>
                                                            @if (isset($peserta->dosen_pembimbing_2) && $peserta->dosen_pembimbing_2)
                                                                <div class="text-xs text-gray-500 mt-1">Pembimbing 2:
                                                                    {{ $peserta->dosen_pembimbing_2 }}</div>
                                                            @endif
                                                        @else
                                                            <div class="flex items-center text-xs text-gray-400">
                                                                <i class="fas fa-clock mr-1"></i>
                                                                Pembimbing belum diatur
                                                            </div>
                                                            <div class="text-xs text-blue-600 mt-1">Akan diatur admin setelah
                                                                KRS disetujui</div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ $peserta->mataKuliah->sks_mata_kuliah }}
                                                        </span>
                                                    </td>
                                                    <!-- Status Mata Kuliah -->
                                                    <td class="px-6 py-4 text-center">
                                                        @switch($peserta->status_mata_kuliah)
                                                            @case('APPROVED')
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                    Disetujui
                                                                </span>
                                                            @break

                                                            @case('REJECTED')
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
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
                                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                                            viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                        </svg>
                                                                        Menunggu
                                                                    </span>
                                                                @break
                                                            @endswitch
                                                        </td>

                                                        <!-- Aksi (hanya tampil jika KRS belum ditolak dan masih dalam status review) -->
                                                        @if (
                                                            $registrasiMahasiswa->status_krs === 'SUBMITTED' &&
                                                                $registrasiMahasiswa->tanggal_submit &&
                                                                !$registrasiMahasiswa->alasan_reject)
                                                            <td class="px-6 py-4 text-center">
                                                                @if ($peserta->status_mata_kuliah === 'SELECTED')
                                                                    <div class="flex items-center justify-center space-x-1">
                                                                        <button
                                                                            onclick="updateMataKuliah('{{ $peserta->id_peserta }}', 'bimbingan', 'approve')"
                                                                            class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors duration-200"
                                                                            title="Setujui">
                                                                            <i class="fa-solid fa-check"></i>
                                                                        </button>
                                                                        <button
                                                                            onclick="updateMataKuliah('{{ $peserta->id_peserta }}', 'bimbingan', 'reject')"
                                                                            class="inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors duration-200"
                                                                            title="Tolak">
                                                                            <i class="fa-solid fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                @else
                                                                    <span class="text-xs text-gray-400">-</span>
                                                                @endif
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-gray-50">
                                                <tr>
                                                    <td colspan="{{ $registrasiMahasiswa->status_krs === 'SUBMITTED' && $registrasiMahasiswa->tanggal_submit ? '6' : '5' }}"
                                                        class="px-6 py-3 text-right text-xs font-medium text-gray-900">
                                                        Subtotal SKS Bimbingan:
                                                    </td>
                                                    <td class="px-6 py-3 text-center">
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                            {{ $mataKuliahBimbingan->sum(function ($p) {return $p->mataKuliah->sks_mata_kuliah;}) }}
                                                        </span>
                                                    </td>
                                                    @if ($registrasiMahasiswa->status_krs === 'SUBMITTED' && $registrasiMahasiswa->tanggal_submit)
                                                        <td></td>
                                                    @endif
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Empty State -->
                            @if ($mataKuliahTerpilih->count() === 0)
                                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                                    <div class="px-6 py-12 text-center">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                            </path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mata Kuliah</p>
                                        <p class="text-xs text-gray-500">Mahasiswa belum memilih mata kuliah apapun.</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Jadwal Mingguan (Hanya untuk Mata Kuliah Reguler) -->
                            @if (count($jadwalMingguan) > 0 && $mataKuliahReguler->count() > 0)
                                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                                    <div class="px-6 py-4 border-b border-gray-100">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-calendar-week text-indigo-600"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-xs font-semibold text-gray-900">Jadwal Mingguan</h3>
                                                <p class="text-xs text-gray-500">Preview jadwal kuliah reguler dalam seminggu</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-6">
                                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                                            @foreach (['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'] as $hari)
                                                <div class="border border-gray-200 rounded-lg">
                                                    <div class="px-3 py-2 bg-gray-50 border-b border-gray-200">
                                                        <h4 class="text-xs font-medium text-gray-900">{{ $hari }}</h4>
                                                    </div>
                                                    <div class="p-3 space-y-2 min-h-[120px]">
                                                        @if (isset($jadwalMingguan[$hari]) && count($jadwalMingguan[$hari]) > 0)
                                                            @foreach ($jadwalMingguan[$hari] as $jadwal)
                                                                <div
                                                                    class="p-2 rounded {{ $jadwal['status'] === 'APPROVED' ? 'bg-green-50 border border-green-200' : ($jadwal['status'] === 'REJECTED' ? 'bg-red-50 border border-red-200' : 'bg-blue-50 border border-blue-200') }}">
                                                                    <div
                                                                        class="text-xs font-medium {{ $jadwal['status'] === 'APPROVED' ? 'text-green-900' : ($jadwal['status'] === 'REJECTED' ? 'text-red-900' : 'text-blue-900') }}">
                                                                        {{ $jadwal['kode_mata_kuliah'] }}
                                                                    </div>
                                                                    <div
                                                                        class="text-xs {{ $jadwal['status'] === 'APPROVED' ? 'text-green-700' : ($jadwal['status'] === 'REJECTED' ? 'text-red-700' : 'text-blue-700') }} mt-1">
                                                                        {{ $jadwal['kelas'] }} • {{ $jadwal['ruangan'] }}
                                                                    </div>
                                                                    <div
                                                                        class="text-xs {{ $jadwal['status'] === 'APPROVED' ? 'text-green-600' : ($jadwal['status'] === 'REJECTED' ? 'text-red-600' : 'text-blue-600') }} mt-1">
                                                                        {{ \Carbon\Carbon::parse($jadwal['jam_mulai'])->format('H:i') }}-{{ \Carbon\Carbon::parse($jadwal['jam_akhir'])->format('H:i') }}
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="text-center text-xs text-gray-400 py-4">Tidak ada jadwal
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Total SKS Summary -->
                            @if ($mataKuliahTerpilih->count() > 0)
                                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                                    <div class="px-6 py-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-calculator text-gray-600"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-xs font-semibold text-gray-900">Total SKS</h3>
                                                    <p class="text-xs text-gray-500">Ringkasan beban studi</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-2xl font-bold text-gray-900">{{ $totalSks }}</div>
                                                <div class="text-xs text-gray-500">SKS Total</div>
                                            </div>
                                        </div>

                                        <div class="mt-4 grid grid-cols-2 gap-4">
                                            @if ($mataKuliahReguler->count() > 0)
                                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                                    <div>
                                                        <div class="text-xs font-medium text-blue-900">Mata Kuliah Reguler</div>
                                                        <div class="text-xs text-blue-700">{{ $mataKuliahReguler->count() }} mata
                                                            kuliah</div>
                                                    </div>
                                                    <div class="text-lg font-semibold text-blue-900">
                                                        {{ $mataKuliahReguler->sum(function ($p) {return $p->mataKuliah->sks_mata_kuliah;}) }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($mataKuliahBimbingan->count() > 0)
                                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                                    <div>
                                                        <div class="text-xs font-medium text-purple-900">Mata Kuliah Bimbingan
                                                        </div>
                                                        <div class="text-xs text-purple-700">{{ $mataKuliahBimbingan->count() }}
                                                            mata kuliah</div>
                                                    </div>
                                                    <div class="text-lg font-semibold text-purple-900">
                                                        {{ $mataKuliahBimbingan->sum(function ($p) {return $p->mataKuliah->sks_mata_kuliah;}) }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Sidebar -->
                        <div class="space-y-6">
                            <!-- Info Mahasiswa -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                                <div class="px-6 py-4 border-b border-gray-100">
                                    <h3 class="text-xs font-semibold text-gray-900">Info Mahasiswa</h3>
                                </div>
                                <div class="px-6 py-4 space-y-4">
                                    <div class="text-center">
                                        <div
                                            class="h-16 w-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <i class="fa-solid fa-user text-gray-600 text-xl"></i>
                                        </div>
                                        <h4 class="text-xs font-medium text-gray-900">
                                            {{ $registrasiMahasiswa->mahasiswa->pengguna->nama }}</h4>
                                        <p class="text-xs text-gray-500">{{ $registrasiMahasiswa->mahasiswa->nim }}</p>
                                    </div>

                                    <div class="border-t border-gray-200 pt-4 space-y-3">
                                        <div class="flex justify-between text-xs">
                                            <span class="text-gray-500">Program Studi:</span>
                                            <span
                                                class="font-medium text-gray-900">{{ $registrasiMahasiswa->mahasiswa->programStudi->nama_program_studi }}</span>
                                        </div>
                                        <div class="flex justify-between text-xs">
                                            <span class="text-gray-500">Angkatan:</span>
                                            <span
                                                class="font-medium text-gray-900">{{ $registrasiMahasiswa->mahasiswa->angkatan }}</span>
                                        </div>
                                        <div class="flex justify-between text-xs">
                                            <span class="text-gray-500">Semester:</span>
                                            <span
                                                class="font-medium text-gray-900">{{ $registrasiMahasiswa->semester->nama_semester }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Riwayat Akademik -->
                            @if (!empty($riwayatAkademik) && $riwayatAkademik->count() > 0)
                                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                                    <div class="px-6 py-4 border-b border-gray-100">
                                        <h3 class="text-xs font-semibold text-gray-900">Riwayat Akademik</h3>
                                    </div>
                                    <div class="px-6 py-4">
                                        <div class="space-y-3">
                                            @foreach ($riwayatAkademik as $riwayat)
                                                <div
                                                    class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                                    <div>
                                                        <p class="text-xs font-medium text-gray-900">{{ $riwayat->nama_semester }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">{{ $riwayat->total_mata_kuliah }} MK •
                                                            {{ $riwayat->total_sks }} SKS</p>
                                                    </div>
                                                    <div class="text-right">
                                                        @if ($riwayat->ipk_semester)
                                                            <p
                                                                class="text-xs font-medium {{ $riwayat->ipk_semester >= 3.0 ? 'text-green-600' : ($riwayat->ipk_semester >= 2.5 ? 'text-yellow-600' : 'text-red-600') }}">
                                                                {{ number_format($riwayat->ipk_semester, 2) }}
                                                            </p>
                                                        @else
                                                            <p class="text-xs text-gray-400">-</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal Approve KRS -->
                <div id="approveModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                        <div
                            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7">
                                            </path>
                                        </svg>
                                    </div>
                                    <div class="mt-3 ml-4 sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">Setujui KRS</h3>
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-500">
                                                Anda akan menyetujui KRS mahasiswa
                                                {{ $registrasiMahasiswa->mahasiswa->pengguna->nama }}
                                                dengan {{ $mataKuliahTerpilih->count() }} mata kuliah
                                                ({{ $totalSks }} SKS).
                                            </p>
                                            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                                <div class="text-xs text-gray-700">
                                                    <div class="flex justify-between">
                                                        <span>Mata Kuliah Reguler:</span>
                                                        <span>{{ $mataKuliahReguler->count() }}
                                                            ({{ $mataKuliahReguler->sum(function ($p) {return $p->mataKuliah->sks_mata_kuliah;}) }}
                                                            SKS)</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span>Mata Kuliah Bimbingan:</span>
                                                        <span>{{ $mataKuliahBimbingan->count() }}
                                                            ({{ $mataKuliahBimbingan->sum(function ($p) {return $p->mataKuliah->sks_mata_kuliah;}) }}
                                                            SKS)</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <form method="POST"
                                    action="{{ route('krs.approval.approve', $registrasiMahasiswa->id_registrasi_mahasiswa) }}"
                                    class="sm:ml-3">
                                    @csrf
                                    <button type="submit"
                                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:w-auto sm:text-xs">
                                        Ya, Setujui KRS
                                    </button>
                                </form>
                                <button type="button" onclick="closeApproveModal()"
                                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-xs">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Reject KRS -->
                <div id="rejectModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                        <div
                            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
                            <form method="POST"
                                action="{{ route('krs.approval.reject', $registrasiMahasiswa->id_registrasi_mahasiswa) }}">
                                @csrf
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="flex items-start">
                                        <div
                                            class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </div>
                                        <div class="mt-3 ml-4 sm:mt-0 sm:ml-4 sm:text-left w-full">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900">Tolak KRS</h3>
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500 mb-4">
                                                    Berikan alasan penolakan KRS mahasiswa
                                                    {{ $registrasiMahasiswa->mahasiswa->pengguna->nama }}.
                                                </p>
                                                <textarea name="alasan_reject" required
                                                    class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                                    rows="4" placeholder="Alasan penolakan KRS..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-xs sm:ml-3">
                                        Tolak KRS
                                    </button>
                                    <button type="button" onclick="closeRejectModal()"
                                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-xs">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @push('scripts')
                    <script>
                        function openApproveModal() {
                            document.getElementById('approveModal').classList.remove('hidden');
                        }

                        function closeApproveModal() {
                            document.getElementById('approveModal').classList.add('hidden');
                        }

                        function openRejectModal() {
                            document.getElementById('rejectModal').classList.remove('hidden');
                        }

                        function closeRejectModal() {
                            document.getElementById('rejectModal').classList.add('hidden');
                        }

                        function updateMataKuliah(idPeserta, jenis, action) {
                            const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
                            const jenisText = jenis === 'reguler' ? 'mata kuliah reguler' : 'mata kuliah bimbingan';

                            if (confirm(`Yakin ingin ${actionText} ${jenisText} ini?`)) {
                                fetch('{{ route('krs.approval.update-mata-kuliah') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            id_peserta: idPeserta,
                                            jenis: jenis,
                                            action: action
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            location.reload();
                                        } else {
                                            alert(data.message || 'Terjadi kesalahan');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('Terjadi kesalahan jaringan');
                                    });
                            }
                        }

                        // Close modal with Escape key
                        document.addEventListener('keydown', function(event) {
                            if (event.key === 'Escape') {
                                closeApproveModal();
                                closeRejectModal();
                            }
                        });
                    </script>
                @endpush
            @endsection
