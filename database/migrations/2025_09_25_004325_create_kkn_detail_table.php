<?php
// database/migrations/2024_01_15_000004_create_kkn_detail_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kkn_detail', function (Blueprint $table) {
            $table->uuid('id_kkn_detail')->primary();

            $table->uuid('id_peserta_bimbingan');
            $table->foreign('id_peserta_bimbingan')->references('id_peserta_bimbingan')->on('peserta_bimbingan')->onDelete('cascade');

            $table->uuid('id_kelompok_kkn');
            $table->foreign('id_kelompok_kkn')->references('id_kelompok_kkn')->on('kkn_kelompok')->onDelete('cascade');

            $table->enum('peran_kelompok', ['KETUA', 'ANGGOTA'])->default('ANGGOTA');
            $table->timestamps();

            // Constraint: satu mahasiswa hanya bisa di satu kelompok
            $table->unique('id_peserta_bimbingan', 'unique_mahasiswa_per_kelompok');

            // Index
            $table->index('id_kelompok_kkn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kkn_detail');
    }
};
