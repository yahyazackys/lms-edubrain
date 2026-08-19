<?php
// database/migrations/2024_01_15_000010_create_kkn_dokumentasi_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kkn_dokumentasi', function (Blueprint $table) {
            $table->uuid('id_kkn_dokumentasi')->primary();

            $table->uuid('id_kelompok_kkn');
            $table->foreign('id_kelompok_kkn')->references('id_kelompok_kkn')->on('kkn_kelompok')->onDelete('cascade');

            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->string('file_path', 500);
            $table->enum('file_type', ['IMAGE', 'DOCUMENT']);
            $table->bigInteger('file_size')->nullable(); // dalam bytes
            $table->string('mime_type', 100)->nullable();
            $table->string('original_filename', 300);

            // Hanya ketua kelompok yang bisa upload
            $table->uuid('uploaded_by');
            $table->foreign('uploaded_by')->references('id_mahasiswa')->on('mahasiswa')->onDelete('cascade');

            $table->timestamps();

            // Index
            $table->index(['id_kelompok_kkn', 'file_type']);
            $table->index('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kkn_dokumentasi');
    }
};
