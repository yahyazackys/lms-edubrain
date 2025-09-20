@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-sm sm:max-w-md w-full space-y-8">
            {{-- Logo & Brand --}}
            <div class="text-center">
                <div
                    class="mx-auto w-16 h-16 sm:w-20 sm:h-20 bg-gray-900 rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                    <i class="fas fa-graduation-cap text-white text-2xl sm:text-3xl"></i>
                </div>
                <h1 class="font-heading text-xl sm:text-2xl font-semibold text-gray-900 mb-2">Edubrain Technology</h1>
                <p class="text-sm text-gray-500">Sistem Informasi Akademik</p>
            </div>

            {{-- Login Box --}}
            <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
                {{-- Status Session --}}
                @if (session('status'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm mb-6">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('status') }}
                    </div>
                @endif

                {{-- Form Header --}}
                <div class="mb-6">
                    <h2 class="text-lg sm:text-xl font-medium text-gray-900 mb-1">Selamat Datang</h2>
                    <p class="text-sm text-gray-500">Masuk untuk mengakses sistem informasi akademik</p>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Username --}}
                    <div class="space-y-1">
                        <label for="username" class="block text-sm font-medium text-gray-700">
                            Username
                        </label>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required
                            autofocus
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent bg-gray-50 focus:bg-white transition-all duration-200 text-sm" />
                        @error('username')
                            <div class="text-sm text-red-500 mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Password with Toggle --}}
                    <div class="space-y-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl focus:ring-2 focus:ring-gray-900 focus:border-transparent bg-gray-50 focus:bg-white transition-all duration-200 text-sm" />
                            <button type="button" id="togglePassword"
                                class="absolute inset-y-0 right-0 flex items-center justify-center w-12 text-gray-400 hover:text-gray-600 transition-colors duration-200 focus:outline-none">
                                <i id="eyeIcon" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-sm text-red-500 mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Login Button --}}
                    <button type="submit"
                        class="w-full bg-gray-900 hover:bg-gray-800 text-white font-medium py-3 px-4 rounded-xl transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                        <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                    </button>
                </form>
            </div>

            {{-- Footer --}}
            <div class="text-center text-xs sm:text-sm text-gray-400">
                <p>&copy; {{ now()->year }} Edubrain. All rights reserved.</p>
            </div>
        </div>
    </div>

    {{-- JavaScript for Password Toggle --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            togglePassword.addEventListener('click', function() {
                // Toggle password visibility
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);

                // Toggle eye icon
                if (type === 'password') {
                    eyeIcon.className = 'fas fa-eye text-sm';
                } else {
                    eyeIcon.className = 'fas fa-eye-slash text-sm';
                }
            });
        });
    </script>
@endsection
