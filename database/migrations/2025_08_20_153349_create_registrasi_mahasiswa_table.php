<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrasi_mahasiswa', function (Blueprint $table) {
            $table->uuid('id_registrasi_mahasiswa')->primary();

            // Relasi utama
            $table->uuid('id_mahasiswa');
            $table->foreign('id_mahasiswa')->references('id_mahasiswa')->on('mahasiswa')->onDelete('cascade');

            $table->uuid('id_semester');
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('cascade');

            // Status workflow KRS (batch level) - DITAMBAH
            $table->enum('status_krs', [
                'SUBMITTED',     // Sudah submit ke PA
                'APPROVED',      // PA sudah approve, KRS AKTIF
                'REJECTED',      // PA sudah approve, KRS AKTIF
            ])->default('SUBMITTED');

            // Relasi ke pembimbing akademik - DITAMBAH
            $table->uuid('id_pembimbing_akademik')->nullable();
            $table->foreign('id_pembimbing_akademik')->references('id_pembimbing_akademik')->on('pembimbing_akademik')->onDelete('set null');

            // Timestamps workflow - DITAMBAH
            $table->timestamp('tanggal_submit')->nullable();
            $table->timestamp('tanggal_approval')->nullable();

            $table->string('alasan_reject')->nullable();

            $table->timestamps();

            // Constraint: satu mahasiswa hanya bisa punya satu KRS per semester
            $table->unique(['id_mahasiswa', 'id_semester']);

            // Index untuk performa
            $table->index(['status_krs', 'id_semester']);
            $table->index(['id_pembimbing_akademik', 'status_krs']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrasi_mahasiswa');
    }
};
