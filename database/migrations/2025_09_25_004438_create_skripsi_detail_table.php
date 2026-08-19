<?php
// database/migrations/2024_01_15_000006_create_skripsi_detail_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skripsi_detail', function (Blueprint $table) {
            $table->uuid('id_skripsi_detail')->primary();

            $table->uuid('id_peserta_bimbingan');
            $table->foreign('id_peserta_bimbingan')->references('id_peserta_bimbingan')->on('peserta_bimbingan')->onDelete('cascade');

            $table->string('judul', 500)->nullable();
            $table->string('bidang_penelitian', 100)->nullable();

            $table->enum('status_proposal', ['DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED'])->default('DRAFT');
            $table->date('tanggal_seminar_proposal')->nullable();
            $table->date('tanggal_sidang_skripsi')->nullable();

            $table->timestamps();

            $table->uuid('id_semester');
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('cascade');

            // Constraint: satu peserta bimbingan hanya satu skripsi
            $table->unique('id_peserta_bimbingan', 'unique_skripsi_per_peserta');

            // Index
            $table->index('status_proposal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skripsi_detail');
    }
};
