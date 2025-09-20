<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404 - Halaman Tidak Ditemukan</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Vite Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-heading {
            font-family: system-ui, -apple-system, sans-serif;
            font-weight: 600;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-pulse-slow {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        }
    </style>
</head>

<body class="h-full bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <!-- Error Icon -->
            <div class="flex justify-center">
                <div class="relative">
                    <div
                        class="w-32 h-32 bg-gradient-primary rounded-full flex items-center justify-center animate-float">
                        <i class="fas fa-exclamation-triangle text-4xl text-white"></i>
                    </div>
                    <!-- Floating dots -->
                    <div class="absolute -top-2 -right-2 w-4 h-4 bg-gray-400 rounded-full animate-pulse-slow"></div>
                    <div class="absolute -bottom-1 -left-3 w-3 h-3 bg-gray-300 rounded-full animate-pulse-slow"
                        style="animation-delay: 2s;"></div>
                    <div class="absolute top-1/2 -right-5 w-2 h-2 bg-gray-200 rounded-full animate-pulse-slow"
                        style="animation-delay: 4s;"></div>
                </div>
            </div>

            <!-- Error Content -->
            <div class="space-y-4">
                <!-- 404 Text -->
                <div>
                    <h1 class="text-6xl font-bold text-gray-900 font-heading">404</h1>
                    <div class="mt-2 w-24 h-1 bg-gray-900 mx-auto rounded-full"></div>
                </div>

                <!-- Error Message -->
                <div class="space-y-3">
                    <h2 class="text-2xl font-semibold text-gray-900 font-heading">
                        Halaman Tidak Ditemukan
                    </h2>
                    <p class="text-gray-600 leading-relaxed">
                        Maaf, halaman yang Anda cari tidak dapat ditemukan. Halaman mungkin telah dipindahkan, dihapus,
                        atau URL yang Anda masukkan salah.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3 pt-4">
                    {{-- <button onclick="goBack()"
                        class="w-full inline-flex justify-center items-center px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Halaman Sebelumnya
                    </button> --}}

                    <a href="{{ route('dashboard') ?? '/' }}"
                        class="w-full inline-flex justify-center items-center px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 transition-colors duration-200">
                        <i class="fas fa-home mr-2"></i>
                        Ke Halaman Utama
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('current-time').textContent = timeString;
        }

        // Go back function
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '{{ route('dashboard') ?? '/' }}';
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateTime();
            setInterval(updateTime, 1000);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' || event.key === 'Backspace') {
                goBack();
            } else if (event.key === 'h' || event.key === 'H') {
                window.location.href = '{{ route('dashboard') ?? '/' }}';
            }
        });
    </script>
</body>

</html>
