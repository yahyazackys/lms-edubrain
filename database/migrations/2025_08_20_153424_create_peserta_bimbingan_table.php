<?php
// database/migrations/2024_01_15_000002_create_peserta_bimbingan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_bimbingan', function (Blueprint $table) {
            $table->uuid('id_peserta_bimbingan')->primary();

            // Relasi utama
            $table->uuid('id_mata_kuliah');
            $table->foreign('id_mata_kuliah')->references('id_mata_kuliah')->on('mata_kuliah')->onDelete('cascade');

            $table->uuid('id_registrasi_mahasiswa');
            $table->foreign('id_registrasi_mahasiswa')->references('id_registrasi_mahasiswa')->on('registrasi_mahasiswa')->onDelete('cascade');

            // Status workflow
            $table->enum('status_mata_kuliah', ['SELECTED', 'APPROVED', 'REJECTED'])->default('SELECTED');

            // Pembimbing (diisi setelah assignment)
            $table->uuid('id_dosen_pembimbing')->nullable();
            $table->foreign('id_dosen_pembimbing')->references('id_dosen')->on('dosen')->onDelete('set null');

            $table->uuid('id_dosen_pembimbing_2')->nullable();
            $table->foreign('id_dosen_pembimbing_2')->references('id_dosen')->on('dosen')->onDelete('set null');


            $table->timestamps();

            // Constraints
            $table->unique(['id_mata_kuliah', 'id_registrasi_mahasiswa'], 'unique_bimbingan_per_semester');

            // Indexes untuk performance
            $table->index('status_mata_kuliah');
            $table->index('id_dosen_pembimbing');
            $table->index('id_registrasi_mahasiswa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_bimbingan');
    }
};