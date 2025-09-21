<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\{
    Pengguna,
    JenjangPendidikan,
    ProgramStudi,
    Semester,
    Kurikulum,
    MataKuliah,
    Mahasiswa,
    Dosen,
    KalenderAkademik,
    Pengumuman
};
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'dosen']);
        Role::create(['name' => 'mahasiswa']);

        // ==========================
        // 1. Admin Default
        // ==========================
        $admin = Pengguna::create([
            'id_pengguna' => Str::uuid(),
            'nama'        => 'Administrator',
            'username'    => '003001',
            'email'       => 'admin@gmail.com',
            'password'    => Hash::make('003001'),
            'role'        => 'admin',
            'is_active'   => true,
        ]);

        $admin->assignRole('admin');

        // ==========================
        // 2. Jenjang Pendidikan
        // ==========================
        $jenjang = JenjangPendidikan::create([
            'id_jenjang_pendidikan'   => Str::uuid(),
            'kode_jenjang_pendidikan' => 'S1',
            'nama_jenjang_pendidikan' => 'Sarjana',
        ]);

        // ==========================
        // 3. Program Studi
        // ==========================
        $prodi = ProgramStudi::create([
            'id_program_studi'   => Str::uuid(),
            'kode_program_studi' => 'PAI001',
            'nama_program_studi' => 'Pendidikan Agama Islam',
            'status'             => 'A',
            'id_jenjang_pendidikan' => $jenjang->id_jenjang_pendidikan,
        ]);

        // ==========================
        // 4. Semester 2024/2025
        // ==========================
        $semesterGanjil = Semester::create([
            'id_semester'   => Str::uuid(),
            'kode_semester' => '20241',
            'nama_semester' => 'Ganjil 2024/2025',
            'tipe'          => 'ganjil',
            'tanggal_mulai' => '2024-08-01',
            'tanggal_selesai' => '2025-01-31',
            'is_active'     => true,
        ]);

        $semesterGenap = Semester::create([
            'id_semester'   => Str::uuid(),
            'kode_semester' => '20242',
            'nama_semester' => 'Genap 2024/2025',
            'tipe'          => 'genap',
            'tanggal_mulai' => '2025-02-01',
            'tanggal_selesai' => '2025-07-31',
            'is_active'     => false,
        ]);

        // ==========================
        // 5. Kurikulum
        // ==========================
        $kurikulum = Kurikulum::create([
            'id_kurikulum'       => Str::uuid(),
            'nama_kurikulum'     => 'Kurikulum 2024',
            'jumlah_sks_lulus'   => 144,
            'jumlah_sks_wajib'   => 130,
            'jumlah_sks_pilihan' => 14,
            'id_program_studi'   => $prodi->id_program_studi,
            'id_semester'        => $semesterGanjil->id_semester,
        ]);

        // ==========================
        // 6. Mata Kuliah Lengkap
        // ==========================
        $mataKuliah = [
            // Semester 1
            ['kode' => 'PAI101', 'nama' => 'Al-Qur\'an Hadits I', 'sks' => 3, 'semester' => 1, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI102', 'nama' => 'Akidah Akhlak I', 'sks' => 3, 'semester' => 1, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI103', 'nama' => 'Fiqh I', 'sks' => 3, 'semester' => 1, 'jenis' => 'WAJIB'],
            ['kode' => 'UNI101', 'nama' => 'Pancasila', 'sks' => 2, 'semester' => 1, 'jenis' => 'WAJIB'],
            ['kode' => 'UNI102', 'nama' => 'Bahasa Indonesia', 'sks' => 2, 'semester' => 1, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI104', 'nama' => 'Pengantar Pendidikan Islam', 'sks' => 3, 'semester' => 1, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI105', 'nama' => 'Bahasa Arab I', 'sks' => 2, 'semester' => 1, 'jenis' => 'WAJIB'],

            // Semester 2
            ['kode' => 'PAI201', 'nama' => 'Al-Qur\'an Hadits II', 'sks' => 3, 'semester' => 2, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI202', 'nama' => 'Akidah Akhlak II', 'sks' => 3, 'semester' => 2, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI203', 'nama' => 'Fiqh II', 'sks' => 3, 'semester' => 2, 'jenis' => 'WAJIB'],
            ['kode' => 'UNI201', 'nama' => 'Kewarganegaraan', 'sks' => 2, 'semester' => 2, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI204', 'nama' => 'Sejarah Peradaban Islam', 'sks' => 3, 'semester' => 2, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI205', 'nama' => 'Bahasa Arab II', 'sks' => 2, 'semester' => 2, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI206', 'nama' => 'Psikologi Pendidikan', 'sks' => 3, 'semester' => 2, 'jenis' => 'WAJIB'],

            // Semester 3
            ['kode' => 'PAI301', 'nama' => 'Ulumul Qur\'an', 'sks' => 3, 'semester' => 3, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI302', 'nama' => 'Ulumul Hadits', 'sks' => 3, 'semester' => 3, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI303', 'nama' => 'Ushul Fiqh I', 'sks' => 3, 'semester' => 3, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI304', 'nama' => 'Filsafat Pendidikan Islam', 'sks' => 3, 'semester' => 3, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI305', 'nama' => 'Metodologi Penelitian', 'sks' => 3, 'semester' => 3, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI306', 'nama' => 'Statistik Pendidikan', 'sks' => 2, 'semester' => 3, 'jenis' => 'WAJIB'],
            ['kode' => 'UNI301', 'nama' => 'Bahasa Inggris', 'sks' => 2, 'semester' => 3, 'jenis' => 'WAJIB'],

            // Semester 4
            ['kode' => 'PAI401', 'nama' => 'Tafsir Al-Qur\'an I', 'sks' => 3, 'semester' => 4, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI402', 'nama' => 'Ilmu Hadits', 'sks' => 3, 'semester' => 4, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI403', 'nama' => 'Ushul Fiqh II', 'sks' => 3, 'semester' => 4, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI404', 'nama' => 'Kurikulum dan Pembelajaran PAI', 'sks' => 3, 'semester' => 4, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI405', 'nama' => 'Media Pembelajaran PAI', 'sks' => 3, 'semester' => 4, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI406', 'nama' => 'Evaluasi Pembelajaran PAI', 'sks' => 3, 'semester' => 4, 'jenis' => 'WAJIB'],

            // Semester 5
            ['kode' => 'PAI501', 'nama' => 'Tafsir Al-Qur\'an II', 'sks' => 3, 'semester' => 5, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI502', 'nama' => 'Ilmu Kalam', 'sks' => 3, 'semester' => 5, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI503', 'nama' => 'Tasawuf', 'sks' => 3, 'semester' => 5, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI504', 'nama' => 'Manajemen Pendidikan Islam', 'sks' => 3, 'semester' => 5, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI505', 'nama' => 'Strategi Pembelajaran PAI', 'sks' => 3, 'semester' => 5, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI506', 'nama' => 'Micro Teaching', 'sks' => 2, 'semester' => 5, 'jenis' => 'WAJIB'],

            // Semester 6
            ['kode' => 'PAI601', 'nama' => 'Sejarah Pemikiran Islam', 'sks' => 3, 'semester' => 6, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI602', 'nama' => 'Islam dan Budaya Lokal', 'sks' => 3, 'semester' => 6, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI603', 'nama' => 'Bimbingan Konseling Islam', 'sks' => 3, 'semester' => 6, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI604', 'nama' => 'Praktik Pengalaman Lapangan (PPL)', 'sks' => 4, 'semester' => 6, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI605', 'nama' => 'Teknologi Pembelajaran PAI', 'sks' => 3, 'semester' => 6, 'jenis' => 'WAJIB'],

            // Semester 7
            ['kode' => 'PAI701', 'nama' => 'Kuliah Kerja Nyata (KKN)', 'sks' => 3, 'semester' => 7, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI702', 'nama' => 'Seminar Proposal Skripsi', 'sks' => 2, 'semester' => 7, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI703', 'nama' => 'Mata Kuliah Pilihan I', 'sks' => 3, 'semester' => 7, 'jenis' => 'PILIHAN'],
            ['kode' => 'PAI704', 'nama' => 'Mata Kuliah Pilihan II', 'sks' => 3, 'semester' => 7, 'jenis' => 'PILIHAN'],
            ['kode' => 'PAI705', 'nama' => 'Pengembangan Kurikulum PAI', 'sks' => 3, 'semester' => 7, 'jenis' => 'WAJIB'],

            // Semester 8
            ['kode' => 'PAI801', 'nama' => 'Skripsi', 'sks' => 6, 'semester' => 8, 'jenis' => 'WAJIB'],
            ['kode' => 'PAI802', 'nama' => 'Mata Kuliah Pilihan III', 'sks' => 3, 'semester' => 8, 'jenis' => 'PILIHAN'],
            ['kode' => 'PAI803', 'nama' => 'Entrepreneurship Islam', 'sks' => 2, 'semester' => 8, 'jenis' => 'WAJIB'],

            // Mata Kuliah Pilihan Tambahan
            ['kode' => 'PAI711', 'nama' => 'Pendidikan Multikultural', 'sks' => 3, 'semester' => 7, 'jenis' => 'PILIHAN'],
            ['kode' => 'PAI712', 'nama' => 'Pendidikan Karakter Islam', 'sks' => 3, 'semester' => 7, 'jenis' => 'PILIHAN'],
            ['kode' => 'PAI713', 'nama' => 'Hukum Keluarga Islam', 'sks' => 3, 'semester' => 7, 'jenis' => 'PILIHAN'],
            ['kode' => 'PAI714', 'nama' => 'Ekonomi Islam', 'sks' => 3, 'semester' => 7, 'jenis' => 'PILIHAN'],
            ['kode' => 'PAI811', 'nama' => 'Dakwah dan Komunikasi Islam', 'sks' => 3, 'semester' => 8, 'jenis' => 'PILIHAN'],
            ['kode' => 'PAI812', 'nama' => 'Studi Gender dalam Islam', 'sks' => 3, 'semester' => 8, 'jenis' => 'PILIHAN'],
            ['kode' => 'PAI813', 'nama' => 'Islam dan Sains', 'sks' => 3, 'semester' => 8, 'jenis' => 'PILIHAN'],
            ['kode' => 'PAI814', 'nama' => 'Pendidikan Inklusif', 'sks' => 3, 'semester' => 8, 'jenis' => 'PILIHAN'],
        ];

        // Insert mata kuliah dan hubungkan dengan kurikulum
        foreach ($mataKuliah as $mk) {
            // Buat mata kuliah
            $matkul = MataKuliah::create([
                'id_mata_kuliah'   => Str::uuid(),
                'kode_mata_kuliah' => $mk['kode'],
                'nama_mata_kuliah' => $mk['nama'],
                'sks_mata_kuliah'  => $mk['sks'],
            ]);

            // Hubungkan ke kurikulum
            DB::table('kurikulum_mata_kuliah')->insert([
                'id'                => Str::uuid(),
                'semester'          => $mk['semester'],
                'jenis_mata_kuliah' => $mk['jenis'],
                'id_kurikulum'      => $kurikulum->id_kurikulum,
                'id_mata_kuliah'    => $matkul->id_mata_kuliah,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        // ==========================
        // 7. Dosen Contoh
        // ==========================
        $dosenData = [
            ['nidn' => '001001', 'nama' => 'Dr. Ahmad Fauzi, M.Pd.I', 'email' => 'dosen1@gmail.com'],
            ['nidn' => '001002', 'nama' => 'Dr. Siti Aminah, M.A', 'email' => 'dosen2@gmail.com'],
            ['nidn' => '001003', 'nama' => 'Prof. Dr. Muhammad Yusuf, M.Pd', 'email' => 'dosen3@gmail.com'],
            ['nidn' => '001004', 'nama' => 'Dr. Fatimah Zahra, M.Pd.I', 'email' => 'dosen4@gmail.com'],
            ['nidn' => '001005', 'nama' => 'Dr. Abdullah Rahman, M.A', 'email' => 'dosen5@gmail.com'],
        ];

        $createdDosen = [];
        foreach ($dosenData as $index => $dosen) {
            $dosenUser = Pengguna::create([
                'id_pengguna' => Str::uuid(),
                'nama'        => $dosen['nama'],
                'username'    => $dosen['nidn'],
                'email'       => $dosen['email'],
                'password'    => Hash::make($dosen['nidn']),
                'role'        => 'dosen',
                'is_active'   => true,
            ]);

            $dosenUser->assignRole('dosen');

            $createdDosen[] = Dosen::create([
                'id_dosen'         => Str::uuid(),
                'nidn'             => $dosen['nidn'],
                'jenis_kelamin'    => $index % 2 == 0 ? 'L' : 'P',
                'id_program_studi' => $prodi->id_program_studi,
                'id_pengguna'      => $dosenUser->id_pengguna,
                'total_kuota_pa'   => 12,
            ]);
        }

        // ==========================
        // 8. Kelas Kuliah dengan Jadwal Non-Bentrok
        // ==========================

        // Ambil beberapa kurikulum mata kuliah untuk dibuka kelasnya
        $kurikulumMataKuliahTerpilih = DB::table('kurikulum_mata_kuliah as kmk')
            ->join('mata_kuliah as mk', 'kmk.id_mata_kuliah', '=', 'mk.id_mata_kuliah')
            ->where('kmk.id_kurikulum', $kurikulum->id_kurikulum)
            ->whereIn('mk.kode_mata_kuliah', [
                // Semester 1 - Semua dibuka (untuk mahasiswa baru)
                'PAI101', 'PAI102', 'PAI103', 'UNI101', 'UNI102', 'PAI104', 'PAI105',

                // Semester 2 - Sebagian dibuka
                'PAI201', 'PAI202', 'UNI201', 'PAI204',

                // Semester 3 - Beberapa saja
                'PAI301', 'PAI302', 'UNI301',

                // Semester 4 - Sedikit
                'PAI401', 'PAI402'
            ])
            ->select('kmk.*', 'mk.kode_mata_kuliah')
            ->get();

        // Jadwal non-bentrok: 5 hari x 4 slot = 20 slot waktu
        $jadwalTersedia = [
            // SENIN
            ['hari' => 'SENIN', 'jam_mulai' => '07:00:00', 'jam_akhir' => '09:30:00', 'ruangan' => 'A101'],
            ['hari' => 'SENIN', 'jam_mulai' => '09:45:00', 'jam_akhir' => '12:15:00', 'ruangan' => 'A102'],
            ['hari' => 'SENIN', 'jam_mulai' => '13:00:00', 'jam_akhir' => '15:30:00', 'ruangan' => 'A103'],
            ['hari' => 'SENIN', 'jam_mulai' => '15:45:00', 'jam_akhir' => '18:15:00', 'ruangan' => 'A104'],

            // SELASA
            ['hari' => 'SELASA', 'jam_mulai' => '07:00:00', 'jam_akhir' => '09:30:00', 'ruangan' => 'A105'],
            ['hari' => 'SELASA', 'jam_mulai' => '09:45:00', 'jam_akhir' => '12:15:00', 'ruangan' => 'A201'],
            ['hari' => 'SELASA', 'jam_mulai' => '13:00:00', 'jam_akhir' => '15:30:00', 'ruangan' => 'A202'],
            ['hari' => 'SELASA', 'jam_mulai' => '15:45:00', 'jam_akhir' => '18:15:00', 'ruangan' => 'A203'],

            // RABU
            ['hari' => 'RABU', 'jam_mulai' => '07:00:00', 'jam_akhir' => '09:30:00', 'ruangan' => 'A204'],
            ['hari' => 'RABU', 'jam_mulai' => '09:45:00', 'jam_akhir' => '12:15:00', 'ruangan' => 'A205'],
            ['hari' => 'RABU', 'jam_mulai' => '13:00:00', 'jam_akhir' => '15:30:00', 'ruangan' => 'B101'],
            ['hari' => 'RABU', 'jam_mulai' => '15:45:00', 'jam_akhir' => '18:15:00', 'ruangan' => 'B102'],

            // KAMIS
            ['hari' => 'KAMIS', 'jam_mulai' => '07:00:00', 'jam_akhir' => '09:30:00', 'ruangan' => 'B103'],
            ['hari' => 'KAMIS', 'jam_mulai' => '09:45:00', 'jam_akhir' => '12:15:00', 'ruangan' => 'B104'],
            ['hari' => 'KAMIS', 'jam_mulai' => '13:00:00', 'jam_akhir' => '15:30:00', 'ruangan' => 'B105'],
            ['hari' => 'KAMIS', 'jam_mulai' => '15:45:00', 'jam_akhir' => '18:15:00', 'ruangan' => 'B201'],

            // JUMAT (3 slot saja, karena sholat Jumat)
            ['hari' => 'JUMAT', 'jam_mulai' => '07:00:00', 'jam_akhir' => '09:30:00', 'ruangan' => 'B202'],
            ['hari' => 'JUMAT', 'jam_mulai' => '09:45:00', 'jam_akhir' => '11:30:00', 'ruangan' => 'B203'], // Lebih pendek untuk Jumat
            ['hari' => 'JUMAT', 'jam_mulai' => '14:00:00', 'jam_akhir' => '16:30:00', 'ruangan' => 'B204'], // Setelah Jumat
            ['hari' => 'JUMAT', 'jam_mulai' => '16:45:00', 'jam_akhir' => '18:15:00', 'ruangan' => 'B205'],
        ];

        $jadwalIndex = 0;

        foreach ($kurikulumMataKuliahTerpilih as $kmk) {
            $dosenTerpilih = $createdDosen[$jadwalIndex % count($createdDosen)];

            // Mata kuliah populer dibuat 2 kelas
            $jumlahKelas = in_array($kmk->kode_mata_kuliah, ['PAI101', 'PAI102', 'PAI103']) ? 2 : 1;

            for ($kelasKe = 1; $kelasKe <= $jumlahKelas; $kelasKe++) {
                $namaKelas = $jumlahKelas > 1 ? chr(64 + $kelasKe) : 'A'; // A, B jika ada 2 kelas
                $jadwal = $jadwalTersedia[$jadwalIndex % count($jadwalTersedia)];

                DB::table('kelas_kuliah')->insert([
                    'id_kelas_kuliah'        => Str::uuid(),
                    'nama_kelas_kuliah'      => "Kelas {$namaKelas}",
                    'nama_ruangan'           => $jadwal['ruangan'],
                    'kapasitas'              => 40,
                    'jam_mulai'              => $jadwal['jam_mulai'],
                    'jam_akhir'              => $jadwal['jam_akhir'],
                    'hari'                   => $jadwal['hari'],
                    'id_kurikulum_mata_kuliah' => $kmk->id,
                    'id_semester'            => $semesterGanjil->id_semester,
                    'id_dosen'               => $dosenTerpilih->id_dosen,
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);

                $jadwalIndex++; // Increment untuk jadwal berikutnya
            }
        }

        // ==========================
        // 9. Mahasiswa Contoh
        // ==========================
        $mahasiswaData = [
            ['nim' => '002001', 'nama' => 'Ahmad Rizki Pratama', 'email' => 'mhs1@gmail.com', 'angkatan' => 2024],
            ['nim' => '002002', 'nama' => 'Siti Nur Halimah', 'email' => 'mhs2@gmail.com', 'angkatan' => 2024],
            ['nim' => '002003', 'nama' => 'Muhammad Fadli', 'email' => 'mhs3@gmail.com', 'angkatan' => 2024],
            ['nim' => '002004', 'nama' => 'Khadijah Azzahra', 'email' => 'mhs4@gmail.com', 'angkatan' => 2024],
            ['nim' => '002005', 'nama' => 'Umar Farouk', 'email' => 'mhs5@gmail.com', 'angkatan' => 2024],
            ['nim' => '002006', 'nama' => 'Ali Hassan', 'email' => 'mhs6@gmail.com', 'angkatan' => 2023],
            ['nim' => '002007', 'nama' => 'Fatma Dewi', 'email' => 'mhs7@gmail.com', 'angkatan' => 2023],
            ['nim' => '002008', 'nama' => 'Ibrahim Malik', 'email' => 'mhs8@gmail.com', 'angkatan' => 2023],
            ['nim' => '002009', 'nama' => 'Zainab Husna', 'email' => 'mhs9@gmail.com', 'angkatan' => 2022],
            ['nim' => '002010', 'nama' => 'Yusuf Abdullah', 'email' => 'mhs10@gmail.com', 'angkatan' => 2022],
        ];

        $createdMahasiswa = [];
        foreach ($mahasiswaData as $index => $mhs) {
            $mhsUser = Pengguna::create([
                'id_pengguna' => Str::uuid(),
                'nama'        => $mhs['nama'],
                'username'    => $mhs['nim'],
                'email'       => $mhs['email'],
                'password'    => Hash::make($mhs['nim']),
                'role'        => 'mahasiswa',
                'is_active'   => true,
            ]);

            $mhsUser->assignRole('mahasiswa');

            $createdMahasiswa[] = Mahasiswa::create([
                'id_mahasiswa'     => Str::uuid(),
                'nim'              => $mhs['nim'],
                'jenis_kelamin'    => $index % 2 == 0 ? 'L' : 'P',
                'angkatan'         => $mhs['angkatan'],
                'id_program_studi' => $prodi->id_program_studi,
                'id_kurikulum'     => $kurikulum->id_kurikulum,
                'id_pengguna'      => $mhsUser->id_pengguna,
            ]);
        }

        // ==========================
        // 10. Assignment Pembimbing Akademik
        // ==========================

        // Distribusi mahasiswa ke dosen PA secara merata
        // 5 dosen dengan kuota 12 masing-masing = total 60 mahasiswa bisa dibimbing
        // Kita punya 10 mahasiswa, jadi masing-masing dosen dapat 2 mahasiswa

        $mahasiswaPerDosen = collect($createdMahasiswa)->chunk(2); // Bagi 10 mahasiswa jadi 5 grup (2 mahasiswa per grup)

        $nomorSK = 1;
        foreach ($mahasiswaPerDosen as $dosenIndex => $mahasiswaGroup) {
            $dosenPA = $createdDosen[$dosenIndex];

            foreach ($mahasiswaGroup as $mahasiswa) {
                DB::table('pembimbing_akademik')->insert([
                    'id_pembimbing_akademik' => Str::uuid(),
                    'id_mahasiswa'           => $mahasiswa->id_mahasiswa,
                    'id_dosen'               => $dosenPA->id_dosen,
                    'id_semester'            => $semesterGanjil->id_semester,
                    'status_pa'              => 'AKTIF',
                    'nomor_sk'               => sprintf("SK/PAI/PA/%03d/2024", $nomorSK),
                    'tanggal_sk'             => '2024-08-01',
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);

                $nomorSK++;
            }
        }

        // Log hasil assignment
        $this->command->info('=== HASIL ASSIGNMENT PEMBIMBING AKADEMIK ===');

        foreach ($createdDosen as $index => $dosen) {
            $jumlahMahasiswaBimbingan = DB::table('pembimbing_akademik as pa')
                ->join('mahasiswa as m', 'pa.id_mahasiswa', '=', 'm.id_mahasiswa')
                ->where('pa.id_dosen', $dosen->id_dosen)
                ->where('pa.id_semester', $semesterGanjil->id_semester)
                ->count();

            $this->command->info("Dosen {$dosen->nidn} ({$dosen->pengguna->nama}): {$jumlahMahasiswaBimbingan} mahasiswa");
        }

        $this->command->info('=== KELAS KULIAH YANG DIBUKA ===');
        $kelasKuliah = DB::table('kelas_kuliah as kk')
            ->join('kurikulum_mata_kuliah as kmk', 'kk.id_kurikulum_mata_kuliah', '=', 'kmk.id')
            ->join('mata_kuliah as mk', 'kmk.id_mata_kuliah', '=', 'mk.id_mata_kuliah')
            ->join('dosen as d', 'kk.id_dosen', '=', 'd.id_dosen')
            ->join('pengguna as p', 'd.id_pengguna', '=', 'p.id_pengguna')
            ->where('kk.id_semester', $semesterGanjil->id_semester)
            ->select(
                'mk.kode_mata_kuliah',
                'mk.nama_mata_kuliah',
                'kk.nama_kelas_kuliah',
                'kk.hari',
                'kk.jam_mulai',
                'kk.jam_akhir',
                'kk.nama_ruangan',
                'p.nama as nama_dosen'
            )
            ->orderBy('kk.hari')
            ->orderBy('kk.jam_mulai')
            ->get();

        foreach ($kelasKuliah as $kelas) {
            $this->command->info("{$kelas->kode_mata_kuliah} ({$kelas->nama_kelas_kuliah}) - {$kelas->hari} {$kelas->jam_mulai}-{$kelas->jam_akhir} - {$kelas->nama_ruangan} - {$kelas->nama_dosen}");
        }

        $pengumuman = [
            [
                'judul' => 'Perkuliahan Semester Ganjil 2025/2026',
                'deskripsi' => 'Perkuliahan semester ganjil Tahun Akademik 2025/2026 akan dimulai pada tanggal 1 September 2025. Mahasiswa diminta melakukan KRS online sebelum tanggal 30 Agustus 2025.',
                'tujuan' => 'mahasiswa',
            ],
            [
                'judul' => 'Pendaftaran Wisuda Periode Desember 2025',
                'deskripsi' => 'Pendaftaran wisuda untuk periode Desember 2025 telah dibuka. Mahasiswa yang telah memenuhi syarat kelulusan dapat melakukan pendaftaran melalui portal akademik.',
                'tujuan' => 'mahasiswa',
            ],
            [
                'judul' => 'Workshop Metodologi Penelitian',
                'deskripsi' => 'Dosen diharapkan mengikuti workshop metodologi penelitian yang akan diselenggarakan pada tanggal 15 Oktober 2025 di Aula Rektorat.',
                'tujuan' => 'dosen',
            ],
            [
                'judul' => 'Pengumuman Cuti Bersama dan Libur Nasional',
                'deskripsi' => 'Sehubungan dengan perayaan Hari Raya Natal dan Tahun Baru, kegiatan akademik akan diliburkan pada tanggal 24 Desember 2025 hingga 2 Januari 2026.',
                'tujuan' => 'umum',
            ],
            [
                'judul' => 'Penerimaan Beasiswa Prestasi',
                'deskripsi' => 'Universitas membuka pendaftaran Beasiswa Prestasi bagi mahasiswa aktif yang memiliki IPK minimal 3.50. Pendaftaran dibuka hingga 10 November 2025.',
                'tujuan' => 'mahasiswa',
            ],
            [
                'judul' => 'Rapat Senat Akademik',
                'deskripsi' => 'Diberitahukan kepada seluruh anggota senat bahwa rapat senat akademik akan dilaksanakan pada tanggal 5 November 2025 pukul 09.00 WIB di Ruang Senat.',
                'tujuan' => 'dosen',
            ],
        ];

        foreach ($pengumuman as $item) {
            Pengumuman::create($item);
        }

        $kalender = [
            [
                'judul' => 'Awal Perkuliahan Semester Ganjil 2025/2026',
                'tanggal_mulai' => '2025-09-01',
                'tanggal_selesai' => '2025-09-01',
                'is_all_day' => true,
            ],
            [
                'judul' => 'Orientasi Mahasiswa Baru',
                'tanggal_mulai' => '2025-09-02',
                'tanggal_selesai' => '2025-09-04',
                'is_all_day' => true,
            ],
            [
                'judul' => 'Batas Akhir Perubahan KRS',
                'tanggal_mulai' => '2025-09-10',
                'tanggal_selesai' => '2025-09-10',
                'is_all_day' => true,
            ],
            [
                'judul' => 'Pembekalan Kuliah Kerja Nyata (KKN)',
                'tanggal_mulai' => '2025-09-15',
                'tanggal_selesai' => '2025-09-16',
                'is_all_day' => true,
            ],
            [
                'judul' => 'Libur Maulid Nabi Muhammad SAW',
                'tanggal_mulai' => '2025-09-17',
                'tanggal_selesai' => '2025-09-17',
                'is_all_day' => true,
            ],
            [
                'judul' => 'Pelaksanaan Seminar Proposal',
                'tanggal_mulai' => '2025-09-25',
                'tanggal_selesai' => '2025-09-26',
                'is_all_day' => true,
            ],
        ];

        foreach ($kalender as $item) {
            KalenderAkademik::create($item);
        }
    }
}
