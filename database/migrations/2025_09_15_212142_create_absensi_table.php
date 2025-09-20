<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('id_sesi_absensi')->index();
            $table->foreign('id_sesi_absensi')->references('id_sesi_absensi')->on('sesi_absensi')->onDelete('cascade');

            $table->uuid('id_mahasiswa')->index();
            $table->foreign('id_mahasiswa')->references('id_mahasiswa')->on('mahasiswa')->onDelete('cascade');

            $table->boolean('is_hadir')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
