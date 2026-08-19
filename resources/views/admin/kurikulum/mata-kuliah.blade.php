@extends('layouts.app')

@section('title', 'Mata Kuliah Kurikulum')

@php
    $categoryLabels = [
        'MKWUUPT' => 'MK Wajib UUPT',
        'MKWU' => 'MK Wajib Universitas',
        'MKWPS' => 'MK Wajib Program Studi',
        'MKP' => 'MK Pilihan',
    ];

    $categoryBadgeClasses = [
        'MKWUUPT' => 'bg-orange-100 text-orange-800',
        'MKWU' => 'bg-cyan-100 text-cyan-800',
        'MKWPS' => 'bg-purple-100 text-purple-800',
        'MKP' => 'bg-indigo-100 text-indigo-800',
    ];

    $courseTypeClasses = [
        'TEORI' => 'bg-gray-100 text-gray-800',
        'PRAKTIKUM' => 'bg-green-100 text-green-800',
        'MAGANG' => 'bg-purple-100 text-purple-800',
        'KKN' => 'bg-yellow-100 text-yellow-800',
        'SKRIPSI' => 'bg-red-100 text-red-800',
    ];

    function getStatusBadge($actual, $required)
    {
        if ($actual < $required) {
            $class = 'bg-red-100 text-red-800';
            $text = 'Kurang ' . ($required - $actual) . ' SKS';
        } elseif ($actual == $required) {
            $class = 'bg-green-100 text-green-800';
            $text = 'Sesuai';
        } else {
            $class = 'bg-blue-100 text-blue-800';
            $text = 'Lebih ' . ($actual - $required) . ' SKS';
        }

        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$class}\">{$text}</span>";
    }
