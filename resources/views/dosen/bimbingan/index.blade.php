@extends('layouts.app')

@section('title', 'Mahasiswa Bimbingan')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-lg font-heading font-semibold text-gray-900">Mahasiswa Bimbingan</h1>
                            <p class="text-xs text-gray-600">Kelola mahasiswa bimbingan KKN, Magang, dan Skripsi</p>
                        </div>

                        <div class="flex items-center space-x-4 mt-4 sm:mt-0">
                            <label class="text-xs font-medium text-gray-700">Semester:</label>
                            <select id="semesterFilter" onchange="changeSemester()"
                                class="text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 min-w-[250px]">
                                <option value="">Pilih Semester</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id_semester }}"
                                        {{ $selectedSemesterId == $semester->id_semester ? 'selected' : '' }}>
                                        {{ $semester->nama_semester }}
                                        {{ $semester->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            @if (!$selectedSemester)
                <!-- Empty State - Pilih Semester -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-16 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Pilih Semester</h3>
                        <p class="text-xs text-gray-500">Pilih semester dari dropdown di atas untuk melihat data mahasiswa
                            bimbingan</p>
                    </div>
                </div>
            @else
                <!-- Tab Navigation dan Content -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 font-heading">
                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 overflow-hidden">
                        <!-- Mobile Tab Navigation -->
                        <div class="block sm:hidden">
                            <div class="relative">
                                <div id="mobile-tabs" class="flex space-x-1 px-4 overflow-x-auto scrollbar-hide touch-pan-x"
                                    style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch;">
                                    <button onclick="switchTab('kkn')"
                                        class="tab-button {{ $activeTab === 'kkn' ? 'active' : '' }} whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                        id="tab-kkn">
                                        <i class="fas fa-users w-4 h-4 inline mr-2"></i>
                                        KKN
                                        <span
                                            class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">{{ $stats['total_kkn_kelompok'] }}</span>
                                    </button>
                                    <button onclick="switchTab('magang')"
                                        class="tab-button {{ $activeTab === 'magang' ? 'active' : '' }} whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                        id="tab-magang">
                                        <i class="fas fa-briefcase w-4 h-4 inline mr-2"></i>
                                        Magang
                                        <span
                                            class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">{{ $stats['total_magang'] }}</span>
                                    </button>
                                    <button onclick="switchTab('skripsi')"
                                        class="tab-button {{ $activeTab === 'skripsi' ? 'active' : '' }} whitespace-nowrap flex-shrink-0 py-3 px-4 border-b-2 font-medium text-sm"
                                        id="tab-skripsi">
                                        <i class="fas fa-graduation-cap w-4 h-4 inline mr-2"></i>
                                        Skripsi
                                        <span
                                            class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">{{ $stats['total_skripsi'] }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop Tab Navigation -->
                        <nav class="hidden sm:flex space-x-8 px-6" aria-label="Tabs">
                            <button onclick="switchTab('kkn')"
                                class="tab-button {{ $activeTab === 'kkn' ? 'active' : '' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                id="tab-kkn-desktop">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-users text-xs"></i>
                                    <span>KKN</span>
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $stats['total_kkn_kelompok'] }}</span>
                                </div>
                            </button>
                            <button onclick="switchTab('magang')"
                                class="tab-button {{ $activeTab === 'magang' ? 'active' : '' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                id="tab-magang-desktop">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-briefcase text-xs"></i>
                                    <span>Magang</span>
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $stats['total_magang'] }}</span>
                                </div>
                            </button>
                            <button onclick="switchTab('skripsi')"
                                class="tab-button {{ $activeTab === 'skripsi' ? 'active' : '' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                                id="tab-skripsi-desktop">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-graduation-cap text-xs"></i>
                                    <span>Skripsi</span>
                                    <span
                                        class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $stats['total_skripsi'] }}</span>
                                </div>
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- KKN Tab -->
                        <div id="content-kkn" class="tab-content {{ $activeTab === 'kkn' ? 'active' : 'hidden' }}">
                            @if (isset($kkn_kelompok) && $kkn_kelompok->isEmpty())
                                <div class="text-center py-16">
                                    <div
                                        class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-users text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada kelompok KKN</h3>
                                    <p class="text-xs text-gray-500">Kelompok KKN akan muncul di sini setelah dibentuk</p>
                                </div>
                            @elseif(isset($kkn_kelompok))
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Kelompok
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Lokasi
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Periode
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Peserta
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200" id="kknTableBody">
                                            @foreach ($kkn_kelompok as $data)
                                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div>
                                                            <div class="text-xs font-medium text-gray-900">
                                                                {{ $data['kelompok']->nama_kelompok }}</div>
                                                            <div class="text-xs text-gray-500">{{ $data['peserta_count'] }}
                                                                anggota</div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="text-xs text-gray-900">{{ $data['kelompok']->lokasi }}
                                                        </div>
                                                        @if ($data['kelompok']->alamat_lokasi)
                                                            <div class="text-xs text-gray-500">
                                                                {{ \Str::limit($data['kelompok']->alamat_lokasi, 50) }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-xs text-gray-900">
                                                            {{ $data['kelompok']->periode_mulai ? $data['kelompok']->periode_mulai->format('d M') : 'TBD' }}
                                                            -
                                                            {{ $data['kelompok']->periode_selesai ? $data['kelompok']->periode_selesai->format('d M Y') : 'TBD' }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ $data['peserta_count'] }} Peserta
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if ($data['pending_review'] > 0)
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                {{ $data['pending_review'] }} Pending
                                                            </span>
                                                        @else
                                                            <span class="text-xs text-gray-500">Up to date</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <a href="{{ route('dosen.bimbingan.kelompok', $data['kelompok']->id_kelompok_kkn) }}"
                                                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                            <i class="fas fa-eye mr-2"></i>
                                                            Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Magang Tab -->
                        <div id="content-magang" class="tab-content {{ $activeTab === 'magang' ? 'active' : 'hidden' }}">
                            @if ($magang->isEmpty())
                                <div class="text-center py-16">
                                    <div
                                        class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-briefcase text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada mahasiswa Magang</h3>
                                    <p class="text-xs text-gray-500">Mahasiswa Magang akan muncul di sini setelah
                                        didaftarkan</p>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Mahasiswa
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Tempat Magang
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Bidang
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Progress
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aktivitas Terakhir
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($magang as $mahasiswa)
                                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10">
                                                                @if ($mahasiswa->mahasiswa['foto'])
                                                                    <img class="h-10 w-10 rounded-full object-cover"
                                                                        src="{{ asset('storage/foto-mahasiswa/' . $mahasiswa->mahasiswa['foto']) }}"
                                                                        alt="{{ $mahasiswa->mahasiswa['nama'] }}">
                                                                @else
                                                                    <div
                                                                        class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                                        <i class="fas fa-user text-gray-400"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-xs font-medium text-gray-900">
                                                                    {{ $mahasiswa->mahasiswa['nama'] }}</div>
                                                                <div class="text-xs text-gray-500">
                                                                    {{ $mahasiswa->mahasiswa['nim'] }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="text-xs text-gray-900">
                                                            {{ $mahasiswa->detail['tempat'] ?? 'Belum ditentukan' }}</div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="text-xs text-gray-900">
                                                            {{ $mahasiswa->detail['bidang'] ?? 'Belum ditentukan' }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
                                                                <div class="bg-gray-900 h-2 rounded-full"
                                                                    style="width: {{ $mahasiswa->progress['progress_percentage'] }}%">
                                                                </div>
                                                            </div>
                                                            <span
                                                                class="text-xs text-gray-900">{{ $mahasiswa->progress['progress_percentage'] }}%</span>
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $mahasiswa->progress['bab_approved'] }}/{{ $mahasiswa->progress['total_bab'] }}
                                                            bab selesai
                                                            @if ($mahasiswa->progress['bab_pending'] > 0)
                                                                • {{ $mahasiswa->progress['bab_pending'] }} pending
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-xs text-gray-500">{{ $mahasiswa->last_activity }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <div class="flex items-center justify-center space-x-2">
                                                            <a href="{{ route('dosen.bimbingan.detail', $mahasiswa->id_peserta_bimbingan) }}"
                                                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                                <i class="fas fa-edit mr-2"></i>
                                                                Kelola
                                                            </a>
                                                            <button
                                                                onclick="openNilaiModal('{{ $mahasiswa->id_peserta_bimbingan }}', '{{ $mahasiswa->mahasiswa['nama'] }}', '{{ $mahasiswa->mata_kuliah }}', '{{ $mahasiswa->nilai ?? null }}')"
                                                                class="inline-flex items-center px-3 py-2 border border-blue-300 shadow-sm text-xs leading-4 font-medium rounded text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                                <i class="fas fa-award mr-2"></i>
                                                                {{ isset($mahasiswa->nilai) ? 'Edit Nilai' : 'Input Nilai' }}
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Skripsi Tab -->
                        <div id="content-skripsi"
                            class="tab-content {{ $activeTab === 'skripsi' ? 'active' : 'hidden' }}">
                            @if ($skripsi->isEmpty())
                                <div class="text-center py-16">
                                    <div
                                        class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-graduation-cap text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada mahasiswa Skripsi</h3>
                                    <p class="text-xs text-gray-500">Mahasiswa Skripsi akan muncul di sini setelah
                                        didaftarkan</p>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Mahasiswa
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Judul Skripsi
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status Proposal
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Progress
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aktivitas Terakhir
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aksi
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($skripsi as $mahasiswa)
                                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10">
                                                                @if ($mahasiswa->mahasiswa['foto'])
                                                                    <img class="h-10 w-10 rounded-full object-cover"
                                                                        src="{{ asset('storage/foto-mahasiswa/' . $mahasiswa->mahasiswa['foto']) }}"
                                                                        alt="{{ $mahasiswa->mahasiswa['nama'] }}">
                                                                @else
                                                                    <div
                                                                        class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                                        <i class="fas fa-user text-gray-400"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-xs font-medium text-gray-900">
                                                                    {{ $mahasiswa->mahasiswa['nama'] }}</div>
                                                                <div class="text-xs text-gray-500">
                                                                    {{ $mahasiswa->mahasiswa['nim'] }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="text-xs text-gray-900">
                                                            {{ \Str::limit($mahasiswa->detail['judul'] ?? 'Belum ditentukan', 60) }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $mahasiswa->detail['bidang_penelitian'] ?? 'Belum ditentukan' }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                            {{ $mahasiswa->detail['status_proposal'] === 'Disetujui'
                                                                ? 'bg-green-100 text-green-800'
                                                                : ($mahasiswa->detail['status_proposal'] === 'Ditolak'
                                                                    ? 'bg-red-100 text-red-800'
                                                                    : 'bg-yellow-100 text-yellow-800') }}">
                                                            {{ $mahasiswa->detail['status_proposal'] }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
                                                                <div class="bg-gray-900 h-2 rounded-full"
                                                                    style="width: {{ $mahasiswa->progress['progress_percentage'] }}%">
                                                                </div>
                                                            </div>
                                                            <span
                                                                class="text-xs text-gray-900">{{ $mahasiswa->progress['progress_percentage'] }}%</span>
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $mahasiswa->progress['bab_approved'] }}/{{ $mahasiswa->progress['total_bab'] }}
                                                            bab selesai
                                                            @if ($mahasiswa->progress['bab_pending'] > 0)
                                                                • {{ $mahasiswa->progress['bab_pending'] }} pending
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-xs text-gray-500">{{ $mahasiswa->last_activity }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <div class="flex items-center justify-center space-x-2">
                                                            <a href="{{ route('dosen.bimbingan.detail', $mahasiswa->id_peserta_bimbingan) }}"
                                                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                                <i class="fas fa-edit mr-2"></i>
                                                                Kelola
                                                            </a>
                                                            <button
                                                                onclick="openNilaiModal('{{ $mahasiswa->id_peserta_bimbingan }}', '{{ $mahasiswa->mahasiswa['nama'] }}', '{{ $mahasiswa->mata_kuliah }}', '{{ $mahasiswa->nilai ?? null }}')"
                                                                class="inline-flex items-center px-3 py-2 border border-blue-300 shadow-sm text-xs leading-4 font-medium rounded text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                                <i class="fas fa-award mr-2"></i>
                                                                {{ isset($mahasiswa->nilai) ? 'Edit Nilai' : 'Input Nilai' }}
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div id="nilaiModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[9999]">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Input Nilai Akhir</h3>
                <button onclick="closeNilaiModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="nilaiForm" method="POST" action="{{ route('dosen.bimbingan.store-nilai') }}">
                @csrf
                <input type="hidden" name="id_peserta_bimbingan" id="modal_id_peserta_bimbingan">

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mahasiswa</label>
                    <p class="text-sm text-gray-900" id="modal_nama_mahasiswa"></p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mata Kuliah</label>
                    <p class="text-sm text-gray-900" id="modal_mata_kuliah"></p>
                </div>

                <div class="mb-4">
                    <label for="nilai_angka" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nilai Angka (0-100) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="nilai_angka" id="nilai_angka" min="0" max="100"
                        step="0.01" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan nilai angka">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview Konversi Nilai</label>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-gray-600">Nilai Huruf:</span>
                                <span id="preview_huruf" class="font-medium text-gray-900">-</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Nilai Indeks:</span>
                                <span id="preview_indeks" class="font-medium text-gray-900">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeNilaiModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-700">
                        Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>

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
        </style>
    @endpush

    @push('scripts')
        <script>
            // Global variables
            let currentTab = 'kkn';

            // Initialize page
            document.addEventListener('DOMContentLoaded', function() {
                setupMobileTabs();
                initializeTab();
            });

            // Utility function to get URL parameters
            function getUrlParameter(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }

            // Utility function to update URL parameter without page refresh
            function updateUrlParameter(key, value) {
                const url = new URL(window.location);
                url.searchParams.set(key, value);
                window.history.pushState({
                    path: url.href
                }, '', url.href);
            }

            // Check if tab name is valid
            function isValidTab(tabName) {
                const validTabs = ['kkn', 'magang', 'skripsi'];
                return validTabs.includes(tabName);
            }

            // Initialize tab based on URL query parameter
            function initializeTab() {
                const urlTab = getUrlParameter('tab');
                const initialTab = urlTab && isValidTab(urlTab) ? urlTab : 'kkn';
                switchTabWithoutUrlUpdate(initialTab);
            }

            // Handle browser back/forward navigation
            window.addEventListener('popstate', function(event) {
                const urlTab = getUrlParameter('tab');
                const targetTab = urlTab && isValidTab(urlTab) ? urlTab : 'kkn';
                switchTabWithoutUrlUpdate(targetTab);
            });

            // Enhanced tab switching with URL update
            function switchTab(tabName) {
                updateUrlParameter('tab', tabName);
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
            }

            // Mobile tabs setup
            function setupMobileTabs() {
                const mobileTabsContainer = document.getElementById('mobile-tabs');

                if (mobileTabsContainer) {
                    function updateScrollIndicators() {
                        // Add any scroll indicator logic here if needed
                    }

                    mobileTabsContainer.addEventListener('scroll', updateScrollIndicators);
                    window.addEventListener('resize', updateScrollIndicators);
                    updateScrollIndicators();
                }
            }

            // Change semester function
            function changeSemester() {
                const semesterId = document.getElementById('semesterFilter').value;
                if (semesterId) {
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.set('semester', semesterId);
                    // Keep current tab when changing semester
                    currentParams.set('tab', currentTab);
                    window.location.href = window.location.pathname + '?' + currentParams.toString();
                } else {
                    const currentParams = new URLSearchParams(window.location.search);
                    currentParams.delete('semester');
                    currentParams.delete('tab');
                    window.location.href = window.location.pathname + (currentParams.toString() ? '?' + currentParams
                        .toString() : '');
                }
            }

            // Make functions globally accessible
            window.changeSemester = changeSemester;
            window.switchTab = switchTab;
        </script>

        <script>
            // Fungsi konversi nilai
            function convertGrade(nilaiAngka) {
                if (nilaiAngka >= 85) return {
                    huruf: 'A',
                    indeks: 4.00
                };
                if (nilaiAngka >= 80) return {
                    huruf: 'A-',
                    indeks: 3.70
                };
                if (nilaiAngka >= 75) return {
                    huruf: 'B+',
                    indeks: 3.30
                };
                if (nilaiAngka >= 70) return {
                    huruf: 'B',
                    indeks: 3.00
                };
                if (nilaiAngka >= 65) return {
                    huruf: 'B-',
                    indeks: 2.70
                };
                if (nilaiAngka >= 60) return {
                    huruf: 'C+',
                    indeks: 2.30
                };
                if (nilaiAngka >= 55) return {
                    huruf: 'C',
                    indeks: 2.00
                };
                if (nilaiAngka >= 50) return {
                    huruf: 'C-',
                    indeks: 1.70
                };
                if (nilaiAngka >= 45) return {
                    huruf: 'D',
                    indeks: 1.00
                };
                return {
                    huruf: 'E',
                    indeks: 0.00
                };
            }

            // Open modal
            function openNilaiModal(idPeserta, namaMahasiswa, mataKuliah, nilaiData) {
                document.getElementById('modal_id_peserta_bimbingan').value = idPeserta;
                document.getElementById('modal_nama_mahasiswa').textContent = namaMahasiswa;
                document.getElementById('modal_mata_kuliah').textContent = mataKuliah;

                // Reset atau set nilai jika edit
                const nilaiAngkaInput = document.getElementById('nilai_angka');
                if (nilaiData && nilaiData !== 'null') {
                    try {
                        const nilai = JSON.parse(nilaiData);
                        nilaiAngkaInput.value = nilai.nilai_angka;
                        updatePreview(nilai.nilai_angka);
                    } catch (e) {
                        nilaiAngkaInput.value = '';
                        resetPreview();
                    }
                } else {
                    nilaiAngkaInput.value = '';
                    resetPreview();
                }

                document.getElementById('nilaiModal').classList.remove('hidden');
            }

            // Close modal
            function closeNilaiModal() {
                document.getElementById('nilaiModal').classList.add('hidden');
                document.getElementById('nilaiForm').reset();
                resetPreview();
            }

            // Update preview saat input nilai
            document.addEventListener('DOMContentLoaded', function() {
                const nilaiAngkaInput = document.getElementById('nilai_angka');

                nilaiAngkaInput.addEventListener('input', function() {
                    const nilai = parseFloat(this.value);
                    if (!isNaN(nilai) && nilai >= 0 && nilai <= 100) {
                        updatePreview(nilai);
                    } else {
                        resetPreview();
                    }
                });
            });

            function updatePreview(nilaiAngka) {
                const converted = convertGrade(nilaiAngka);
                document.getElementById('preview_huruf').textContent = converted.huruf;
                document.getElementById('preview_indeks').textContent = converted.indeks.toFixed(2);
            }

            function resetPreview() {
                document.getElementById('preview_huruf').textContent = '-';
                document.getElementById('preview_indeks').textContent = '-';
            }
        </script>
    @endpush
@endsection
