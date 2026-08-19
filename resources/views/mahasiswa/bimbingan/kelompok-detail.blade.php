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
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-3">
                                <h1 class="text-2xl font-semibold text-gray-900">{{ $kelompok->nama_kelompok }}</h1>
                                @if ($isKetua)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-900 text-white">
                                        <i class="fas fa-crown mr-1.5"></i>
                                        Ketua Kelompok
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Lokasi:</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $kelompok->lokasi }}</p>
                                </div>
                                @if ($kelompok->alamat_lokasi)
                                    <div>
                                        <p class="text-xs text-gray-600 mb-1">Alamat:</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $kelompok->alamat_lokasi }}</p>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Dosen Pembimbing Lapangan:</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $kelompok->dpl->pengguna->nama }}</p>
                                    @if ($kelompok->dpl->pengguna->email || $kelompok->dpl->pengguna->no_hp)
                                        <div class="flex items-center space-x-3 mt-1">
                                            @if ($kelompok->dpl->pengguna->email)
                                                <a href="mailto:{{ $kelompok->dpl->pengguna->email }}"
                                                    class="text-xs text-gray-600 hover:text-gray-900 flex items-center">
                                                    <i class="fas fa-envelope mr-1"></i>
                                                    {{ $kelompok->dpl->pengguna->email }}
                                                </a>
                                            @endif
                                            @if ($kelompok->dpl->pengguna->no_hp)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $kelompok->dpl->pengguna->no_hp) }}"
                                                    target="_blank"
                                                    class="text-xs text-gray-600 hover:text-gray-900 flex items-center">
                                                    <i class="fab fa-whatsapp mr-1"></i>
                                                    {{ $kelompok->dpl->pengguna->no_hp }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Periode:</p>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $kelompok->periode_mulai ? $kelompok->periode_mulai->format('d M Y') : 'TBD' }}
                                        -
                                        {{ $kelompok->periode_selesai ? $kelompok->periode_selesai->format('d M Y') : 'TBD' }}
                                    </p>
                                </div>
                            </div>

                            @if ($kelompok->target_program_kerja)
                                <div class="mt-4 p-4 bg-gray-50 rounded border">
                                    <h3 class="text-xs font-medium text-gray-900 mb-2">Target Program Kerja</h3>
                                    <p class="text-xs text-gray-700">{{ $kelompok->target_program_kerja }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Back Button -->
                        <a href="{{ route('mahasiswa.bimbingan.index', ['tab' => 'kkn']) }}"
                            class="mt-4 lg:mt-0 inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-900 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                            <i class="fas fa-arrow-left w-3 h-3 mr-2"></i>
                            Kembali
                        </a>
                    </div>
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
                                <span>Anggota Kelompok</span>
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
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($peserta_list as $anggota)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <!-- Mahasiswa -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-12 w-12">
                                                        @if ($anggota['mahasiswa']['foto'])
                                                            <img class="h-12 w-12 rounded-full object-cover"
                                                                src="{{ asset('storage/foto-mahasiswa/' . $anggota['mahasiswa']['foto']) }}"
                                                                alt="{{ $anggota['mahasiswa']['nama'] }}">
                                                        @else
                                                            <div
                                                                class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center">
                                                                <i class="fas fa-user text-gray-400 text-lg"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $anggota['mahasiswa']['nama'] }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $anggota['mahasiswa']['nim'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Angkatan -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $anggota['mahasiswa']['angkatan'] }}
                                                </div>
                                            </td>

                                            <!-- Kontak -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="space-y-1">
                                                    @if ($anggota['mahasiswa']['email'])
                                                        <a href="mailto:{{ $anggota['mahasiswa']['email'] }}"
                                                            class="flex items-center text-xs text-gray-600 hover:text-gray-900">
                                                            <i class="fas fa-envelope w-4 mr-1.5"></i>
                                                            <span
                                                                class="max-w-[180px]">{{ $anggota['mahasiswa']['email'] }}</span>
                                                        </a>
                                                    @endif
                                                    @if ($anggota['mahasiswa']['no_hp'])
                                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $anggota['mahasiswa']['no_hp']) }}"
                                                            target="_blank"
                                                            class="flex items-center text-xs text-gray-600 hover:text-gray-900">
                                                            <i class="fab fa-whatsapp w-4 mr-1.5"></i>
                                                            {{ $anggota['mahasiswa']['no_hp'] }}
                                                        </a>
                                                    @endif
                                                    @if (!$anggota['mahasiswa']['email'] && !$anggota['mahasiswa']['no_hp'])
                                                        <span class="text-xs text-gray-400">-</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Peran -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $anggota['peran'] === 'KETUA' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-800' }}">
                                                    @if ($anggota['peran'] === 'KETUA')
                                                        <i class="fas fa-crown mr-1"></i>
                                                    @endif
                                                    {{ $anggota['peran'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Dokumentasi Tab -->
                    <div id="dokumentasi-tab" class="tab-content hidden">
                        @if ($isKetua)
                            <div class="mb-6">
                                <button onclick="openUploadModal()"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Upload Dokumentasi
                                </button>
                            </div>
                        @endif

                        @if ($dokumentasi['images']->isEmpty() && $dokumentasi['documents']->isEmpty())
                            <div class="text-center py-16">
                                <div
                                    class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-camera text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada dokumentasi</h3>
                                <p class="text-xs text-gray-500">
                                    @if ($isKetua)
                                        Klik tombol "Upload Dokumentasi" untuk menambahkan foto atau dokumen
                                    @else
                                        Dokumentasi akan diupload oleh ketua kelompok
                                    @endif
                                </p>
                            </div>
                        @else
                            <!-- Dokumentasi Images - Update bagian ini -->
                            @if ($dokumentasi['images']->isNotEmpty())
                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                        Foto Dokumentasi ({{ $dokumentasi['images']->count() }})
                                    </h3>
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                        @foreach ($dokumentasi['images'] as $image)
                                            <div class="relative">
                                                <div class="group relative">
                                                    <!-- Image Container -->
                                                    <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer transition-all hover:border-gray-400"
                                                        onclick="openImageModal('{{ asset('storage/' . $image->file_path) }}', '{{ $image->judul }}', '{{ $image->deskripsi }}')">
                                                        <img src="{{ asset('storage/' . $image->file_path) }}"
                                                            alt="{{ $image->judul }}"
                                                            class="w-full h-full object-cover transition-transform group-hover:scale-105">
                                                    </div>

                                                    <!-- Overlay on hover -->
                                                    <div
                                                        class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all rounded-lg pointer-events-none flex items-center justify-center">
                                                        <i
                                                            class="fas fa-search-plus text-white text-xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                                    </div>

                                                    <!-- Delete Button - Only for uploader -->
                                                    @if ($isKetua && $image->uploaded_by === $mahasiswa->id_mahasiswa)
                                                        <button
                                                            onclick="deleteDokumentasi('{{ $image->id_kkn_dokumentasi }}')"
                                                            class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full px-2 shadow-lg hover:bg-red-700 transition-colors z-10"
                                                            title="Hapus gambar">
                                                            <i class="fas fa-times text-xs"></i>
                                                        </button>
                                                    @endif
                                                </div>

                                                <!-- Image Info -->
                                                <div class="mt-2">
                                                    <p class="text-xs font-medium text-gray-900 truncate"
                                                        title="{{ $image->judul }}">
                                                        {{ $image->judul }}
                                                    </p>
                                                    <p class="text-xs font-medium text-gray-900 truncate"
                                                        title="{{ $image->deskripsi }}">
                                                        {{ $image->deskripsi }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $image->uploader->pengguna->nama ?? 'Unknown' }} •
                                                        {{ $image->created_at->format('d M Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Dokumentasi Documents - Update bagian ini -->
                            @if ($dokumentasi['documents']->isNotEmpty())
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                        Dokumen ({{ $dokumentasi['documents']->count() }})
                                    </h3>
                                    <div class="space-y-3">
                                        @foreach ($dokumentasi['documents'] as $document)
                                            @php
                                                $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                                                $iconClass = 'fa-file';
                                                $iconColor = 'text-gray-400';
                                                $bgColor = 'bg-gray-100';

                                                if ($extension === 'pdf') {
                                                    $iconClass = 'fa-file-pdf';
                                                    $iconColor = 'text-red-500';
                                                    $bgColor = 'bg-red-50';
                                                } elseif (in_array($extension, ['doc', 'docx'])) {
                                                    $iconClass = 'fa-file-word';
                                                    $iconColor = 'text-blue-500';
                                                    $bgColor = 'bg-blue-50';
                                                } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                                    $iconClass = 'fa-file-excel';
                                                    $iconColor = 'text-green-500';
                                                    $bgColor = 'bg-green-50';
                                                }
                                            @endphp

                                            <div
                                                class="flex items-center space-x-3 p-4 {{ $bgColor }} rounded-lg border border-gray-200 hover:border-gray-300 transition-all group">
                                                <!-- Icon -->
                                                <div
                                                    class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded {{ $bgColor }}">
                                                    <i class="fas {{ $iconClass }} text-2xl {{ $iconColor }}"></i>
                                                </div>

                                                <!-- File Info -->
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate"
                                                        title="{{ $document->judul }}">
                                                        {{ $document->judul }}
                                                    </p>
                                                    <div class="flex items-center space-x-2 mt-1">
                                                        <span class="text-xs text-gray-500">
                                                            {{ $document->uploader->pengguna->nama ?? 'Unknown' }}
                                                        </span>
                                                        <span class="text-xs text-gray-400">•</span>
                                                        <span class="text-xs text-gray-500">
                                                            {{ $document->created_at->format('d M Y') }}
                                                        </span>
                                                        <span class="text-xs text-gray-400">•</span>
                                                        <span class="text-xs text-gray-500">
                                                            {{ $document->formatted_file_size }}
                                                        </span>
                                                    </div>
                                                    @if ($document->deskripsi)
                                                        <p class="text-xs text-gray-600 mt-1">
                                                            {{ \Str::limit($document->deskripsi, 80) }}
                                                        </p>
                                                    @endif
                                                </div>

                                                <!-- Actions -->
                                                <div class="flex items-center space-x-2">
                                                    <!-- View Button -->
                                                    <a href="{{ asset('storage/' . $document->file_path) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center px-3 py-2 text-xs font-medium rounded text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors"
                                                        title="Lihat file">
                                                        <i class="fas fa-external-link-alt mr-2"></i>
                                                        Lihat
                                                    </a>

                                                    <!-- Download Button -->
                                                    <a href="{{ asset('storage/' . $document->file_path) }}"
                                                        download="{{ $document->original_filename }}"
                                                        class="inline-flex items-center px-3 py-2 text-xs font-medium rounded text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors"
                                                        title="Download file">
                                                        <i class="fas fa-download mr-2"></i>
                                                        Download
                                                    </a>

                                                    <!-- Delete Button - Only for uploader -->
                                                    @if ($isKetua && $document->uploaded_by === $mahasiswa->id_mahasiswa)
                                                        <button
                                                            onclick="deleteDokumentasi('{{ $document->id_kkn_dokumentasi }}')"
                                                            class="inline-flex items-center px-3 py-2 text-xs font-medium rounded text-red-600 hover:bg-red-50 transition-colors"
                                                            title="Hapus dokumen">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Modal -->
        @if ($isKetua)
            <div id="uploadModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeUploadModal()">
                    </div>

                    <div class="relative bg-white rounded-lg max-w-2xl w-full shadow-xl max-h-[90vh] overflow-y-auto">
                        <div
                            class="sticky top-0 bg-white flex items-center justify-between p-6 border-b border-gray-200 z-10">
                            <h3 class="text-lg font-semibold text-gray-900">Upload Dokumentasi</h3>
                            <button onclick="closeUploadModal()"
                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <form
                            action="{{ route('mahasiswa.bimbingan.kelompok.upload-dokumentasi', $peserta->id_peserta_bimbingan) }}"
                            method="POST" enctype="multipart/form-data" id="uploadForm">
                            @csrf
                            <div class="p-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe File</label>
                                    <select name="file_type" id="file_type" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm"
                                        onchange="updateFileInput()">
                                        <option value="IMAGE">Gambar (JPG, PNG)</option>
                                        <option value="DOCUMENT">Dokumen (PDF, DOC, DOCX, XLS, XLSX)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                                    <input type="text" name="judul" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm"
                                        placeholder="Contoh: Kegiatan Gotong Royong">
                                    <p class="text-xs text-gray-500 mt-1">Jika upload lebih dari 1 file, akan diberi nomor
                                        otomatis</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi
                                        (Opsional)</label>
                                    <textarea name="deskripsi" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm"
                                        placeholder="Tambahkan deskripsi dokumentasi..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">File</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors cursor-pointer"
                                        onclick="document.getElementById('file_input').click()">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600 mb-1">
                                            Klik untuk memilih file atau drag & drop
                                        </p>
                                        <p class="text-xs text-gray-500" id="file_hint">
                                            Max 5MB per file, maksimal 10 file
                                        </p>
                                    </div>
                                    <input type="file" name="files[]" id="file_input" required accept="image/*"
                                        multiple class="hidden" onchange="handleFileSelect(event)">
                                </div>

                                <!-- Preview Area for Images -->
                                <div id="image_preview_area" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview Gambar</label>
                                    <div id="image_preview_container" class="grid grid-cols-3 gap-3">
                                        <!-- Image previews will be inserted here -->
                                    </div>
                                </div>

                                <!-- File List for Documents -->
                                <div id="document_list_area" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">File yang akan
                                        diupload</label>
                                    <div id="document_list_container" class="space-y-2">
                                        <!-- Document list will be inserted here -->
                                    </div>
                                </div>

                                <!-- Summary -->
                                <div id="upload_summary" class="hidden p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-700">Total file dipilih:</span>
                                        <span id="total_files" class="font-semibold text-gray-900">0</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm mt-1">
                                        <span class="text-gray-700">Total ukuran:</span>
                                        <span id="total_size" class="font-semibold text-gray-900">0 MB</span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="sticky bottom-0 bg-gray-50 flex items-center justify-end space-x-3 px-6 py-4 rounded-b-lg border-t">
                                <button type="button" onclick="closeUploadModal()"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" id="submit_button"
                                    class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                                    <i class="fas fa-upload mr-2"></i>
                                    Upload <span id="file_count_badge"
                                        class="hidden ml-1 px-2 py-0.5 bg-white text-gray-900 rounded-full text-xs">0</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

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
                        <div class="flex gap-3 mt-10 items-start">
                            <h1 class="text-xs font-medium text-gray-900">Deskripsi:</h1>
                            <p id="imageModalDescription" class="text-xs font-medium text-gray-900"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeDeleteModal()"></div>

                <div class="relative bg-white rounded-lg max-w-md w-full shadow-xl">
                    <div class="p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Hapus Dokumentasi?</h3>
                        <p class="text-sm text-gray-600 text-center mb-6">
                            File yang dihapus tidak dapat dikembalikan.
                        </p>
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="flex items-center justify-center space-x-3">
                                <button type="button" onclick="closeDeleteModal()"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                                    Ya, Hapus
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let selectedFiles = [];

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

            // Upload modal
            function openUploadModal() {
                document.getElementById('uploadModal').classList.remove('hidden');
                resetUploadForm();
            }

            function closeUploadModal() {
                document.getElementById('uploadModal').classList.add('hidden');
                resetUploadForm();
            }

            function resetUploadForm() {
                document.getElementById('uploadForm').reset();
                selectedFiles = [];
                document.getElementById('image_preview_area').classList.add('hidden');
                document.getElementById('document_list_area').classList.add('hidden');
                document.getElementById('upload_summary').classList.add('hidden');
                document.getElementById('image_preview_container').innerHTML = '';
                document.getElementById('document_list_container').innerHTML = '';
                document.getElementById('file_count_badge').classList.add('hidden');
            }

            function updateFileInput() {
                const fileType = document.getElementById('file_type').value;
                const fileInput = document.getElementById('file_input');
                const fileHint = document.getElementById('file_hint');

                if (fileType === 'IMAGE') {
                    fileInput.accept = 'image/*';
                    fileHint.textContent = 'Max 5MB per file, maksimal 10 file';
                } else {
                    fileInput.accept = '.pdf,.doc,.docx,.xls,.xlsx';
                    fileHint.textContent = 'Max 10MB per file, maksimal 10 file';
                }

                // Reset file selection when type changes - HANYA reset input file
                fileInput.value = '';
                selectedFiles = [];

                // Reset preview areas
                document.getElementById('image_preview_area').classList.add('hidden');
                document.getElementById('document_list_area').classList.add('hidden');
                document.getElementById('upload_summary').classList.add('hidden');
                document.getElementById('image_preview_container').innerHTML = '';
                document.getElementById('document_list_container').innerHTML = '';
                document.getElementById('file_count_badge').classList.add('hidden');
            }

            function handleFileSelect(event) {
                const files = Array.from(event.target.files);
                const fileType = document.getElementById('file_type').value;

                console.log('File Type:', fileType); // Debug
                console.log('Files:', files); // Debug

                if (files.length === 0) {
                    return;
                }

                if (files.length > 10) {
                    alert('Maksimal 10 file dapat diupload sekaligus');
                    event.target.value = '';
                    return;
                }

                // Validasi file sesuai tipe yang dipilih
                if (fileType === 'IMAGE') {
                    const invalidFiles = files.filter(file => !file.type.startsWith('image/'));
                    if (invalidFiles.length > 0) {
                        alert('Harap pilih file gambar saja (JPG, PNG, GIF)');
                        event.target.value = '';
                        return;
                    }
                } else if (fileType === 'DOCUMENT') {
                    const allowedExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
                    const invalidFiles = files.filter(file => {
                        const ext = file.name.split('.').pop().toLowerCase();
                        return !allowedExts.includes(ext);
                    });
                    if (invalidFiles.length > 0) {
                        alert('Harap pilih file dokumen saja (PDF, DOC, DOCX, XLS, XLSX)');
                        event.target.value = '';
                        return;
                    }
                }

                selectedFiles = files;

                // Pastikan memanggil fungsi yang benar sesuai tipe
                if (fileType === 'IMAGE') {
                    console.log('Displaying image previews'); // Debug
                    displayImagePreviews(files);
                } else if (fileType === 'DOCUMENT') {
                    console.log('Displaying document list'); // Debug
                    displayDocumentList(files);
                }

                displayUploadSummary(files);
            }

            function displayImagePreviews(files) {
                const container = document.getElementById('image_preview_container');
                const previewArea = document.getElementById('image_preview_area');

                container.innerHTML = '';

                files.forEach((file, index) => {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative';
                        div.innerHTML = `
                <div class="group relative">
                    <!-- Image Container with Hover -->
                    <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer transition-all hover:border-gray-400"
                        >
                        <img src="${e.target.result}" alt="${file.name}" 
                            class="w-full h-full object-cover transition-transform group-hover:scale-105">
                    </div>
                    
                    <!-- Remove Button - Always visible, positioned outside -->
                    <button type="button" onclick="removeFile(${index})" 
                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full px-2 shadow-lg hover:bg-red-700 transition-colors z-10">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                
                <!-- File Info -->
                <div class="mt-2">
                    <p class="text-xs text-gray-700 font-medium truncate" title="${file.name}">${file.name}</p>
                    <p class="text-xs text-gray-500">${formatFileSize(file.size)}</p>
                </div>
            `;
                        container.appendChild(div);
                    };

                    reader.readAsDataURL(file);
                });

                previewArea.classList.remove('hidden');
                document.getElementById('document_list_area').classList.add('hidden');
            }

            function displayDocumentList(files) {
                const container = document.getElementById('document_list_container');
                const listArea = document.getElementById('document_list_area');

                container.innerHTML = '';

                files.forEach((file, index) => {
                    const div = document.createElement('div');
                    div.className = 'relative';

                    const extension = file.name.split('.').pop().toLowerCase();
                    let iconClass = 'fa-file';
                    let iconColor = 'text-gray-400';
                    let bgColor = 'bg-gray-100';

                    if (extension === 'pdf') {
                        iconClass = 'fa-file-pdf';
                        iconColor = 'text-red-500';
                        bgColor = 'bg-red-50';
                    } else if (['doc', 'docx'].includes(extension)) {
                        iconClass = 'fa-file-word';
                        iconColor = 'text-blue-500';
                        bgColor = 'bg-blue-50';
                    } else if (['xls', 'xlsx'].includes(extension)) {
                        iconClass = 'fa-file-excel';
                        iconColor = 'text-green-500';
                        bgColor = 'bg-green-50';
                    }

                    // Create temporary URL for preview
                    const fileURL = URL.createObjectURL(file);

                    div.innerHTML = `
            <div class="flex items-center space-x-3 p-3 ${bgColor} rounded-lg border border-gray-200 hover:border-gray-300 transition-all group">
                <!-- Icon -->
                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded ${bgColor}">
                    <i class="fas ${iconClass} text-2xl ${iconColor}"></i>
                </div>
                
                <!-- File Info -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate group-hover:text-gray-700" title="${file.name}">
                        ${file.name}
                    </p>
                    <div class="flex items-center space-x-2 mt-1">
                        <span class="text-xs text-gray-500">${formatFileSize(file.size)}</span>
                        <span class="text-xs text-gray-400">•</span>
                        <span class="text-xs text-gray-500 uppercase">${extension}</span>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center space-x-2">
                    <!-- Preview Button for documents -->
                    <a href="${fileURL}" target="_blank" 
                        class="p-2 text-gray-600 hover:text-gray-900 hover:bg-white rounded transition-colors"
                        title="Lihat file">
                        <i class="fas fa-external-link-alt text-sm"></i>
                    </a>
                    
                    <!-- Remove Button -->
                    <button type="button" onclick="removeFile(${index})" 
                        class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors"
                        title="Hapus file">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
            </div>
        `;

                    container.appendChild(div);
                });

                listArea.classList.remove('hidden');
                document.getElementById('image_preview_area').classList.add('hidden');
            }

            // New function for preview modal from upload form
            function previewImageModal(imageSrc, title, description) {
                const modal = document.getElementById('imageModal');
                const modalTitle = document.getElementById('imageModalTitle');
                const modalDescription = document.getElementById('imageModalDescription');
                const modalImage = document.getElementById('imageModalContent');

                modalTitle.textContent = title;
                modalTitle.textContent = description;
                modalImage.src = imageSrc;
                modalImage.alt = title;

                modal.classList.remove('hidden');
            }

            function displayUploadSummary(files) {
                const summary = document.getElementById('upload_summary');
                const totalFiles = document.getElementById('total_files');
                const totalSize = document.getElementById('total_size');
                const badge = document.getElementById('file_count_badge');

                const totalBytes = files.reduce((sum, file) => sum + file.size, 0);

                totalFiles.textContent = files.length;
                totalSize.textContent = formatFileSize(totalBytes);
                badge.textContent = files.length;

                summary.classList.remove('hidden');
                badge.classList.remove('hidden');
            }

            function removeFile(index) {
                const fileInput = document.getElementById('file_input');
                const dt = new DataTransfer();

                selectedFiles.forEach((file, i) => {
                    if (i !== index) {
                        dt.items.add(file);
                    }
                });

                fileInput.files = dt.files;
                selectedFiles = Array.from(dt.files);

                if (selectedFiles.length === 0) {
                    resetUploadForm();
                } else {
                    const fileType = document.getElementById('file_type').value;
                    if (fileType === 'IMAGE') {
                        displayImagePreviews(selectedFiles);
                    } else {
                        displayDocumentList(selectedFiles);
                    }
                    displayUploadSummary(selectedFiles);
                }
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
            }

            // Image modal
            function openImageModal(imageSrc, title, description) {
                const modal = document.getElementById('imageModal');
                const modalTitle = document.getElementById('imageModalTitle');
                const modalDescription = document.getElementById('imageModalDescription');
                const modalImage = document.getElementById('imageModalContent');

                modalTitle.textContent = title;
                modalDescription.textContent = description;
                modalImage.src = imageSrc;
                modalImage.alt = title;

                modal.classList.remove('hidden');
            }

            function closeImageModal() {
                document.getElementById('imageModal').classList.add('hidden');
            }

            // Delete modal
            function deleteDokumentasi(idDokumentasi) {
                const modal = document.getElementById('deleteModal');
                const form = document.getElementById('deleteForm');
                form.action =
                    `{{ route('mahasiswa.bimbingan.kelompok.delete-dokumentasi', ['id_peserta_bimbingan' => $peserta->id_peserta_bimbingan, 'id_dokumentasi' => ':id']) }}`
                    .replace(':id', idDokumentasi);
                modal.classList.remove('hidden');
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.add('hidden');
            }

            // Drag and drop support
            const dropZone = document.querySelector('.border-dashed');
            if (dropZone) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.add('border-gray-600', 'bg-gray-50');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.remove('border-gray-600', 'bg-gray-50');
                    }, false);
                });

                dropZone.addEventListener('drop', (e) => {
                    const files = e.dataTransfer.files;
                    document.getElementById('file_input').files = files;
                    handleFileSelect({
                        target: {
                            files: files
                        }
                    });
                }, false);
            }

            // Close modals with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeUploadModal();
                    closeImageModal();
                    closeDeleteModal();
                }
            });

            // Make functions globally accessible
            window.switchTab = switchTab;
            window.openUploadModal = openUploadModal;
            window.closeUploadModal = closeUploadModal;
            window.updateFileInput = updateFileInput;
            window.handleFileSelect = handleFileSelect;
            window.removeFile = removeFile;
            window.openImageModal = openImageModal;
            window.closeImageModal = closeImageModal;
            window.deleteDokumentasi = deleteDokumentasi;
            window.closeDeleteModal = closeDeleteModal;
            window.previewImageModal = previewImageModal;
        </script>
    @endpush
@endsection
