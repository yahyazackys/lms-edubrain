<?php
// database/migrations/2024_01_15_000005_create_magang_detail_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('magang_detail', function (Blueprint $table) {
            $table->uuid('id_magang_detail')->primary();

            $table->uuid('id_peserta_bimbingan');
            $table->foreign('id_peserta_bimbingan')->references('id_peserta_bimbingan')->on('peserta_bimbingan')->onDelete('cascade');

            $table->string('tempat_magang', 200);
            $table->text('alamat_magang')->nullable();
            $table->string('bidang_magang', 100)->nullable();

            $table->timestamps();

            $table->uuid('id_semester');
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('cascade');

            // Constraint: satu peserta bimbingan hanya satu tempat magang
            $table->unique('id_peserta_bimbingan', 'unique_magang_per_peserta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magang_detail');
    }
};
