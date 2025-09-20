<header
    class="hidden lg:flex fixed top-0 left-0 right-0 z-[500] bg-white backdrop-blur-md border-b border-gray-100 h-16 items-center justify-between px-6">
    <!-- Left Side - Breadcrumb atau Title (opsional) -->
    <div class="flex items-center space-x-3">
        <img src="{{ asset('logo-primary.png') }}" alt="Logo" class="h-auto w-32">
    </div>

    <!-- Right Side - User Profile -->
    <div class="flex items-center space-x-4">
        <p class="text-sm">Halo, {{ Auth::user()->nama }}</p>
        <!-- User Profile Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="flex items-center space-x-3 p-2 text-sm font-medium text-slate-700 hover:bg-slate-100 rounded-full transition-colors group">
                @if (Auth::user()->foto)
                    <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="{{ Auth::user()->nama }} }}"
                        class="w-10 h-10 rounded-full object-cover border border-slate-200">
                @else
                    <svg class="w-7 h-7 text-slate-600 group-hover:text-slate-800" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z" />
                    </svg>
                @endif
            </button>

            <!-- Dropdown Menu -->
            <div x-cloak x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-3 w-56 bg-white rounded-lg shadow-lg border border-slate-200 py-2 z-50">

                <!-- User Info Header -->
                <div class="px-4 py-3 border-b border-slate-100">
                    <p class="font-medium text-slate-900 truncate">{{ Auth::user()->nama }}</p>
                    <p class="text-sm text-slate-500 truncate">{{ Auth::user()->email }}</p>
                </div>

                <!-- Menu Items -->
                <div class="py-1">
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors group">
                        <svg class="w-4 h-4 mr-3 text-slate-500 group-hover:text-primary-600" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Pengaturan Profil
                    </a>
                </div>

                <!-- Divider -->
                <div class="border-t border-slate-100 my-1"></div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors group">
                        <svg class="w-4 h-4 mr-3 group-hover:text-red-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
