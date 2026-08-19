<?php
// database/migrations/2024_01_15_000012_create_bimbingan_files_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bimbingan_file', function (Blueprint $table) {
            $table->uuid('id_bimbingan_file')->primary();

            $table->uuid('id_peserta_bimbingan');
            $table->foreign('id_peserta_bimbingan')->references('id_peserta_bimbingan')->on('peserta_bimbingan')->onDelete('cascade');

            // Relasi ke bab tertentu
            $table->uuid('id_laporan_bab')->nullable();
            $table->foreign('id_laporan_bab')->references('id_laporan_bab')->on('laporan_bab')->onDelete('cascade');

            // Dual input method
            $table->string('file_path')->nullable();           // File upload
            $table->text('konten')->nullable();         // Text input
            $table->enum('input_type', ['FILE', 'TEXT']);

            $table->timestamps();

            // Index
            $table->index(['id_peserta_bimbingan', 'input_type']);
            $table->index(['id_laporan_bab']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bimbingan_file');
    }
};
