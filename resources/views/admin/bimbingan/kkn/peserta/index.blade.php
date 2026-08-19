@extends('layouts.app')

@section('title', 'Detail ' . $kelompok->nama_kelompok)

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
                        <!-- Info Kelompok -->
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-3">
                                <a href="{{ route('bimbingan.kkn.kelompok.index', ['semester' => $kelompok->id_semester]) }}"
                                    class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                </a>
                                <h1 class="text-xl font-semibold text-gray-900">{{ $kelompok->nama_kelompok }}</h1>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-gray-600">
                                        <span class="font-medium">Semester:</span>
                                        {{ $kelompok->semester->nama_semester }}
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        <span class="font-medium">Lokasi:</span>
                                        {{ $kelompok->lokasi }}
                                    </p>
                                    @if ($kelompok->alamat_lokasi)
                                        <p class="text-xs text-gray-600">
                                            <span class="font-medium">Alamat:</span>
                                            {{ $kelompok->alamat_lokasi }}
                                        </p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">
                                        <span class="font-medium">DPL:</span>
                                        {{ $kelompok->dpl->pengguna->nama ?? 'N/A' }}
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        <span class="font-medium">NIDN:</span>
                                        {{ $kelompok->dpl->nidn ?? 'N/A' }}
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        <span class="font-medium">Periode:</span>
                                        {{ \Carbon\Carbon::parse($kelompok->periode_mulai)->format('d M Y') }} -
                                        {{ \Carbon\Carbon::parse($kelompok->periode_selesai)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>

                            @if ($kelompok->target_program_kerja)
                                <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <h3 class="text-xs font-medium text-gray-900 mb-1">Target Program Kerja</h3>
                                    <p class="text-xs text-gray-700">{{ $kelompok->target_program_kerja }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Quick Stats -->
                        <div class="mt-4 lg:mt-0 lg:ml-6 flex flex-col space-y-2">
                            <div class="bg-blue-50 px-4 py-3 rounded-lg border border-blue-100">
                                <div class="text-xs text-blue-600 font-medium">Total Anggota</div>
                                <div class="text-2xl font-bold text-blue-900">{{ $kelompok->kknDetails->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="switchTab('peserta')"
                            class="tab-button border-gray-900 text-gray-900 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-xs transition-colors duration-200">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-user-friends"></i>
                                <span>Peserta KKN</span>
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $kelompok->kknDetails->count() }}
                                </span>
                            </div>
                        </button>
                        <button onclick="switchTab('dokumentasi')"
                            class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-xs transition-colors duration-200">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-camera"></i>
                                <span>Dokumentasi</span>
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $dokumentasi_count }}
                                </span>
                            </div>
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Peserta Tab -->
                    <div id="peserta-tab" class="tab-content">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Daftar Peserta KKN</h3>
                                <p class="text-xs text-gray-500">Kelola anggota kelompok KKN</p>
                            </div>
                            <button onclick="openTambahPesertaModal()"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Peserta
                            </button>
                        </div>

                        @if ($kelompok->kknDetails->isEmpty())
                            <div class="text-center py-12 bg-gray-50 rounded-lg">
                                <i class="fas fa-user-friends text-4xl text-gray-400 mb-4"></i>
                                <p class="text-sm font-medium text-gray-900 mb-2">Belum ada peserta</p>
                                <p class="text-xs text-gray-500">Tambahkan peserta KKN untuk kelompok ini</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Mahasiswa
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Program Studi
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Peran
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Kontak
                                            </th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($kelompok->kknDetails as $detail)
                                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            @if ($detail->pesertaBimbingan->registrasiMahasiswa->mahasiswa->pengguna->foto)
                                                                <img class="h-10 w-10 rounded-full object-cover"
                                                                    src="{{ asset('storage/' . $detail->pesertaBimbingan->registrasiMahasiswa->mahasiswa->pengguna->foto) }}"
                                                                    alt="">
                                                            @else
                                                                <div
                                                                    class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                                    <i class="fas fa-user text-gray-400"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-xs font-medium text-gray-900">
                                                                {{ $detail->pesertaBimbingan->registrasiMahasiswa->mahasiswa->pengguna->nama ?? 'N/A' }}
                                                            </div>
                                                            <div class="text-xs text-gray-500">
                                                                {{ $detail->pesertaBimbingan->registrasiMahasiswa->mahasiswa->nim ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-xs text-gray-900">
                                                        {{ $detail->pesertaBimbingan->registrasiMahasiswa->mahasiswa->programStudi->nama_program_studi ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        {{ $detail->peran_kelompok === 'KETUA' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ $detail->peran_kelompok }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="space-y-1">
                                                        @if ($detail->pesertaBimbingan->registrasiMahasiswa->mahasiswa->pengguna->email)
                                                            <div class="text-xs text-gray-600 flex items-center">
                                                                <i class="fas fa-envelope w-4 mr-1.5"></i>
                                                                {{ $detail->pesertaBimbingan->registrasiMahasiswa->mahasiswa->pengguna->email }}
                                                            </div>
                                                        @endif
                                                        @if ($detail->pesertaBimbingan->registrasiMahasiswa->mahasiswa->no_hp)
                                                            <div class="text-xs text-gray-600 flex items-center">
                                                                <i class="fab fa-whatsapp w-4 mr-1.5"></i>
                                                                {{ $detail->pesertaBimbingan->registrasiMahasiswa->mahasiswa->no_hp }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        <button
                                                            onclick="editPeran('{{ $detail->id_kkn_detail }}', '{{ $detail->peran_kelompok }}')"
                                                            class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                                            <i class="fas fa-edit text-xs"></i>
                                                        </button>
                                                        <button
                                                            onclick="confirmRemovePeserta('{{ $detail->id_kkn_detail }}', '{{ $detail->pesertaBimbingan->registrasiMahasiswa->mahasiswa->pengguna->nama }}')"
                                                            class="inline-flex items-center p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors">
                                                            <i class="fas fa-trash text-xs"></i>
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

                    <!-- Dokumentasi Tab -->
                    <div id="dokumentasi-tab" class="tab-content hidden">
                        @if ($dokumentasi['images']->isEmpty() && $dokumentasi['documents']->isEmpty())
                            <div class="text-center py-16 bg-gray-50 rounded-lg">
                                <i class="fas fa-camera text-4xl text-gray-400 mb-4"></i>
                                <h3 class="text-sm font-medium text-gray-900 mb-2">Belum ada dokumentasi</h3>
                                <p class="text-xs text-gray-500">Dokumentasi akan muncul di sini ketika ketua kelompok
                                    mengupload</p>
                            </div>
                        @else
                            <!-- Images -->
                            @if ($dokumentasi['images']->isNotEmpty())
                                <div class="mb-8">
                                    <h3 class="text-sm font-semibold text-gray-900 mb-4">
                                        Foto Dokumentasi ({{ $dokumentasi['images']->count() }})
                                    </h3>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                        @foreach ($dokumentasi['images'] as $image)
                                            <div class="relative group cursor-pointer border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow"
                                                onclick="openImageModal('{{ asset('storage/' . $image->file_path) }}', '{{ $image->judul }}', '{{ $image->deskripsi }}')">
                                                <div class="aspect-square bg-gray-100">
                                                    <img src="{{ asset('storage/' . $image->file_path) }}"
                                                        alt="{{ $image->judul }}"
                                                        class="w-full h-full object-cover group-hover:opacity-75 transition-opacity">
                                                </div>
                                                <div
                                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all flex items-center justify-center">
                                                    <i
                                                        class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                                </div>
                                                <div class="p-3 bg-white">
                                                    <p class="text-xs font-medium text-gray-900 truncate">
                                                        {{ $image->judul }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $image->created_at->format('d M Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Documents -->
                            @if ($dokumentasi['documents']->isNotEmpty())
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900 mb-4">
                                        Dokumen ({{ $dokumentasi['documents']->count() }})
                                    </h3>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Nama Dokumen
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Deskripsi
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Tanggal Upload
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Ukuran
                                                    </th>
                                                    <th
                                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Aksi
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($dokumentasi['documents'] as $document)
                                                    <tr class="hover:bg-gray-50 transition-colors">
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
                                                            <div class="text-xs text-gray-900 max-w-xs truncate">
                                                                {{ $document->deskripsi ?? '-' }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-xs text-gray-500">
                                                                {{ $document->created_at->format('d M Y') }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-xs text-gray-500">
                                                                {{ number_format($document->file_size / 1024, 2) }} KB
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                                            <a href="{{ asset('storage/' . $document->file_path) }}"
                                                                download="{{ $document->original_filename }}"
                                                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 transition-colors">
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

                <div class="relative bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-xl">
                    <div class="flex items-center justify-between p-4 border-b border-gray-200">
                        <div>
                            <h3 id="imageModalTitle" class="text-sm font-semibold text-gray-900"></h3>
                            <p id="imageModalDesc" class="text-xs text-gray-500 mt-1"></p>
                        </div>
                        <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="p-4 flex items-center justify-center bg-gray-50">
                        <img id="imageModalContent" src="" alt=""
                            class="max-w-full max-h-[70vh] rounded">
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Peserta -->
        <div id="tambahPesertaModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    onclick="closeTambahPesertaModal()"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-2xl">
                    <form method="POST"
                        action="{{ route('bimbingan.kkn.peserta.store', ['kelompok' => $kelompok->id_kelompok_kkn]) }}">
                        @csrf

                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Tambah Peserta KKN</h3>
                                <button type="button" onclick="closeTambahPesertaModal()"
                                    class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Cari Mahasiswa <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="mahasiswa_search"
                                        placeholder="Cari berdasarkan nama atau NIM..." autocomplete="off"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
                                    <input type="hidden" name="id_peserta_bimbingan" id="id_peserta_bimbingan" required>

                                    <div id="mahasiswa_dropdown"
                                        class="mt-2 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">
                                        Peran dalam Kelompok <span class="text-red-500">*</span>
                                    </label>
                                    <select name="peran_kelompok" required
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
                                        <option value="">Pilih Peran</option>
                                        <option value="KETUA">Ketua</option>
                                        <option value="ANGGOTA">Anggota</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                            <button type="button" onclick="closeTambahPesertaModal()"
                                class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                                Tambah Peserta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Hidden Forms -->
        <form id="delete-peserta-form" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>

    @push('scripts')
        <script>
            // Tab switching
            function switchTab(tabName) {
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });

                document.getElementById(tabName + '-tab').classList.remove('hidden');

                document.querySelectorAll('.tab-button').forEach(button => {
                    button.className = button.className.replace(/border-gray-900 text-gray-900/g,
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300');
                });

                const activeButton = document.querySelector(`button[onclick="switchTab('${tabName}')"]`);
                if (activeButton) {
                    activeButton.className = activeButton.className.replace(
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                        'border-gray-900 text-gray-900');
                }
            }

            // Image modal
            function openImageModal(imageSrc, title, desc) {
                document.getElementById('imageModalTitle').textContent = title;
                document.getElementById('imageModalDesc').textContent = desc || '';
                document.getElementById('imageModalContent').src = imageSrc;
                document.getElementById('imageModal').classList.remove('hidden');
            }

            function closeImageModal() {
                document.getElementById('imageModal').classList.add('hidden');
            }

            // Tambah Peserta Modal
            function openTambahPesertaModal() {
                document.getElementById('tambahPesertaModal').classList.remove('hidden');
            }

            function closeTambahPesertaModal() {
                document.getElementById('tambahPesertaModal').classList.add('hidden');
                document.getElementById('mahasiswa_search').value = '';
                document.getElementById('id_peserta_bimbingan').value = '';
                document.getElementById('mahasiswa_dropdown').classList.add('hidden');
            }

            // Mahasiswa search
            let mahasiswaData = [];

            document.addEventListener('DOMContentLoaded', function() {
                loadMahasiswaData();
                setupMahasiswaSearch();
            });

            function loadMahasiswaData() {
                fetch('/bimbingan/kkn/kelompok/{{ $kelompok->id_kelompok_kkn }}/available-peserta')
                    .then(response => response.json())
                    .then(data => {
                        mahasiswaData = data;
                    })
                    .catch(error => console.error('Error:', error));
            }

            function setupMahasiswaSearch() {
                const searchInput = document.getElementById('mahasiswa_search');

                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    if (query) {
                        searchMahasiswa(query);
                    } else {
                        document.getElementById('mahasiswa_dropdown').classList.add('hidden');
                    }
                });
            }

            function searchMahasiswa(query) {
                const filtered = mahasiswaData.filter(item => {
                    return item.nama.toLowerCase().includes(query) ||
                        item.nim.toLowerCase().includes(query);
                });

                displayMahasiswaResults(filtered);
            }

            function displayMahasiswaResults(data) {
                const dropdown = document.getElementById('mahasiswa_dropdown');

                if (data.length === 0) {
                    dropdown.innerHTML =
                        '<div class="px-4 py-3 text-center text-xs text-gray-500">Tidak ada mahasiswa ditemukan</div>';
                } else {
                    dropdown.innerHTML = data.map(item => `
                        <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0" 
                             onclick="selectMahasiswa('${item.id_peserta_bimbingan}', '${item.nama}', '${item.nim}')">
                            <div class="text-xs font-medium text-gray-900">${item.nama}</div>
                            <div class="text-xs text-gray-500">${item.nim} • ${item.program_studi}</div>
                        </div>
                    `).join('');
                }

                dropdown.classList.remove('hidden');
            }

            function selectMahasiswa(id, nama, nim) {
                document.getElementById('mahasiswa_search').value = `${nama} (${nim})`;
                document.getElementById('id_peserta_bimbingan').value = id;
                document.getElementById('mahasiswa_dropdown').classList.add('hidden');
            }

            // Edit peran
            function editPeran(idDetail, currentPeran) {
                const newPeran = prompt('Ubah peran (KETUA/ANGGOTA):', currentPeran);
                if (newPeran && (newPeran === 'KETUA' || newPeran === 'ANGGOTA')) {
                    // Submit form untuk update peran
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/bimbingan/kkn/peserta/${idDetail}/update-peran`;

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';

                    const peranInput = document.createElement('input');
                    peranInput.type = 'hidden';
                    peranInput.name = 'peran_kelompok';
                    peranInput.value = newPeran;

                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    form.appendChild(peranInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            // Remove peserta
            function confirmRemovePeserta(idDetail, namaMahasiswa) {
                if (confirm(`Yakin ingin mengeluarkan ${namaMahasiswa} dari kelompok?`)) {
                    const form = document.getElementById('delete-peserta-form');
                    form.action = `/bimbingan/kkn/peserta/${idDetail}`;
                    form.submit();
                }
            }

            // Close modal with Escape
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeImageModal();
                    closeTambahPesertaModal();
                }
            });

            // Make functions global
            window.switchTab = switchTab;
            window.openImageModal = openImageModal;
            window.closeImageModal = closeImageModal;
            window.openTambahPesertaModal = openTambahPesertaModal;
            window.closeTambahPesertaModal = closeTambahPesertaModal;
            window.selectMahasiswa = selectMahasiswa;
            window.editPeran = editPeran;
            window.confirmRemovePeserta = confirmRemovePeserta;
        </script>
    @endpush
@endsection
