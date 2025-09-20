<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_kelas_kuliah', function (Blueprint $table) {
            $table->uuid('id_peserta')->primary();

            // Relasi utama
            $table->uuid('id_kelas_kuliah');
            $table->foreign('id_kelas_kuliah')
                ->references('id_kelas_kuliah')
                ->on('kelas_kuliah')
                ->onDelete('cascade');

            $table->uuid('id_mata_kuliah');
            $table->foreign('id_mata_kuliah')
                ->references('id_mata_kuliah')
                ->on('mata_kuliah')
                ->onDelete('cascade');

            $table->uuid('id_registrasi_mahasiswa');
            $table->foreign('id_registrasi_mahasiswa')
                ->references('id_registrasi_mahasiswa')
                ->on('registrasi_mahasiswa')
                ->onDelete('cascade');

            // Status per mata kuliah
            $table->enum('status_mata_kuliah', [
                'SELECTED',
                'APPROVED',
                'REJECTED',
            ])->default('SELECTED');

            $table->timestamps();

            // Index dengan nama custom agar tidak terlalu panjang
            $table->index('status_mata_kuliah', 'idx_status');
            $table->index(['id_registrasi_mahasiswa', 'status_mata_kuliah'], 'idx_registrasi_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_kelas_kuliah');
    }
};
