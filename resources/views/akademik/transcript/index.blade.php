{{-- resources/views/akademik/transcript/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Transkrip Akademik')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Transkrip Akademik</h1>
                            <p class="text-sm text-gray-600">Riwayat lengkap mata kuliah yang telah lulus</p>
                        </div>

                        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                            <a href="{{ route('akademik.transcript.download-pdf', request()->only('mahasiswa_id')) }}"
                                target="_blank"
                                class="inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                                <i class="fas fa-file-pdf w-3 h-3 mr-2"></i>
                                Download PDF
                            </a>
                            @if (request('mahasiswa_id'))
                                <a href="{{ route('admin.akademik.cari-mahasiswa') }}"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-900 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                    <i class="fas fa-arrow-left w-3 h-3 mr-2"></i>
                                    Kembali
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Info Mahasiswa & Progress -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Info Mahasiswa -->
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 mb-3">Informasi Mahasiswa</h3>
                            <table class="table-auto text-sm w-full">
                                <tr>
                                    <td class="font-medium text-gray-700 pr-4 py-1 w-24">NIM</td>
                                    <td class="text-gray-600 w-4">:</td>
                                    <td class="text-gray-900 pl-2">{{ $transcriptData['mahasiswa']['nim'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-gray-700 pr-4 py-1">Nama</td>
                                    <td class="text-gray-600">:</td>
                                    <td class="text-gray-900 pl-2">{{ $transcriptData['mahasiswa']['nama'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-gray-700 pr-4 py-1 whitespace-nowrap">Program Studi</td>
                                    <td class="text-gray-600">:</td>
                                    <td class="text-gray-900 pl-2">{{ $transcriptData['mahasiswa']['program_studi'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-gray-700 pr-4 py-1">Angkatan</td>
                                    <td class="text-gray-600">:</td>
                                    <td class="text-gray-900 pl-2">{{ $transcriptData['mahasiswa']['angkatan'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-gray-700 pr-4 py-1">Kurikulum</td>
                                    <td class="text-gray-600">:</td>
                                    <td class="text-gray-900 pl-2">{{ $transcriptData['mahasiswa']['kurikulum'] }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Progress Kelulusan -->
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 mb-3">Progress Kelulusan</h3>

                            <!-- Statistics Cards -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="bg-white rounded-lg p-3 shadow-sm border">
                                    <div class="text-xs font-medium text-gray-600">SKS Lulus</div>
                                    <div class="text-lg font-semibold text-blue-600">
                                        {{ $transcriptData['ringkasan']['total_sks_lulus'] }}/{{ $transcriptData['ringkasan']['batas_sks_kurikulum'] }}
                                    </div>
                                </div>
                                <div class="bg-white rounded-lg p-3 shadow-sm border">
                                    <div class="text-xs font-medium text-gray-600">IPK</div>
                                    <div class="text-lg font-semibold text-green-600">
                                        {{ $transcriptData['ringkasan']['ipk_keseluruhan'] }}</div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-3">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-gray-700">Progress Kelulusan</span>
                                    <span
                                        class="text-xs font-medium text-gray-900">{{ $transcriptData['ringkasan']['persentase_kelulusan'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                        style="width: {{ min(100, $transcriptData['ringkasan']['persentase_kelulusan']) }}%">
                                    </div>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            {{-- <div class="text-center">
                                @if ($transcriptData['ringkasan']['status_kelulusan'] == 'LULUS')
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        LULUS
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        BELUM LULUS
                                    </span>
                                @endif
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Mata Kuliah -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode MK</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mata Kuliah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    SKS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Semester</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nilai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Indeks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($transcriptData['mata_kuliah'] as $index => $mk)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-medium bg-gray-100 text-gray-800">
                                            {{ $mk['kode_mata_kuliah'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-gray-900">{{ $mk['nama_mata_kuliah'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $mk['sks'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($mk['jenis'] == 'WAJIB')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Wajib
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Pilihan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                        {{ $mk['semester_diambil'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="text-xs font-semibold
                                        ">{{ $mk['nilai_huruf'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">
                                        {{ number_format($mk['nilai_indeks'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-12 h-12 text-gray-400 mb-4">
                                                <i class="fas fa-certificate text-4xl"></i>
                                            </div>
                                            <p class="text-lg font-medium text-gray-900 mb-2">Belum ada mata kuliah lulus
                                            </p>
                                            <p class="text-sm text-gray-500">Mata kuliah yang sudah lulus akan muncul di
                                                sini</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
