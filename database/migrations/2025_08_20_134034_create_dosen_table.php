<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->uuid('id_dosen')->primary();

            $table->string('foto')->nullable();

            // Biodata Dosen
            $table->string('nidn', 20)->unique();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('gelar_depan', 50)->nullable();
            $table->string('gelar_belakang', 50)->nullable();
            $table->string('nik', 16)->nullable()->unique();
            $table->string('npwp', 20)->nullable();
            $table->string('jalan', 255)->nullable();
            $table->string('dusun', 100)->nullable();
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();

            // Status Dosen (NEO Feeder Standard)
            $table->enum('status_dosen', [
                'AKTIF',  // Aktif
                'CUTI',  // Cuti
                'KELUAR',  // Keluar
                'NONAKTIF',  // Non Aktif
                'PENSIUN',  // Pensiun
            ])->default('AKTIF');

            // Status Kepegawaian
            $table->enum('status_kepegawaian', [
                'PNS',      // Pegawai Negeri Sipil
                'CPNS',     // Calon Pegawai Negeri Sipil
                'P3K',      // Pegawai Pemerintah dengan Perjanjian Kerja
                'TETAP',    // Dosen Tetap Non-PNS
                'KONTRAK',  // Dosen Kontrak
                'HONORER'   // Dosen Honorer
            ])->default('TETAP');

            $table->integer('total_kuota_pa')->nullable();

            // Relasi
            $table->uuid('id_program_studi')->nullable();
            $table->foreign('id_program_studi')->references('id_program_studi')->on('program_studi')->onDelete('cascade');
            $table->uuid('id_pengguna')->nullable();
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');

            $table->timestamps();

            // Index untuk performa
            $table->index(['status_dosen', 'status_kepegawaian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
