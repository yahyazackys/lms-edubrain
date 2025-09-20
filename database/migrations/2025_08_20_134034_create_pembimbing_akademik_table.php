<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembimbing_akademik', function (Blueprint $table) {
            $table->uuid('id_pembimbing_akademik')->primary();

            // Relasi utama
            $table->uuid('id_mahasiswa');
            $table->foreign('id_mahasiswa')->references('id_mahasiswa')->on('mahasiswa')->onDelete('cascade');

            $table->uuid('id_dosen');
            $table->foreign('id_dosen')->references('id_dosen')->on('dosen')->onDelete('cascade');

            $table->uuid('id_semester');
            $table->foreign('id_semester')->references('id_semester')->on('semester')->onDelete('cascade');

            // Status PA
            $table->enum('status_pa', ['AKTIF', 'SELESAI'])->default('AKTIF')->comment('Status pembimbing akademik');

            // Approval workflow
            $table->string('nomor_sk', 100)->nullable()->comment('Nomor SK penugasan PA');
            $table->date('tanggal_sk')->nullable()->comment('Tanggal SK penugasan PA');

            $table->timestamps();

            // Index untuk performa
            $table->index(['id_dosen', 'id_semester']);
            $table->index(['id_semester']);
            $table->index(['status_pa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembimbing_akademik');
    }
};
