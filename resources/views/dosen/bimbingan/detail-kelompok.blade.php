@extends('layouts.app')

@section('title', 'Detail Kelompok ' . $kelompok->nama_kelompok)

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">

            <!-- Header Info Kelompok -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="px-6 py-6">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
                        <!-- Info Kelompok -->
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900 mb-2">{{ $kelompok->nama_kelompok }}</h1>
                            <div class="space-y-1">
                                <p class="text-xs text-gray-600">
                                    <span class="font-medium">Lokasi:</span> {{ $kelompok->lokasi }}
                                </p>
                                @if ($kelompok->alamat_lokasi)
                                    <p class="text-xs text-gray-600">
                                        <span class="font-medium">Alamat:</span> {{ $kelompok->alamat_lokasi }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-600">
                                    <span class="font-medium">DPL:</span> {{ $kelompok->dpl->pengguna->nama }}
                                </p>
                                <p class="text-xs text-gray-600">
                                    <span class="font-medium">Periode:</span>
                                    {{ $kelompok->periode_mulai ? $kelompok->periode_mulai->format('d M Y') : 'TBD' }} -
                                    {{ $kelompok->periode_selesai ? $kelompok->periode_selesai->format('d M Y') : 'TBD' }}
                                </p>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <a href="{{ route('dosen.bimbingan.index', ['tab' => 'kkn']) }}"
                            class="mt-4 inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-900 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                            <i class="fas fa-arrow-left w-3 h-3 mr-2"></i>
                            Kembali
                        </a>
                    </div>

                    @if ($kelompok->target_program_kerja)
                        <div class="mt-6 p-4 bg-gray-50 rounded border">
                            <h3 class="text-xs font-medium text-gray-900 mb-2">Target Program Kerja</h3>
                            <p class="text-xs text-gray-700">{{ $kelompok->target_program_kerja }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tab Content -->
            <div class="bg-white rounded-lg shadow-sm">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="switchTab('peserta')"
                            class="tab-button border-gray-900 text-gray-900 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-xs transition-colors duration-200">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-user-friends text-xs"></i>
                                <span>Peserta</span>
                                <span
                                    class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $peserta_list->count() }}</span>
                            </div>
                        </button>
                        <button onclick="switchTab('dokumentasi')"
                            class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-xs transition-colors duration-200">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-camera text-xs"></i>
                                <span>Dokumentasi</span>
                                <span
                                    class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $dokumentasi['images']->count() + $dokumentasi['documents']->count() }}</span>
                            </div>
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Peserta Tab -->
                    <div id="peserta-tab" class="tab-content">
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
                                            Angkatan
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kontak
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Peran
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status Struktur Laporan
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nilai Akhir
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
                                    @foreach ($peserta_list as $peserta)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if ($peserta['mahasiswa']['foto'])
                                                            <img class="h-10 w-10 rounded-full object-cover"
                                                                src="{{ asset('storage/foto-mahasiswa/' . $peserta['mahasiswa']['foto']) }}"
                                                                alt="{{ $peserta['mahasiswa']['nama'] }}">
                                                        @else
                                                            <div
                                                                class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                                <i class="fas fa-user text-gray-400"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-xs font-medium text-gray-900">
                                                            {{ $peserta['mahasiswa']['nama'] }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $peserta['mahasiswa']['nim'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <!-- Angkatan -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $peserta['mahasiswa']['angkatan'] }}
                                                </div>
                                            </td>

                                            <!-- Kontak -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="space-y-1">
                                                    @if ($peserta['mahasiswa']['email'])
                                                        <a href="mailto:{{ $peserta['mahasiswa']['email'] }}"
                                                            class="flex items-center text-xs text-gray-600 hover:text-gray-900">
                                                            <i class="fas fa-envelope w-4 mr-1.5"></i>
                                                            <span
                                                                class="max-w-[180px]">{{ $peserta['mahasiswa']['email'] }}</span>
                                                        </a>
                                                    @endif
                                                    @if ($peserta['mahasiswa']['no_hp'])
                                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $peserta['mahasiswa']['no_hp']) }}"
                                                            target="_blank"
                                                            class="flex items-center text-xs text-gray-600 hover:text-gray-900">
                                                            <i class="fab fa-whatsapp w-4 mr-1.5"></i>
                                                            {{ $peserta['mahasiswa']['no_hp'] }}
                                                        </a>
                                                    @endif
                                                    @if (!$peserta['mahasiswa']['email'] && !$peserta['mahasiswa']['no_hp'])
                                                        <span class="text-xs text-gray-400">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $peserta['peran'] === 'KETUA' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $peserta['peran'] }}
                                                </span>
                                            </td>
                                            {{-- <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
                                                        <div class="bg-gray-900 h-2 rounded-full"
                                                            style="width: {{ $peserta['progress']['progress_percentage'] }}%">
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="text-xs text-gray-900">{{ $peserta['progress']['progress_percentage'] }}%</span>
                                                </div>
                                            </td> --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-xs text-gray-900">
                                                    {{ $peserta['progress']['bab_approved'] }}/{{ $peserta['progress']['total_bab'] }}
                                                    selesai
                                                </div>
                                                @if ($peserta['progress']['bab_pending'] > 0)
                                                    <div class="text-xs text-gray-600">
                                                        {{ $peserta['progress']['bab_pending'] }} pending review</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if (isset($peserta['nilai']))
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="text-sm font-semibold text-gray-900">{{ $peserta['nilai']['huruf'] }}</span>
                                                        <span
                                                            class="text-xs text-gray-500">{{ number_format($peserta['nilai']['angka'], 2) }}
                                                            | {{ number_format($peserta['nilai']['indeks'], 2) }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-400 italic">Belum dinilai</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-xs text-gray-500">
                                                    {{ $peserta['last_activity'] ? \Carbon\Carbon::parse($peserta['last_activity'])->diffForHumans() : 'Belum ada' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <a href="{{ route('dosen.bimbingan.detail', $peserta['id_peserta_bimbingan']) }}"
                                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                                        <i class="fas fa-edit mr-2"></i>
                                                        Kelola
                                                    </a>
                                                    <button
                                                        onclick="openNilaiModal('{{ $peserta['id_peserta_bimbingan'] }}', '{{ $peserta['mahasiswa']['nama'] }}', 'KKN', '{{ isset($peserta['nilai']) ? json_encode($peserta['nilai']) : null }}')"
                                                        class="inline-flex items-center px-3 py-2 border border-blue-300 shadow-sm text-xs leading-4 font-medium rounded text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                                        <i class="fas fa-award mr-2"></i>
                                                        {{ isset($peserta['nilai']) ? 'Edit Nilai' : 'Beri Nilai' }}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Dokumentasi Tab -->
                    <div id="dokumentasi-tab" class="tab-content hidden">
                        @if ($dokumentasi['images']->isEmpty() && $dokumentasi['documents']->isEmpty())
                            <div class="text-center py-16">
                                <div
                                    class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-camera text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada dokumentasi</h3>
                                <p class="text-xs text-gray-500">Dokumentasi akan diupload oleh ketua kelompok</p>
                            </div>
                        @else
                            <!-- Dokumentasi Images -->
                            @if ($dokumentasi['images']->isNotEmpty())
                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                        Foto Dokumentasi ({{ $dokumentasi['images']->count() }})
                                    </h3>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                        @foreach ($dokumentasi['images'] as $image)
                                            <div class="relative group cursor-pointer border border-gray-200 rounded-lg overflow-hidden"
                                                onclick="openImageModal('{{ asset('storage/' . $image->file_path) }}', '{{ $image->judul }}')">
                                                <div class="aspect-square bg-gray-100">
                                                    <img src="{{ asset('storage/' . $image->file_path) }}"
                                                        alt="{{ $image->judul }}"
                                                        class="w-full h-full object-cover group-hover:opacity-75 transition-opacity duration-200">
                                                </div>
                                                <div
                                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center">
                                                    <i
                                                        class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                                                </div>
                                                <div class="p-3 bg-white">
                                                    <p class="text-xs font-medium text-gray-900 truncate">
                                                        {{ $image->judul }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $image->uploader->pengguna->nama ?? 'Unknown' }} •
                                                        {{ $image->created_at->format('d M Y') }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Dokumentasi Documents -->
                            @if ($dokumentasi['documents']->isNotEmpty())
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                        Dokumen ({{ $dokumentasi['documents']->count() }})
                                    </h3>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Nama Dokumen
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Deskripsi
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Diupload Oleh
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Tanggal
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Ukuran
                                                    </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Aksi
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($dokumentasi['documents'] as $document)
                                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                        <td class="px-6 py-4">
                                                            <div class="flex items-center">
                                                                <div class="flex-shrink-0">
                                                                    <div
                                                                        class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center">
                                                                        <i
                                                                            class="fas fa-file-alt text-gray-400 text-xs"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="ml-4">
                                                                    <div class="text-xs font-medium text-gray-900">
                                                                        {{ $document->judul }}</div>
                                                                    <div class="text-xs text-gray-500">
                                                                        {{ $document->original_filename }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <div class="text-xs text-gray-900">
                                                                {{ $document->deskripsi ? \Str::limit($document->deskripsi, 60) : '-' }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-xs text-gray-900">
                                                                {{ $document->uploader->pengguna->nama ?? 'Unknown' }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-xs text-gray-500">
                                                                {{ $document->created_at->format('d M Y') }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-xs text-gray-500">
                                                                {{ $document->formatted_file_size }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                                            <a href="{{ asset('storage/' . $document->file_path) }}"
                                                                download="{{ $document->original_filename }}"
                                                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-xs leading-4 font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                                                <i class="fas fa-download mr-2"></i>
                                                                Download
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div id="imageModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" onclick="closeImageModal()"></div>

                <div class="relative bg-white rounded-lg max-w-4xl max-h-full overflow-hidden shadow-xl">
                    <div class="flex items-center justify-between p-4 border-b border-gray-200">
                        <h3 id="imageModalTitle" class="text-lg font-semibold text-gray-900"></h3>
                        <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        <img id="imageModalContent" src="" alt=""
                            class="max-w-full max-h-96 mx-auto rounded">
                    </div>
                </div>
            </div>
        </div>

        <div id="nilaiModal"
            class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[99999]">
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

    </div>

    @push('scripts')
        <script>
            // Tab switching
            function switchTab(tabName) {
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });

                // Show selected tab content
                document.getElementById(tabName + '-tab').classList.remove('hidden');

                // Update tab button styles
                document.querySelectorAll('.tab-button').forEach(button => {
                    button.className = button.className.replace(/border-gray-900 text-gray-900/g,
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300');
                });

                // Apply active style to selected tab
                const activeButton = document.querySelector(`button[onclick="switchTab('${tabName}')"]`);
                if (activeButton) {
                    activeButton.className = activeButton.className.replace(
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        'border-gray-900 text-gray-900');
                }
            }

            // Image modal functions
            function openImageModal(imageSrc, title) {
                const modal = document.getElementById('imageModal');
                const modalTitle = document.getElementById('imageModalTitle');
                const modalImage = document.getElementById('imageModalContent');

                modalTitle.textContent = title;
                modalImage.src = imageSrc;
                modalImage.alt = title;

                modal.classList.remove('hidden');
            }

            function closeImageModal() {
                document.getElementById('imageModal').classList.add('hidden');
            }

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeImageModal();
                }
            });

            // Make functions globally accessible
            window.switchTab = switchTab;
            window.openImageModal = openImageModal;
            window.closeImageModal = closeImageModal;
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

            // Open nilai modal
            function openNilaiModal(idPeserta, namaMahasiswa, mataKuliah, nilaiData) {
                document.getElementById('modal_id_peserta_bimbingan').value = idPeserta;
                document.getElementById('modal_nama_mahasiswa').textContent = namaMahasiswa;
                document.getElementById('modal_mata_kuliah').textContent = mataKuliah;

                // Reset atau set nilai jika edit
                const nilaiAngkaInput = document.getElementById('nilai_angka');
                if (nilaiData && nilaiData !== 'null') {
                    try {
                        const nilai = JSON.parse(nilaiData);
                        nilaiAngkaInput.value = nilai.angka;
                        updatePreview(nilai.angka);
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

            // Close nilai modal
            function closeNilaiModal() {
                document.getElementById('nilaiModal').classList.add('hidden');
                document.getElementById('nilaiForm').reset();
                resetPreview();
            }

            // Update preview saat input nilai
            document.addEventListener('DOMContentLoaded', function() {
                const nilaiAngkaInput = document.getElementById('nilai_angka');

                if (nilaiAngkaInput) {
                    nilaiAngkaInput.addEventListener('input', function() {
                        const nilai = parseFloat(this.value);
                        if (!isNaN(nilai) && nilai >= 0 && nilai <= 100) {
                            updatePreview(nilai);
                        } else {
                            resetPreview();
                        }
                    });
                }
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

            // Make functions globally accessible
            window.openNilaiModal = openNilaiModal;
            window.closeNilaiModal = closeNilaiModal;
        </script>
    @endpush
@endsection
