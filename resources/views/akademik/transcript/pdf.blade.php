{{-- resources/views/akademik/transcript/pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Transkrip Nilai - {{ $transcriptData['mahasiswa']['nim'] }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }

        .letterhead {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }

        .letterhead .logo-section {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
            padding-right: 12px;
        }

        .letterhead .logo-section img {
            width: 70px;
            height: auto;
            display: block;
        }

        .letterhead .text-section {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .letterhead h1 {
            font-size: 15px;
            margin: 0 0 3px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .letterhead h2 {
            font-size: 13px;
            margin: 0 0 6px;
            font-weight: bold;
            color: #333;
        }

        .letterhead .address {
            font-size: 9px;
            margin: 3px 0;
            line-height: 1.4;
        }

        .letterhead .contact {
            font-size: 8px;
            margin: 3px 0 0;
            color: #666;
        }

        .document-title {
            text-align: center;
            margin: 12px 0;
            padding: 8px;
            background: #000;
            color: #fff;
        }

        .document-title h3 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .student-info {
            margin-bottom: 12px;
            border: 1px solid #000;
            padding: 8px;
            background: #f9f9f9;
        }

        .student-info table {
            width: 100%;
            font-size: 9px;
        }

        .student-info td {
            padding: 2px 0;
            vertical-align: top;
        }

        .student-info .label {
            width: 140px;
            font-weight: bold;
        }

        .student-info .colon {
            width: 15px;
            text-align: center;
        }

        .semester-header {
            background: #e0e0e0;
            border: 1px solid #000;
            padding: 5px 8px;
            margin-top: 10px;
            margin-bottom: 3px;
            font-weight: bold;
            font-size: 10px;
        }

        .course-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .course-table th,
        .course-table td {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            font-size: 8px;
        }

        .course-table th {
            background: #f0f0f0;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .course-table .left {
            text-align: left;
        }

        .course-table .right {
            text-align: right;
        }

        .grade-A {
            background: #d4f0d4;
            font-weight: bold;
        }

        .grade-A- {
            background: #d4f0d4;
            font-weight: bold;
        }

        .grade-B\+ {
            background: #d4e6f0;
            font-weight: bold;
        }

        .grade-B {
            background: #d4e6f0;
            font-weight: bold;
        }

        .grade-B- {
            background: #d4e6f0;
            font-weight: bold;
        }

        .grade-C\+ {
            background: #f0f0d4;
            font-weight: bold;
        }

        .grade-C {
            background: #f0f0d4;
            font-weight: bold;
        }

        .grade-C- {
            background: #f0f0d4;
            font-weight: bold;
        }

        .grade-D {
            background: #f0e0d4;
            font-weight: bold;
        }

        .grade-E {
            background: #f0d4d4;
            font-weight: bold;
        }

        .summary-box {
            margin-top: 15px;
            border: 2px solid #000;
            padding: 10px;
            background: #f8f8f8;
        }

        .summary-box h4 {
            margin: 0 0 8px 0;
            font-size: 11px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 4px 8px;
            font-size: 10px;
            border: 1px solid #000;
        }

        .summary-table .label {
            width: 60%;
            font-weight: bold;
            background: #f0f0f0;
        }

        .summary-table .value {
            width: 40%;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }

        .status-lulus {
            background: #d4f0d4;
            color: #006400;
            font-size: 12px;
        }

        .status-belum {
            background: #f0d4d4;
            color: #8b0000;
            font-size: 12px;
        }

        .legend {
            margin: 10px 0;
            font-size: 7px;
            border: 1px solid #000;
            padding: 5px;
            background: #f9f9f9;
        }

        .legend table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        .legend td {
            border: 1px solid #000;
            padding: 2px 3px;
            text-align: center;
        }

        .legend-header {
            background: #e0e0e0;
            font-weight: bold;
        }

        .signatures {
            margin-top: 15px;
        }

        .signature-container {
            display: table;
            width: 100%;
        }

        .signature-left,
        .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-box {
            height: 50px;
            margin: 10px 0 5px;
        }

        .signature-label {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature-name {
            font-size: 9px;
            margin-top: 2px;
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 160px;
            padding: 0 5px 2px;
        }

        .signature-nip {
            font-size: 8px;
            margin-top: 3px;
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 130px;
            padding: 0 5px 2px;
        }

        .footer {
            margin-top: 10px;
            font-size: 7px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <!-- Kop Surat -->
    <div class="letterhead">
        <div class="logo-section">
            <img src="{{ public_path('logo-primary.png') }}" alt="Logo">
        </div>
        <div class="text-section">
            <h1>Sekolah Tinggi Ilmu Tarbiyah Muara Enim</h1>
            <h2>{{ $transcriptData['mahasiswa']['program_studi'] }}</h2>
            <div class="address">
                Jl. Mayor Ruslan Kelurahan Air Lintang Muara Enim 31313, Sumatera Selatan
            </div>
            <div class="contact">
                Telp: (0734) 422677 | Email: official@muaraenim.ac.id | Website: www.stitmuaraenim.ac.id
            </div>
        </div>
    </div>

    <!-- Document Title -->
    <div class="document-title">
        <h3>Transkrip Nilai Akademik</h3>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <table>
            <tr>
                <td class="label">Nama Mahasiswa</td>
                <td class="colon">:</td>
                <td><strong>{{ strtoupper($transcriptData['mahasiswa']['nama']) }}</strong></td>
            </tr>
            <tr>
                <td class="label">Nomor Induk Mahasiswa</td>
                <td class="colon">:</td>
                <td><strong>{{ $transcriptData['mahasiswa']['nim'] }}</strong></td>
            </tr>
            <tr>
                <td class="label">Program Studi</td>
                <td class="colon">:</td>
                <td>{{ $transcriptData['mahasiswa']['program_studi'] }}</td>
            </tr>
            <tr>
                <td class="label">Angkatan</td>
                <td class="colon">:</td>
                <td>{{ $transcriptData['mahasiswa']['angkatan'] }}</td>
            </tr>
            <tr>
                <td class="label">Kurikulum</td>
                <td class="colon">:</td>
                <td>{{ $transcriptData['mahasiswa']['kurikulum'] }}</td>
            </tr>
        </table>
    </div>

    <!-- Courses by Semester -->
    @php
        $groupedBySemester = collect($transcriptData['mata_kuliah'])->groupBy('semester_diambil');
        $counter = 1;
    @endphp

    @foreach ($groupedBySemester as $semester => $courses)
        <div class="semester-header">
            {{ $semester }}
        </div>

        <table class="course-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Kode MK</th>
                    <th style="width: 38%;">Nama Mata Kuliah</th>
                    <th style="width: 6%;">SKS</th>
                    <th style="width: 12%;">Jenis</th>
                    <th style="width: 9%;">Nilai</th>
                    <th style="width: 9%;">Indeks</th>
                    <th style="width: 9%;">Mutu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courses as $mk)
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td>{{ $mk['kode_mata_kuliah'] }}</td>
                        <td class="left">{{ $mk['nama_mata_kuliah'] }}</td>
                        <td>{{ $mk['sks'] }}</td>
                        <td>
                            @switch($mk['jenis'])
                                @case('TEORI')
                                    Teori
                                @break

                                @case('PRAKTIKUM')
                                    Praktikum
                                @break

                                @case('KKN')
                                    KKN
                                @break

                                @case('MAGANG')
                                    Magang
                                @break

                                @case('SKRIPSI')
                                    Skripsi
                                @break

                                @default
                                    {{ $mk['jenis'] }}
                            @endswitch
                        </td>
                        <td class="grade-{{ $mk['nilai_huruf'] }}"><strong>{{ $mk['nilai_huruf'] }}</strong></td>
                        <td>{{ number_format($mk['nilai_indeks'], 2) }}</td>
                        <td>{{ number_format($mk['mutu'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <!-- Summary Box -->
    <div class="summary-box">
        <h4>Ringkasan Prestasi Akademik</h4>
        <table class="summary-table">
            <tr>
                <td class="label">Total SKS yang Telah Diambil</td>
                <td class="value">{{ $transcriptData['ringkasan']['total_sks_lulus'] }}</td>
            </tr>
            <tr>
                <td class="label">Total SKS Kurikulum yang Disyaratkan</td>
                <td class="value">{{ $transcriptData['ringkasan']['batas_sks_kurikulum'] }}</td>
            </tr>
            <tr>
                <td class="label">Persentase Penyelesaian</td>
                <td class="value">{{ number_format($transcriptData['ringkasan']['persentase_kelulusan'], 2) }}%</td>
            </tr>
            <tr>
                <td class="label">Indeks Prestasi Kumulatif (IPK)</td>
                <td class="value" style="font-size: 14px; color: #0066cc;">
                    {{ number_format($transcriptData['ringkasan']['ipk_keseluruhan'], 2) }}</td>
            </tr>
            <tr>
                <td class="label">Status Kelulusan</td>
                <td
                    class="value {{ $transcriptData['ringkasan']['status_kelulusan'] === 'LULUS' ? 'status-lulus' : 'status-belum' }}">
                    {{ $transcriptData['ringkasan']['status_kelulusan'] }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Grade Legend -->
    <div class="legend">
        <strong>KETERANGAN SISTEM PENILAIAN:</strong>
        <table>
            <tr>
                <td class="legend-header">Nilai</td>
                <td class="legend-header">A</td>
                <td class="legend-header">A-</td>
                <td class="legend-header">B+</td>
                <td class="legend-header">B</td>
                <td class="legend-header">B-</td>
                <td class="legend-header">C+</td>
                <td class="legend-header">C</td>
                <td class="legend-header">C-</td>
                <td class="legend-header">D</td>
                <td class="legend-header">E</td>
            </tr>
            <tr>
                <td class="legend-header">Bobot</td>
                <td class="grade-A">4.00</td>
                <td class="grade-A-">3.70</td>
                <td class="grade-B+">3.30</td>
                <td class="grade-B">3.00</td>
                <td class="grade-B-">2.70</td>
                <td class="grade-C+">2.30</td>
                <td class="grade-C">2.00</td>
                <td class="grade-C-">1.70</td>
                <td class="grade-D">1.00</td>
                <td class="grade-E">0.00</td>
            </tr>
        </table>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-container">
            <div class="signature-left">
                <div class="signature-label">
                    <strong>Muara Enim,
                        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}</strong><br>
                    <strong>Dekan Fakultas</strong>
                </div>
                <div class="signature-box"></div>
                <div class="signature-name">&nbsp;</div>
                <div style="font-size: 8px; margin-top: 2px;">NIP. <span class="signature-nip">&nbsp;</span></div>
            </div>
            <div class="signature-right">
                <div class="signature-label">
                    <strong>Mengetahui,</strong><br>
                    <strong>Ketua Program Studi</strong>
                </div>
                <div class="signature-box"></div>
                <div class="signature-name">&nbsp;</div>
                <div style="font-size: 8px; margin-top: 2px;">NIP. <span class="signature-nip">&nbsp;</span></div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh Sistem Informasi Akademik STIT Muara Enim<br>
        Tanggal Cetak: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm:ss') }}
    </div>
</body>

</html>
