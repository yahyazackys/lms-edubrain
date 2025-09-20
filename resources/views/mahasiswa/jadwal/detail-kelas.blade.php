@extends('layouts.app')

@section('title', 'Detail Kelas - ' . $kelasKuliah->mataKuliah->nama_mata_kuliah)

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header dengan Info Kelas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-3">
                <div class="px-6 py-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <!-- Info Kelas -->
                        <div class="flex items-start space-x-4">
                            <div class="flex-1 min-w-0">
                                <h1 class="text-lg font-semibold font-heading text-gray-900">
                                    {{ $kelasKuliah->mataKuliah->kode_mata_kuliah }} -
                                    {{ $kelasKuliah->mataKuliah->nama_mata_kuliah }}
                                </h1>
                                <div class="flex sm:flex-row flex-col items-start sm:items-center gap-x-4 gap-y-1 mt-2">
                                    <div class="flex items-center text-xs text-gray-600 gap-1">
                                        <i class="fa-solid fa-users text-xs"></i>
                                        {{ $kelasKuliah->nama_kelas_kuliah }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600 gap-1">
                                        <i class="fa-solid fa-clock text-xs"></i>
                                        {{ ucwords(strtolower($kelasKuliah->hari)) }},
                                        {{ \Carbon\Carbon::parse($kelasKuliah->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($kelasKuliah->jam_akhir)->format('H:i') }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600 gap-1">
                                        <i class="fa-solid fa-door-open text-xs"></i>
                                        {{ $kelasKuliah->nama_ruangan }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600 gap-1">
                                        <i class="fa-solid fa-calendar text-xs"></i>
                                        {{ $kelasKuliah->semester->nama_semester }}
                                    </div>
                                </div>

                                <!-- Dosen Info -->
                                <div class="mt-3">
                                    <div class="flex items-center text-xs text-gray-600 gap-1">
                                        <i class="fa-solid fa-chalkboard-teacher text-xs"></i>
                                        <span>{{ $kelasKuliah->dosen->pengguna->nama ?? 'N/A' }}</span>
                                        @if ($kelasKuliah->dosen->nidn)
                                            <span class="text-gray-500">• {{ $kelasKuliah->dosen->nidn }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status & Info -->
                                <div class="flex items-center gap-3 mt-3">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $kelasKuliah->mataKuliah->sks_mata_kuliah }} SKS
                                    </span>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $kelasKuliah->status === 'selesai' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        <i class="fas fa-check-circle mr-1 text-xs"></i>
                                        {{ ucfirst($kelasKuliah->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 lg:mt-0">
                            <a href="{{ route('jadwal-kuliah.index') }}"
                                class="w-full lg:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Jadwal
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if ($kelasKuliah->status === 'selesai')
                <div class="my-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg mt-3">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                        <span class="text-sm text-yellow-800">
                            Kelas ini telah diakhiri. Mode tampilan hanya untuk melihat data.
                        </span>
                    </div>
                </div>
            @endif

            <!-- Tab Navigation dan Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 overflow-hidden font-heading">
                    <!-- Mobile Tab Navigation -->
                    <div class="block sm:hidden">
                        <div class="relative">
                            <div id="mobile-tabs" class="flex space-x-1 px-4 overflow-x-auto scrollbar-hide touch-pan-x"
                                style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch;">
                                <button onclick="switchTab('materi')"
                                    class="tab-button active whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-materi">
                                    <i class="fas fa-book w-4 h-4 inline mr-2"></i>
                                    Materi
                                </button>
                                <button onclick="switchTab('tugas')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-tugas">
                                    <i class="fas fa-tasks w-4 h-4 inline mr-2"></i>
                                    Tugas
                                </button>
                                <button onclick="switchTab('uts')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-uts">
                                    <i class="fas fa-file-alt w-4 h-4 inline mr-2"></i>
                                    UTS
                                </button>
                                <button onclick="switchTab('uas')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-uas">
                                    <i class="fas fa-graduation-cap w-4 h-4 inline mr-2"></i>
                                    UAS
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Tab Navigation -->
                    <nav class="hidden sm:flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="switchTab('materi')"
                            class="tab-button active whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-materi-desktop">
                            <i class="fas fa-book w-4 h-4 inline mr-2"></i>
                            Materi
                        </button>
                        <button onclick="switchTab('tugas')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-tugas-desktop">
                            <i class="fas fa-tasks w-4 h-4 inline mr-2"></i>
                            Tugas
                        </button>
                        <button onclick="switchTab('uts')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-uts-desktop">
                            <i class="fas fa-file-alt w-4 h-4 inline mr-2"></i>
                            UTS
                        </button>
                        <button onclick="switchTab('uas')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-uas-desktop">
                            <i class="fas fa-graduation-cap w-4 h-4 inline mr-2"></i>
                            UAS
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->

                <!-- Tab 1: Materi -->
                <div id="content-materi" class="tab-content active">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 font-heading">Materi Pembelajaran</h3>
                                <p class="text-sm text-gray-600">Akses materi yang telah diberikan oleh dosen</p>
                            </div>
                        </div>

                        @if ($materis->count() > 0)
                            <!-- Tabel Materi -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Judul Materi</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deskripsi</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal Upload</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($materis as $index => $materi)
                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $index + 1 }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs font-medium text-gray-900">{{ $materi->judul }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-xs text-gray-900">
                                                        {{ $materi->deskripsi ? Str::limit($materi->deskripsi, 80) : 'Tidak ada deskripsi' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $materi->created_at->format('d/m/Y H:i') }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if ($materi->dokumen)
                                                        <a href="{{ route('detail-kelas.download-materi', $materi->id_materi) }}"
                                                            target="_blank"
                                                            class="underline text-xs text-blue-600 hover:text-blue-800">
                                                            Lihat File
                                                        </a>
                                                    @else
                                                        <span class="text-xs text-gray-400">Tidak ada file</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-16">
                                <div
                                    class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-book text-gray-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Materi</h3>
                                <p class="text-sm text-gray-500">Materi pembelajaran belum tersedia untuk kelas ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 2: Tugas -->
                <div id="content-tugas" class="tab-content hidden">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 font-heading">Tugas</h3>
                                <p class="text-sm text-gray-600">Lihat dan kumpulkan tugas yang diberikan</p>
                            </div>
                        </div>

                        @if ($tugas->count() > 0)
                            <!-- Tabel Tugas -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Judul Tugas</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deskripsi</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deadline</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nilai</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File Soal</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File Jawaban</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($tugas as $index => $tugasItem)
                                            @php
                                                $isExpired = $tugasItem->batas_akhir_pengumpulan < now();
                                                $pengumpulan = $tugasItem->pengumpulanTugas->first();
                                                $sudahDikumpulkan = $pengumpulan !== null;

                                                $statusClass = $sudahDikumpulkan
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-yellow-100 text-yellow-800';
                                                $statusText = $sudahDikumpulkan ? 'Dikumpulkan' : 'Belum Dikumpulkan';
                                                $statusIcon = $sudahDikumpulkan ? 'check-circle' : 'clock';
                                            @endphp

                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $index + 1 }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs font-medium text-gray-900">{{ $tugasItem->judul }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-xs text-gray-900">
                                                        {{ Str::limit($tugasItem->deskripsi, 80) }}</div>
                                                </td>
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap text-xs {{ $isExpired ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                                    {{ $tugasItem->batas_akhir_pengumpulan->format('d/m/Y H:i') }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                                        <i class="fas fa-{{ $statusIcon }} mr-1"></i>
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if ($sudahDikumpulkan && $pengumpulan->nilai)
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">

                                                            {{ $pengumpulan->nilai }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-gray-400">Belum dinilai</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if ($tugasItem->dokumen)
                                                        <a href="{{ route('detail-kelas.download-tugas', $tugasItem->id_tugas) }}"
                                                            target="_blank"
                                                            class="underline text-xs text-blue-600 hover:text-blue-800">
                                                            Lihat File
                                                        </a>
                                                    @else
                                                        <span class="text-xs text-gray-400">Tidak ada</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if ($pengumpulan && $pengumpulan->dokumen)
                                                        <a href="{{ route('detail-kelas.download-pengumpulan-tugas', $pengumpulan->id_pengumpulan_tugas) }}"
                                                            target="_blank"
                                                            class="underline text-xs text-blue-600 hover:text-blue-800">
                                                            Lihat File
                                                        </a>
                                                    @else
                                                        <span class="text-xs text-gray-400">Tidak ada</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if (!$isExpired)
                                                        <button
                                                            class="pengumpulan-btn inline-flex items-center px-3 py-1 bg-gray-900 hover:bg-gray-700 text-white text-xs rounded-lg transition-colors duration-200"
                                                            data-type="tugas" data-id="{{ $tugasItem->id_tugas }}"
                                                            data-title="{{ htmlspecialchars($tugasItem->judul, ENT_QUOTES, 'UTF-8') }}"
                                                            data-is-update="{{ $sudahDikumpulkan ? 'true' : 'false' }}">
                                                            <i
                                                                class="fas fa-{{ $sudahDikumpulkan ? 'edit' : 'upload' }} mr-1"></i>
                                                            {{ $sudahDikumpulkan ? 'Update' : 'Kumpulkan' }}
                                                        </button>
                                                    @else
                                                        <span class="text-xs text-gray-400">Expired</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-16">
                                <div
                                    class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-tasks text-gray-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Tugas</h3>
                                <p class="text-sm text-gray-500">Tugas belum tersedia untuk kelas ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 3: UTS -->
                <div id="content-uts" class="tab-content hidden">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 font-heading">Ujian Tengah Semester (UTS)
                                </h3>
                                <p class="text-sm text-gray-600">Lihat dan kumpulkan ujian UTS</p>
                            </div>
                        </div>

                        @if ($uts->count() > 0)
                            <!-- Tabel UTS -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Judul UTS</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deskripsi</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deadline</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nilai</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File Soal</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File Jawaban</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($uts as $index => $utsItem)
                                            @php
                                                $isExpired = $utsItem->batas_akhir_pengumpulan < now();
                                                $pengumpulan = $utsItem->pengumpulanUts->first();
                                                $sudahDikumpulkan = $pengumpulan !== null;

                                                $statusClass = $sudahDikumpulkan
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-yellow-100 text-yellow-800';
                                                $statusText = $sudahDikumpulkan ? 'Dikumpulkan' : 'Belum Dikumpulkan';
                                                $statusIcon = $sudahDikumpulkan ? 'check-circle' : 'clock';
                                            @endphp

                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $index + 1 }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs font-medium text-gray-900">{{ $utsItem->judul }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-xs text-gray-900">
                                                        {{ Str::limit($utsItem->deskripsi, 80) }}</div>
                                                </td>
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap text-xs {{ $isExpired ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                                    {{ $utsItem->batas_akhir_pengumpulan->format('d/m/Y H:i') }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                                        <i class="fas fa-{{ $statusIcon }} mr-1"></i>
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if ($sudahDikumpulkan && $pengumpulan->nilai)
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">

                                                            {{ $pengumpulan->nilai }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-gray-400">Belum dinilai</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if ($utsItem->dokumen)
                                                        <a href="{{ route('detail-kelas.download-uts', $utsItem->id_uts) }}"
                                                            target="_blank"
                                                            class="underline text-xs text-blue-600 hover:text-blue-800">
                                                            Lihat File
                                                        </a>
                                                    @else
                                                        <span class="text-xs text-gray-400">Tidak ada</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if ($pengumpulan && $pengumpulan->dokumen)
                                                        <a href="{{ route('detail-kelas.download-pengumpulan-uts', $pengumpulan->id_pengumpulan_uts) }}"
                                                            target="_blank"
                                                            class="underline text-xs text-blue-600 hover:text-blue-800">
                                                            Lihat File
                                                        </a>
                                                    @else
                                                        <span class="text-xs text-gray-400">Tidak ada</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if (!$isExpired)
                                                        <button
                                                            class="pengumpulan-btn inline-flex items-center px-3 py-1 bg-gray-900 hover:bg-gray-700 text-white text-xs rounded-lg transition-colors duration-200"
                                                            data-type="uts" data-id="{{ $utsItem->id_uts }}"
                                                            data-title="{{ htmlspecialchars($utsItem->judul, ENT_QUOTES, 'UTF-8') }}"
                                                            data-is-update="{{ $sudahDikumpulkan ? 'true' : 'false' }}">
                                                            <i
                                                                class="fas fa-{{ $sudahDikumpulkan ? 'edit' : 'upload' }} mr-1"></i>
                                                            {{ $sudahDikumpulkan ? 'Update' : 'Kumpulkan' }}
                                                        </button>
                                                    @else
                                                        <span class="text-xs text-gray-400">Expired</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-16">
                                <div
                                    class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-file-alt text-gray-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada UTS</h3>
                                <p class="text-sm text-gray-500">UTS belum tersedia untuk kelas ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 4: UAS -->
                <div id="content-uas" class="tab-content hidden">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 font-heading">Ujian Akhir Semester (UAS)
                                </h3>
                                <p class="text-sm text-gray-600">Lihat dan kumpulkan ujian UAS</p>
                            </div>
                        </div>

                        @if ($uas->count() > 0)
                            <!-- Tabel UAS -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Judul UAS</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deskripsi</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deadline</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nilai</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File Soal</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File Jawaban</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($uas as $index => $uasItem)
                                            @php
                                                $isExpired = $uasItem->batas_akhir_pengumpulan < now();
                                                $pengumpulan = $uasItem->pengumpulanUas->first();
                                                $sudahDikumpulkan = $pengumpulan !== null;

                                                $statusClass = $sudahDikumpulkan
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-yellow-100 text-yellow-800';
                                                $statusText = $sudahDikumpulkan ? 'Dikumpulkan' : 'Belum Dikumpulkan';
                                                $statusIcon = $sudahDikumpulkan ? 'check-circle' : 'clock';
                                            @endphp

                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $index + 1 }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs font-medium text-gray-900">{{ $uasItem->judul }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-xs text-gray-900">
                                                        {{ Str::limit($uasItem->deskripsi, 80) }}</div>
                                                </td>
                                                <td
                                                    class="px-4 py-3 whitespace-nowrap text-xs {{ $isExpired ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                                    {{ $uasItem->batas_akhir_pengumpulan->format('d/m/Y H:i') }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                                        <i class="fas fa-{{ $statusIcon }} mr-1"></i>
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if ($sudahDikumpulkan && $pengumpulan->nilai)
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">

                                                            {{ $pengumpulan->nilai }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-gray-400">Belum dinilai</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if ($uasItem->dokumen)
                                                        <a href="{{ route('detail-kelas.download-uas', $uasItem->id_uas) }}"
                                                            target="_blank"
                                                            class="underline text-xs text-blue-600 hover:text-blue-800">
                                                            Lihat File
                                                        </a>
                                                    @else
                                                        <span class="text-xs text-gray-400">Tidak ada</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if ($pengumpulan && $pengumpulan->dokumen)
                                                        <a href="{{ route('detail-kelas.download-pengumpulan-uas', $pengumpulan->id_pengumpulan_uas) }}"
                                                            target="_blank"
                                                            class="underline text-xs text-blue-600 hover:text-blue-800">
                                                            Lihat File
                                                        </a>
                                                    @else
                                                        <span class="text-xs text-gray-400">Tidak ada</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if (!$isExpired)
                                                        <button
                                                            class="pengumpulan-btn inline-flex items-center px-3 py-1 bg-gray-900 hover:bg-gray-700 text-white text-xs rounded-lg transition-colors duration-200"
                                                            data-type="uas" data-id="{{ $uasItem->id_uas }}"
                                                            data-title="{{ htmlspecialchars($uasItem->judul, ENT_QUOTES, 'UTF-8') }}"
                                                            data-is-update="{{ $sudahDikumpulkan ? 'true' : 'false' }}">
                                                            <i
                                                                class="fas fa-{{ $sudahDikumpulkan ? 'edit' : 'upload' }} mr-1"></i>
                                                            {{ $sudahDikumpulkan ? 'Update' : 'Kumpulkan' }}
                                                        </button>
                                                    @else
                                                        <span class="text-xs text-gray-400">Expired</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-16">
                                <div
                                    class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-graduation-cap text-gray-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada UAS</h3>
                                <p class="text-sm text-gray-500">UAS belum tersedia untuk kelas ini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('mahasiswa.jadwal.modals.pengumpulan-modal')

    @push('styles')
        <style>
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }

            .tab-button.active {
                @apply border-gray-900 text-gray-900;
            }

            .tab-button:not(.active) {
                @apply border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300;
            }

            .tab-content {
                display: none;
            }

            .tab-content.active {
                display: block;
            }

            /* Mobile tab animation */
            #mobile-tabs {
                scroll-snap-type: x mandatory;
            }

            #mobile-tabs button {
                scroll-snap-align: start;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Global variables
            let currentTab = 'materi';

            // Initialize page
            document.addEventListener('DOMContentLoaded', function() {
                switchTab('materi');
            });

            // Tab switching
            function switchTab(tabName) {
                currentTab = tabName;

                // Hide all tab contents
                const tabContents = document.querySelectorAll('.tab-content');
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                    content.classList.remove('active');
                });

                // Remove active class from all tab buttons
                const tabButtons = document.querySelectorAll('.tab-button');
                tabButtons.forEach(button => {
                    button.classList.remove('active', 'border-gray-900', 'text-gray-900');
                    button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700',
                        'hover:border-gray-300');
                });

                // Show selected tab content
                const selectedContent = document.getElementById(`content-${tabName}`);
                if (selectedContent) {
                    selectedContent.classList.remove('hidden');
                    selectedContent.classList.add('active');
                }

                // Add active class to selected tab buttons
                const mobileButton = document.getElementById(`tab-${tabName}`);
                const desktopButton = document.getElementById(`tab-${tabName}-desktop`);

                [mobileButton, desktopButton].forEach(button => {
                    if (button) {
                        button.classList.add('active', 'border-gray-900', 'text-gray-900');
                        button.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700',
                            'hover:border-gray-300');
                    }
                });

                // Scroll mobile tab into view
                if (mobileButton && window.innerWidth < 640) {
                    mobileButton.scrollIntoView({
                        behavior: 'smooth',
                        inline: 'center',
                        block: 'nearest'
                    });
                }
            }

            // Make functions global
            window.switchTab = switchTab;
        </script>

        @include('mahasiswa.jadwal.scripts.pengumpulan-script')
    @endpush
@endsection
