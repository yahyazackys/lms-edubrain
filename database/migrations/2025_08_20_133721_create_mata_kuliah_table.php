<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->uuid('id_mata_kuliah')->primary();
            $table->string('kode_mata_kuliah', 20)->unique();
            $table->string('nama_mata_kuliah', 150);
            $table->decimal('sks_mata_kuliah', 4, 2);

            $table->enum('jenis_mata_kuliah', ['TEORI', 'PRAKTIKUM', 'MAGANG', 'KKN', 'SKRIPSI'])->default('TEORI')->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};