@endphp

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            {{-- Header Section --}}
            <header class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        {{-- Title and Info --}}
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-lg font-heading font-semibold text-gray-900">Manajemen Mata Kuliah</h1>
                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-1 sm:space-y-0 text-xs text-gray-600">
                                <span class="font-medium">{{ $kurikulum->nama_kurikulum }}</span>
                                <span class="text-gray-400 hidden sm:inline">•</span>
                                <span>{{ $kurikulum->programStudi->nama_program_studi }}
                                    ({{ $kurikulum->programStudi->jenjang->kode_jenjang_pendidikan }})</span>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('kurikulum.index') }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-900 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Kembali
                            </a>

                            <!-- Import & Export Container -->
                            <div class="flex space-x-3">
                                <!-- Import Excel Button -->
                                <button onclick="openImportModal()"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                        </path>
                                    </svg>
                                    Import Excel
                                </button>

                                <!-- Export Excel Button -->
                                <a href="{{ route('kurikulum.mata-kuliah.export', $kurikulum->id_kurikulum) }}"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Export Excel
                                </a>
                            </div>

                            <!-- Download Template Button -->
                            <a href="{{ route('kurikulum.mata-kuliah.export-template', $kurikulum->id_kurikulum) }}"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Template Excel
                            </a>

                            <button onclick="openModal()"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Mata Kuliah
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Statistics Section --}}
                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                    <h1 class="text-lg font-heading font-semibold text-gray-900 mb-4 text-center">Statistik Kurikulum</h1>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="border border-gray-400 px-4 py-3 text-left font-semibold text-gray-900">
                                        Kategori Mata Kuliah</th>
                                    <th class="border border-gray-400 px-4 py-3 text-center font-semibold text-gray-900">
                                        Minimal SKS</th>
                                    <th class="border border-gray-400 px-4 py-3 text-center font-semibold text-gray-900">
                                        Jumlah MK</th>
                                    <th class="border border-gray-400 px-4 py-3 text-center font-semibold text-gray-900">
                                        Total SKS</th>
                                    <th class="border border-gray-400 px-4 py-3 text-center font-semibold text-gray-900">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-white">
                                    <td class="border border-gray-400 px-4 py-3 font-medium">
                                        {{ $categoryLabels['MKWUUPT'] }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center font-semibold">
                                        {{ $statistics['sks_mkwuupt_minimal'] }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center">
                                        {{ $statistics['mata_kuliah_mkwuupt'] }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center font-semibold">
                                        {{ number_format($statistics['sks_mkwuupt'], 0) }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center">{!! getStatusBadge($statistics['sks_mkwuupt'], $statistics['sks_mkwuupt_minimal']) !!}</td>
                                </tr>
                                <tr class="bg-white">
                                    <td class="border border-gray-400 px-4 py-3 font-medium">{{ $categoryLabels['MKWU'] }}
                                    </td>
                                    <td class="border border-gray-400 px-4 py-3 text-center font-semibold">
                                        {{ $statistics['sks_mkwu_minimal'] }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center">
                                        {{ $statistics['mata_kuliah_mkwu'] }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center font-semibold">
                                        {{ number_format($statistics['sks_mkwu'], 0) }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center">{!! getStatusBadge($statistics['sks_mkwu'], $statistics['sks_mkwu_minimal']) !!}</td>
                                </tr>
                                <tr class="bg-white">
                                    <td class="border border-gray-400 px-4 py-3 font-medium">{{ $categoryLabels['MKWPS'] }}
                                    </td>
                                    <td class="border border-gray-400 px-4 py-3 text-center font-semibold">
                                        {{ $statistics['sks_mkwps_minimal'] }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center">
                                        {{ $statistics['mata_kuliah_mkwps'] }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center font-semibold">
                                        {{ number_format($statistics['sks_mkwps'], 0) }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center">{!! getStatusBadge($statistics['sks_mkwps'], $statistics['sks_mkwps_minimal']) !!}</td>
                                </tr>
                                <tr class="bg-white">
                                    <td class="border border-gray-400 px-4 py-3 font-medium">{{ $categoryLabels['MKP'] }}
                                    </td>
                                    <td class="border border-gray-400 px-4 py-3 text-center font-semibold">
                                        {{ $statistics['sks_mkp_minimal'] }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center">
                                        {{ $statistics['mata_kuliah_mkp'] }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center font-semibold">
                                        {{ number_format($statistics['sks_mkp'], 0) }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center">{!! getStatusBadge($statistics['sks_mkp'], $statistics['sks_mkp_minimal']) !!}</td>
                                </tr>
                                <tr class="bg-gray-200 font-bold">
                                    <td class="border border-gray-400 px-4 py-3 font-bold">TOTAL</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center font-bold">
                                        {{ $statistics['sks_lulus_minimal'] }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center font-bold">
                                        {{ $statistics['total_mata_kuliah'] }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center font-bold">
                                        {{ number_format($statistics['total_sks'], 0) }}</td>
                                    <td class="border border-gray-400 px-4 py-3 text-center">{!! getStatusBadge($statistics['total_sks'], $statistics['sks_lulus_minimal']) !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="space-y-6">
                @forelse($mataKuliahKurikulum as $semesterLevel => $mataKuliahList)
                    <article class="bg-white rounded-lg shadow-sm border border-gray-200">
                        {{-- Semester Header --}}
                        <header class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Semester {{ $semesterLevel }}</h3>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $mataKuliahList->count() }} Mata Kuliah
                                    </span>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $mataKuliahList->sum('sks_mata_kuliah') }} SKS
                                    </span>
                                </div>

                                <button onclick="toggleSemester({{ $semesterLevel }})"
                                    class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 rounded-lg p-2 transition-colors duration-200">
                                    <svg class="w-5 h-5 transform transition-transform duration-200"
                                        id="chevron-{{ $semesterLevel }}" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                        </header>

                        {{-- Course Table --}}
                        <div id="semester-{{ $semesterLevel }}-content" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kode MK</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Mata Kuliah</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            SKS</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jenis</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kategori</th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($mataKuliahList as $mataKuliah)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-medium bg-gray-100 text-gray-800">
                                                    {{ $mataKuliah->kode_mata_kuliah }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-xs font-medium text-gray-900">
                                                    {{ $mataKuliah->nama_mata_kuliah }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ number_format($mataKuliah->sks_mata_kuliah, 0) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $courseTypeClasses[$mataKuliah->jenis_mata_kuliah] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $mataKuliah->jenis_mata_kuliah }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $categoryBadgeClasses[$mataKuliah->pivot->kategori_mata_kuliah] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $categoryLabels[$mataKuliah->pivot->kategori_mata_kuliah] ?? $mataKuliah->pivot->kategori_mata_kuliah }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <div class="relative group">
                                                        <button
                                                            onclick="openEditModal('{{ $mataKuliah->id_mata_kuliah }}', '{{ addslashes($mataKuliah->nama_mata_kuliah) }}', {{ $semesterLevel }}, '{{ $mataKuliah->pivot->kategori_mata_kuliah }}')"
                                                            class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                        <div
                                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                            Edit Mata Kuliah
                                                            <div
                                                                class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="relative group">
                                                        <button
                                                            onclick="confirmDelete('{{ $mataKuliah->id_mata_kuliah }}', '{{ addslashes($mataKuliah->nama_mata_kuliah) }}')"
                                                            class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                        <div
                                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                            Hapus Mata Kuliah
                                                            <div
                                                                class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-16 text-center">
                            <svg class="w-16 h-16 text-gray-400 mb-4 mx-auto" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mata Kuliah</h3>
                            <p class="text-xs text-gray-500 mb-6">Mulai tambahkan mata kuliah untuk kurikulum ini atau
                                import dari Excel</p>
                            <div class="flex items-center justify-center space-x-3">
                                <button onclick="openImportModal()"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                                        </path>
                                    </svg>
                                    Import Excel
                                </button>
                                <button onclick="openModal()"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Tambah Manual
                                </button>
                            </div>
                        </div>
                    </div>
                @endforelse
            </main>
        </div>
    </div>

    {{-- Add Mata Kuliah Modal --}}
    <div id="addModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

            <div
                class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl">
                <form id="addForm" method="POST"
                    action="{{ route('kurikulum.mata-kuliah.store', $kurikulum->id_kurikulum) }}">
                    @csrf

                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Tambah Mata Kuliah</h3>
                            <button type="button" onclick="closeModal()"
                                class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-700 mb-2">
                                    Pilih Mata Kuliah <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="mataKuliahSearch"
                                        placeholder="Cari berdasarkan kode atau nama mata kuliah..." autocomplete="off"
                                        class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">

                                    <div id="searchDropdown"
                                        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                    </div>

                                    <div id="searchLoading" class="absolute right-3 top-3 hidden">
                                        <svg class="animate-spin h-4 w-4 text-gray-400" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>

                                <div id="selectedContainer" class="mt-4 hidden">
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Mata Kuliah
                                        Terpilih:</label>
                                    <div id="selectedList" class="space-y-2"></div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">
                                    Semester <span class="text-red-500">*</span>
                                </label>
                                <select name="semester" id="semester" required
                                    class="w-full px-3 py-2 border text-xs border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Pilih Semester</option>
                                    @for ($i = 1; $i <= 14; $i++)
                                        <option value="{{ $i }}">Semester {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">
                                    Kategori Mata Kuliah <span class="text-red-500">*</span>
                                </label>
                                <select name="kategori_mata_kuliah" id="kategoriMataKuliah" required
                                    class="w-full px-3 py-2 border text-xs border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categoryLabels as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                        <button type="button" onclick="closeModal()"
                            class="w-full sm:w-auto px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Batal
                        </button>
                        <button type="submit" id="submitBtn" disabled
                            class="w-full sm:w-auto px-4 py-2 text-xs font-medium text-white bg-gray-400 border border-transparent rounded-lg cursor-not-allowed transition-colors duration-200">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeEditModal()"></div>

            <div
                class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Edit Mata Kuliah</h3>
                            <button type="button" onclick="closeEditModal()"
                                class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Mata Kuliah:</p>
                                <p id="editMataKuliahName" class="font-medium text-gray-900"></p>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Semester <span class="text-red-500">*</span>
                                    </label>
                                    <select name="semester" id="editSemester" required
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @for ($i = 1; $i <= 14; $i++)
                                            <option value="{{ $i }}">Semester {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Kategori <span class="text-red-500">*</span>
                                    </label>
                                    <select name="kategori_mata_kuliah" id="editKategori" required
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @foreach ($categoryLabels as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                        <button type="button" onclick="closeEditModal()"
                            class="w-full sm:w-auto px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Batal
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <div id="importModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeImportModal()"></div>

            <div
                class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">
                <form id="importForm" method="POST"
                    action="{{ route('kurikulum.mata-kuliah.import', $kurikulum->id_kurikulum) }}"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-heading font-semibold text-gray-900">Import Mata Kuliah</h3>
                            <button type="button" onclick="closeImportModal()"
                                class="sm:hidden text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">
                                    File Excel <span class="text-red-500">*</span>
                                </label>
                                <input type="file" name="file" id="import_file" accept=".xlsx,.xls"
                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                    required>
                                <p class="text-xs text-gray-500 mt-1">Format: .xlsx atau .xls (max 2MB)</p>
                            </div>

                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <div class="flex">
                                    <svg class="w-4 h-4 text-yellow-400 mt-0.5 mr-2 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.966-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                    <div>
                                        <p class="text-xs font-medium text-yellow-800 mb-1">Penting!</p>
                                        <ul class="text-xs text-yellow-700 space-y-1">
                                            <li>• Download template Excel terlebih dahulu</li>
                                            <li>• Isi data sesuai format yang ada di template</li>
                                            <li>• Kode mata kuliah harus sudah terdaftar di sistem</li>
                                            <li>• Data yang error akan dilewati dan dilaporkan</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                        <button type="button" onclick="closeImportModal()"
                            class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                            Batal
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Import Error Modal --}}
    @if (session('import_errors'))
        <div id="import-error-modal" class="fixed inset-0 z-[9999] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeErrorModal()"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
                    <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.966-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-heading font-semibold text-gray-900">Detail Error Import</h3>
                            </div>
                            <button type="button" onclick="closeErrorModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            <div class="space-y-2">
                                @foreach (session('import_errors') as $error)
                                    <div class="flex items-start p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-sm text-yellow-800">{{ $error }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 sm:px-6 py-4 flex justify-end">
                        <button type="button" onclick="closeErrorModal()"
                            class="inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Hidden Delete Form --}}
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let selectedMataKuliah = new Set();
                let searchTimeout;

                function toggleSemester(semesterLevel) {
                    const content = document.getElementById(`semester-${semesterLevel}-content`);
                    const chevron = document.getElementById(`chevron-${semesterLevel}`);

                    if (content.classList.contains('hidden')) {
                        content.classList.remove('hidden');
                        chevron.classList.remove('rotate-180');
                    } else {
                        content.classList.add('hidden');
                        chevron.classList.add('rotate-180');
                    }
                }

                function setupSearch() {
                    const searchInput = document.getElementById('mataKuliahSearch');
                    const dropdown = document.getElementById('searchDropdown');
                    const loading = document.getElementById('searchLoading');

                    searchInput.addEventListener('input', function() {
                        const query = this.value.trim();

                        if (searchTimeout) {
                            clearTimeout(searchTimeout);
                        }

                        if (query === '') {
                            dropdown.classList.add('hidden');
                            return;
                        }

                        loading.classList.remove('hidden');

                        searchTimeout = setTimeout(() => {
                            searchMataKuliah(query);
                            loading.classList.add('hidden');
                        }, 300);
                    });

                    searchInput.addEventListener('focus', function() {
                        const query = this.value.trim();
                        if (query !== '') {
                            searchMataKuliah(query);
                        }
                    });

                    document.addEventListener('click', function(event) {
                        if (!searchInput.contains(event.target) && !dropdown.contains(event.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                }

                function searchMataKuliah(query) {
                    const dropdown = document.getElementById('searchDropdown');

                    fetch(
                            `{{ route('kurikulum.mata-kuliah.getMataKuliah', $kurikulum->id_kurikulum) }}?search=${encodeURIComponent(query)}`
                            )
                        .then(response => response.json())
                        .then(data => {
                            displaySearchResults(data);
                        })
                        .catch(error => {
                            console.error('Error searching mata kuliah:', error);
                            dropdown.innerHTML =
                                '<div class="px-4 py-3 text-center text-xs text-red-500">Error loading data</div>';
                            dropdown.classList.remove('hidden');
                        });
                }

                function displaySearchResults(data) {
                    const dropdown = document.getElementById('searchDropdown');

                    if (data.length === 0) {
                        dropdown.innerHTML =
                            '<div class="px-4 py-3 text-center text-xs text-gray-500">Tidak ada mata kuliah ditemukan</div>';
                    } else {
                        dropdown.innerHTML = data.map(item => `
                            <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 ${selectedMataKuliah.has(item.id_mata_kuliah) ? 'bg-blue-50' : ''}" 
                                 onclick="toggleSelection('${item.id_mata_kuliah}', '${escapeHtml(item.kode_mata_kuliah)}', '${escapeHtml(item.nama_mata_kuliah)}', '${item.sks_mata_kuliah}', '${item.jenis_mata_kuliah}')">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-xs font-medium text-gray-900">${item.nama_mata_kuliah}</div>
                                        <div class="text-xs text-gray-500">${item.kode_mata_kuliah} • ${item.sks_mata_kuliah} SKS • ${item.jenis_mata_kuliah}</div>
                                    </div>
                                    <div class="ml-3">
                                        ${selectedMataKuliah.has(item.id_mata_kuliah) ? 
                                            '<svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>' : 
                                            '<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>'
                                        }
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    }

                    dropdown.classList.remove('hidden');
                }

                function toggleSelection(id, kode, nama, sks, jenis) {
                    const container = document.getElementById('selectedContainer');
                    const list = document.getElementById('selectedList');
                    const submitBtn = document.getElementById('submitBtn');

                    if (selectedMataKuliah.has(id)) {
                        selectedMataKuliah.delete(id);
                        const item = document.getElementById(`selected-${id}`);
                        if (item) item.remove();
                    } else {
                        selectedMataKuliah.add(id);
                        const selectedItem = document.createElement('div');
                        selectedItem.id = `selected-${id}`;
                        selectedItem.className =
                            'flex items-center justify-between bg-blue-50 p-3 rounded-lg border border-blue-200';
                        selectedItem.innerHTML = `
                            <div>
                                <div class="text-xs font-medium text-gray-900">${nama}</div>
                                <div class="text-xs text-gray-500">${kode} • ${sks} SKS • ${jenis}</div>
                            </div>
                            <button type="button" onclick="toggleSelection('${id}', '${kode}', '${nama}', '${sks}', '${jenis}')"
                                    class="text-red-500 hover:text-red-700 focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6m0 12L6 6"></path>
                                </svg>
                            </button>
                            <input type="hidden" name="mata_kuliah[]" value="${id}">
                        `;
                        list.appendChild(selectedItem);
                    }

                    if (selectedMataKuliah.size > 0) {
                        container.classList.remove('hidden');
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        submitBtn.classList.add('bg-gray-900', 'hover:bg-gray-700');
                    } else {
                        container.classList.add('hidden');
                        submitBtn.disabled = true;
                        submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                        submitBtn.classList.remove('bg-gray-900', 'hover:bg-gray-700');
                    }

                    const query = document.getElementById('mataKuliahSearch').value.trim();
                    if (query) {
                        searchMataKuliah(query);
                    }
                }

                function openModal() {
                    resetForm();
                    document.getElementById('addModal').classList.remove('hidden');
                    setTimeout(() => {
                        setupSearch();
                        document.getElementById('mataKuliahSearch').focus();
                    }, 100);
                }

                function closeModal() {
                    document.getElementById('addModal').classList.add('hidden');
                }

                function openEditModal(id, nama, currentSemester, currentKategori) {
                    const modal = document.getElementById('editModal');
                    const form = document.getElementById('editForm');

                    form.action =
                        `{{ route('kurikulum.mata-kuliah.update', ['kurikulum' => $kurikulum->id_kurikulum, 'mataKuliah' => '__ID__']) }}`
                        .replace('__ID__', id);
                    document.getElementById('editMataKuliahName').textContent = nama;
                    document.getElementById('editSemester').value = currentSemester;
                    document.getElementById('editKategori').value = currentKategori;

                    modal.classList.remove('hidden');
                }

                function closeEditModal() {
                    document.getElementById('editModal').classList.add('hidden');
                }

                function openImportModal() {
                    document.getElementById('importModal').classList.remove('hidden');
                }

                function closeImportModal() {
                    document.getElementById('importModal').classList.add('hidden');
                    document.getElementById('importForm').reset();
                }

                function closeErrorModal() {
                    const modal = document.getElementById('import-error-modal');
                    if (modal) modal.style.display = 'none';
                }

                function confirmDelete(id, nama) {
                    if (confirm(`Yakin ingin menghapus mata kuliah "${nama}" dari kurikulum ini?`)) {
                        const form = document.getElementById('deleteForm');
                        form.action =
                            `{{ route('kurikulum.mata-kuliah.destroy', ['kurikulum' => $kurikulum->id_kurikulum, 'mataKuliah' => '__ID__']) }}`
                            .replace('__ID__', id);
                        form.submit();
                    }
                }

                function resetForm() {
                    selectedMataKuliah.clear();
                    document.getElementById('selectedContainer').classList.add('hidden');
                    document.getElementById('selectedList').innerHTML = '';
                    document.getElementById('mataKuliahSearch').value = '';
                    document.getElementById('semester').value = '';
                    document.getElementById('kategoriMataKuliah').value = '';

                    const submitBtn = document.getElementById('submitBtn');
                    submitBtn.disabled = true;
                    submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    submitBtn.classList.remove('bg-gray-900', 'hover:bg-gray-700');
                }

                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                window.toggleSemester = toggleSemester;
                window.openModal = openModal;
                window.closeModal = closeModal;
                window.openEditModal = openEditModal;
                window.closeEditModal = closeEditModal;
                window.openImportModal = openImportModal;
                window.closeImportModal = closeImportModal;
                window.closeErrorModal = closeErrorModal;
                window.confirmDelete = confirmDelete;
                window.toggleSelection = toggleSelection;

                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeModal();
                        closeEditModal();
                        closeImportModal();
                        closeErrorModal();
                    }
                });

                const errorModal = document.getElementById('import-error-modal');
                if (errorModal) errorModal.style.display = 'block';
            });
        </script>
    @endpush
@endsection
