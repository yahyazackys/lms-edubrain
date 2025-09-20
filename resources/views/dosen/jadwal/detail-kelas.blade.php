@extends('layouts.app')

@section('title', 'Detail Kelas - ' . $kelasKuliah->mataKuliah->nama_mata_kuliah)

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header dengan Info Kelas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-3">
                <div class="px-6 py-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <!-- Info Kelas -->
                        <div class="flex items-start space-x-4">
                            <!-- Detail Kelas -->
                            <div class="flex-1 min-w-0">
                                <h1 class="text-lg font-semibold font-heading text-gray-900">
                                    {{ $kelasKuliah->mataKuliah->kode_mata_kuliah }} -
                                    {{ $kelasKuliah->mataKuliah->nama_mata_kuliah }}
                                </h1>
                                <div class="flex sm:flex-row flex-col items-start sm:items-center gap-x-4 gap-y-1 mt-2">
                                    <div class="flex items-center text-xs text-gray-600 gap-1">
                                        <i class="fa-solid fa-users text-xs"></i>
                                        {{ $kelasKuliah->nama_kelas_kuliah }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600 gap-1">
                                        <i class="fa-solid fa-clock text-xs"></i>
                                        {{ ucwords(strtolower($kelasKuliah->hari)) }},
                                        {{ \Carbon\Carbon::parse($kelasKuliah->jam_mulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($kelasKuliah->jam_akhir)->format('H:i') }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600 gap-1">
                                        <i class="fa-solid fa-door-open text-xs"></i>
                                        {{ $kelasKuliah->nama_ruangan }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-600 gap-1">
                                        <i class="fa-solid fa-calendar text-xs"></i>
                                        {{ $kelasKuliah->semester->nama_semester }}
                                    </div>
                                </div>

                                <!-- Status & Info -->
                                <div class="flex items-center gap-3 mt-3">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $kelasKuliah->mataKuliah->sks_mata_kuliah }} SKS
                                    </span>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $kelasKuliah->pesertaKelasKuliah->count() }}/{{ $kelasKuliah->kapasitas }}
                                        Mahasiswa
                                    </span>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $kelasKuliah->status === 'selesai' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        <i class="fas fa-check-circle mr-1 text-xs"></i>
                                        {{ ucfirst($kelasKuliah->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 lg:mt-0">
                            <a href="{{ route('jadwal-mengajar.index') }}"
                                class="w-full lg:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Jadwal
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if ($kelasKuliah->status === 'selesai')
                <div class="my-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                        <span class="text-sm text-yellow-800">
                            Kelas ini telah diakhiri. Mode tampilan hanya untuk melihat data.
                        </span>
                    </div>
                </div>
            @endif

            <!-- Tab Navigation dan Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 overflow-hidden font-heading">
                    <!-- Mobile Tab Navigation -->
                    <div class="block sm:hidden">
                        <div class="relative">
                            <div id="mobile-tabs" class="flex space-x-1 px-4 overflow-x-auto scrollbar-hide touch-pan-x"
                                style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch;">
                                <button onclick="switchTab('mahasiswa')"
                                    class="tab-button active whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-mahasiswa">
                                    <i class="fas fa-users w-4 h-4 inline mr-2"></i>
                                    Mahasiswa
                                </button>
                                <button onclick="switchTab('materi')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-materi">
                                    <i class="fas fa-book w-4 h-4 inline mr-2"></i>
                                    Materi
                                </button>
                                <button onclick="switchTab('tugas')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-tugas">
                                    <i class="fas fa-tasks w-4 h-4 inline mr-2"></i>
                                    Tugas
                                </button>
                                <button onclick="switchTab('uts')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-uts">
                                    <i class="fas fa-file-alt w-4 h-4 inline mr-2"></i>
                                    UTS
                                </button>
                                <button onclick="switchTab('uas')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-uas">
                                    <i class="fas fa-graduation-cap w-4 h-4 inline mr-2"></i>
                                    UAS
                                </button>
                                <button onclick="switchTab('absensi')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-absensi">
                                    <i class="fas fa-qrcode w-4 h-4 inline mr-2"></i>
                                    Absensi
                                </button>
                                <button onclick="switchTab('pengaturan')"
                                    class="tab-button whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                    id="tab-pengaturan">
                                    <i class="fas fa-cog w-4 h-4 inline mr-2"></i>
                                    Pengaturan
                                </button>
                            </div>

                            <!-- Scroll Indicators -->
                            <div id="scroll-left"
                                class="absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-r from-white to-transparent pointer-events-none opacity-0 transition-opacity duration-200">
                            </div>
                            <div id="scroll-right"
                                class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-white to-transparent pointer-events-none transition-opacity duration-200">
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Tab Navigation -->
                    <nav class="hidden sm:flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="switchTab('mahasiswa')"
                            class="tab-button active whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-mahasiswa-desktop">
                            <i class="fas fa-users w-4 h-4 inline mr-2"></i>
                            Mahasiswa
                        </button>
                        <button onclick="switchTab('materi')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-materi-desktop">
                            <i class="fas fa-book w-4 h-4 inline mr-2"></i>
                            Materi
                        </button>
                        <button onclick="switchTab('tugas')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-tugas-desktop">
                            <i class="fas fa-tasks w-4 h-4 inline mr-2"></i>
                            Tugas
                        </button>
                        <button onclick="switchTab('uts')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-uts-desktop">
                            <i class="fas fa-file-alt w-4 h-4 inline mr-2"></i>
                            UTS
                        </button>
                        <button onclick="switchTab('uas')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-uas-desktop">
                            <i class="fas fa-graduation-cap w-4 h-4 inline mr-2"></i>
                            UAS
                        </button>
                        <button onclick="switchTab('absensi')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-absensi-desktop">
                            <i class="fas fa-qrcode w-4 h-4 inline mr-2"></i>
                            Absensi
                        </button>
                        <button onclick="switchTab('pengaturan')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            id="tab-pengaturan-desktop">
                            <i class="fas fa-cog w-4 h-4 inline mr-2"></i>
                            Pengaturan
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->

                <!-- Tab 1: Daftar Mahasiswa -->
                <div id="content-mahasiswa" class="tab-content active">
                    <div class="p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 font-heading">Daftar Mahasiswa</h3>
                                <p class="text-sm text-gray-600">Total
                                    {{ $kelasKuliah->pesertaKelasKuliah->count() }} mahasiswa terdaftar</p>
                            </div>
                            <!-- Search dan Filter -->
                            <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row gap-3">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" id="searchMahasiswa"
                                        placeholder="Cari nama atau NIM mahasiswa..."
                                        class="min-w-[280px] block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                </div>
                                <select id="filterProdi"
                                    class="py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    <option value="">Semua Program Studi</option>
                                    @foreach ($kelasKuliah->pesertaKelasKuliah->groupBy('registrasiMahasiswa.mahasiswa.programStudi.nama_program_studi') as $prodi => $mahasiswa)
                                        <option value="{{ $prodi }}">{{ $prodi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if ($kelasKuliah->pesertaKelasKuliah->count() > 0)
                            <!-- Tabel Mahasiswa -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Mahasiswa</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Program Studi</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Angkatan</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Kontak</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="mahasiswaTableBody">
                                        @foreach ($kelasKuliah->pesertaKelasKuliah as $index => $peserta)
                                            <tr class="hover:bg-gray-50 transition-colors duration-200 mahasiswa-row"
                                                data-nama="{{ strtolower($peserta->registrasiMahasiswa->mahasiswa->pengguna->nama) }}"
                                                data-nim="{{ $peserta->registrasiMahasiswa->mahasiswa->nim }}"
                                                data-prodi="{{ $peserta->registrasiMahasiswa->mahasiswa->programStudi->nama_program_studi ?? '' }}">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $index + 1 }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8">
                                                            <div
                                                                class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                                <span class="text-xs font-medium text-gray-700">
                                                                    {{ strtoupper(substr($peserta->registrasiMahasiswa->mahasiswa->pengguna->nama, 0, 2)) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="ml-3">
                                                            <div class="text-xs font-medium text-gray-900">
                                                                {{ $peserta->registrasiMahasiswa->mahasiswa->pengguna->nama }}
                                                            </div>
                                                            <div class="text-xs font-medium text-gray-500">
                                                                {{ $peserta->registrasiMahasiswa->mahasiswa->nim }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $peserta->registrasiMahasiswa->mahasiswa->programStudi->nama_program_studi ?? '' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $peserta->registrasiMahasiswa->mahasiswa->angkatan ?? '' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    <div class="flex flex-col space-y-1">
                                                        @if ($peserta->registrasiMahasiswa->mahasiswa->pengguna->email)
                                                            <div class="flex items-center">
                                                                <i class="fas fa-envelope text-xs mr-2"></i>
                                                                <span
                                                                    class="text-xs">{{ $peserta->registrasiMahasiswa->mahasiswa->pengguna->email }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($peserta->registrasiMahasiswa->mahasiswa->pengguna->no_hp)
                                                            <div class="flex items-center">
                                                                <i class="fas fa-phone text-xs mr-1"></i>
                                                                <span
                                                                    class="text-xs">{{ $peserta->registrasiMahasiswa->mahasiswa->pengguna->no_hp }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-12">
                                <i class="fas fa-user-graduate text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mahasiswa</h3>
                                <p class="text-sm text-gray-500">Belum ada mahasiswa yang terdaftar di kelas ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 2: Materi -->
                <div id="content-materi" class="tab-content hidden">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 font-heading">Manajemen Materi</h3>
                                <p class="text-sm text-gray-600">
                                    @if ($kelasKuliah->status === 'selesai')
                                        Lihat materi pembelajaran (Mode Read-Only)
                                    @else
                                        Kelola materi pembelajaran mahasiswa
                                    @endif
                                </p>
                            </div>
                            @if ($kelasKuliah->status === 'aktif')
                                <button onclick="openMateriModal()"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah Materi
                                </button>
                            @endif
                        </div>

                        @if ($materis->count() > 0)
                            <!-- Tabel Materi -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Judul Materi</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deskripsi</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal Upload</th>
                                            @if ($kelasKuliah->status === 'aktif')
                                                <th
                                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($materis as $index => $materi)
                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $index + 1 }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs font-medium text-gray-900">{{ $materi->judul }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-xs text-gray-900">
                                                        {{ Str::limit($materi->deskripsi, 50) ?? 'Tidak ada deskripsi' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if ($materi->dokumen)
                                                        <div class="flex items-center">
                                                            <i class="fas fa-file text-gray-400 mr-2"></i>
                                                            <a href="{{ route('materi.download', $materi->id_materi) }}"
                                                                class="text-xs text-blue-600 hover:text-blue-800 underline">
                                                                Download
                                                            </a>
                                                        </div>
                                                    @else
                                                        <span class="text-sm text-gray-400">Tidak ada file</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $materi->created_at->format('d/m/Y H:i') }}
                                                </td>
                                                @if ($kelasKuliah->status === 'aktif')
                                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                                        <div class="flex items-center justify-center space-x-2">
                                                            {{-- Tombol Edit --}}
                                                            <div class="relative group">
                                                                <button
                                                                    onclick="openEditMateriModal('{{ $materi->id_materi }}', '{{ addslashes($materi->judul) }}', '{{ addslashes($materi->deskripsi) }}', '{{ $materi->dokumen }}')"
                                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                    <i class="fas fa-edit text-xs"></i>
                                                                </button>
                                                                <div
                                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                    Edit Materi
                                                                    <div
                                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Tombol Hapus --}}
                                                            <div class="relative group">
                                                                <button
                                                                    onclick="openDeleteMateriModal('{{ $materi->id_materi }}', '{{ addslashes($materi->judul) }}', '{{ addslashes($materi->deskripsi) }}', '{{ $materi->dokumen }}')"
                                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                    <i class="fas fa-trash text-xs"></i>
                                                                </button>
                                                                <div
                                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                    Hapus Materi
                                                                    <div
                                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-16">
                                <div
                                    class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-book text-gray-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Materi</h3>
                                @if ($kelasKuliah->status === 'aktif')
                                    <p class="text-sm text-gray-500 mb-6">Mulai tambahkan materi pembelajaran untuk kelas
                                        ini</p>
                                @else
                                    <p class="text-sm text-gray-500 mb-6">Tidak ada materi yang tersedia untuk kelas ini
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 3: Tugas -->
                <div id="content-tugas" class="tab-content hidden">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 font-heading">Manajemen Tugas</h3>
                                <p class="text-sm text-gray-600">
                                    @if ($kelasKuliah->status === 'selesai')
                                        Lihat tugas dan penilaian mahasiswa (Mode Read-Only)
                                    @else
                                        Kelola tugas dan penilaian mahasiswa
                                    @endif
                                </p>
                            </div>
                            @if ($kelasKuliah->status === 'aktif')
                                <button onclick="openTugasModal()"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                    <i class="fas fa-plus mr-2"></i>
                                    Buat Tugas Baru
                                </button>
                            @endif
                        </div>

                        @if ($tugas->count() > 0)
                            <!-- Tabel Tugas -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Judul Tugas</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deskripsi</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File Soal</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deadline</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($tugas as $index => $tugasItem)
                                            @php
                                                $isExpired = $tugasItem->batas_akhir_pengumpulan < now();
                                                $deadlineClass = $isExpired ? 'text-red-600' : 'text-gray-900';
                                                $statusClass = $isExpired
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-green-100 text-green-800';
                                                $statusText = $isExpired ? 'Expired' : 'Aktif';
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $index + 1 }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs font-medium text-gray-900">{{ $tugasItem->judul }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-xs text-gray-900">
                                                        {{ Str::limit($tugasItem->deskripsi, 60) }}</div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if ($tugasItem->dokumen)
                                                        <div class="flex items-center">
                                                            <i class="fas fa-file text-gray-400 mr-2"></i>
                                                            <a href="{{ route('tugas.download', $tugasItem->id_tugas) }}"
                                                                class="text-xs text-blue-600 hover:text-blue-800 underline">Download</a>
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-400">Tidak ada file</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs {{ $deadlineClass }}">
                                                        {{ $tugasItem->batas_akhir_pengumpulan->format('d/m/Y H:i') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        ({{ $tugasItem->batas_akhir_pengumpulan->diffForHumans() }})
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        {{-- Tombol View Status (selalu aktif) --}}
                                                        <div class="relative group">
                                                            <button
                                                                onclick="openStatusPengumpulanModal('{{ $tugasItem->id_tugas }}')"
                                                                class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                <i class="fa-solid fa-list text-xs"></i>
                                                            </button>
                                                            <div
                                                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                Lihat Pengumpulan
                                                                <div
                                                                    class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @if ($kelasKuliah->status === 'aktif')
                                                            {{-- Tombol Edit --}}
                                                            <div class="relative group">
                                                                <button
                                                                    onclick="openEditTugasModal('{{ $tugasItem->id_tugas }}', '{{ addslashes($tugasItem->judul) }}', '{{ addslashes($tugasItem->deskripsi) }}', '{{ $tugasItem->dokumen }}', '{{ $tugasItem->batas_akhir_pengumpulan->toISOString() }}')"
                                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                    <i class="fas fa-edit text-xs"></i>
                                                                </button>
                                                                <div
                                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                    Edit Tugas
                                                                    <div
                                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Tombol Hapus --}}
                                                            <div class="relative group">
                                                                <button
                                                                    onclick="openDeleteTugasModal('{{ $tugasItem->id_tugas }}', '{{ addslashes($tugasItem->judul) }}', '{{ $tugasItem->dokumen }}', '{{ $tugasItem->batas_akhir_pengumpulan->toISOString() }}')"
                                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                    <i class="fas fa-trash text-xs"></i>
                                                                </button>
                                                                <div
                                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                    Hapus Tugas
                                                                    <div
                                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-16">
                                <div
                                    class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-tasks text-gray-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Tugas</h3>
                                @if ($kelasKuliah->status === 'aktif')
                                    <p class="text-sm text-gray-500 mb-6">Buat tugas pertama untuk mahasiswa di kelas ini
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500 mb-6">Tidak ada tugas yang tersedia untuk kelas ini</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 4: UTS -->
                <div id="content-uts" class="tab-content hidden">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 font-heading">Manajemen UTS</h3>
                                <p class="text-sm text-gray-600">
                                    @if ($kelasKuliah->status === 'selesai')
                                        Lihat UTS dan penilaian mahasiswa (Mode Read-Only)
                                    @else
                                        Kelola UTS dan penilaian mahasiswa
                                    @endif
                                </p>
                            </div>
                            @if ($kelasKuliah->status === 'aktif')
                                <button onclick="openUtsModal()"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                    <i class="fas fa-plus mr-2"></i>
                                    Buat UTS Baru
                                </button>
                            @endif
                        </div>

                        @if ($uts->count() > 0)
                            <!-- Tabel UTS -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Judul UTS</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deskripsi</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File Soal</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deadline</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($uts as $index => $utsItem)
                                            @php
                                                $isExpired = $utsItem->batas_akhir_pengumpulan < now();
                                                $deadlineClass = $isExpired ? 'text-red-600' : 'text-gray-900';
                                                $statusClass = $isExpired
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-green-100 text-green-800';
                                                $statusText = $isExpired ? 'Expired' : 'Aktif';
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs font-medium text-gray-900">{{ $utsItem->judul }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-xs text-gray-900">
                                                        {{ Str::limit($utsItem->deskripsi, 60) }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if ($utsItem->dokumen)
                                                        <div class="flex items-center">
                                                            <i class="fas fa-file text-gray-400 mr-2"></i>
                                                            <a href="{{ route('uts.download', $utsItem->id_uts) }}"
                                                                class="text-xs text-blue-600 hover:text-blue-800 underline">
                                                                Download
                                                            </a>
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-400">Tidak ada file</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs {{ $deadlineClass }}">
                                                        {{ $utsItem->batas_akhir_pengumpulan->format('d/m/Y H:i') }}
                                                    </div>
                                                    @if ($isExpired)
                                                        <div class="text-xs text-red-500 font-medium">
                                                            ({{ $utsItem->batas_akhir_pengumpulan->diffForHumans() }})
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-gray-500">
                                                            ({{ $utsItem->batas_akhir_pengumpulan->diffForHumans() }})
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        <div class="relative group">
                                                            <button
                                                                onclick="openStatusPengumpulanUtsModal('{{ $utsItem->id_uts }}')"
                                                                class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                <i class="fa-solid fa-list text-xs"></i>
                                                            </button>
                                                            <div
                                                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                Lihat Pengumpulan
                                                                <div
                                                                    class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @if ($kelasKuliah->status === 'aktif')
                                                            {{-- Tombol Edit --}}
                                                            <div class="relative group">
                                                                <button
                                                                    onclick="openEditUtsModal(
                                                                    '{{ $utsItem->id_uts }}', 
                                                                    '{{ addslashes($utsItem->judul) }}', 
                                                                    '{{ addslashes($utsItem->deskripsi) }}', 
                                                                    '{{ $utsItem->dokumen }}', 
                                                                    '{{ $utsItem->batas_akhir_pengumpulan->toISOString() }}'
                                                                )"
                                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                    <i class="fas fa-edit text-xs"></i>
                                                                </button>
                                                                <div
                                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                    Edit UTS
                                                                    <div
                                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Tombol Hapus --}}
                                                            <div class="relative group">
                                                                <button
                                                                    onclick="openDeleteUtsModal(
                                                                    '{{ $utsItem->id_uts }}', 
                                                                    '{{ addslashes($utsItem->judul) }}', 
                                                                    '{{ $utsItem->dokumen }}', 
                                                                    '{{ $utsItem->batas_akhir_pengumpulan->toISOString() }}'
                                                                )"
                                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                    <i class="fas fa-trash text-xs"></i>
                                                                </button>
                                                                <div
                                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                    Hapus UTS
                                                                    <div
                                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-16">
                                <div
                                    class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-file-alt text-gray-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada UTS</h3>
                                <p class="text-sm text-gray-500 mb-6">Buat UTS pertama untuk mahasiswa di kelas ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 5: UAS -->
                <div id="content-uas" class="tab-content hidden">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 font-heading">Manajemen UAS</h3>
                                <p class="text-sm text-gray-600">
                                    @if ($kelasKuliah->status === 'selesai')
                                        Lihat UAS dan penilaian mahasiswa (Mode Read-Only)
                                    @else
                                        Kelola UAS dan penilaian mahasiswa
                                    @endif
                                </p>
                            </div>
                            @if ($kelasKuliah->status === 'aktif')
                                <button onclick="openUasModal()"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                    <i class="fas fa-plus mr-2"></i>
                                    Buat UAS Baru
                                </button>
                            @endif
                        </div>

                        @if ($uas->count() > 0)
                            <!-- Tabel UAS -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Judul UAS</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deskripsi</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                File Soal</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Deadline</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($uas as $index => $uasItem)
                                            @php
                                                $isExpired = $uasItem->batas_akhir_pengumpulan < now();
                                                $deadlineClass = $isExpired ? 'text-red-600' : 'text-gray-900';
                                                $statusClass = $isExpired
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-green-100 text-green-800';
                                                $statusText = $isExpired ? 'Expired' : 'Aktif';
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs font-medium text-gray-900">{{ $uasItem->judul }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-xs text-gray-900">
                                                        {{ Str::limit($uasItem->deskripsi, 60) }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if ($uasItem->dokumen)
                                                        <div class="flex items-center">
                                                            <i class="fas fa-file text-gray-400 mr-2"></i>
                                                            <a href="{{ route('uas.download', $uasItem->id_uas) }}"
                                                                class="text-xs text-blue-600 hover:text-blue-800 underline">
                                                                Download
                                                            </a>
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-400">Tidak ada file</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs {{ $deadlineClass }}">
                                                        {{ $uasItem->batas_akhir_pengumpulan->format('d/m/Y H:i') }}
                                                    </div>
                                                    @if ($isExpired)
                                                        <div class="text-xs text-red-500 font-medium">
                                                            ({{ $uasItem->batas_akhir_pengumpulan->diffForHumans() }})
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-gray-500">
                                                            ({{ $uasItem->batas_akhir_pengumpulan->diffForHumans() }})
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        {{-- Tombol View Status --}}
                                                        <div class="relative group">
                                                            <button
                                                                onclick="openStatusPengumpulanUasModal('{{ $uasItem->id_uas }}')"
                                                                class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                <i class="fa-solid fa-list text-xs"></i>
                                                            </button>
                                                            <div
                                                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                Lihat Pengumpulan
                                                                <div
                                                                    class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @if ($kelasKuliah->status === 'aktif')
                                                            {{-- Tombol Edit --}}
                                                            <div class="relative group">
                                                                <button
                                                                    onclick="openEditUasModal(
                                                                    '{{ $uasItem->id_uas }}', 
                                                                    '{{ addslashes($uasItem->judul) }}', 
                                                                    '{{ addslashes($uasItem->deskripsi) }}', 
                                                                    '{{ $uasItem->dokumen }}', 
                                                                    '{{ $uasItem->batas_akhir_pengumpulan->toISOString() }}'
                                                                )"
                                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                    <i class="fas fa-edit text-xs"></i>
                                                                </button>
                                                                <div
                                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                    Edit UAS
                                                                    <div
                                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Tombol Hapus --}}
                                                            <div class="relative group">
                                                                <button
                                                                    onclick="openDeleteUasModal(
                                                                    '{{ $uasItem->id_uas }}', 
                                                                    '{{ addslashes($uasItem->judul) }}', 
                                                                    '{{ $uasItem->dokumen }}', 
                                                                    '{{ $uasItem->batas_akhir_pengumpulan->toISOString() }}'
                                                                )"
                                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                    <i class="fas fa-trash text-xs"></i>
                                                                </button>
                                                                <div
                                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                    Hapus UAS
                                                                    <div
                                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-16">
                                <div
                                    class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-graduation-cap text-gray-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada UAS</h3>
                                <p class="text-sm text-gray-500 mb-6">Buat UAS pertama untuk mahasiswa di kelas ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 6: Absensi -->
                <div id="content-absensi" class="tab-content hidden">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 font-heading">Manajemen Absensi</h3>
                                <p class="text-sm text-gray-600">
                                    @if ($kelasKuliah->status === 'selesai')
                                        Lihat sesi absensi dan kehadiran mahasiswa (Mode Read-Only)
                                    @else
                                        Kelola sesi absensi dan kehadiran mahasiswa
                                    @endif
                                </p>
                            </div>
                            @if ($kelasKuliah->status === 'aktif')
                                <button onclick="openSesiAbsensiModal()"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                    <i class="fas fa-plus mr-2"></i>
                                    Buat Sesi Baru
                                </button>
                            @endif
                        </div>

                        @if ($sesiAbsensi && $sesiAbsensi->count() > 0)
                            <!-- Tabel Sesi Absensi -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Topik Pertemuan</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal Dibuat</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Batas Akhir</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Kehadiran</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($sesiAbsensi as $index => $sesi)
                                            @php
                                                // ✅ Safe null checks untuk mencegah error
                                                $isExpired = $sesi->batas_akhir_absensi
                                                    ? $sesi->batas_akhir_absensi < now()
                                                    : false;
                                                $isOpen = $sesi->status === 'dibuka';
                                                $deadlineClass = $isExpired ? 'text-red-600' : 'text-gray-900';

                                                // Status logic
                                                if ($isExpired) {
                                                    $statusClass = 'bg-red-100 text-red-800';
                                                    $statusText = 'Expired';
                                                    $statusIcon = 'clock';
                                                } elseif ($isOpen) {
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                    $statusText = 'Dibuka';
                                                    $statusIcon = 'check-circle';
                                                } else {
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                    $statusText = 'Ditutup';
                                                    $statusIcon = 'pause-circle';
                                                }

                                                // ✅ Safe kehadiran calculation dengan null checks
                                                $totalMahasiswa = 0;
                                                $hadir = 0;
                                                $persentaseKehadiran = 0;

                                                if ($kelasKuliah && $kelasKuliah->pesertaKelasKuliah) {
                                                    $totalMahasiswa = $kelasKuliah->pesertaKelasKuliah->count();
                                                }

                                                if ($sesi && $sesi->absensi) {
                                                    $hadir = $sesi->absensi->where('is_hadir', true)->count();
                                                }

                                                if ($totalMahasiswa > 0) {
                                                    $persentaseKehadiran = round(($hadir / $totalMahasiswa) * 100, 1);
                                                }
                                            @endphp

                                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="text-xs font-medium text-gray-900">
                                                        {{ $sesi->topik ?: 'Pertemuan ' . ($index + 1) }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $sesi->created_at ? $sesi->created_at->format('d/m/Y H:i') : '-' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-xs text-gray-900">
                                                        {{ $sesi->created_at ? $sesi->created_at->format('d/m/Y') : '-' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $sesi->created_at ? $sesi->created_at->format('H:i') : '-' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if ($sesi->batas_akhir_absensi)
                                                        <div class="text-xs {{ $deadlineClass }}">
                                                            {{ $sesi->batas_akhir_absensi->format('d/m/Y H:i') }}
                                                        </div>
                                                        @if ($isExpired)
                                                            <div class="text-xs text-red-500 font-medium">
                                                                ({{ $sesi->batas_akhir_absensi->diffForHumans() }})
                                                            </div>
                                                        @else
                                                            <div class="text-xs text-gray-500">
                                                                ({{ $sesi->batas_akhir_absensi->diffForHumans() }})
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="text-xs text-gray-500">Tidak diatur</div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                        <i class="fas fa-{{ $statusIcon }} mr-1"></i>
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    <div class="text-xs font-medium text-gray-900">
                                                        {{ $hadir }}/{{ $totalMahasiswa }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        {{-- Tombol View Status (selalu aktif) --}}
                                                        <div class="relative group">
                                                            <button
                                                                onclick="openStatusAbsensiModal('{{ $sesi->id_sesi_absensi }}')"
                                                                class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                <i class="fa-solid fa-list text-xs"></i>
                                                            </button>
                                                            <div
                                                                class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                Lihat Detail Kehadiran
                                                                <div
                                                                    class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @if ($kelasKuliah->status === 'aktif')
                                                            {{-- QR Code Button --}}
                                                            <div class="relative group">
                                                                <button
                                                                    onclick="openQrCodeModal('{{ $sesi->id_sesi_absensi }}')"
                                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                    <i class="fas fa-qrcode text-xs"></i>
                                                                </button>
                                                                <div
                                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                    Tampilkan QR Code
                                                                    <div
                                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Toggle Status Button --}}
                                                            <div class="relative group">
                                                                <button
                                                                    onclick="toggleSesiStatus('{{ $sesi->id_sesi_absensi }}', '{{ $sesi->status }}')"
                                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                    <i
                                                                        class="fas fa-{{ $sesi->status === 'dibuka' ? 'pause' : 'play' }} text-xs"></i>
                                                                </button>
                                                                <div
                                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                    {{ $sesi->status === 'dibuka' ? 'Tutup Sesi' : 'Buka Sesi' }}
                                                                    <div
                                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Edit Button --}}
                                                            <div class="relative group">
                                                                <button
                                                                    onclick="openEditSesiAbsensiModal('{{ $sesi->id_sesi_absensi }}', '{{ addslashes($sesi->topik ?? '') }}', '{{ $sesi->batas_akhir_absensi ? $sesi->batas_akhir_absensi->toISOString() : '' }}')"
                                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                    <i class="fas fa-edit text-xs"></i>
                                                                </button>
                                                                <div
                                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                    Edit Sesi
                                                                    <div
                                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Delete Button --}}
                                                            <div class="relative group">
                                                                <button
                                                                    onclick="openDeleteSesiAbsensiModal('{{ $sesi->id_sesi_absensi }}', '{{ addslashes($sesi->topik ?? '') }}', '{{ $sesi->batas_akhir_absensi ? $sesi->batas_akhir_absensi->toISOString() : '' }}', '{{ $sesi->status }}')"
                                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                                    <i class="fas fa-trash text-xs"></i>
                                                                </button>
                                                                <div
                                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                                    Hapus Sesi
                                                                    <div
                                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-16">
                                <div
                                    class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-qrcode text-gray-600 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Sesi Absensi</h3>
                                @if ($kelasKuliah->status === 'aktif')
                                    <p class="text-sm text-gray-500 mb-6">Buat sesi absensi pertama untuk kelas ini</p>
                                @else
                                    <p class="text-sm text-gray-500 mb-6">Tidak ada sesi absensi yang tersedia untuk kelas
                                        ini</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 7: Pengaturan Kelas -->
                <div id="content-pengaturan" class="tab-content hidden">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 font-heading mb-6">Pengaturan Kelas</h3>

                        <!-- Bobot Penilaian -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-md font-semibold text-gray-900">Bobot Penilaian</h4>
                                @if ($kelasKuliah->status === 'aktif')
                                    <button onclick="openBobotModal()"
                                        class="inline-flex items-center px-3 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <i class="fas fa-edit mr-2"></i>
                                        Edit Bobot
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-900">{{ $kelasKuliah->bobot_absensi }}%
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">Absensi</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-900">{{ $kelasKuliah->bobot_tugas }}%</div>
                                    <div class="text-sm text-gray-600 mt-1">Tugas</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-900">{{ $kelasKuliah->bobot_uts }}%</div>
                                    <div class="text-sm text-gray-600 mt-1">UTS</div>
                                </div>
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-900">{{ $kelasKuliah->bobot_uas }}%</div>
                                    <div class="text-sm text-gray-600 mt-1">UAS</div>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">Total Bobot:</span>
                                    <span class="text-lg font-bold text-gray-900">
                                        {{ $kelasKuliah->bobot_absensi + $kelasKuliah->bobot_tugas + $kelasKuliah->bobot_uts + $kelasKuliah->bobot_uas }}%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Kelas & Actions -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Status & Aksi Kelas</h4>

                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        @if ($kelasKuliah->status === 'selesai')
                                            <i class="fas fa-check-circle text-gray-600"></i>
                                            <span class="text-gray-900 font-medium">Status: Kelas Selesai</span>
                                        @else
                                            <i class="fas fa-play-circle text-green-600"></i>
                                            <span class="text-gray-900 font-medium">Status: Kelas Aktif</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        @if ($kelasKuliah->status === 'selesai')
                                            Kelas telah diakhiri. Semua fitur pengelolaan dinonaktifkan.
                                        @else
                                            Kelas masih aktif. Anda dapat mengelola semua konten dan mengakhiri kelas.
                                        @endif
                                    </p>
                                </div>

                                @if ($kelasKuliah->status === 'aktif')
                                    <button onclick="confirmAkhiriKelas()"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <i class="fas fa-stop-circle mr-2"></i>
                                        Akhiri Kelas
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Informasi Tambahan -->
                        @if ($kelasKuliah->status === 'aktif')
                            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                                    <div>
                                        <h5 class="font-medium text-yellow-800">Penting!</h5>
                                        <p class="text-sm text-yellow-700 mt-1">
                                            Setelah kelas diakhiri, Anda tidak akan dapat lagi menambah, mengedit, atau
                                            menghapus
                                            materi, tugas, UTS, UAS, dan sesi absensi. Pastikan semua data sudah lengkap
                                            sebelum mengakhiri kelas.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dosen.jadwal.modals.materi-modal')
    @include('dosen.jadwal.modals.tugas-modal')
    @include('dosen.jadwal.modals.uts-modal')
    @include('dosen.jadwal.modals.uas-modal')
    @include('dosen.jadwal.modals.sesi-absensi-modal')
    @include('dosen.jadwal.modals.pengaturan-modal')

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

            @media print {
                .no-print {
                    display: none !important;
                }

                .print-content {
                    display: block !important;
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
            }
        </style>
    @endpush

    @push('scripts')
        {{-- <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script> --}}
        <script>
            // Global variables
            let currentTab = 'mahasiswa';
            let qrCodeActive = false;

            // Utility function to get URL parameters
            function getUrlParameter(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }

            // Utility function to update URL parameter without page refresh
            function updateUrlParameter(key, value) {
                const url = new URL(window.location);
                url.searchParams.set(key, value);

                // Update URL without refreshing the page
                window.history.pushState({
                    path: url.href
                }, '', url.href);
            }

            // Initialize page
            document.addEventListener('DOMContentLoaded', function() {
                setupMobileTabs();
                setupMahasiswaSearch();

                // Check for tab parameter in URL, default to 'mahasiswa' if not present
                const urlTab = getUrlParameter('tab');
                const initialTab = urlTab && isValidTab(urlTab) ? urlTab : 'mahasiswa';

                switchTab(initialTab);
            });

            // Check if tab name is valid
            function isValidTab(tabName) {
                const validTabs = ['mahasiswa', 'materi', 'tugas', 'uts', 'uas', 'absensi', 'pengaturan'];
                return validTabs.includes(tabName);
            }

            // Handle browser back/forward navigation
            window.addEventListener('popstate', function(event) {
                const urlTab = getUrlParameter('tab');
                const targetTab = urlTab && isValidTab(urlTab) ? urlTab : 'mahasiswa';

                // Switch tab without updating URL (to avoid infinite loop)
                switchTabWithoutUrlUpdate(targetTab);
            });

            // Enhanced tab switching with URL update
            function switchTab(tabName) {
                // Update URL parameter
                updateUrlParameter('tab', tabName);

                // Perform the actual tab switch
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

                // Update page title based on active tab
                updatePageTitle(tabName);
            }

            // Update page title based on active tab
            function updatePageTitle(tabName) {
                const tabTitles = {
                    'mahasiswa': 'Daftar Mahasiswa',
                    'materi': 'Materi Pembelajaran',
                    'tugas': 'Tugas',
                    'uts': 'UTS',
                    'uas': 'UAS',
                    'absensi': 'Absensi',
                    'pengaturan': 'Pengaturan Kelas'
                };

                const baseTitle = document.title.split(' - ')[0]; // Get original title
                document.title = `${baseTitle} - ${tabTitles[tabName] || 'Detail Kelas'}`;
            }

            // Mobile tabs setup
            function setupMobileTabs() {
                const mobileTabsContainer = document.getElementById('mobile-tabs');
                const scrollLeft = document.getElementById('scroll-left');
                const scrollRight = document.getElementById('scroll-right');

                if (mobileTabsContainer) {
                    function updateScrollIndicators() {
                        const canScrollLeft = mobileTabsContainer.scrollLeft > 0;
                        const canScrollRight = mobileTabsContainer.scrollLeft <
                            (mobileTabsContainer.scrollWidth - mobileTabsContainer.clientWidth);

                        if (scrollLeft) scrollLeft.style.opacity = canScrollLeft ? '1' : '0';
                        if (scrollRight) scrollRight.style.opacity = canScrollRight ? '1' : '0';
                    }

                    mobileTabsContainer.addEventListener('scroll', updateScrollIndicators);
                    window.addEventListener('resize', updateScrollIndicators);
                    updateScrollIndicators();
                }
            }

            // Mahasiswa search and filter
            function setupMahasiswaSearch() {
                const searchInput = document.getElementById('searchMahasiswa');
                const filterSelect = document.getElementById('filterProdi');

                if (searchInput) {
                    searchInput.addEventListener('input', filterMahasiswa);
                }

                if (filterSelect) {
                    filterSelect.addEventListener('change', filterMahasiswa);
                }
            }

            function filterMahasiswa() {
                const searchTerm = document.getElementById('searchMahasiswa')?.value.toLowerCase() || '';
                const prodiFilter = document.getElementById('filterProdi')?.value || '';
                const rows = document.querySelectorAll('.mahasiswa-row');

                rows.forEach(row => {
                    const nama = row.getAttribute('data-nama') || '';
                    const nim = row.getAttribute('data-nim') || '';
                    const prodi = row.getAttribute('data-prodi') || '';

                    const matchesSearch = nama.includes(searchTerm) || nim.includes(searchTerm);
                    const matchesProdi = !prodiFilter || prodi === prodiFilter;

                    if (matchesSearch && matchesProdi) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update row numbers
                const visibleRows = document.querySelectorAll('.mahasiswa-row[style=""], .mahasiswa-row:not([style])');
                visibleRows.forEach((row, index) => {
                    const numberCell = row.querySelector('td:first-child');
                    if (numberCell) {
                        numberCell.textContent = index + 1;
                    }
                });
            }

            // Enhanced modal functions with URL preservation
            function openMateriModal() {
                document.getElementById('materiModal').classList.remove('hidden');
            }

            function closeMateriModal() {
                document.getElementById('materiModal').classList.add('hidden');
                document.getElementById('materiForm').reset();
            }

            function openTugasModal() {
                document.getElementById('tugasModal').classList.remove('hidden');
            }

            function closeTugasModal() {
                document.getElementById('tugasModal').classList.add('hidden');
                document.getElementById('tugasForm').reset();
            }

            function openUtsModal() {
                document.getElementById('utsModal').classList.remove('hidden');
            }

            function closeUtsModal() {
                document.getElementById('utsModal').classList.add('hidden');
                document.getElementById('utsForm').reset();
            }

            function openUasModal() {
                document.getElementById('uasModal').classList.remove('hidden');
            }

            function closeUasModal() {
                document.getElementById('uasModal').classList.add('hidden');
                document.getElementById('uasForm').reset();
            }

            function openSesiAbsensiModal() {
                document.getElementById('sesiAbsensiModal').classList.remove('hidden');
            }

            function closeSesiAbsensiModal() {
                document.getElementById('sesiAbsensiModal').classList.add('hidden');
                document.getElementById('sesiAbsensiForm').reset();
            }

            // Form submission handlers that preserve tab state
            function handleFormSubmission(formId) {
                const form = document.getElementById(formId);
                if (form) {
                    form.addEventListener('submit', function(e) {
                        // Add current tab as hidden input to preserve state after form submission
                        const currentTabInput = form.querySelector('input[name="current_tab"]');
                        if (currentTabInput) {
                            currentTabInput.value = currentTab;
                        } else {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'current_tab';
                            hiddenInput.value = currentTab;
                            form.appendChild(hiddenInput);
                        }
                    });
                }
            }

            // Initialize form submission handlers
            document.addEventListener('DOMContentLoaded', function() {
                handleFormSubmission('materiForm');
                handleFormSubmission('tugasForm');
                handleFormSubmission('utsForm');
                handleFormSubmission('uasForm');
                handleFormSubmission('sesiAbsensiForm');
            });

            // QR Code functions
            function generateQRCode() {
                if (qrCodeActive) {
                    // Deactivate QR Code
                    qrCodeActive = false;
                    document.getElementById('qr-code-container').classList.add('hidden');
                    document.getElementById('qr-placeholder').classList.remove('hidden');
                    document.getElementById('qr-status').textContent = 'Tidak Aktif';
                    document.getElementById('qr-validity').textContent = '-';

                    const button = document.getElementById('qr-button');
                    button.innerHTML = '<i class="fas fa-qrcode mr-2"></i>Generate QR Code';
                    button.className =
                        'w-full inline-flex justify-center items-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900';
                } else {
                    // Generate new QR Code
                    const qrData = {
                        kelas_id: '{{ $kelasKuliah->id_kelas_kuliah }}',
                        timestamp: new Date().getTime(),
                        session: Math.random().toString(36).substr(2, 9)
                    };

                    const qrString = JSON.stringify(qrData);
                    const canvas = document.getElementById('qr-code');

                    QRCode.toCanvas(canvas, qrString, {
                        width: 200,
                        margin: 2,
                        color: {
                            dark: '#000000',
                            light: '#FFFFFF'
                        }
                    }, function(error) {
                        if (error) {
                            console.error(error);
                            alert('Error generating QR Code');
                            return;
                        }

                        // Show QR Code
                        qrCodeActive = true;
                        document.getElementById('qr-placeholder').classList.add('hidden');
                        document.getElementById('qr-code-container').classList.remove('hidden');
                        document.getElementById('qr-status').textContent = 'Aktif';

                        // Set validity time (30 minutes)
                        const validUntil = new Date();
                        validUntil.setMinutes(validUntil.getMinutes() + 30);
                        document.getElementById('qr-validity').textContent = validUntil.toLocaleTimeString('id-ID');

                        // Update button
                        const button = document.getElementById('qr-button');
                        button.innerHTML = '<i class="fas fa-times mr-2"></i>Nonaktifkan QR';
                        button.className =
                            'w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-900';

                        // Auto expire after 30 minutes
                        setTimeout(() => {
                            if (qrCodeActive) {
                                generateQRCode(); // This will deactivate it
                            }
                        }, 30 * 60 * 1000);
                    });
                }
            }
            // Keyboard shortcuts
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeMateriModal();
                    closeTugasModal();
                    closeUtsModal();
                    closeUasModal();
                    closeSesiAbsensiModal();
                }
            });

            // Make functions global
            window.switchTab = switchTab;
            window.openMateriModal = openMateriModal;
            window.closeMateriModal = closeMateriModal;
            window.openTugasModal = openTugasModal;
            window.closeTugasModal = closeTugasModal;
            window.openUtsModal = openUtsModal;
            window.closeUtsModal = closeUtsModal;
            window.openUasModal = openUasModal;
            window.closeUasModal = closeUasModal;
            window.openSesiAbsensiModal = openSesiAbsensiModal;
            window.closeSesiAbsensiModal = closeSesiAbsensiModal;
        </script>
    @endpush
@endsection

@push('scripts')
    @include('dosen.jadwal.scripts.materi-script')
    @include('dosen.jadwal.scripts.tugas-script')
    @include('dosen.jadwal.scripts.uts-script')
    @include('dosen.jadwal.scripts.uas-script')
    @include('dosen.jadwal.scripts.sesi-absensi-script')
    @include('dosen.jadwal.scripts.pengaturan-script')
@endpush
