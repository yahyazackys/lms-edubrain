<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurikulum_mata_kuliah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('semester');

            $table->enum('kategori_mata_kuliah', [
                'MKWUUPT',   // Wajib UUPT
                'MKWU',      // Wajib Universitas
                'MKWPS',     // Wajib Program Studi
                'MKP'        // Pilihan
            ])->default('MKWPS');

            $table->uuid('id_kurikulum');
            $table->foreign('id_kurikulum')->references('id_kurikulum')->on('kurikulum')->onDelete('cascade');
            $table->uuid('id_mata_kuliah');
            $table->foreign('id_mata_kuliah')->references('id_mata_kuliah')->on('mata_kuliah')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurikulum_mata_kuliah');
    }
};
