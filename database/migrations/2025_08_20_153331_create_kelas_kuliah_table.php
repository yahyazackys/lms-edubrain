<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_kuliah', function (Blueprint $table) {
            $table->uuid('id_kelas_kuliah')->primary();
            $table->string('nama_kelas_kuliah', 100)->nullable();
            $table->string('nama_ruangan', 100)->nullable();
            $table->integer('kapasitas')->default(40);
            $table->time('jam_mulai')->nullable();
            $table->time('jam_akhir')->nullable();
            $table->enum('hari', ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'])->nullable();

            $table->integer('bobot_absensi')->default(10);
            $table->integer('bobot_tugas')->default(30);
            $table->integer('bobot_uts')->default(30);
            $table->integer('bobot_uas')->default(30);

            $table->enum('status', ['aktif', 'selesai'])->default('aktif');

            $table->uuid('id_kurikulum_mata_kuliah');
            $table->foreign('id_kurikulum_mata_kuliah')->references('id')->on('kurikulum_mata_kuliah')->onDelete('cascade');

            $table->uuid('id_semester');
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('cascade');

            $table->uuid('id_dosen')->nullable();
            $table->foreign('id_dosen')->references('id_dosen')->on('dosen')->onDelete('cascade');


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_kuliah');
    }
};
