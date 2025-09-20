@extends('layouts.app')

@section('title', 'Jadwal Mengajar')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-lg font-semibold font-heading text-gray-900">Jadwal Mengajar</h1>
                            <p class="text-xs text-gray-600">Kelola jadwal mengajar Anda berdasarkan periode semester</p>
                        </div>
                        <form method="GET" action="{{ route('jadwal-mengajar.index') }}" id="semesterForm">
                            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                                <label class="text-xs font-medium text-gray-700">Pilih Periode Semester:</label>
                                <select name="semester" id="semesterSelect"
                                    class="w-full sm:w-auto py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent bg-white"
                                    onchange="document.getElementById('semesterForm').submit()">
                                    <option value="">-- Pilih Semester --</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id_semester }}"
                                            {{ $selectedSemesterId == $semester->id_semester ? 'selected' : '' }}>
                                            {{ $semester->nama_semester }} (Aktif)
                                        </option>
                                    @endforeach
                                </select>

                                @if ($selectedSemester)
                                    <button type="button" onclick="window.print()"
                                        class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200 no-print">
                                        <i class="fa-solid fa-print mr-2"></i>
                                        Print Jadwal
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if (!$selectedSemester)
                <!-- Empty State - No Semester Selected -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-chalkboard-teacher text-6xl text-gray-400 mb-4"></i>
                            <p class="text-lg font-medium text-gray-900 mb-2">Pilih Periode Semester</p>
                            <p class="text-sm text-gray-500">Pilih periode semester terlebih dahulu untuk melihat jadwal
                                mengajar Anda</p>
                        </div>
                    </div>
                </div>
            @elseif(collect($jadwalPerHari)->flatten(1)->isEmpty())
                <!-- Empty State - No Schedule -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-calendar-xmark text-6xl text-gray-400 mb-4"></i>
                            <p class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Jadwal</p>
                            <p class="text-sm text-gray-500">Belum ada jadwal mengajar untuk semester
                                {{ $selectedSemester->nama_semester }}</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Schedule Content -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="overflow-x-auto">
                        <div class="grid grid-cols-1 lg:grid-cols-6 gap-0 min-w-full">
                            @foreach (['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'] as $hari)
                                <div class="border-r border-gray-200 last:border-r-0 min-h-40">
                                    <!-- Header Hari -->
                                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 text-center">
                                        <h4 class="font-medium text-gray-900 text-sm">{{ $hari }}</h4>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ count($jadwalPerHari[$hari]) }} kelas
                                        </p>
                                    </div>

                                    <!-- Schedule per Day -->
                                    <div class="p-3 space-y-3">
                                        @forelse($jadwalPerHari[$hari] as $jadwal)
                                            @php
                                                $isFinished =
                                                    isset($jadwal['status']) && $jadwal['status'] === 'selesai';
                                            @endphp
                                            <div class="relative bg-gray-50 border border-gray-200 rounded-lg p-3 hover:bg-gray-100 transition-colors duration-200 cursor-pointer"
                                                onclick="window.location.href='{{ route('jadwal-mengajar.detail-kelas', $jadwal['id_kelas_kuliah']) }}'">
                                                @if ($isFinished)
                                                    <!-- Badge Selesai -->
                                                    <div class="absolute top-1.5 right-1.5">
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-check-circle mr-1 text-xs"></i>
                                                            Selesai
                                                        </span>
                                                    </div>
                                                @else
                                                    <!-- Badge Aktif -->
                                                    <div class="absolute top-1.5 right-1.5">
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            <i class="fas fa-check-circle mr-1 text-xs"></i>
                                                            Aktif
                                                        </span>
                                                    </div>
                                                @endif
                                                <div class="space-y-2">
                                                    <!-- Subject Code & Name -->
                                                    <div class="mb-4">
                                                        <h5 class="font-medium text-gray-900 text-sm">
                                                            {{ $jadwal['kode_mata_kuliah'] }}
                                                        </h5>
                                                        <p class="text-xs text-gray-700 leading-tight">
                                                            {{ $jadwal['nama_mata_kuliah'] }}
                                                        </p>
                                                    </div>

                                                    <!-- Time -->
                                                    <div class="flex items-center text-xs text-gray-600">
                                                        <i class="fas fa-clock w-3 h-3 mr-3"></i>
                                                        {{ \Carbon\Carbon::parse($jadwal['jam_mulai'])->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($jadwal['jam_akhir'])->format('H:i') }}
                                                    </div>

                                                    <!-- Room & Class -->
                                                    <div class="flex items-center text-xs text-gray-600">
                                                        <i class="fas fa-door-open w-3 h-3 mr-3"></i>
                                                        {{ $jadwal['ruangan'] }} • {{ $jadwal['nama_kelas'] }}
                                                    </div>

                                                    <!-- Student Count -->
                                                    <div class="flex items-center text-xs text-gray-600">
                                                        <i class="fas fa-users w-3 h-3 mr-3"></i>
                                                        {{ $jadwal['jumlah_mahasiswa'] }}/{{ $jadwal['kapasitas'] }}
                                                        mahasiswa
                                                    </div>

                                                    <!-- SKS & Actions -->
                                                    <div class="flex justify-between items-center mt-4">
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ $jadwal['sks'] }} SKS
                                                        </span>
                                                        <div class="flex items-center space-x-1 no-print">
                                                            <button class="p-1 text-gray-600 hover:text-gray-800"
                                                                title="Detail Kelas"
                                                                onclick="event.stopPropagation(); window.location.href='{{ route('jadwal-mengajar.detail-kelas', $jadwal['id_kelas_kuliah']) }}'">
                                                                <i class="fa-solid fa-eye text-xs"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-8">
                                                <i class="fas fa-calendar-alt text-3xl mx-auto mb-2 text-gray-300"></i>
                                                <p class="text-xs text-gray-400">Tidak ada jadwal</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Schedule Summary -->
                    @if (collect($jadwalPerHari)->flatten(1)->count() > 0)
                        <div class="px-6 py-4 border-t border-gray-200">
                            <div
                                class="flex flex-col md:flex-row items-start justify-between text-xs text-gray-700 gap-y-1">
                                <div>Total Kelas: {{ collect($jadwalPerHari)->flatten(1)->count() }}</div>
                                <div class="flex gap-x-4 gap-y-1 flex-col md:flex-row items-start">
                                    <span>Total SKS: {{ collect($jadwalPerHari)->flatten(1)->sum('sks') }}</span>
                                    <span>Total Mahasiswa:
                                        {{ collect($jadwalPerHari)->flatten(1)->sum('jumlah_mahasiswa') }}</span>
                                    <span>Periode: {{ $selectedSemester->nama_semester }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                .print-content,
                .print-content * {
                    visibility: visible;
                }

                .print-content {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }

                .no-print {
                    display: none !important;
                }

                body {
                    background: white !important;
                }

                .bg-gray-50 {
                    background: white !important;
                }

                .shadow-sm {
                    box-shadow: none !important;
                }

                .border {
                    border: 1px solid #e5e7eb !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Print functionality
            window.addEventListener('beforeprint', function() {
                // Add print-content class to main content
                const mainContent = document.querySelector('.bg-white.rounded-lg.shadow-sm');
                if (mainContent) {
                    mainContent.classList.add('print-content');
                }

                // Hide non-printable elements
                const noPrintElements = document.querySelectorAll('.no-print, button, .bg-gray-100');
                noPrintElements.forEach(el => {
                    el.style.display = 'none';
                });
            });

            window.addEventListener('afterprint', function() {
                // Restore visibility
                const mainContent = document.querySelector('.print-content');
                if (mainContent) {
                    mainContent.classList.remove('print-content');
                }

                const hiddenElements = document.querySelectorAll('[style*="display: none"]');
                hiddenElements.forEach(el => {
                    el.style.display = '';
                });
            });
        </script>
    @endpush
@endsection
