<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_studi', function (Blueprint $table) {
            $table->uuid('id_program_studi')->primary();
            $table->string('kode_program_studi', 20)->unique();
            $table->string('nama_program_studi', 100);
            $table->enum('status', ['A', 'N'])->default('A'); // aktif/nonaktif

            // Relasi
            $table->uuid('id_jenjang_pendidikan');
            $table->foreign('id_jenjang_pendidikan')->references('id_jenjang_pendidikan')->on('jenjang_pendidikan')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_studi');
    }
};