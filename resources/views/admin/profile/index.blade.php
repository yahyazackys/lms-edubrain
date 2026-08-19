@extends('layouts.app')

@section('title', 'Pengaturan Profil Admin')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <h1 class="text-xl font-semibold font-heading text-gray-900">Pengaturan Profil</h1>
                    <p class="text-sm text-gray-600">Kelola informasi akun admin Anda</p>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Profile Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-900">Informasi Profil</h2>
                    <p class="text-xs text-gray-600">Perbarui informasi profil Anda</p>
                </div>
                <div class="px-6 py-6">
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama" value="{{ old('nama', $admin->nama) }}"
                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('nama') border-red-500 @enderror"
                                    placeholder="Masukkan nama lengkap" required>
                                @error('nama')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input type="email" name="email" value="{{ old('email', $admin->email) }}"
                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('email') border-red-500 @enderror"
                                    placeholder="email@example.com">
                                @error('email')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">
                                    Nomor HP
                                </label>
                                <input type="text" name="no_hp" value="{{ old('no_hp', $admin->no_hp) }}"
                                    class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent @error('no_hp') border-red-500 @enderror"
                                    placeholder="08xxxxxxxxxx">
                                @error('no_hp')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Username Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-900">Username</h2>
                    <p class="text-xs text-gray-600">Ubah username untuk login</p>
                </div>
                <div class="px-6 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-700">Username Saat Ini</p>
                            <p class="text-sm font-mono font-semibold text-gray-900 mt-1">{{ $admin->username }}</p>
                        </div>
                        <button onclick="openUsernameModal()"
                            class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                </path>
                            </svg>
                            Ubah Username
                        </button>
                    </div>
                </div>
            </div>

            <!-- Password Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-900">Password</h2>
                    <p class="text-xs text-gray-600">Ubah password untuk keamanan akun</p>
                </div>
                <div class="px-6 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-700">Password</p>
                            <p class="text-sm text-gray-900 mt-1">••••••••</p>
                        </div>
                        <button onclick="openPasswordModal()"
                            class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                </path>
                            </svg>
                            Ubah Password
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Username Modal -->
        <div id="usernameModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeUsernameModal()">
                </div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md mx-auto">
                    <form action="{{ route('admin.profile.update-username') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-heading font-semibold text-gray-900">Ubah Username</h3>
                                <button type="button" onclick="closeUsernameModal()"
                                    class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Password Saat Ini <span class="text-red-500">*</span>
                                    </label>
                                    <input type="password" name="current_password"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Masukkan password saat ini" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Username Baru <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="username" value="{{ old('username') }}"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent font-mono"
                                        placeholder="username_baru" required>
                                    <p class="text-xs text-gray-500 mt-1">Hanya huruf, angka, dan underscore</p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                            <button type="button" onclick="closeUsernameModal()"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                Batal
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Modal -->
        <div id="passwordModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closePasswordModal()">
                </div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-md mx-auto">
                    <form action="{{ route('admin.profile.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-heading font-semibold text-gray-900">Ubah Password</h3>
                                <button type="button" onclick="closePasswordModal()"
                                    class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Password Saat Ini <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="current_password" id="current_password"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="Masukkan password saat ini" required>
                                        <button type="button" onclick="togglePassword('current_password')"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Password Baru <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="new_password" id="new_password"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="Masukkan password baru" required>
                                        <button type="button" onclick="togglePassword('new_password')"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Min. 8 karakter, huruf besar, kecil, dan angka
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="new_password_confirmation"
                                            id="new_password_confirmation"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                            placeholder="Konfirmasi password baru" required>
                                        <button type="button" onclick="togglePassword('new_password_confirmation')"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                            <button type="button" onclick="closePasswordModal()"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                Batal
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openUsernameModal() {
                document.getElementById('usernameModal').classList.remove('hidden');
            }

            function closeUsernameModal() {
                document.getElementById('usernameModal').classList.add('hidden');
            }

            function openPasswordModal() {
                document.getElementById('passwordModal').classList.remove('hidden');
            }

            function closePasswordModal() {
                document.getElementById('passwordModal').classList.add('hidden');
            }

            function togglePassword(fieldId) {
                const field = document.getElementById(fieldId);
                field.type = field.type === 'password' ? 'text' : 'password';
            }

            // Close modals on Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeUsernameModal();
                    closePasswordModal();
                }
            });

            // Auto-hide success/error messages after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        </script>
    @endpush
@endsection
