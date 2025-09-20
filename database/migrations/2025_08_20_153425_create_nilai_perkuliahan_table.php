<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_perkuliahan', function (Blueprint $table) {
            $table->uuid('id_nilai_perkuliahan')->primary();

            // Relasi ke peserta
            $table->uuid('id_peserta')->index();
            $table->foreign('id_peserta')->references('id_peserta')->on('peserta_kelas_kuliah')->onDelete('cascade');

            // Nilai akademik
            $table->decimal('nilai_angka', 5, 2)->nullable(); // 0.00 - 100.00
            $table->decimal('nilai_indeks', 3, 2)->nullable(); // 0.00 - 4.00
            $table->string('nilai_huruf', 2)->nullable(); // A, B+, B, C+, C, D, E

            $table->timestamps();

            // Index untuk perhitungan IPK dan tracking kelulusan
            $table->index(['nilai_indeks']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_perkuliahan');
    }
};