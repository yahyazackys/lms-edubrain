<?php
// database/migrations/2024_01_15_000011_create_laporan_bab_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_bab', function (Blueprint $table) {
            $table->uuid('id_laporan_bab')->primary();

            $table->uuid('id_peserta_bimbingan');
            $table->foreign('id_peserta_bimbingan')->references('id_peserta_bimbingan')->on('peserta_bimbingan')->onDelete('cascade');

            $table->string('judul_bab', 200);
            $table->longText('konten')->nullable(); // konten bab jika ditulis langsung
            $table->string('file_template')->nullable(); // file template bab jika ada templatenya

            // Status workflow
            $table->enum('status', ['DRAFT', 'SUBMITTED', 'APPROVED', 'NEEDS_REVISION'])->default('DRAFT');
            $table->text('catatan_pembimbing')->nullable();

            // Timeline workflow
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            // Index
            $table->index(['id_peserta_bimbingan', 'status']);
            $table->index(['status', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_bab');
    }
};
