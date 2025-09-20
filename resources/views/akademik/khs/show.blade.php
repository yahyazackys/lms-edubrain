{{-- resources/views/akademik/khs/show.blade.php --}}
@extends('layouts.app')

@section('title', 'KHS - ' . $khsData['semester']['nama'])

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">
                                Kartu Hasil Studi (KHS)
                            </h1>
                            <p class="text-sm text-gray-600">{{ $khsData['semester']['nama'] }}</p>
                        </div>

                        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                            <a href="{{ route('akademik.khs.download-pdf', array_merge(request()->all())) }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                                <i class="fas fa-file-pdf w-3 h-3 mr-2"></i>
                                Download PDF
                            </a>
                            <a href="{{ route('akademik.khs.index', request()->only('mahasiswa_id')) }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-900 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                <i class="fas fa-arrow-left w-3 h-3 mr-2"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Info Mahasiswa & Ringkasan -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Info Mahasiswa -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Informasi Mahasiswa</h3>
                            <table class="table-auto text-sm w-full">
                                <tr>
                                    <td class="font-medium text-gray-700 pr-4 py-1">NIM</td>
                                    <td class="text-gray-600">:</td>
                                    <td class="text-gray-900 pl-2">{{ $khsData['mahasiswa']['nim'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-gray-700 pr-4 py-1">Nama</td>
                                    <td class="text-gray-600">:</td>
                                    <td class="text-gray-900 pl-2">{{ $khsData['mahasiswa']['nama'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-gray-700 pr-4 py-1">Program Studi</td>
                                    <td class="text-gray-600">:</td>
                                    <td class="text-gray-900 pl-2">{{ $khsData['mahasiswa']['program_studi'] }}</td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-gray-700 pr-4 py-1">Angkatan</td>
                                    <td class="text-gray-600">:</td>
                                    <td class="text-gray-900 pl-2">{{ $khsData['mahasiswa']['angkatan'] }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Ringkasan Semester -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Ringkasan Semester</h3>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-white rounded-lg p-3 shadow-sm border">
                                    <div class="text-xs font-medium text-gray-600">Total SKS</div>
                                    <div class="text-lg font-semibold text-blue-600">
                                        {{ $khsData['ringkasan']['total_sks_semester'] }}</div>
                                </div>
                                <div class="bg-white rounded-lg p-3 shadow-sm border">
                                    <div class="text-xs font-medium text-gray-600">IP Semester</div>
                                    <div class="text-lg font-semibold text-green-600">
                                        {{ $khsData['ringkasan']['ip_semester'] }}</div>
                                </div>
                                <div class="bg-white rounded-lg p-3 shadow-sm border">
                                    <div class="text-xs font-medium text-gray-600">IPK Kumulatif</div>
                                    <div class="text-lg font-semibold text-purple-600">
                                        {{ $khsData['ringkasan']['ipk_kumulatif'] }}</div>
                                </div>
                            </div>
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
                                    Nilai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Indeks</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mutu</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($khsData['mata_kuliah'] as $index => $mk)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-medium bg-gray-100 text-gray-800">
                                            {{ $mk['kode_mata_kuliah'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-gray-900 whitespace-nowrap">
                                        {{ $mk['nama_mata_kuliah'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
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
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="text-xs font-semibold
                                        ">{{ $mk['nilai_huruf'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">
                                        {{ number_format($mk['nilai_indeks'], 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">
                                        {{ number_format($mk['mutu'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-12 h-12 text-gray-400 mb-4">
                                                <i class="fas fa-file-alt text-4xl"></i>
                                            </div>
                                            <p class="text-lg font-medium text-gray-900 mb-2">Tidak ada data</p>
                                            <p class="text-sm text-gray-500">Belum ada mata kuliah untuk semester ini</p>
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
