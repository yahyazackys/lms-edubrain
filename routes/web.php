<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KalenderAkademikController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // KHS & Transcript Routes
    Route::prefix('akademik')->name('akademik.')->group(function () {
        // Dashboard akademik
        Route::get('/dashboard', [\App\Http\Controllers\KhsTranscriptController::class, 'dashboardAkademik'])
            ->name('dashboard');

        // KHS Routes
        Route::prefix('khs')->name('khs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\KhsTranscriptController::class, 'indexKhs'])
                ->name('index');
            Route::get('/show', [\App\Http\Controllers\KhsTranscriptController::class, 'showKhs'])
                ->name('show');
            Route::get('/download-pdf', [\App\Http\Controllers\KhsTranscriptController::class, 'downloadKhsPdf'])
                ->name('download-pdf');
        });

        // Transcript Routes  
        Route::prefix('transcript')->name('transcript.')->group(function () {
            Route::get('/', [\App\Http\Controllers\KhsTranscriptController::class, 'indexTranscript'])
                ->name('index');
            Route::get('/download-pdf', [\App\Http\Controllers\KhsTranscriptController::class, 'downloadTranscriptPdf'])
                ->name('download-pdf');
        });
    });

    // Daftar Kalender Akademik
    Route::get('kalender-akademik/events', [KalenderAkademikController::class, 'getEvents'])->name('kalender-akademik.events');
    Route::get('kalender-akademik', [KalenderAkademikController::class, 'index'])->name('kalender-akademik.index');

    // Daftar pengumuman
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    Route::get('/pengumuman/{pengumuman}/download', [PengumumanController::class, 'streamFile'])->name('pengumuman.download');

    // Admin
    Route::middleware('role:admin')->group(function () {
        // Semester
        Route::resource('semester', \App\Http\Controllers\Admin\SemesterController::class);
        Route::patch('/semester/{id}/activate', [\App\Http\Controllers\Admin\SemesterController::class, 'activate'])->name('semester.activate');
        Route::patch('/semester/{id}/deactivate', [\App\Http\Controllers\Admin\SemesterController::class, 'deactivate'])->name('semester.deactivate');

        // Jenjang Pendidikan
        Route::prefix('jenjang')->name('jenjang.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\JenjangPendidikanController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\JenjangPendidikanController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\JenjangPendidikanController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\JenjangPendidikanController::class, 'destroy'])->name('destroy');

            // Route untuk kelola program studi di jenjang tertentu
            Route::get('/{id}/program-studi', [\App\Http\Controllers\Admin\ProgramStudiController::class, 'indexByJenjang'])->name('program-studi.index');
        });

        // Program Studi
        Route::prefix('program-studi')->name('program-studi.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ProgramStudiController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\ProgramStudiController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\ProgramStudiController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\ProgramStudiController::class, 'destroy'])->name('destroy');

            // API endpoints
            Route::get('/api/jenjang', [\App\Http\Controllers\Admin\ProgramStudiController::class, 'getJenjang'])->name('api.jenjang');
            Route::get('/statistik/{jenjangId}', [\App\Http\Controllers\Admin\ProgramStudiController::class, 'statistik'])->name('statistik');
        });

        // Mata Kuliah
        Route::prefix('mata-kuliah')->name('mata-kuliah.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\MataKuliahController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\MataKuliahController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\MataKuliahController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\MataKuliahController::class, 'destroy'])->name('destroy');

            // API Routes untuk AJAX
            Route::get('/api/mata-kuliah', [\App\Http\Controllers\Admin\MataKuliahController::class, 'getMataKuliah'])->name('api.mata-kuliah');
            Route::get('/api/statistik', [\App\Http\Controllers\Admin\MataKuliahController::class, 'statistik'])->name('api.statistik');
        });

        // Kurikulum
        Route::prefix('kurikulum')->name('kurikulum.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\KurikulumController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\KurikulumController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\KurikulumController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\KurikulumController::class, 'destroy'])->name('destroy');

            // API endpoints
            Route::get('/api/program-studi', [\App\Http\Controllers\Admin\KurikulumController::class, 'getProgramStudi'])->name('api.program-studi');
            Route::get('/statistik/{semesterId}', [\App\Http\Controllers\Admin\KurikulumController::class, 'statistik'])->name('statistik');
        });

        // Kurikulum Mata Kuliah Management
        Route::prefix('kurikulum/{kurikulum}')->name('kurikulum.')->group(function () {
            Route::prefix('mata-kuliah')->name('mata-kuliah.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\KurikulumMataKuliahController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\Admin\KurikulumMataKuliahController::class, 'store'])->name('store');
                Route::put('/{mataKuliah}', [\App\Http\Controllers\Admin\KurikulumMataKuliahController::class, 'update'])->name('update');
                Route::delete('/{mataKuliah}', [\App\Http\Controllers\Admin\KurikulumMataKuliahController::class, 'destroy'])->name('destroy');

                // Bulk operations
                Route::post('/bulk-update', [\App\Http\Controllers\Admin\KurikulumMataKuliahController::class, 'bulkUpdate'])->name('bulk-update');
                Route::post('/copy', [\App\Http\Controllers\Admin\KurikulumMataKuliahController::class, 'copy'])->name('copy');

                // API
                Route::get('/api/mata-kuliah', [\App\Http\Controllers\Admin\KurikulumMataKuliahController::class, 'getMataKuliah'])->name('api.mata-kuliah');
            });
        });

        // Mahasiswa
        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
            // Main CRUD Routes
            Route::get('/', [\App\Http\Controllers\Admin\MahasiswaController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\MahasiswaController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Admin\MahasiswaController::class, 'show'])->name('show');
            Route::put('/{id}', [\App\Http\Controllers\Admin\MahasiswaController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\MahasiswaController::class, 'destroy'])->name('destroy');

            // Detail Update Route (untuk update lengkap dari halaman show)
            Route::put('/{id}/detail', [\App\Http\Controllers\Admin\MahasiswaController::class, 'updateDetail'])->name('update-detail');

            // Reset Password Route
            Route::post('/{id}/reset-password', [\App\Http\Controllers\Admin\MahasiswaController::class, 'resetPassword'])->name('reset-password');

            // Excel Import/Export Routes
            Route::get('/template/export', [\App\Http\Controllers\Admin\MahasiswaController::class, 'exportTemplate'])->name('export-template');
            Route::post('/import', [\App\Http\Controllers\Admin\MahasiswaController::class, 'import'])->name('import');

            // API Routes untuk AJAX calls
            Route::prefix('api')->name('api.')->group(function () {
                Route::get('/program-studi', [\App\Http\Controllers\Admin\MahasiswaController::class, 'getProgramStudi'])->name('program-studi');
                Route::get('/kurikulum', [\App\Http\Controllers\Admin\MahasiswaController::class, 'getKurikulumByProdi'])->name('kurikulum');
            });
        });

        // Dosen 
        Route::prefix('dosen')->name('dosen.')->group(function () {
            // Main CRUD Routes
            Route::get('/', [\App\Http\Controllers\Admin\DosenController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\DosenController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Admin\DosenController::class, 'show'])->name('show');
            Route::put('/{id}', [\App\Http\Controllers\Admin\DosenController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\DosenController::class, 'destroy'])->name('destroy');

            // Update detail lengkap dosen (dari halaman detail)
            Route::put('/{dosen}/update-detail', [\App\Http\Controllers\Admin\DosenController::class, 'updateDetail'])->name('update-detail');
            // Reset password dosen
            Route::post('/{dosen}/reset-password', [\App\Http\Controllers\Admin\DosenController::class, 'resetPassword'])->name('reset-password');
            // Upload foto dosen
            Route::post('/{dosen}/upload-photo', [\App\Http\Controllers\Admin\DosenController::class, 'uploadPhoto'])->name('upload-photo');
            // Export template Excel
            Route::get('/export-template', [\App\Http\Controllers\Admin\DosenController::class, 'exportTemplate'])->name('export-template');
            // Import dosen dari Excel
            Route::post('/import', [\App\Http\Controllers\Admin\DosenController::class, 'import'])->name('import');
            // API Routes untuk AJAX
            Route::prefix('api')->name('api.')->group(function () {
                // Get program studi untuk dropdown
                Route::get('/program-studi', [\App\Http\Controllers\Admin\DosenController::class, 'getProgramStudi'])->name('program-studi');
            });
        });

        // Kelas Kuliah
        Route::prefix('kelas-kuliah')->name('kelas-kuliah.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\KelasKuliahController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\KelasKuliahController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\KelasKuliahController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\KelasKuliahController::class, 'destroy'])->name('destroy');

            // API routes untuk search functionality (step-by-step)
            Route::prefix('api')->name('api.')->group(function () {
                Route::get('list', [\App\Http\Controllers\Admin\KelasKuliahController::class, 'getKelasKuliah'])->name('list');
                Route::get('program-studi', [\App\Http\Controllers\Admin\KelasKuliahController::class, 'getProgramStudi'])->name('program-studi');
                Route::get('kurikulum', [\App\Http\Controllers\Admin\KelasKuliahController::class, 'getKurikulum'])->name('kurikulum');
                Route::get('kurikulum-mata-kuliah', [\App\Http\Controllers\Admin\KelasKuliahController::class, 'getKurikulumMataKuliah'])->name('kurikulum-mata-kuliah');
                Route::get('dosen', [\App\Http\Controllers\Admin\KelasKuliahController::class, 'getDosen'])->name('dosen');
                Route::get('statistik/{semesterId?}', [\App\Http\Controllers\Admin\KelasKuliahController::class, 'statistik'])->name('statistik');

                Route::get('kurikulum-mata-kuliah/{id}', [\App\Http\Controllers\Admin\KelasKuliahController::class, 'getKurikulumMataKuliahDetail'])->name('kurikulum-mata-kuliah-detail');
                Route::get('dosen/{id}', [\App\Http\Controllers\Admin\KelasKuliahController::class, 'getDosenDetail'])->name('dosen-detail');
            });
        });

        // Pembimbing Akademik
        Route::prefix('pembimbing-akademik')->name('pembimbing-akademik.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PembimbingAkademikController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\PembimbingAkademikController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Admin\PembimbingAkademikController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\PembimbingAkademikController::class, 'destroy'])->name('destroy');

            // API routes
            Route::prefix('api')->name('api.')->group(function () {
                Route::get('dosen-kuota', [\App\Http\Controllers\Admin\PembimbingAkademikController::class, 'getDosenWithKuota'])->name('dosen-kuota');
            });
        });

        Route::prefix('admin/krs')->name('admin.krs.')->group(function () {
            // Dashboard Monitoring KRS
            Route::get('/', [\App\Http\Controllers\Admin\KrsAdminController::class, 'index'])->name('index');

            // Laporan Detail KRS
            Route::get('/laporan', [\App\Http\Controllers\Admin\KrsAdminController::class, 'laporan'])->name('laporan');

            // Detail KRS Mahasiswa untuk Admin
            Route::get('/detail/{registrasiId}', [\App\Http\Controllers\Admin\KrsAdminController::class, 'detailMahasiswa'])->name('detail');

            // Statistik Kelas Kuliah
            Route::get('/statistik-kelas', [\App\Http\Controllers\Admin\KrsAdminController::class, 'statistikKelas'])->name('statistik-kelas');

            // Aktivasi Mass KRS
            Route::post('/aktivasi-mass', [\App\Http\Controllers\Admin\KrsAdminController::class, 'aktivasiMassKrs'])->name('aktivasi-mass');

            // Export Laporan
            Route::get('/export', [\App\Http\Controllers\Admin\KrsAdminController::class, 'export'])->name('export');
        });

        // Kalender Akademik
        Route::prefix('kalender-akademik')
            ->name('kalender-akademik.')
            ->controller(KalenderAkademikController::class)
            ->group(function () {
                Route::get('create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('{kalenderAkademik}/edit', 'edit')->name('edit');
                Route::put('{kalenderAkademik}', 'update')->name('update');
                Route::delete('{kalenderAkademik}', 'destroy')->name('destroy');
            });

        // Pengumuman
        Route::prefix('pengumuman')
            ->name('pengumuman.')
            ->controller(PengumumanController::class)
            ->group(function () {
                Route::post('/', 'store')->name('store');
                Route::put('{pengumuman}', 'update')->name('update');
                Route::delete('{pengumuman}', 'destroy')->name('destroy');
            });
    });

    Route::middleware(['role:mahasiswa'])->group(function () {
        Route::prefix('krs')->name('krs.')->group(function () {
            // Dashboard KRS Mahasiswa
            Route::get('/', [\App\Http\Controllers\KrsController::class, 'index'])->name('index');

            // Pilih Mata Kuliah
            Route::get('/{semester}/pilih-mata-kuliah', [\App\Http\Controllers\KrsController::class, 'pilihMataKuliah'])->name('pilih-mata-kuliah');

            // Tambah/Hapus Mata Kuliah (AJAX)
            Route::post('/add-mata-kuliah', [\App\Http\Controllers\KrsController::class, 'addMataKuliah'])->name('add-mata-kuliah');
            Route::post('/remove-mata-kuliah', [\App\Http\Controllers\KrsController::class, 'removeMataKuliah'])->name('remove-mata-kuliah');

            // Review dan Submit KRS
            Route::get('/{semester}/review', [\App\Http\Controllers\KrsController::class, 'reviewKrs'])->name('review');
            Route::post('/submit', [\App\Http\Controllers\KrsController::class, 'submitKrs'])->name('submit');
        });

        Route::prefix('jadwal-kuliah')->name('jadwal-kuliah.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Mahasiswa\JadwalKuliahController::class, 'index'])->name('index');
            Route::get('/print', [\App\Http\Controllers\Mahasiswa\JadwalKuliahController::class, 'printJadwal'])->name('print');
        });

        Route::prefix('absensi')->name('absensi.')->group(function () {
            Route::get('/scan/{sesi}', [App\Http\Controllers\Mahasiswa\AbsensiController::class, 'scanQrCode'])->name('scan');
            Route::post('/submit', [App\Http\Controllers\Mahasiswa\AbsensiController::class, 'submitAbsensi'])->name('submit');
            Route::get('/riwayat', [App\Http\Controllers\Mahasiswa\AbsensiController::class, 'riwayatAbsensi'])->name('riwayat');
        });

        Route::prefix('detail-kelas')->name('detail-kelas.')->group(function () {
            // Detail kelas mahasiswa
            Route::get('/{kelasId}', [\App\Http\Controllers\Mahasiswa\DetailKelasController::class, 'show'])->name('show');

            // Download materi
            Route::get('/download-materi/{materiId}', [\App\Http\Controllers\Mahasiswa\DetailKelasController::class, 'downloadMateri'])->name('download-materi');

            // Download soal tugas/uts/uas
            Route::get('/download-tugas/{tugasId}', [\App\Http\Controllers\Mahasiswa\DetailKelasController::class, 'downloadTugas'])->name('download-tugas');
            Route::get('/download-uts/{utsId}', [\App\Http\Controllers\Mahasiswa\DetailKelasController::class, 'downloadUts'])->name('download-uts');
            Route::get('/download-uas/{uasId}', [\App\Http\Controllers\Mahasiswa\DetailKelasController::class, 'downloadUas'])->name('download-uas');

            // Submit pengumpulan
            Route::post('/submit-tugas/{tugasId}', [\App\Http\Controllers\Mahasiswa\DetailKelasController::class, 'submitTugas'])->name('submit-tugas');
            Route::post('/submit-uts/{utsId}', [\App\Http\Controllers\Mahasiswa\DetailKelasController::class, 'submitUts'])->name('submit-uts');
            Route::post('/submit-uas/{uasId}', [\App\Http\Controllers\Mahasiswa\DetailKelasController::class, 'submitUas'])->name('submit-uas');
        });
    });

    Route::middleware(['role:dosen'])->group(function () {
        Route::prefix('krs/approval')->name('krs.approval.')->group(function () {
            // Dashboard PA untuk Approval KRS
            Route::get('/', [\App\Http\Controllers\KrsApprovalController::class, 'index'])->name('index');

            // Review Detail KRS Mahasiswa
            Route::get('/review/{registrasiId}', [\App\Http\Controllers\KrsApprovalController::class, 'review'])->name('review');

            // Approve/Reject Mata Kuliah Individual (AJAX)
            Route::post('/update-mata-kuliah', [\App\Http\Controllers\KrsApprovalController::class, 'updateMataKuliah'])->name('update-mata-kuliah');

            // Approve/Reject Seluruh KRS
            Route::post('/approve/{registrasiId}', [\App\Http\Controllers\KrsApprovalController::class, 'approveKrs'])->name('approve');
            Route::post('/reject/{registrasiId}', [\App\Http\Controllers\KrsApprovalController::class, 'rejectKrs'])->name('reject');

            // Get Kelas Alternatif (AJAX)
            Route::get('/kelas-alternatif', [\App\Http\Controllers\KrsApprovalController::class, 'getKelasAlternatif'])->name('kelas-alternatif');
        });

        Route::prefix('jadwal-mengajar')->name('jadwal-mengajar.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Dosen\JadwalMengajarController::class, 'index'])->name('index');
            Route::get('/detail/{kelasId}', [\App\Http\Controllers\Dosen\JadwalMengajarController::class, 'detailKelas'])->name('detail-kelas');
            Route::get('/print', [\App\Http\Controllers\Dosen\JadwalMengajarController::class, 'printJadwal'])->name('print');
        });

        Route::prefix('kelas')->name('kelas.')->group(function () {
            Route::post('/{kelasId}/update-bobot', [\App\Http\Controllers\Dosen\JadwalMengajarController::class, 'updateBobotPenilaian'])->name('update-bobot');
            Route::post('/{kelasId}/akhiri', [\App\Http\Controllers\Dosen\JadwalMengajarController::class, 'akhiriKelas'])->name('akhiri');

            // Grade Calculation Routes
            Route::get('/{kelasId}/preview-grades', [\App\Http\Controllers\Dosen\JadwalMengajarController::class, 'previewGrades'])->name('preview-grades');
            Route::get('/{kelasId}/peserta/{pesertaId}/nilai', [\App\Http\Controllers\Dosen\JadwalMengajarController::class, 'detailNilaiMahasiswa'])->name('detail-nilai');
            Route::post('/{kelasId}/update-bobot', [\App\Http\Controllers\Dosen\JadwalMengajarController::class, 'updateBobotPenilaian'])->name('update-bobot');
        });

        Route::prefix('materi')->name('materi.')->group(function () {
            Route::post('/store', [\App\Http\Controllers\Dosen\MateriController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Dosen\MateriController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Dosen\MateriController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/download', [\App\Http\Controllers\Dosen\MateriController::class, 'download'])->name('download');
        });

        Route::prefix('tugas')->name('tugas.')->group(function () {
            Route::post('/store', [\App\Http\Controllers\Dosen\TugasController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Dosen\TugasController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Dosen\TugasController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/download', [\App\Http\Controllers\Dosen\TugasController::class, 'download'])->name('download');
            Route::get('/{id}/status', [\App\Http\Controllers\Dosen\TugasController::class, 'status'])->name('status');
            Route::post('/pengumpulan/{id}/grade', [\App\Http\Controllers\Dosen\TugasController::class, 'grade'])->name('pengumpulan.grade');
        });

        Route::prefix('uts')->name('uts.')->group(function () {
            Route::post('/store', [\App\Http\Controllers\Dosen\UtsController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Dosen\UtsController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Dosen\UtsController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/download', [\App\Http\Controllers\Dosen\UtsController::class, 'download'])->name('download');
            // UTS - Detail Daftar Pengumpulan UTS
            Route::get('/{id}/status', [\App\Http\Controllers\Dosen\UtsController::class, 'status'])->name('status');
            // UTS - Dosen Memberi Nilai
            Route::post('/pengumpulan/{id}/grade', [\App\Http\Controllers\Dosen\UtsController::class, 'gradeUts'])->name('pengumpulan.grade');
        });

        Route::prefix('uas')->name('uas.')->group(function () {
            Route::post('/store', [\App\Http\Controllers\Dosen\UasController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Dosen\UasController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Dosen\UasController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/download', [\App\Http\Controllers\Dosen\UasController::class, 'download'])->name('download');
            // UAS - Detail Daftar Pengumpulan UAS
            Route::get('/{id}/status', [\App\Http\Controllers\Dosen\UasController::class, 'status'])->name('status');
            // UAS - Dosen Memberi Nilai
            Route::post('/pengumpulan/{id}/grade', [\App\Http\Controllers\Dosen\UasController::class, 'gradeUas'])->name('pengumpulan.grade');
        });

        Route::prefix('sesi-absensi')->name('sesi-absensi.')->group(function () {
            Route::post('/', [\App\Http\Controllers\Dosen\SesiAbsensiController::class, 'store'])->name('store');
            Route::put('/{id}', [\App\Http\Controllers\Dosen\SesiAbsensiController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Dosen\SesiAbsensiController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-status', [\App\Http\Controllers\Dosen\SesiAbsensiController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{id}/qr-code', [\App\Http\Controllers\Dosen\SesiAbsensiController::class, 'generateQRCode'])->name('qr-code');
            Route::get('/{id}/status', [\App\Http\Controllers\Dosen\SesiAbsensiController::class, 'getStatusInfo'])->name('status');
            Route::get('/{id}/export', [\App\Http\Controllers\Dosen\SesiAbsensiController::class, 'exportAbsensi'])->name('export');
            Route::post('/auto-close-expired', [\App\Http\Controllers\Dosen\SesiAbsensiController::class, 'autoCloseExpiredSessions'])->name('auto-close');
        });
    });

    Route::middleware(['role:dosen|mahasiswa'])->group(function () {
        Route::prefix('detail-kelas')->name('detail-kelas.')->group(function () {
            // Download jawaban tugas/uts/uas
            Route::get('/pengumpulan-tugas/{pengumpulanId}/download', [\App\Http\Controllers\Mahasiswa\DetailKelasController::class, 'downloadJawabanTugas'])->name('download-pengumpulan-tugas');
            Route::get('/pengumpulan-uts/{pengumpulanId}/download', [\App\Http\Controllers\Mahasiswa\DetailKelasController::class, 'downloadJawabanUts'])->name('download-pengumpulan-uts');
            Route::get('/pengumpulan-uas/{pengumpulanId}/download', [\App\Http\Controllers\Mahasiswa\DetailKelasController::class, 'downloadJawabanUas'])->name('download-pengumpulan-uas');
        });
    });

    // Routes untuk admin/dosen
    Route::middleware('role:admin|dosen')->prefix('admin/akademik')->name('admin.akademik.')->group(function () {
        Route::get('/cari-mahasiswa', [\App\Http\Controllers\KhsTranscriptController::class, 'cariMahasiswa'])
            ->name('cari-mahasiswa');

        // Akses data mahasiswa lain
        Route::get('/khs/mahasiswa', [\App\Http\Controllers\KhsTranscriptController::class, 'indexKhs'])
            ->name('khs.mahasiswa.index');
        Route::get('/khs/mahasiswa/show', [\App\Http\Controllers\KhsTranscriptController::class, 'showKhs'])
            ->name('khs.mahasiswa.show');
        Route::get('/transcript/mahasiswa', [\App\Http\Controllers\KhsTranscriptController::class, 'indexTranscript'])
            ->name('transcript.mahasiswa.index');
        Route::get('/dashboard/mahasiswa', [\App\Http\Controllers\KhsTranscriptController::class, 'dashboardAkademik'])
            ->name('dashboard.mahasiswa');
    });
});

require __DIR__ . '/auth.php';
