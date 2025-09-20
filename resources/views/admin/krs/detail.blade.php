@extends('layouts.app')

@section('title', 'Detail KRS Mahasiswa - Admin')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Detail KRS Mahasiswa</h1>
                            <p class="text-sm text-gray-600">{{ $registrasiMahasiswa->mahasiswa->pengguna->nama ?? 'N/A' }} •
                                {{ $registrasiMahasiswa->mahasiswa->nim ?? 'N/A' }}</p>
                        </div>

                        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                            <a href="{{ route('admin.krs.laporan', ['semester' => $registrasiMahasiswa->id_semester]) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Laporan
                            </a>

                            <button onclick="printDetail()"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                    </path>
                                </svg>
                                Print Detail
                            </button>

                            @if ($registrasiMahasiswa->status_krs === 'APPROVED')
                                <button
                                    onclick="confirmActivateKrs('{{ $registrasiMahasiswa->id_registrasi_mahasiswa }}', '{{ $registrasiMahasiswa->mahasiswa->pengguna->nama ?? '' }}')"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Aktivasi KRS
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Student Info Bar -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
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
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-900">
                                    {{ $registrasiMahasiswa->mahasiswa->pengguna->nama ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $registrasiMahasiswa->mahasiswa->nim ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m6 0h2m2 0H7m6 0v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v6">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-900">
                                    {{ $registrasiMahasiswa->mahasiswa->programStudi->nama_program_studi ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $registrasiMahasiswa->mahasiswa->programStudi->jenjang->kode_jenjang_pendidikan ?? 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-900">
                                    {{ $registrasiMahasiswa->pembimbingAkademik->dosen->pengguna->nama ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $registrasiMahasiswa->pembimbingAkademik->dosen->nidn ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-900">{{ $totalSks }} SKS</p>
                                <p class="text-xs text-gray-500">
                                    {{ $registrasiMahasiswa->pesertaKelasKuliah->where('status_mata_kuliah', '!=', 'REJECTED')->count() }}
                                    Mata Kuliah</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3a4 4 0 118 0v4m-4 8a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-900">
                                    {{ $registrasiMahasiswa->semester->nama_semester ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">Semester Akademik</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 {{ $registrasiMahasiswa->status_krs === 'ACTIVE' ? 'bg-green-100' : ($registrasiMahasiswa->status_krs === 'APPROVED' ? 'bg-blue-100' : ($registrasiMahasiswa->tanggal_submit ? 'bg-yellow-100' : 'bg-gray-100')) }} rounded-full flex items-center justify-center">
                                    @if ($registrasiMahasiswa->status_krs === 'ACTIVE')
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    @elseif($registrasiMahasiswa->status_krs === 'APPROVED')
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($registrasiMahasiswa->tanggal_submit)
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-900">
                                    @if ($registrasiMahasiswa->status_krs === 'ACTIVE')
                                        KRS Aktif
                                    @elseif($registrasiMahasiswa->status_krs === 'APPROVED')
                                        KRS Disetujui
                                    @elseif($registrasiMahasiswa->tanggal_submit)
                                        Menunggu Approval
                                    @else
                                        Belum Submit
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">Status KRS</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Mata Kuliah Details -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">Detail Mata Kuliah</h3>
                            <p class="text-xs text-gray-500 mt-1">Daftar mata kuliah yang dipilih mahasiswa</p>
                        </div>

                        <div class="overflow-x-auto">
                            @if ($registrasiMahasiswa->pesertaKelasKuliah->count() > 0)
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
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($registrasiMahasiswa->pesertaKelasKuliah as $index => $peserta)
                                            @if ($peserta->status_mata_kuliah !== 'REJECTED')
                                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                                        {{ $index + 1 }}
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="text-xs font-medium text-gray-900">
                                                            {{ $peserta->mataKuliah->kode_mata_kuliah }}</div>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $peserta->mataKuliah->nama_mata_kuliah }}</div>
                                                        <div class="flex items-center space-x-2 mt-2">
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $peserta->kelasKuliah->kurikulumMataKuliah->jenis_mata_kuliah === 'WAJIB' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                                {{ $peserta->kelasKuliah->kurikulumMataKuliah->jenis_mata_kuliah }}
                                                            </span>
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                Semester
                                                                {{ $peserta->kelasKuliah->kurikulumMataKuliah->semester }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-xs font-medium text-gray-900">
                                                            {{ $peserta->kelasKuliah->nama_kelas_kuliah }}</div>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $peserta->kelasKuliah->nama_ruangan }}</div>
                                                        <div class="text-xs text-gray-400 mt-1">
                                                            Kapasitas:
                                                            {{ $peserta->kelasKuliah->jumlah_peserta }}/{{ $peserta->kelasKuliah->kapasitas }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-xs font-medium text-gray-900">
                                                            {{ $peserta->kelasKuliah->dosen->pengguna->nama ?? 'N/A' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $peserta->kelasKuliah->dosen->nidn ?? 'N/A' }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if ($peserta->kelasKuliah->hari && $peserta->kelasKuliah->jam_mulai && $peserta->kelasKuliah->jam_akhir)
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
                                                    <td class="px-6 py-4 text-center">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ $peserta->mataKuliah->sks_mata_kuliah }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        @if ($peserta->status_mata_kuliah === 'APPROVED')
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <svg class="w-3 h-3 mr-1" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                                Disetujui
                                                            </span>
                                                        @elseif($peserta->status_mata_kuliah === 'SELECTED')
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                <svg class="w-3 h-3 mr-1" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                    </path>
                                                                </svg>
                                                                Menunggu Review
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="5"
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-900">
                                                Total SKS:
                                            </td>
                                            <td class="px-6 py-3 text-center">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-600 text-white">
                                                    {{ $totalSks }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            @else
                                <div class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                        </path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mata Kuliah</p>
                                    <p class="text-xs text-gray-500">Mahasiswa belum memilih mata kuliah</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Jadwal Mingguan -->
                    @if (count($jadwalMingguan) > 0 && $totalSks > 0)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-900">Jadwal Mingguan</h3>
                                <p class="text-xs text-gray-500 mt-1">Jadwal kuliah mahasiswa dalam seminggu</p>
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
                                                        <div class="bg-blue-50 border border-blue-200 rounded p-2">
                                                            <div class="text-xs font-medium text-blue-900">
                                                                {{ $jadwal['kode_mata_kuliah'] }}</div>
                                                            <div class="text-xs text-blue-700 mt-1">{{ $jadwal['kelas'] }}
                                                                • {{ $jadwal['ruangan'] }}</div>
                                                            <div class="text-xs text-blue-600 mt-1">
                                                                {{ \Carbon\Carbon::parse($jadwal['jam_mulai'])->format('H:i') }}-{{ \Carbon\Carbon::parse($jadwal['jam_akhir'])->format('H:i') }}
                                                            </div>
                                                            @if ($jadwal['status'] === 'APPROVED')
                                                                <div class="text-xs text-green-600 mt-1 font-medium">✓
                                                                    Disetujui</div>
                                                            @elseif($jadwal['status'] === 'SELECTED')
                                                                <div class="text-xs text-yellow-600 mt-1 font-medium">⏳
                                                                    Review</div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-center text-xs text-gray-400 py-4">
                                                        Tidak ada jadwal
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Summary Statistics -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">Ringkasan KRS</h3>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            @php
                                $totalMK = $registrasiMahasiswa->pesertaKelasKuliah
                                    ->where('status_mata_kuliah', '!=', 'REJECTED')
                                    ->count();
                                $approved = $registrasiMahasiswa->pesertaKelasKuliah
                                    ->where('status_mata_kuliah', 'APPROVED')
                                    ->count();
                                $wajib = $registrasiMahasiswa->pesertaKelasKuliah
                                    ->filter(function ($p) {
                                        return $p->kelasKuliah->kurikulumMataKuliah->jenis_mata_kuliah === 'WAJIB' &&
                                            $p->status_mata_kuliah !== 'REJECTED';
                                    })
                                    ->count();
                                $pilihan = $registrasiMahasiswa->pesertaKelasKuliah
                                    ->filter(function ($p) {
                                        return $p->kelasKuliah->kurikulumMataKuliah->jenis_mata_kuliah === 'PILIHAN' &&
                                            $p->status_mata_kuliah !== 'REJECTED';
                                    })
                                    ->count();
                            @endphp

                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Total Mata Kuliah</span>
                                <span class="text-xs font-medium text-gray-900">{{ $totalMK }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Mata Kuliah Wajib</span>
                                <span class="text-xs font-medium text-green-600">{{ $wajib }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Mata Kuliah Pilihan</span>
                                <span class="text-xs font-medium text-yellow-600">{{ $pilihan }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Status Disetujui</span>
                                <span
                                    class="text-xs font-medium text-blue-600">{{ $approved }}/{{ $totalMK }}</span>
                            </div>
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-gray-900">Total SKS</span>
                                    <span class="text-lg font-semibold text-blue-600">{{ $totalSks }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Aktivitas -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">Timeline Aktivitas</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    @foreach ($timeline as $index => $activity)
                                        <li>
                                            <div class="relative {{ $index < $timeline->count() - 1 ? 'pb-8' : '' }}">
                                                @if ($index < $timeline->count() - 1)
                                                    <div class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200">
                                                    </div>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div
                                                        class="h-8 w-8 {{ $activity['color'] === 'green' ? 'bg-green-500' : ($activity['color'] === 'yellow' ? 'bg-yellow-500' : ($activity['color'] === 'blue' ? 'bg-blue-500' : 'bg-gray-500')) }} rounded-full flex items-center justify-center ring-8 ring-white">
                                                        <i class="{{ $activity['icon'] }} w-4 h-4 text-white text-xs"></i>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5">
                                                        <div>
                                                            <p class="text-xs font-medium text-gray-900">
                                                                {{ $activity['aktivitas'] }}</p>
                                                            <p class="text-xs text-gray-500 mt-0.5">
                                                                {{ $activity['keterangan'] }}</p>
                                                        </div>
                                                        <div class="mt-2">
                                                            <p class="text-xs text-gray-400">
                                                                {{ $activity['tanggal']->format('d M Y H:i') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Actions -->
                    @if ($registrasiMahasiswa->status_krs === 'APPROVED')
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-900">Aksi Admin</h3>
                            </div>
                            <div class="px-6 py-4 space-y-4">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <div class="flex">
                                        <svg class="w-4 h-4 text-green-400 mt-0.5 mr-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div class="text-xs text-green-800">
                                            <p class="font-medium">KRS Siap Diaktivasi</p>
                                            <p class="mt-1">KRS telah disetujui PA dan dapat diaktivasi untuk memulai
                                                perkuliahan.</p>
                                        </div>
                                    </div>
                                </div>

                                <button
                                    onclick="confirmActivateKrs('{{ $registrasiMahasiswa->id_registrasi_mahasiswa }}', '{{ $registrasiMahasiswa->mahasiswa->pengguna->nama ?? '' }}')"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Aktivasi KRS Sekarang
                                </button>
                            </div>
                        </div>
                    @elseif($registrasiMahasiswa->status_krs === 'ACTIVE')
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-900">Status KRS</h3>
                            </div>
                            <div class="px-6 py-4">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <div class="flex">
                                        <svg class="w-4 h-4 text-green-400 mt-0.5 mr-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div class="text-xs text-green-800">
                                            <p class="font-medium">KRS Sudah Aktif</p>
                                            <p class="mt-1">Mahasiswa dapat mengikuti perkuliahan sesuai jadwal.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Info -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">Informasi Mahasiswa</h3>
                        </div>
                        <div class="px-6 py-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500">Angkatan</span>
                                <span
                                    class="text-xs font-medium text-gray-900">{{ $registrasiMahasiswa->mahasiswa->angkatan ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500">Status Mahasiswa</span>
                                <span
                                    class="text-xs font-medium {{ $registrasiMahasiswa->mahasiswa->status_mahasiswa === 'AKTIF' ? 'text-green-600' : 'text-red-600' }}">{{ $registrasiMahasiswa->mahasiswa->status_mahasiswa ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500">Kurikulum</span>
                                <span
                                    class="text-xs font-medium text-gray-900">{{ $registrasiMahasiswa->mahasiswa->kurikulum->nama_kurikulum ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activation Modal -->
    <div id="activationModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeActivationModal()">
            </div>

            <div
                class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">
                <form id="activationForm" method="POST" action="{{ route('admin.krs.aktivasi-mass') }}">
                    @csrf
                    <input type="hidden" name="semester_id" value="{{ $registrasiMahasiswa->id_semester }}">
                    <input type="hidden" name="single_id" id="single_krs_id">

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
                                    Aktivasi KRS
                                </h3>
                                <p class="text-sm text-gray-500 mt-1" id="activationText">
                                    <!-- Will be populated by JavaScript -->
                                </p>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex">
                                <svg class="w-4 h-4 text-blue-400 mt-0.5 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-xs text-blue-800">
                                    <p class="font-medium">Informasi:</p>
                                    <p class="mt-1">KRS akan diubah statusnya dari "APPROVED" menjadi "ACTIVE". Mahasiswa
                                        dapat mulai mengikuti perkuliahan sesuai jadwal.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                        <button type="button" onclick="closeActivationModal()"
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

    @push('scripts')
        <script>
            // Print function
            function printDetail() {
                window.print();
            }

            // Activation modal
            function confirmActivateKrs(registrasiId, namaMahasiswa) {
                document.getElementById('single_krs_id').value = registrasiId;
                document.getElementById('activationText').textContent = `Aktivasi KRS mahasiswa ${namaMahasiswa}?`;
                document.getElementById('activationModal').classList.remove('hidden');
            }

            function closeActivationModal() {
                document.getElementById('activationModal').classList.add('hidden');
            }

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeActivationModal();
                }
            });
        </script>
    @endpush
@endsection
