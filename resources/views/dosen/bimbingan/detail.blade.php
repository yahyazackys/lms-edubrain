@extends('layouts.app')

@section('title', 'Detail Progress - ' . $mahasiswa->pengguna->nama)

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Back Button Header -->
            <div class="mb-6">
                <button onclick="history.back()"
                    class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-xs font-medium rounded-lg shadow-sm hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Daftar Mahasiswa
                </button>
            </div>

            <!-- Header Card - Profil Mahasiswa -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="px-6 py-6">
                    <!-- Top Section: Info Mahasiswa + Quick Actions -->
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between mb-6">
                        <!-- Info Mahasiswa -->
                        <div class="flex items-start space-x-4">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                @if ($mahasiswa->foto)
                                    <img src="{{ asset('storage/foto-mahasiswa/' . $mahasiswa->foto) }}"
                                        class="w-16 h-16 rounded-full object-cover" alt="{{ $mahasiswa->pengguna->nama }}">
                                @else
                                    <i class="fas fa-user text-gray-600 text-xl"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h1 class="text-xl font-semibold text-gray-900">{{ $mahasiswa->pengguna->nama }}</h1>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
                                    <div class="flex items-center text-xs text-gray-600">
                                        <i class="fas fa-id-card mr-2 text-gray-400"></i>
                                        {{ $mahasiswa->nim }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600">
                                        <i class="fas fa-graduation-cap mr-2 text-gray-400"></i>
                                        {{ $mahasiswa->programStudi->nama_program_studi ?? 'N/A' }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600">
                                        <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                        Angkatan {{ $mahasiswa->angkatan }}
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 mt-3">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-graduation-cap mr-2"></i>
                                        {{ $jenis_bimbingan }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $mata_kuliah->nama_mata_kuliah }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-4 lg:mt-0 flex flex-col space-y-3">
                            <button onclick="openBabModal('create')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Struktur Laporan
                            </button>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-book text-gray-600 text-lg"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Total Struktur</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $progress_summary['total_bab'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-gray-600 text-lg"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Disetujui</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $progress_summary['bab_approved'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock text-gray-600 text-lg"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Pending Review</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $progress_summary['bab_submitted'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-edit text-gray-600 text-lg"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs font-medium text-gray-600">Perlu Revisi</p>
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $progress_summary['bab_needs_revision'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pembimbing Info -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-xs font-semibold text-gray-900 mb-3">Tim Pembimbing</h4>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-6">
                                <div class="flex items-center">
                                    <i class="fas fa-user-tie text-gray-500 mr-2"></i>
                                    <span class="text-xs text-gray-600">Pembimbing Utama:</span>
                                    <span class="text-xs font-medium text-gray-900 ml-2">
                                        {{ $pembimbing_utama->pengguna->nama ?? 'Belum ditentukan' }}
                                        @if ($is_pembimbing_utama)
                                            <span class="text-gray-600">(Anda)</span>
                                        @endif
                                    </span>
                                </div>
                                @if ($pembimbing_kedua)
                                    <div class="flex items-center">
                                        <i class="fas fa-user-tie text-gray-500 mr-2"></i>
                                        <span class="text-xs text-gray-600">Pembimbing Kedua:</span>
                                        <span class="text-xs font-medium text-gray-900 ml-2">
                                            {{ $pembimbing_kedua->pengguna->nama }}
                                            @if (!$is_pembimbing_utama)
                                                <span class="text-gray-600">(Anda)</span>
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Tracking Table -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-4 sm:px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
                        <div>
                            <h2 class="text-base sm:text-lg font-semibold text-gray-900">Progress Tracking Laporan</h2>
                            <p class="text-xs text-gray-600 mt-0.5">Struktur laporan dan progress submission mahasiswa</p>
                        </div>
                        <div class="flex items-center gap-2 sm:space-x-2">
                            <span class="text-xs text-gray-500 shrink-0">Filter:</span>
                            <select id="statusFilter" onchange="filterByStatus()"
                                class="text-xs py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-500 w-full sm:w-auto">
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
                    <div class="px-6 py-12 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-book-open text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada struktur laporan</h3>
                        <p class="text-xs text-gray-500 mb-4">Mulai dengan menambahkan struktur laporan untuk
                            {{ $jenis_bimbingan }}</p>
                        <button onclick="openBabModal('create')"
                            class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Struktur Laporan Pertama
                        </button>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <span>Struktur Laporan</span>
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <span>Status</span>
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <span>Submission Mahasiswa</span>
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        <span>Aksi</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($babs as $bab)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 bab-row"
                                        data-status="{{ $bab['status'] }}">
                                        <!-- Struktur Laporan -->
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col space-y-3">
                                                <div>
                                                    <h4 class="text-sm font-semibold text-gray-900 mb-2">
                                                        {{ $bab['judul_bab'] }}
                                                    </h4>

                                                    <!-- Deskripsi/Konten dari Dosen -->
                                                    @if ($bab['konten'])
                                                        <div
                                                            class="mb-3 p-3 bg-blue-50 rounded-lg border-l-4 border-blue-200">
                                                            <div class="flex items-start">
                                                                <i
                                                                    class="fas fa-info-circle text-blue-500 mr-2 mt-0.5 text-xs"></i>
                                                                <div class="flex-1">
                                                                    <p class="text-xs font-medium text-blue-800 mb-1">
                                                                        Pedoman dari Pembimbing:</p>
                                                                    <div
                                                                        class="text-xs text-blue-700 prose prose-sm max-w-none">
                                                                        {!! $bab['konten'] !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- File Template -->
                                                    @if ($bab['file_template'])
                                                        <div
                                                            class="mb-3 p-3 bg-green-50 rounded-lg border-l-4 border-green-200">
                                                            <div class="flex items-center">
                                                                <i
                                                                    class="fas fa-file-download text-green-500 mr-2 text-xs"></i>
                                                                <div class="flex-1">
                                                                    <p class="text-xs font-medium text-green-800 mb-1">File
                                                                        Template:</p>
                                                                    <a href="{{ asset('storage/' . $bab['file_template']) }}"
                                                                        target="_blank"
                                                                        class="text-xs text-green-700 hover:text-green-900 underline">
                                                                        Download Template
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Catatan Pembimbing -->
                                                    @if ($bab['catatan_pembimbing'])
                                                        <div
                                                            class="mt-2 p-3 bg-gray-50 rounded-lg border-l-4 border-gray-300">
                                                            <div class="flex items-start">
                                                                <i
                                                                    class="fas fa-sticky-note text-gray-500 mr-2 mt-0.5 text-xs"></i>
                                                                <div>
                                                                    <p class="text-xs font-medium text-gray-800">Catatan
                                                                        Review Terakhir:</p>
                                                                    <p class="text-xs text-gray-700 mt-1">
                                                                        {{ $bab['catatan_pembimbing'] }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            <div class="flex flex-col items-center space-y-2">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i
                                                        class="fas fa-{{ $bab['status'] === 'APPROVED' ? 'check-circle' : ($bab['status'] === 'SUBMITTED' ? 'clock' : ($bab['status'] === 'NEEDS_REVISION' ? 'edit' : 'file')) }} mr-1.5"></i>
                                                    {{ $bab['status_formatted'] }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Submission Mahasiswa -->
                                        <td class="px-6 py-4">
                                            @if ($bab['latest_submission'])
                                                <div class="space-y-3">
                                                    {{-- <div
                                                        class="flex items-center justify-center text-xs text-gray-600 mb-2">
                                                        <i
                                                            class="fas fa-{{ $bab['latest_submission']->isFileUpload() ? 'file-alt' : 'edit' }} mr-1"></i>
                                                        {{ $bab['latest_submission']->isFileUpload() ? 'File Upload' : 'Text Submission' }}
                                                        <span class="ml-2 text-gray-500">
                                                            {{ $bab['latest_submission']->created_at->diffForHumans() }}
                                                        </span>
                                                    </div> --}}

                                                    <!-- Display Submission Content -->
                                                    {{-- @if ($bab['latest_submission']->isFileUpload())
                                                        <div class="p-3 bg-gray-50 rounded-lg">
                                                            <div class="flex items-center justify-center">
                                                                <a href="{{ asset('storage/' . $bab['latest_submission']->file_path) }}"
                                                                    target="_blank"
                                                                    class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">
                                                                    <i class="fas fa-download mr-2"></i>
                                                                    Download File
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="p-3 bg-gray-50 rounded-lg border">
                                                            <div class="text-xs text-gray-700 prose prose-sm max-w-none">
                                                                {!! $bab['latest_submission']->konten !!}
                                                            </div>
                                                        </div>
                                                    @endif --}}

                                                    {{-- @if ($bab['submission_count'] > 1) --}}
                                                    <div class="text-center">
                                                        <button onclick="viewSubmissions('{{ $bab['id_laporan_bab'] }}')"
                                                            class="text-xs text-blue-600 hover:text-blue-800 underline">
                                                            Lihat semua submission ({{ $bab['submission_count'] }})
                                                        </button>
                                                    </div>
                                                    {{-- @endif --}}
                                                </div>
                                            @else
                                                <div class="text-center py-6">
                                                    <span class="text-xs text-gray-400">
                                                        <i class="fas fa-minus-circle mr-1"></i>
                                                        Belum ada submission
                                                    </span>
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Aksi -->
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-1">
                                                @if ($bab['can_review'])
                                                    <button
                                                        onclick="openReviewModal('{{ $bab['id_laporan_bab'] }}', '{{ addslashes($bab['judul_bab']) }}')"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors duration-200"
                                                        title="Review Submission">
                                                        <i class="fas fa-check-circle text-xs"></i>
                                                    </button>
                                                @endif

                                                <button
                                                    onclick="openBabModal('edit', '{{ $bab['id_laporan_bab'] }}', '{{ addslashes($bab['judul_bab']) }}', `{{ addslashes($bab['konten'] ?? '') }}`)"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors duration-200"
                                                    title="Edit Struktur Laporan">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>

                                                <button
                                                    onclick="confirmDeleteBab('{{ $bab['id_laporan_bab'] }}', '{{ addslashes($bab['judul_bab']) }}')"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors duration-200"
                                                    title="Hapus Struktur Laporan">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal Tambah/Edit Struktur Laporan -->
        <div id="babModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeBabModal()"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">
                    <form id="babForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 id="babModalTitle" class="text-lg font-semibold text-gray-900">Tambah Struktur Laporan
                                </h3>
                                <button type="button" onclick="closeBabModal()"
                                    class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Judul Struktur Laporan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="judul_bab" id="judul_bab" maxlength="200" required
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Contoh: Pendahuluan">
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Pedoman/Deskripsi Struktur Laporan
                                    </label>
                                    <textarea name="konten" id="konten" rows="6"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Deskripsi atau pedoman untuk struktur laporan ini..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Template File (Opsional)
                                    </label>
                                    <input type="file" name="file_template" id="file_template"
                                        accept=".pdf,.doc,.docx"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <p class="text-xs text-gray-500 mt-1">Upload template untuk struktur laporan ini (PDF,
                                        DOC, DOCX). Maksimal 10MB.</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                            <button type="button" onclick="closeBabModal()"
                                class="inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                Batal
                            </button>
                            <button type="submit"
                                class="inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                <span id="babSubmitText">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Review -->
        <div id="reviewModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeReviewModal()">
                </div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">
                    <form id="reviewForm" method="POST">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Review Submission</h3>
                                <button type="button" onclick="closeReviewModal()"
                                    class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="mb-4">
                                <p class="text-xs text-gray-600">Struktur Laporan: <span id="reviewBabTitle"
                                        class="font-medium"></span></p>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Aksi</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="action" value="approve" class="mr-3">
                                            <span class="text-xs text-gray-700">Setujui</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="action" value="reject" class="mr-3">
                                            <span class="text-xs text-gray-700">Minta Revisi</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Catatan untuk Mahasiswa
                                    </label>
                                    <textarea name="catatan_pembimbing" rows="4" maxlength="1000"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Berikan catatan atau feedback untuk mahasiswa..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                            <button type="button" onclick="closeReviewModal()"
                                class="inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800">
                                Simpan Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- View Submissions Modal -->
        <div id="submissionsModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeSubmissionsModal()">
                </div>

                <div class="relative bg-white rounded-lg shadow-xl transform transition-all w-full max-w-4xl">
                    <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Riwayat Submission</h3>
                                <p class="text-xs text-gray-600 mt-1">Struktur Laporan: <span id="submissionsModalTitle"
                                        class="font-medium text-gray-900"></span></p>
                            </div>
                            <button type="button" onclick="closeSubmissionsModal()"
                                class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div id="submissionsContent" class="px-6 py-4 max-h-[500px] overflow-y-auto">
                        <!-- Dynamic content will be loaded here -->
                    </div>

                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
                        <button type="button" onclick="closeSubmissionsModal()"
                            class="inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden Forms -->
        <form id="delete-bab-form" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

    </div>

    @push('styles')
        <style>
            /* CKEditor 5 Custom Styling */
            .ck-editor {
                border: 1px solid #d1d5db !important;
                border-radius: 0.5rem !important;
            }

            .ck-toolbar {
                border: 1px solid #d1d5db !important;
                border-bottom: none !important;
                border-radius: 0.5rem 0.5rem 0 0 !important;
                background: #f9fafb !important;
                padding: 8px !important;
            }

            .ck-editor__editable {
                min-height: 200px !important;
                border: 1px solid #d1d5db !important;
                border-top: none !important;
                border-radius: 0 0 0.5rem 0.5rem !important;
                font-size: 14px !important;
                line-height: 1.6 !important;
                color: #374151 !important;
                padding: 12px !important;
            }

            .ck-button {
                background: #ffffff !important;
                border: 1px solid #d1d5db !important;
                border-radius: 0.25rem !important;
                color: #374151 !important;
                margin: 2px !important;
            }

            .ck-button:hover:not(.ck-disabled) {
                background: #f3f4f6 !important;
                border-color: #9ca3af !important;
            }

            .ck-button.ck-on {
                background: #111827 !important;
                color: #ffffff !important;
                border-color: #111827 !important;
            }

            .ck-content h3,
            .ck-content h4,
            .ck-content h5 {
                color: #111827 !important;
                margin-top: 1em !important;
                margin-bottom: 0.5em !important;
                font-weight: 600 !important;
            }

            .ck-content p {
                margin-bottom: 1em !important;
            }

            .ck-content ul,
            .ck-content ol {
                margin-bottom: 1em !important;
                padding-left: 1.5em !important;
            }

            /* Prose styling for rich text content */
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
                font-weight: 600;
            }

            .prose p {
                margin-top: 0.5em;
                margin-bottom: 0.5em;
            }

            .prose ul,
            .prose ol {
                margin-top: 0.5em;
                margin-bottom: 0.5em;
                padding-left: 1.5em;
            }

            .prose li {
                margin-top: 0.25em;
                margin-bottom: 0.25em;
            }

            .prose strong {
                font-weight: 600;
                color: #111827;
            }
        </style>
    @endpush

    @push('scripts')
        <!-- CKEditor 5 CDN -->
        <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

        <script>
            let currentBabId = null;
            let ckEditor = null;

            // Modal Management
            function openBabModal(mode, babId = null, judulBab = '', konten = '') {
                const modal = document.getElementById('babModal');
                const form = document.getElementById('babForm');
                const title = document.getElementById('babModalTitle');
                const submitText = document.getElementById('babSubmitText');

                // Reset form
                form.reset();

                // Destroy existing CKEditor instance
                if (ckEditor) {
                    ckEditor.destroy().catch(error => console.log(error));
                    ckEditor = null;
                }

                if (mode === 'create') {
                    form.action = '{{ route('dosen.bimbingan.bab.store', $peserta->id_peserta_bimbingan) }}';
                    form.querySelector('input[name="_method"]')?.remove();
                    title.textContent = 'Tambah Struktur Laporan';
                    submitText.textContent = 'Simpan';
                } else {
                    form.action = `{{ route('dosen.bimbingan.bab.update', [$peserta->id_peserta_bimbingan, ':babId']) }}`
                        .replace(':babId', babId);

                    let methodInput = form.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        form.appendChild(methodInput);
                    }
                    methodInput.value = 'PUT';

                    title.textContent = 'Edit Struktur Laporan';
                    submitText.textContent = 'Update';

                    // Set form values
                    document.getElementById('judul_bab').value = judulBab;
                }

                modal.classList.remove('hidden');

                // Initialize CKEditor after modal is shown
                setTimeout(() => {
                    ClassicEditor
                        .create(document.querySelector('#konten'), {
                            toolbar: [
                                'heading', '|',
                                'bold', 'italic', 'underline', '|',
                                'bulletedList', 'numberedList', '|',
                                'link', '|',
                                'undo', 'redo'
                            ],
                            heading: {
                                options: [{
                                        model: 'paragraph',
                                        title: 'Paragraph',
                                        class: 'ck-heading_paragraph'
                                    },
                                    {
                                        model: 'heading3',
                                        view: 'h3',
                                        title: 'Heading 3',
                                        class: 'ck-heading_heading3'
                                    },
                                    {
                                        model: 'heading4',
                                        view: 'h4',
                                        title: 'Heading 4',
                                        class: 'ck-heading_heading4'
                                    },
                                    {
                                        model: 'heading5',
                                        view: 'h5',
                                        title: 'Heading 5',
                                        class: 'ck-heading_heading5'
                                    }
                                ]
                            },
                            placeholder: 'Deskripsi atau pedoman untuk struktur laporan ini...'
                        })
                        .then(editor => {
                            ckEditor = editor;

                            // Set content for edit mode
                            if (mode === 'edit' && konten) {
                                editor.setData(konten);
                            }

                            // Update hidden textarea when content changes
                            editor.model.document.on('change:data', () => {
                                const data = editor.getData();
                                document.getElementById('konten').value = data;
                            });
                        })
                        .catch(error => {
                            console.error('CKEditor initialization error:', error);
                        });
                }, 200);
            }

            function closeBabModal() {
                // Destroy CKEditor before closing modal
                if (ckEditor) {
                    ckEditor.destroy().catch(error => console.log(error));
                    ckEditor = null;
                }
                document.getElementById('babModal').classList.add('hidden');
            }

            function openReviewModal(babId, judulBab) {
                const modal = document.getElementById('reviewModal');
                const form = document.getElementById('reviewForm');
                const titleSpan = document.getElementById('reviewBabTitle');

                form.action = `{{ route('dosen.bimbingan.bab.review', [$peserta->id_peserta_bimbingan, ':babId']) }}`.replace(
                    ':babId', babId);
                titleSpan.textContent = judulBab;

                modal.classList.remove('hidden');
            }

            function closeReviewModal() {
                document.getElementById('reviewModal').classList.add('hidden');
            }

            function confirmDeleteBab(babId, judulBab) {
                if (confirm(
                        `Yakin ingin menghapus "${judulBab}"?\n\nSemua submission mahasiswa untuk struktur laporan ini akan ikut terhapus.`
                    )) {
                    const form = document.getElementById('delete-bab-form');
                    form.action = `{{ route('dosen.bimbingan.bab.delete', [$peserta->id_peserta_bimbingan, ':babId']) }}`
                        .replace(':babId', babId);
                    form.submit();
                }
            }

            // function viewSubmissions(babId) {
            //     window.location.href =
            //         `{{ route('dosen.bimbingan.detail', $peserta->id_peserta_bimbingan) }}?view_bab=${babId}#submissions`;
            // }

            // Status filter functionality
            function filterByStatus() {
                const selectedStatus = document.getElementById('statusFilter').value;
                const rows = document.querySelectorAll('.bab-row');

                rows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    if (selectedStatus === '' || rowStatus === selectedStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Close modals with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeBabModal();
                    closeReviewModal();
                    closeSubmissionsModal();
                }
            });

            // Form validation
            document.getElementById('babForm').addEventListener('submit', function(e) {
                const judulBab = document.getElementById('judul_bab').value.trim();

                if (!judulBab) {
                    e.preventDefault();
                    alert('Judul struktur laporan harus diisi');
                    document.getElementById('judul_bab').focus();
                    return;
                }

                // Ensure CKEditor content is saved to textarea before submission
                if (ckEditor) {
                    const data = ckEditor.getData();
                    document.getElementById('konten').value = data;
                }
            });

            function viewSubmissions(babId) {
                const modal = document.getElementById('submissionsModal');
                const content = document.getElementById('submissionsContent');

                // Show loading state
                content.innerHTML = `
        <div class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-gray-400 text-2xl"></i>
            <p class="text-xs text-gray-600 mt-3">Memuat data...</p>
        </div>
    `;
                modal.classList.remove('hidden');

                // Fetch submissions data
                fetch(`{{ route('dosen.bimbingan.submissions', [$peserta->id_peserta_bimbingan, ':babId']) }}`.replace(
                        ':babId', babId))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('submissionsModalTitle').textContent = data.bab.judul_bab;

                        if (data.submissions.length === 0) {
                            content.innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
                        <p class="text-xs text-gray-600">Belum ada submission</p>
                    </div>
                `;
                            return;
                        }

                        let html = '<div class="space-y-4">';
                        data.submissions.forEach((submission, index) => {
                            const statusColor = submission.status === 'APPROVED' ? 'green' :
                                submission.status === 'NEEDS_REVISION' ? 'red' : 'gray';
                            const statusText = submission.status === 'APPROVED' ? 'Disetujui' :
                                submission.status === 'NEEDS_REVISION' ? 'Perlu Revisi' : 'Menunggu Review';

                            html += `
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0 mb-3">
    <div class="flex items-center gap-2 flex-wrap">
        <span class="inline-flex items-center justify-center w-6 h-6 bg-gray-900 text-white text-xs font-bold rounded-full shrink-0">
            ${data.submissions.length - index}
        </span>
        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
            <i class="fas fa-${submission.input_type === 'FILE' ? 'file-alt' : 'align-left'} mr-1"></i>
            ${submission.input_type === 'FILE' ? 'File Upload' : 'Text Submission'}
        </span>
        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-${statusColor}-100 text-${statusColor}-800">
            <i class="fas fa-${submission.status === 'APPROVED' ? 'check-circle' : submission.status === 'NEEDS_REVISION' ? 'exclamation-circle' : 'clock'} mr-1"></i>
            ${statusText}
        </span>
    </div>
    <span class="text-xs text-gray-500 sm:ml-3">${new Date(submission.created_at).toLocaleString('id-ID', {
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
                        <div class="mb-3">
                            <a href="{{ asset('storage/') }}/${submission.file_path}" target="_blank"
                               class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700">
                                <i class="fas fa-download mr-2"></i>
                                Download File
                            </a>
                        </div>
                    `;
                            } else {
                                html += `
                        <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="text-xs text-gray-700 prose prose-sm max-w-none">
                                ${submission.konten}
                            </div>
                        </div>
                    `;
                            }

                            if (submission.catatan_pembimbing) {
                                html += `
                        <div class="mt-3 p-3 bg-blue-50 rounded-lg border-l-4 border-blue-200">
                            <div class="flex items-start">
                                <i class="fas fa-comment text-blue-500 mr-2 mt-0.5 text-xs"></i>
                                <div>
                                    <p class="text-xs font-medium text-blue-800">Catatan Review:</p>
                                    <p class="text-xs text-blue-700 mt-1">${submission.catatan_pembimbing}</p>
                                </div>
                            </div>
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
                        content.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-exclamation-circle text-red-400 text-2xl mb-3"></i>
                    <p class="text-xs text-red-600">Gagal memuat data submission</p>
                </div>
            `;
                    });
            }

            function closeSubmissionsModal() {
                document.getElementById('submissionsModal').classList.add('hidden');
            }

            document.getElementById('reviewForm').addEventListener('submit', function(e) {
                const action = document.querySelector('input[name="action"]:checked');

                if (!action) {
                    e.preventDefault();
                    alert('Pilih aksi review (Setujui atau Minta Revisi)');
                    return;
                }
            });

            // Clean up CKEditor when page unloads
            window.addEventListener('beforeunload', function() {
                if (ckEditor) {
                    ckEditor.destroy().catch(error => console.log(error));
                }
            });

            // Make functions globally accessible
            window.openBabModal = openBabModal;
            window.closeBabModal = closeBabModal;
            window.openReviewModal = openReviewModal;
            window.closeReviewModal = closeReviewModal;
            window.confirmDeleteBab = confirmDeleteBab;
            window.viewSubmissions = viewSubmissions;
            window.closeSubmissionsModal = closeSubmissionsModal;
            window.filterByStatus = filterByStatus;
        </script>
    @endpush
@endsection
