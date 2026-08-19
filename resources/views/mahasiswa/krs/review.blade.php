@extends('layouts.app')

@section('title', 'Review KRS - Konfirmasi Sebelum Submit')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-lg font-semibold font-heading text-gray-900">Review Kartu Rencana Studi</h1>
                            <p class="text-xs text-gray-600">Periksa kembali pilihan mata kuliah Anda sebelum disubmit ke
                                Pembimbing Akademik</p>
                        </div>

                        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                            <a href="{{ route('krs.pilih-mata-kuliah', $selectedSemester->id_semester) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Edit Pilihan
                            </a>

                            @php
                                $canSubmit =
                                    $registrasiKrs->pembimbingAkademik && $totalSks > 0 && $totalSks <= $batasSks;
                            @endphp

                            @if ($canSubmit)
                                <button onclick="confirmSubmitKrs()"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Submit KRS ke PA
                                </button>
                            @else
                                <button disabled
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-500 text-xs font-medium rounded-lg cursor-not-allowed">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Submit KRS ke PA
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Summary Bar -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <p class="text-base font-semibold text-gray-900">{{ $mahasiswa->pengguna->nama }}</p>
                            <p class="text-xs text-gray-500">{{ $mahasiswa->nim }}</p>
                        </div>
                        <div>
                            <p class="text-base font-semibold text-gray-900">{{ $selectedSemester->nama_semester }}</p>
                            <p class="text-xs text-gray-500">Semester Akademik</p>
                        </div>
                        <div>
                            @php
                                $totalReguler = $mataKuliahTerpilih->where('jenis', 'reguler')->count();
                                $totalBimbingan = $mataKuliahTerpilih->where('jenis', 'bimbingan')->count();
                            @endphp
                            <p class="text-base font-semibold text-gray-900">{{ $mataKuliahTerpilih->count() }}</p>
                            <p class="text-xs text-gray-500">Mata Kuliah ({{ $totalReguler }}R + {{ $totalBimbingan }}B)</p>
                        </div>
                        <div>
                            <p
                                class="text-base font-semibold text-gray-900 {{ $totalSks > $batasSks ? 'text-red-600' : '' }}">
                                {{ $totalSks }}/{{ $batasSks }}
                            </p>
                            <p class="text-xs text-gray-500">Total SKS</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation Alerts -->
            @if (!$registrasiKrs->pembimbingAkademik)
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-red-400 mt-0.5 mr-3"></i>
                        <div>
                            <h4 class="font-medium">Pembimbing Akademik Belum Ditentukan</h4>
                            <p class="text-xs mt-1">Anda belum mendapatkan Pembimbing Akademik untuk semester ini. Silakan
                                hubungi bagian akademik untuk mendapatkan PA sebelum dapat melakukan submit KRS.</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($totalSks > $batasSks)
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-red-400 mt-0.5 mr-3"></i>
                        <div>
                            <h4 class="font-medium">Beban SKS Melebihi Batas Maksimal</h4>
                            <p class="text-xs mt-1">Total SKS ({{ $totalSks }}) melebihi batas maksimal yang diizinkan
                                ({{ $batasSks }} SKS). Harap kurangi beberapa mata kuliah sebelum submit.</p>
                        </div>
                    </div>
                </div>
            @elseif($totalSks < 12)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mt-0.5 mr-3"></i>
                        <div>
                            <h4 class="font-medium">Beban SKS Rendah</h4>
                            <p class="text-xs mt-1">Total SKS ({{ $totalSks }}) tergolong rendah. Anda masih bisa
                                menambah mata kuliah jika diperlukan (batas: {{ $batasSks }} SKS).</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($totalSks == 0)
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-red-400 mt-0.5 mr-3"></i>
                        <div>
                            <h4 class="font-medium">Error: Tidak Ada Mata Kuliah Dipilih</h4>
                            <p class="text-xs mt-1">Anda belum memilih mata kuliah apapun. Silakan kembali ke halaman
                                pemilihan mata kuliah.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content - Daftar Mata Kuliah -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Mata Kuliah Reguler -->
                    @php
                        $mataKuliahReguler = $mataKuliahTerpilih->where('jenis', 'reguler');
                        $mataKuliahBimbingan = $mataKuliahTerpilih->where('jenis', 'bimbingan');
                    @endphp

                    @if ($mataKuliahReguler->count() > 0)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-chalkboard-teacher text-green-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-semibold text-gray-900">Mata Kuliah Reguler</h3>
                                        <p class="text-xs text-gray-500">{{ $mataKuliahReguler->count() }} mata kuliah
                                            dengan jadwal tetap</p>
                                    </div>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Mata Kuliah</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Kelas</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dosen</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Jadwal</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                SKS</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($mataKuliahReguler as $index => $peserta)
                                            @php
                                                $status = $peserta->status_mata_kuliah;
                                                $isApproved = $status === 'APPROVED';
                                                $isRejected = $status === 'REJECTED';
                                                $isPending = $status === 'SELECTED';

                                                $rowClass = '';
                                                if ($isApproved) {
                                                    $rowClass = 'bg-green-50';
                                                } elseif ($isRejected) {
                                                    $rowClass = 'bg-red-50';
                                                } elseif ($isPending) {
                                                    $rowClass = 'bg-yellow-50';
                                                }
                                            @endphp

                                            <tr
                                                class="{{ $rowClass }} hover:bg-opacity-75 transition-colors duration-200">
                                                <td class="px-6 py-4 text-xs text-gray-500">{{ $index + 1 }}</td>

                                                <td class="px-6 py-4">
                                                    <div class="text-xs font-medium text-gray-900">
                                                        {{ $peserta->mataKuliah->kode_mata_kuliah }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $peserta->mataKuliah->nama_mata_kuliah }}</div>
                                                    <div class="text-xs text-gray-400 mt-1">
                                                        {{ $peserta->kategori_mata_kuliah }} •
                                                        {{ $peserta->mataKuliah->jenis_mata_kuliah }}</div>
                                                </td>

                                                <td class="px-6 py-4">
                                                    <div class="text-xs font-medium text-gray-900">{{ $peserta->kelas }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $peserta->ruangan ?: 'Ruangan belum diatur' }}</div>
                                                    @if ($peserta->kelasKuliah)
                                                        <div class="text-xs text-gray-400 mt-1">
                                                            Kapasitas:
                                                            {{ $peserta->kelasKuliah->jumlah_peserta ?? '0' }}/{{ $peserta->kelasKuliah->kapasitas ?? '0' }}
                                                        </div>
                                                    @endif
                                                </td>

                                                <td class="px-6 py-4">
                                                    <div class="text-xs font-medium text-gray-900">{{ $peserta->dosen }}
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4">
                                                    @if ($peserta->hari && $peserta->jam_mulai && $peserta->jam_akhir)
                                                        <div class="text-xs font-medium text-gray-900">{{ $peserta->hari }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ \Carbon\Carbon::parse($peserta->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($peserta->jam_akhir)->format('H:i') }}
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-400">Jadwal belum diatur</span>
                                                    @endif
                                                </td>

                                                <td class="px-6 py-4 text-center">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $peserta->mataKuliah->sks_mata_kuliah }} SKS
                                                    </span>
                                                </td>

                                                <td class="px-6 py-4 text-center">
                                                    @if ($isApproved)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-check mr-1"></i>
                                                            Disetujui
                                                        </span>
                                                    @elseif ($isRejected)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <i class="fas fa-times mr-1"></i>
                                                            Ditolak
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            Menunggu Review
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="7"
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-900">
                                                Subtotal SKS Reguler:
                                                <span class="font-bold">
                                                    {{ $mataKuliahReguler->sum(function ($p) {return $p->mataKuliah->sks_mata_kuliah;}) }}
                                                    SKS
                                                </span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Mata Kuliah Bimbingan -->
                    @if ($mataKuliahBimbingan->count() > 0)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-graduation-cap text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-semibold text-gray-900">Mata Kuliah Bimbingan</h3>
                                        <p class="text-xs text-gray-500">{{ $mataKuliahBimbingan->count() }} mata kuliah
                                            dengan sistem bimbingan</p>
                                    </div>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Mata Kuliah</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Jenis</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Pembimbing</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                SKS</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($mataKuliahBimbingan as $index => $peserta)
                                            @php
                                                $status = $peserta->status_mata_kuliah;
                                                $isApproved = $status === 'APPROVED';
                                                $isRejected = $status === 'REJECTED';

                                                $rowClass = '';
                                                if ($isApproved) {
                                                    $rowClass = 'bg-green-50';
                                                } elseif ($isRejected) {
                                                    $rowClass = 'bg-red-50';
                                                } else {
                                                    $rowClass = 'bg-blue-50';
                                                }
                                            @endphp

                                            <tr
                                                class="{{ $rowClass }} hover:bg-opacity-75 transition-colors duration-200">
                                                <td class="px-6 py-4 text-xs text-gray-500">{{ $index + 1 }}</td>

                                                <td class="px-6 py-4">
                                                    <div class="text-xs font-medium text-gray-900">
                                                        {{ $peserta->mataKuliah->kode_mata_kuliah }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $peserta->mataKuliah->nama_mata_kuliah }}</div>
                                                    <div class="text-xs text-gray-400 mt-1">
                                                        {{ $peserta->kategori_mata_kuliah }}</div>
                                                </td>

                                                <td class="px-6 py-4">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <i class="fas fa-user-graduate mr-1"></i>
                                                        {{ $peserta->mataKuliah->jenis_mata_kuliah }}
                                                    </span>
                                                </td>

                                                <td class="px-6 py-4">
                                                    @if ($peserta->dosen !== 'Belum diatur')
                                                        <div class="text-xs font-medium text-gray-900">
                                                            {{ $peserta->dosen }}</div>
                                                        @if (isset($peserta->dosen_pembimbing_2) && $peserta->dosen_pembimbing_2)
                                                            <div class="text-xs text-gray-500">Pembimbing 2:
                                                                {{ $peserta->dosen_pembimbing_2 }}</div>
                                                        @endif
                                                    @else
                                                        <div class="flex items-center text-xs text-gray-400">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            Pembimbing belum diatur
                                                        </div>
                                                        <div class="text-xs text-blue-600 mt-1">Akan diatur admin setelah
                                                            KRS disetujui</div>
                                                    @endif
                                                </td>

                                                <td class="px-6 py-4 text-center">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $peserta->mataKuliah->sks_mata_kuliah }} SKS
                                                    </span>
                                                </td>

                                                <td class="px-6 py-4 text-center">
                                                    @if ($isApproved)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-check mr-1"></i>
                                                            Disetujui
                                                        </span>
                                                    @elseif ($isRejected)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <i class="fas fa-times mr-1"></i>
                                                            Ditolak
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            Menunggu Review
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="7"
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-900">
                                                Subtotal SKS Bimbingan:
                                                <span class="font-bold">
                                                    {{ $mataKuliahBimbingan->sum(function ($p) {return $p->mataKuliah->sks_mata_kuliah;}) }}
                                                    SKS
                                                </span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Empty State -->
                    @if ($mataKuliahTerpilih->count() == 0)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="px-6 py-12 text-center">
                                <i class="fas fa-clipboard-list w-12 h-12 text-gray-400 mx-auto mb-4 text-6xl"></i>
                                <p class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mata Kuliah Terpilih</p>
                                <p class="text-xs text-gray-500 mb-4">Pilih mata kuliah terlebih dahulu sebelum melakukan
                                    review</p>
                                <a href="{{ route('krs.pilih-mata-kuliah', $selectedSemester->id_semester) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                    <i class="fas fa-plus mr-2"></i>
                                    Pilih Mata Kuliah
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Jadwal Mingguan untuk Mata Kuliah Reguler -->
                    @if (count($jadwalMingguan) > 0 && $mataKuliahReguler->count() > 0)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-calendar-week text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-semibold text-gray-900">Jadwal Mingguan</h3>
                                        <p class="text-xs text-gray-500">Preview jadwal kuliah reguler dalam seminggu</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                                    @foreach (['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'] as $hari)
                                        <div class="border border-gray-200 rounded-lg">
                                            <div class="px-3 py-2 bg-gray-50 border-b border-gray-200">
                                                <h4 class="text-xs font-medium text-gray-900">{{ $hari }}</h4>
                                            </div>
                                            <div class="p-3 space-y-2 min-h-[120px]">
                                                @if (isset($jadwalMingguan[$hari]) && count($jadwalMingguan[$hari]) > 0)
                                                    @foreach ($jadwalMingguan[$hari] as $jadwal)
                                                        <div class="bg-indigo-50 border border-indigo-200 rounded p-2">
                                                            <div class="text-xs font-medium text-indigo-900">
                                                                {{ $jadwal['kode_mata_kuliah'] }}</div>
                                                            <div class="text-xs text-indigo-700 mt-1">
                                                                {{ $jadwal['kelas'] }} • {{ $jadwal['ruangan'] }}</div>
                                                            <div class="text-xs text-indigo-600 mt-1">
                                                                {{ \Carbon\Carbon::parse($jadwal['jam_mulai'])->format('H:i') }}-{{ \Carbon\Carbon::parse($jadwal['jam_akhir'])->format('H:i') }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-center text-xs text-gray-400 py-4">
                                                        Tidak ada jadwal
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar - Informasi & Aksi -->
                <div class="space-y-6">
                    <!-- Ringkasan KRS -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-900">Ringkasan KRS</h3>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Mata Kuliah Reguler</span>
                                <span class="text-xs font-medium text-gray-900">{{ $mataKuliahReguler->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Mata Kuliah Bimbingan</span>
                                <span class="text-xs font-medium text-gray-900">{{ $mataKuliahBimbingan->count() }}</span>
                            </div>

                            @if ($sksPerKategori)
                                <div class="border-t border-gray-200 pt-4 space-y-2">
                                    <p class="text-xs font-medium text-gray-900 mb-2">SKS per Kategori:</p>
                                    @foreach ($sksPerKategori as $kategori => $sks)
                                        @if ($sks > 0)
                                            <div class="flex justify-between items-center">
                                                <span class="text-xs text-gray-600">{{ $kategori }}:</span>
                                                <span class="text-xs font-medium text-gray-900">{{ $sks }}
                                                    SKS</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-gray-900">Total SKS</span>
                                    <span
                                        class="text-lg font-semibold {{ $totalSks > $batasSks ? 'text-red-600' : 'text-gray-900' }}">{{ $totalSks }}</span>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-xs text-gray-500">Batas Maksimal</span>
                                    <span class="text-xs text-gray-500">{{ $batasSks }} SKS</span>
                                </div>
                                <div class="mt-2">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span>Progress</span>
                                        <span>{{ round(($totalSks / $batasSks) * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        @php
                                            $progressPercentage = min(($totalSks / $batasSks) * 100, 100);
                                            $progressColor =
                                                $totalSks > $batasSks
                                                    ? 'bg-red-500'
                                                    : ($totalSks >= $batasSks * 0.8
                                                        ? 'bg-green-500'
                                                        : 'bg-blue-500');
                                        @endphp
                                        <div class="{{ $progressColor }} h-2 rounded-full transition-all duration-300"
                                            style="width: {{ $progressPercentage }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Info tambahan berdasarkan status -->
                            @if ($totalSks > $batasSks)
                                <div class="mt-3 p-2 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="text-xs text-red-800">
                                        <p class="font-medium">Peringatan!</p>
                                        <p class="mt-1">Total SKS melebihi batas maksimal {{ $batasSks }} SKS.
                                            Kurangi {{ $totalSks - $batasSks }} SKS sebelum submit.</p>
                                    </div>
                                </div>
                            @elseif ($totalSks >= $batasSks * 0.9)
                                <div class="mt-3 p-2 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="text-xs text-green-800">
                                        <p class="font-medium">Optimal!</p>
                                        <p class="mt-1">Pengambilan SKS sudah mendekati maksimal. Beban kuliah akan cukup
                                            tinggi.</p>
                                    </div>
                                </div>
                            @elseif ($totalSks >= $batasSks * 0.7)
                                <div class="mt-3 p-2 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="text-xs text-blue-800">
                                        <p class="font-medium">Baik!</p>
                                        <p class="mt-1">Beban SKS dalam batas normal. Masih bisa menambah
                                            {{ $batasSks - $totalSks }} SKS jika diperlukan.</p>
                                    </div>
                                </div>
                            @elseif ($totalSks > 0)
                                <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="text-xs text-yellow-800">
                                        <p class="font-medium">Perhatian</p>
                                        <p class="mt-1">Total SKS masih rendah
                                            ({{ $totalSks }}/{{ $batasSks }}). Pertimbangkan menambah mata kuliah.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pembimbing Akademik Info -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-900">Pembimbing Akademik</h3>
                        </div>
                        <div class="px-6 py-4">
                            @if ($registrasiKrs->pembimbingAkademik)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-xs font-medium text-gray-900">
                                            {{ $registrasiKrs->pembimbingAkademik->dosen->pengguna->nama }}</p>
                                        <p class="text-xs text-gray-500">NIDN:
                                            {{ $registrasiKrs->pembimbingAkademik->dosen->nidn }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 p-3 bg-blue-50 border-blue-200 border rounded-lg">
                                    <p class="text-xs text-blue-800">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        KRS akan dikirim ke dosen pembimbing untuk review dan persetujuan
                                    </p>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div
                                        class="h-12 w-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                                    </div>
                                    <p class="text-xs font-medium text-gray-900 mb-1">PA Belum Ditentukan</p>
                                    <p class="text-xs text-gray-500">Silakan hubungi bagian akademik</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Status -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-900">Status Submit</h3>
                        </div>
                        <div class="px-6 py-4">
                            @if (!$registrasiKrs->pembimbingAkademik)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                    <div class="flex">
                                        <i class="fas fa-times-circle text-red-400 mt-0.5 mr-2"></i>
                                        <div class="text-xs text-red-800">
                                            <p class="font-medium">Tidak Dapat Submit!</p>
                                            <p class="mt-1">Anda belum mendapatkan Pembimbing Akademik.</p>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($totalSks == 0)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                    <div class="flex">
                                        <i class="fas fa-exclamation-triangle text-yellow-400 mt-0.5 mr-2"></i>
                                        <div class="text-xs text-yellow-800">
                                            <p class="font-medium">Belum Ada Mata Kuliah!</p>
                                            <p class="mt-1">Pilih mata kuliah terlebih dahulu.</p>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($totalSks > $batasSks)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                    <div class="flex">
                                        <i class="fas fa-times-circle text-red-400 mt-0.5 mr-2"></i>
                                        <div class="text-xs text-red-800">
                                            <p class="font-medium">Tidak Dapat Submit!</p>
                                            <p class="mt-1">Total SKS ({{ $totalSks }}) melebihi batas maksimal
                                                ({{ $batasSks }} SKS).</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                    <div class="flex">
                                        <i class="fas fa-check-circle text-green-400 mt-0.5 mr-2"></i>
                                        <div class="text-xs text-green-800">
                                            <p class="font-medium">Siap untuk Submit!</p>
                                            <p class="mt-1">KRS Anda sudah lengkap dan memenuhi persyaratan.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <i class="fas fa-info-circle text-yellow-400 mt-0.5 mr-3"></i>
                            <div class="text-xs text-yellow-800">
                                <p class="font-medium">Penting!</p>
                                <ul class="mt-2 space-y-1 list-disc list-inside text-xs">
                                    <li>Setelah KRS disubmit, Anda tidak dapat mengubah pilihan mata kuliah</li>
                                    <li>Mata kuliah bimbingan akan diatur pembimbingnya oleh admin setelah approval</li>
                                    <li>Pastikan semua pilihan sudah benar sebelum submit</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    @if ($canSubmit)
        <div id="confirmModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                                <i class="fas fa-paper-plane text-blue-600"></i>
                            </div>
                            <div class="mt-3 ml-4 sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Konfirmasi Submit KRS</h3>
                                <div class="mt-2">
                                    <p class="text-xs text-gray-500">
                                        Anda akan mensubmit KRS dengan:
                                    </p>
                                    <ul class="text-xs text-gray-500 mt-2 space-y-1">
                                        <li>• {{ $mataKuliahTerpilih->count() }} mata kuliah ({{ $totalReguler }} reguler,
                                            {{ $totalBimbingan }} bimbingan)</li>
                                        <li>• Total {{ $totalSks }} SKS</li>
                                        <li>• Ke PA:
                                            <strong>{{ $registrasiKrs->pembimbingAkademik->dosen->pengguna->nama }}</strong>
                                        </li>
                                    </ul>
                                    <p class="text-xs text-gray-500 mt-2">
                                        Setelah disubmit, Anda tidak dapat mengubah pilihan hingga mendapat persetujuan atau
                                        penolakan dari PA.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <form method="POST" action="{{ route('krs.submit') }}" class="sm:ml-3">
                            @csrf
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-gray-900 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-xs">
                                Ya, Submit KRS
                            </button>
                        </form>
                        <button type="button" onclick="closeConfirmModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-xs">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            function confirmSubmitKrs() {
                document.getElementById('confirmModal').classList.remove('hidden');
            }

            function closeConfirmModal() {
                document.getElementById('confirmModal').classList.add('hidden');
            }

            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeConfirmModal();
                }
            });
        </script>
    @endpush
@endsection
