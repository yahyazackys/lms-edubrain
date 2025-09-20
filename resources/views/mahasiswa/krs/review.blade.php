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
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Review Kartu Rencana Studi</h1>
                            <p class="text-sm text-gray-600">Periksa kembali pilihan mata kuliah Anda sebelum disubmit ke
                                Pembimbing Akademik</p>
                        </div>

                        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                            <a href="{{ route('krs.pilih-mata-kuliah', $selectedSemester->id_semester) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Edit Pilihan
                            </a>

                            {{-- Hanya tampilkan tombol submit jika ada mata kuliah dan ada PA --}}
                            @if ($registrasiKrs->pembimbingAkademik && $totalSks > 0 && $totalSks <= $batasSks)
                                <button onclick="confirmSubmitKrs()"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Submit KRS ke PA
                                </button>
                            @else
                                <button disabled
                                    class=" inline-flex justify-center items-center px-4 py-3 bg-gray-300 text-gray-500 text-xs font-medium rounded-lg cursor-not-allowed">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
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
                            <p class="text-base font-semibold text-gray-900">{{ $mataKuliahTerpilih->count() }}</p>
                            <p class="text-xs text-gray-500">Mata Kuliah</p>
                        </div>
                        <div>
                            {{-- Update: Tampilkan format "X/Y SKS" --}}
                            <p class="text-base font-semibold text-gray-900">
                                {{ $totalSks }}/{{ $batasSks ?? 24 }}
                            </p>
                            <p class="text-xs text-gray-500">Total SKS</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation Alerts -->
            {{-- Alert jika belum ada PA --}}
            @if (!$registrasiKrs->pembimbingAkademik)
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <svg class="w-4 h-4 text-red-400 mt-0.5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium">Pembimbing Akademik Belum Ditentukan</h4>
                            <p class="text-sm mt-1">Anda belum mendapatkan Pembimbing Akademik untuk semester ini.
                                Silakan hubungi bagian akademik untuk mendapatkan PA sebelum dapat melakukan submit KRS.</p>
                        </div>
                    </div>
                </div>
            @endif

            @php
                $batasSks = $batasSks ?? 21; // fallback jika tidak ada
            @endphp

            @if ($totalSks > $batasSks)
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <svg class="w-4 h-4 text-red-400 mt-0.5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium">Beban SKS Melebihi Batas Maksimal</h4>
                            <p class="text-sm mt-1">Total SKS ({{ $totalSks }}) melebihi batas maksimal yang diizinkan
                                ({{ $batasSks }} SKS). Harap kurangi beberapa mata kuliah sebelum
                                submit.</p>
                        </div>
                    </div>
                </div>
            @elseif($totalSks < 12)
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <svg class="w-4 h-4 text-yellow-400 mt-0.5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                        <div>
                            <h4 class="font-medium">Beban SKS Rendah</h4>
                            <p class="text-sm mt-1">Total SKS ({{ $totalSks }}) tergolong rendah. Anda masih bisa
                                menambah mata kuliah jika diperlukan (batas: {{ $batasSks }} SKS).</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($totalSks == 0)
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <svg class="w-4 h-4 text-red-400 mt-0.5 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium">Error: Tidak Ada Mata Kuliah Dipilih</h4>
                            <p class="text-sm mt-1">Anda belum memilih mata kuliah apapun. Silakan kembali ke halaman
                                pemilihan mata kuliah.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content - Daftar Mata Kuliah -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Daftar Mata Kuliah Terpilih -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">Mata Kuliah Terpilih</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ $mataKuliahTerpilih->count() }} mata kuliah, total
                                {{ $totalSks }} SKS</p>
                        </div>

                        <div class="overflow-x-auto">
                            @if ($mataKuliahTerpilih->count() > 0)
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
                                        @foreach ($mataKuliahTerpilih as $index => $peserta)
                                            @php
                                                $status = $peserta->status_mata_kuliah;
                                                $isApproved = $status === 'APPROVED';
                                                $isRejected = $status === 'REJECTED';
                                                $isPending = $status === 'SELECTED';

                                                // Row styling berdasarkan status
                                                $rowClass = '';
                                                if ($isApproved) {
                                                    $rowClass = 'bg-green-50 hover:bg-green-100';
                                                } elseif ($isRejected) {
                                                    $rowClass = 'bg-red-50 hover:bg-red-100';
                                                } elseif ($isPending) {
                                                    $rowClass = 'bg-yellow-50 hover:bg-yellow-100';
                                                } else {
                                                    $rowClass = 'hover:bg-gray-50';
                                                }
                                            @endphp

                                            <tr class="{{ $rowClass }} transition-colors duration-200">
                                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-xs font-medium text-gray-900">
                                                        {{ $peserta->mataKuliah->kode_mata_kuliah }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $peserta->mataKuliah->nama_mata_kuliah }}
                                                    </div>
                                                    <div class="flex items-center space-x-2 mt-2">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $peserta->kelasKuliah->kurikulumMataKuliah->jenis_mata_kuliah === 'WAJIB' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                            {{ $peserta->kelasKuliah->kurikulumMataKuliah->jenis_mata_kuliah }}
                                                        </span>
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            Semester
                                                            {{ $peserta->kelasKuliah->kurikulumMataKuliah->semester }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-xs font-medium text-gray-900">
                                                        {{ $peserta->kelasKuliah->nama_kelas_kuliah }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $peserta->kelasKuliah->nama_ruangan }}
                                                    </div>
                                                    <div class="text-xs text-gray-400 mt-1">
                                                        Kapasitas:
                                                        {{ $peserta->kelasKuliah->jumlah_peserta ?? '0' }}/{{ $peserta->kelasKuliah->kapasitas }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-xs font-medium text-gray-900">
                                                        {{ $peserta->kelasKuliah->dosen->pengguna->nama ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $peserta->kelasKuliah->dosen->nidn ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if ($peserta->kelasKuliah->hari && $peserta->kelasKuliah->jam_mulai && $peserta->kelasKuliah->jam_akhir)
                                                        <div class="text-xs font-medium text-gray-900">
                                                            {{ $peserta->kelasKuliah->hari }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ \Carbon\Carbon::parse($peserta->kelasKuliah->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($peserta->kelasKuliah->jam_akhir)->format('H:i') }}
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-400">Belum diatur</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $peserta->mataKuliah->sks_mata_kuliah }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    @if ($isApproved)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                                            title="Mata kuliah ini telah disetujui oleh Pembimbing Akademik">
                                                            <svg class="w-3 h-3 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            Disetujui
                                                        </span>
                                                    @elseif ($isRejected)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
                                                            title="Mata kuliah ini ditolak oleh Pembimbing Akademik. Silakan ganti dengan mata kuliah lain.">
                                                            <svg class="w-3 h-3 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            Ditolak
                                                        </span>
                                                    @elseif ($isPending)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                                                            title="Mata kuliah ini sedang menunggu review dari Pembimbing Akademik">
                                                            <svg class="w-3 h-3 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 8v4l3 3"></path>
                                                            </svg>
                                                            Menunggu Review
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600"
                                                            title="Status mata kuliah tidak diketahui">
                                                            <svg class="w-3 h-3 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093v0M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                </path>
                                                            </svg>
                                                            Tidak Diketahui
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="6"
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-900">
                                                Total SKS:
                                            </td>
                                            <td class="px-6 py-3 text-center">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-600 text-white">
                                                    {{ $totalSks }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            @else
                                <div class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                        </path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mata Kuliah Terpilih</p>
                                    <p class="text-xs text-gray-500 mb-4">Pilih mata kuliah terlebih dahulu sebelum
                                        melakukan review</p>
                                    <a href="{{ route('krs.pilih-mata-kuliah') }}"
                                        class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                        Pilih Mata Kuliah
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Jadwal Mingguan -->
                    @if (count($jadwalMingguan) > 0 && $totalSks > 0)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-900">Jadwal Mingguan</h3>
                                <p class="text-xs text-gray-500 mt-1">Preview jadwal kuliah Anda dalam seminggu</p>
                            </div>

                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                                    @foreach (['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'] as $hari)
                                        <div class="border border-gray-200 rounded-lg">
                                            <div class="px-3 py-2 bg-gray-50 border-b border-gray-200">
                                                <h4 class="text-xs font-medium text-gray-900">{{ $hari }}</h4>
                                            </div>
                                            <div class="p-3 space-y-2 min-h-[120px]">
                                                @if (isset($jadwalMingguan[$hari]) && count($jadwalMingguan[$hari]) > 0)
                                                    @foreach ($jadwalMingguan[$hari] as $jadwal)
                                                        <div class="bg-blue-50 border border-blue-200 rounded p-2">
                                                            <div class="text-xs font-medium text-blue-900">
                                                                {{ $jadwal['kode_mata_kuliah'] }}</div>
                                                            <div class="text-xs text-blue-700 mt-1">{{ $jadwal['kelas'] }}
                                                                • {{ $jadwal['ruangan'] }}</div>
                                                            <div class="text-xs text-blue-600 mt-1">
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
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">Ringkasan KRS</h3>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            @php
                                $batasSksValue = $batasSks ?? 24;
                                $sisaSks = $batasSksValue - $totalSks;
                            @endphp
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Mata Kuliah Wajib</span>
                                <span class="text-xs font-medium text-gray-900">
                                    {{ $mataKuliahTerpilih->filter(function ($p) {
                                            return $p->kelasKuliah->kurikulumMataKuliah->jenis_mata_kuliah === 'WAJIB';
                                        })->count() }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">Mata Kuliah Pilihan</span>
                                <span class="text-xs font-medium text-gray-900">
                                    {{ $mataKuliahTerpilih->filter(function ($p) {
                                            return $p->kelasKuliah->kurikulumMataKuliah->jenis_mata_kuliah === 'PILIHAN';
                                        })->count() }}
                                </span>
                            </div>

                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-gray-900">Total SKS</span>
                                    <span class="text-lg font-semibold text-gray-900">{{ $totalSks }}</span>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-xs text-gray-500">Batas Maksimal</span>
                                    <span class="text-xs text-gray-500">{{ $batasSksValue }} SKS</span>
                                </div>
                                <div class="mt-2">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span>Progress</span>
                                        <span>{{ round(($totalSks / $batasSksValue) * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        @php
                                            $progressPercentage = min(($totalSks / $batasSksValue) * 100, 100);
                                            $progressColor =
                                                $totalSks > $batasSksValue
                                                    ? 'bg-red-500'
                                                    : ($totalSks >= $batasSksValue * 0.8
                                                        ? 'bg-green-500'
                                                        : 'bg-blue-500');
                                        @endphp
                                        <div class="{{ $progressColor }} h-2 rounded-full transition-all duration-300"
                                            style="width: {{ $progressPercentage }}%"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Info tambahan berdasarkan status --}}
                            @if ($totalSks > $batasSksValue)
                                <div class="mt-3 p-2 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="text-xs text-red-800">
                                        <p class="font-medium">Peringatan!</p>
                                        <p class="mt-1">Total SKS melebihi batas maksimal {{ $batasSksValue }} SKS.
                                            Kurangi {{ $totalSks - $batasSksValue }} SKS sebelum submit.</p>
                                    </div>
                                </div>
                            @elseif ($totalSks >= $batasSksValue * 0.9)
                                <div class="mt-3 p-2 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="text-xs text-green-800">
                                        <p class="font-medium">Optimal!</p>
                                        <p class="mt-1">Pengambilan SKS sudah mendekati maksimal. Beban kuliah akan cukup
                                            tinggi.</p>
                                    </div>
                                </div>
                            @elseif ($totalSks >= $batasSksValue * 0.7)
                                <div class="mt-3 p-2 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="text-xs text-blue-800">
                                        <p class="font-medium">Baik!</p>
                                        <p class="mt-1">Beban SKS dalam batas normal. Masih bisa menambah
                                            {{ $batasSksValue - $totalSks }} SKS jika diperlukan.</p>
                                    </div>
                                </div>
                            @elseif ($totalSks > 0)
                                <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="text-xs text-yellow-800">
                                        <p class="font-medium">Perhatian</p>
                                        <p class="mt-1">Total SKS masih rendah
                                            ({{ $totalSks }}/{{ $batasSksValue }}). Pertimbangkan menambah mata
                                            kuliah.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pembimbing Akademik Info -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">Pembimbing Akademik</h3>
                        </div>
                        <div class="px-6 py-4">
                            @if ($registrasiKrs->pembimbingAkademik)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
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
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        KRS akan dikirim ke dosen pembimbing untuk review dan persetujuan
                                    </p>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div
                                        class="h-12 w-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 mb-1">PA Belum Ditentukan</p>
                                    <p class="text-xs text-gray-500">Silakan hubungi bagian akademik</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if (!$registrasiKrs->pembimbingAkademik)
                        {{-- Pesan jika belum ada PA --}}
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex">
                                <svg class="w-4 h-4 text-red-400 mt-0.5 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-xs text-red-800">
                                    <p class="font-medium">Tidak Dapat Submit!</p>
                                    <p class="mt-1">Anda belum mendapatkan Pembimbing Akademik. Hubungi bagian
                                        akademik untuk mendapatkan PA.</p>
                                </div>
                            </div>
                        </div>
                    @elseif ($totalSks == 0)
                        {{-- Pesan jika belum ada mata kuliah --}}
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <div class="flex">
                                <svg class="w-4 h-4 text-yellow-400 mt-0.5 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                                <div class="text-xs text-yellow-800">
                                    <p class="font-medium">Belum Ada Mata Kuliah!</p>
                                    <p class="mt-1">Pilih mata kuliah terlebih dahulu sebelum melakukan submit
                                        KRS.</p>
                                </div>
                            </div>
                        </div>
                    @elseif ($totalSks > $batasSks)
                        {{-- Pesan jika melebihi batas SKS --}}
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex">
                                <svg class="w-4 h-4 text-red-400 mt-0.5 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-xs text-red-800">
                                    <p class="font-medium">Tidak Dapat Submit!</p>
                                    <p class="mt-1">Total SKS ({{ $totalSks }}) melebihi batas maksimal
                                        ({{ $batasSks }} SKS).
                                        Kurangi beberapa mata kuliah sebelum submit.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Normal flow jika sudah ada PA dan ada mata kuliah dan tidak melebihi batas --}}
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <div class="flex">
                                <svg class="w-4 h-4 text-yellow-400 mt-0.5 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                                <div class="text-xs text-yellow-800">
                                    <p class="font-medium">Penting!</p>
                                    <p class="mt-1">Setelah KRS disubmit, Anda tidak dapat mengubah pilihan mata
                                        kuliah. Pastikan semua pilihan sudah benar.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Floating Submit Button dengan Tooltip --}}
                    <div class="fixed bottom-6 right-6">
                        @if ($registrasiKrs->pembimbingAkademik && $totalSks > 0 && $totalSks <= $batasSks)
                            {{-- Tombol aktif --}}
                            <button onclick="confirmSubmitKrs()"
                                class="inline-flex items-center px-6 py-3 bg-gray-900 text-white text-sm font-medium rounded-full shadow-lg hover:bg-gray-700 hover:shadow-xl transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Submit ke Pembimbing Akademik
                            </button>
                        @else
                            {{-- Tombol disabled dengan tooltip --}}
                            <div class="relative group">
                                <button disabled
                                    class="inline-flex items-center px-6 py-3 bg-gray-300 text-gray-500 text-sm font-medium rounded-full shadow-lg cursor-not-allowed">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Submit ke Pembimbing Akademik
                                </button>

                                {{-- Tooltip --}}
                                <div
                                    class="absolute bottom-full right-0 mb-2 px-3 py-2 bg-gray-800 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap">
                                    @if (!$registrasiKrs->pembimbingAkademik)
                                        Pembimbing Akademik belum diatur
                                    @elseif ($totalSks <= 0)
                                        Belum ada mata kuliah dipilih
                                    @elseif ($totalSks > $batasSks)
                                        SKS melebihi batas maksimal ({{ $batasSks }})
                                    @endif

                                    {{-- Tooltip arrow --}}
                                    <div class="absolute top-full right-4 w-2 h-2 bg-gray-800 transform rotate-45">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    @if ($registrasiKrs->pembimbingAkademik && $totalSks > 0)
        <div id="confirmModal" class="fixed inset-0 z-[9999] overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <div
                    class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-gray-100">
                                <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </div>
                            <div class="mt-3 ml-4 sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Konfirmasi Submit KRS
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Anda akan mensubmit KRS dengan {{ $mataKuliahTerpilih->count() }} mata kuliah
                                        ({{ $totalSks }} SKS) ke Pembimbing Akademik:
                                        <strong>{{ $registrasiKrs->pembimbingAkademik->dosen->pengguna->nama }}</strong>.
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
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-gray-900 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm">
                                Ya, Submit KRS
                            </button>
                        </form>
                        <button type="button" onclick="closeConfirmModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm">
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
