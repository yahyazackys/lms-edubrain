<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ESD Edubrain</title>
    <link rel="shortcut icon" href="{{ asset('icon-primary.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Public+Sans:ital,wght@0,100..900;1,100..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">


    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

    {{-- Alpine JS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="{
    sidebarOpen: false,
    alerts: []
}" class="font-sans antialiased bg-gray-50">

    <!-- Session Expired Modal -->
    <div id="sessionExpiredModal" class="fixed inset-0 z-[10001] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
            <div
                class="inline-block w-full max-w-sm p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                </div>

                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Sesi Telah Berakhir</h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Sesi Anda telah habis. Silakan login kembali untuk melanjutkan.
                    </p>

                    <button id="redirectToLogin"
                        class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 transition-colors">
                        Login Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container - Fixed Position -->
    <div id="alert-container" class="fixed top-16 right-4 z-[99999] space-y-3 max-w-xs md:max-w-sm w-full"
        x-data="alertManager()" x-cloak>

        <template x-for="alert in alerts" :key="alert.id">
            <div x-show="alert.show" x-transition:enter="transform transition-all duration-300 ease-out"
                x-transition:enter-start="translate-x-full opacity-0 scale-95"
                x-transition:enter-end="translate-x-0 opacity-100 scale-100"
                x-transition:leave="transform transition-all duration-200 ease-in"
                x-transition:leave-start="translate-x-0 opacity-100 scale-100"
                x-transition:leave-end="translate-x-full opacity-0 scale-95"
                :class="{
                    'bg-green-50 border-green-200': alert.type === 'success',
                    'bg-red-50 border-red-200': alert.type === 'error',
                    'bg-yellow-50 border-yellow-200': alert.type === 'warning',
                    'bg-blue-50 border-blue-200': alert.type === 'info'
                }"
                class="border rounded-lg p-4 shadow-lg backdrop-blur-sm">

                <div class="flex items-start">
                    <!-- Icon -->
                    <div class="flex-shrink-0 mr-3">
                        <svg x-show="alert.type === 'success'" class="w-5 h-5 text-green-600" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <svg x-show="alert.type === 'error'" class="w-5 h-5 text-red-600" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <svg x-show="alert.type === 'warning'" class="w-5 h-5 text-yellow-600" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <svg x-show="alert.type === 'info'" class="w-5 h-5 text-blue-600" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <h4 x-text="alert.title"
                            :class="{
                                'text-green-800': alert.type === 'success',
                                'text-red-800': alert.type === 'error',
                                'text-yellow-800': alert.type === 'warning',
                                'text-blue-800': alert.type === 'info'
                            }"
                            class="text-sm font-medium"></h4>
                        <p x-text="alert.message"
                            :class="{
                                'text-green-600': alert.type === 'success',
                                'text-red-600': alert.type === 'error',
                                'text-yellow-600': alert.type === 'warning',
                                'text-blue-600': alert.type === 'info'
                            }"
                            class="text-sm mt-1"></p>
                    </div>

                    <!-- Close Button -->
                    <button @click="removeAlert(alert.id)"
                        :class="{
                            'text-green-500 hover:text-green-600': alert.type === 'success',
                            'text-red-500 hover:text-red-600': alert.type === 'error',
                            'text-yellow-500 hover:text-yellow-600': alert.type === 'warning',
                            'text-blue-500 hover:text-blue-600': alert.type === 'info'
                        }"
                        class="flex-shrink-0 ml-3 p-1 rounded-md hover:bg-white/50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Progress Bar -->
                <div x-show="alert.autoHide" class="mt-3 -mb-1 -mx-1">
                    <div class="h-1 bg-white/30 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-75 ease-linear"
                            :class="{
                                'bg-green-400': alert.type === 'success',
                                'bg-red-400': alert.type === 'error',
                                'bg-yellow-400': alert.type === 'warning',
                                'bg-blue-400': alert.type === 'info'
                            }"
                            :style="`width: ${alert.progress}%`"></div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div class="flex h-screen overflow-hidden">
        @include('partials.navbar')

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-y-auto">
            <main class="px-4 lg:px-8 flex-1 lg:ml-64 mt-20 lg:mt-20">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Session & Alert Manager Script -->
    <script>
        // Session Management
        let sessionCheckInterval;
        let sessionExpiredModalShown = false;

        function initSessionManager() {
            // Check session every 5 minutes
            sessionCheckInterval = setInterval(checkSession, 5 * 60 * 1000);

            // Intercept all fetch requests to handle session expiry
            interceptFetchRequests();
        }

        function checkSession() {
            // Use root URL - works from any page
            const checkUrl = '{{ url('/') }}';

            fetch(checkUrl, {
                    method: 'HEAD', // Use HEAD to avoid loading full page content
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                })
                .then(response => {
                    if (response.status === 401 || response.status === 419) {
                        handleSessionExpired();
                    }
                })
                .catch(error => {
                    console.log('Session check failed:', error);
                });
        }

        function handleSessionExpired() {
            if (!sessionExpiredModalShown) {
                sessionExpiredModalShown = true;
                clearInterval(sessionCheckInterval);
                showSessionExpiredModal();
            }
        }

        function showSessionExpiredModal() {
            const modal = document.getElementById('sessionExpiredModal');
            modal.classList.remove('hidden');

            // Handle redirect button
            document.getElementById('redirectToLogin').addEventListener('click', function() {
                window.location.href = '{{ route('login') }}';
            });
        }

        function interceptFetchRequests() {
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args)
                    .then(response => {
                        if (response.status === 401 || response.status === 419) {
                            handleSessionExpired();
                        }
                        return response;
                    })
                    .catch(error => {
                        console.log('Fetch error:', error);
                        throw error;
                    });
            };
        }

        // Alert Manager
        function alertManager() {
            return {
                alerts: [],
                nextId: 1,

                init() {
                    // Show session alerts if any
                    @if (session('success'))
                        this.showAlert('success', 'Berhasil', '{{ session('success') }}');
                    @endif

                    @if (session('error'))
                        this.showAlert('error', 'Gagal', '{{ session('error') }}');
                    @endif

                    @if (session('warning'))
                        this.showAlert('warning', 'Peringatan', '{{ session('warning') }}');
                    @endif

                    @if (session('info'))
                        this.showAlert('info', 'Informasi', '{{ session('info') }}');
                    @endif

                    // Make showAlert globally available
                    window.showAlert = this.showAlert.bind(this);
                },

                showAlert(type, title, message, autoHide = true, duration = 5000) {
                    const alert = {
                        id: this.nextId++,
                        type: type,
                        title: title,
                        message: message,
                        show: true,
                        autoHide: autoHide,
                        progress: 100
                    };

                    this.alerts.push(alert);

                    if (autoHide) {
                        this.startProgressBar(alert, duration);
                        setTimeout(() => {
                            this.removeAlert(alert.id);
                        }, duration);
                    }
                },

                removeAlert(id) {
                    const index = this.alerts.findIndex(alert => alert.id === id);
                    if (index > -1) {
                        this.alerts[index].show = false;
                        setTimeout(() => {
                            this.alerts.splice(index, 1);
                        }, 200);
                    }
                },

                startProgressBar(alert, duration) {
                    const interval = 50; // Update every 50ms
                    const steps = duration / interval;
                    let step = 0;

                    const timer = setInterval(() => {
                        step++;
                        alert.progress = Math.max(0, 100 - (step / steps * 100));

                        if (step >= steps) {
                            clearInterval(timer);
                        }
                    }, interval);
                }
            }
        }

        // Helper functions for easy use
        function showSuccess(message, title = 'Berhasil') {
            window.showAlert('success', title, message);
        }

        function showError(message, title = 'Gagal') {
            window.showAlert('error', title, message);
        }

        function showWarning(message, title = 'Peringatan') {
            window.showAlert('warning', title, message);
        }

        function showInfo(message, title = 'Informasi') {
            window.showAlert('info', title, message);
        }

        // Initialize session manager when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initSessionManager();
        });
    </script>

    @stack('scripts')
</body>

</html>
