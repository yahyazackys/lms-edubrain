@extends('layouts.app')

@section('title', 'Dashboard Bimbingan')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-lg font-heading font-semibold text-gray-900">Dashboard Bimbingan</h1>
                            <p class="text-xs text-gray-600">Kelola laporan bimbingan KKN, Magang, dan Skripsi Anda</p>
                        </div>

                        <div class="flex items-center space-x-4 mt-4 sm:mt-0">
                            <label class="text-xs font-medium text-gray-700">Semester:</label>
                            <select id="semesterFilter" onchange="changeSemester()"
                                class="text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 min-w-[250px]">
                                <option value="">Pilih Semester</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id_semester }}"
                                        {{ $selectedSemesterId == $semester->id_semester ? 'selected' : '' }}>
                                        {{ $semester->nama_semester }}
                                        {{ $semester->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            @if (!$selectedSemester)
                <!-- Empty State - Pilih Semester -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-16 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Pilih Semester</h3>
                        <p class="text-xs text-gray-500">Pilih semester dari dropdown di atas untuk melihat mata kuliah
                            bimbingan Anda</p>
                    </div>
                </div>
            @elseif(isset($stats) && $stats['total_kkn'] == 0 && $stats['total_magang'] == 0 && $stats['total_skripsi'] == 0)
                <!-- Empty State - Tidak ada mata kuliah -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-16 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-book-open text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Mata Kuliah Bimbingan</h3>
                        <p class="text-xs text-gray-500">Anda tidak memiliki mata kuliah bimbingan terkait di semester
                            {{ $selectedSemester->nama_semester }}</p>
                    </div>
                </div>
            @else
                <!-- Tab Navigation dan Content -->
                <div class="bg-white rounded-lg shadow-sm font-heading">
                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 overflow-hidden">
                        <!-- Mobile Tab Navigation -->
                        <div class="block sm:hidden">
                            <div class="relative">
                                <div id="mobile-tabs" class="flex space-x-1 px-4 overflow-x-auto scrollbar-hide touch-pan-x"
                                    style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch;">
                                    <button onclick="switchTab('kkn')"
                                        class="tab-button {{ $activeTab === 'kkn' ? 'active' : '' }} whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                        id="tab-kkn">
                                        <i class="fas fa-users w-4 h-4 inline mr-2"></i>
                                        KKN
                                        <span
                                            class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">{{ $stats['total_kkn'] ?? 0 }}</span>
                                    </button>
                                    <button onclick="switchTab('magang')"
                                        class="tab-button {{ $activeTab === 'magang' ? 'active' : '' }} whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                        id="tab-magang">
                                        <i class="fas fa-briefcase w-4 h-4 inline mr-2"></i>
                                        Magang
                                        <span
                                            class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">{{ $stats['total_magang'] ?? 0 }}</span>
                                    </button>
                                    <button onclick="switchTab('skripsi')"
                                        class="tab-button {{ $activeTab === 'skripsi' ? 'active' : '' }} whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                        id="tab-skripsi">
                                        <i class="fas fa-graduation-cap w-4 h-4 inline mr-2"></i>
                                        Skripsi
                                        <span
                                            class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">{{ $stats['total_skripsi'] ?? 0 }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop Tab Navigation -->
                        <nav class="hidden sm:flex space-x-8 px-6" aria-label="Tabs">
                            <button onclick="switchTab('kkn')"
                                class="tab-button {{ $activeTab === 'kkn' ? 'active' : '' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                id="tab-kkn-desktop">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-users text-xs"></i>
                                    <span>KKN</span>
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $stats['total_kkn'] ?? 0 }}</span>
                                </div>
                            </button>
                            <button onclick="switchTab('magang')"
                                class="tab-button {{ $activeTab === 'magang' ? 'active' : '' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                id="tab-magang-desktop">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-briefcase text-xs"></i>
                                    <span>Magang</span>
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $stats['total_magang'] ?? 0 }}</span>
                                </div>
                            </button>
                            <button onclick="switchTab('skripsi')"
                                class="tab-button {{ $activeTab === 'skripsi' ? 'active' : '' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                id="tab-skripsi-desktop">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-graduation-cap text-xs"></i>
                                    <span>Skripsi</span>
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $stats['total_skripsi'] ?? 0 }}</span>
                                </div>
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- KKN Tab -->
                        <div id="content-kkn" class="tab-content {{ $activeTab === 'kkn' ? 'active' : 'hidden' }}">
                            @if (isset($kkn) && $kkn->isEmpty())
                                <div class="text-center py-16">
                                    <div
                                        class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-users text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada mata kuliah KKN</h3>
                                    <p class="text-xs text-gray-500">Anda tidak terdaftar dalam mata kuliah KKN di semester
                                        ini</p>
                                </div>
                            @elseif(isset($kkn))
                                @foreach ($kkn as $item)
                                    <div class="bg-gray-50 rounded-lg p-6 mb-4">
                                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                                    {{ $item->mata_kuliah->nama_mata_kuliah }}</h3>
                                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                                    <div>
                                                        <p class="text-xs text-gray-600 mb-1">Pembimbing Utama:</p>
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $item->pembimbing_utama->pengguna->nama ?? 'Belum ada pembimbing' }}
                                                        </p>
                                                    </div>
                                                    @if ($item->pembimbing_kedua)
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Pembimbing Kedua:</p>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $item->pembimbing_kedua->pengguna->nama }}</p>
                                                        </div>
                                                    @endif
                                                    @if (isset($item->detail))
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Kelompok:</p>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $item->detail['kelompok'] ?? '' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Peran:</p>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $item->detail['peran'] ?? '' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Periode:</p>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $item->detail['periode'] ?? '' }}</p>
                                                        </div>
                                                    @endif
                                                </div>

                                                @if (isset($item->detail))
                                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Lokasi:</p>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $item->detail['lokasi'] }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Alamat Lokasi:</p>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $item->detail['alamat_lokasi'] }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="mb-4">
                                                        <p class="text-xs text-gray-600 mb-1">Target Program Kerja:</p>
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $item->detail['target_program_kerja'] }}
                                                        </p>
                                                    </div>
                                                @endif

                                                <!-- Progress -->
                                                <div class="flex items-center space-x-4 mb-3">
                                                    <div class="flex-1">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <span class="text-xs font-medium text-gray-700">Progress
                                                                Laporan</span>
                                                            <span
                                                                class="text-xs text-gray-700">{{ $item->progress['progress_percentage'] }}%</span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                                            <div class="bg-gray-900 h-2 rounded-full"
                                                                style="width: {{ $item->progress['progress_percentage'] }}%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>

                                            <div class="mt-4 lg:mt-0 lg:ml-6 flex space-x-2">
                                                @if ($item->detail)
                                                    <a href="{{ route('mahasiswa.bimbingan.kelompok.detail', $item->id_peserta_bimbingan) }}"
                                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                                        <i class="fas fa-users mr-2"></i>
                                                        Lihat Kelompok
                                                    </a>
                                                @endif
                                                <a href="{{ route('mahasiswa.bimbingan.detail', $item->id_peserta_bimbingan) }}"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                                    <i class="fas fa-eye mr-2"></i>
                                                    Kelola Laporan
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Magang Tab -->
                        <div id="content-magang" class="tab-content {{ $activeTab === 'magang' ? 'active' : 'hidden' }}">
                            @if (isset($magang) && $magang->isEmpty())
                                <div class="text-center py-16">
                                    <div
                                        class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-briefcase text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada mata kuliah Magang</h3>
                                    <p class="text-xs text-gray-500">Anda tidak terdaftar dalam mata kuliah Magang di
                                        semester ini</p>
                                </div>
                            @elseif(isset($magang))
                                @foreach ($magang as $item)
                                    <div class="bg-gray-50 rounded-lg p-6 mb-4">
                                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                                    {{ $item->mata_kuliah->nama_mata_kuliah }}</h3>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                    <div>
                                                        <p class="text-xs text-gray-600 mb-1">Pembimbing Utama:</p>
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $item->pembimbing_utama->pengguna->nama ?? 'Belum ada pembimbing' }}
                                                        </p>
                                                    </div>
                                                    @if ($item->pembimbing_kedua)
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Pembimbing Kedua:</p>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $item->pembimbing_kedua->pengguna->nama }}</p>
                                                        </div>
                                                    @endif
                                                    @if (isset($item->detail))
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Tempat Magang:</p>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $item->detail['tempat'] }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Bidang:</p>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $item->detail['bidang'] }}</p>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Progress -->
                                                <div class="flex items-center space-x-4 mb-3">
                                                    <div class="flex-1">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <span class="text-xs font-medium text-gray-700">Progress
                                                                Laporan</span>
                                                            <span
                                                                class="text-xs text-gray-700">{{ $item->progress['progress_percentage'] }}%</span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                                            <div class="bg-gray-900 h-2 rounded-full"
                                                                style="width: {{ $item->progress['progress_percentage'] }}%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>

                                            <div class="mt-4 lg:mt-0 lg:ml-6">
                                                <a href="{{ route('mahasiswa.bimbingan.detail', $item->id_peserta_bimbingan) }}"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                    <i class="fas fa-eye mr-2"></i>
                                                    Kelola Laporan
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Skripsi Tab -->
                        <div id="content-skripsi"
                            class="tab-content {{ $activeTab === 'skripsi' ? 'active' : 'hidden' }}">
                            @if (isset($skripsi) && $skripsi->isEmpty())
                                <div class="text-center py-16">
                                    <div
                                        class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-graduation-cap text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada mata kuliah Skripsi</h3>
                                    <p class="text-xs text-gray-500">Anda tidak terdaftar dalam mata kuliah Skripsi di
                                        semester ini</p>
                                </div>
                            @elseif(isset($skripsi))
                                @foreach ($skripsi as $item)
                                    <div class="bg-gray-50 rounded-lg p-6 mb-4">
                                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                                    {{ $item->mata_kuliah->nama_mata_kuliah }}</h3>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                    <div>
                                                        <p class="text-xs text-gray-600 mb-1">Pembimbing Utama:</p>
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $item->pembimbing_utama->pengguna->nama ?? 'Belum ada pembimbing' }}
                                                        </p>
                                                    </div>
                                                    @if ($item->pembimbing_kedua)
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Pembimbing Kedua:</p>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $item->pembimbing_kedua->pengguna->nama }}</p>
                                                        </div>
                                                    @endif
                                                    @if (isset($item->detail))
                                                        <div class="col-span-2">
                                                            <p class="text-xs text-gray-600 mb-1">Judul Skripsi:</p>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $item->detail['judul'] }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-xs text-gray-600 mb-1">Bidang Penelitian:</p>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                {{ $item->detail['bidang_penelitian'] }}</p>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Progress -->
                                                <div class="flex items-center space-x-4 mb-3">
                                                    <div class="flex-1">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <span class="text-xs font-medium text-gray-700">Progress
                                                                Laporan</span>
                                                            <span
                                                                class="text-xs text-gray-700">{{ $item->progress['progress_percentage'] }}%</span>
                                                        </div>
                                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                                            <div class="bg-gray-900 h-2 rounded-full"
                                                                style="width: {{ $item->progress['progress_percentage'] }}%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>

                                            <div class="mt-4 lg:mt-0 lg:ml-6">
                                                <a href="{{ route('mahasiswa.bimbingan.detail', $item->id_peserta_bimbingan) }}"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                    <i class="fas fa-eye mr-2"></i>
                                                    Kelola Laporan
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

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
            let currentTab = 'kkn';

            // Initialize page
            document.addEventListener('DOMContentLoaded', function() {
                setupMobileTabs();
                initializeTab();
            });

            // Utility function to get URL parameters
            function getUrlParameter(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }

            // Utility function to update URL parameter without page refresh
            function updateUrlParameter(key, value) {
                const url = new URL(window.location);
                url.searchParams.set(key, value);
                window.history.pushState({
                    path: url.href
                }, '', url.href);
            }

            // Check if tab name is valid
            function isValidTab(tabName) {
                const validTabs = ['kkn', 'magang', 'skripsi'];
                return validTabs.includes(tabName);
            }

            // Initialize tab based on URL query parameter
            function initializeTab() {
                const urlTab = getUrlParameter('tab');
                const initialTab = urlTab && isValidTab(urlTab) ? urlTab : 'kkn';
                switchTabWithoutUrlUpdate(initialTab);
            }

            // Handle browser back/forward navigation
            window.addEventListener('popstate', function(event) {
                const urlTab = getUrlParameter('tab');
                const targetTab = urlTab && isValidTab(urlTab) ? urlTab : 'kkn';
                switchTabWithoutUrlUpdate(targetTab);
            });

            // Enhanced tab switching with URL update
            function switchTab(tabName) {
                updateUrlParameter('tab', tabName);
                switchTabWithoutUrlUpdate(tabName);
            }

            // Tab switching without URL update (for internal use)
            function switchTabWithoutUrlUpdate(tabName) {
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

            // Mobile tabs setup
            function setupMobileTabs() {
                const mobileTabsContainer = document.getElementById('mobile-tabs');

                if (mobileTabsContainer) {
                    function updateScrollIndicators() {
                        // Add any scroll indicator logic here if needed
                    }

                    mobileTabsContainer.addEventListener('scroll', updateScrollIndicators);
                    window.addEventListener('resize', updateScrollIndicators);
                    updateScrollIndicators();
                }
            }

            // Change semester function
            function changeSemester() {
                const semesterId = document.getElementById('semesterFilter').value;
                if (semesterId) {
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.set('semester', semesterId);
                    // Keep current tab when changing semester
                    currentParams.set('tab', currentTab);
                    window.location.href = window.location.pathname + '?' + currentParams.toString();
                } else {
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.delete('semester');
                    currentParams.delete('tab');
                    window.location.href = window.location.pathname + (currentParams.toString() ? '?' + currentParams
                        .toString() : '');
                }
            }

            // Make functions globally accessible
            window.changeSemester = changeSemester;
            window.switchTab = switchTab;
        </script>
    @endpush
@endsection
