@extends('layouts.app')

@section('title', 'Kartu Hasil Studi (KHS)')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col space-y-1">
                            <h1 class="text-xl font-semibold font-heading text-gray-900">Kartu Hasil Studi (KHS)</h1>
                            <p class="text-sm text-gray-600">Pilih semester untuk melihat hasil studi</p>
                        </div>

                        @if (request('mahasiswa_id'))
                            <div class="mt-4 sm:mt-0">
                                <a href="{{ route('admin.akademik.cari-mahasiswa') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-900 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                    <i class="fas fa-arrow-left w-3 h-3 mr-2"></i>
                                    Kembali
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Info Mahasiswa -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <table class="table-auto text-sm">
                                <tr>
                                    <td class="font-medium text-gray-700 pr-4">NIM</td>
                                    <td class="text-gray-600">:</td>
                                    <td class="text-gray-900 pl-2">{{ $mahasiswa->nim }}</td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-gray-700 pr-4">Nama</td>
                                    <td class="text-gray-600">:</td>
                                    <td class="text-gray-900 pl-2">{{ $mahasiswa->pengguna->nama }}</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table class="table-auto text-sm">
                                <tr>
                                    <td class="font-medium text-gray-700 pr-4">Program Studi</td>
                                    <td class="text-gray-600">:</td>
                                    <td class="text-gray-900 pl-2">{{ $mahasiswa->programStudi->nama_program_studi }}</td>
                                </tr>
                                <tr>
                                    <td class="font-medium text-gray-700 pr-4">Angkatan</td>
                                    <td class="text-gray-600">:</td>
                                    <td class="text-gray-900 pl-2">{{ $mahasiswa->angkatan }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Pilih Semester -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4">
                    @if ($semesterList->count() > 0)
                        <form method="GET" action="{{ route('akademik.khs.show') }}">
                            @if (request('mahasiswa_id'))
                                <input type="hidden" name="mahasiswa_id" value="{{ request('mahasiswa_id') }}">
                            @endif

                            <div class="max-w-md">
                                <label class="block text-xs font-medium text-gray-700 mb-2">Pilih Semester:</label>
                                <div class="flex space-x-3">
                                    <select name="semester_id" required
                                        class="flex-1 text-xs px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                        <option value="">-- Pilih Semester --</option>
                                        @foreach ($semesterList as $semester)
                                            <option value="{{ $semester->id_semester }}">
                                                {{ $semester->nama_semester }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded-lg hover:bg-gray-800 transition-colors duration-200">
                                        <i class="fas fa-search w-3 h-3 mr-2"></i>
                                        Tampilkan
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 text-gray-400">
                                <i class="fas fa-file-alt text-6xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data KHS</h3>
                            <p class="text-sm text-gray-600">Belum ada semester yang dapat ditampilkan untuk mahasiswa ini.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
