<?php
// database/migrations/2024_01_15_000003_create_kkn_kelompok_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kkn_kelompok', function (Blueprint $table) {
            $table->uuid('id_kelompok_kkn')->primary();
            $table->string('nama_kelompok', 100);
            $table->string('lokasi', 200);
            $table->text('alamat_lokasi')->nullable();

            // Dosen Pembimbing Lapangan
            $table->uuid('id_dpl');
            $table->foreign('id_dpl')->references('id_dosen')->on('dosen')->onDelete('restrict');

            // Periode KKN
            $table->date('periode_mulai');
            $table->date('periode_selesai');

            $table->text('target_program_kerja')->nullable();
            $table->timestamps();

            $table->uuid('id_semester');
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('cascade');

            // Index
            $table->index('id_dpl');
            $table->index(['periode_mulai', 'periode_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kkn_kelompok');
    }
};
