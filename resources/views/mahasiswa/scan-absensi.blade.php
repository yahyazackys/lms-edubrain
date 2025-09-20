{{-- File: resources/views/mahasiswa/scan-absensi.blade.php --}}

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absensi - {{ $sesi->topik ?? 'Scan QR Code' }}</title>
    <link rel="shortcut icon" href="{{ asset('icon-primary.png') }}" />

    <!-- Tailwind CSS -->
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Styles -->
    <style>
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .subtle-shadow {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .border-elegant {
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        /* Custom font for headings */
        .font-heading {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #f1f3f4 100%);
        }

        .btn-primary {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e0 100%);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .status-icon {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border: 2px solid rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-lg px-4">
        {{-- Main Content --}}
        <div class="bg-white rounded-2xl subtle-shadow border-elegant animate-fade-in">

            {{-- AUTH REQUIRED STATUS --}}
            @if ($status === 'auth_required')
                <div class="px-8 py-10">
                    <div class="text-center">
                        <div class="w-20 h-20 status-icon rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-sign-in-alt text-gray-600 text-3xl"></i>
                        </div>
                        <h2 class="text-xl font-heading text-gray-900 mb-3">Login Diperlukan</h2>
                        <p class="text-sm text-gray-600 mb-8 leading-relaxed">{{ $message }}</p>

                        <div class="space-y-4">
                            <a href="{{ route('login') }}"
                                class="w-full btn-primary inline-flex justify-center items-center px-6 py-4 text-white text-sm font-medium rounded-xl">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Login Sekarang
                            </a>
                            <button onclick="window.history.back()"
                                class="w-full btn-secondary inline-flex justify-center items-center px-6 py-4 text-gray-700 text-sm font-medium rounded-xl">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ERROR STATUS --}}
            @elseif($status === 'error')
                <div class="px-8 py-10">
                    <div class="text-center">
                        <div class="w-20 h-20 status-icon rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-exclamation-triangle text-gray-600 text-3xl"></i>
                        </div>
                        <h2 class="text-xl font-heading text-gray-900 mb-3">Tidak Dapat Melakukan Absensi</h2>
                        <p class="text-sm text-gray-600 mb-8 leading-relaxed">{{ $message }}</p>

                        @if (isset($error) && config('app.debug'))
                            <div class="bg-gray-50 border-elegant rounded-xl p-4 mb-6">
                                <p class="text-gray-700 text-xs font-mono">{{ $error }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- SUCCESS STATUS --}}
            @elseif($status === 'success')
                <div class="px-8 py-10">
                    <div class="text-center">
                        <div class="w-16 h-16 status-icon rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check-circle text-green-600 text-3xl"></i>
                        </div>

                        @if (isset($absensi) && $absensi->created_at->diffInMinutes(now()) > 5)
                            {{-- Jika absensi sudah lama (lebih dari 5 menit) - artinya sudah absen sebelumnya --}}
                            <h2 class="text-xl font-heading text-gray-900 mb-0">Absensi Sudah Tercatat</h2>
                            <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                                Anda telah berhasil melakukan absensi untuk perkuliahan ini
                            </p>
                        @else
                            {{-- Absensi baru saja dilakukan --}}
                            <h2 class="text-xl font-heading text-gray-900 mb-0">Absensi Berhasil</h2>
                            <p class="text-sm text-gray-600 mb-6 leading-relaxed">{{ $message }}</p>
                        @endif

                        @if (isset($absensi))
                            <div class="space-y-4">
                                {{-- Course Information --}}
                                @if (isset($sesi))
                                    <div class="bg-gray-50 border-elegant rounded-lg p-4">
                                        <h4 class="font-medium text-gray-900 text-sm mb-3 flex items-center">
                                            <i class="fas fa-book mr-2 text-gray-600"></i>
                                            Informasi Perkuliahan
                                        </h4>
                                        <div class="grid grid-cols-1 gap-2 text-xs">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Mata Kuliah:</span>
                                                <span class="font-medium text-gray-900">
                                                    {{ $sesi->kelasKuliah->mataKuliah->nama_mata_kuliah ?? 'N/A' }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Kode:</span>
                                                <span class="font-medium text-gray-900">
                                                    {{ $sesi->kelasKuliah->mataKuliah->kode_mata_kuliah ?? 'N/A' }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Kelas:</span>
                                                <span class="font-medium text-gray-900">
                                                    {{ $sesi->kelasKuliah->nama_kelas_kuliah ?? 'N/A' }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Topik:</span>
                                                <span class="font-medium text-gray-900">{{ $sesi->topik }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Student Info -->
                                @if (isset($mahasiswa))
                                    <div class="bg-gray-50 p-4 rounded-xl border-elegant mb-4">
                                        <h3 class="font-medium text-gray-900 text-sm mb-2 flex items-center">
                                            <i class="fas fa-user mr-2 text-gray-600"></i>
                                            Informasi Mahasiswa
                                        </h3>
                                        <ul class="space-y-0">
                                            <li class="info-item flex justify-between items-center py-1 rounded-lg">
                                                <span class="text-xs text-gray-600">NIM:</span>
                                                <span
                                                    class="text-xs font-medium text-gray-900">{{ $mahasiswa->nim }}</span>
                                            </li>
                                            <li class="info-item flex justify-between items-center py-1 rounded-lg">
                                                <span class="text-xs text-gray-600">Nama:</span>
                                                <span
                                                    class="text-xs font-medium text-gray-900">{{ $mahasiswa->pengguna->nama }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- FORM STATUS --}}
            @elseif($status === 'form')
                <div class="px-8 py-10">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 status-icon rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-graduation-cap text-gray-700 text-2xl"></i>
                        </div>
                        <h2 class="text-xl font-heading text-gray-900 mb-0">Konfirmasi Kehadiran</h2>
                        <p class="text-sm text-gray-600 leading-relaxed">Konfirmasi kehadiran Anda untuk sesi ini
                        </p>
                    </div>

                    {{-- Header Section --}}
                    @if (isset($sesi) && $sesi)
                        <!-- Mata Kuliah Section -->
                        <div class="bg-gray-50 p-4 rounded-xl border-elegant mb-4">
                            <h3 class="font-medium text-gray-900 text-sm mb-2 flex items-center">
                                <i class="fas fa-book mr-2 text-gray-600"></i>
                                Informasi Perkuliahan
                            </h3>
                            <ul class="space-y-0">
                                <li class="info-item flex justify-between items-center py-1 rounded-lg">
                                    <span class="text-xs text-gray-600">Kode:</span>
                                    <span
                                        class="text-xs font-medium text-gray-900">{{ $sesi->kelasKuliah->mataKuliah->kode_mata_kuliah ?? 'Kode Mata Kuliah' }}</span>
                                </li>
                                <li class="info-item flex justify-between items-center py-1 rounded-lg">
                                    <span class="text-xs text-gray-600">Nama:</span>
                                    <span
                                        class="text-xs font-medium text-gray-900">{{ $sesi->kelasKuliah->mataKuliah->nama_mata_kuliah ?? 'Mata Kuliah' }}</span>
                                </li>
                                <li class="info-item flex justify-between items-center py-1 rounded-lg">
                                    <span class="text-xs text-gray-600">Kelas:</span>
                                    <span
                                        class="text-xs font-medium text-gray-900">{{ $sesi->kelasKuliah->nama_kelas_kuliah ?? 'Kelas Kuliah' }}</span>
                                </li>
                                <li class="info-item flex justify-between items-center py-1 rounded-lg">
                                    <span class="text-xs text-gray-600">Topik Perkuliahan:</span>
                                    <span class="text-xs font-medium text-gray-900">{{ $sesi->topik }}</span>
                                </li>
                            </ul>
                        </div>
                    @endif

                    @if (isset($mahasiswa))
                        <div class="bg-gray-50 p-4 rounded-xl border-elegant mb-4">
                            <h3 class="font-medium text-gray-900 text-sm mb-2 flex items-center">
                                <i class="fas fa-user mr-2 text-gray-600"></i>
                                Informasi Mahasiswa
                            </h3>
                            <ul class="space-y-0">
                                <li class="info-item flex justify-between items-center py-1 rounded-lg">
                                    <span class="text-xs text-gray-600">NIM:</span>
                                    <span class="text-xs font-medium text-gray-900">{{ $mahasiswa->nim }}</span>
                                </li>
                                <li class="info-item flex justify-between items-center py-1 rounded-lg">
                                    <span class="text-xs text-gray-600">Nama:</span>
                                    <span
                                        class="text-xs font-medium text-gray-900">{{ $mahasiswa->pengguna->nama }}</span>
                                </li>
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('absensi.submit') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="id_sesi_absensi" value="{{ $sesi->id_sesi_absensi }}">

                        <div class="space-y-4">
                            <button type="submit" id="submitBtn"
                                class="w-full btn-primary inline-flex justify-center items-center px-6 py-4 text-white text-sm font-medium rounded-xl">
                                <i class="fas fa-check mr-2"></i>
                                Konfirmasi Kehadiran
                            </button>
                        </div>
                    </form>
                </div>

                {{-- DEFAULT/UNKNOWN STATUS --}}
            @else
                <div class="px-8 py-10">
                    <div class="text-center">
                        <div class="w-20 h-20 status-icon rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-question-circle text-gray-500 text-3xl"></i>
                        </div>
                        <h2 class="text-xl font-heading text-gray-900 mb-3">Status Tidak Dikenal</h2>
                        <p class="text-sm text-gray-600 mb-8 leading-relaxed">Terjadi kesalahan sistem. Silakan
                            coba lagi.</p>

                        <button onclick="window.location.reload()"
                            class="w-full btn-primary inline-flex justify-center items-center px-6 py-4 text-white text-sm font-medium rounded-xl">
                            <i class="fas fa-refresh mr-2"></i>
                            Muat Ulang
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-gray-400 text-xs">
                <i class="fas fa-shield-alt mr-1"></i>
                Edubrain Technology Indonesia - {{ date('Y') }}
            </p>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent double submission
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function() {
                    const submitBtn = document.getElementById('submitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
                        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                    }
                });
            }

            // Auto-close after success (if opened in popup)
            @if ($status === 'success')
                if (window.opener && typeof window.opener === 'object') {
                    setTimeout(function() {
                        const closeBtn = document.querySelector('button[onclick="window.close()"]');
                        if (closeBtn) {
                            setTimeout(() => window.close(), 5000);
                        }
                    }, 2000);
                }
            @endif

            // Enhanced loading for auth redirect
            @if ($status === 'auth_required')
                const loginBtn = document.querySelector('a[href*="login"]');
                if (loginBtn) {
                    loginBtn.addEventListener('click', function() {
                        this.innerHTML =
                            '<i class="fas fa-spinner fa-spin mr-2"></i>Mengarahkan ke Login...';
                        this.classList.add('opacity-75');
                    });
                }
            @endif
        });
    </script>
</body>

</html>
