@extends('layouts.app')

@section('title', 'Laporan ' . $jenis_bimbingan)

@section('content')
    <!-- Alert Messages -->
    @if (session('success'))
        <div class="mb-3 bg-green-50 border-l-4 border-green-500 px-6 py-4 rounded-r-lg" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                <span class="text-xs text-green-800 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-3 bg-red-50 border-l-4 border-red-500 px-6 py-4 rounded-r-lg" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 text-lg"></i>
                <span class="text-xs text-red-800 font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-3 bg-red-50 border-l-4 border-red-500 px-6 py-4 rounded-r-lg" role="alert">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3 text-lg mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-xs font-semibold text-red-800 mb-2">Terdapat kesalahan dalam pengisian form:</p>
                    <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Page Header -->
            <div class="mb-6 bg-white rounded-lg shadow-sm px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-lg font-heading font-semibold text-gray-900 mb-1">Sistem Pelaporan
                            {{ $jenis_bimbingan }}</h1>
                        <p class="text-xs text-gray-600">Kelola dan pantau progres laporan bimbingan Anda</p>
                    </div>
                    <button onclick="history.back()"
                        class="inline-flex items-center px-5 py-2.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 shadow-sm transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </button>
                </div>
            </div>

            <!-- Course Information Card -->
            <div class="w-full  mb-6">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-5">
                        <h3 class="text-xs font-bold text-gray-900 mb-4 uppercase tracking-wide">Informasi
                            Mata Kuliah
                        </h3>
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Kode
                                    Mata Kuliah:</p>
                                <p class="text-xs font-semibold text-gray-900">
                                    {{ $mata_kuliah->kode_mata_kuliah }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nama Mata Kuliah:
                                </p>
                                <p class="text-xs font-semibold text-gray-900">
                                    {{ $mata_kuliah->nama_mata_kuliah }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">SKS
                                </p>
                                <p class="text-xs font-semibold text-gray-900">
                                    {{ $mata_kuliah->sks_mata_kuliah }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Bimbingan -->
                    @if ($detail_bimbingan)
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-5">
                            @if ($jenis_bimbingan === 'KKN')
                                <h3 class="text-xs font-bold text-gray-900 mb-4 uppercase tracking-wide">Informasi
                                    Kelompok
                                </h3>
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nama
                                            Kelompok:</p>
                                        <p class="text-xs font-semibold text-gray-900">
                                            {{ $detail_bimbingan['kelompok']->nama_kelompok ?? 'Belum ditentukan' }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Lokasi:</p>
                                        <p class="text-xs font-semibold text-gray-900">
                                            {{ $detail_bimbingan['kelompok']->lokasi ?? 'Belum ditentukan' }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Peran Anda
                                        </p>
                                        <p class="text-xs font-semibold text-gray-900">
                                            {{ $detail_bimbingan['peran'] }}
                                        </p>
                                    </div>
                                </div>
                            @elseif($jenis_bimbingan === 'MAGANG')
                                <h3 class="text-xs font-bold text-gray-900 mb-4 uppercase tracking-wide">Informasi
                                    Magang
                                </h3>
                                <div class="flex flex-col md:flex-row gap-6">
                                    <div class="flex-1">
                                        <p class="text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Tempat
                                            Magang</p>
                                        <p class="text-xs font-semibold text-gray-900">
                                            {{ $detail_bimbingan->tempat_magang ?? 'Belum ditentukan' }}
                                        </p>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Bidang
                                        </p>
                                        <p class="text-xs font-semibold text-gray-900">
                                            {{ $detail_bimbingan->bidang_magang ?? 'Belum ditentukan' }}
                                        </p>
                                    </div>
                                </div>
                            @elseif($jenis_bimbingan === 'SKRIPSI')
                                <h3 class="text-xs font-bold text-gray-900 mb-4 uppercase tracking-wide">Informasi
                                    Skripsi
                                </h3>
                                <div class="flex flex-col gap-4">
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Judul
                                            Penelitian</p>
                                        <p class="text-xs font-semibold text-gray-900 leading-relaxed">
                                            {{ $detail_bimbingan->judul ?? 'Belum ditentukan' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Bidang
                                            Penelitian</p>
                                        <p class="text-xs font-semibold text-gray-900">
                                            {{ $detail_bimbingan->bidang_penelitian ?? 'Belum ditentukan' }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-5">
                        <h3 class="text-xs font-bold text-gray-900 mb-4 uppercase tracking-wide">Tim Pembimbing
                        </h3>
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pembimbing Utama:
                                </p>
                                <p class="text-xs font-semibold text-gray-900">
                                    {{ $pembimbing_utama->pengguna->nama ?? 'Belum ditentukan' }}
                                </p>
                            </div>
                            @if ($pembimbing_kedua)
                                <div class="flex items-center gap-2">
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pembimbing
                                        Kedua:
                                    </p>
                                    <p class="text-xs font-semibold text-gray-900">
                                        {{ $pembimbing_kedua->pengguna->nama }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Structure Table -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                        <div class="flex-1">
                            <h2 class="text-base sm:text-lg font-semibold font-heading text-gray-900 mb-1">Struktur Laporan
                            </h2>
                            <p class="text-xs text-gray-600">Pantau dan kelola submission laporan bimbingan Anda</p>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-3">
                            <label class="text-xs font-medium text-gray-700 shrink-0 hidden sm:inline">Filter
                                Status:</label>
                            <label class="text-xs font-medium text-gray-700 shrink-0 sm:hidden">Filter:</label>
                            <select id="statusFilter" onchange="filterByStatus()"
                                class="text-xs py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent bg-white font-medium w-full sm:w-auto min-w-[140px]">
                                <option value="">Semua Status</option>
                                <option value="DRAFT">Draft</option>
                                <option value="SUBMITTED">Submitted</option>
                                <option value="APPROVED">Approved</option>
                                <option value="NEEDS_REVISION">Needs Revision</option>
                            </select>
                        </div>
                    </div>
                </div>

                @if ($babs->isEmpty())
                    <div class="px-8 py-20 text-center">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-book-open text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Struktur Laporan</h3>
                        <p class="text-xs text-gray-600">Pembimbing belum menambahkan struktur laporan untuk
                            {{ $jenis_bimbingan }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-3 sm:px-6 lg:px-8 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                                Struktur Laporan
                                            </th>
                                            <th scope="col"
                                                class="px-3 sm:px-6 lg:px-8 py-3 sm:py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">
                                                Status
                                            </th>
                                            <th scope="col"
                                                class="px-3 sm:px-6 lg:px-8 py-3 sm:py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                                Submission
                                            </th>
                                            <th scope="col"
                                                class="px-3 sm:px-6 lg:px-8 py-3 sm:py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($babs as $bab)
                                            <tr class="hover:bg-gray-50 transition-colors duration-150 bab-row"
                                                data-status="{{ $bab['status'] }}">
                                                <!-- Report Structure -->
                                                <td class="px-3 sm:px-6 lg:px-8 py-4 sm:py-6">
                                                    <div class="space-y-3 sm:space-y-4 min-w-[280px] sm:min-w-0">
                                                        <h4 class="text-sm sm:text-base font-bold text-gray-900">
                                                            {{ $bab['judul_bab'] }}</h4>

                                                        <!-- Guidelines (Collapsible for Mobile) -->
                                                        @if ($bab['konten'])
                                                            <div
                                                                class="bg-amber-50 border-l-4 border-amber-400 rounded-r-lg">
                                                                <div class="p-3 sm:p-4">
                                                                    <button onclick="toggleGuideline(this)"
                                                                        class="flex items-start justify-between w-full text-left group">
                                                                        <div class="flex items-start flex-1">
                                                                            <i
                                                                                class="fas fa-lightbulb text-amber-600 mr-2 sm:mr-3 mt-0.5 text-sm sm:text-base"></i>
                                                                            <div class="flex-1 min-w-0">
                                                                                <p
                                                                                    class="text-xs font-bold text-amber-900 mb-1 uppercase tracking-wide">
                                                                                    Pedoman Pembimbing
                                                                                </p>
                                                                                <p class="text-xs text-amber-700">
                                                                                    Tap untuk lihat detail
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <i
                                                                            class="fas fa-chevron-down text-amber-600 ml-2 transition-transform duration-200 mt-1"></i>
                                                                    </button>
                                                                    <div class="guideline-content hidden mt-2">
                                                                        <div
                                                                            class="text-xs text-amber-800 prose prose-sm max-w-none">
                                                                            {!! $bab['konten'] !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <!-- Template File -->
                                                        @if ($bab['file_template'])
                                                            <div
                                                                class="bg-green-50 border-l-4 border-green-400 p-3 sm:p-4 rounded-r-lg">
                                                                <div
                                                                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                                                    <div class="flex items-start sm:items-center">
                                                                        <i
                                                                            class="fas fa-file-download text-green-600 mr-2 sm:mr-3 mt-0.5 sm:mt-0 text-sm sm:text-base"></i>
                                                                        <div class="min-w-0">
                                                                            <p
                                                                                class="text-xs font-bold text-green-900 uppercase tracking-wide">
                                                                                Template Tersedia
                                                                            </p>
                                                                            <p
                                                                                class="text-xs text-green-700 mt-0.5 hidden sm:block">
                                                                                Download template untuk panduan
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <a href="{{ asset('storage/' . $bab['file_template']) }}"
                                                                        target="_blank"
                                                                        class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 whitespace-nowrap">
                                                                        <i class="fas fa-download mr-2"></i>
                                                                        <span class="hidden sm:inline">Download</span>
                                                                        <span class="sm:hidden">Unduh Template</span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <!-- Feedback -->
                                                        @if ($bab['catatan_pembimbing'])
                                                            <div class="bg-red-50 border-l-4 border-red-400 rounded-r-lg">
                                                                <div class="p-3 sm:p-4">
                                                                    <button onclick="toggleFeedback(this)"
                                                                        class="flex items-start justify-between w-full text-left group sm:pointer-events-none">
                                                                        <div class="flex items-start flex-1 min-w-0">
                                                                            <i
                                                                                class="fas fa-comment-dots text-red-600 mr-2 sm:mr-3 mt-0.5 text-sm sm:text-base shrink-0"></i>
                                                                            <div class="flex-1 min-w-0">
                                                                                <p
                                                                                    class="text-xs font-bold text-red-900 mb-1 uppercase tracking-wide">
                                                                                    Feedback Pembimbing
                                                                                </p>
                                                                                <p class="text-xs text-red-700 sm:hidden">
                                                                                    Tap untuk lihat
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <i
                                                                            class="fas fa-chevron-down text-red-600 ml-2 transition-transform duration-200 mt-1 sm:hidden shrink-0"></i>
                                                                    </button>
                                                                    <div class="feedback-content hidden sm:block mt-2">
                                                                        <p
                                                                            class="text-xs text-red-800 whitespace-pre-line">
                                                                            {{ $bab['catatan_pembimbing'] }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>

                                                <!-- Status -->
                                                <td class="px-3 sm:px-6 lg:px-8 py-4 sm:py-6 text-center">
                                                    <span
                                                        class="inline-flex items-center px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs font-bold whitespace-nowrap
                                                    {{ $bab['status'] === 'APPROVED'
                                                        ? 'bg-green-100 text-green-800'
                                                        : ($bab['status'] === 'SUBMITTED'
                                                            ? 'bg-amber-100 text-amber-800'
                                                            : ($bab['status'] === 'NEEDS_REVISION'
                                                                ? 'bg-red-100 text-red-800'
                                                                : 'bg-gray-100 text-gray-700')) }}">
                                                        <i
                                                            class="fas fa-{{ $bab['status'] === 'APPROVED'
                                                                ? 'check-circle'
                                                                : ($bab['status'] === 'SUBMITTED'
                                                                    ? 'clock'
                                                                    : ($bab['status'] === 'NEEDS_REVISION'
                                                                        ? 'exclamation-circle'
                                                                        : 'file')) }} mr-1 sm:mr-2"></i>
                                                        <span class="inline">{{ $bab['status_formatted'] }}</span>
                                                    </span>
                                                </td>

                                                <!-- Student Submission -->
                                                <td class="px-3 sm:px-6 lg:px-8 py-4 sm:py-6">
                                                    @if ($bab['latest_submission'])
                                                        <div class="text-center">
                                                            <button
                                                                onclick="viewSubmissions('{{ $bab['id_laporan_bab'] }}')"
                                                                class="text-xs text-blue-500 hover:text-blue-700 underline font-medium hover:underline px-2">
                                                                <span class="inline">Lihat Riwayat Pengumpulan
                                                                    ({{ $bab['submission_count'] }})
                                                                </span>
                                                            </button>
                                                        </div>
                                                    @else
                                                        <div class="text-center py-4 sm:py-8">
                                                            <i
                                                                class="fas fa-inbox text-gray-300 text-2xl sm:text-3xl mb-2 sm:mb-3"></i>
                                                            <p class="text-xs text-gray-500 font-medium">
                                                                <span class="hidden sm:inline">Belum Ada Submission</span>
                                                                <span class="sm:hidden">Belum Ada</span>
                                                            </p>
                                                        </div>
                                                    @endif
                                                </td>

                                                <!-- Actions -->
                                                <td class="px-3 sm:px-6 lg:px-8 py-4 sm:py-6">
                                                    <div
                                                        class="flex flex-col sm:flex-row items-center justify-center gap-2">
                                                        @if ($bab['can_submit'])
                                                            <button
                                                                onclick="openSubmissionModal('{{ $bab['id_laporan_bab'] }}', '{{ addslashes($bab['judul_bab']) }}')"
                                                                class="inline-flex items-center justify-center w-full sm:w-auto px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-900 text-white text-xs font-bold rounded-lg hover:bg-gray-800 transition-all duration-200 shadow-sm whitespace-nowrap">
                                                                <i class="fas fa-upload mr-2"></i>
                                                                <span class="hidden sm:inline">Submit Laporan</span>
                                                                <span class="sm:hidden">Submit</span>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Submit Modal -->
            <div id="submissionModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                        onclick="closeSubmissionModal()"></div>

                    <div class="relative bg-white rounded-lg shadow-2xl transform transition-all w-full max-w-3xl">
                        <form id="submissionForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="bg-gray-50 px-8 py-6 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">Submit Laporan</h3>
                                        <p class="text-xs text-gray-600 mt-1">Bagian: <span id="submissionBabTitle"
                                                class="font-semibold text-gray-900"></span></p>
                                    </div>
                                    <button type="button" onclick="closeSubmissionModal()"
                                        class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="px-8 py-6 space-y-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-900 mb-3 uppercase tracking-wide">Pilih
                                        Tipe Submission</label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label
                                            class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-900 hover:bg-gray-50 transition-all duration-200">
                                            <input type="radio" name="submission_type" value="file"
                                                class="w-4 h-4 text-gray-900 focus:ring-gray-900"
                                                onchange="toggleSubmissionType()">
                                            <div class="ml-3">
                                                <p class="text-xs font-bold text-gray-900">Upload File</p>
                                                <p class="text-xs text-gray-600">PDF, DOC, DOCX</p>
                                            </div>
                                        </label>
                                        <label
                                            class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-900 hover:bg-gray-50 transition-all duration-200">
                                            <input type="radio" name="submission_type" value="text"
                                                class="w-4 h-4 text-gray-900 focus:ring-gray-900"
                                                onchange="toggleSubmissionType()">
                                            <div class="ml-3">
                                                <p class="text-xs font-bold text-gray-900">Text Editor</p>
                                                <p class="text-xs text-gray-600">Tulis langsung</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- File Upload -->
                                <div id="fileUploadSection" class="hidden">
                                    <label
                                        class="block text-xs font-bold text-gray-900 mb-3 uppercase tracking-wide">Upload
                                        File Laporan</label>

                                    <label for="file_submission" id="fileUploadLabel"
                                        class="relative border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-gray-900 transition-all duration-200 cursor-pointer block">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                        <p class="text-xs font-semibold text-gray-700 mb-1">Klik untuk memilih file</p>
                                        <p class="text-xs text-gray-600">Format: PDF, DOC, DOCX (Maksimal 10MB)</p>
                                    </label>

                                    <input type="file" name="file_submission" id="file_submission"
                                        accept=".pdf,.doc,.docx" class="hidden" onchange="handleFileSelect(event)">

                                    <!-- File Preview - akan muncul setelah file dipilih -->
                                    <div id="filePreview"
                                        class="hidden mt-4 p-4 bg-gray-50 border-2 border-gray-200 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-file-alt text-white text-lg"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-gray-900" id="fileName">-</p>
                                                    <p class="text-xs text-gray-600" id="fileSize">-</p>
                                                </div>
                                            </div>
                                            <button type="button" onclick="removeFile()"
                                                class="text-red-600 hover:text-red-800 transition-colors"
                                                title="Hapus file">
                                                <i class="fas fa-times-circle text-xl"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Text Editor -->
                                <div id="textEditorSection" class="hidden">
                                    <label
                                        class="block text-xs font-bold text-gray-900 mb-3 uppercase tracking-wide">Konten
                                        Laporan</label>
                                    <textarea name="text_submission" id="text_submission" rows="10"
                                        class="w-full text-xs px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Tulis konten laporan Anda di sini..."></textarea>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex justify-end gap-3">
                                <button type="button" onclick="closeSubmissionModal()"
                                    class="px-6 py-2.5 text-xs font-bold text-gray-700 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-6 py-2.5 text-xs font-bold text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-all duration-200 shadow-sm">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Submit Laporan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- View Submissions Modal -->
            <div id="submissionsModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                        onclick="closeSubmissionsModal()"></div>

                    <div class="relative bg-white rounded-lg shadow-2xl transform transition-all w-full max-w-5xl">
                        <div class="bg-gray-50 px-8 py-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Riwayat Submission</h3>
                                    <p class="text-xs text-gray-600 mt-1">Bagian: <span id="submissionsModalTitle"
                                            class="font-semibold text-gray-900"></span></p>
                                </div>
                                <button type="button" onclick="closeSubmissionsModal()"
                                    class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                        </div>

                        <div id="submissionsContent" class="px-8 py-6 max-h-[600px] overflow-y-auto">
                            <!-- Dynamic content -->
                        </div>

                        <div class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex justify-end">
                            <button type="button" onclick="closeSubmissionsModal()"
                                class="px-6 py-2.5 text-xs font-bold text-gray-700 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .ck-editor {
                border: 2px solid #e5e7eb !important;
                border-radius: 0.5rem !important;
            }

            .ck-toolbar {
                border: 2px solid #e5e7eb !important;
                border-bottom: none !important;
                border-radius: 0.5rem 0.5rem 0 0 !important;
                background: #f9fafb !important;
                padding: 12px !important;
            }

            .ck-editor__editable {
                min-height: 250px !important;
                border: 2px solid #e5e7eb !important;
                border-top: none !important;
                border-radius: 0 0 0.5rem 0.5rem !important;
                font-size: 14px !important;
                line-height: 1.6 !important;
                color: #374151 !important;
                padding: 16px !important;
            }

            .prose {
                color: #374151;
                max-width: none;
            }

            .prose h1,
            .prose h2,
            .prose h3,
            .prose h4,
            .prose h5,
            .prose h6 {
                color: #111827;
                font-weight: 700;
            }

            .prose p {
                margin-top: 0.75em;
                margin-bottom: 0.75em;
            }

            .prose ul,
            .prose ol {
                margin-top: 0.75em;
                margin-bottom: 0.75em;
                padding-left: 1.5em;
            }

            .prose li {
                margin-top: 0.5em;
                margin-bottom: 0.5em;
            }

            .prose strong {
                font-weight: 700;
                color: #111827;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

        <script>
            function toggleGuideline(button) {

                const content = button.parentElement.querySelector('.guideline-content');
                const icon = button.querySelector('.fa-chevron-down');

                content.classList.toggle('hidden');
                content.classList.toggle('block');
                content.classList.toggle('mt-3');
                icon.classList.toggle('rotate-180');
            }

            function toggleFeedback(button) {

                const content = button.parentElement.querySelector('.feedback-content');
                const icon = button.querySelector('.fa-chevron-down');

                content.classList.toggle('hidden');
                content.classList.toggle('block');
                content.classList.toggle('mt-3');
                icon.classList.toggle('rotate-180');
            }
        </script>

        <script>
            let currentBabId = null;
            let ckEditor = null;

            function openSubmissionModal(babId, judulBab) {
                currentBabId = babId;
                const modal = document.getElementById('submissionModal');
                const form = document.getElementById('submissionForm');
                const titleSpan = document.getElementById('submissionBabTitle');

                form.action = `{{ route('mahasiswa.bimbingan.submit', [$peserta->id_peserta_bimbingan, ':babId']) }}`.replace(
                    ':babId', babId);
                titleSpan.textContent = judulBab;

                form.reset();
                document.getElementById('fileUploadSection').classList.add('hidden');
                document.getElementById('textEditorSection').classList.add('hidden');

                modal.classList.remove('hidden');
            }

            function closeSubmissionModal() {
                if (ckEditor) {
                    ckEditor.destroy().catch(error => console.log(error));
                    ckEditor = null;
                }
                document.getElementById('submissionModal').classList.add('hidden');
            }

            function toggleSubmissionType() {
                const selectedType = document.querySelector('input[name="submission_type"]:checked')?.value;
                const fileSection = document.getElementById('fileUploadSection');
                const textSection = document.getElementById('textEditorSection');

                if (selectedType === 'file') {
                    fileSection.classList.remove('hidden');
                    textSection.classList.add('hidden');
                    if (ckEditor) {
                        ckEditor.destroy().catch(error => console.log(error));
                        ckEditor = null;
                    }
                } else if (selectedType === 'text') {
                    fileSection.classList.add('hidden');
                    textSection.classList.remove('hidden');

                    setTimeout(() => {
                        ClassicEditor.create(document.querySelector('#text_submission'), {
                                toolbar: ['heading', '|', 'bold', 'italic', 'underline', '|', 'bulletedList',
                                    'numberedList', '|', 'link', '|', 'undo', 'redo'
                                ],
                                placeholder: 'Tulis konten laporan Anda di sini...'
                            })
                            .then(editor => {
                                ckEditor = editor;
                            })
                            .catch(error => console.error('CKEditor error:', error));
                    }, 100);
                }
            }

            function viewSubmissions(babId) {
                const modal = document.getElementById('submissionsModal');
                const content = document.getElementById('submissionsContent');

                content.innerHTML =
                    '<div class="text-center py-12"><i class="fas fa-spinner fa-spin text-gray-400 text-2xl"></i><p class="text-xs text-gray-600 mt-3">Memuat data...</p></div>';
                modal.classList.remove('hidden');

                fetch(`{{ route('mahasiswa.bimbingan.submissions', [$peserta->id_peserta_bimbingan, ':babId']) }}`.replace(
                        ':babId', babId))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('submissionsModalTitle').textContent = data.bab.judul_bab;

                        if (data.submissions.length === 0) {
                            content.innerHTML =
                                '<div class="text-center py-12"><i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i><p class="text-xs text-gray-600">Belum ada submission</p></div>';
                            return;
                        }

                        let html = '<div class="space-y-4">';
                        data.submissions.forEach((submission, index) => {
                            html += `
                                <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-gray-900 transition-all duration-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-900 text-white text-xs font-bold rounded-full">
                                                ${data.submissions.length - index}
                                            </span>
                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-gray-100 text-gray-700">
                                                <i class="fas fa-${submission.input_type === 'FILE' ? 'file-alt' : 'align-left'} mr-2"></i>
                                                ${submission.input_type === 'FILE' ? 'File Upload' : 'Text Submission'}
                                            </span>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600">${new Date(submission.created_at).toLocaleString('id-ID', {
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        })}</span>
                                    </div>
                            `;

                            if (submission.input_type === 'FILE') {
                                html += `
                                    <a href="{{ asset('storage/') }}/${submission.file_path}" target="_blank"
                                       class="inline-flex items-center px-5 py-2.5 bg-gray-900 text-white text-xs font-bold rounded-lg hover:bg-gray-800 transition-all duration-200">
                                        <i class="fas fa-download mr-2"></i>
                                        Download File
                                    </a>
                                `;
                            } else {
                                html += `
                                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 prose prose-sm max-w-none">
                                        ${submission.konten}
                                    </div>
                                `;
                            }

                            html += '</div>';
                        });
                        html += '</div>';

                        content.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        content.innerHTML =
                            '<div class="text-center py-12"><i class="fas fa-exclamation-circle text-red-400 text-3xl mb-3"></i><p class="text-xs text-red-600">Gagal memuat data submission</p></div>';
                    });
            }

            function closeSubmissionsModal() {
                document.getElementById('submissionsModal').classList.add('hidden');
            }

            function filterByStatus() {
                const selectedStatus = document.getElementById('statusFilter').value;
                const rows = document.querySelectorAll('.bab-row');

                rows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    row.style.display = (selectedStatus === '' || rowStatus === selectedStatus) ? '' : 'none';
                });
            }

            document.getElementById('submissionForm').addEventListener('submit', function(e) {
                const submissionType = document.querySelector('input[name="submission_type"]:checked');

                if (!submissionType) {
                    e.preventDefault();
                    alert('Silakan pilih tipe submission (File atau Text)');
                    return;
                }

                if (submissionType.value === 'file') {
                    const fileInput = document.getElementById('file_submission');
                    if (!fileInput.files[0]) {
                        e.preventDefault();
                        alert('Silakan pilih file untuk diupload');
                        return;
                    }
                    document.getElementById('text_submission').disabled = true;
                } else if (submissionType.value === 'text') {
                    if (ckEditor) {
                        const data = ckEditor.getData();
                        if (!data.trim()) {
                            e.preventDefault();
                            alert('Silakan tulis konten laporan');
                            return;
                        }
                        document.getElementById('text_submission').value = data;
                    }
                    document.getElementById('file_submission').disabled = true;
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeSubmissionModal();
                    closeSubmissionsModal();
                }
            });

            window.openSubmissionModal = openSubmissionModal;
            window.closeSubmissionModal = closeSubmissionModal;
            window.toggleSubmissionType = toggleSubmissionType;
            window.viewSubmissions = viewSubmissions;
            window.closeSubmissionsModal = closeSubmissionsModal;
            window.filterByStatus = filterByStatus;
        </script>

        <script>
            function handleFileSelect(event) {
                const file = event.target.files[0];
                const filePreview = document.getElementById('filePreview');
                const fileUploadLabel = document.getElementById('fileUploadLabel');

                if (file) {
                    // Tampilkan nama file dan ukuran
                    document.getElementById('fileName').textContent = file.name;
                    document.getElementById('fileSize').textContent = formatFileSize(file.size);

                    // Tampilkan preview dan ubah style label
                    filePreview.classList.remove('hidden');
                    fileUploadLabel.classList.add('border-green-500', 'bg-green-50');
                    fileUploadLabel.classList.remove('border-gray-300');

                    // Update label text
                    fileUploadLabel.innerHTML = `
                <i class="fas fa-check-circle text-4xl text-green-500 mb-3"></i>
                <p class="text-xs font-semibold text-green-700 mb-1">File berhasil dipilih</p>
                <p class="text-xs text-gray-600">Klik lagi untuk mengganti file</p>
            `;
                }
            }

            function removeFile() {
                const fileInput = document.getElementById('file_submission');
                const filePreview = document.getElementById('filePreview');
                const fileUploadLabel = document.getElementById('fileUploadLabel');

                // Reset file input
                fileInput.value = '';

                // Sembunyikan preview
                filePreview.classList.add('hidden');

                // Kembalikan label ke kondisi awal
                fileUploadLabel.classList.remove('border-green-500', 'bg-green-50');
                fileUploadLabel.classList.add('border-gray-300');
                fileUploadLabel.innerHTML = `
            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
            <p class="text-xs font-semibold text-gray-700 mb-1">Klik untuk memilih file</p>
            <p class="text-xs text-gray-600">Format: PDF, DOC, DOCX (Maksimal 10MB)</p>
        `;
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            }

            // Tambahkan ke fungsi closeSubmissionModal yang sudah ada
            function closeSubmissionModal() {
                if (ckEditor) {
                    ckEditor.destroy().catch(error => console.log(error));
                    ckEditor = null;
                }

                // Reset file upload
                removeFile();

                document.getElementById('submissionModal').classList.add('hidden');
            }

            // Update fungsi toggleSubmissionType yang sudah ada
            function toggleSubmissionType() {
                const selectedType = document.querySelector('input[name="submission_type"]:checked')?.value;
                const fileSection = document.getElementById('fileUploadSection');
                const textSection = document.getElementById('textEditorSection');

                if (selectedType === 'file') {
                    fileSection.classList.remove('hidden');
                    textSection.classList.add('hidden');
                    if (ckEditor) {
                        ckEditor.destroy().catch(error => console.log(error));
                        ckEditor = null;
                    }
                } else if (selectedType === 'text') {
                    fileSection.classList.add('hidden');
                    textSection.classList.remove('hidden');

                    // Reset file upload saat switch ke text
                    removeFile();

                    setTimeout(() => {
                        ClassicEditor.create(document.querySelector('#text_submission'), {
                                toolbar: ['heading', '|', 'bold', 'italic', 'underline', '|', 'bulletedList',
                                    'numberedList', '|', 'link', '|', 'undo', 'redo'
                                ],
                                placeholder: 'Tulis konten laporan Anda di sini...'
                            })
                            .then(editor => {
                                ckEditor = editor;
                            })
                            .catch(error => console.error('CKEditor error:', error));
                    }, 100);
                }
            }

            // Pastikan functions tersedia secara global
            window.handleFileSelect = handleFileSelect;
            window.removeFile = removeFile;
            window.formatFileSize = formatFileSize;
        </script>
    @endpush
@endsection
