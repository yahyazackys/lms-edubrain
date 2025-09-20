@extends('layouts.app')

@section('title', 'Approval KRS - Pembimbing Akademik')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-lg font-semibold font-heading text-gray-900">Approval KRS Mahasiswa</h1>
                            <p class="text-xs text-gray-600">Kelola dan setujui KRS mahasiswa bimbingan Anda</p>
                        </div>

                        <form method="GET" action="{{ route('krs.approval.index') }}" id="semesterForm">
                            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                                <label class="text-xs font-medium text-gray-700">Pilih Periode Semester:</label>
                                <select name="semester" id="semesterSelect"
                                    class="w-full sm:w-auto py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent bg-white"
                                    onchange="document.getElementById('semesterForm').submit()">
                                    <option value="">-- Pilih Semester --</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id_semester }}"
                                            {{ $selectedSemesterId == $semester->id_semester ? 'selected' : '' }}>
                                            {{ $semester->nama_semester }}
                                            {{ $semester->is_active ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                @if ($selectedSemester)
                    <!-- Statistik Bar -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-center">
                            <!-- Total Mahasiswa -->
                            <div class="flex items-center justify-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-users text-gray-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3 text-left">
                                    <p class="text-lg font-semibold text-gray-900">{{ $statistik['total_mahasiswa'] }}</p>
                                    <p class="text-xs ">Total Mahasiswa</p>
                                </div>
                            </div>

                            <!-- Belum Submit -->
                            <div class="flex items-center justify-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-edit text-orange-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3 text-left">
                                    <p class="text-lg font-semibold text-gray-900">{{ $statistik['belum_submit'] }}</p>
                                    <p class="text-xs">Belum Submit</p>
                                </div>
                            </div>

                            <!-- Menunggu Review -->
                            <div class="flex items-center justify-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-clock text-amber-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3 text-left">
                                    <p class="text-lg font-semibold text-gray-900">{{ $statistik['pending'] }}</p>
                                    <p class="text-xs">Menunggu Review</p>
                                </div>
                            </div>

                            <!-- Ditolak -->
                            <div class="flex items-center justify-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-times-circle text-red-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3 text-left">
                                    <p class="text-lg font-semibold text-gray-900">{{ $statistik['rejected'] }}</p>
                                    <p class="text-xs">Ditolak</p>
                                </div>
                            </div>

                            <!-- Disetujui -->
                            <div class="flex items-center justify-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-check-circle text-green-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3 text-left">
                                    <p class="text-lg font-semibold text-gray-900">{{ $statistik['approved'] }}</p>
                                    <p class="text-xs">Sudah Disetujui</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if (!$selectedSemester)
                <!-- Empty State - Pilih Semester -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-calendar text-6xl text-gray-400 mb-4"></i>
                            <p class="text-lg font-medium text-gray-900 mb-2">Pilih Periode Semester</p>
                            <p class="text-sm text-gray-500">Pilih periode semester terlebih dahulu untuk melihat KRS
                                mahasiswa bimbingan Anda</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Filter & Search -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                    <div class="px-6 py-4">
                        <form method="GET" action="{{ route('krs.approval.index') }}" class="space-y-4">
                            <input type="hidden" name="semester" value="{{ $selectedSemesterId }}">

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Status Filter -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Status KRS</label>
                                    <select name="status"
                                        class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                        <option value="">Semua Status</option>
                                        <option value="BELUM_SUBMIT"
                                            {{ request('status') == 'BELUM_SUBMIT' ? 'selected' : '' }}>
                                            Belum Submit KRS
                                        </option>
                                        <option value="SUBMITTED" {{ request('status') == 'SUBMITTED' ? 'selected' : '' }}>
                                            Menunggu Review
                                        </option>
                                        <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>
                                            Ditolak (Menunggu Revisi)
                                        </option>
                                        <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>
                                            Sudah Disetujui
                                        </option>
                                    </select>
                                </div>

                                <!-- Program Studi Filter -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Program Studi</label>
                                    <select name="program_studi"
                                        class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                        <option value="">Semua Program Studi</option>
                                        @foreach ($programStudis as $prodi)
                                            <option value="{{ $prodi->id_program_studi }}"
                                                {{ request('program_studi') == $prodi->id_program_studi ? 'selected' : '' }}>
                                                {{ $prodi->nama_program_studi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Search -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Cari Mahasiswa</label>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Nama atau NIM mahasiswa..."
                                        class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                </div>

                                <!-- Actions -->
                                <div class="flex items-end space-x-2">
                                    <button type="submit"
                                        class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                        <i class="fa-solid fa-search mr-2"></i>
                                        Filter
                                    </button>
                                    <a href="{{ route('krs.approval.index', ['semester' => $selectedSemesterId]) }}"
                                        class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                        <i class="fa-solid fa-refresh mr-1"></i>
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="bg-gray-50 border border-gray-200 text-gray-800 px-4 py-3 rounded-lg mb-6">
                        <div class="flex">
                            <svg class="w-4 h-4 text-gray-600 mt-0.5 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-gray-100 border border-gray-300 text-gray-800 px-4 py-3 rounded-lg mb-6">
                        <div class="flex">
                            <svg class="w-4 h-4 text-gray-600 mt-0.5 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Daftar Mahasiswa Bimbingan -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    @if ($mahasiswaBimbingan->count() > 0)
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
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total SKS
                                        </th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status KRS
                                        </th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal Submit
                                        </th>
                                        <th
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($mahasiswaBimbingan as $data)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0">
                                                        <div
                                                            class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                            <i class="fa-solid fa-user text-gray-600"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-xs font-medium text-gray-900">
                                                            {{ $data->mahasiswa->pengguna->nama }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ $data->mahasiswa->nim }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-xs text-gray-900">
                                                    {{ $data->mahasiswa->programStudi->nama_program_studi }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                @if ($data->has_registration && $data->pesertaKelasKuliah->count() > 0)
                                                    @php
                                                        $totalSks = $data->pesertaKelasKuliah->sum(function ($p) {
                                                            return $p->mataKuliah->sks_mata_kuliah;
                                                        });
                                                        $batasSks = $data->batas_sks ?? 24;
                                                    @endphp

                                                    <div class="flex flex-col items-center space-y-1">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ $totalSks }}/{{ $batasSks }} SKS
                                                        </span>

                                                        @if ($totalSks > $batasSks)
                                                            <span
                                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800">
                                                                <i
                                                                    class="fa-solid fa-exclamation-triangle mr-1 text-xs"></i>
                                                                Melebihi Batas
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                @if ($data->status_krs === 'BELUM_SUBMIT' || !$data->has_registration)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                        Belum Submit KRS
                                                    </span>
                                                @else
                                                    @php
                                                        $hasRejectedCourses = false;
                                                        $hasApprovedCourses = false;
                                                        $hasSelectedCourses = false;
                                                        $totalCourses = 0;
                                                        $approvedCount = 0;

                                                        if (
                                                            $data->pesertaKelasKuliah &&
                                                            $data->pesertaKelasKuliah->count() > 0
                                                        ) {
                                                            $totalCourses = $data->pesertaKelasKuliah->count();

                                                            foreach ($data->pesertaKelasKuliah as $peserta) {
                                                                switch ($peserta->status_mata_kuliah) {
                                                                    case 'REJECTED':
                                                                        $hasRejectedCourses = true;
                                                                        break;
                                                                    case 'APPROVED':
                                                                        $hasApprovedCourses = true;
                                                                        $approvedCount++;
                                                                        break;
                                                                    case 'SELECTED':
                                                                        $hasSelectedCourses = true;
                                                                        break;
                                                                }
                                                            }
                                                        }

                                                        if ($data->status_krs === 'REJECTED') {
                                                            $displayStatus = 'rejected';
                                                        } elseif ($data->status_krs === 'APPROVED') {
                                                            $displayStatus = 'approved';
                                                        } elseif ($data->status_krs === 'SUBMITTED') {
                                                            if ($hasRejectedCourses) {
                                                                $displayStatus = 'needs_revision';
                                                            } elseif ($hasApprovedCourses && $hasSelectedCourses) {
                                                                $displayStatus = 'under_review';
                                                            } elseif (
                                                                $hasApprovedCourses &&
                                                                !$hasSelectedCourses &&
                                                                !$hasRejectedCourses
                                                            ) {
                                                                $displayStatus = 'approved';
                                                            } elseif (
                                                                $hasSelectedCourses &&
                                                                !$hasApprovedCourses &&
                                                                !$hasRejectedCourses
                                                            ) {
                                                                $displayStatus = 'pending_review';
                                                            } else {
                                                                $displayStatus = 'under_review';
                                                            }
                                                        } else {
                                                            $displayStatus = 'draft';
                                                        }
                                                    @endphp

                                                    @switch($displayStatus)
                                                        @case('rejected')
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-300 text-gray-800">
                                                                <i class="fa-solid fa-times-circle mr-1"></i>
                                                                Ditolak
                                                            </span>
                                                        @break

                                                        @case('needs_revision')
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
                                                                <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                                                                Perlu Revisi
                                                            </span>
                                                        @break

                                                        @case('approved')
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-800 text-white">
                                                                <i class="fa-solid fa-check-circle mr-1"></i>
                                                                Disetujui
                                                                @if ($totalCourses > 0)
                                                                    ({{ $approvedCount }}/{{ $totalCourses }})
                                                                @endif
                                                            </span>
                                                        @break

                                                        @case('pending_review')
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                                <i class="fa-solid fa-clock mr-1"></i>
                                                                Menunggu Review
                                                            </span>
                                                        @break

                                                        @case('under_review')
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                                                                <i class="fa-solid fa-eye mr-1"></i>
                                                                Dalam Review ({{ $approvedCount }}/{{ $totalCourses }})
                                                            </span>
                                                        @break

                                                        @default
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                                <i class="fa-solid fa-edit mr-1"></i>
                                                                Draft
                                                            </span>
                                                    @endswitch
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                @if ($data->tanggal_submit)
                                                    <div class="text-xs text-gray-900">
                                                        {{ $data->tanggal_submit->format('d M Y') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $data->tanggal_submit->format('H:i') }}
                                                    </div>
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                <div class="flex items-center justify-center space-x-2">
                                                    @if (!$data->has_registration || $data->status_krs === 'BELUM_SUBMIT')
                                                        <!-- Belum Submit KRS -->
                                                        <span class="text-xs text-gray-400">-</span>
                                                    @else
                                                        <!-- Review Button - Hanya untuk yang sudah submit -->
                                                        <a href="{{ route('krs.approval.review', $data->id_registrasi_mahasiswa) }}"
                                                            class="inline-flex items-center px-3 py-1 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors duration-200">
                                                            <i class="fa-solid fa-eye mr-1"></i>
                                                            Review
                                                        </a>

                                                        @if ($data->status_krs === 'REJECTED')
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 bg-gray-200 text-gray-700 text-xs font-medium rounded">
                                                                <i class="fa-solid fa-info-circle mr-1"></i>
                                                                Menunggu Revisi
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($mahasiswaBimbingan->hasPages())
                            <div class="px-6 py-4 border-t border-gray-200">
                                {{ $mahasiswaBimbingan->appends(request()->query())->links('pagination::tailwind') }}
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 7h.01M9 16h.01">
                                </path>
                            </svg>
                            <p class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Mahasiswa Bimbingan</p>
                            <p class="text-sm text-gray-500">
                                @if (request()->hasAny(['status', 'program_studi', 'search']))
                                    Tidak ada mahasiswa yang sesuai dengan filter yang dipilih.
                                @else
                                    Belum ada mahasiswa yang ditugaskan sebagai bimbingan PA untuk semester ini.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function quickApprove(registrasiId, namaMahasiswa) {
                if (confirm(
                        `Setujui KRS mahasiswa "${namaMahasiswa}"?\n\nCatatan: Semua mata kuliah akan disetujui secara otomatis.`
                    )) {
                    // Submit form untuk approve
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/krs/approval/${registrasiId}/approve`;

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';

                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            function quickReject(registrasiId, namaMahasiswa) {
                const alasan = prompt(`Alasan penolakan KRS mahasiswa "${namaMahasiswa}":`);
                if (alasan && alasan.trim() !== '') {
                    // Submit form untuk reject
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/krs/approval/${registrasiId}/reject`;

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';

                    const alasanInput = document.createElement('input');
                    alasanInput.type = 'hidden';
                    alasanInput.name = 'alasan_reject';
                    alasanInput.value = alasan;

                    form.appendChild(csrfInput);
                    form.appendChild(alasanInput);
                    document.body.appendChild(form);
                    form.submit();
                } else if (alasan !== null) {
                    alert('Alasan penolakan harus diisi!');
                }
            }
        </script>
    @endpush
@endsection
