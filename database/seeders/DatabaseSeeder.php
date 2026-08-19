<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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

class DatabaseSeeder extends Seeder
{
    private $createdDosen = [];
    private $createdKurikulum = null;
    private $createdSemester = [];
    private $createdMataKuliah = [];
    private $jadwalMatrix = [];
    private $currentDate = '2025-09-25'; // Tanggal sekarang

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
            'nama'        => 'Administrator SIAKAD',
            'username'    => '001001',
            'email'       => 'admin@edubrain.ac.id',
            'password'    => Hash::make('001001'),
            'role'        => 'admin',
            'is_active'   => true,
        ]);
        $admin->assignRole('admin');

        // ==========================
        // 2. Jenjang Pendidikan & Program Studi
        // ==========================
        $jenjang = JenjangPendidikan::create([
            'id_jenjang_pendidikan'   => Str::uuid(),
            'kode_jenjang_pendidikan' => 'S1',
            'nama_jenjang_pendidikan' => 'Sarjana',
        ]);

        $prodi = ProgramStudi::create([
            'id_program_studi'   => Str::uuid(),
            'kode_program_studi' => 'TI001',
            'nama_program_studi' => 'Teknik Informatika',
            'status'             => 'A',
            'id_jenjang_pendidikan' => $jenjang->id_jenjang_pendidikan,
        ]);

        // ==========================
        // 3. Semester 2022-2025
        // ==========================
        $this->createAllSemesters();

        // ==========================
        // 4. Kurikulum 2022 (Standar)
        // ==========================
        $this->createKurikulum2022($prodi);

        // ==========================
        // 5. Mata Kuliah Kurikulum 2022
        // ==========================
        $this->createMataKuliahKurikulum2022();

        // ==========================
        // 6. Dosen
        // ==========================
        $this->createDosen($prodi);

        // ==========================
        // 7. Kelas Kuliah per Semester
        // ==========================
        $this->createKelasKuliahHistoris();

        // ==========================
        // 8. Mahasiswa dengan Data Historis Akademik
        // ==========================
        $this->createMahasiswaWithAcademicHistory($prodi);

        // ==========================
        // 9. Pengumuman & Kalender
        // ==========================
        $this->createPengumumanDanKalender();

        $this->logResults();
    }

    private function createAllSemesters()
    {
        $semesterData = [
            // 2022/2023  
            ['kode' => '20221', 'nama' => 'Ganjil 2022/2023', 'tipe' => 'ganjil', 'mulai' => '2022-08-15', 'selesai' => '2023-01-15', 'aktif' => false],
            ['kode' => '20222', 'nama' => 'Genap 2022/2023', 'tipe' => 'genap', 'mulai' => '2023-02-15', 'selesai' => '2023-07-15', 'aktif' => false],

            // 2023/2024
            ['kode' => '20231', 'nama' => 'Ganjil 2023/2024', 'tipe' => 'ganjil', 'mulai' => '2023-08-15', 'selesai' => '2024-01-15', 'aktif' => false],
            ['kode' => '20232', 'nama' => 'Genap 2023/2024', 'tipe' => 'genap', 'mulai' => '2024-02-15', 'selesai' => '2024-07-15', 'aktif' => false],

            // 2024/2025
            ['kode' => '20241', 'nama' => 'Ganjil 2024/2025', 'tipe' => 'ganjil', 'mulai' => '2024-08-15', 'selesai' => '2025-01-15', 'aktif' => false],
            ['kode' => '20242', 'nama' => 'Genap 2024/2025', 'tipe' => 'genap', 'mulai' => '2025-02-15', 'selesai' => '2025-07-15', 'aktif' => false],

            // 2025/2026 - Semester aktif
            ['kode' => '20251', 'nama' => 'Ganjil 2025/2026', 'tipe' => 'ganjil', 'mulai' => '2025-08-15', 'selesai' => '2026-01-15', 'aktif' => true],
        ];

        foreach ($semesterData as $sem) {
            $semester = Semester::create([
                'id_semester'    => Str::uuid(),
                'kode_semester'  => $sem['kode'],
                'nama_semester'  => $sem['nama'],
                'tipe'           => $sem['tipe'],
                'tanggal_mulai'  => $sem['mulai'],
                'tanggal_selesai' => $sem['selesai'],
                'is_active'      => $sem['aktif'],
            ]);

            $this->createdSemester[$sem['kode']] = $semester;
        }
    }

    private function createKurikulum2022($prodi)
    {
        $this->createdKurikulum = Kurikulum::create([
            'id_kurikulum'         => Str::uuid(),
            'nama_kurikulum'       => 'Kurikulum 2022',
            'jumlah_sks_lulus'     => 144,
            'sks_mkwuupt_minimal' => 8,   // Pancasila, Kewarganegaraan, Agama
            'sks_mkwu_minimal'    => 12,  // Bahasa Indonesia, Inggris, Kewirausahaan, dll
            'sks_mkwps_minimal'   => 108, // Mata kuliah inti program studi
            'sks_mkp_minimal'     => 16,  // Mata kuliah pilihan
            'id_program_studi'     => $prodi->id_program_studi,
            'id_semester'          => $this->createdSemester['20221']->id_semester,
        ]);
    }

    private function createMataKuliahKurikulum2022()
    {
        $mataKuliahData = [
            // ===========================================
            // SEMESTER 1 (18 SKS)
            // ===========================================
            ['kode' => 'TIF101', 'nama' => 'Pengantar Teknologi Informasi', 'sks' => 2, 'semester' => 1, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF102', 'nama' => 'Algoritma dan Pemrograman', 'sks' => 3, 'semester' => 1, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF103', 'nama' => 'Praktikum Algoritma dan Pemrograman', 'sks' => 1, 'semester' => 1, 'kategori' => 'MKWPS', 'jenis' => 'PRAKTIKUM'],
            ['kode' => 'TIF104', 'nama' => 'Matematika Dasar', 'sks' => 3, 'semester' => 1, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF105', 'nama' => 'Fisika Dasar', 'sks' => 2, 'semester' => 1, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'UNI101', 'nama' => 'Pancasila', 'sks' => 2, 'semester' => 1, 'kategori' => 'MKWUUPT', 'jenis' => 'TEORI'],
            ['kode' => 'UNI102', 'nama' => 'Bahasa Indonesia', 'sks' => 2, 'semester' => 1, 'kategori' => 'MKWU', 'jenis' => 'TEORI'],
            ['kode' => 'UNI103', 'nama' => 'Agama Islam', 'sks' => 3, 'semester' => 1, 'kategori' => 'MKWUUPT', 'jenis' => 'TEORI'],

            // ===========================================
            // SEMESTER 2 (20 SKS)
            // ===========================================
            ['kode' => 'TIF201', 'nama' => 'Struktur Data', 'sks' => 3, 'semester' => 2, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF202', 'nama' => 'Praktikum Struktur Data', 'sks' => 1, 'semester' => 2, 'kategori' => 'MKWPS', 'jenis' => 'PRAKTIKUM'],
            ['kode' => 'TIF203', 'nama' => 'Pemrograman Berorientasi Objek', 'sks' => 3, 'semester' => 2, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF204', 'nama' => 'Praktikum Pemrograman Berorientasi Objek', 'sks' => 1, 'semester' => 2, 'kategori' => 'MKWPS', 'jenis' => 'PRAKTIKUM'],
            ['kode' => 'TIF205', 'nama' => 'Matematika Diskrit', 'sks' => 3, 'semester' => 2, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF206', 'nama' => 'Organisasi dan Arsitektur Komputer', 'sks' => 3, 'semester' => 2, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF207', 'nama' => 'Statistika dan Probabilitas', 'sks' => 3, 'semester' => 2, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'UNI201', 'nama' => 'Kewarganegaraan', 'sks' => 3, 'semester' => 2, 'kategori' => 'MKWUUPT', 'jenis' => 'TEORI'],

            // ===========================================
            // SEMESTER 3 (20 SKS)
            // ===========================================
            ['kode' => 'TIF301', 'nama' => 'Basis Data', 'sks' => 3, 'semester' => 3, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF302', 'nama' => 'Praktikum Basis Data', 'sks' => 1, 'semester' => 3, 'kategori' => 'MKWPS', 'jenis' => 'PRAKTIKUM'],
            ['kode' => 'TIF303', 'nama' => 'Analisis dan Perancangan Algoritma', 'sks' => 3, 'semester' => 3, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF304', 'nama' => 'Sistem Operasi', 'sks' => 3, 'semester' => 3, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF305', 'nama' => 'Praktikum Sistem Operasi', 'sks' => 1, 'semester' => 3, 'kategori' => 'MKWPS', 'jenis' => 'PRAKTIKUM'],
            ['kode' => 'TIF306', 'nama' => 'Jaringan Komputer', 'sks' => 3, 'semester' => 3, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF307', 'nama' => 'Praktikum Jaringan Komputer', 'sks' => 1, 'semester' => 3, 'kategori' => 'MKWPS', 'jenis' => 'PRAKTIKUM'],
            ['kode' => 'TIF308', 'nama' => 'Kalkulus', 'sks' => 3, 'semester' => 3, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'UNI301', 'nama' => 'Bahasa Inggris', 'sks' => 2, 'semester' => 3, 'kategori' => 'MKWU', 'jenis' => 'TEORI'],

            // ===========================================
            // SEMESTER 4 (18 SKS)
            // ===========================================
            ['kode' => 'TIF401', 'nama' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'semester' => 4, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF402', 'nama' => 'Praktikum Rekayasa Perangkat Lunak', 'sks' => 1, 'semester' => 4, 'kategori' => 'MKWPS', 'jenis' => 'PRAKTIKUM'],
            ['kode' => 'TIF403', 'nama' => 'Pemrograman Web', 'sks' => 3, 'semester' => 4, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF404', 'nama' => 'Praktikum Pemrograman Web', 'sks' => 1, 'semester' => 4, 'kategori' => 'MKWPS', 'jenis' => 'PRAKTIKUM'],
            ['kode' => 'TIF405', 'nama' => 'Interaksi Manusia dan Komputer', 'sks' => 3, 'semester' => 4, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF406', 'nama' => 'Grafika Komputer', 'sks' => 3, 'semester' => 4, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF407', 'nama' => 'Praktikum Grafika Komputer', 'sks' => 1, 'semester' => 4, 'kategori' => 'MKWPS', 'jenis' => 'PRAKTIKUM'],
            ['kode' => 'TIF408', 'nama' => 'Metodologi Penelitian', 'sks' => 3, 'semester' => 4, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],

            // ===========================================
            // SEMESTER 5 (18 SKS)
            // ===========================================
            ['kode' => 'TIF501', 'nama' => 'Kecerdasan Buatan', 'sks' => 3, 'semester' => 5, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF502', 'nama' => 'Praktikum Kecerdasan Buatan', 'sks' => 1, 'semester' => 5, 'kategori' => 'MKWPS', 'jenis' => 'PRAKTIKUM'],
            ['kode' => 'TIF503', 'nama' => 'Keamanan Sistem Informasi', 'sks' => 3, 'semester' => 5, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF504', 'nama' => 'Praktikum Keamanan Sistem Informasi', 'sks' => 1, 'semester' => 5, 'kategori' => 'MKWPS', 'jenis' => 'PRAKTIKUM'],
            ['kode' => 'TIF505', 'nama' => 'Pemrograman Mobile', 'sks' => 3, 'semester' => 5, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF506', 'nama' => 'Praktikum Pemrograman Mobile', 'sks' => 1, 'semester' => 5, 'kategori' => 'MKWPS', 'jenis' => 'PRAKTIKUM'],
            ['kode' => 'TIF507', 'nama' => 'Manajemen Proyek TI', 'sks' => 3, 'semester' => 5, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'UNI501', 'nama' => 'Kewirausahaan', 'sks' => 3, 'semester' => 5, 'kategori' => 'MKWU', 'jenis' => 'TEORI'],

            // ===========================================
            // SEMESTER 6 (18 SKS)
            // ===========================================
            ['kode' => 'TIF601', 'nama' => 'Data Mining', 'sks' => 3, 'semester' => 6, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF602', 'nama' => 'Praktikum Data Mining', 'sks' => 1, 'semester' => 6, 'kategori' => 'MKWPS', 'jenis' => 'PRAKTIKUM'],
            ['kode' => 'TIF603', 'nama' => 'Sistem Terdistribusi', 'sks' => 3, 'semester' => 6, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF604', 'nama' => 'Etika Profesi TI', 'sks' => 2, 'semester' => 6, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF605', 'nama' => 'Mata Kuliah Pilihan I', 'sks' => 3, 'semester' => 6, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIF606', 'nama' => 'Mata Kuliah Pilihan II', 'sks' => 3, 'semester' => 6, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIF607', 'nama' => 'Seminar', 'sks' => 1, 'semester' => 6, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'UNI601', 'nama' => 'Bahasa Asing', 'sks' => 2, 'semester' => 6, 'kategori' => 'MKWU', 'jenis' => 'TEORI'],

            // ===========================================
            // SEMESTER 7 (16 SKS)
            // ===========================================
            ['kode' => 'TIF701', 'nama' => 'Kuliah Kerja Nyata (KKN)', 'sks' => 4, 'semester' => 7, 'kategori' => 'MKWPS', 'jenis' => 'KKN'],
            ['kode' => 'TIF702', 'nama' => 'Kerja Praktik', 'sks' => 4, 'semester' => 7, 'kategori' => 'MKWPS', 'jenis' => 'MAGANG'],
            ['kode' => 'TIF703', 'nama' => 'Mata Kuliah Pilihan III', 'sks' => 3, 'semester' => 7, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIF704', 'nama' => 'Mata Kuliah Pilihan IV', 'sks' => 3, 'semester' => 7, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIF705', 'nama' => 'Proposal Tugas Akhir', 'sks' => 2, 'semester' => 7, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],

            // ===========================================
            // SEMESTER 8 (18 SKS)
            // ===========================================
            ['kode' => 'TIF801', 'nama' => 'Tugas Akhir/Skripsi', 'sks' => 6, 'semester' => 8, 'kategori' => 'MKWPS', 'jenis' => 'SKRIPSI'],
            ['kode' => 'TIF802', 'nama' => 'Mata Kuliah Pilihan V', 'sks' => 3, 'semester' => 8, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIF803', 'nama' => 'Mata Kuliah Pilihan VI', 'sks' => 3, 'semester' => 8, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIF804', 'nama' => 'Kapita Selekta', 'sks' => 3, 'semester' => 8, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],
            ['kode' => 'TIF805', 'nama' => 'Teknologi Web Terkini', 'sks' => 3, 'semester' => 8, 'kategori' => 'MKWPS', 'jenis' => 'TEORI'],

            // ===========================================
            // MATA KULIAH PILIHAN
            // ===========================================
            ['kode' => 'TIP101', 'nama' => 'Machine Learning', 'sks' => 3, 'semester' => 6, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIP102', 'nama' => 'Cloud Computing', 'sks' => 3, 'semester' => 6, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIP103', 'nama' => 'Internet of Things (IoT)', 'sks' => 3, 'semester' => 6, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIP104', 'nama' => 'Blockchain Technology', 'sks' => 3, 'semester' => 6, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIP105', 'nama' => 'Cyber Security', 'sks' => 3, 'semester' => 7, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIP106', 'nama' => 'Big Data Analytics', 'sks' => 3, 'semester' => 7, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIP107', 'nama' => 'Augmented Reality/Virtual Reality', 'sks' => 3, 'semester' => 7, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIP108', 'nama' => 'DevOps', 'sks' => 3, 'semester' => 7, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIP109', 'nama' => 'Digital Image Processing', 'sks' => 3, 'semester' => 8, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIP110', 'nama' => 'Game Development', 'sks' => 3, 'semester' => 8, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIP111', 'nama' => 'Natural Language Processing', 'sks' => 3, 'semester' => 8, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
            ['kode' => 'TIP112', 'nama' => 'Robotics', 'sks' => 3, 'semester' => 8, 'kategori' => 'MKP', 'jenis' => 'TEORI'],
        ];

        foreach ($mataKuliahData as $mk) {
            $matkul = MataKuliah::create([
                'id_mata_kuliah'   => Str::uuid(),
                'kode_mata_kuliah' => $mk['kode'],
                'nama_mata_kuliah' => $mk['nama'],
                'sks_mata_kuliah'  => $mk['sks'],
                'jenis_mata_kuliah' => $mk['jenis'],
            ]);

            $this->createdMataKuliah[$mk['kode']] = $matkul;

            // Hubungkan ke kurikulum
            DB::table('kurikulum_mata_kuliah')->insert([
                'id'                   => Str::uuid(),
                'semester'             => $mk['semester'],
                'kategori_mata_kuliah' => $mk['kategori'],
                'id_kurikulum'         => $this->createdKurikulum->id_kurikulum,
                'id_mata_kuliah'       => $matkul->id_mata_kuliah,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }
    }

    private function createDosen($prodi)
    {
        $dosenData = [
            ['nidn' => '0001018201', 'nama' => 'Dr. Budi Santoso, M.Kom', 'email' => 'budi.santoso@edubrain.ac.id', 'bidang' => 'Algoritma dan Pemrograman'],
            ['nidn' => '0002027802', 'nama' => 'Dr. Sari Widyaningsih, M.T', 'email' => 'sari.widya@edubrain.ac.id', 'bidang' => 'Basis Data'],
            ['nidn' => '0003036703', 'nama' => 'Prof. Dr. Ahmad Fauzi, M.Sc', 'email' => 'ahmad.fauzi@edubrain.ac.id', 'bidang' => 'Kecerdasan Buatan'],
            ['nidn' => '0004048304', 'nama' => 'Dr. Rina Fitriani, M.Kom', 'email' => 'rina.fitriani@edubrain.ac.id', 'bidang' => 'Jaringan Komputer'],
            ['nidn' => '0005057905', 'nama' => 'Dr. Dedy Hermawan, M.T', 'email' => 'dedy.hermawan@edubrain.ac.id', 'bidang' => 'Rekayasa Perangkat Lunak'],
            ['nidn' => '0006068506', 'nama' => 'Dr. Maya Sari, M.Kom', 'email' => 'maya.sari@edubrain.ac.id', 'bidang' => 'Pemrograman Web'],
            ['nidn' => '0007079107', 'nama' => 'Dr. Andi Suryanto, M.T', 'email' => 'andi.suryanto@edubrain.ac.id', 'bidang' => 'Sistem Operasi'],
            ['nidn' => '0008088708', 'nama' => 'Dr. Lisa Permata, M.Kom', 'email' => 'lisa.permata@edubrain.ac.id', 'bidang' => 'Data Mining'],
            ['nidn' => '0009097509', 'nama' => 'Dr. Rudi Hartono, M.T', 'email' => 'rudi.hartono@edubrain.ac.id', 'bidang' => 'Mobile Development'],
            ['nidn' => '0010108010', 'nama' => 'Dr. Nina Wijaya, M.Kom', 'email' => 'nina.wijaya@edubrain.ac.id', 'bidang' => 'Keamanan Sistem'],
            ['nidn' => '0011118911', 'nama' => 'Dr. Hadi Purnomo, M.T', 'email' => 'hadi.purnomo@edubrain.ac.id', 'bidang' => 'Cloud Computing'],
            ['nidn' => '0012129212', 'nama' => 'Dr. Indah Lestari, M.Kom', 'email' => 'indah.lestari@edubrain.ac.id', 'bidang' => 'Machine Learning'],
        ];

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

            $this->createdDosen[] = Dosen::create([
                'id_dosen'         => Str::uuid(),
                'nidn'             => $dosen['nidn'],
                'jenis_kelamin'    => $index % 2 == 0 ? 'L' : 'P',
                'tempat_lahir'     => ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Medan'][$index % 5],
                'tanggal_lahir'    => Carbon::parse('1970-01-01')->addYears($index),
                'id_program_studi' => $prodi->id_program_studi,
                'id_pengguna'      => $dosenUser->id_pengguna,
                'total_kuota_pa'   => 10,
                'status_dosen'     => 'AKTIF',
                'status_kepegawaian' => ['PNS', 'TETAP', 'KONTRAK'][$index % 3],
            ]);
        }
    }

    private function createKelasKuliahHistoris()
    {
        $this->initializeJadwalMatrix();

        $semesterHistoris = ['20221', '20222', '20231', '20232', '20241', '20242'];

        foreach ($semesterHistoris as $kodeSemester) {
            $this->createKelasPerSemester($kodeSemester);
            // Reset jadwal matrix untuk semester berikutnya
            $this->initializeJadwalMatrix();
        }
    }

    private function initializeJadwalMatrix()
    {
        $this->jadwalMatrix = [];
        $hari = ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT'];
        $jamSlot = [
            ['mulai' => '07:00:00', 'akhir' => '09:30:00'],
            ['mulai' => '09:45:00', 'akhir' => '12:15:00'],
            ['mulai' => '13:00:00', 'akhir' => '15:30:00'],
            ['mulai' => '15:45:00', 'akhir' => '18:15:00'],
        ];

        $ruangan = [
            'Lab Komputer A', 'Lab Komputer B', 'Lab Komputer C', 'Lab Komputer D',
            'Ruang A101', 'Ruang A102', 'Ruang B101', 'Ruang B102',
            'Ruang C101', 'Ruang C102', 'Ruang D101', 'Ruang D102'
        ];

        foreach ($hari as $h) {
            foreach ($jamSlot as $slot) {
                foreach ($ruangan as $r) {
                    $key = $h . '_' . $slot['mulai'] . '_' . $r;
                    $this->jadwalMatrix[$key] = [
                        'hari' => $h,
                        'jam_mulai' => $slot['mulai'],
                        'jam_akhir' => $slot['akhir'],
                        'ruangan' => $r,
                        'terpakai' => false
                    ];
                }
            }
        }
    }

    private function createKelasPerSemester($kodeSemester)
    {
        $semesterObj = $this->createdSemester[$kodeSemester];
        $tipeSemester = substr($kodeSemester, 4, 1) == '1' ? 'ganjil' : 'genap';

        // Mata kuliah yang dibuka di semester ini
        $mataKuliahDibuka = $this->getMataKuliahDibukaPerSemester($tipeSemester);

        $kurikulumMataKuliah = DB::table('kurikulum_mata_kuliah as kmk')
            ->join('mata_kuliah as mk', 'kmk.id_mata_kuliah', '=', 'mk.id_mata_kuliah')
            ->where('kmk.id_kurikulum', $this->createdKurikulum->id_kurikulum)
            ->whereIn('mk.kode_mata_kuliah', $mataKuliahDibuka)
            ->where('mk.jenis_mata_kuliah', '!=', 'KKN')  // Exclude KKN, MAGANG, SKRIPSI
            ->where('mk.jenis_mata_kuliah', '!=', 'MAGANG')
            ->where('mk.jenis_mata_kuliah', '!=', 'SKRIPSI')
            ->select('kmk.*', 'mk.kode_mata_kuliah', 'mk.nama_mata_kuliah', 'mk.jenis_mata_kuliah')
            ->get();

        foreach ($kurikulumMataKuliah as $kmk) {
            $jumlahKelas = $this->getJumlahKelasPerMK($kmk->kode_mata_kuliah);

            for ($kelasKe = 1; $kelasKe <= $jumlahKelas; $kelasKe++) {
                $jadwal = $this->getAvailableJadwal();
                if (!$jadwal) continue;

                $dosen = $this->createdDosen[array_rand($this->createdDosen)];
                $namaKelas = $jumlahKelas > 1 ? chr(64 + $kelasKe) : 'A';

                DB::table('kelas_kuliah')->insert([
                    'id_kelas_kuliah'          => Str::uuid(),
                    'nama_kelas_kuliah'        => "Kelas {$namaKelas}",
                    'nama_ruangan'             => $jadwal['ruangan'],
                    'kapasitas'                => 40,
                    'jam_mulai'                => $jadwal['jam_mulai'],
                    'jam_akhir'                => $jadwal['jam_akhir'],
                    'hari'                     => $jadwal['hari'],
                    'status'                   => 'selesai',
                    'id_kurikulum_mata_kuliah' => $kmk->id,
                    'id_semester'              => $semesterObj->id_semester,
                    'id_dosen'                 => $dosen->id_dosen,
                    'created_at'               => now(),
                    'updated_at'               => now(),
                ]);

                $this->markJadwalTerpakai($jadwal);
            }
        }
    }

    private function getMataKuliahDibukaPerSemester($tipeSemester)
    {
        if ($tipeSemester === 'ganjil') {
            return [
                // Semester 1
                'TIF101', 'TIF102', 'TIF103', 'TIF104', 'TIF105', 'UNI101', 'UNI102', 'UNI103',

                // Semester 3
                'TIF301', 'TIF302', 'TIF303', 'TIF304', 'TIF305', 'TIF306', 'TIF307', 'TIF308', 'UNI301',

                // Semester 5
                'TIF501', 'TIF502', 'TIF503', 'TIF504', 'TIF505', 'TIF506', 'TIF507', 'UNI501',

                // Semester 7
                'TIF703', 'TIF704', 'TIF705',

                // Mata kuliah pilihan
                'TIP105', 'TIP106', 'TIP107', 'TIP108'
            ];
        } else { // genap
            return [
                // Semester 2
                'TIF201', 'TIF202', 'TIF203', 'TIF204', 'TIF205', 'TIF206', 'TIF207', 'UNI201',

                // Semester 4
                'TIF401', 'TIF402', 'TIF403', 'TIF404', 'TIF405', 'TIF406', 'TIF407', 'TIF408',

                // Semester 6
                'TIF601', 'TIF602', 'TIF603', 'TIF604', 'TIF605', 'TIF606', 'TIF607', 'UNI601',

                // Semester 8
                'TIF802', 'TIF803', 'TIF804', 'TIF805',

                // Mata kuliah pilihan
                'TIP101', 'TIP102', 'TIP103', 'TIP104', 'TIP109', 'TIP110', 'TIP111', 'TIP112'
            ];
        }
    }

    private function getJumlahKelasPerMK($kodeMK)
    {
        // Mata kuliah dasar lebih banyak kelas
        $mkDasar = ['TIF101', 'TIF102', 'TIF103', 'TIF104', 'TIF201', 'TIF203', 'UNI101', 'UNI102', 'UNI103'];

        if (in_array($kodeMK, $mkDasar)) {
            return 3;
        }

        return 2;
    }

    private function getAvailableJadwal()
    {
        foreach ($this->jadwalMatrix as $key => $jadwal) {
            if (!$jadwal['terpakai']) {
                return $jadwal;
            }
        }
        return null;
    }

    private function markJadwalTerpakai($jadwal)
    {
        $key = $jadwal['hari'] . '_' . $jadwal['jam_mulai'] . '_' . $jadwal['ruangan'];
        if (isset($this->jadwalMatrix[$key])) {
            $this->jadwalMatrix[$key]['terpakai'] = true;
        }
    }

    private function createMahasiswaWithAcademicHistory($prodi)
    {
        $mahasiswaData = [
            // Mahasiswa yang masuk semester 20221 (Ganjil 2022/2023) - sekarang semester 7
            ['nim' => '2022001', 'nama' => 'Ahmad Rizki Pratama', 'angkatan' => 2022, 'semester_masuk' => '20221', 'semester_sekarang' => 7],

            // Mahasiswa yang masuk semester 20222 (Genap 2022/2023) - sekarang semester 6  
            ['nim' => '2022002', 'nama' => 'Siti Nurhaliza', 'angkatan' => 2022, 'semester_masuk' => '20222', 'semester_sekarang' => 6],

            // Mahasiswa yang masuk semester 20231 (Ganjil 2023/2024) - sekarang semester 5
            ['nim' => '2023001', 'nama' => 'Budi Santoso', 'angkatan' => 2023, 'semester_masuk' => '20231', 'semester_sekarang' => 5],

            // Mahasiswa yang masuk semester 20232 (Genap 2023/2024) - sekarang semester 4
            ['nim' => '2023002', 'nama' => 'Citra Dewi', 'angkatan' => 2023, 'semester_masuk' => '20232', 'semester_sekarang' => 4],

            // Mahasiswa yang masuk semester 20241 (Ganjil 2024/2025) - sekarang semester 3
            ['nim' => '2024001', 'nama' => 'Dedi Kurniawan', 'angkatan' => 2024, 'semester_masuk' => '20241', 'semester_sekarang' => 3],

            // Mahasiswa yang masuk semester 20242 (Genap 2024/2025) - sekarang semester 2
            ['nim' => '2024002', 'nama' => 'Eka Fitri', 'angkatan' => 2024, 'semester_masuk' => '20242', 'semester_sekarang' => 2],

            // Mahasiswa yang masuk semester 20251 (Ganjil 2025/2026) - sekarang semester 1
            ['nim' => '2025001', 'nama' => 'Fajar Hakim', 'angkatan' => 2025, 'semester_masuk' => '20251', 'semester_sekarang' => 1],
        ];

        foreach ($mahasiswaData as $index => $mhs) {
            // Buat user pengguna (nama disimpan di sini)
            $mhsUser = Pengguna::create([
                'id_pengguna' => Str::uuid(),
                'nama'        => $mhs['nama'], // Nama disimpan di tabel pengguna
                'username'    => $mhs['nim'],
                'email'       => "mhs{$mhs['nim']}@student.edubrain.ac.id",
                'password'    => Hash::make($mhs['nim']),
                'role'        => 'mahasiswa',
                'is_active'   => true,
            ]);

            $mhsUser->assignRole('mahasiswa');

            // Buat mahasiswa (tidak ada kolom nama, hanya relasi ke pengguna)
            $mahasiswa = Mahasiswa::create([
                'id_mahasiswa'     => Str::uuid(),
                'nim'              => $mhs['nim'],
                'jenis_kelamin'    => $index % 2 == 0 ? 'L' : 'P',
                'tempat_lahir'     => ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Medan'][$index % 5],
                'tanggal_lahir'    => Carbon::parse('2000-01-01')->addMonths($index * 3),
                'angkatan'         => $mhs['angkatan'],
                'id_program_studi' => $prodi->id_program_studi,
                'id_kurikulum'     => $this->createdKurikulum->id_kurikulum,
                'id_pengguna'      => $mhsUser->id_pengguna, // Relasi ke pengguna
                'status_mahasiswa' => 'AKTIF',
            ]);

            // Buat pembimbing akademik untuk semester aktif
            $dosenPA = $this->createdDosen[$index % count($this->createdDosen)];
            $pembimbingAkademik = DB::table('pembimbing_akademik')->insertGetId([
                'id_pembimbing_akademik' => Str::uuid(),
                'id_mahasiswa'           => $mahasiswa->id_mahasiswa,
                'id_dosen'               => $dosenPA->id_dosen,
                'id_semester'            => $this->createdSemester['20251']->id_semester,
                'status_pa'              => 'AKTIF',
                'nomor_sk'               => sprintf("SK/TI/PA/%03d/2025", $index + 1),
                'tanggal_sk'             => '2025-08-01',
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);

            // Buat data historis akademik
            $this->createDataHistorisAkademik($mahasiswa, $mhs['semester_masuk'], $mhs['semester_sekarang']);
        }
    }

    private function createDataHistorisAkademik($mahasiswa, $semesterMasuk, $semesterSekarang)
    {
        // Tentukan semester yang sudah diambil (historis)
        $semesterHistoris = $this->getSemesterHistoris($semesterMasuk, $semesterSekarang);

        $semesterAkademik = 1;
        foreach ($semesterHistoris as $kodeSemester) {
            $semester = $this->createdSemester[$kodeSemester];

            // Buat registrasi mahasiswa (KRS)
            $registrasiId = Str::uuid();
            $tanggalMulai = Carbon::parse($semester->tanggal_mulai);

            // Ambil pembimbing akademik
            $pembimbingAkademik = DB::table('pembimbing_akademik')
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->where('status_pa', 'AKTIF')
                ->first();

            DB::table('registrasi_mahasiswa')->insert([
                'id_registrasi_mahasiswa' => $registrasiId,
                'id_mahasiswa'            => $mahasiswa->id_mahasiswa,
                'id_semester'             => $semester->id_semester,
                'status_krs'              => 'APPROVED',
                'id_pembimbing_akademik'  => $pembimbingAkademik->id_pembimbing_akademik ?? null,
                'tanggal_submit'          => $tanggalMulai->copy()->subDays(10),
                'tanggal_approval'        => $tanggalMulai->copy()->subDays(5),
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);

            // Ambil mata kuliah untuk semester akademik ini
            $mataKuliah = $this->getMataKuliahBySemesterAkademik($semesterAkademik);

            foreach ($mataKuliah as $kodeMK) {
                // Untuk mata kuliah reguler (bukan bimbingan)
                if (!in_array($kodeMK, ['TIF701', 'TIF702', 'TIF801'])) {
                    $this->createPesertaKelasReguler($kodeMK, $registrasiId, $semester->id_semester, $mahasiswa);
                } else {
                    // Untuk mata kuliah bimbingan
                    $this->createPesertaBimbingan($kodeMK, $registrasiId, $mahasiswa, $semesterAkademik);
                }
            }

            $semesterAkademik++;
        }
    }

    private function getSemesterHistoris($semesterMasuk, $semesterSekarang)
    {
        $allSemesters = ['20221', '20222', '20231', '20232', '20241', '20242'];
        $startIndex = array_search($semesterMasuk, $allSemesters);

        $semesterHistoris = [];
        for ($i = 0; $i < $semesterSekarang - 1; $i++) {
            if (isset($allSemesters[$startIndex + $i])) {
                $semesterHistoris[] = $allSemesters[$startIndex + $i];
            }
        }

        return $semesterHistoris;
    }

    private function getMataKuliahBySemesterAkademik($semesterAkademik)
    {
        $semesterMK = [
            1 => ['TIF101', 'TIF102', 'TIF103', 'TIF104', 'TIF105', 'UNI101', 'UNI102', 'UNI103'],
            2 => ['TIF201', 'TIF202', 'TIF203', 'TIF204', 'TIF205', 'TIF206', 'TIF207', 'UNI201'],
            3 => ['TIF301', 'TIF302', 'TIF303', 'TIF304', 'TIF305', 'TIF306', 'TIF307', 'TIF308', 'UNI301'],
            4 => ['TIF401', 'TIF402', 'TIF403', 'TIF404', 'TIF405', 'TIF406', 'TIF407', 'TIF408'],
            5 => ['TIF501', 'TIF502', 'TIF503', 'TIF504', 'TIF505', 'TIF506', 'TIF507', 'UNI501'],
            6 => ['TIF601', 'TIF602', 'TIF603', 'TIF604', 'TIF605', 'TIF606', 'TIF607', 'UNI601'],
            7 => ['TIF701', 'TIF702', 'TIF703', 'TIF704', 'TIF705'],
            8 => ['TIF801', 'TIF802', 'TIF803', 'TIF804', 'TIF805'],
        ];

        return $semesterMK[$semesterAkademik] ?? [];
    }

    private function createPesertaKelasReguler($kodeMK, $registrasiId, $semesterId, $mahasiswa)
    {
        // Cari kelas kuliah untuk mata kuliah ini
        $kelasKuliah = DB::table('kelas_kuliah as kk')
            ->join('kurikulum_mata_kuliah as kmk', 'kk.id_kurikulum_mata_kuliah', '=', 'kmk.id')
            ->join('mata_kuliah as mk', 'kmk.id_mata_kuliah', '=', 'mk.id_mata_kuliah')
            ->where('mk.kode_mata_kuliah', $kodeMK)
            ->where('kk.id_semester', $semesterId)
            ->select('kk.id_kelas_kuliah', 'mk.id_mata_kuliah')
            ->first();

        if ($kelasKuliah) {
            $pesertaId = Str::uuid();

            // Insert peserta kelas kuliah
            DB::table('peserta_kelas_kuliah')->insert([
                'id_peserta'                => $pesertaId,
                'id_kelas_kuliah'           => $kelasKuliah->id_kelas_kuliah,
                'id_mata_kuliah'            => $kelasKuliah->id_mata_kuliah,
                'id_registrasi_mahasiswa'   => $registrasiId,
                'status_mata_kuliah'        => 'APPROVED',
                'created_at'                => now(),
                'updated_at'                => now(),
            ]);

            // Generate nilai random yang realistis
            $nilai = $this->generateNilaiRealistis();

            // Insert nilai
            DB::table('nilai_perkuliahan')->insert([
                'id_nilai_perkuliahan' => Str::uuid(),
                'jenis_peserta'        => 'KELAS',
                'id_peserta'           => $pesertaId,
                'id_peserta_bimbingan' => null,
                'nilai_angka'          => $nilai['angka'],
                'nilai_indeks'         => $nilai['indeks'],
                'nilai_huruf'          => $nilai['huruf'],
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }
    }

    private function createPesertaBimbingan($kodeMK, $registrasiId, $mahasiswa, $semesterAkademik)
    {
        $mataKuliahObj = $this->createdMataKuliah[$kodeMK] ?? null;
        if (!$mataKuliahObj) return;

        // Pilih dosen pembimbing
        $dosenPembimbing1 = $this->createdDosen[array_rand($this->createdDosen)];
        $dosenPembimbing2 = $this->createdDosen[array_rand($this->createdDosen)];

        $pesertaBimbinganId = Str::uuid();

        // Insert peserta bimbingan
        DB::table('peserta_bimbingan')->insert([
            'id_peserta_bimbingan'     => $pesertaBimbinganId,
            'id_mata_kuliah'           => $mataKuliahObj->id_mata_kuliah,
            'id_registrasi_mahasiswa'  => $registrasiId,
            'status_mata_kuliah'       => 'APPROVED',
            'id_dosen_pembimbing'      => $dosenPembimbing1->id_dosen,
            'id_dosen_pembimbing_2'    => $dosenPembimbing2->id_dosen,
            'created_at'               => now(),
            'updated_at'               => now(),
        ]);

        // Buat detail khusus berdasarkan jenis mata kuliah
        if ($kodeMK == 'TIF701') { // KKN
            $this->createKKNDetail($pesertaBimbinganId);
        } elseif ($kodeMK == 'TIF702') { // Kerja Praktik/Magang
            $this->createMagangDetail($pesertaBimbinganId);
        } elseif ($kodeMK == 'TIF801') { // Tugas Akhir/Skripsi
            $this->createSkripsiDetail($pesertaBimbinganId);
        }

        // Generate nilai untuk bimbingan
        $nilai = $this->generateNilaiRealistis();

        DB::table('nilai_perkuliahan')->insert([
            'id_nilai_perkuliahan' => Str::uuid(),
            'jenis_peserta'        => 'BIMBINGAN',
            'id_peserta'           => null,
            'id_peserta_bimbingan' => $pesertaBimbinganId,
            'nilai_angka'          => $nilai['angka'],
            'nilai_indeks'         => $nilai['indeks'],
            'nilai_huruf'          => $nilai['huruf'],
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);
    }

    private function createKKNDetail($pesertaBimbinganId)
    {
        // Cek apakah sudah ada kelompok KKN, jika belum buat baru
        $kelompokKKN = DB::table('kkn_kelompok')->first();

        if (!$kelompokKKN) {
            $dpl = $this->createdDosen[array_rand($this->createdDosen)];

            $kelompokKKNId = Str::uuid();
            DB::table('kkn_kelompok')->insert([
                'id_kelompok_kkn'     => $kelompokKKNId,
                'nama_kelompok'       => 'Kelompok KKN A',
                'lokasi'              => 'Desa Sukamaju, Kabupaten Bandung',
                'alamat_lokasi'       => 'Jl. Raya Sukamaju No. 123, Desa Sukamaju, Kec. Cileunyi, Kab. Bandung',
                'id_dpl'              => $dpl->id_dosen,
                'periode_mulai'       => '2024-07-01',
                'periode_selesai'     => '2024-08-31',
                'target_program_kerja' => 'Pemberdayaan masyarakat desa melalui teknologi informasi, pelatihan komputer dasar, dan digitalisasi UMKM lokal.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        } else {
            $kelompokKKNId = $kelompokKKN->id_kelompok_kkn;
        }

        // Insert KKN detail
        DB::table('kkn_detail')->insert([
            'id_kkn_detail'         => Str::uuid(),
            'id_peserta_bimbingan'  => $pesertaBimbinganId,
            'id_kelompok_kkn'       => $kelompokKKNId,
            'peran_kelompok'        => 'KETUA',
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);
    }

    private function createMagangDetail($pesertaBimbinganId)
    {
        $tempatMagang = [
            'PT. Teknologi Nusantara',
            'CV. Digital Solusi Indonesia',
            'PT. Inovasi Teknologi Maju',
            'Startup TechnoHub',
            'PT. Sistem Informasi Terpadu'
        ];

        DB::table('magang_detail')->insert([
            'id_magang_detail'      => Str::uuid(),
            'id_peserta_bimbingan'  => $pesertaBimbinganId,
            'tempat_magang'         => $tempatMagang[array_rand($tempatMagang)],
            'alamat_magang'         => 'Jl. Sudirman No. 45, Jakarta Selatan',
            'bidang_magang'         => 'Web Development',
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);
    }

    private function createSkripsiDetail($pesertaBimbinganId)
    {
        $judulSkripsi = [
            'Implementasi Machine Learning untuk Prediksi Penjualan E-Commerce',
            'Pengembangan Sistem Informasi Manajemen Perpustakaan Berbasis Web',
            'Analisis Sentimen Media Sosial Menggunakan Natural Language Processing',
            'Rancang Bangun Aplikasi Mobile untuk Monitoring Kesehatan',
            'Optimasi Algoritma Genetika untuk Vehicle Routing Problem'
        ];

        DB::table('skripsi_detail')->insert([
            'id_skripsi_detail'      => Str::uuid(),
            'id_peserta_bimbingan'   => $pesertaBimbinganId,
            'judul'                  => $judulSkripsi[array_rand($judulSkripsi)],
            'bidang_penelitian'      => 'Artificial Intelligence',
            'status_proposal'        => 'APPROVED',
            'tanggal_seminar_proposal' => '2024-10-15',
            'tanggal_sidang_skripsi' => '2025-01-20',
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);
    }

    private function generateNilaiRealistis()
    {
        $nilaiAngka = rand(65, 95) + (rand(0, 99) / 100); // 65.00 - 95.99

        if ($nilaiAngka >= 85) {
            return ['angka' => $nilaiAngka, 'indeks' => 4.0, 'huruf' => 'A'];
        } elseif ($nilaiAngka >= 80) {
            return ['angka' => $nilaiAngka, 'indeks' => 3.5, 'huruf' => 'B+'];
        } elseif ($nilaiAngka >= 75) {
            return ['angka' => $nilaiAngka, 'indeks' => 3.0, 'huruf' => 'B'];
        } elseif ($nilaiAngka >= 70) {
            return ['angka' => $nilaiAngka, 'indeks' => 2.5, 'huruf' => 'C+'];
        } elseif ($nilaiAngka >= 65) {
            return ['angka' => $nilaiAngka, 'indeks' => 2.0, 'huruf' => 'C'];
        } else {
            return ['angka' => $nilaiAngka, 'indeks' => 1.0, 'huruf' => 'D'];
        }
    }

    private function createPengumumanDanKalender()
    {
        $pengumuman = [
            [
                'judul' => 'Periode Registrasi Semester Ganjil 2025/2026',
                'deskripsi' => 'Periode registrasi dan pengisian KRS untuk semester ganjil 2025/2026 dibuka mulai tanggal 1-15 September 2025. Mahasiswa wajib melakukan registrasi sebelum batas waktu yang ditentukan.',
                'tujuan' => 'mahasiswa',
            ],
            [
                'judul' => 'Seminar Proposal Tugas Akhir Periode Oktober 2025',
                'deskripsi' => 'Pendaftaran seminar proposal tugas akhir untuk periode Oktober 2025 telah dibuka. Mahasiswa yang akan mengambil mata kuliah tugas akhir dapat mendaftar melalui sistem akademik.',
                'tujuan' => 'mahasiswa',
            ],
            [
                'judul' => 'Workshop Machine Learning dan AI',
                'deskripsi' => 'Program Studi Teknik Informatika menyelenggarakan workshop tentang Machine Learning dan Artificial Intelligence pada tanggal 20-22 Oktober 2025. Terbuka untuk mahasiswa dan dosen.',
                'tujuan' => 'umum',
            ],
            [
                'judul' => 'Beasiswa Prestasi Akademik Tahun 2025',
                'deskripsi' => 'Tersedia beasiswa prestasi akademik untuk mahasiswa berprestasi dengan IPK minimal 3.50. Kuota terbatas untuk 20 mahasiswa. Pendaftaran hingga 30 Oktober 2025.',
                'tujuan' => 'mahasiswa',
            ],
        ];

        foreach ($pengumuman as $item) {
            Pengumuman::create([
                'id' => Str::uuid(),
                'judul' => $item['judul'],
                'deskripsi' => $item['deskripsi'],
                'tujuan' => $item['tujuan'],
            ]);
        }

        $kalender = [
            [
                'judul' => 'Awal Perkuliahan Semester Ganjil 2025/2026',
                'tanggal_mulai' => '2025-09-01',
                'tanggal_selesai' => '2025-09-01',
                'is_all_day' => true,
            ],
            [
                'judul' => 'Periode Registrasi dan KRS',
                'tanggal_mulai' => '2025-09-01',
                'tanggal_selesai' => '2025-09-15',
                'is_all_day' => true,
            ],
            [
                'judul' => 'Workshop Machine Learning dan AI',
                'tanggal_mulai' => '2025-10-20',
                'tanggal_selesai' => '2025-10-22',
                'is_all_day' => true,
            ],
            [
                'judul' => 'Ujian Tengah Semester (UTS)',
                'tanggal_mulai' => '2025-11-10',
                'tanggal_selesai' => '2025-11-20',
                'is_all_day' => true,
            ],
            [
                'judul' => 'Ujian Akhir Semester (UAS)',
                'tanggal_mulai' => '2025-12-15',
                'tanggal_selesai' => '2025-12-25',
                'is_all_day' => true,
            ],
        ];

        foreach ($kalender as $item) {
            KalenderAkademik::create([
                'id' => Str::uuid(),
                'judul' => $item['judul'],
                'tanggal_mulai' => $item['tanggal_mulai'],
                'tanggal_selesai' => $item['tanggal_selesai'],
                'is_all_day' => $item['is_all_day'],
            ]);
        }
    }

    private function logResults()
    {
        $totalMahasiswa = Mahasiswa::count();
        $totalDosen = count($this->createdDosen);
        $totalKelas = DB::table('kelas_kuliah')->count();
        $totalRegistrasi = DB::table('registrasi_mahasiswa')->count();
        $totalPesertaKelas = DB::table('peserta_kelas_kuliah')->count();
        $totalPesertaBimbingan = DB::table('peserta_bimbingan')->count();
        $totalNilai = DB::table('nilai_perkuliahan')->count();
        $totalSemester = count($this->createdSemester);

        $this->command->info('=== SIAKAD TEKNIK INFORMATIKA BERHASIL DIBUAT ===');
        $this->command->info("Periode: 2022-2025 (Semester 20221 s/d 20251)");
        $this->command->info("Semester Aktif: 20251 (Ganjil 2025/2026)");
        $this->command->info("Kurikulum: 2022 (144 SKS, 8 Semester)");
        $this->command->info("Total Semester: {$totalSemester}");
        $this->command->info("Total Mahasiswa: {$totalMahasiswa} (dengan data historis lengkap)");
        $this->command->info("Total Dosen: {$totalDosen}");
        $this->command->info("Total Kelas Kuliah: {$totalKelas} (historis 2022-2024)");
        $this->command->info("Total Registrasi Mahasiswa: {$totalRegistrasi}");
        $this->command->info("Total Peserta Kelas: {$totalPesertaKelas}");
        $this->command->info("Total Peserta Bimbingan: {$totalPesertaBimbingan}");
        $this->command->info("Total Nilai: {$totalNilai}");
        $this->command->info("Data historis akademik lengkap untuk setiap mahasiswa!");

        // Detail per mahasiswa
        $mahasiswaDetail = DB::table('mahasiswa as m')
            ->join('pengguna as p', 'm.id_pengguna', '=', 'p.id_pengguna')
            ->select('m.id_mahasiswa', 'm.nim', 'p.nama', 'm.angkatan')
            ->get();

        foreach ($mahasiswaDetail as $mhs) {
            $jumlahNilai = DB::table('nilai_perkuliahan as np')
                ->join('peserta_kelas_kuliah as pkk', function ($join) {
                    $join->on('np.id_peserta', '=', 'pkk.id_peserta')
                        ->where('np.jenis_peserta', '=', 'KELAS');
                })
                ->join('registrasi_mahasiswa as rm', 'pkk.id_registrasi_mahasiswa', '=', 'rm.id_registrasi_mahasiswa')
                ->where('rm.id_mahasiswa', $mhs->id_mahasiswa)
                ->count();

            $jumlahBimbingan = DB::table('nilai_perkuliahan as np')
                ->join('peserta_bimbingan as pb', function ($join) {
                    $join->on('np.id_peserta_bimbingan', '=', 'pb.id_peserta_bimbingan')
                        ->where('np.jenis_peserta', '=', 'BIMBINGAN');
                })
                ->join('registrasi_mahasiswa as rm', 'pb.id_registrasi_mahasiswa', '=', 'rm.id_registrasi_mahasiswa')
                ->where('rm.id_mahasiswa', $mhs->id_mahasiswa)
                ->count();

            $this->command->info("- {$mhs->nim} ({$mhs->nama}): {$jumlahNilai} nilai reguler, {$jumlahBimbingan} nilai bimbingan");
        }
    }
}