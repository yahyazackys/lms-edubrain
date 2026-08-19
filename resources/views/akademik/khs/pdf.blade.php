{{-- resources/views/akademik/khs/pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>KHS - {{ $khsData['mahasiswa']['nim'] }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 15mm;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }

        .letterhead {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 12px;
        }

        .letterhead .logo-section {
            display: table-cell;
            width: 90px;
            vertical-align: middle;
            padding-right: 15px;
        }

        .letterhead .logo-section img {
            width: 80px;
            height: auto;
            display: block;
        }

        .letterhead .text-section {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .letterhead h1 {
            font-size: 16px;
            margin: 0 0 4px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .letterhead h2 {
            font-size: 14px;
            margin: 0 0 8px;
            font-weight: bold;
            color: #333;
        }

        .letterhead .address {
            font-size: 10px;
            margin: 4px 0;
            line-height: 1.4;
        }

        .letterhead .contact {
            font-size: 9px;
            margin: 4px 0 0;
            color: #666;
        }

        .document-title {
            text-align: center;
            margin: 15px 0;
            padding: 8px;
            background: #f8f8f8;
            border: 2px solid #000;
        }

        .document-title h3 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .document-title .semester {
            font-size: 11px;
            margin-top: 3px;
            font-weight: normal;
        }

        .student-info {
            margin-bottom: 15px;
        }

        .student-info table {
            width: 100%;
            font-size: 10px;
        }

        .student-info td {
            padding: 3px 0;
            vertical-align: top;
        }

        .student-info .label {
            width: 150px;
            font-weight: bold;
        }

        .student-info .colon {
            width: 15px;
            text-align: center;
        }

        .course-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }

        .course-table th,
        .course-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            font-size: 9px;
        }

        .course-table th {
            background: #e0e0e0;
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

        .total-row {
            background: #f0f0f0;
            font-weight: bold;
            border-top: 2px solid #000;
        }

        .summary-rows {
            background: #f8f8f8;
            font-weight: bold;
        }

        .legend {
            margin: 12px 0;
            font-size: 8px;
        }

        .legend table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .legend td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
        }

        .legend-header {
            background: #e0e0e0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .signatures {
            margin-top: 20px;
            text-align: right;
        }

        .signature-right {
            display: inline-block;
            width: 220px;
            text-align: center;
            vertical-align: top;
        }

        .signature-box {
            height: 50px;
            margin: 12px 0;
        }

        .signature-label {
            font-size: 10px;
            margin-top: 4px;
        }

        .signature-name {
            font-size: 10px;
            margin-top: 4px;
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 180px;
            padding: 0 5px 2px;
        }

        .signature-nip {
            font-size: 9px;
            margin-top: 4px;
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 150px;
            padding: 0 5px 2px;
        }

        .footer {
            margin-top: 15px;
            font-size: 7px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 8px;
            color: #666;
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
            <h2>{{ $khsData['mahasiswa']['program_studi'] }}</h2>
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
        <h3>Kartu Hasil Studi (KHS)</h3>
        <div class="semester">{{ $khsData['semester']['nama'] }}</div>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <table>
            <tr>
                <td class="label">Nama Mahasiswa</td>
                <td class="colon">:</td>
                <td><strong>{{ strtoupper($khsData['mahasiswa']['nama']) }}</strong></td>
            </tr>
            <tr>
                <td class="label">Nomor Induk Mahasiswa</td>
                <td class="colon">:</td>
                <td><strong>{{ $khsData['mahasiswa']['nim'] }}</strong></td>
            </tr>
            <tr>
                <td class="label">Program Studi</td>
                <td class="colon">:</td>
                <td>{{ $khsData['mahasiswa']['program_studi'] }}</td>
            </tr>
            <tr>
                <td class="label">Angkatan</td>
                <td class="colon">:</td>
                <td>{{ $khsData['mahasiswa']['angkatan'] }}</td>
            </tr>
        </table>
    </div>

    <!-- Course Table -->
    @if (count($khsData['mata_kuliah']) > 0)
        <table class="course-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Kode MK</th>
                    <th style="width: 35%;">Nama Mata Kuliah</th>
                    <th style="width: 6%;">SKS</th>
                    <th style="width: 12%;">Jenis</th>
                    <th style="width: 10%;">Nilai<br>Angka</th>
                    <th style="width: 8%;">Nilai<br>Huruf</th>
                    <th style="width: 8%;">Nilai<br>Indeks</th>
                    <th style="width: 12%;">Mutu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($khsData['mata_kuliah'] as $index => $mk)
                    <tr>
                        <td>{{ $index + 1 }}</td>
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
                        <td>{{ number_format($mk['nilai_angka'], 2) }}</td>
                        <td class="grade-{{ $mk['nilai_huruf'] }}"><strong>{{ $mk['nilai_huruf'] }}</strong></td>
                        <td class="grade-{{ $mk['nilai_indeks'] }}"><strong>{{ $mk['nilai_indeks'] }}</strong></td>
                        <td>{{ number_format($mk['mutu'], 2) }}</td>
                    </tr>
                @endforeach

                <!-- Total SKS Row -->
                <tr class="total-row">
                    <td colspan="3" class="right"><strong>JUMLAH SKS</strong></td>
                    <td><strong>{{ $khsData['ringkasan']['total_sks_semester'] }}</strong></td>
                    <td colspan="5"></td>
                </tr>

                <!-- IP Semester Row -->
                <tr class="summary-rows">
                    <td colspan="3" class="right"><strong>INDEKS PRESTASI SEMESTER (IPS)</strong></td>
                    <td colspan="6"><strong>{{ number_format($khsData['ringkasan']['ip_semester'], 2) }}</strong>
                    </td>
                </tr>

                <!-- IPK Row -->
                <tr class="summary-rows">
                    <td colspan="3" class="right"><strong>INDEKS PRESTASI KUMULATIF (IPK)</strong></td>
                    <td colspan="6"><strong>{{ number_format($khsData['ringkasan']['ipk_kumulatif'], 2) }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 30px; border: 2px solid #000; background: #f9f9f9;">
            <strong>Tidak ada mata kuliah pada semester ini</strong>
        </div>
    @endif

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-right">
            <div style="margin-bottom: 8px; font-size: 10px;">
                <strong>Muara Enim, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}</strong><br>
                <strong>Mengetahui,</strong><br>
                <strong>Ketua Program Studi</strong>
            </div>
            <div class="signature-box"></div>
            <div class="signature-label">
                <div class="signature-name">&nbsp;</div>
                <div style="font-size: 9px; margin-top: 2px;">NIP. <span class="signature-nip">&nbsp;</span></div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh Sistem Informasi Akademik<br>
        Tanggal Cetak: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm:ss') }}
    </div>
</body>

</html>
