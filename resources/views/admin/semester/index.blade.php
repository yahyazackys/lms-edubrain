@extends('layouts.app')

@section('title', 'Manajemen Semester')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Manajemen Semester</h1>
                            <p class="text-sm text-gray-600">Kelola data semester akademik</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <button onclick="openModal('create')"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Semester
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Bar - KEMBALI KE REALTIME -->
                <div class="px-6 py-4 bg-gray-50">
                    <div class="relative max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="searchInput" placeholder="Cari kode atau nama semester..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-xs placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="semesterTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode Semester</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Semester</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipe</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Mulai</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Selesai</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="semesterTableBody">
                            @forelse($semesters as $semester)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 semester-row" data-searchable>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-xs font-medium text-gray-900 searchable-kode">
                                        {{ $semester->kode_semester }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900 searchable-nama">
                                        {{ $semester->nama_semester }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $semester->tipe == 'ganjil' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($semester->tipe) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">
                                        {{ $semester->tanggal_mulai ? \Carbon\Carbon::parse($semester->tanggal_mulai)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">
                                        {{ $semester->tanggal_selesai ? \Carbon\Carbon::parse($semester->tanggal_selesai)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $semester->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $semester->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            {{-- Tombol Aktifkan Semester --}}
                                            @if (!$semester->is_active)
                                                <div class="relative group">
                                                    <button
                                                        onclick="confirmActivate('{{ $semester->id_semester }}', '{{ addslashes($semester->nama_semester) }}')"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="text-xs fas fa-play"></i>
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Aktifkan Semester
                                                        <div
                                                            class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Tombol Nonaktifkan Semester --}}
                                            @elseif($semester->is_active)
                                                <div class="relative group">
                                                    <button
                                                        onclick="confirmDeactivate('{{ $semester->id_semester }}', '{{ addslashes($semester->nama_semester) }}')"
                                                        class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                        <i class="text-xs fas fa-pause"></i>
                                                    </button>
                                                    <div
                                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                        Nonaktifkan Semester
                                                        <div
                                                            class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Tombol Edit --}}
                                            <div class="relative group">
                                                <button
                                                    onclick="openEditModal(
                                                        '{{ $semester->id_semester }}',
                                                        '{{ addslashes($semester->kode_semester) }}',
                                                        '{{ addslashes($semester->nama_semester) }}',
                                                        '{{ $semester->tipe }}',
                                                        '{{ $semester->tanggal_mulai }}',
                                                        '{{ $semester->tanggal_selesai }}'
                                                    )"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                    <i class="text-xs fas fa-edit"></i>
                                                </button>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                    Edit Semester
                                                    <div
                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Tombol Hapus --}}
                                            <div class="relative group">
                                                <button
                                                    onclick="confirmDelete('{{ $semester->id_semester }}', '{{ addslashes($semester->nama_semester) }}')"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                    Hapus Semester
                                                    <div
                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr id="empty-state-row">
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 mb-2">Belum ada data semester</p>
                                            <p class="text-xs text-gray-500">Tambahkan semester baru dengan klik tombol
                                                "Tambah Semester"</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($semesters->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $semesters->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div id="semesterModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <!-- GANTI bagian ini untuk responsive yang lebih baik -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

                <!-- Modal panel dengan responsive yang diperbaiki -->
                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">

                    <form id="semesterForm" method="POST" action="{{ route('semester.store') }}">
                        @csrf
                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 id="modalTitle" class="text-lg font-heading font-semibold text-gray-900">Tambah
                                    Semester</h3>
                                <!-- Tambahkan tombol close untuk mobile -->
                                <button type="button" onclick="closeModal()"
                                    class="sm:hidden text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Kode Semester</label>
                                    <input type="text" name="kode_semester" id="kode_semester"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Contoh: 20241" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Nama Semester</label>
                                    <input type="text" name="nama_semester" id="nama_semester"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Contoh: Semester Ganjil 2024/2025" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Tipe Semester</label>
                                    <select name="tipe" id="tipe"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="ganjil">Ganjil</option>
                                        <option value="genap">Genap</option>
                                    </select>
                                </div>

                                <!-- Grid responsive: 1 kolom di mobile, 2 kolom di tablet+ -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                                        <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                                        <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                                            class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Button area dengan padding yang konsisten -->
                        <div
                            class="bg-gray-50 px-4 sm:px-6 py-4 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                            <button type="button" onclick="closeModal()"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                Batal
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center px-4 py-2 text-xs font-medium text-white bg-gray-900 border border-transparent rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900">
                                <span id="submitText">Simpan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms untuk Actions -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <form id="activate-form" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
    </form>

    <form id="deactivate-form" method="POST" style="display: none;">
        @csrf
        @method('PATCH')
    </form>

    @push('scripts')
        <script>
            // REALTIME SEARCH FUNCTIONALITY
            document.getElementById('searchInput').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const tbody = document.getElementById('semesterTableBody');
                const rows = tbody.querySelectorAll('tr[data-searchable]');
                let visibleCount = 0;

                rows.forEach(row => {
                    const kodeCell = row.querySelector('.searchable-kode');
                    const namaCell = row.querySelector('.searchable-nama');

                    const kodeText = kodeCell ? kodeCell.textContent.toLowerCase() : '';
                    const namaText = namaCell ? namaCell.textContent.toLowerCase() : '';

                    const isMatch = searchTerm === '' ||
                        kodeText.includes(searchTerm) ||
                        namaText.includes(searchTerm);

                    if (isMatch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update empty state
                updateEmptyState(visibleCount, searchTerm);
            });

            function updateEmptyState(visibleCount, searchTerm) {
                const tbody = document.getElementById('semesterTableBody');
                let emptyRow = tbody.querySelector('tr[data-empty-search]');

                // Remove existing empty row
                if (emptyRow) {
                    emptyRow.remove();
                }

                // Show empty message if no results
                if (visibleCount === 0 && searchTerm !== '') {
                    emptyRow = document.createElement('tr');
                    emptyRow.setAttribute('data-empty-search', 'true');
                    emptyRow.innerHTML = `
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-900 mb-2">Tidak ada hasil ditemukan</p>
                                <p class="text-xs text-gray-500">Coba kata kunci pencarian yang berbeda untuk "${searchTerm}"</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(emptyRow);
                }
            }

            // Modal Functions
            function openModal(type) {
                const modal = document.getElementById('semesterModal');
                const form = document.getElementById('semesterForm');
                const modalTitle = document.getElementById('modalTitle');
                const submitText = document.getElementById('submitText');

                // Reset form
                form.reset();

                if (type === 'create') {
                    form.action = '{{ route('semester.store') }}';
                    form.querySelector('input[name="_method"]')?.remove();
                    modalTitle.textContent = 'Tambah Semester';
                    submitText.textContent = 'Simpan';
                }

                modal.classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('kode_semester').focus();
                }, 100);
            }

            function openEditModal(id, kode, nama, tipe, tanggalMulai, tanggalSelesai) {
                const modal = document.getElementById('semesterModal');
                const form = document.getElementById('semesterForm');
                const modalTitle = document.getElementById('modalTitle');
                const submitText = document.getElementById('submitText');

                // Set form untuk edit
                form.action = "{{ route('semester.index') }}/" + id;

                // Add method spoofing untuk PUT
                let methodInput = form.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    form.appendChild(methodInput);
                }
                methodInput.value = 'PUT';

                // Set values
                document.getElementById('kode_semester').value = kode || '';
                document.getElementById('nama_semester').value = nama || '';
                document.getElementById('tipe').value = tipe || '';
                document.getElementById('tanggal_mulai').value = tanggalMulai || '';
                document.getElementById('tanggal_selesai').value = tanggalSelesai || '';

                modalTitle.textContent = 'Edit Semester';
                submitText.textContent = 'Update';

                modal.classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('kode_semester').focus();
                }, 100);
            }

            function closeModal() {
                const modal = document.getElementById('semesterModal');
                modal.classList.add('hidden');
            }

            // Action Functions - MENGGUNAKAN LAYOUT ALERTS
            function confirmDelete(id, name) {
                if (confirm(`Yakin ingin menghapus semester "${name}"?`)) {
                    const form = document.getElementById('delete-form');
                    form.action = `/semester/${id}`;
                    form.submit();
                }
            }

            function confirmActivate(id, name) {
                if (confirm(`Yakin ingin mengaktifkan semester "${name}"?`)) {
                    const form = document.getElementById('activate-form');
                    form.action = `/semester/${id}/activate`;
                    form.submit();
                }
            }

            function confirmDeactivate(id, name) {
                if (confirm(`Yakin ingin menonaktifkan semester "${name}"?`)) {
                    const form = document.getElementById('deactivate-form');
                    form.action = `/semester/${id}/deactivate`;
                    form.submit();
                }
            }

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });

            // Form validation
            document.getElementById('semesterForm').addEventListener('submit', function(e) {
                const requiredFields = ['kode_semester', 'nama_semester', 'tipe'];

                for (let fieldName of requiredFields) {
                    const field = document.getElementById(fieldName);
                    if (!field.value.trim()) {
                        e.preventDefault();
                        alert(`Field ${field.previousElementSibling.textContent.replace(' *', '')} tidak boleh kosong`);
                        field.focus();
                        return false;
                    }
                }

                // Validate dates
                const tanggalMulai = document.getElementById('tanggal_mulai').value;
                const tanggalSelesai = document.getElementById('tanggal_selesai').value;

                if (tanggalMulai && tanggalSelesai && tanggalMulai > tanggalSelesai) {
                    e.preventDefault();
                    alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai');
                    document.getElementById('tanggal_selesai').focus();
                    return false;
                }
            });
        </script>
    @endpush
@endsection
