@extends('layouts.app')

@section('title', 'Manajemen Jenjang Pendidikan')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Manajemen Jenjang Pendidikan</h1>
                            <p class="text-sm text-gray-600">Kelola data jenjang pendidikan akademik</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <button onclick="openModal('create')"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Jenjang
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="jenjangTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode Jenjang</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Jenjang</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Program Studi</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="jenjangTableBody">
                            @forelse($jenjangs as $jenjang)
                                <tr class="hover:bg-gray-50 transition-colors duration-200 jenjang-row" data-searchable>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-xs font-medium text-gray-900 searchable-kode">
                                        {{ $jenjang->kode_jenjang_pendidikan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900 searchable-nama">
                                        {{ $jenjang->nama_jenjang_pendidikan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-900">
                                        <div class="flex items-center">
                                            <span class="text-gray-600">{{ $jenjang->programStudi->count() }} Program</span>
                                            @if ($jenjang->programStudi->count() > 0)
                                                <span
                                                    class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Tersedia
                                                </span>
                                            @else
                                                <span
                                                    class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Kosong
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            {{-- Tombol Kelola Program Studi --}}
                                            <div class="relative group">
                                                <a href="{{ route('program-studi.index', ['jenjang' => $jenjang->id_jenjang_pendidikan]) }}"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                    <i class="fas fa-graduation-cap text-xs"></i>
                                                </a>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                    Kelola Program Studi
                                                    <div
                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Tombol Edit --}}
                                            <div class="relative group">
                                                <button
                                                    onclick="openEditModal(
                                                        '{{ $jenjang->id_jenjang_pendidikan }}',
                                                        '{{ addslashes($jenjang->kode_jenjang_pendidikan) }}',
                                                        '{{ addslashes($jenjang->nama_jenjang_pendidikan) }}'
                                                    )"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                    <i class="text-xs fas fa-edit"></i>
                                                </button>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                    Edit Jenjang
                                                    <div
                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Tombol Hapus --}}
                                            <div class="relative group">
                                                <button
                                                    onclick="confirmDelete('{{ $jenjang->id_jenjang_pendidikan }}', '{{ addslashes($jenjang->nama_jenjang_pendidikan) }}')"
                                                    class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200"
                                                    {{ $jenjang->programStudi->count() > 0 ? 'disabled title="Tidak dapat menghapus jenjang yang masih memiliki program studi"' : '' }}>
                                                    <i
                                                        class="fas fa-trash text-xs {{ $jenjang->programStudi->count() > 0 ? 'text-gray-400' : '' }}"></i>
                                                </button>
                                                <div
                                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-50">
                                                    {{ $jenjang->programStudi->count() > 0 ? 'Hapus program studi terlebih dahulu' : 'Hapus Jenjang' }}
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
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                </path>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 mb-2">Belum ada data jenjang
                                                pendidikan</p>
                                            <p class="text-xs text-gray-500">Tambahkan jenjang pendidikan baru dengan klik
                                                tombol
                                                "Tambah Jenjang"</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($jenjangs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $jenjangs->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div id="jenjangModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

                <!-- Modal panel -->
                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg mx-auto">

                    <form id="jenjangForm" method="POST" action="{{ route('jenjang.store') }}">
                        @csrf
                        <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 id="modalTitle" class="text-lg font-heading font-semibold text-gray-900">Tambah
                                    Jenjang Pendidikan</h3>
                                <!-- Tombol close untuk mobile -->
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
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Kode Jenjang Pendidikan
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="kode_jenjang_pendidikan" id="kode_jenjang_pendidikan"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Contoh: S1, S2, S3, D3, D4" maxlength="50" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Nama Jenjang Pendidikan
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="nama_jenjang_pendidikan" id="nama_jenjang_pendidikan"
                                        class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                        placeholder="Contoh: Sarjana (S1), Magister (S2), Doktor (S3)" maxlength="50"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- Button area -->
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

    @push('scripts')
        <script>
            function updateEmptyState(visibleCount, searchTerm) {
                const tbody = document.getElementById('jenjangTableBody');
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
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
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
                const modal = document.getElementById('jenjangModal');
                const form = document.getElementById('jenjangForm');
                const modalTitle = document.getElementById('modalTitle');
                const submitText = document.getElementById('submitText');

                // Reset form
                form.reset();

                if (type === 'create') {
                    form.action = '{{ route('jenjang.store') }}';
                    form.querySelector('input[name="_method"]')?.remove();
                    modalTitle.textContent = 'Tambah Jenjang Pendidikan';
                    submitText.textContent = 'Simpan';
                }

                modal.classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('kode_jenjang_pendidikan').focus();
                }, 100);
            }

            function openEditModal(id, kode, nama) {
                const modal = document.getElementById('jenjangModal');
                const form = document.getElementById('jenjangForm');
                const modalTitle = document.getElementById('modalTitle');
                const submitText = document.getElementById('submitText');

                // Set form untuk edit
                form.action = "{{ route('jenjang.index') }}/" + id;

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
                document.getElementById('kode_jenjang_pendidikan').value = kode || '';
                document.getElementById('nama_jenjang_pendidikan').value = nama || '';

                modalTitle.textContent = 'Edit Jenjang Pendidikan';
                submitText.textContent = 'Update';

                modal.classList.remove('hidden');
                setTimeout(() => {
                    document.getElementById('kode_jenjang_pendidikan').focus();
                }, 100);
            }

            function closeModal() {
                const modal = document.getElementById('jenjangModal');
                modal.classList.add('hidden');
            }

            // Action Functions
            function confirmDelete(id, name) {
                if (confirm(`Yakin ingin menghapus jenjang pendidikan "${name}"?`)) {
                    const form = document.getElementById('delete-form');
                    form.action = `/jenjang/${id}`;
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
            document.getElementById('jenjangForm').addEventListener('submit', function(e) {
                const requiredFields = ['kode_jenjang_pendidikan', 'nama_jenjang_pendidikan'];

                for (let fieldName of requiredFields) {
                    const field = document.getElementById(fieldName);
                    if (!field.value.trim()) {
                        e.preventDefault();
                        alert(`Field ${field.previousElementSibling.textContent.replace(' *', '')} tidak boleh kosong`);
                        field.focus();
                        return false;
                    }
                }

                // Additional validation for field lengths
                const kodeField = document.getElementById('kode_jenjang_pendidikan');
                const namaField = document.getElementById('nama_jenjang_pendidikan');

                if (kodeField.value.length > 50) {
                    e.preventDefault();
                    alert('Kode jenjang pendidikan maksimal 50 karakter');
                    kodeField.focus();
                    return false;
                }

                if (namaField.value.length > 50) {
                    e.preventDefault();
                    alert('Nama jenjang pendidikan maksimal 50 karakter');
                    namaField.focus();
                    return false;
                }
            });
        </script>
    @endpush
@endsection
