@extends('layouts.app')

@section('title', 'Mata Kuliah Kurikulum')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="w-full">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Mata Kuliah Kurikulum</h1>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-1 sm:space-y-0">
                                <p class="text-sm text-gray-600">{{ $kurikulum->nama_kurikulum }}</p>
                                <span class="text-xs text-gray-400">•</span>
                                <p class="text-sm text-gray-600">{{ $kurikulum->programStudi->nama_program_studi }}
                                    ({{ $kurikulum->programStudi->jenjang->kode_jenjang_pendidikan }})</p>
                            </div>
                        </div>

                        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                            <!-- Tombol Back -->
                            <a href="{{ route('kurikulum.index') }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-900 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                <i class="fas fa-arrow-left w-3 h-3 mr-2"></i>
                                Kembali
                            </a>

                            <!-- Tombol Tambah -->
                            <button onclick="openModal('create')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                <i class="fas fa-plus w-3 h-3 mr-2"></i>
                                Tambah Mata Kuliah
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-slate-900">Total Mata Kuliah</p>
                                    <p class="text-base font-semibold text-slate-600">{{ $statistics['total_mata_kuliah'] }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-slate-900">Jumlah SKS Lulus</p>
                                    <p class="text-base font-semibold text-slate-600">{{ $kurikulum->jumlah_sks_lulus }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-slate-900">Jumlah SKS Wajib</p>
                                    <p class="text-base font-semibold text-slate-600">{{ $kurikulum->jumlah_sks_wajib }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-slate-900">Jumlah SKS Pilihan</p>
                                    <p class="text-base font-semibold text-slate-600">
                                        {{ $kurikulum->jumlah_sks_pilihan }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mata Kuliah per Semester -->
            <div class="space-y-6">
                @forelse($mataKuliahKurikulum as $semesterLevel => $mataKuliahList)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-base font-semibold text-gray-900">Semester {{ $semesterLevel }}</h3>
                                    {{-- <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $mataKuliahList->count() }} Mata Kuliah
                                    </span>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $mataKuliahList->sum('sks_mata_kuliah') }} SKS
                                    </span> --}}
                                </div>
                                <button onclick="toggleSemester({{ $semesterLevel }})"
                                    class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5 transform transition-transform duration-200"
                                        id="chevron-{{ $semesterLevel }}" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div id="semester-{{ $semesterLevel }}-content" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                            Kode Mata Kuliah
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                            Nama Mata Kuliah
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                            Jenis
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                            SKS
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($mataKuliahList as $mataKuliah)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-medium bg-gray-100 text-gray-800">
                                                    {{ $mataKuliah->kode_mata_kuliah }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-xs font-medium text-gray-900">
                                                    {{ $mataKuliah->nama_mata_kuliah }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($mataKuliah->pivot->jenis_mata_kuliah == 'WAJIB')
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
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $mataKuliah->sks_mata_kuliah }} SKS
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <!-- Tombol Pindah Semester -->
                                                    <div class="relative group">
                                                        <button
                                                            onclick="openEditSemesterModal('{{ $mataKuliah->id_mata_kuliah }}', '{{ addslashes($mataKuliah->nama_mata_kuliah) }}', {{ $semesterLevel }}, '{{ $mataKuliah->pivot->jenis_mata_kuliah }}')"
                                                            class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                        <div
                                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                            Pindah Semester
                                                            <div
                                                                class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Tombol Hapus -->
                                                    <div class="relative group">
                                                        <button
                                                            onclick="confirmRemove('{{ $mataKuliah->id_mata_kuliah }}', '{{ addslashes($mataKuliah->nama_mata_kuliah) }}')"
                                                            class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                            <i class="fas fa-trash text-xs"></i>
                                                        </button>
                                                        <div
                                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                            Hapus dari Kurikulum
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
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 text-gray-400 mb-4 mx-auto" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                            <p class="text-lg font-medium text-gray-900 mb-2">Belum ada mata kuliah</p>
                            <p class="text-xs text-gray-500 mb-4">Tambahkan mata kuliah untuk kurikulum ini</p>
                            <button onclick="openModal('create')"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Mata Kuliah
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Add Mata Kuliah Modal -->
        <div id="mataKuliahModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl mx-auto">
                    <form id="mataKuliahForm" method="POST"
                        action="{{ route('kurikulum.mata-kuliah.store', $kurikulum->id_kurikulum) }}">
                        @csrf

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 id="modalTitle" class="text-lg font-heading font-semibold text-gray-900">
                                    Tambah Mata Kuliah
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Mata Kuliah <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="mata_kuliah_search"
                                            placeholder="Cari berdasarkan kode atau nama mata kuliah..."
                                            autocomplete="off"
                                            class="w-full text-xs px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">

                                        <!-- Dropdown hasil pencarian -->
                                        <div id="mata_kuliah_dropdown"
                                            class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                            <!-- Results akan dimuat di sini -->
                                        </div>

                                        <!-- Loading indicator -->
                                        <div id="search_loading" class="absolute right-2 top-2 hidden">
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

                                    <!-- Selected mata kuliah container -->
                                    <div id="selected_mata_kuliah" class="mt-3 space-y-2 hidden">
                                        <label class="block text-xs font-medium text-gray-700">Mata Kuliah
                                            Terpilih:</label>
                                        <div id="selected_mata_kuliah_list" class="space-y-2">
                                            <!-- Selected items will be added here -->
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Semester <span class="text-red-500">*</span>
                                    </label>
                                    <select name="semester" id="semester"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        required>
                                        <option value="">Pilih Semester</option>
                                        @for ($i = 1; $i <= 14; $i++)
                                            <option value="{{ $i }}">Semester {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Jenis Mata Kuliah <span class="text-red-500">*</span>
                                    </label>
                                    <select name="jenis_mata_kuliah" id="jenis_mata_kuliah"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="WAJIB">Mata Kuliah Wajib</option>
                                        <option value="PILIHAN">Mata Kuliah Pilihan</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="mt-4 bg-blue-50 p-3 rounded-lg">
                                <div class="flex">
                                    <svg class="w-4 h-4 text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="text-xs text-blue-800">
                                        <p class="font-medium mb-1">Panduan:</p>
                                        <ul class="text-xs text-blue-700 space-y-1">
                                            <li>• Klik pada mata kuliah untuk memilih (bisa pilih multiple)</li>
                                            <li>• Pilih semester untuk semua mata kuliah yang dipilih</li>
                                            <li>• Mata kuliah yang sudah ada dalam kurikulum tidak akan muncul</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                            <button type="button" onclick="closeModal()"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                Batal
                            </button>
                            <button type <button type="submit" id="submitBtn" disabled
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-400 border border-transparent rounded-lg cursor-not-allowed transition-colors duration-200">
                                <span id="submitText">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Semester Modal -->
        <div id="editSemesterModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    onclick="closeEditSemesterModal()"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md mx-auto">
                    <form id="editSemesterForm" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-heading font-semibold text-gray-900">
                                    Pindah Semester
                                </h3>
                                <button type="button" onclick="closeEditSemesterModal()"
                                    class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">
                                    Mata Kuliah: <span id="editMataKuliahName" class="font-medium text-gray-900"></span>
                                </p>
                                <p class="text-xs text-gray-500 mb-2">
                                    Semester saat ini: <span id="currentSemester" class="font-medium"></span>
                                </p>
                                <p class="text-xs text-gray-500 mb-2">
                                    Jenis MK saat ini: <span id="currentJenis" class="font-medium"></span>
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-700 mb-2">
                                    Semester Baru <span class="text-red-500">*</span>
                                </label>
                                <select name="semester" id="newSemester"
                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                    required>
                                    <option value="">Pilih Semester</option>
                                    @for ($i = 1; $i <= 14; $i++)
                                        <option value="{{ $i }}">Semester {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">
                                    Jenis Mata Kuliah <span class="text-red-500">*</span>
                                </label>
                                <select name="jenis_mata_kuliah" id="newJenisMataKuliah"
                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                    required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="WAJIB">Mata Kuliah Wajib</option>
                                    <option value="PILIHAN">Mata Kuliah Pilihan</option>
                                </select>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                            <button type="button" onclick="closeEditSemesterModal()"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800">
                                Pindah
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Copy Kurikulum Modal -->
        <div id="copyModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeCopyModal()"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md mx-auto">
                    <form id="copyForm" method="POST"
                        action="{{ route('kurikulum.mata-kuliah.copy', $kurikulum->id_kurikulum) }}">
                        @csrf

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-heading font-semibold text-gray-900">
                                    Copy dari Kurikulum Lain
                                </h3>
                                <button type="button" onclick="closeCopyModal()"
                                    class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-700 mb-2">
                                    Kurikulum Sumber <span class="text-red-500">*</span>
                                </label>
                                <select name="source_kurikulum_id" id="sourceKurikulum"
                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                    required>
                                    <option value="">Pilih Kurikulum</option>
                                    <!-- Options akan dimuat via AJAX -->
                                </select>
                            </div>

                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <div class="flex">
                                    <svg class="w-4 h-4 text-yellow-400 mr-2 mt-0.5 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                    <div class="text-xs text-yellow-800">
                                        <p class="font-medium mb-1">Perhatian:</p>
                                        <p>Mata kuliah yang sudah ada dalam kurikulum ini tidak akan ditimpa.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                            <button type="button" onclick="closeCopyModal()"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800">
                                Copy Mata Kuliah
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms untuk Actions -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            // Global variables
            let selectedMataKuliah = new Set();
            let searchTimeout;

            // Toggle semester visibility
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

            // ===============================================
            // MATA KULIAH SEARCH FUNCTIONALITY
            // ===============================================

            function setupMataKuliahSearch() {
                const searchInput = document.getElementById('mata_kuliah_search');
                const dropdown = document.getElementById('mata_kuliah_dropdown');
                const loadingIndicator = document.getElementById('search_loading');

                if (!searchInput || !dropdown || !loadingIndicator) {
                    console.error('Mata kuliah search elements not found');
                    return;
                }

                // Remove existing event listeners
                const newSearchInput = searchInput.cloneNode(true);
                searchInput.parentNode.replaceChild(newSearchInput, searchInput);

                // Event listener untuk input search
                newSearchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();

                    if (searchTimeout) {
                        clearTimeout(searchTimeout);
                    }

                    if (query === '') {
                        dropdown.classList.add('hidden');
                        return;
                    }

                    loadingIndicator.classList.remove('hidden');

                    searchTimeout = setTimeout(() => {
                        searchMataKuliah(query);
                        loadingIndicator.classList.add('hidden');
                    }, 300);
                });

                // Event listener untuk focus
                newSearchInput.addEventListener('focus', function() {
                    const query = this.value.toLowerCase().trim();
                    if (query !== '') {
                        searchMataKuliah(query);
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!newSearchInput.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }

            function searchMataKuliah(query) {
                const dropdown = document.getElementById('mata_kuliah_dropdown');

                fetch(
                        `{{ route('kurikulum.mata-kuliah.api.mata-kuliah', $kurikulum->id_kurikulum) }}?search=${encodeURIComponent(query)}`
                    )
                    .then(response => response.json())
                    .then(data => {
                        displayMataKuliahResults(data);
                    })
                    .catch(error => {
                        console.error('Error searching mata kuliah:', error);
                        dropdown.innerHTML = `
                            <div class="px-4 py-3 text-center text-xs text-red-500">
                                Error loading data
                            </div>
                        `;
                        dropdown.classList.remove('hidden');
                    });
            }

            function displayMataKuliahResults(data) {
                const dropdown = document.getElementById('mata_kuliah_dropdown');

                if (data.length === 0) {
                    dropdown.innerHTML = `
                        <div class="px-4 py-3 text-center text-xs text-gray-500">
                            <i class="fas fa-search mb-2"></i>
                            <div>Tidak ada mata kuliah tersedia</div>
                        </div>
                    `;
                } else {
                    dropdown.innerHTML = data.map(item => `
                        <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 ${selectedMataKuliah.has(item.id_mata_kuliah) ? 'bg-blue-50' : ''}" 
                             onclick="toggleMataKuliahSelection('${item.id_mata_kuliah}', '${escapeHtml(item.kode_mata_kuliah)}', '${escapeHtml(item.nama_mata_kuliah)}', '${item.sks_mata_kuliah}')">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-medium text-gray-900">${item.nama_mata_kuliah}</div>
                                    <div class="text-xs text-gray-500">
                                        ${item.kode_mata_kuliah} • ${item.sks_mata_kuliah} SKS
                                    </div>
                                </div>
                                <div class="ml-3">
                                    ${selectedMataKuliah.has(item.id_mata_kuliah) ? 
                                        '<svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>' : 
                                        '<svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>'
                                    }
                                </div>
                            </div>
                        </div>
                    `).join('');
                }

                dropdown.classList.remove('hidden');
            }

            function toggleMataKuliahSelection(id, kode, nama, sks) {
                const selectedContainer = document.getElementById('selected_mata_kuliah');
                const selectedList = document.getElementById('selected_mata_kuliah_list');
                const submitBtn = document.getElementById('submitBtn');

                if (selectedMataKuliah.has(id)) {
                    // Remove from selection
                    selectedMataKuliah.delete(id);
                    const item = document.getElementById(`selected-${id}`);
                    if (item) item.remove();
                } else {
                    // Add to selection
                    selectedMataKuliah.add(id);
                    const selectedItem = document.createElement('div');
                    selectedItem.id = `selected-${id}`;
                    selectedItem.className = 'flex items-center justify-between bg-blue-50 p-2 rounded border';
                    selectedItem.innerHTML = `
                        <div>
                            <div class="text-xs font-medium text-gray-900">${nama}</div>
                            <div class="text-xs text-gray-500">${kode} • ${sks} SKS</div>
                        </div>
                        <button type="button" onclick="toggleMataKuliahSelection('${id}', '${kode}', '${nama}', '${sks}')"
                            class="text-red-500 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6m0 12L6 6"></path>
                            </svg>
                        </button>
                        <input type="hidden" name="mata_kuliah[]" value="${id}">
                    `;
                    selectedList.appendChild(selectedItem);
                }

                // Update visibility and button state
                if (selectedMataKuliah.size > 0) {
                    selectedContainer.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    submitBtn.classList.add('bg-gray-900', 'hover:bg-gray-800');
                } else {
                    selectedContainer.classList.add('hidden');
                    submitBtn.disabled = true;
                    submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    submitBtn.classList.remove('bg-gray-900', 'hover:bg-gray-800');
                }

                // Update dropdown display
                const query = document.getElementById('mata_kuliah_search').value.toLowerCase().trim();
                if (query) {
                    searchMataKuliah(query);
                }
            }

            // ===============================================
            // MODAL FUNCTIONS
            // ===============================================

            function openModal(type) {
                const modal = document.getElementById('mataKuliahModal');
                modal.classList.remove('hidden');

                // Clear selections
                selectedMataKuliah.clear();
                document.getElementById('selected_mata_kuliah').classList.add('hidden');
                document.getElementById('selected_mata_kuliah_list').innerHTML = '';
                document.getElementById('mata_kuliah_search').value = '';
                document.getElementById('semester').value = '';

                // Reset submit button
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                submitBtn.classList.remove('bg-gray-900', 'hover:bg-gray-800');

                setTimeout(() => {
                    setupMataKuliahSearch();
                    document.getElementById('mata_kuliah_search').focus();
                }, 100);
            }

            function closeModal() {
                document.getElementById('mataKuliahModal').classList.add('hidden');
            }

            function openEditSemesterModal(mataKuliahId, mataKuliahName, currentSemester, currentJenis) {
                const modal = document.getElementById('editSemesterModal');
                const form = document.getElementById('editSemesterForm');

                form.action =
                    `{{ route('kurikulum.mata-kuliah.update', ['kurikulum' => $kurikulum->id_kurikulum, 'mataKuliah' => '__MATA_KULIAH_ID__']) }}`
                    .replace('__MATA_KULIAH_ID__', mataKuliahId);

                document.getElementById('editMataKuliahName').textContent = mataKuliahName;
                document.getElementById('currentSemester').textContent = currentSemester;
                document.getElementById('currentJenis').textContent = currentJenis === 'WAJIB' ? 'Wajib' : 'Pilihan';
                document.getElementById('newSemester').value = currentSemester;
                document.getElementById('newJenisMataKuliah').value = currentJenis;

                modal.classList.remove('hidden');
            }

            function closeEditSemesterModal() {
                document.getElementById('editSemesterModal').classList.add('hidden');
            }

            function openCopyModal() {
                const modal = document.getElementById('copyModal');
                modal.classList.remove('hidden');

                // Load kurikulum list
                loadKurikulumList();
            }

            function closeCopyModal() {
                document.getElementById('copyModal').classList.add('hidden');
            }

            function loadKurikulumList() {
                // This would need an API endpoint to get available kurikulum
                // For now, we'll just show a placeholder
                const select = document.getElementById('sourceKurikulum');
                select.innerHTML = '<option value="">Loading...</option>';

                // You would implement this API endpoint
                // fetch('/api/kurikulum')
                //     .then(response => response.json())
                //     .then(data => {
                //         // Populate select options
                //     });
            }

            function confirmRemove(mataKuliahId, mataKuliahName) {
                if (confirm(`Yakin ingin menghapus mata kuliah "${mataKuliahName}" dari kurikulum ini?`)) {
                    const form = document.getElementById('delete-form');
                    form.action =
                        `{{ route('kurikulum.mata-kuliah.destroy', ['kurikulum' => $kurikulum->id_kurikulum, 'mataKuliah' => '__MATA_KULIAH_ID__']) }}`
                        .replace('__MATA_KULIAH_ID__', mataKuliahId);
                    form.submit();
                }
            }

            // Helper function
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeModal();
                    closeEditSemesterModal();
                    closeCopyModal();
                }
            });

            // Make functions globally accessible
            window.toggleSemester = toggleSemester;
            window.openModal = openModal;
            window.closeModal = closeModal;
            window.openEditSemesterModal = openEditSemesterModal;
            window.closeEditSemesterModal = closeEditSemesterModal;
            window.openCopyModal = openCopyModal;
            window.closeCopyModal = closeCopyModal;
            window.confirmRemove = confirmRemove;
            window.toggleMataKuliahSelection = toggleMataKuliahSelection;
        </script>
    @endpush
@endsection
