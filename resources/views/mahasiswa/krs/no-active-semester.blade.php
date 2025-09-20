@extends('layouts.app')

@section('title', 'Tidak Ada Semester Aktif - KRS')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Kartu Rencana Studi (KRS)</h1>
                            <p class="text-sm text-gray-600">Sistem Pengelolaan KRS Online</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content - No Active Semester -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-12 text-center">
                    <!-- Icon -->
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-yellow-100 mb-6">
                        <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="max-w-md mx-auto">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Tidak Ada Semester Aktif</h2>
                        <p class="text-gray-600 mb-6">
                            Saat ini tidak ada semester yang sedang dibuka untuk pengisian KRS.
                            Silakan tunggu pengumuman dari akademik untuk jadwal pembukaan KRS.
                        </p>

                        <!-- Information Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full mx-auto mb-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-medium text-blue-900 mb-1">Informasi</h3>
                                <p class="text-xs text-blue-700">
                                    Pengisian KRS akan dibuka sesuai dengan kalender akademik yang telah ditetapkan.
                                </p>
                            </div>

                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div
                                    class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-full mx-auto mb-2">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-medium text-green-900 mb-1">Persiapan</h3>
                                <p class="text-xs text-green-700">
                                    Gunakan waktu ini untuk berkonsultasi dengan pembimbing akademik Anda.
                                </p>
                            </div>
                        </div>

                        <!-- Action Suggestions -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Yang Dapat Anda Lakukan:</h3>
                            <div class="space-y-3 text-left">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-5 h-5 mt-0.5">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full mt-1.5"></div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-xs text-gray-700">
                                            <span class="font-medium">Cek kalender akademik</span> untuk mengetahui jadwal
                                            pembukaan KRS
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-5 h-5 mt-0.5">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full mt-1.5"></div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-xs text-gray-700">
                                            <span class="font-medium">Konsultasi dengan PA</span> untuk merencanakan mata
                                            kuliah semester depan
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-5 h-5 mt-0.5">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full mt-1.5"></div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-xs text-gray-700">
                                            <span class="font-medium">Pantau pengumuman</span> dari bagian akademik atau
                                            sistem informasi
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-5 h-5 mt-0.5">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full mt-1.5"></div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-xs text-gray-700">
                                            <span class="font-medium">Periksa transkrip nilai</span> untuk memastikan
                                            prerequisite mata kuliah
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h3 class="text-sm font-medium text-blue-900 mb-2">Butuh Bantuan?</h3>
                            <p class="text-xs text-blue-700 mb-3">
                                Hubungi bagian akademik atau pembimbing akademik Anda untuk informasi lebih lanjut.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <div class="flex items-center text-xs text-blue-700">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    akademik@universitas.ac.id
                                </div>
                                <div class="flex items-center text-xs text-blue-700">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                        </path>
                                    </svg>
                                    (021) 123-4567
                                </div>
                            </div>
                        </div>

                        <!-- Refresh Button -->
                        <div class="mt-6">
                            <button onclick="window.location.reload()"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Refresh Halaman
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info Section -->
            <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Jadwal Umum KRS</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mx-auto mb-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 mb-1">Periode Perencanaan</h4>
                            <p class="text-xs text-gray-600">Konsultasi dengan PA dan persiapan rencana studi</p>
                        </div>
                        <div class="text-center">
                            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mx-auto mb-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 mb-1">Periode Pengisian</h4>
                            <p class="text-xs text-gray-600">Mahasiswa mengisi KRS secara online</p>
                        </div>
                        <div class="text-center">
                            <div
                                class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-full mx-auto mb-3">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 mb-1">Periode Persetujuan</h4>
                            <p class="text-xs text-gray-600">PA melakukan review dan persetujuan KRS</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto refresh every 5 minutes to check for active semester
            setInterval(function() {
                window.location.reload();
            }, 300000); // 5 minutes

            // Optional: Add notification when semester becomes active
            // This would require WebSocket or polling mechanism
        </script>
    @endpush
@endsection
