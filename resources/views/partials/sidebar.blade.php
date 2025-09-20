<div x-data="{ sidebarOpen: false }" class="relative font-heading">

    <!-- Mobile Header -->
    <nav class="fixed top-0 left-0 right-0 lg:hidden z-30 bg-white backdrop-blur-md border-b border-gray-100">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <img src="{{ asset('logo-primary.png') }}" alt="Logo" class="h-auto w-32">
                </div>

                <div class="flex items-center space-x-2">
                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center p-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-xl transition-colors duration-200">
                            @if (Auth::user()->foto)
                                <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="{{ Auth::user()->foto }}"
                                    class="w-8 h-8 rounded-full object-cover border border-gray-100">
                            @else
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                            @endif
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-cloak x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-lg border border-gray-100 py-2 z-50">

                            <!-- User Info Header -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->nama }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-2">
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-cog w-4 h-4 mr-3 text-gray-500"></i>
                                    Pengaturan Profil
                                </a>
                            </div>

                            <!-- Divider -->
                            <div class="border-t border-gray-100 my-1"></div>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt w-4 h-4 mr-3"></i>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-all duration-200">
                        <i class="fa-solid fa-align-right text-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <nav x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed top-0 left-0 z-[400] w-60 h-screen bg-white transform transition-transform duration-300 backdrop-blur-md ease-in-out lg:translate-x-0 flex flex-col border-r border-gray-100">

        <!-- Sidebar Header -->
        <div class="px-6 py-4 border-b border-gray-100">
            <!-- Logo untuk mobile -->
            <div class="lg:hidden flex items-center justify-between">
                <img src="{{ asset('logo-primary.png') }}" alt="Logo" class="h-auto w-32">

                <!-- Close button (mobile only) -->
                <button @click="sidebarOpen = false"
                    class="p-2 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="flex-1 px-4 py-4 lg:py-12 overflow-y-auto">
            @role('admin')
                <div class="space-y-6">
                    <div class="space-y-2">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700 rounded-xl transition-all duration-200 hover:no-underline relative
                    {{ request()->is('dashboard*') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-chart-simple"></i>
                            </span>

                            <span>Dashboard</span>
                        </a>

                        <!-- Mahasiswa -->
                        <a href="{{ route('mahasiswa.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700 rounded-xl transition-all duration-200 hover:no-underline relative
                    {{ request()->is('mahasiswa*') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-chart-simple"></i>
                            </span>

                            <span>Mahasiswa</span>
                        </a>

                        <!-- Dosen -->
                        <a href="{{ route('dosen.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700 rounded-xl transition-all duration-200 hover:no-underline relative
                    {{ request()->is('dosen*') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-chart-simple"></i>
                            </span>

                            <span>Dosen</span>
                        </a>
                    </div>

                    <!-- Kategori Referensi -->
                    <div class="space-y-2">
                        <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wide">Referensi</h3>

                        <a href="{{ route('semester.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                            {{ request()->is('semester') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-calendar-alt"></i>
                            </span>

                            <span>Semester</span>
                        </a>

                        <a href="{{ route('jenjang.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                            {{ request()->is('jenjang') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </span>

                            <span>Jenjang Pendidikan</span>
                        </a>
                    </div>

                    <!-- Kategori Akademik -->
                    <div class="space-y-2">
                        <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wide">Akademik</h3>

                        <a href="{{ route('program-studi.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                            {{ request()->is('program-studi') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-book-open"></i>
                            </span>

                            <span>Program Studi</span>
                        </a>

                        <a href="{{ route('mata-kuliah.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                            {{ request()->is('mata-kuliah') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-book-open"></i>
                            </span>

                            <span>Mata Kuliah</span>
                        </a>

                        <a href="{{ route('kurikulum.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                            {{ request()->is('kurikulum*') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </span>

                            <span>Kurikulum</span>
                        </a>

                        <a href="{{ route('pembimbing-akademik.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                            {{ request()->is('pembimbing-akademik*') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </span>

                            <span>Pembimbing Akademik</span>
                        </a>

                        <a href="{{ route('admin.akademik.cari-mahasiswa') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                {{ request()->is('admin/akademik*') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </span>
                            <span>Data Akademik Mahasiswa</span>
                        </a>
                    </div>

                    <!-- Kategori Perkuliahan -->
                    <div class="space-y-2">
                        <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wide">Perkuliahan</h3>

                        <a href="{{ route('kelas-kuliah.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                            {{ request()->is('kelas-kuliah') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-book-open"></i>
                            </span>

                            <span>Kelas Perkuliahan</span>
                        </a>
                    </div>
                </div>
            @endrole

            @role('mahasiswa')
                <div class="space-y-6">
                    <div class="space-y-2">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700 rounded-xl transition-all duration-200 hover:no-underline relative
                    {{ request()->is('dashboard*') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-chart-simple"></i>
                            </span>

                            <span>Dashboard</span>
                        </a>
                    </div>

                    <!-- Kategori Akademik -->
                    <div class="space-y-2">
                        <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wide">Akademik</h3>

                        <a href="{{ route('krs.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                            {{ request()->is('krs') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-book-open"></i>
                            </span>

                            <span>Kartu Rencana Studi</span>
                        </a>

                        <a href="{{ route('akademik.khs.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                            {{ request()->is('akademik/khs*') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-book-open"></i>
                            </span>

                            <span>Kartu Hasil Studi</span>
                        </a>

                        <a href="{{ route('akademik.transcript.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                            {{ request()->is('akademik/transcript*') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-book-open"></i>
                            </span>

                            <span>Transkrip</span>
                        </a>
                    </div>

                    <!-- Kategori Perkuliahan -->
                    <div class="space-y-2">
                        <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wide">Perkuliahan</h3>

                        <a href="{{ route('jadwal-kuliah.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                            {{ request()->is('jadwal-kuliah*') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-book-open"></i>
                            </span>

                            <span>Jadwal Kuliah</span>
                        </a>
                    </div>
                </div>
            @endrole

            @role('dosen')
                <div class="space-y-6">
                    <div class="space-y-2">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700 rounded-xl transition-all duration-200 hover:no-underline relative
                    {{ request()->is('dashboard*') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-chart-simple"></i>
                            </span>

                            <span>Dashboard</span>
                        </a>
                    </div>

                    <!-- Kategori Akademik -->
                    <div class="space-y-2">
                        <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wide">Akademik</h3>

                        <a href="{{ route('krs.approval.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                            {{ request()->is('krs*') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-book-open"></i>
                            </span>

                            <span>KRS</span>
                        </a>
                    </div>

                    <!-- Kategori Perkuliahan -->
                    <div class="space-y-2">
                        <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wide">Perkuliahan</h3>

                        <a href="{{ route('jadwal-mengajar.index') }}"
                            class="group flex items-center px-4 py-2 text-sm text-gray-700  rounded-xl transition-all duration-200 hover:no-underline relative
                            {{ request()->is('jadwal-mengajar*') ? 'bg-gray-50 font-semibold shadow-sm' : ' hover:bg-gray-50 hover:text-gray-900 font-medium' }}">

                            <!-- Icon wrapper -->
                            <span class="flex items-center justify-center w-5 h-5 mr-3">
                                <i class="fa-solid fa-book-open"></i>
                            </span>

                            <span>Jadwal Mengajar</span>
                        </a>
                    </div>
                </div>
            @endrole
        </div>
    </nav>

    <!-- Mobile Overlay -->
    <div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-30 lg:hidden"></div>
</div>
