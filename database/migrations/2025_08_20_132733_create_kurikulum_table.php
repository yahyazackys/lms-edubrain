<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurikulum', function (Blueprint $table) {
            $table->uuid('id_kurikulum')->primary();
            $table->string('nama_kurikulum', 100); // Kurikulum 2021

            // Total SKS untuk lulus
            $table->integer('jumlah_sks_lulus')->default(144);

            // SKS berdasarkan kategori mata kuliah
            $table->integer('sks_mkwuupt_minimal')->default(8);     // MK Wajib UUPT (Pancasila, Kewarganegaraan, Agama)
            $table->integer('sks_mkwu_minimal')->default(12);       // MK Wajib Universitas (Bahasa Indonesia, Inggris, dll)
            $table->integer('sks_mkwps_minimal')->default(108);     // MK Wajib Program Studi
            $table->integer('sks_mkp_minimal')->default(16);        // MK Pilihan

            // Relasi
            $table->uuid('id_program_studi');
            $table->foreign('id_program_studi')->references('id_program_studi')->on('program_studi')->onDelete('cascade');
            $table->uuid('id_semester');
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('cascade');

            $table->timestamps();

            // Index untuk performa
            $table->index(['id_program_studi', 'id_semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurikulum');
    }
};