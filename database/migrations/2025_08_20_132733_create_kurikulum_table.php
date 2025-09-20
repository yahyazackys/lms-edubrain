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
            $table->integer('jumlah_sks_lulus')->default(0);
            $table->integer('jumlah_sks_wajib')->default(0);
            $table->integer('jumlah_sks_pilihan')->default(0);

            // Relasi
            $table->uuid('id_program_studi');
            $table->foreign('id_program_studi')->references('id_program_studi')->on('program_studi')->onDelete('cascade');
            $table->uuid('id_semester');
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurikulum');
    }
};
