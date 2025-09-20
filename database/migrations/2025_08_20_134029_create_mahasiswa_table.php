<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->uuid('id_mahasiswa')->primary();

            // Biodata Mahasiswa
            $table->string('nim', 20)->unique();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->year('angkatan');
            $table->string('nik', 16)->nullable()->unique();
            $table->string('nisn', 20)->nullable();
            $table->string('npwp', 20)->nullable();
            $table->string('agama', 50)->nullable();
            $table->string('kode_negara', 3)->default('ID');
            $table->string('kewarganegaraan', 50)->default('Indonesia');
            $table->string('jalan', 255)->nullable();
            $table->string('dusun', 100)->nullable();
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();

            // Status Mahasiswa (NEO Feeder Standard)
            $table->enum('status_mahasiswa', [
                'AKTIF',  // Aktif
                'CUTI',  // Cuti
                'DO',  // Drop Out/Putus Studi
                'KELUAR',  // Keluar
                'LULUS',  // Lulus
                'NONAKTIF',  // Non Aktif
            ])->default('AKTIF');

            // Relasi
            $table->uuid('id_program_studi')->index();
            $table->foreign('id_program_studi')->references('id_program_studi')->on('program_studi')->onDelete('cascade');
            $table->uuid('id_kurikulum')->nullable()->index();
            $table->foreign('id_kurikulum')->references('id_kurikulum')->on('kurikulum')->onDelete('cascade');
            $table->uuid('id_pengguna')->index();
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');

            // Biodata Ayah
            $table->string('nik_ayah', 16)->nullable();
            $table->string('nama_ayah', 150)->nullable();
            $table->string('tempat_lahir_ayah', 100)->nullable();
            $table->date('tanggal_lahir_ayah')->nullable();
            $table->string('nama_pendidikan_ayah', 100)->nullable();
            $table->string('nama_pekerjaan_ayah', 100)->nullable();
            $table->string('nama_penghasilan_ayah', 100)->nullable();

            // Biodata Ibu
            $table->string('nik_ibu', 16)->nullable();
            $table->string('nama_ibu', 150)->nullable();
            $table->string('tempat_lahir_ibu', 100)->nullable();
            $table->date('tanggal_lahir_ibu')->nullable();
            $table->string('nama_pendidikan_ibu', 100)->nullable();
            $table->string('nama_pekerjaan_ibu', 100)->nullable();
            $table->string('nama_penghasilan_ibu', 100)->nullable();

            // Biodata Wali
            $table->string('nama_wali', 150)->nullable();
            $table->string('tempat_lahir_wali', 100)->nullable();
            $table->date('tanggal_lahir_wali')->nullable();
            $table->string('nama_pendidikan_wali', 100)->nullable();
            $table->string('nama_pekerjaan_wali', 100)->nullable();
            $table->string('nama_penghasilan_wali', 100)->nullable();

            $table->timestamps();

            // Index untuk performa
            $table->index('status_mahasiswa');
            $table->index(['id_program_studi', 'angkatan']); // untuk filter program studi + angkatan
            $table->index('angkatan'); // untuk filter angkatan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
